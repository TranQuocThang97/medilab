<?php
	session_start();
	define('IN_ims', 1);
	define('PATH_ROOT', dirname(__FILE__));
	define('DS', DIRECTORY_SEPARATOR);
	$imsdebug_start=microtime();

	class ims{}
	$ims = new ims;
	require_once ("dbcon.php"); 
	$ims->conf = $conf;
	require_once ($ims->conf["rootpath"]."config/inc_ajax.php"); 

	$ims->output = '';

	// Main	 
	$ims->conf['cur_mod'] = (isset($ims->input['m'])) ? $ims->input['m'] : "home";
	$ims->conf['cur_act'] = (isset($ims->input['a'])) ? $ims->input['a'] : "ajax";

	$fileactname = "modules/".$ims->conf['cur_mod']."/controllers/".$ims->conf['cur_act'].".php";	
	if($ims->conf['cur_mod'] == "library"){
		$fileactname = "modules/".$ims->conf['cur_mod']."/".$ims->conf['cur_act'].".php";
	}
	if (file_exists($fileactname)) {
		require_once $fileactname;
	} else {
		flush();		
		die('Access denied');
		exit();
	}
	// end main
		
	exit();

	$ims->db->close();
?>
