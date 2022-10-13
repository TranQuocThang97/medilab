<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "user";
	var $action  = "user";
	var $sub 	 = "manage";
	var $template = "account";

	function __construct (){
		global $ims;
		
		$dir_assets  = $ims->func->dirModules($this->modules, 'assets');
		$ims->func->include_css($dir_assets."css/".$this->modules.'.css');
		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->modules,
			'js'  	 		 => $this->modules,
			'css'  	 		 => $this->template,
			'use_func'  	 => $this->modules, // Sử dụng func
			'use_navigation' => 0, // Sử dụng navigation
			'required_login' => 1, // Bắt buộc đăng nhập
		);
        $ims->func->loadTemplate($arrLoad);	
        //custom tpl
		$dir_view      = $ims->func->dirModules($this->modules, 'views', 'path');
        $ims->temp_out = new XTemplate($dir_view . $this->template . ".tpl");
        $ims->temp_out->assign('CONF', $ims->conf);
        $ims->temp_out->assign('LANG', $ims->lang);
        $ims->temp_out->assign('DIR_IMAGE', $ims->dir_images);
		// $ims->func->include_js ($ims->dir_js.'jquery.copy-to-clipboard.js');
		$ims->func->include_js ($ims->dir_js.'croppie/croppie.min.js');
		$ims->func->include_css ($ims->dir_js.'croppie/croppie.min.css');
		
		$dir_global_js = $ims->func->dirModules("global", "assets", "js");
		$ims->func->include_js ($dir_global_js.'/location.js');

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
		
		$link_action = $ims->site_func->get_link($this->modules);
		$err = '';
		
		$data = $ims->data['user_cur'];	
		$data['src'] = $ims->conf['rooturl'].'resources/images/user/';

		if(!empty($data['picture'])){
			$data['picture'] = $ims->func->get_src_mod($data['picture']);
		}else{
			$data['picture'] = $data['src'].'avatar-default.png';
		}
		$data['icon_crop'] = $data['src'].'avatar-crop.svg';
		$data['icon_remove'] = $data['src'].'avatar-remove.svg';
		$data['link_root'] = $ims->conf['rooturl'];
		$data['link'] = $ims->conf['rooturl'].'?contributor='.$ims->func->base64_encode($data['user_code']).'&type=link';
		$data['link_mail'] = $ims->conf['rooturl'].'?contributor='.$ims->func->base64_encode($data['user_code']).'&type=mail';

		$data["list_country"] = $ims->site_func->selectLocation (
        	"country",
        	"vi", 
        	$data["country"],
        	" class='form-control' ", 
        	array('title' => $ims->lang["user"]["country_select"]),
        	"country"
        );

		$data["list_province"] = $ims->site_func->selectLocation (
        	"province",
        	"vi", 
        	$data["province"],
        	" class='form-control select_location_province' data-district='district' data-ward='ward' id='province' ", 
        	array('title' => $ims->lang["user"]["province"]),
        	"province"
        );
		$data["list_district"] = $ims->site_func->selectLocation (
			"district", 
			$data['province'], 
			$data['district'],
			" class='form-control select_location_district' data-ward='ward' id='district' ", 
        	array('title' => $ims->lang["user"]["district"]),
			"district"
		);
		$data["list_ward"] = $ims->site_func->selectLocation (
			"ward",
			$data['district'], 
			$data['ward'],
			" class='form-control' id='ward' ", 
        	array('title' => $ims->lang["user"]["ward"]),
			"ward"
		);
		if(!empty($data['full_name'])){
			$full_name = explode(" ",$data['full_name']);
			$data['first_name'] = !empty($data['first_name'])?$data['first_name']:$full_name[count($full_name) - 1]; 
			unset($full_name[count($full_name) - 1]);
			$last_name = implode(" ", $full_name);
			$data['last_name'] = !empty($data['last_name'])?$data['last_name']:$last_name;

		}
		// if($data['link_shorten'] != ''){
		// 	$data['link'] = $ims->conf['rooturl'].$data['link_shorten'].'&type=link';
		// 	$data['link_mail'] = $ims->conf['rooturl'].$data['link_shorten'].'&type=mail';
		// }
		// $data['link'] = $ims->conf['rooturl'].'?contributor='.$ims->func->base64_encode($data['user_code']);
		// $data['link'] = $data['user_code'];
		$data['err'] = $err;
		$data['link_action'] = $link_action;
		$data['link_change_pass'] = $ims->site_func->get_link($this->modules, $ims->setting[$this->modules]["change_pass_link"]);

		$ims->temp_out->assign('data', $data);
		$ims->temp_out->parse("profile");
		return $ims->temp_out->text("profile");
	}
  // end class
}
?>