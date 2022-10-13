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
class projectFunc {

    public $modules     = "project";
    public $parent      = null;
    public $parent_mod  = "project";
    public $parent_act  = "project";
    public $temp_act    = "";

    public function __construct($parent = null) {
        global $ims;
        $this->parent     = $parent;
        $this->parent_mod = $this->parent_property('modules');
        $this->parent_act = $this->parent_property('action');
        $this->temp_act   = $this->parent_property('temp_act');
        $ims->func->include_css($ims->func->dirModules($this->modules, 'assets')."css/func.css");
        $ims->call->mfunc_temp($this);

        return true;
    }

    //=================box_column===============
    public function box_column() {
        global $ims;

        $output = $ims->site->block_column();
        return $output;
    }

    public function parent_property($property) {
        global $ims;
        $output = false;
        if ($this->parent) {
            if (property_exists($this->parent, $property)) {
                $output = $this->parent->$property;
            }
        }
        return $output;
    }

    public function parent_method($method, $param_arr = array()) {
        global $ims;
        $output = false;
        if (method_exists($this->parent, $method)) {
            //$output = call_user_func(array($this->parent, $method));
            $output = call_user_func_array(array($this->parent, $method), $param_arr);
        }
        return $output;
    }

    // where_project
    function where_project($type = 'project') {
        global $ims;

        return $ims->site_func->whereLoaded($type);
    }

    // project_loaded
    function project_loaded($id = 0, $type = 'project') {
        global $ims;

        return array();
        return $ims->site_func->addLoaded($type, $id);
    }

    //-----------get_group_name
    function get_group_name($group_id, $type = 'none') {
        global $ims;

        $output = '';

        $sql = "select title,friendly_link    
					from project_group 
					where group_id='" . $group_id . "' 
					limit 0,1";
        //echo $sql;
        $result = $ims->db->query($sql);
        $html_row = "";
        if ($row = $ims->db->fetch_row($result)) {
            switch ($type) {
                case "link":
                    $link = $ims->site_func->get_link('project', $row['friendly_link']);
                    $output = '<a href="' . $link . '">' . $row['title'] . '</a>';
                    break;
                default:
                    $output = $row['title'];
                    break;
            }
        }

        return $output;
    }
	//-----------
	function html_list_group ($arr_in = array())
	{
		global $ims;
		
		$output = '';
		
		$link_action = (isset($arr_in['link_action'])) ? $arr_in['link_action'] : $ims->site->get_link ('project');
		$temp = (isset($arr_in['temp'])) ? $arr_in['temp'] : 'list_item';
		$p = (isset($ims->input["p"])) ? $ims->input["p"] : 1;
		$pic_w = 164;//(isset($ims->setting['project']["img_list_w"])) ? $ims->setting['project']["img_list_w"] : 100;
		$pic_h = 164;//(isset($ims->setting['project']["img_list_h"])) ? $ims->setting['project']["img_list_h"] : 100;
		$num_row = 1;
		
		$ext = '';
		$where = (isset($arr_in['where'])) ? $arr_in['where'] : '';
		
		$num_total = 0;
		$res_num = $ims->db->query("select group_id 
						from project_group 
						where is_show=1 
						and lang='".$ims->conf["lang_cur"]."' 
						".$where." ");
		$num_total = $ims->db->num_rows($res_num);
		if($num_total == 0) {
			return '';
		}
		
		$n = (isset($ims->setting['project']["num_list"])) ? $ims->setting['project']["num_list"] : 30;
		$num_items = ceil($num_total / $n);
		if ($p > $num_items)
			$p = $num_items;
		if ($p < 1)
			$p = 1;
		$start = ($p - 1) * $n;
		
		$where .= " order by show_order desc, date_update desc";
		
		$sql = "select group_id,picture,title,short,content,friendly_link,date_update  
						from project_group 
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
				$row['link'] = $ims->site->get_link ('project',$row['friendly_link']);
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
			$ims->temp_act->assign('row', array("mess"=>$ims->lang["project"]["no_have_item"]));
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
		
		$link_action = (isset($arr_in['link_action'])) ? $arr_in['link_action'] : $ims->site->get_link ('project');
		$temp = (isset($arr_in['temp'])) ? $arr_in['temp'] : 'list_item';
		$p = (isset($ims->input["p"])) ? $ims->input["p"] : 1;
		$pic_w = (isset($arr_in["pic_w"])) ? $arr_in["pic_w"] : 100;
		$pic_h = (isset($arr_in["pic_h"])) ? $arr_in["pic_h"] : 100;
		$num_row = (isset($arr_in["num_row"])) ? $arr_in["num_row"] : 2;
		$short_len = (isset($arr_in["short_len"])) ? $arr_in["short_len"] : 250;
		
		$ext = '';
		$where = (isset($arr_in['where'])) ? $arr_in['where'] : '';
		
		$num_total = 0;
		$res_num = $ims->db->query("select item_id 
						from project 
						where is_show=1 
						and lang='".$ims->conf["lang_cur"]."' 
						".$where." ");
		$num_total = $ims->db->num_rows($res_num);
		$n = (isset($ims->setting['project']["num_list"])) ? $ims->setting['project']["num_list"] : 30;
		$n = (isset($arr_in["num_show"])) ? $arr_in["num_show"] : $n;
		$num_items = ceil($num_total / $n);
		if ($p > $num_items)
			$p = $num_items;
		if ($p < 1)
			$p = 1;
		$start = ($p - 1) * $n;
		
		$where .= " order by show_order desc, date_update desc";
		
		$sql = "select item_id,group_id,picture,title,content,link,friendly_link,date_update  
						from project 
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
				$row['link_detail'] = $ims->site->get_link ('project','',$row['friendly_link']);
				$row['link_view'] = $row['link'];
				$row['link'] = $ims->func->fix_link ($row['link']);
				$row["picture"] = $ims->func->get_src_mod($row["picture"], $pic_w, $pic_h, 1, 1);
				$row['short'] = $ims->func->short ($row['content'], $short_len);
				$row['date_update'] = date('d/m/Y',$row['date_update']);
				
				$row['class'] = ($i%$num_row == 0 || $i == $num) ? ' last' : '';
				
				$row['link_share'] = $row['link'];
				
				$this->temp_func->assign('col', $row);
				$this->temp_func->parse($temp.".row_item.col_item");
				
				if($i%$num_row == 0 || $i == $num){
					$this->temp_func->assign('row', array('hr' => ($i < $num) ? '<div class="hr"></div>' : ''));
					$this->temp_func->parse($temp.".row_item");
				}
			}
		}else{			
			$this->temp_func->assign('row', array("mess"=>$ims->lang["project"]["no_have_item"]));
			$this->temp_func->parse($temp.".row_empty");
		}
		
		$data['html_row'] = $html_row;
		$data['nav'] = ($nav) ? '<div class="hr"></div>'.$nav : '';
		
		$data['link_action'] = $link_action."&p=".$p;
		
		$this->temp_func->assign('data', $data);
		$this->temp_func->parse($temp);
		return $this->temp_func->text($temp);
	}

