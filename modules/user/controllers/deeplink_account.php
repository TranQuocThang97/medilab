<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "user";
	var $action = "deeplink_account";
	var $template  = "deeplink";
	var $sub = "manage";

	function __construct (){
		global $ims;

		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->template,
			'js'  	 		 => $this->modules,
			'css'  	 		 => $this->modules,
			'use_func'  	 => $this->modules, // Sử dụng func
            'use_navigation' => 0, // Sử dụng navigation
			'required_login' => 1, // Bắt buộc đăng nhập
		);
        $ims->func->loadTemplate($arrLoad);

		$data = array();
        if ($ims->data['user_cur']['is_affiliates'] == 1){
            $ims->html->redirect_rel($ims->site_func->get_link('user',$ims->setting['user']['deeplink_link']));
        }

        $data['content'] = '<div class="page_title">'.$ims->setting["user"]["deeplink_account_meta_title"].'</div>';
        $data['content'] .= $this->do_main();
		$data['box_left'] = box_left($this->action);
        $ims->conf['container_layout'] = 'm';
        $ims->conf['user_control'] = 'deeplink_account';
		$ims->conf["class_full"] = ' user deeplink_account ';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	
	function do_main ()
	{
		global $ims;	
		
		$link_action = $ims->site_func->get_link($this->modules, $ims->setting[$this->modules][$this->action."_link"]);
		$err = '';
		
		$data = $ims->data['user_cur'];
		$data['err'] = $err;
		$data['check_fb_gg'] = '';
		if($data['fb_id'] != '' || $data['gg_id'] != ''){
			$data['check_fb_gg'] = 'none';
			$ims->temp_act->assign('data', $data);
			$ims->temp_act->parse("deeplink_account.check_fb_gg");
		}
		$data['link_action'] = $link_action;
		$data['picture'] = $ims->func->get_src_mod($data['picture']);
		$data['link_change_pass'] = $ims->site_func->get_link($this->modules, $ims->setting[$this->modules]["change_pass_link"]);
  
		
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
//		$data['picture_src'] = $ims->func->get_src_mod($data['picture'], 150, 180, 1, 1);		
//		$data["link_up"] = $ims->conf['rooturl'].'ajax.php?m=library&sub=popup_library&type=1&fldr='.date('Y_m').'&editor=mce_0';
		// $data['birthday'] = ($data['birthday']) ? $data['birthday']) : '';
//		$data["list_gender"] = $ims->html->select ("gender",
//			array(0 => $ims->lang['global']['gender_0'],
//				1 => $ims->lang['global']['gender_1'],
//				2 => $ims->lang['global']['gender_2']
//			),
//			$data['gender'],
//			' class="form-control"'
//		);

        $data['upload_pic'] = $ims->site->get_form_upload_muti('affiliate_picture');
		
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("deeplink_account");
		return $ims->temp_act->text("deeplink_account");


		// $nd = array(
		// 	'content' => $ims->temp_act->text("deeplink_account"),
		// 	'title'	=> $ims->setting['user']['deeplink_account_meta_title'],
		// );
		// $ims->temp_box->assign('data',$nd);
		// $ims->temp_box->parse('box_main');
		// return $ims->temp_box->text('box_main');
	}
	
  // end class
}
?>