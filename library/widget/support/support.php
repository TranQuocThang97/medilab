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

class widget_support
{
	var $widget = "support";
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
		
		$result = $ims->db->query("select *   
														from support 
														where is_show=1
														order by show_order desc, date_update asc");
		if($num = $ims->db->num_rows($result)){
			while($row = $ims->db->fetch_row($result)){
				$row['arr_title'] = unserialize($row['arr_title']);
				$row['title'] = $row['arr_title'][$ims->conf['lang_cur']];
				if(isset($row['yahoo']) || isset($row['skype'])) {
					$row['content'] = '';
					
					$this->temp->reset("main.row.yahoo");
					$this->temp->reset("main.row.skype");
					
					if(!empty($row['yahoo'])) {
						/*$status = @file_get_contents('http://opi.yahoo.com/online?u='.$row['yahoo'].'&m=s&t=1');
						$status = ($status == '01') ? 'on' : 'off';
						$status = 'on';*/
						
						$this->temp->assign('row', $row);
						$this->temp->parse("main.row.yahoo");
					}
					
					if(!empty($row['skype'])) {
						/*$status = @file_get_contents('http://mystatus.skype.com/'.$row['skype'].'.num');
						$status = (in_array($status, array(0,1,6))) ? 'off' : 'on';
						$status = 'on';*/

						$this->temp->assign('row', $row);
						$this->temp->parse("main.row.skype");
					}
					
					$this->temp->assign('row', $row);
					$this->temp->parse("main.row");
				}
			}
			$this->temp->parse("main");
			$this->output = $ims->html->temp_box('box', array(
				'class_box' => 'box_support',
				'title' => $ims->lang['widget_support']['widget_title'],
				'content' => $this->temp->text("main")
			));
		}
		
		return $this->output;
	}
	
  // end class
}
?>