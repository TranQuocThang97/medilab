<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "user";
	var $action  = "change_pass";
	var $sub 	 = "manage";
	
	/**
		* Khởi tạo
		* Đổi mật khẩu
	**/
	function __construct ()
	{
		global $ims;

		if(isset($ims->get['code']) && $ims->get['code'] != ''){
		 	$ims->site_func->loginWithGoogle();
		}

		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->modules,
			'js'  	 		 => $this->modules,
			'css'  	 		 => $this->modules,
			'use_func'  	 => $this->modules, // Sử dụng func
			'use_navigation' => 0, // Sử dụng navigation
			'required_login' => 1, // Bắt buộc đăng nhập
		);
        $ims->func->loadTemplate($arrLoad);
		
		$data = array();
		$data['content']  = $this->do_main();
		$data['box_left'] = box_left($this->action);
		$ims->conf["class_full"] = 'user';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	
	function do_main ()
	{
		global $ims;
		
		$data = $ims->data['user_cur'];
		$data['forget_password'] = $ims->site_func->get_link($this->modules, $ims->setting[$this->modules]['forget_pass_link']);
		$data['page_title'] = $ims->conf["meta_title"];
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("change_pass");
		return $ims->temp_act->text("change_pass");
	}
  	// End class
}
?>