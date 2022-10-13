<?php

/*================================================================================*\
Name code : view.php
Copyright © 2013 by Tran Thanh Hiep
@version : 1.0
@date upgrade : 03/02/2013 by Tran Thanh Hiep
\*================================================================================*/

if (! defined('IN_ims')) {
  die('Access denied');
}
$nts = new sMain();

class sMain{
	var $modules = "page";
	var $action = "page";
	var $sub = "manage";

	function __construct (){
		global $ims;

        $arrLoad = array(
            'modules' 		 => $this->modules,
            'action'  		 => $this->action,
            'template'  	 => $this->modules,
            'css'  	 		 => $this->modules,
            'use_func'  	 => '', // Sử dụng func
            'use_navigation' => 0, // Sử dụng navigation
            'required_login' => 0, // Bắt buộc đăng nhập
        );
        $ims->func->loadTemplate($arrLoad);

        require_once ($this->modules."_func.php");
		
		$data = array();
		if(isset($ims->conf['cur_group']) && isset($ims->data['cur_group']) && $ims->data['cur_group']){
			$row = $ims->data['cur_group'];
			//Current menu
            $arr_group_nav = (!empty($row["group_nav"])) ? explode(',',$row["group_nav"]) : array();
            foreach($arr_group_nav as $v) {
                $ims->conf['menu_action'][] = $this->modules.'-group-'.$v;
            }
            //End current menu
            $ims->conf["cur_group_nav"] = $row["group_nav"];
            $ims->conf["meta_image"]    = ($row["picture"] != '') ? $ims->func->get_src_mod($row["picture"], 630, 420, 1, 1) : '';

            //Make link lang
            $result = $ims->db->query("select friendly_link,lang from page_group where group_id = ".$ims->conf['cur_group']);
            while($row_lang = $ims->db->fetch_row($result)){
                $ims->data['link_lang'][$row_lang['lang']] = $ims->site_func->get_link_lang ($row_lang['lang'], $this->modules, $row_lang['friendly_link']);
            }
            //End Make link lang

            //SEO
            $ims->site->get_seo ($row);

            $data['content'] = $this->do_list_group($row);
		}elseif (isset($ims->conf['cur_item']) && isset($ims->data['cur_item']) && $ims->data['cur_item']) {
            $row = $ims->data['cur_item'];

            //Make link lang
            $result = $ims->db->query("select friendly_link,lang from page where item_id = ".$ims->conf['cur_item']);
            while($row_lang = $ims->db->fetch_row($result)){
                $ims->data['link_lang'][$row_lang['lang']] = $ims->site_func->get_link_lang ($row_lang['lang'], $this->modules, '', $row_lang['friendly_link']);
            }
            //End Make link lang

            //SEO
            $ims->site->get_seo($row);
            $ims->conf['cur_group'] = $row['group_id'];
            $ims->conf["cur_group_nav"] = $row["group_nav"];
            $ims->conf["meta_image"]    = $ims->func->get_src_mod($row["picture"], 630, 420, 1, 1);

            $ims->conf['menu_action'][] = $this->modules.'-group-'.$ims->conf['cur_group'];
            $ims->conf['menu_action'][] = $this->modules.'-item-'.$ims->conf['cur_item'];

            $data['content'] = $this->do_detail ($row);
		}else{
			$ims->html->redirect_rel($ims->site->get_link ('home'));
		}
		
		$ims->conf['container_layout'] = 'c-m';
		$ims->conf['class_full'] = 'page';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	
	function do_list_group ($info = array()){
		global $ims;	
		
		$data = array(
			'title' => $info['title']
		);
		
		$arr_in = array(
			'link_action' => $ims->func->get_link ($info['friendly_link'], ''),
			'where' => " and find_in_set('".$info['group_id']."',group_nav)",
			'temp' => 'list_item',
		);

        $data['content'] = html_list_item($arr_in);
		
		$ims->temp_box->assign('data', $data);
		$ims->temp_box->parse("box_main");
		return $ims->temp_box->text("box_main");
	}
	
	function do_detail ($info = array()){
		global $ims;
		
//		$info['link_share'] = $ims->data['link_lang'][$ims->conf['lang_cur']];
		
		/*$string = file_get_contents('http://graph.facebook.com/'.$info['link_share'], FILE_USE_INCLUDE_PATH);
		$facebook_info = json_decode($string);
		if(!isset($facebook_info->comments)) {
			$facebook_info->comments = 0;
		}
		$info['num_comment'] = $facebook_info->comments;*/
        $info['title'] = $ims->func->input_editor_decode($info['title']);
        $info['content'] = $ims->func->input_editor_decode($info['content']);

		$ims->temp_act->assign('data', $info);
		$ims->temp_act->parse("item_detail");
		return $ims->temp_act->text("item_detail");
	}
	
  // end class
}
?>