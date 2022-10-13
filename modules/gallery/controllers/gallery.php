<?php
if (!defined('IN_ims')) {die('Access denied');}
$nts = new sMain();

class sMain
{
	var $modules = "gallery";
	var $action  = "gallery";
	var $sub 	 = "manage";
	var $template= "gallery";
	/**
		* function __construct ()
		* Khoi tao 
	**/
	function __construct (){
		global $ims;

		$dir_assets  = $ims->func->dirModules($this->modules, 'assets');		
		$ims->func->include_css($dir_assets."css/event.css");
		$ims->func->include_css($dir_assets."css/".$this->modules.'.css');
		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->modules,
			'js'  	 		 => $this->modules,
			'css'  	 		 => $this->modules,
			'use_func'  	 => $this->modules, // Sử dụng func
			'use_navigation' => 0, // Sử dụng navigation
			'required_login' => 1, // Bắt buộc đăng nhập
		);
        $ims->func->loadTemplate($arrLoad);

        require_once ($ims->conf['rootpath']."modules/event/controllers/event_func.php");
		$this->eventFunc = new eventFunc($this);

		$data = array();
		$data['content'] = '';		
		$data['content'] .= $this->do_main();		
	
		$ims->conf["class_full"] = 'user';
		$ims->conf['container_layout'] = 'm';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	
	function do_main (){
		global $ims;
      	// $sql = 'SELECT d.* FROM event_order as o INNER JOIN event_order_detail as d ON d.order_id=o.order_id WHERE o.order_id=17 AND o.is_show=1 AND o.is_cancel=0 AND o.is_status!=17 AND (o.user_id=3 or d.email="test@gmail.com" or d.phone="0987654321")';
      	$data = array();      	

      	$ims->data['user_cur']['user_id'] = 1;
      	$arr_ticket = $ims->db->load_item_arr('event_order as o INNER JOIN event_order_detail as d ON d.order_id=o.order_id', 'o.is_show=1 AND o.is_cancel=0 AND o.is_status!=17 AND (o.user_id="'.$ims->data['user_cur']['user_id'].'" or d.email="'.$ims->data['user_cur']['email'].'" or d.phone="'.$ims->data['user_cur']['phone'].'") group by d.event_id', 'd.event_id');
      	$tmp = array();
      	if($arr_ticket){
      		foreach ($arr_ticket as $row) {
      		    $tmp[] = $row['event_id'];
      		}
      	}      	
      	$p = $ims->func->if_isset($ims->input["p"], 1);
		$ext = '';
		$where = ' and find_in_set(item_id, "'.implode(',',$tmp).'")';

		$num_total = $ims->db->do_get_num("event", 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" '.$where);
		$n = !empty($ims->setting['gallery']['num_register_list'])?$ims->setting['gallery']['num_register_list']:10;
        $num_items = ceil($num_total / $n);
        if ($p > $num_items)
            $p = $num_items;
        if ($p < 1)
            $p = 1;
        $start = ($p - 1) * $n;
     	$link_action =  $ims->site_func->get_link('gallery');
        $nav = $ims->site->paginate($link_action, $num_total, $n, $ext, $p);        
      	
      	$arr_event = $ims->db->load_row_arr('event', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" '.$where.' LIMIT '.$start.','.$n);
      	if($arr_event){
      		foreach ($arr_event as $row) {
      			 $title1 = ($row['title1'] != '') ? $ims->func->input_editor_decode($row['title1']).': ' : '';
		        $row['title'] = $title1.$ims->func->input_editor_decode($row['title']);
		        $arr = $ims->site->check_favorite($row['item_id']);
		        $row['class_favorite'] = isset($arr['class']) ? $arr['class'] : '';
		        $row['added'] = isset($arr['added']) ? $arr['added'] : '';
		        $row['date_begin'] = $ims->lang['global']['day_'.date('N', $row['date_begin'])].', '.date('d/m h:i A');
		        $event_owner = $ims->db->load_row('user', 'user_id = '.$row['user_id'], 'full_name, num_follow');
		        $row['event_owner'] = $event_owner['full_name'];
		        $row['num_follow'] = $this->eventFunc->convert_number($event_owner['num_follow']);
				$row["picture"] = $ims->func->get_src_mod($row["picture"], 285, 162, 1, 1);
				$favorite = $ims->site->check_favorite($row['item_id']);
		        if (!empty($favorite)) {
		            $row['i_favorite'] = $ims->func->if_isset($favorite["class"]);
		            $row['added'] = $ims->func->if_isset($favorite["added"]);
		        }
				$row['item_id'] = $ims->func->base64_encode($row['item_id']);
		        $row['loading'] = $ims->dir_images."spin.svg";
		        $row['rooturl'] = $ims->conf['rooturl'];
		        $row['link'] = $ims->site_func->get_link('gallery', $ims->setting['gallery']['find_image_link']).'/'.$ims->func->link2hex('?detail='.$row['item_id'],6);
				$ims->temp_act->assign('row', $row);
				$ims->temp_act->parse("gallery.row");		        
      		}
      	}

     	$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("gallery");
		$ims->output .=  $ims->temp_act->text("gallery");   
	}
  // end class
}
?>