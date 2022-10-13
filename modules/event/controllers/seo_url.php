<?php
if (!defined('IN_ims')) { die('Access denied'); }

$ims->conf['cur_act'] = (isset($ims->conf['cur_act'])) ? $ims->conf['cur_act'] : "event";

function load_setting ()
{
	global $ims;
	
	$ims->site_func->setting("event");
	// $ims->site_func->setting("user");
	// $ims->site_func->setting("promotion");
	$ims->load_data->data_group ('event');
	
	return true;
}
load_setting ();

if($ims->conf['cur_act'] == "group" && !empty($ims->conf['cur_act_id'])) {
	$result = $ims->db->load_row("event_group" , " group_id='".$ims->conf['cur_act_id']."' and is_show = 1 and lang='".$ims->conf['lang_cur']."'");
	if (!empty($result)) {
		$result['content'] 		= $ims->func->input_editor_decode($result['content']);
		$ims->conf['cur_act']   = "event";
		$ims->conf['cur_group'] = $result['group_id'];
		$ims->data['cur_group'] = $result;
	}
}elseif($ims->conf['cur_act'] == "detail" && !empty($ims->conf['cur_act_id'])) {
	$result = $ims->db->load_row("event" , " item_id='".$ims->conf['cur_act_id']."' and is_show = 1 and lang='".$ims->conf['lang_cur']."'");
	if (!empty($result)) {
		$ims->conf['cur_act']  = "detail";
		$ims->conf['cur_item'] = $result['item_id'];
		$ims->data['cur_item'] = $result;
	}
}
?>