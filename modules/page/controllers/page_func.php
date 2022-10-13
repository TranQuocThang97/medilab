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

//-----------
function html_list_group ($arr_in = array())
{
	global $ims;
	
	$output = '';
	
	$link_action = (isset($arr_in['link_action'])) ? $arr_in['link_action'] : $ims->site->get_link ('page');
	$temp = (isset($arr_in['temp'])) ? $arr_in['temp'] : 'list_item';
	$p = (isset($ims->input["p"])) ? $ims->input["p"] : 1;
	$pic_w = 164;//(isset($ims->setting['page']["img_list_w"])) ? $ims->setting['page']["img_list_w"] : 100;
	$pic_h = 164;//(isset($ims->setting['page']["img_list_h"])) ? $ims->setting['page']["img_list_h"] : 100;
	$num_row = 1;
	
	$ext = '';
	$where = (isset($arr_in['where'])) ? $arr_in['where'] : '';
	
	$num_total = 0;
	$res_num = $ims->db->query("select group_id 
					from page_group 
					where is_show=1 
					and lang='".$ims->conf["lang_cur"]."' 
					".$where." ");
	$num_total = $ims->db->num_rows($res_num);
	if($num_total == 0) {
		return '';
	}
	
	$n = (isset($ims->setting['page']["num_list"])) ? $ims->setting['page']["num_list"] : 30;
	$num_items = ceil($num_total / $n);
	if ($p > $num_items)
		$p = $num_items;
	if ($p < 1)
		$p = 1;
	$start = ($p - 1) * $n;
	
	$where .= " order by show_order desc, date_update desc";
	
	$sql = "select group_id,picture,title,short,content,friendly_link,date_update  
					from page_group 
					where is_show=1 
					and lang='".$ims->conf["lang_cur"]."' 
					".$where." 
					limit $start,$n";
	//echo $sql;
	
	$nav = $ims->site->paginate ($link_action, $num_total, $n, $ext, $p);
	
	$result = $ims->db->query($sql);
	$html_row = "";
	if ($num = $ims->db->num_rows($result))
	{
		$i = 0;
		while ($row = $ims->db->fetch_row($result)) 
		{
			$i++;
			$row['stt'] = $i;
			
			$row['pic_w'] = $pic_w;
			$row['pic_h'] = $pic_h;
			$row['link'] = $ims->site->get_link ('page',$row['friendly_link']);
			$row["picture"] = $ims->func->get_src_mod($row["picture"], $pic_w, $pic_h, 1, 0, array('fix_min' => 1));
			$row['short'] = $ims->func->short ($row['short'], 800);
			$row['date_update'] = date('d/m/Y',$row['date_update']);
			
			$row['class'] = ($i%$num_row == 0 || $i == $num) ? ' last' : '';
			
			$ims->temp_act->assign('col', $row);
			$ims->temp_act->parse($temp.".row_item.col_item");
			
			if($i%$num_row == 0 || $i == $num){
				$ims->temp_act->assign('row', array('hr' => ($i < $num) ? '<div class="hr"></div>' : ''));
				$ims->temp_act->parse($temp.".row_item");
			}
		}
	}
	else
	{
		$ims->temp_act->assign('row', array("mess"=>$ims->lang["page"]["no_have_item"]));
		$ims->temp_act->parse($temp.".row_empty");
	}
	
	$data['html_row'] = $html_row;
	$data['nav'] = $nav;
	
	$data['link_action'] = $link_action."&p=".$p;
	
	$ims->temp_act->assign('data', $data);
	$ims->temp_act->parse($temp);
	return $ims->temp_act->text($temp);
}

//-----------
function html_list_item ($arr_in = array())
{
	global $ims;
	
	$output = '';
	
	$link_action = (isset($arr_in['link_action'])) ? $arr_in['link_action'] : $ims->site->get_link ('page');
	$temp = (isset($arr_in['temp'])) ? $arr_in['temp'] : 'list_item';
	$p = (isset($ims->input["p"])) ? $ims->input["p"] : 1;
	$pic_w = (isset($arr_in["pic_w"])) ? $arr_in["pic_w"] : 220;
	$pic_h = (isset($arr_in["pic_h"])) ? $arr_in["pic_h"] : 150;
	$num_row = (isset($arr_in["num_row"])) ? $arr_in["num_row"] : 2;
	$short_len = (isset($arr_in["short_len"])) ? $arr_in["short_len"] : 250;
	
	$ext = '';
	$where = (isset($arr_in['where'])) ? $arr_in['where'] : '';
	
	$num_total = 0;
	$res_num = $ims->db->query("select item_id 
					from page 
					where is_show=1 
					and lang='".$ims->conf["lang_cur"]."' 
					".$where." ");
	$num_total = $ims->db->num_rows($res_num);
	$n = (isset($ims->setting['page']["num_list"])) ? $ims->setting['page']["num_list"] : 30;
	$n = (isset($arr_in["num_show"])) ? $arr_in["num_show"] : $n;
	$num_items = ceil($num_total / $n);
	if ($p > $num_items)
		$p = $num_items;
	if ($p < 1)
		$p = 1;
	$start = ($p - 1) * $n;
	
	$where .= " order by show_order desc, date_update desc";
	
	$sql = "select item_id,group_id,picture,title,content,friendly_link,date_update  
					from page 
					where is_show=1 
					and lang='".$ims->conf["lang_cur"]."' 
					".$where." 
					limit $start,$n";
	//echo $sql;
	
	$nav = $ims->site->paginate ($link_action, $num_total, $n, $ext, $p);
	
	$result = $ims->db->query($sql);
	$html_row = "";
	if ($num = $ims->db->num_rows($result))
	{
		$i = 0;
		while ($row = $ims->db->fetch_row($result)) 
		{
			$i++;
			$row['stt'] = $i;
			
			$row['pic_w'] = $pic_w;
			$row['pic_h'] = $pic_h;
			$row['link'] = $ims->site->get_link ('page','',$row['friendly_link']);
        	$row["picture"] = $ims->func->get_src_mod($row["picture"], $pic_w, $pic_h, 1, 0, array('fix_max' => '1'));
			$row['short'] = $ims->func->short ($row['content'], $short_len);
			$row['date_update'] = date('d/m/Y',$row['date_update']);
			
			$row['class'] = ($i%$num_row == 0 || $i == $num) ? ' last' : '';
			
			$row['link_share'] = $row['link'];
			
			$row['num_comment'] = 0;
			if($temp == 'list_item_detail') {
				$string = file_get_contents('http://graph.facebook.com/'.$row['link_share'], FILE_USE_INCLUDE_PATH);
				$facebook_info = json_decode($string);
				if(!isset($facebook_info->comments)) {
					$facebook_info->comments = 0;
				}
				$row['num_comment'] = $facebook_info->comments;
			}
			
			$ims->temp_act->assign('col', $row);
			$ims->temp_act->parse($temp.".row_item.col_item");
			
			if($i%$num_row == 0 || $i == $num){
				$ims->temp_act->assign('row', array('hr' => ($i < $num) ? '<div class="hr"></div>' : ''));
				$ims->temp_act->parse($temp.".row_item");
			}
		}
	}
	else
	{
		$ims->temp_act->assign('row', array("mess"=>$ims->lang["page"]["no_have_item"]));
		$ims->temp_act->parse($temp.".row_empty");
	}
	
	$data['html_row'] = $html_row;
	$data['nav'] = ($nav) ? '<div class="hr"></div>'.$nav : '';
	
	$data['link_action'] = $link_action."&p=".$p;
	
	$ims->temp_act->assign('data', $data);
	$ims->temp_act->parse($temp);
	return $ims->temp_act->text($temp);
}

function list_other ($where='')
{
	global $ims;	
	
	$output = '';
	
	$sql = "select item_id,title,friendly_link,date_update  
			from page 
			where is_show=1 
			and lang='".$ims->conf["lang_cur"]."' 
			".$where."
			order by show_order desc, date_update desc";
	//echo $sql;
	
	$result = $ims->db->query($sql);
	$html_row = '';
	if ($num = $ims->db->num_rows($result))
	{
		$i = 0;
		while ($row = $ims->db->fetch_row($result)) 
		{
			$i++;
			$row['link'] = $ims->site->get_link ('page','',$row['friendly_link']);
			$row['date_update'] = date('d/m/Y',$row['date_update']);
			
			$ims->temp_act->assign('row', $row);
			$ims->temp_act->parse("list_other.row");
		}
	
		$ims->temp_act->parse("list_other");
		return $ims->temp_act->text("list_other");
	}
}

//=================box_column===============
function box_left ()
{
	global $ims;
	
	$output = $ims->site->block_left ();
	
	return $output;
}

//=================box_column===============
function box_column ()
{
	global $ims;
	
	$output = $ims->site->block_column ();
	
	return $output;
}

?>