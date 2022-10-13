<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "user";
	var $action  = "membership";
	var $sub 	 = "manage";
	var $template = "membership";
	
	/**
		* Khởi tạo
		* Quản lý sự kiện
	**/
	function __construct (){
		global $ims;

		$dir_assets  = $ims->func->dirModules($this->modules, 'assets');
		$ims->func->include_css($dir_assets."css/".$this->modules.'.css');
		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->modules,
			'js'  	 		 => $this->modules,
			'css'  	 		 => $this->template,
			'use_func'  	 => $this->modules, // Sử dụng func
			'use_navigation' => 0, // Sử dụng navigation
			'required_login' => 1, // Bắt buộc đăng nhập
		);
        $ims->func->loadTemplate($arrLoad);
		//custom tpl
		$dir_view      = $ims->func->dirModules($this->modules, 'views', 'path');
        $ims->temp_out = new XTemplate($dir_view . $this->template . ".tpl");
        $ims->temp_out->assign('CONF', $ims->conf);
        $ims->temp_out->assign('LANG', $ims->lang);
        $ims->temp_out->assign('DIR_IMAGE', $ims->dir_images);

		$data = array();
		$data['content'] = '';
		
	
		$ims->conf["class_full"] = 'user';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}

}