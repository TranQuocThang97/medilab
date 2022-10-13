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

$ims->conf['cur_act'] = (isset($ims->conf['cur_act'])) ? $ims->conf['cur_act'] : "about";

//=================list_skin===============
function load_setting ()
{
	global $ims;
	
	$ims->site_func->setting('about');
	
	return true;
}
load_setting ();

$result = $ims->db->load_row('about','lang="'.$ims->conf['lang_cur'].'"  and friendly_link="'.$ims->conf['cur_friendly_link'].'"');
if($result){
	$ims->conf['cur_item'] = $result['item_id'];
	$ims->data['cur_item'] = $result;
}else{
	$result = $ims->db->load_row('about','lang="'.$ims->conf['lang_cur'].'" order by show_order desc, date_create desc');
	if($result){	
		$ims->conf['cur_item'] = $result['item_id'];
		$ims->data['cur_item'] = $result;
	}
}
// print_arr($ims->conf);
// die();
?>