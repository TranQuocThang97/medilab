<?php
    if (!defined('IN_ims')) { die('Access denied'); }

	define('DIR_UPLOAD', $ims->conf["rooturl_web"].'uploads/');
	$ims->conf['minify'] = false;

	function print_arr($array=array()){
		echo "<div style=\"background:#ffffff; color:#000000\">";
		echo "<pre>";
		@print_r($array);
		echo "</pre>";
		echo "</div>";
	}

	$root_uri = str_replace((isset($_SERVER["HTTPS"])?'https':'http').'://' . $_SERVER['HTTP_HOST'], "", $conf['rooturl']);
	define('ROOT_URI', $root_uri);
	$ims->conf["rooturi"] = $root_uri;
	$ims->conf["rooturi_mod"] = $root_uri;

	$ims->resources      = $conf['rooturl']."resources/";
	$ims->resources_path = $conf['rootpath']."resources/";
	$ims->dir_js         = $conf['rooturl']."resources/js/";
	$ims->dir_js_path    = $conf['rootpath']."resources/js/";
	$ims->dir_lib        = $conf['rooturl']."library/";
	$ims->dir_lib_path   = $conf['rootpath']."library/";

	require_once ("config".DS."session.php"); 
	require_once ("config".DS."captcha.php"); 
	require_once ("config".DS."minify.php"); 
	require_once ("library".DS."tinymce".DS."tinymce.php"); 

	$ims->db 		  = new DB($conf);
	$ims->html 		  = new Html;
	$ims->func 		  = new Func;
	$ims->site_func   = new siteFunc;
	$ims->call 		  = new Call;
	$ims->load_data   = new Data;
    $ims->editor 	  = new Editor;

	$ims->dir_images  = $conf['rooturl']."resources/images/";
	$ims->dir_css     = $ims->func->dirModules("global", "assets", "css");
	$ims->path_html   = $ims->func->dirModules("global", "views", "path");

	// Login with facebook
	define('app_id_facebook', "406352317810423");
	define('app_secret_facebook', "ad38358f435ba13bbfdb8485fd3d4299");
	define('redirect_uri_facebook', urlencode($conf['rooturl'].'thong-tin-tai-khoan/'));

	// Login with google
	define('client_id_google', "771635951137-kov3nfuprbt9ah3i47kmsltpqhn6fe7v.apps.googleusercontent.com");
	define('client_secret_google', "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");
	define('redirect_uri_google', $conf['rooturl'].'doi-mat-khau/');

	// Login with zalo
	define('app_id_zalo', "1642521612167028844");
	define('app_secret_zalo', "70C3OSz7oGFSSLTxpfD3");
	define('redirect_uri_zalo', urlencode($conf['rooturl'].'thanh-vien/'));

?>