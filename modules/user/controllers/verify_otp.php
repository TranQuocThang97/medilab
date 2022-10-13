<?php
if (! defined('IN_ims')) {
  die('Access denied');
}
$nts = new sMain();

class sMain
{
	var $modules = "user";
	var $action = "verify_otp";
	var $sub = "manage";
	
	/**
	* function __construct()
	* Khoi tao 
	**/
	function __construct()
	{
		global $ims;
		
		 $arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->modules,
			'js'  	 		 => $this->modules,
			'css'  	 		 => $this->modules,
			'use_func'  	 => $this->modules, // Sử dụng func
			'use_navigation' => 0, // Sử dụng navigation
			'required_login' => 0, // Bắt buộc đăng nhập
		);
    $ims->func->loadTemplate($arrLoad);

		$this->sign_go = (isset($ims->get['url'])) ? $ims->func->base64_decode($ims->get['url']) : $ims->site_func->get_link ('user');
		if($ims->site_func->checkUserLogin() == 1) {
			$ims->html->redirect_rel($this->sign_go);
		}
		
		$ims->conf['container_layout'] = 'm';
    $ims->conf["class_full"] = 'signup';
		$ims->output .=  $this->do_main();
	}
	
	function do_main (){
		global $ims;
		$data = array();
		$data = Session::Get('signup_info', array());		
		$data['note'] = $ims->site_func->get_lang ('otp_note', 'user', array('[phone]' => '<b>'.$data['phone'].'</b>'));
		$data['link_go'] =  $ims->site_func->get_link ('user');
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("otp");
		return $ims->temp_act->text("otp");
	}	
  // end class
}
?>