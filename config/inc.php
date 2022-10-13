<?php
    if (!defined('IN_ims')) { die('Access denied'); }


	spl_autoload_register(function ($classname) {
		if(strpos($classname, 'oogle_') < 1 && (strpos($classname,'Aws') === false && strpos($classname,'GuzzleHttp') === false && strpos($classname,'JmesPath') === false && strpos($classname,'Symfony') === false && strpos($classname,'Psr') === false && strpos($classname,'imagick') === false)){
			$classname = strtolower($classname);
			include $classname . '.php';
		}
	});

	require_once ("config.php"); 
	$ims->site = new Site();
	$ims->site->compressingJS ();
	$ims->site->compressingCSS ();

	require_once ("seo_url.php");

	$ims->load_data->data_banner_group();
	$ims->load_data->data_banner();

	// website đang ở chế độ xây dựng
	if($ims->conf['is_under_construction'] == 1) {
		if(Session::Get('is_admin') == 'admin' || (isset($ims->input['is_admin']))) {
			Session::Set('is_admin', 'admin');
		} else {
			require_once ($conf["rootpath"].'config'.DS."under_construction.php");
			exit();
		}
	}

	// require_once ($conf["rootpath"].DS."library".DS."tinymce".DS."tinymce.php");
	// $ims->editor = new Editor;

	$ims->func->load_language("global");

	if (!file_exists($ims->conf['rootpath'].'modules/'.$ims->conf['cur_mod'].'/controllers/seo_url.php')) {
		require_once $ims->conf['rootpath'].'modules/'.$ims->conf['cur_mod'].'/seo_url.php';
	}else{
		require_once $ims->conf['rootpath'].'modules/'.$ims->conf['cur_mod'].'/controllers/seo_url.php';
	}

	include_once("xtemplate.class.php");
	$ims->temp_box  = new XTemplate($ims->path_html."box.tpl");
	$ims->temp_html = new XTemplate($ims->path_html."html.tpl");
	$ims->conf['copyright'] = $ims->site->copyright ();
	$ims->detect = new Mobile_Detect;
?>
