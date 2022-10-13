<?php
	session_start();
	define('IN_ims', 1);
	class ims {}
	$ims = new ims;
	require_once ("dbcon.php");
	$ims->conf = $conf;
	require_once("config" . DIRECTORY_SEPARATOR . "thumbs.php");
	$thumbs = new Thumb;
	$thumbs->detach_src_mod($_GET);
	die();
?>