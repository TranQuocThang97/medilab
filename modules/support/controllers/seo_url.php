<?php
if (!defined('IN_ims')) { die('Access denied'); }

$ims->conf['cur_act'] = (isset($ims->conf['cur_act'])) ? $ims->conf['cur_act'] : "support";

function load_setting (){
	global $ims;
	
	$ims->site_func->setting ('support');
    $ims->load_data->data_group('support');
	return $ims->setting;
}
load_setting ();


if($ims->conf['cur_act'] == "group" && !empty($ims->conf['cur_act_id'])) {
	$result = $ims->db->query("SELECT * FROM support_group  WHERE lang='".$ims->conf['lang_cur']."' 
								AND is_show=1 AND group_id='".$ims->conf['cur_act_id']."' limit 0,1");
	if($row = $ims->db->fetch_row($result)){
		$ims->conf['cur_act'] = "news";
		$ims->conf['cur_group'] = $row['group_id'];
		$ims->data['cur_group'] = $row;
	}
}elseif($ims->conf['cur_act'] == "detail" && !empty($ims->conf['cur_act_id'])) {
	$result = $ims->db->query("SELECT * FROM support WHERE lang='".$ims->conf['lang_cur']."' AND is_show=1 
							AND item_id='".$ims->conf['cur_act_id']."' ".$ims->site_func->whereLoaded('news')." LIMIT 0,1");
	if($row = $ims->db->fetch_row($result)){
		$ims->conf['cur_act']   = "detail";
		$ims->conf['cur_item']  = $row['item_id'];
		$ims->conf['cur_group'] = $row['group_id'];
		$ims->conf["cur_group_nav"] = $row["group_nav"];
		$ims->data['cur_item']  = $row;
	}
}

?>