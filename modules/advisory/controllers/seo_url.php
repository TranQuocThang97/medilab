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

$ims->conf['cur_act'] = (isset($ims->conf['cur_act'])) ? $ims->conf['cur_act'] : "advisory";

function load_setting (){
    global $ims;

    $ims->site_func->setting ('advisory');
    $ims->load_data->data_group('advisory');
    return $ims->setting;
}
load_setting ();

$qr = ' is_show = 1 and lang = "'.$ims->conf['lang_cur'].'"';
if($ims->conf['cur_act'] == "advisory") {
    $row = $ims->db->load_row('advisory_group', $qr.' order by show_order desc, date_create desc');
    if($row){
        $ims->conf['cur_act'] = "advisory";
        $ims->conf['cur_group'] = $row['group_id'];
        $ims->data['cur_group'] = $row;
    }
}
if($ims->conf['cur_act'] == "group" && !empty($ims->conf['cur_act_id'])) {
    $row = $ims->db->load_row('advisory_group', $qr.' and group_id = '.$ims->conf['cur_act_id']);
	if($row){
		$ims->conf['cur_act'] = "advisory";
		$ims->conf['cur_group'] = $row['group_id'];
		$ims->data['cur_group'] = $row;
	}
}elseif($ims->conf['cur_act'] == "detail" && !empty($ims->conf['cur_act_id'])) {
    $row = $ims->db->load_row('advisory', $qr.' and item_id = '.$ims->conf['cur_act_id']);
	if($row){
		$ims->conf['cur_act'] = "advisory";
		$ims->conf['cur_item'] = $row['item_id'];
        $ims->data['cur_item'] = $row;
	}
}

?>