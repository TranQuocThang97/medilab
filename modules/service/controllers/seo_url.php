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

$ims->conf['cur_act'] = (isset($ims->conf['cur_act'])) ? $ims->conf['cur_act'] : "service";

function load_setting ()
{
	global $ims;
	
	$ims->setting = (isset($ims->setting)) ? $ims->setting : array();
	if(!isset($ims->setting['service'])){
		$ims->setting['service'] = array();
		$result = $ims->db->query("select * from service_setting ");
		while($row = $ims->db->fetch_row($result)){
			$ims->setting['service_'.$row['lang']] = $row;
			if($ims->conf['lang_cur'] == $row['lang']) {
				$ims->setting['service'] = $row;
			}
		}
	}
	
	$ims->data['service_group'] = (isset($ims->data['service_group'])) ? $ims->data['service_group'] : array();
	$query = "select group_id, title, friendly_link 
				from service_group 
				where is_show=1 
				and lang='".$ims->conf['lang_cur']."' 
				order by show_order desc, date_create asc";
	//echo '<br />query='.$query;
	$result = $ims->db->query($query);
	if($num = $ims->db->num_rows($result)){
		while($row = $ims->db->fetch_row($result)){
			$ims->data['service_group'][$row['group_id']] = $row;
		}
	}
	
	return true;
}
load_setting ();


if($ims->conf['cur_act'] == "group" && !empty($ims->conf['cur_act_id'])) {
	$result = $ims->db->query("select *  
								from service_group 
								where lang='".$ims->conf['lang_cur']."' 
								and group_id='".$ims->conf['cur_act_id']."' 
								limit 0,1");
	if($row = $ims->db->fetch_row($result)){
		$row['content'] = $ims->func->input_editor_decode($row['content']);
		$ims->conf['cur_act'] = "service";
		$ims->conf['cur_group'] = $row['group_id'];
		$ims->data['cur_group'] = $row;
	}
}elseif($ims->conf['cur_act'] == "detail" && !empty($ims->conf['cur_act_id'])) {
	$result = $ims->db->query("select *  
							from service 
							where lang='".$ims->conf['lang_cur']."' 
							and item_id='".$ims->conf['cur_act_id']."' 
							limit 0,1");
	if($row = $ims->db->fetch_row($result)){
		$row['content'] = $ims->func->input_editor_decode($row['content']);
		$ims->conf['cur_act'] = "service";
		$ims->conf['cur_item'] = $row['item_id'];
		$ims->data['cur_item'] = $row;
	}
}

/*print_arr($ims->conf);
die();*/
?>