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

class widget_maillist
{
	var $widget = "maillist";
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
		$data['form_id'] = $ims->func->random_str(10);
	
		$this->temp->assign('data', $data);
		$this->temp->parse("main");
		$this->output = $this->temp->text("main");
		return $this->output;
	}
	
  // end class
}
?>