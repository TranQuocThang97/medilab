<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "user";
	var $action  = "event";
	var $sub 	 = "manage";
	var $template = "event";
	
	/**
		* Khởi tạo
		* Quản lý sự kiện
	**/
	function __construct (){
		global $ims;
		
		$dir_assets  = $ims->func->dirModules($this->modules, 'assets');
		$ims->func->include_css($dir_assets."css/".$this->modules.'.css');
		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->modules,
			'js'  	 		 => $this->modules,
			'css'  	 		 => $this->template,
			'use_func'  	 => $this->modules, // Sử dụng func
			'use_navigation' => 0, // Sử dụng navigation
			'required_login' => 1, // Bắt buộc đăng nhập
		);
        $ims->func->loadTemplate($arrLoad);
		//custom tpl
		$dir_view      = $ims->func->dirModules($this->modules, 'views', 'path');
        $ims->temp_out = new XTemplate($dir_view . $this->template . ".tpl");
        $ims->temp_out->assign('CONF', $ims->conf);
        $ims->temp_out->assign('LANG', $ims->lang);
        $ims->temp_out->assign('DIR_IMAGE', $ims->dir_images);

		$data = array();		
		$data['content'] = $this->do_main();
		$data['box_left'] = box_left($this->action);

		$ims->conf["class_full"] = 'user';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	
	function do_main (){
		global $ims;	
		$data = array();
		$data['src'] = $ims->conf['rooturl'].'resources/images/user/';				
		$data['link_action'] = $ims->site_func->get_link('user', $ims->setting['user']['event_link']);
		$ims->site_func->setting('event');
		$search_keyword = $ims->func->if_isset($ims->input['search_keyword']);
		$search_status = $ims->func->if_isset($ims->input['search_status']);
		$search_organizer = $ims->func->if_isset($ims->input['search_organizer']);		
		$is_search = 0;

		$p = $ims->func->if_isset($ims->input["p"], 1);
		$ext = '';
		$where = '';		

		$arr_status = array(
        	0 => $ims->lang['user']['event_upcoming'],
        	1 => $ims->lang['user']['event_ongoing'],
        	2 => $ims->lang['user']['event_over'],
        );        
        $data['status'] = $ims->html->select("search_status", $arr_status, $search_status, " class=\"\"", array("title" => $ims->lang['user']['event_status']));

        $arr_organizer = $ims->load_data->data_table('event', 'organizer', 'organizer, organizer as title', 'is_show=1 and user_id="'.$ims->data['user_cur']['user_id'].'" and organizer!="" ');
        $data['organizer'] = $ims->html->select("search_organizer", $arr_organizer, $search_organizer, " class=\"\"", array("title" => $ims->lang['user']['organizer']));

		if(!empty($search_keyword)){
			$data['search_keyword'] = $search_keyword;
			$arr_tmp = array();
			$arr_key = explode(" ", $search_keyword);
	        foreach ($arr_key as $value) {
	            $value = trim($value);
	            if (!empty($value)) {
	                $value = str_replace(chr(39), chr(34),$value);
	                $arr_tmp['title'][] = "LOWER(title) like CONCAT('%', CONVERT('" . mb_strtolower($value,'UTF-8') . "', BINARY), '%')";	                
	                $order .= "(title = '".$value."') desc, ";
	            }
	        }
	        if (count($arr_tmp) > 0) {
	            foreach ($arr_tmp as $k => $v) {
	                if (count($v) > 0) {
	                    $arr_tmp[$k] = "(" . implode(" and ", $v) . ")";
	                } else {
	                    unset($arr_tmp[$k]);
	                }
	            }
	        }
	        if (count($arr_tmp) > 0) {
	            $where .= " and (" . implode(" or ", $arr_tmp) . ") ";
	        }
			$ext .= "&search_keyword=".$search_keyword;
			$is_search = 1;
		}
		if(!empty($search_status)){
			switch ($search_status) {
				case '1':
					$where .= " AND date_begin <= ".time()." and date_end >= ".time()." ";					
					break;
				case '2':
					$where .= " AND date_end < ".time()." ";
					break;
				default: //0
					$where .= " AND date_begin > ".time()." ";
					break;
			}
			$ext .= "&search_status=".$search_status;
			$is_search = 1;
		}
		if(!empty($search_organizer)){
			$where .= " AND LOWER(organizer) = CONVERT('" . mb_strtolower($search_organizer,'UTF-8') . "', BINARY) ";
			$ext .= "&search_organizer=".$search_organizer;
			$is_search = 1;
		}

		$num_total = $ims->db->do_get_num("event", 'lang="'.$ims->conf['lang_cur'].'" and is_show=1 and user_id="'.$ims->data['user_cur']['user_id'].'" '.$where);
		$n = !empty($ims->setting['user']['num_list'])?$ims->setting['user']['num_list']:10;
        $num_items = ceil($num_total / $n);
        if ($p > $num_items)
            $p = $num_items;
        if ($p < 1)
            $p = 1;
        $start = ($p - 1) * $n;
     	$link_action =  $ims->site_func->get_link('user', $ims->setting['user']['event_link']);       	
        $nav = $ims->site->paginate($link_action, $num_total, $n, $ext, $p);
        $data['nav'] = $nav;

		$arr_event = $ims->db->load_item_arr('event', 'lang="'.$ims->conf['lang_cur'].'" and is_show=1 and user_id="'.$ims->data['user_cur']['user_id'].'" '.$where.' order by date_create desc limit '.$start.','.$n, 'item_id, title, picture, address, date_begin, date_end, arr_price');		
		if($arr_event){
			foreach ($arr_event as $row) {
				$row['title'] = $ims->func->input_editor_decode($row['title']);
				$row['picture'] = $ims->func->get_src_mod($row['picture'],102,102,1,0,array('fix_min' => 1));
				$row['address'] = $ims->func->input_editor_decode($row['address']);
				$row['status'] = $ims->lang['user']['event_upcoming'];
				if(time() >= $row['date_begin'] && time() <= $row['date_end']){
					$row['status'] = $ims->lang['user']['event_ongoing'];
				}
				if(time() > $row['date_end']){
					$row['status'] = $ims->lang['user']['event_over'];
				}
				switch ($ims->conf['lang_cur']) {
					case 'vi':
						$row['date_begin'] = $ims->func->rebuild_date('l, d/m, h:i A', $row['date_begin']);	
						break;
					case 'en':
						$row['date_begin'] = date('l, d/m, h:i A', $row['date_begin']);	
					default:
						break;
				}
				$row['ticket_remain'] = 0;
				$row['ticket_total'] = 0;
				$arr_price = $ims->func->unserialize($row['arr_price']);
				if($arr_price){
					foreach ($arr_price as $k_p => $v_p) {						
						$row['ticket_remain'] += $v_p['num_ticket_remain'];
						$row['ticket_total'] += $v_p['num_ticket'];
					}
				}
				$row['revenue'] = 0;
				$event_order = $ims->db->load_row('event_order',' event_id="'.$row['item_id'].'" ',' SUM(total_payment) as revenue');
				if(!empty($event_order['revenue'])){
					$row['revenue'] += $event_order['revenue'];
				}
				$row['revenue'] = $ims->func->format_number($row['revenue']).' '.$ims->lang['global']['unit'];
				$row['participations'] = $ims->db->do_get_num('event_order_detail',' event_id="'.$row['item_id'].'" and is_checkin=1');
				$row['link_list'] = $ims->site_func->get_link('user', $ims->setting['user']['statistic_link']).'/'.$ims->func->link2hex('?detail='.$row['item_id'],6);
				
				$ims->temp_out->assign('row', $row);
				if(time() <= $row['date_end']){
					$ims->temp_out->parse("event.row.checkin");			
				}
				$ims->temp_out->parse("event.row");
			}
		}else{
			$ims->temp_out->assign('row', array('title' => $ims->lang['global']['no_have_data']));
			$ims->temp_out->parse("event.row_empty");
		}
		
		$data['link_package'] = $ims->site_func->get_link('user');
		$data['link_create'] = $ims->site_func->get_link('event', $ims->setting['event']['create_link']);

		$ims->temp_out->assign('data', $data);
		$ims->temp_out->parse("event");
		return $ims->temp_out->text("event");
	}

  	// End class
}
?>