	function list_other ($where='')
	{
		global $ims;	
		
		$output = '';
		
		$sql = "select item_id,title,friendly_link,date_update  
				from project 
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
				$row['link'] = $ims->site->get_link ('project','',$row['friendly_link']);
				$row['date_update'] = date('d/m/Y',$row['date_update']);
				
				$ims->temp_act->assign('row', $row);
				$ims->temp_act->parse("list_other.row");
			}
		
			$ims->temp_act->parse("list_other");
			return $ims->temp_act->text("list_other");
		}
	}

	function box_menu () {
		global $ims;
		
		$cur = (isset($ims->conf["cur_item"])) ? $ims->conf["cur_item"] : 0;
		
		if(!isset($ims->data["project"])){
			$query = "select item_id, title, friendly_link 
					from project 
					where is_show=1 
					and lang='".$ims->conf['lang_cur']."' 
					and group_id='".$ims->conf['cur_group']."' 
					order by show_order desc, date_create asc";
			//echo $query;
			$result = $ims->db->query($query);
			$ims->data["project"] = array();
			if($num = $ims->db->num_rows($result)){
				while($row = $ims->db->fetch_row($result)){
					$ims->data["project"][$row["item_id"]] = $row;
				}
			}
		}
		
		$output = '';
		
		if(count($ims->data["project"]) > 0){
			$data = array(
				'title' => $ims->lang['project']['menu_title'],
				'content' => ''
			);
			
			$menu_sub = '';
			$i = 0;
			foreach($ims->data["project"] as $row)
			{
				$i++;
				$row['link'] = $ims->site->get_link ('project','',$row['friendly_link']);
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
}
?>