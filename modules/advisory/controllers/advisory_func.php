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
define('DIR_MOD_UPLOAD', $ims->conf['rooturl'].'uploads/advisory/');

//-----------
function html_list_item ($arr_in = array())
{
    global $ims;

    $output = '';

    $link_action = (isset($arr_in['link_action'])) ? $arr_in['link_action'] : $ims->site_func->get_link('advisory');
    $temp = (isset($arr_in['temp'])) ? $arr_in['temp'] : 'group_advisory';
    $p = (isset($ims->input["p"])) ? $ims->input["p"] : 1;
    $pic_w = (isset($arr_in["pic_w"])) ? $arr_in["pic_w"] : 200;
    $pic_h = (isset($arr_in["pic_h"])) ? $arr_in["pic_h"] : 150;
    $num_row = (isset($arr_in["num_row"])) ? $arr_in["num_row"] : 2;
    $short_len = (isset($arr_in["short_len"])) ? $arr_in["short_len"] : 550;

    $ext = '';
    $where = (isset($arr_in['where'])) ? $arr_in['where'] : '';

    $num_total = 0;
    $res_num = $ims->db->query("select item_id
					from advisory
					where is_show=1
					and lang='".$ims->conf["lang_cur"]."'
					".$where." ");
    $num_total = $ims->db->num_rows($res_num);
    $n = (isset($ims->setting['advisory']["num_list"])) ? $ims->setting['advisory']["num_list"] : 30;
    $n = (isset($arr_in["num_show"])) ? $arr_in["num_show"] : $n;
    $num_items = ceil($num_total / $n);
    if ($p > $num_items)
        $p = $num_items;
    if ($p < 1)
        $p = 1;
    $start = ($p - 1) * $n;

    $where .= " order by show_order desc, date_update desc";

    $sql = "select *
					from advisory
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
            $row['title'] = strip_tags($ims->func->input_editor_decode($row['title']));        
            $row['link'] = $ims->site_func->get_link('advisory','',$row['friendly_link']);
            $row['link_root'] = $ims->conf['rooturl'];

			/*
			$row['pic_w'] = $pic_w;
            $row['pic_h'] = $pic_h;
            $row["picture"] = $ims->func->get_src_mod($row["picture"], $pic_w, $pic_h, 1, 1);
            */
            $row['stt'] = $i;
			$row['short'] = $ims->func->short ($row['content'], $short_len);
            $row['content'] = strip_tags($ims->func->input_editor_decode($row['content']));
            $row['date_update'] = date('d/m/Y',$row['date_update']);
            $row['class'] = ($i%$num_row == 0 || $i == $num) ? ' last' : '';
            $row['num'] = $i;
            $row['link_share'] = $row['link'];
            $row['num_comment'] = 0;
			
			$row['owner_email'] = str_replace(substr($row['owner_email'],0,3),'***',$row['owner_email']);
			
			/*
            if($temp == 'list_item_detail') {
                $string = file_get_contents('http://graph.facebook.com/'.$row['link_share'], FILE_USE_INCLUDE_PATH);
                $facebook_info = json_decode($string);
                if(!isset($facebook_info->comments)) {
                    $facebook_info->comments = 0;
                }
                $row['num_comment'] = $facebook_info->comments;
            }
			*/

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
        $ims->temp_act->assign('row', array("mess"=>$ims->lang["advisory"]["no_have_item"]));
        $ims->temp_act->parse($temp.".row_empty");
    }   
    $data['nav'] = $nav;
    $data['link_action'] = $link_action."&p=".$p;
    $ims->temp_act->assign('data', $data);
    $ims->temp_act->parse($temp);
    return $ims->temp_act->text($temp);
}

