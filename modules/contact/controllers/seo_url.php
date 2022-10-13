<?php

/*================================================================================*\
Name code : class_functions.php
Copyright © 2013 by Tran Thanh Hiep
@version : 1.0
@date upgrade : 03/02/2013 by Tran Thanh Hiep
\*================================================================================*/

if (! defined('IN_ims')) {
  die('Access denied');
}

$ims->conf['cur_act'] = "contact";

function load_setting ()
{
	global $ims;
	
    $ims->site_func->setting('contact', array('editor' => 'contact_info,contact_form'));
	
	return true;
}
load_setting ();

/*print_arr($ims->conf);
die();*/
?>