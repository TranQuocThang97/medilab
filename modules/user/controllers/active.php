<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "user";
	var $action  = "active";
	var $sub 	 = "manage";
	
	/**
		* Khởi tạo
		* Kích hoạt tài khoản
	**/
	function __construct ()
	{
		global $ims;


		$this->sign_go = (isset($ims->get['url'])) ? $ims->func->base64_decode($ims->get['url']) : $ims->site_func->get_link ('home');			
		if($ims->site_func->checkUserLogin() == 1) {
			$ims->html->redirect_rel($this->sign_go);
		}

			
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
		
		$data = array();
		if(isset($ims->get['code'])) {			
			$data['content'] = $this->do_main();
		} else {
			$ims->html->redirect_rel($this->sign_go);
		}

		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	
	function do_main ()
	{
		global $ims;	
		
		$user_code = (isset($ims->get['code'])) ? $ims->get['code'] : '';
		
		$sql = "SELECT user_id, username, password, user_code, token_login FROM user where is_show=0 AND user_code='".$user_code."' LIMIT 0,1";				
		$result = $ims->db->query($sql);
		if ($user = $ims->db->fetch_row($result)) {
			$col = array();
			$col["is_show"] = 0;
			$ok = $ims->db->do_update("user", $col, " user_id='".$user['user_id']."'");	
			if($ok) {				
				Session::Set('user_cur', array(
					'userid' => $user['user_id'],
					'username' => $user['username'],
					'password' => $user['password'],
					'session' => ''
				));				
				if($ims->deviceType == 'computer'){
					$link_go = $ims->site_func->get_link ($this->modules);
					$ims->html->redirect_rel($link_go);
				}else{
					$link_go = 'https://totvatot.com/redirect/profile/'.$user['token_login'];
					$ims->html->redirect_rel($link_go);
				}
			} else {
				$link_go = $ims->site_func->get_link ($this->modules, $ims->setting[$this->modules]["signin_link"]);
				$ims->html->alert ($ims->lang["user"]["not_found_page"], $link_go);
			}
		} else {
			$link_go = $ims->site_func->get_link ($this->modules, $ims->setting[$this->modules]["signin_link"]);
			$ims->html->alert ($ims->lang["user"]["not_found_page"], $link_go);
		}
	}
  	// End class
}
?>