<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "user";
	var $action  = "change_pass_otp";
	var $sub 	 = "manage";
	
	/**
		* Khởi tạo
		* Đổi mật khẩu
	**/
	function __construct ()
	{
		global $ims;

		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->action,
			'js'  	 		 => $this->modules,
			'css'  	 		 => $this->modules,
			'use_func'  	 => $this->modules, // Sử dụng func
			'use_navigation' => 0, // Sử dụng navigation
			'required_login' => 0, // Bắt buộc đăng nhập
		);
        $ims->func->loadTemplate($arrLoad);
		
		$data = array();
		$data['content']  = $this->do_main();
		$ims->conf["class_full"] = 'user';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	
	function do_main ()
	{
		global $ims;
		
		$data = array();
		$data['phone'] = Session::GET ('otp_pw_request');		
		$data['forget_password'] = $ims->site_func->get_link($this->modules, $ims->setting[$this->modules]['forget_pass_link']);
		$data['page_title'] = $ims->conf["meta_title"];

		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("change_pass_otp");
		return $ims->temp_act->text("change_pass_otp");
	}
  	// End class
}
?>