<?php

/*================================================================================*\
Name code : function.php
Copyright © 2013 by Tran Thanh Hiep
@version : 1.0
@date upgrade : 03/02/2013 by Tran Thanh Hiep
\*================================================================================*/

if (! defined('IN_ims')) {
  die('Hacking attempt!');
}

//=================list_skin===============
function load_setting_ordering (){
	global $ims;
	if(!isset($ims->setting_voucher)){
		$ims->setting_voucher = array();
		$result = $ims->db->query("select * from voucher_setting where lang='".$ims->conf['lang_cur']."' ");
		if($row = $ims->db->fetch_row($result)){
			$ims->setting_voucher = $row;
		}
	}
	$ims->setting_ordering = array();	
	// $ims->setting_ordering['status_order'] = array(
	// 	0 => array(
	// 		'title' => $ims->lang['user']['status_order_0'],
	// 		'color_title' => '#815621',
	// 		'color_bg' => '#f4c58f',
	// 		'color_border' => '#e6b77f',
	// 	),
	// 	1 => array(
	// 		'title' => $ims->lang['user']['status_order_1'],
	// 		'color_title' => '#4b6319',
	// 		'color_bg' => '#e5f2ce',
	// 		'color_border' => '#cddeb5',
	// 	),		
	// );
	$ims->setting_ordering['status_order'] = $ims->load_data->data_table(
	    'product_order_status', 
	    'item_id', '*', 
	    "lang='".$ims->conf['lang_cur']."' ORDER BY show_order DESC, date_create DESC", array()
	);
	$arr_delivery_status = $ims->load_data->data_table('product_order_deliverystatus','item_id','item_id,title,color_title,color_bg,color_border','lang="'.$ims->conf['lang_cur'].'" and is_show=1');	
	$ims->setting_ordering['status_delivery'] = array(
		0 => array(
			'item_id' => 0,
			'title' => $ims->lang['user']['status_delivery0'],
		    'color_title' => '#343434',
		    'color_bg' => '#fff',
		    'color_border' => '#000',
		)
	);
	$ims->setting_ordering['status_delivery'] += $arr_delivery_status;	
	return false;
}
load_setting_ordering ();

function status_order_name ($status=0) {
	global $ims;
	
	$output = (isset($ims->setting_ordering['status_order'][$status]['title'])) ? $ims->setting_ordering['status_order'][$status]['title'] : '';
	return $output;
}

function status_order_info ($status=0) {
	global $ims;	
	$output = (isset($ims->setting_ordering['status_order'][$status])) ? $ims->setting_ordering['status_order'][$status] : array();
	return $output;
}

function status_delivery_info ($status=0) {
	global $ims;	
	$output = (isset($ims->setting_ordering['status_delivery'][$status])) ? $ims->setting_ordering['status_delivery'][$status] : array();
	return $output;
}

function list_status_order ($select_name="is_status", $cur="", $ext="",$arr_more=array()) {
	global $ims;
	
	$arr_data = $ims->setting_ordering['status_order'];
	
	return $ims->html->select ($select_name, $arr_data, $cur, $ext,$arr_more);
}

function get_data_product_group () {
	global $ims;
	
	if(!isset($ims->data["product_group"])){
		$query = "select group_id, group_nav, parent_id, title, friendly_link 
							from product_group 
							where is_show=1 
							and lang='".$ims->conf["lang_cur"]."' 
							order by group_level asc, show_order desc, group_id asc";
		//echo $query;
		$result = $ims->db->query($query);
		$ims->data["product_group"] = array();
		$ims->data["product_group_tree"] = array();
		if($num = $ims->db->num_rows($result)){
			while($row = $ims->db->fetch_row($result)){
				$ims->data["product_group"][$row["group_id"]] = $row;
				
				$arr_group_nav = explode(',',$row['group_nav']);
				$str_code = '';
				$f = 0;
				foreach($arr_group_nav as $tmp){
					$f++;
					$str_code .= ($f == 1) ? '['.$tmp.']' : '["arr_sub"]['.$tmp.']';
				}
				eval('$ims->data["product_group_tree"]'.$str_code.'["group_id"] = $row["group_id"];
				$ims->data["product_group_tree"]'.$str_code.'["title"] = $row["title"];
				$ims->data["product_group_tree"]'.$str_code.'["friendly_link"] = $row["friendly_link"];');
			}
		}
	}
	
	return $ims->data["product_group"];
}

function list_product_group ($select_name="group_id", $cur="", $ext="",$arr_more=array()) {
	global $ims;
	get_data_product_group ();
	
	return $ims->html->select ($select_name, $ims->data["product_group_tree"], $cur, $ext,$arr_more);
}

function get_product_group_name ($group_id, $link='') {
	global $ims;
	$output = '';
		
	get_data_product_group ();
	
	if (isset($ims->data["product_group"][$group_id])) {
		$row = $ims->data["product_group"][$group_id];
		if(!empty($link)) {
			$output = '<a href="'.$link.'">'.$row['title'].'</a>';
		} else {
			$output = $row['title'];
		}
	}
	return $output;
}

?>