<?php
if (! defined('IN_ims')) {
  die('Access denied');
}
$ims->conf['cur_act'] = "home";
function load_setting (){
	global $ims;
	$ims->site_func->setting('home');
	return true;
}
load_setting ();
?>