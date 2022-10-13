<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain{
	var $modules = "gallery";
	var $action  = "register";
	var $sub 	 = "manage";

	function __construct (){
		global $ims;

		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->modules,
            'js'             => $this->modules,
			'css'  	 		 => $this->modules,
			'use_func'  	 => $this->modules, // Sử dụng func
			'use_navigation' => 0, // Sử dụng navigation
			'required_login' => 0, // Bắt buộc đăng nhập
		);

        $ims->func->loadTemplate($arrLoad);
		require_once ($this->modules."_func.php");
        $this->modFunc = new galleryFunc($this);

		$data = array();
        $ims->conf["cur_group"] = 0;
        $data['content'] = $this->do_list();

		$ims->conf['class_full'] = 'register';
		$ims->conf['container_layout'] = 'm';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
		
	function do_list (){
		global $ims;
        $note = $ims->lang['gallery']['note'];
        $time_end = @mktime(23, 59, 59, 11, 15, 2021);
        if(time() < $time_end){
            $ims->temp_act->assign('upload_pic', $ims->site->get_form_upload_muti());
            $ims->temp_act->parse('register.button');
        }else{
            $note = $ims->lang['gallery']['finished_register'];
            $ims->temp_act->assign('disabled', 'disabled');
        }
        $ims->temp_act->assign('note', $note);
        $ims->temp_act->parse('register');
        return $ims->temp_act->text('register');
	}
	
  // end class
}
?>