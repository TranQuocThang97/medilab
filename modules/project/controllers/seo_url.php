<?php
if (! defined('IN_ims')) {die('Access denied');}

$ims->conf['cur_act'] = (isset($ims->conf['cur_act'])) ? $ims->conf['cur_act'] : "project";

function load_setting ()
{
	global $ims;	
	
	$ims->site_func->setting ('project');
    $ims->load_data->data_group('project');
    return $ims->setting;
}
load_setting ();


if($ims->conf['cur_act'] == "group" && !empty($ims->conf['cur_act_id'])) {
	$result = $ims->db->load_row('project_group','lang="'.$ims->conf['lang_cur'].'" 
								and group_id="'.$ims->conf['cur_act_id'].'"');
	if($result){
		$result['content'] = $ims->func->input_editor_decode($result['content']);
		$ims->conf['cur_act'] = "project";
		$ims->conf['cur_group'] = $result['group_id'];
		$ims->data['cur_group'] = $result;
	}
}elseif($ims->conf['cur_act'] == "detail" && !empty($ims->conf['cur_act_id'])) {
	$result = $ims->db->load_row('project','lang="'.$ims->conf['lang_cur'].'" 
								and group_id="'.$ims->conf['cur_act_id'].'"');
	if($result){
		$row['content'] = $ims->func->input_editor_decode($row['content']);
		$ims->conf['cur_act'] = "project";
		$ims->conf['cur_item'] = $result['item_id'];
		$ims->data['cur_item'] = $result;
	}
}

// print_arr($ims->conf);
// die();
?>