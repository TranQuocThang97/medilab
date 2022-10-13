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

class sMain
{
	var $modules = "about";
	var $action = "about";
	var $sub = "manage";
	
	/**
	* function __construct ()
	* Khoi tao 
	**/
	function __construct ()
	{
		global $ims;
		
		$arrLoad = array(
            'modules'        => $this->modules,
            'action'         => $this->action,
            'template'       => $this->modules,
            'js'             => $this->modules,
            'css'            => $this->modules,
            'use_func'       => "", // Sử dụng func
            'use_navigation' => 0, // Sử dụng navigation
            'required_login' => 0, // Bắt buộc đăng nhập
        );
        $ims->func->loadTemplate($arrLoad);

		if($ims->data['cur_item']){						
			//Make link lang
			$result = $ims->db->query("select friendly_link,lang 
										from about 
										where item_id='".$ims->conf['cur_item']."' ");
			while($row_lang = $ims->db->fetch_row($result)){
				$ims->data['link_lang'][$row_lang['lang']] = $ims->site_func->get_link_lang ($row_lang['lang'], $this->modules, '', $row_lang['friendly_link']);
			}
			//End Make link lang
			//SEO
			$ims->site->get_seo ($ims->data['cur_item']);
			
			$ims->conf['menu_action'][] = $this->modules.'-item-'.$ims->conf['cur_item'];
			
			$ims->navigation = $ims->site->html_arr_navigation (array(
				array(
				'title' => '<i class="fad fa-home"></i>'.$ims->lang['global']['homepage'],
					'link' => $ims->site_func->get_link ('home')
				),
				array(
					'title' => $ims->lang['about']['mod_title'],
					'link' => $ims->site_func->get_link ('about')
				),				
			));
			//echo $ims->data['cur_item']['content'];die();
			$ims->conf['page_title'] = '<div class="title">'.$ims->lang['about']['mod_title'].'</div>';
			$ims->conf['nav'] = $ims->navigation;

			$data['content'] = $this->do_about($ims->data['cur_item']);
		}else{
			$ims->html->redirect_rel($ims->site_func->get_link ('home'));
		}
		//$data['checkdomain'] = $ims->site->load_widget ('checkdomain');
		$ims->conf['container_layout'] = 'c-m';
		$ims->conf['class_full'] = 'about';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	
	function do_about($info = array()){
		global $ims;		
		$data = $info;
		// $data['nav'] = $ims->navigation;
		$data['title'] = $ims->func->input_editor_decode($info['title']);
		// $data['short'] = $ims->func->input_editor_decode($info['short']);
		// $data['picture'] = $ims->func->get_src_mod($info['picture'],570,365);
		$data['content'] = $ims->func->input_editor_decode($info['content']);
		// $ims->func->include_js_content('
		// 	countUpWaypoint(".count",7000);
		// ');
		$ims->temp_act->assign('CONF',$ims->conf);
		$ims->temp_act->assign('data',$data);
		$ims->temp_act->parse("about");
		return $ims->temp_act->text("about");
	}
  // end class
}
?>