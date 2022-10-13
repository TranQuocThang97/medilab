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

function box_menu () {
	global $ims;
	
	$cur = (isset($ims->conf["cur_item"])) ? $ims->conf["cur_item"] : 0;
	
	if(!isset($ims->data["about"])){
		$query = "select item_id, title, friendly_link 
				from about 
				where is_show=1 
				and lang='".$ims->conf['lang_cur']."' 
				order by show_order desc, date_create asc";
		//echo $query;
		$result = $ims->db->query($query);
		$ims->data["about"] = array();
		if($num = $ims->db->num_rows($result)){
			while($row = $ims->db->fetch_row($result)){
				$ims->data["about"][$row["item_id"]] = $row;
			}
		}
	}
	
	$output = '';
	
	if(count($ims->data["about"]) > 0){
		$data = array(
			'title' => $ims->lang['about']['menu_title'],
			'content' => ''
		);
		
		$menu_sub = '';
		$i = 0;
		foreach($ims->data["about"] as $row)
		{
			$i++;
			$row['link'] = $ims->site_func->get_link('about','',$row['friendly_link']);
			$row['first'] = ($i == 1) ? ' class="first"' : '';
			$row['class'] = ($row['item_id'] == $cur) ? ' class="current"' : '';
			
			$ims->temp_box->assign('row', $row);
			$ims->temp_box->parse("box_menu.menu_sub.row");
			$menu_sub .= $ims->temp_box->text("box_menu.menu_sub.row");
			$ims->temp_box->reset("box_menu.menu_sub.row");
		}		
		
		$ims->temp_box->reset("box_menu.menu_sub");
		$ims->temp_box->assign('data', array('content' => $menu_sub));
		$ims->temp_box->parse("box_menu.menu_sub");
		
		$ims->temp_box->assign('data', $data);
		$ims->temp_box->parse("box_menu");
		$output = $ims->temp_box->text("box_menu");
	}
	
	return $output;
}
//=================box_column===============
function box_column ()
{
	global $ims;
	
	$output = $ims->site->block_left ();
	
	return $output;
}

?>