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

class widget_menu_group
{
	var $widget = "menu_group";
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
	
	//=================box_menu_sub===============
	function box_menu_sub ($array=array())
	{
		global $ims;

		$output = '';
		$arr_cur = ($ims->conf['cur_group'] > 0 && isset($ims->conf["cur_group_nav"])) ? explode(',',$ims->conf["cur_group_nav"]) : array();
		
		$menu_sub = '';
		$num = count($array);
		$i = 0;
		foreach($array as $row)
		{
			$i++;
			$row['link'] = $ims->site->get_link ($ims->conf['cur_mod'],$row['friendly_link']);
			$class_li = array();
			if($i == 1) {
				$class_li[] = 'first';
			}
			if($i == $num) {
				$class_li[] = 'last';
			}
			$row['class_li'] = (count($class_li) > 0) ? ' class="'.implode(' ',$class_li).'"' : '';
			$row['class'] = (in_array($row["group_id"],$arr_cur)) ? ' class="current"' : '';
			$row['input_choose'] = '<input type="checkbox" name="product_group[]" class="product_group_view" value = "'.$row['group_id'].'"/>';
			$row['menu_sub'] = '';
			if(isset($row['arr_sub'])){
				$row['menu_sub'] = $this->box_menu_sub ($row['arr_sub']);
			}
			$ims->temp_box->assign('row', $row);
			$ims->temp_box->parse("box_menu.menu_sub.row");
			$menu_sub .= $ims->temp_box->text("box_menu.menu_sub.row");
			$ims->temp_box->reset("box_menu.menu_sub.row");
		}
		
		$ims->temp_box->reset("box_menu.menu_sub");
		$ims->temp_box->assign('data', array('content' => $menu_sub));
		$ims->temp_box->parse("box_menu.menu_sub");
		return $ims->temp_box->text("box_menu.menu_sub");
	}
	
	//=================box_menu===============
	function box_menu () {
		global $ims;
		$temp = 'box_menu';
		$output = '';
		$arr_cur = (isset($ims->conf['cur_group']) && $ims->conf['cur_group'] > 0 && isset($ims->conf["cur_group_nav"])) ? explode(',',$ims->conf["cur_group_nav"]) : array();

		$ims->load_data->data_group ($ims->conf['cur_mod']);
		
		if(($num = count($ims->data[$ims->conf['cur_mod']."_group_tree"])) > 0){
			$data = array(
				'title' => (isset($ims->lang['widget_menu_group']['menu_'.$ims->conf['cur_mod']]) ? $ims->lang['widget_menu_group']['menu_'.$ims->conf['cur_mod']] : $ims->lang['widget_menu_group']['widget_title']),
				'content' => ''
			);
			//echo $data['title'];die();
			$menu_sub = '';
			$i = 0;
			//echo $ims->conf['cur_mod'];die();
			//$row=$ims->data[$ims->conf['cur_mod']."_group_tree"];
			//print_arr($row);die();
			
			foreach($ims->data[$ims->conf['cur_mod']."_group_tree"] as $row)
			{
				$i++;
				//if($row['is_focus']==1){
            //print_arr($row);die();
            $row['link'] = $ims->site->get_link ($ims->conf['cur_mod'],$row['friendly_link']);
            $class_li = array();
            $class_li[] = 'menu_li';
            if($i == 1) {
               $class_li[] = 'first';
            }
            if($i == $num) {
               $class_li[] = 'last';
            }
            $row['class_li'] = (count($class_li) > 0) ? ' class="'.implode(' ',$class_li).'"' : '';

            $row['class'] = (in_array($row["group_id"],$arr_cur)) ? 'current' : '';
            $row['menu_sub'] = '';
            if(isset($row['arr_sub'])){
                $row['menu_sub'] = $this->box_menu_sub ($row['arr_sub']);
                $row['open_sub'] = '<i class="ficon-angle-down"></i>';
           	    $row['class'] = ' class="has-sub menu_link '.$row['class'].'"';
            }
            else{
           	   $row['class'] = ' class="menu_link '.$row['class'].'"';
            }
            $ims->temp_box->assign('row', $row);
            $ims->temp_box->parse("box_menu.menu_sub.row");
            $menu_sub .= $ims->temp_box->text("box_menu.menu_sub.row");
            $ims->temp_box->reset("box_menu.menu_sub.row");
				//}
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