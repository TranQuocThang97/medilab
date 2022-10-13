<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain{
	var $modules  = "user";
	var $action   = "signin";
	var $sub 	  = "manage";
	var $template = "sign_in_up";
	var $sign_go  = "";

	function __construct () {
		global $ims;
		
		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->template,
			'js'  	 		 => $this->modules,
			'css'  	 		 => $this->template,
			'use_func'  	 => $this->modules, // Sử dụng func
			'use_navigation' => 0, // Sử dụng navigation
			'required_login' => 0, // Bắt buộc đăng nhập
		);
        $ims->func->loadTemplate($arrLoad);

		$this->sign_go = (isset($ims->get['url'])) ? $ims->func->base64_decode($ims->get['url']) : $ims->site_func->get_link('user');
		if($ims->site_func->checkUserLogin() == 1) {
			$ims->html->redirect_rel($this->sign_go);
		}
		
		$ims->conf['container_layout'] = 'full';
        $ims->conf["class_full"] = 'signin';
		$ims->output .=  $this->do_main();
	}
	
	function do_main (){
		global $ims;
		
		$link_action = $ims->site_func->get_link($this->modules, $ims->setting[$this->modules]["change_pass_link"]);
		$link_forget_password = $ims->site_func->get_link($this->modules, $ims->setting[$this->modules]['forget_pass_link']);
		$err = '';

//		$link = explode('/', $this->sign_go);
//		$check_store = $link[count($link)-1];
//		$store_link = $ims->db->load_item('store_setting', $ims->conf['qr'].' and setting_key = "store_link"', 'setting_value');

		$data = array();
		$data['err'] = $err;
		$data['link_action'] = $link_action;
		$data['link_root'] = $ims->conf['rooturl'];
		$data['link_login_go'] = $this->sign_go;
		$data['link_forget_password'] = $link_forget_password;
		$data['link_signup'] = $ims->site_func->get_link('user', $ims->setting['user']['signup_link']);
//		$data['url_fb'] = "https://www.facebook.com/dialog/oauth?client_id=".app_id_facebook."&redirect_uri=".redirect_uri_facebook;
//        $data['url_gg'] = "https://accounts.google.com/o/oauth2/auth?response_type=code&redirect_uri=".redirect_uri_google."&client_id=".client_id_google."&scope=email+profile&access_type=online&approval_prompt=auto";
//		$data['banner'] = $ims->site->get_banner('bg-signin',1);
//		$data['form_signin_title'] = ($check_store == $store_link) ? $ims->lang['user']['form_signin_store_title'] : $ims->lang['user']['form_signin_title'];
//		$data['form_signin_title_more'] = ($check_store == $store_link) ? $ims->lang['user']['form_signin_title_store_more'] : $ims->lang['user']['form_signin_title_more'];

		$data['logo'] = $ims->site->get_logo();
//		$data['short'] = ($ims->setting['user']['short'] != '') ? '<div class="short">'.$ims->func->input_editor_decode($ims->setting['user']['short']).'</div>' : '';
        $data['background'] = !empty($ims->setting['user']['signin_picture']) ? $ims->func->get_src_mod($ims->setting['user']['signin_picture']) : $ims->conf['rooturl'].'resources/images/use/sign-in.jpg';

		$data['signup_link'] = $ims->site_func->get_lang('signup_link', 'user', array( '[signup]' => '<a href="'.$data['link_signup'].'">'.$ims->lang['user']['signup_now'].'</a>'));
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("signin");
		return $ims->temp_act->text("signin");
	}
}
?>