function box_right(){
   global $ims;

   $output = $ims->site->block_left();

   return $output;
}
//=================get_navigation===============
function get_navigation ()
{
    global $ims;
    $arr_nav = array(
       array(
           'title' => $ims->lang['global']['homepage'],
           'link' => $ims->site_func->get_link('home')
       ),
        array(
            'title' => $ims->setting['advisory']['advisory_meta_title'],
            'link' => $ims->site_func->get_link('advisory')
        )
    );
    $arr_group = (isset($ims->conf['cur_group']) && $ims->conf['cur_group'] > 0 && isset($ims->conf["cur_group_nav"])) ? explode(',',$ims->conf["cur_group_nav"]) : array();

    foreach($arr_group as $group_id) {
        if(isset($ims->data["advisory_group"][$group_id])) {
            $arr_nav[] = array(
                'title' => $ims->data["advisory_group"][$group_id]['title'],
                'link' => $ims->site_func->get_link('advisory', $ims->data["advisory_group"][$group_id]['friendly_link'])
            );
        }
    }
    if(isset($ims->conf['cur_item']) && $ims->conf['cur_item'] > 0) {
        $arr_nav[] = array(
            'title' => $ims->data["cur_item"]['title'],
            //'link' => $ims->site_func->get_link('advisory', '', $ims->data["advisory_group"][$group_id]['friendly_link'])
        );
    }
    return $ims->site->html_arr_navigation($arr_nav);
}

//=================select===============
function box_menu_sub ($array=array())
{
	global $ims;

	$output = '';
	$arr_cur = ($ims->conf['cur_group'] > 0 && isset($ims->conf["cur_group_nav"])) ? explode(',',$ims->conf["cur_group_nav"]) : array();

	$menu_sub = '';
	foreach($array as $row)
	{
		$row['link'] = $ims->site_func->get_link('advisory',$row['friendly_link']);
		$row['class'] = (in_array($row["group_id"],$arr_cur)) ? ' class="current"' : '';
		$row['menu_sub'] = '';
		if(isset($row['arr_sub'])){
			$row['menu_sub'] = box_menu_sub ($row['arr_sub']);
		}
		$ims->temp_box->assign('row', $row);
		$ims->temp_box->parse("box_menu.menu_sub.row");
		$menu_sub .= $ims->temp_box->text("box_menu.menu_sub.row");
		$ims->temp_box->reset("box_menu.menu_sub.row");
	}

	$ims->temp_box->reset("box_menu.menu_sub");
	$ims->temp_box->assign('data', array('content' => $menu_sub));
	$ims->temp_box->parse("box_menu.menu_sub");
	return $ims->temp_box->text("box_menu.menu_sub");
}
function box_menu () {
	global $ims;

	$arr_cur = ($ims->conf['cur_group'] > 0 && isset($ims->conf["cur_group_nav"])) ? explode(',',$ims->conf["cur_group_nav"]) : array();

	if(!isset($ims->data["advisory_group"])){
		$query = "select group_id, group_nav, parent_id, title, friendly_link
							from advisory_group
							where is_show=1
							and lang='".$ims->conf["lang_cur"]."'
							order by group_level asc, show_order desc, date_update desc";
		//echo $query;
		$result = $ims->db->query($query);
		$ims->data["advisory_group"] = array();
		$ims->data["advisory_group_tree"] = array();
		if($num = $ims->db->num_rows($result)){
			while($row = $ims->db->fetch_row($result)){
				$ims->data["advisory_group"][$row["group_id"]] = $row;

				$arr_group_nav = explode(',',$row['group_nav']);
				$str_code = '';
				$f = 0;
				foreach($arr_group_nav as $tmp){
					$f++;
					$str_code .= ($f == 1) ? '['.$tmp.']' : '["arr_sub"]['.$tmp.']';
				}
				eval('$ims->data["advisory_group_tree"]'.$str_code.'["group_id"] = $row["group_id"];
				$ims->data["advisory_group_tree"]'.$str_code.'["title"] = $row["title"];
				$ims->data["advisory_group_tree"]'.$str_code.'["friendly_link"] = $row["friendly_link"];');
			}
		}
	}

	$output = '';

	if(count($ims->data["advisory_group_tree"]) > 0){
		$data = array(
			'title' => $ims->lang['advisory']['menu_title'],
			'content' => ''
		);

		$menu_sub = '';
		foreach($ims->data["advisory_group_tree"] as $row)
		{
			$row['link'] = $ims->site_func->get_link('advisory',$row['friendly_link']);
			$row['class'] = (in_array($row["group_id"],$arr_cur)) ? ' class="current"' : '';
			$row['menu_sub'] = '';
			if(isset($row['arr_sub'])){
				$row['menu_sub'] = box_menu_sub ($row['arr_sub']);
			}
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
	
	$output = $ims->site->block_column ();
	
	return $output;
}
?>