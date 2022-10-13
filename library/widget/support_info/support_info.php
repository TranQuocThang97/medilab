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

class widget_support_info
{
	var $widget = "support_info";
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
		
		$tmp = explode(',',$ims->conf['hotline_support']);
		foreach($tmp as $hotline) {
			$hotline= trim($hotline);
			if($hotline) {
				$this->temp->assign('row', array(
					'value' => $hotline,
					'class_li' => (substr($hotline,0,1) == '(') ? 'phone' : 'mobile'
				));
				$this->temp->parse("main.li");
			}
		}
		
		if($ims->conf['fanpage_facebook']) {
			$this->temp->assign('row', array(
				'share_title' => $ims->conf['share_title'],
				'link' => $ims->conf['fanpage_facebook']
			));
			$this->temp->parse("main.link");
		}

		$this->temp->parse("main");
		$this->output = $ims->html->temp_box('box', array(
			'class_box' => 'box_support_info',
			'title' => $ims->lang['widget_support_info']['widget_title'],
			'content' => $this->temp->text("main")
		));
		return $this->output;
	}
	
  // end class
}
?>