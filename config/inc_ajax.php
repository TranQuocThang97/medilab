<?php
    if (!defined('IN_ims')) { die('Access denied'); }
//&& (strpos($classname,'Aws') === false && strpos($classname,'GuzzleHttp') === false && strpos($classname,'JmesPath') === false && strpos($classname,'Symfony') === false && strpos($classname,'Psr') === false && strpos($classname,'imagick') === false)
    spl_autoload_register(function ($classname) {
		if(strpos($classname, 'oogle_') === false && strpos($classname, 'PHPExcel') === false){
			$classname = strtolower($classname);
			include $classname . '.php';
		}
	});

	require_once ("config.php"); 

	$ims->conf['lang_cur'] = isset($ims->data["lang_default"]['name']) ? $ims->data["lang_default"]['name'] : 'vi';
	if(isset($ims->input['lang_cur']) && array_key_exists($ims->input['lang_cur'],$ims->data["lang"])) {
		$ims->conf['lang_cur'] = $ims->input['lang_cur'];
	}
	$ims->conf['where_lang'] = " AND is_show=1 AND lang='".$ims->conf['lang_cur']."'";
	
	$ims->func->load_language("global");
	include_once("xtemplate.class.php");
	$ims->temp_box = new XTemplate($ims->path_html."box.tpl");
?>