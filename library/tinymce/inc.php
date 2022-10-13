<?php
	session_start();
	define('IN_ims', 1);
	define('PATH_ROOT', dirname(__FILE__));
	define('DS', DIRECTORY_SEPARATOR);
	$imsdebug_start=microtime();

	class ims{}
	$ims = new ims;
	require_once ("../../../../dbcon.php"); 
	$ims->conf = $conf;
	require_once ($ims->conf["rootpath"]."inc/admin_inc_popup.php"); 
?>