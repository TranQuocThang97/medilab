<?php

if (! defined('IN_ims')) {
  die('Access denied');
}

$ims->conf['cur_act'] = (isset($ims->conf['cur_act'])) ? $ims->conf['cur_act'] : "page";
function load_setting (){
    global $ims;

    $ims->site_func->setting ('page');
    $ims->load_data->data_group('page');
    return $ims->setting;
}
load_setting ();

$qr = 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" ';
if($ims->conf['cur_act'] == "group" && !empty($ims->conf['cur_act_id'])) {
    $row = $ims->db->load_row('page_group', $qr.' and group_id = '.$ims->conf['cur_act_id']);
	if($row){
		$ims->conf['cur_act'] = "page";
		$ims->conf['cur_group'] = $row['group_id'];
		$ims->data['cur_group'] = $row;
	}
}elseif($ims->conf['cur_act'] == "detail" && !empty($ims->conf['cur_act_id'])) {
    $row = $ims->db->load_row('page', $qr.' and item_id = '.$ims->conf['cur_act_id']);
	if($row){
		$ims->conf['cur_act'] = "page";
		$ims->conf['cur_item'] = $row['item_id'];
		$ims->data['cur_item'] = $row;
	}
}

?>