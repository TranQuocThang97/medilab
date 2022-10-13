<?php
if (!defined('IN_ims')) { die('Access denied'); }

$ims->conf['cur_act'] = (isset($ims->conf['cur_act'])) ? $ims->conf['cur_act'] : "product";

function load_setting ()
{
	global $ims;
	
	$ims->site_func->setting("product");
	// $ims->site_func->setting("user");
	// $ims->site_func->setting("promotion");
	$ims->load_data->data_group ('product');
	
	return true;
}
load_setting ();

if($ims->conf['cur_act'] == "promotion"){
	if($ims->conf['cur_act'] == "promotion" && !empty($ims->conf['cur_act_id'])) {
		$result = $ims->db->load_row("product_promotion" , " item_id='".$ims->conf['cur_act_id']."' and is_show = 1 and lang='".$ims->conf['lang_cur']."'");
		if (!empty($result)) {
			$result['content'] 	   = $ims->func->input_editor_decode($result['content']);
			$ims->conf['cur_act']  = "promotion";
			$ims->conf['cur_item'] = $result['item_id'];
			$ims->data['cur_item'] = $result;
		}
	}
}
if($ims->conf['cur_act'] == "group" && !empty($ims->conf['cur_act_id'])) {
	$result = $ims->db->load_row("product_group" , " group_id='".$ims->conf['cur_act_id']."' and is_show = 1 and lang='".$ims->conf['lang_cur']."'");
	if (!empty($result)) {
		$result['content'] 		= $ims->func->input_editor_decode($result['content']);
		$ims->conf['cur_act']   = "product";
		$ims->conf['cur_group'] = $result['group_id'];
		$ims->data['cur_group'] = $result;
	}
}elseif($ims->conf['cur_act'] == "detail" && !empty($ims->conf['cur_act_id'])) {
	$result = $ims->db->load_row("product" , " item_id='".$ims->conf['cur_act_id']."' and is_show = 1 and lang='".$ims->conf['lang_cur']."'");
	if (!empty($result)) {
//		$product_detail = $ims->db->load_row('product_detail', " product_id='".$result['item_id']."' ");
//		$result = array_merge($result, $product_detail);
//		$result['short'] 	   = $ims->func->input_editor_decode($result['short']);
//		$result['content']     = $ims->func->input_editor_decode($result['content']);
		$ims->conf['cur_act']  = "detail";
		$ims->conf['cur_item'] = $result['item_id'];
		$ims->data['cur_item'] = $result;
	}
}elseif (in_array($ims->conf['cur_act'], array('link_header1', 'link_header2', 'link_header3', 'link_header4'))){
    $action = explode('_', $ims->conf['cur_act']);
    $ims->conf['cur_act'] = $action[1];
    $ims->setting['product']['header_meta_title'] = $ims->setting['product']['text_'.$ims->conf['cur_act']];
    $ims->setting['product']['header_meta_key'] = $ims->setting['product']['text_'.$ims->conf['cur_act']];
    $ims->setting['product']['header_meta_desc'] = $ims->setting['product']['text_'.$ims->conf['cur_act']];
    $ims->conf['cur_act_distinct'] = $ims->conf['cur_act'];
    $ims->conf['cur_act']  = "header";
}
// print_arr($ims->conf);
// die;
?>