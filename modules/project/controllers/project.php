<?php
if (!defined('IN_ims')) {die('Access denied');}
$nts = new sMain();

class sMain
{
	var $modules = "project";
	var $action  = "project";
	var $sub 	 = "manage";
	
	/**
		* function __construct ()
		* Khoi tao 
	**/
	function __construct ()
	{
		global $ims;		
		
		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->modules,
			'css'  	 		 => $this->modules,
			'use_func'  	 => $this->modules, // Sử dụng func
			'use_navigation' => 1, // Sử dụng navigation
			'required_login' => 0, // Bắt buộc đăng nhập
		);

        $ims->func->loadTemplate($arrLoad);	
		
		require_once ($this->modules."_func.php");
        $this->modFunc = new projectFunc($this);

		$data = array();
		if(isset($ims->conf['cur_group']) && isset($ims->data['cur_group']) && $ims->data['cur_group']){
			$row = $ims->data['cur_group'];
			//Current menu
			$arr_group_nav = (!empty($row["group_nav"])) ? explode(',',$row["group_nav"]) : array();
			foreach($arr_group_nav as $v) {
				$ims->conf['menu_action'][] = $this->modules.'-group-'.$v;
			}
			//End current menu
			
			//Make link lang
			$result = $ims->db->query("select friendly_link,lang   
										from project_group 
										where group_id='".$ims->conf['cur_group']."' ");
			while($row_lang = $ims->db->fetch_row($result)){
				$ims->data['link_lang'][$row_lang['lang']] = $ims->site_func->get_link_lang($row_lang['lang'], $this->modules, $row_lang['friendly_link']);
			}
			//End Make link lang
			//SEO
			$ims->site->get_seo ($ims->data['cur_group']);
			// $ims->conf['container_layout'] = 'm';
			
			$ims->conf["cur_group_nav"] = $row["group_nav"];			
			$data = array();
			// $data['content'] = $this->do_focus();
			$data['content'] = $this->do_list_group($row);
			// $data['box_column'] = $this->modFunc->box_column ();

			$ims->conf["class_full"] = 'project';

		}else{
			if((!isset($ims->input['keyword']) || !$ims->input['keyword']) && (!isset($ims->input['tag']) || !$ims->input['tag'])) {
				//$ims->html->redirect_rel($ims->site_func->get_link('home'));
			}
			
			//Make link lang
			foreach($ims->data['lang'] as $row_lang) {
				$ims->data['link_lang'][$row_lang['name']] = $ims->site_func->get_link_lang($row_lang['name'], $this->modules);
			}
			//End Make link lang
			
			//SEO
			$ims->site->get_seo (array(
				'meta_title' => (isset($ims->setting['project']["project_meta_title"])) ? $ims->setting['project']["project_meta_title"] : '',
				'meta_key' => (isset($ims->setting['project']["project_meta_key"])) ? $ims->setting['project']["project_meta_key"] : '',
				'meta_desc' => (isset($ims->setting['project']["project_meta_desc"])) ? $ims->setting['project']["project_meta_desc"] : ''
			));
			$ims->conf["cur_group"] = 0;
			$data = array(
				"content" => $this->do_list(),
			);
			// $data['box_column'] = $this->modFunc->box_column ();
			$ims->conf["class_full"] = 'project';

		}		
		$ims->conf['container_layout'] = 'm';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	
	function do_list (){
		global $ims;
      
//      if(isset($ims->post['keyword'])) {
//         $ims->html->redirect_rel($ims->site_func->get_link('project', '', '', array('keyword'=>$ims->post['keyword'])));
//         die;
//      }

		$keyword = (isset($ims->input['keyword'])) ? trim($ims->input['keyword']) : '';
		$tag = (isset($ims->input['tag'])) ? trim($ims->input['tag']) : '';
		$ext = '';
		$where = '';
		if($keyword) {
			$ext = '&keyword='.$keyword;
			//$text_search = $ims->func->get_text_search ($str);
			$arr_key = explode(' ',$keyword);
			$arr_tmp = array();
			foreach($arr_key as $value) {
				$value = trim($value);
				if(!empty($value)) {
					$arr_tmp[] = "title like '%".$value."%'";
					//$arr_tmp[] = "content like '%".$value."%'";
				}	
			}
			if(count($arr_tmp) > 0) {
				//$where .= " and (".implode(" or ",$arr_tmp).")";
				$where .= " and (".implode(" and ",$arr_tmp).")";
			}
		} elseif($tag) {
			$ext = '&tag='.$tag;
			$where .= " and find_in_set('".$tag."', tag_list)";
		}
		
		
		$arr_in = array(
			'link_action' => $ims->site_func->get_link('project'),
			'where' => $where,
			'ext' => $ext,
			'temp' => 'list_item',
		);
		$list_item = $this->modFunc->html_list_item($arr_in);
		$data = array(
			'content' => $list_item,
			'class' => 'project_content',
			// 'title' => $ims->lang['project']['mod_title'],
		);
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("project");
		return $ims->temp_act->text("project");
  //       $ims->temp_box->assign('data', $data);
		// $ims->temp_box->parse("box_main");
		// return $ims->temp_box->text("box_main");
	}
	
	function do_list_group ($info = array()) {
		global $ims;	
		
		$arr_in = array(
			'link_action' => $ims->site_func->get_link('project',$info['friendly_link']),
			'where' => " and find_in_set('".$info['group_id']."',group_nav)>0",
			'temp' => 'list_item',
		);
		// $project_focus = $ims->site->project_focus();
		$column_mini = $this->column_mini ($info);
		$list_item = $this->modFunc->html_list_item($arr_in);
		$data = array(
			// 'project_focus' => $project_focus,
			// 'nav' => $ims->navigation,
			'column_mini' => $column_mini,
			'class' => ($column_mini) ? '' : 'full_content', 
			'content' => $list_item,
			// 'title' => $info['title']
		);
		
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("project");
		return $ims->temp_act->text("project");
		
		/*$ims->temp_box->assign('data', $data);
		$ims->temp_box->parse("box_main");
		return $ims->temp_box->text("box_main");*/
	}
  // end class
}
?>