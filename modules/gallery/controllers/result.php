<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain{
	var $modules = "gallery";
	var $action  = "result";
	var $sub 	 = "manage";

	function __construct (){
		global $ims;

		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->modules,
            'js'             => $this->modules,
			'css'  	 		 => $this->modules,
			'use_func'  	 => $this->modules, // Sử dụng func
			'use_navigation' => 0, // Sử dụng navigation
			'required_login' => 0, // Bắt buộc đăng nhập
		);

        $ims->func->loadTemplate($arrLoad);
		require_once ($this->modules."_func.php");
        $this->modFunc = new galleryFunc($this);

		$data = array();
        $ims->conf["cur_group"] = 0;
        $data['content'] = $this->do_list();

		$ims->conf['container_layout'] = 'm';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
		
	function do_list (){
		global $ims;

		$title = '<div class="left_title">'.$ims->site->main_title($ims->lang['gallery']['result']);
		if($ims->setting['gallery']['finish'] == 0){
		    $title .= $ims->conf['register'];
		    $content = '<div style="text-align: center; font-size:15px; padding: 20px 0">'.$ims->lang['gallery']['not_result'].'</div>';
        }else{
		    $content = $this->result();
        }
        $title .= '</div>';
        $total = $ims->db->do_get_num("gallery", "1 ". $ims->conf['where_lang'] . ' and is_approve = 1 ');
        $total = number_format($total, 0,',','.');
        $title .= '<div class="join_total"><span class="title">'.$ims->lang['gallery']['join_total'].'</span><span class="total">'.$total.'</span></div>';
        $data = array(
            'title' => $title,
            'content' => $content
        );
        $ims->temp_box->assign('data', $data);
        $ims->temp_box->parse('box');
        return $ims->temp_box->text('box');
	}
	function result(){
	    global $ims;
        $more = '';

	    $result = $ims->db->load_item_arr('gallery', $ims->conf['qr'].' and award > 0 order by award asc, show_order desc', 'title, full_name, item_code, picture, content, award');
	    if($result){
	        $i = 0;
	        foreach ($result as $row){
	            $i++;
	            $row['pic_zoom'] = $ims->func->get_src_mod($row['picture']);
	            $row['picture'] = $ims->func->get_src_mod($row['picture'], 267, 209, 1, 1);
	            $row['content'] = $ims->func->short($row['content'], 134);
	            $row['none'] = '';
                $row['award'] = $ims->lang['gallery'][$row['award'].'_award'];
	            if ($i > 6){
                    $row['none'] = 'd-none';
                    $more = '<div class="see_more"><button><i class="far fa-angle-down"></i></button></div>';
                }

	            $ims->temp_act->assign('row', $row);
	            $ims->temp_act->parse('result.item');
            }
            $ims->temp_act->assign('more', $more);
            $ims->temp_act->parse('result');
            return $ims->temp_act->text('result');
        }
    }
	
  // end class
}
?>