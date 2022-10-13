<?php
if (!defined('IN_ims')) { die('Access denied'); }
$ims->conf['cur_act'] = (isset($ims->conf['cur_act']) && $ims->conf['cur_act']) ? $ims->conf['cur_act'] : "user";

function load_setting ()
{
	global $ims;

	$ims->site_func->setting("user");
	$ims->site_func->setting("product");
	$ims->site_func->setting("promotion");
  	$ims->conf['container_layout'] = 'm';
  	
	return true;
}
load_setting ();

// print_arr($ims->conf);
// die();
?>