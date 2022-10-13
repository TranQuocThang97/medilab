<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
    var $modules  = "user";
    var $action   = "forget_pass";
    var $sub	  = "manage";
    var $template = "sign_in_up";
    var $sign_go  = "";

	function __construct ()
	{
		global $ims;
		
		$this->sign_go = (isset($ims->get['url'])) ? $ims->func->base64_decode($ims->get['url']) : $ims->site_func->get_link ('');
		// if($ims->site_func->checkUserLogin() == 1) {
		// 	$ims->html->redirect_rel($this->sign_go);
		// }

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
		
		$data = array();
		$data['content'] = '';
		if(isset($ims->get['code'])) {
			$data['content'] .= $this->do_reset_pass();
		} else {
			$data['content'] .= $this->do_main();
		}

        $ims->conf['container_layout'] = 'full';
		$ims->conf["class_full"] = 'user';
//		$ims->temp_act->assign('data', $data);
//		$ims->temp_act->parse("main_forget");
//		$ims->output .=  $ims->temp_act->text("main_forget");
		$ims->output .=  $data['content'];
	}
	
	function do_main ()
	{
		global $ims;	
		
		$link_action = $ims->site_func->get_link ($this->modules, $ims->setting[$this->modules]["forget_pass_link"]);
		
		$data = array();
		$data['content'] = $ims->func->input_editor_decode($ims->setting[$this->modules]["forget_pass_content"]);
		$data['page_title'] = $ims->conf["meta_title"];

        $data['link_signin'] = $ims->site_func->get_link('user', $ims->setting['user']['signin_link']);
        $data['logo'] = $ims->site->get_logo();
//        $data['short'] = ($ims->setting['user']['short'] != '') ? '<div class="short">'.$ims->func->input_editor_decode($ims->setting['user']['short']).'</div>' : '';
        $data['background'] = ($ims->setting['user']['signin_picture'] != '') ? $ims->func->get_src_mod($ims->setting['user']['signin_picture']) : $ims->conf['rooturl'].'resources/images/use/sign-in.jpg';

		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("forget_pass");
		return $ims->temp_act->text("forget_pass");
	}
	
	function do_reset_pass ()
	{
		global $ims;	
		
		$user_code = $ims->func->if_isset($ims->get['code']);
		$check = $ims->db->load_row("user", " is_show=1 AND user_code='".$user_code."' ", "user_id, username, pass_reset, session");		
		if (!empty($check)) {
			$col = array();
			$col["password"] = $ims->func->md25($check["pass_reset"]);
			$ok = $ims->db->do_update("user", $col, " user_id='".$check['user_id']."'");
			if($ok) {
				Session::Set('user_cur', array(
					'userid' => $check['user_id'],
					'username' => $check['username'],
					'password' => $col['password'],
					'session' => $check['session']
				));
				unset($ims->get['code']);
				$link_go = $ims->site_func->get_link ($this->modules, $ims->setting["user"]["change_pass_link"]);				
				$ims->html->redirect_rel($link_go);
			} else {
				$link_go = $ims->site_func->get_link ($this->modules, $ims->setting[$this->modules]["signin_link"]);
				$ims->html->alert ($ims->lang["user"]["not_found_page"], $link_go);
			}
		} else{
			$link_go = $ims->site_func->get_link ($this->modules, $ims->setting[$this->modules]["signin_link"]);
			$ims->html->alert ($ims->lang["user"]["not_found_page"], $link_go);
		}
	}
  	// End class
}
?>