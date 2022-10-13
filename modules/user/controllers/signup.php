<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules  = "user";
	var $action   = "signup";
	var $sub	  = "manage";
	var $template = "sign_in_up";
	var $sign_go  = "";
	
	/**
		* function __construct ()
		* Khoi tao 
		* Đăng ký tài khoản
	**/
	function __construct ()
	{
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

		$this->sign_go = (isset($ims->get['url'])) ? $ims->func->base64_decode($ims->get['url']) : $ims->site_func->get_link ('user');
		if($ims->site_func->checkUserLogin() == 1) {
			$ims->html->redirect_rel($this->sign_go);
		}
		
		$ims->conf['container_layout'] = 'full';
        $ims->conf["class_full"] = 'signup';
		$ims->output .=  $this->do_main();
	}
	
	function do_main ()
	{
		global $ims;	
		

		// $check_contributor = Session::Get('user_contributor');
		$check_contributor = isset($_COOKIE['user_contributor']) ? $_COOKIE['user_contributor'] : '';
		$link_action = $ims->site_func->get_link ($this->modules, $ims->setting[$this->modules]["change_pass_link"]);
		$err = '';
		
		$data = array();
		$data['err'] = $err;
		$data['link_action']   = $link_action;

		$data["list_province"] = $ims->site_func->selectLocation (
        	"province",
        	"vi", 
        	"",
        	" class='form-control select_location_province' data-district='district' data-ward='ward' id='province' ", 
        	array('title' => $ims->lang["user"]["select_title"]),
        	"province"
        );
		$data["list_district"] = $ims->site_func->selectLocation (
			"district",
			"", 
			"",
			" class='form-control select_location_district' data-ward='ward' id='district' ", 
        	array('title' => $ims->lang["user"]["select_title"]),
			""
		);
		$data["list_ward"] = $ims->site_func->selectLocation (
			"ward",
			"", 
			"",
			" class='form-control' id='ward' ", 
        	array('title' => $ims->lang["user"]["select_title"]),
			""
		);
		$data['link_root'] = $ims->conf['rooturl'];
		$data['link_login_go'] = $this->sign_go;
		$data['link_signin'] = $ims->site_func->get_link('user', $ims->setting['user']['signin_link']);
		$data['signin_link'] = $ims->site_func->get_lang('signin_link', 'user', array( '[signin]' => '<a href="'.$data['link_signin'].'">'.$ims->lang['user']['signin_now'].'</a>'));
		if($check_contributor != ''){
			$user_row = $ims->db->load_row("user"," user_code ='".$check_contributor."' ");
            if(!empty($user_row)){
            	$ims->temp_act->assign('user', $user_row);
				$ims->temp_act->parse("signup.contributor");
            }
		}
        $data['logo'] = $ims->site->get_logo();
//        $data['short'] = ($ims->setting['user']['short'] != '') ? '<div class="short">'.$ims->func->input_editor_decode($ims->setting['user']['short']).'</div>' : '';
        $data['background'] = !empty($ims->setting['user']['signup_picture']) ? $ims->func->get_src_mod($ims->setting['user']['signup_picture']) : $ims->conf['rooturl'].'resources/images/use/sign-in.jpg';

		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("signup");
		return $ims->temp_act->text("signup");
	}
  	// End class
}
?>