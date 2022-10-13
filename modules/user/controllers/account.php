<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "user";
	var $action  = "account";
	
	/**
		* Khởi tạo
		* Thông tin tài khoản
	**/
	function __construct ()
	{
		global $ims;

		if(isset($ims->get['code']) && $ims->get['code'] != ''){
			$ims->site_func->loginWithFacebook();			
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
	
	function do_main (){
		global $ims;

		$data = $ims->data['user_cur'];
		$dir = date('Y_m');
		$resource = $ims->conf['rooturl'].'resources/images/';
		$default_img = $resource.'user/default-avatar.png';
		$data['picture'] = $ims->site_func->get_form_pic ('picture', !empty($data['picture'])?$data['picture']:$default_img,  $dir);

		$data['check_fb_gg'] = '';
		if($data['fb_id'] != '' || $data['gg_id'] != ''){
			$data['check_fb_gg'] = 'none';
			$ims->temp_act->assign('data', $data);
			$ims->temp_act->parse("account.check_fb_gg");
		}
		// $data['picture_src']   = $ims->func->get_src_mod($data['picture'], 100, 100, 1, 0, array('fix_max' => '1'));
        $data["list_province"] = $ims->site_func->selectLocation (
        	"province",
        	"vi", 
        	$data["province"],
        	" class='form-control select_location_province' data-district='district' data-ward='ward' id='province' ", 
        	array('title' => $ims->lang["user"]["select_title"]),
        	"province"
        );
		$data["list_district"] = $ims->site_func->selectLocation (
			"district", 
			$data['province'], 
			$data['district'],
			" class='form-control select_location_district' data-ward='ward' id='district' ", 
        	array('title' => $ims->lang["user"]["select_title"]),
			"district"
		);
		$data["list_ward"] = $ims->site_func->selectLocation (
			"ward",
			$data['district'], 
			$data['ward'],
			" class='form-control' id='ward' ", 
        	array('title' => $ims->lang["user"]["select_title"]),
			"ward"
		);

		// $tmp = strtotime("23-04-1992");
		// $tmp = time() - (60*60*24*3);
		// print_r(date("d/m/Y",$tmp));

		$data["list_date"] = $data["list_month"] = $data["list_year"] = '';
		for ($i=1; $i <= 31 ; $i++) { 
			$i = str_pad($i,2,"0",STR_PAD_LEFT);
			$select = (date("d",$data["birthday"]) == $i)?"selected":'';
			$data["list_date"] .= '<option value="'.$i.'" '.$select.'>'.$i.'</option>';
		}
		for ($i=1; $i <= 12 ; $i++) { 
			$i = str_pad($i,2,"0",STR_PAD_LEFT);
			$select = (date("m",$data["birthday"]) == $i)?"selected":'';
			$data["list_month"] .= '<option value="'.$i.'" '.$select.'>'.$i.'</option>';
		}
		for ($i=date("Y",time()); $i >= 1900 ; $i--) { 
			$select = (date("Y",$data["birthday"]) == $i)?"selected":'';
			$data["list_year"] .= '<option value="'.$i.'" '.$select.'>'.$i.'</option>';
		}
		$data["src_fb"] = $resource.'user/sync-facebook.png';
		$data["src_gg"] = $resource.'user/sync-google.png';
		$data["src_zl"] = $resource.'user/sync-zalo.png';


        if(empty($ims->data['user_cur']['fb_id'])){
            $data["link_fb"] = "https://www.facebook.com/dialog/oauth?client_id=".app_id_facebook."&redirect_uri=".redirect_uri_facebook;
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("account.sync_fb0");
        }else{
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("account.sync_fb1");
        }

        if(empty($ims->data['user_cur']['gg_id'])){
            $data["link_gg"] = "https://accounts.google.com/o/oauth2/auth?response_type=code&redirect_uri=".redirect_uri_google."&client_id=".client_id_google."&scope=email+profile&access_type=online&approval_prompt=auto";
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("account.sync_gg0");
        }else{
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("account.sync_gg1");
        }

        if(empty($ims->data['user_cur']['zl_id'])){
            $data["link_zl"] = "https://oauth.zaloapp.com/v3/permission?app_id=".app_id_zalo."&redirect_uri=".redirect_uri_zalo."&state='";
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("account.sync_zl0");
        }else{
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("account.sync_zl1");
        }
		// print_r(date("d/m/Y",$tmp));
		// $data['picture_src'] = $ims->func->get_src_mod($data['picture'], 150, 180, 1, 1);
        if($data['email'] != ''){
            $data['readonly'] = 'readonly';
        }
        $data['checked'.$data['gender']] = 'checked';
		$data['page_title'] = $ims->conf["meta_title"];
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("account");
		return $ims->temp_act->text("account");
	}
  	// End class
}
?>