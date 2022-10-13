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
	var $modules = "service";
	var $action = "service";
	var $sub = "manage";
	
	/**
	* function __construct ()
	* Khoi tao 
	**/
	function __construct ()
	{
		global $ims;
		
		$ims->func->load_language($this->modules);
		$ims->temp_act = new XTemplate($ims->path_html.$this->modules.DS.$this->action.".tpl");
		$ims->temp_act->assign('CONF', $ims->conf);
		$ims->temp_act->assign('LANG', $ims->lang);
		$ims->temp_act->assign('DIR_IMAGE', $ims->dir_images);
		
		$ims->func->include_css ($ims->dir_css.$this->modules.'/'.$this->action.".css");
		
		$ims->conf['menu_action'] = array($this->modules);
		$ims->data['link_lang'] = (isset($ims->data['link_lang'])) ? $ims->data['link_lang'] : array();
		
		include ($this->modules."_func.php");
		
		$data = array();
		if(isset($ims->conf['cur_group'])){
			$result = $ims->db->query("select group_id, group_nav, type_show, num_show, is_show   
										from service_group 
										where group_id='".$ims->conf['cur_group']."' 
										and is_show=1 
										limit 0,1");
			if($row = $ims->db->fetch_row($result)){
				
				//Current menu
				$arr_group_nav = (!empty($row["group_nav"])) ? explode(',',$row["group_nav"]) : array();
				foreach($arr_group_nav as $v) {
					$ims->conf['menu_action'][] = $this->modules.'-group-'.$v;
				}
				//End current menu
				$ims->conf["cur_group_nav"] = $row["group_nav"];	
				
				//Make link lang
				$result = $ims->db->query("select friendly_link,lang   
											from service_group 
											where group_id='".$ims->conf['cur_group']."' ");
				while($row_lang = $ims->db->fetch_row($result)){
					$ims->data['link_lang'][$row_lang['lang']] = $ims->site->get_link_lang ($row_lang['lang'], $this->modules, $row_lang['friendly_link']);
				}
				//End Make link lang
				//SEO
				$ims->site->get_seo ($ims->data['cur_group']);
				
				$ims->navigation = $ims->site->html_arr_navigation (array(
					array(
						'title' => $ims->lang['global']['homepage'],
						'link' => $ims->site->get_link ('home')
					),
					array(
						'title' => $ims->data['service_group'][$ims->conf['cur_group']]['title'],
						'link' => $ims->site->get_link ('service',$ims->data['service_group'][$ims->conf['cur_group']]['friendly_link'])
					)
				));
				
				$data = array();
				$data['content'] = $this->do_list_group($row, $ims->data['cur_group']);
			}else{
				$ims->html->redirect_rel($ims->site->get_link ($this->modules));
			}
		}elseif(isset($ims->conf['cur_item'])){
			
			$where = " and p.item_id='".$ims->conf['cur_item']."' ";
			
			$result = $ims->db->query("select * 
										from service 
										where is_show=1 
										".$where." 
										limit 0,1");
			if($row = $ims->db->fetch_row($result)){
				$row['content'] = $ims->func->input_editor_decode($row['content']);
				$ims->conf['cur_group'] = $row['group_id'];
				$ims->conf["cur_group_nav"] = $row["group_nav"];			
				$ims->conf['cur_item'] = $row['item_id'];
				$ims->data['cur_item'] = $row;
				//Make link lang
				$result = $ims->db->query("select friendly_link,lang 
											from service 
											where item_id='".$ims->conf['cur_item']."' ");
				while($row_lang = $ims->db->fetch_row($result)){
					$ims->data['link_lang'][$row_lang['lang']] = $ims->site->get_link_lang ($row_lang['lang'], $this->modules, '', $row_lang['friendly_link']);
				}
				//End Make link lang
				//SEO
				$ims->site->get_seo ($ims->data['cur_item']);
				
				$ims->conf['menu_action'][] = $this->modules.'-group-'.$ims->conf['cur_group'];
				$ims->conf['menu_action'][] = $this->modules.'-item-'.$ims->conf['cur_item'];
				
				//$data = $ims->data['cur_item'];
	
				/*$ims->temp_act->assign('data', array(
					'title' => urlencode($data['title']),
					'link' => $ims->data['link_lang'][$ims->conf['lang_cur']]
				));
				$ims->temp_act->parse("html_title_more");
				$data['more_title'] = $ims->temp_act->text("html_title_more");
				$ims->temp_box->assign('data', $data);
				$ims->temp_box->parse("box_main");
				$data = array(
					"content" => $ims->temp_box->text("box_main"),
					"box_right" => box_right ()
				);*/
				
				$data = array();
				$data['content'] = $this->do_detail ($ims->data['cur_item']);
			}else{
				$ims->html->redirect_rel($ims->site->get_link ('home'));
			}
		}else{
			$ims->html->redirect_rel($ims->site->get_link ('home'));
		}
		
		$data['box_left'] = box_left();
		$data['box_column'] = box_column();
	
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	
	function do_list_group ($info = array(), $info_lang = array())
	{
		global $ims;	
		
		$data = array(
			'title' => $info_lang['title']
		);
		
		$arr_in = array(
			'link_action' => $ims->site->get_link ('service',$info_lang['friendly_link']),
			'where' => " and find_in_set('".$info['group_id']."',group_nav)>0",
			'temp' => 'list_item',
		);
		
		switch ($info['type_show']) {
			case "list_item":
				$arr_in['temp'] = "list_item";
				$data['content'] = html_list_item($arr_in);
				break;
			case "grid_item":
				$arr_in['temp'] = "grid_item";
				$arr_in['pic_w'] = 200;
				$arr_in['pic_h'] = 200;
				$arr_in['num_row'] = 4;
				if($info['num_show'] > 0) {
					$arr_in['num_show'] = $info['num_show'];
				}
				$arr_in['short_len'] = 200;
				$data['content'] = html_list_item($arr_in);
				break;
			case "go_item":
				//Go to detail
				$result = $ims->db->query("select friendly_link 
											from service 
											where is_show=1 
											and lang='".$ims->conf['lang_cur']."' 
											and find_in_set('".$ims->conf['cur_group']."',group_nav) 
											order by show_order desc, date_create desc
											limit 0,1");
				if($row = $ims->db->fetch_row($result)){
					$ims->html->redirect_rel($ims->site->get_link ($this->modules, '', $row['friendly_link']));
				}
				//End
				break;
			case "content_only":
				$data['content'] = $info_lang['content'];
				break;
			default:
				$arr_in['where'] .= " and a.group_id!='".$info['group_id']."'";
				$data['content'] = html_list_group($arr_in);
				
				if($data['content']) {
				} else {
					$arr_in['where'] = " and find_in_set('".$info['group_id']."',group_nav)>0";
					$arr_in['temp'] = "list_item";
					$data['content'] = html_list_item($arr_in);
				}
				
				break;
		}
		
		$ims->temp_box->assign('data', $data);
		$ims->temp_box->parse("box_main");
		return $ims->temp_box->text("box_main");
	}
	
	function do_detail ($info = array())
	{
		global $ims;	
		
		$info['link_share'] = $ims->data['link_lang'][$ims->conf['lang_cur']];
		$info['date_update'] = date('d-m-Y',$info['date_update']);
		//$info['list_other'] = list_other (" and a.item_id!='".$info['item_id']."'");
		
		$ims->temp_act->assign('data', $info);
		$ims->temp_act->parse("item_detail");
		//return $ims->temp_act->text("item_detail");
		$data = array(
			'content' => $ims->temp_act->text("item_detail"),
			'title' => $info['title']
		);
		
		$ims->temp_box->assign('data', $data);
		$ims->temp_box->parse("box_main");
		return $ims->temp_box->text("box_main");
	}
	
  // end class
}
?>