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

class widget_menu_item
{
	var $widget = "menu_item";
	var $output = '';
	var $temp = '';
	
	/**
	* function __construct ()
	* Khoi tao 
	**/
	function __construct ()
	{
		global $ims;
		
		$ims->func->load_language_widget($this->widget);
		
		$file_tbl = $ims->path_html.'widget'.DS.$this->widget.".tpl";
		if(!file_exists($file_tbl)) {
			$file_tbl = $ims->conf['rootpath'].DS."widget".DS.$this->widget.DS.$this->widget.".tpl";
		}
		$file_css = $ims->path_css."widget".DS.$this->widget.DS.$this->widget.".css";
		if(file_exists($file_css)) {
			$ims->func->include_css ($ims->dir_css.'widget/'.$this->widget.'/'.$this->widget.".css");
		}

		$this->temp = new XTemplate($file_tbl);
		$this->temp->assign('CONF', $ims->conf);
		$this->temp->assign('LANG', $ims->lang);
		$this->temp->assign('DIR_IMAGE', $ims->dir_images);
	}
	
	//=================main===============
	function do_main ()
	{
		global $ims;
		
		if($this->output) {
			return $this->output;
		}

		$data = array();		
		$data['content'] = $this->box_menu();
	
		$this->temp->reset("main");
		$this->temp->assign('data', $data);
		$this->temp->parse("main");
		$this->output = $this->temp->text("main");
		return $this->output;
	}
	
	//=================box_menu===============
	function box_menu () {
		global $ims;
		
		$cur_group = ($ims->conf['cur_group'] > 0) ? $ims->conf['cur_group'] : 0;
		$arr_cur = (isset($ims->conf['cur_item']) && $ims->conf['cur_item'] > 0) ? array($ims->conf['cur_item']) : array();
		$group_data = $ims->load_data->data_group ($ims->conf['cur_mod']);
		$table_data = $ims->load_data->data_table (
			$ims->conf['cur_mod'].' t,'.$ims->conf['cur_mod'].'_lang tl', 
			'item_id', 
			't.item_id, title, friendly_link, skin', 
			"t.item_id=tl.item_id and lang='".$ims->conf['lang_cur']."' and is_show=1 and find_in_set('".$cur_group."', group_nav)
			
			 order by show_order desc, date_create desc
			limit 0,5"
		);
		
		$output = '';
		if(($num = count($table_data)) > 0){
			$data = array(
				'title' => (isset($ims->lang['widget_menu_item']['menu_'.$ims->conf['cur_mod']]) ? $ims->lang['widget_menu_item']['menu_'.$ims->conf['cur_mod']] : $ims->lang['widget_menu_item']['widget_title']),
				'content' => ''
			);
			$data['title'] = (isset($group_data[$cur_group]['title'])) ? $group_data[$cur_group]['title'] : $data['title'];
			
			$menu_sub = '';
			$i = 0;
			foreach($table_data as $row)
			{
				$i++;
				$row['link'] = $ims->site->get_link ($ims->conf['cur_mod'], '',$row['friendly_link']);
				if($row['skin']==1){
				$row['target']='target="_blank"';;	
				}
				$class_li = array();
				$class_li[] = 'menu_li';
				if($i == 1) {
					$class_li[] = 'first';
				}
				if($i == $num) {
					$class_li[] = 'last';
				}
				$row['class_li'] = (count($class_li) > 0) ? ' class="'.implode(' ',$class_li).'"' : '';
				
				$row['class'] = (in_array($row["item_id"],$arr_cur)) ? 'current' : '';
				$row['class'] = ' class="menu_link '.$row['class'].'"';

				$ims->temp_box->assign('row', $row);
				$ims->temp_box->parse("box_menu.menu_sub.row");
				$menu_sub .= $ims->temp_box->text("box_menu.menu_sub.row");
				$ims->temp_box->reset("box_menu.menu_sub.row");
			}		
			
			$ims->temp_box->reset("box_menu.menu_sub");
			$ims->temp_box->assign('data', array('content' => $menu_sub));
			$ims->temp_box->parse("box_menu.menu_sub");
			
			$ims->temp_box->reset("box_menu");
			$ims->temp_box->assign('data', $data);
			$ims->temp_box->parse("box_menu");
			$output = $ims->temp_box->text("box_menu");
		}
		
		return $output;
	}
	
  // end class
}
?>