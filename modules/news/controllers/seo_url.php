<?php
if (!defined('IN_ims')) { die('Access denied'); }

$ims->conf['cur_act'] = (isset($ims->conf['cur_act'])) ? $ims->conf['cur_act'] : "news";

function load_setting (){
	global $ims;
	
	$ims->site_func->setting ('news');
    $ims->load_data->data_group('news');
	return $ims->setting;
}
load_setting ();


if($ims->conf['cur_act'] == "group" && !empty($ims->conf['cur_act_id'])) {
	$result = $ims->db->query("SELECT * FROM news_group  WHERE lang='".$ims->conf['lang_cur']."' 
								AND is_show=1 AND group_id='".$ims->conf['cur_act_id']."' limit 0,1");
	if($row = $ims->db->fetch_row($result)){
		$row['content'] = $ims->func->input_editor_decode($row['content']);
		$ims->conf['cur_act'] = "news";
		$ims->conf['cur_group'] = $row['group_id'];
		$ims->data['cur_group'] = $row;
	}
}elseif($ims->conf['cur_act'] == "detail" && !empty($ims->conf['cur_act_id'])) {
	$result = $ims->db->query("SELECT * FROM news WHERE lang='".$ims->conf['lang_cur']."' AND is_show=1 
							AND item_id='".$ims->conf['cur_act_id']."' ".$ims->site_func->whereLoaded('news')." LIMIT 0,1");
	if($row = $ims->db->fetch_row($result)){
		$row['short'] = $ims->func->input_editor_decode($row['short']);
		$row['content'] = $ims->func->input_editor_decode($row['content']);
		$ims->conf['cur_act']   = "detail";
		$ims->conf['cur_item']  = $row['item_id'];
		$ims->conf['cur_group'] = $row['group_id'];
		$ims->conf["cur_group_nav"] = $row["group_nav"];
		$ims->data['cur_item']  = $row;
	}
}

/*print_arr($ims->conf);
die();*/
?>