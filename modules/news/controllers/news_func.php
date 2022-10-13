<?php
if (!defined('IN_ims')) { die('Access denied'); }

class newsFunc {

    public $modules     = "news";
    public $parent      = null;
    public $parent_mod  = "news";
    public $parent_act  = "news";
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

    // where_news
    function where_news($type = 'news') {
        global $ims;

        return $ims->site_func->whereLoaded($type);
    }

    // news_loaded
    function news_loaded($id = 0, $type = 'news') {
        global $ims;

        return array();
        return $ims->site_func->addLoaded($type, $id);
    }

    //-----------get_group_name
    function get_group_name($group_id, $type = 'none') {
        global $ims;

        $output = '';

        $sql = "select title,friendly_link    
					from news_group 
					where group_id='" . $group_id . "' 
					limit 0,1";
        //echo $sql;
        $result = $ims->db->query($sql);
        $html_row = "";
        if ($row = $ims->db->fetch_row($result)) {
            switch ($type) {
                case "link":
                    $link = $ims->site_func->get_link('news', $row['friendly_link']);
                    $output = '<a href="' . $link . '">' . $row['title'] . '</a>';
                    break;
                default:
                    $output = $row['title'];
                    break;
            }
        }

        return $output;
    }

    /** mod_item
     * @global type $ims
     * @param type $group_id
     * @param type $type
     * @return type
     */
    function mod_item($row, $temp = 'mod_item') {
        global $ims;

        // $ims->call->mfunc_temp($this);

        $row['link'] = $ims->func->get_link($row['friendly_link'], '');
        $row['picture'] = $ims->func->get_src_mod($row["picture"], $row['pic_w'], $row['pic_h'], 1, 1);
        $row['short'] = ($row['short'] != '') ? $row['short'] : $row['content'];
        $row['short'] = $ims->func->short($row['short'], 202);
        $row['group_name'] = $this->get_group_name($row['group_id'], 'link');
        // $row['group_name'] = $this->get_group_name($row['group_id'] , 'link');
         $row['date_create'] = date('d/m/Y', $row['date_create']);
//        $row['date_update'] = date('d', $row['date_create']);
//        if($ims->conf['lang_cur'] == "vi"){
//            $row['my_update'] = $ims->func->rebuild_date('F, Y', $row['date_create']);
//        }else{
//            $row['my_update'] = date('F, Y', $row['date_create']);
//        }
        // $row['day_update'] = date('d', $row['date_update']);
        // $row['month_update'] = date('m', $row['date_update']);
        //The item loaded and no load again
        $this->news_loaded($row['item_id'], $this->modules);

        $this->temp_func->reset($temp);
        $this->temp_func->assign('row', $row);
        $this->temp_func->assign('CONF', $ims->conf);
        $this->temp_func->parse($temp);
        return $this->temp_func->text($temp);
    }

    //-----------
    function html_list_item($arr_in = array(), $type = '') {
        global $ims;

        // $ims->call->mfunc_temp($this);

        $output = '';

        $link_action = (isset($arr_in['link_action'])) ? $arr_in['link_action'] : $ims->site_func->get_link('news');
        $temp = (isset($arr_in['temp'])) ? $arr_in['temp'] : 'list_item';
        $temp_mod = (isset($arr_in['temp_mod'])) ? $arr_in['temp_mod'] : 'mod_item';
        $paginate = (isset($arr_in['paginate'])) ? $arr_in['paginate'] : 1;
        $p = (isset($ims->input["p"])) ? $ims->input["p"] : 1;
        $n = (isset($ims->setting['news']["num_list"])) ? $ims->setting['news']["num_list"] : 30;
        $n = (isset($arr_in['num_list'])) ? $arr_in['num_list'] : $n;
        $pic_w = (isset($arr_in['pic_w'])) ? $arr_in['pic_w'] : 341;
        $pic_h = (isset($arr_in['pic_h'])) ? $arr_in['pic_h'] : 191;

        $ext = (isset($arr_in['ext'])) ? $arr_in['ext'] : '';
        $where = (isset($arr_in['where'])) ? $arr_in['where'] : '';
        $where .= $this->where_news();

        $nav = '';
        $num_total = 0;
        $start = 0;
        if ($paginate == 1) {
            $num_total = $ims->db->do_get_num("news", "1 ". $ims->conf['where_lang'] . $where);
            $num_items = ceil($num_total / $n);
            if ($p > $num_items)
                $p = $num_items;
            if ($p < 1)
                $p = 1;
            $start = ($p - 1) * $n;

            $nav = $ims->site->paginate($link_action, $num_total, $n, $ext, $p);
        }
        if($where == ''){
            $where .= " order by show_order desc, date_create desc";
        }

        $result = $ims->db->load_item_arr('news', $ims->conf['qr'].$where.' limit '.$start.','.$n, 'item_id, title, picture, friendly_link, short, content, date_create, group_id');
        if ($result) {
            $i = 0;
            foreach ($result as $row) {
                $i++;
                $row['stt'] = $i;
                $row['pic_w'] = $pic_w;
                $row['pic_h'] = $pic_h;
                if(isset($type) && $type == 'first_big' && $i == 1){
                    $row['pic_w'] = 710;
                    $row['pic_h'] = 355;
                    $row['link'] = $ims->site_func->get_link($this->modules, '', $row['friendly_link']);
                    $row['picture'] = $ims->func->get_src_mod($row["picture"], $row['pic_w'], $row['pic_h'], 1, 1);

                    $row['short'] = $ims->func->short($row['short'], 300);
                    $this->temp_func->assign('row', $row);
                    $this->temp_func->parse($temp . ".row_item_first");
                }
                else{
                    $row['mod_item'] = $this->mod_item($row,$temp_mod);
                }
                $this->temp_func->assign('row', $row);
                $this->temp_func->parse($temp . ".row_item");
            }
        } else {
            $this->temp_func->assign('row', array("mess" => $ims->lang["news"]["no_have_item"]));
            $this->temp_func->parse($temp . ".row_empty");
        }

        $data['nav'] = $nav;

        $data['link_action'] = $link_action . "&p=" . $p;

        $this->temp_func->assign('data', $data);
        $this->temp_func->parse($temp);
        return $this->temp_func->text($temp);
    }

//=================select===============
    function box_menu_sub($array = array()) {
        global $ims;

        $output = '';
        $arr_cur = ($ims->conf['cur_group'] > 0 && isset($ims->conf["cur_group_nav"])) ? explode(',', $ims->conf["cur_group_nav"]) : array();

        $menu_sub = '';
        foreach ($array as $row) {
            $row['link'] = $ims->site_func->get_link('news', $row['friendly_link']);
            $row['class'] = (in_array($row["group_id"], $arr_cur)) ? ' class="current"' : '';
            $row['menu_sub'] = '';
            if (isset($row['arr_sub'])) {
                $row['menu_sub'] = $this->box_menu_sub($row['arr_sub']);
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

    function box_menu() {
        global $ims;

        $arr_cur = ($ims->conf['cur_group'] > 0 && isset($ims->conf["cur_group_nav"])) ? explode(',', $ims->conf["cur_group_nav"]) : array();

        if (!isset($ims->data["news_group"])) {
            $query = "select group_id, group_nav, parent_id, title, friendly_link  
							from news_group 
							where is_show=1 
							and lang='" . $ims->conf["lang_cur"] . "' 
							order by group_level asc, show_order desc, date_update desc";
            //echo $query;
            $result = $ims->db->query($query);
            $ims->data["news_group"] = array();
            $ims->data["news_group_tree"] = array();
            if ($num = $ims->db->num_rows($result)) {
                while ($row = $ims->db->fetch_row($result)) {
                    $ims->data["news_group"][$row["group_id"]] = $row;

                    $arr_group_nav = explode(',', $row['group_nav']);
                    $str_code = '';
                    $f = 0;
                    foreach ($arr_group_nav as $tmp) {
                        $f++;
                        $str_code .= ($f == 1) ? '[' . $tmp . ']' : '["arr_sub"][' . $tmp . ']';
                    }
                    eval('$ims->data["news_group_tree"]' . $str_code . '["group_id"] = $row["group_id"];
				$ims->data["news_group_tree"]' . $str_code . '["title"] = $row["title"];
				$ims->data["news_group_tree"]' . $str_code . '["friendly_link"] = $row["friendly_link"];');
                }
            }
        }

        $output = '';

        if (count($ims->data["news_group_tree"]) > 0) {
            $data = array(
                'title' => $ims->lang['news']['menu_title'],
                'content' => ''
            );

            $menu_sub = '';
            foreach ($ims->data["news_group_tree"] as $row) {
                $row['link'] = $ims->site_func->get_link('news', $row['friendly_link']);
                $row['class'] = (in_array($row["group_id"], $arr_cur)) ? ' class="current"' : '';
                $row['menu_sub'] = '';
                if (isset($row['arr_sub'])) {
                    $row['menu_sub'] = $this->box_menu_sub($row['arr_sub']);
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

    //=================get_navigation===============
    function get_navigation() {
        global $ims;

        $arr_nav = array(
            array(
                'title' => '<i class="fad fa-home"></i> '.$ims->lang['global']['homepage'],
                'link' => $ims->site_func->get_link('home')
            ),
            array(
                'title' => $ims->setting['news']['news_meta_title'],
                'link' => $ims->site_func->get_link('news')
            )
        );

        $arr_group = ($ims->conf['cur_group'] > 0 && isset($ims->conf["cur_group_nav"])) ? explode(',', $ims->conf["cur_group_nav"]) : array();

        foreach ($arr_group as $group_id) {
            if (isset($ims->data["news_group"][$group_id])) {
                $arr_nav[] = array(
                    'title' => $ims->data["news_group"][$group_id]['title'],
                    'link' => $ims->site_func->get_link('news', $ims->data["news_group"][$group_id]['friendly_link'])
                );
            }
        }

        if (isset($ims->conf['cur_item']) && $ims->conf['cur_item'] > 0 && isset($ims->data["cur_item"]['friendly_link'])) {
            $arr_nav[] = array(
                'title' => $ims->data["cur_item"]['title'],
                'link' => $ims->site_func->get_link('news', '', $ims->data["cur_item"]['friendly_link'])
            );
        }

        return $ims->site->html_arr_navigation($arr_nav);
    }

    function list_item_related($arr_in = array()) {
        global $ims;

        $n = (isset($ims->setting['news']["num_order_detail"])) ? $ims->setting['news']["num_order_detail"] : 30;
        $n = (isset($arr_in["num_show"])) ? $arr_in["num_show"] : $n;
        $title = (isset($arr_in["title"])) ? $arr_in["title"] : $ims->lang['news']['other_news'];

        $output = '';

        $where = (isset($arr_in["where"])) ? $arr_in["where"] : "";
        $order_by = (isset($arr_in["order_by"])) ? $arr_in["order_by"] : " order by show_order desc, date_update desc";

        $sql = "select item_id,title,friendly_link,date_update  
			from news 
			where is_show=1 
			and lang='" . $ims->conf["lang_cur"] . "' 
			" . $where . "			
			limit 0, " . $n;
        //echo $sql;

        $result = $ims->db->query($sql);
        $html_row = '';
        if ($num = $ims->db->num_rows($result)) {
            $i = 0;
            while ($row = $ims->db->fetch_row($result)) {
                $i++;
                $row['link'] = $ims->site_func->get_link('news', '', $row['friendly_link']);
                $row['date_update'] = date('d/m/Y H:i:s', $row['date_update']);

                $this->temp_act->assign('row', $row);
                $this->temp_act->parse("item_related.row");
            }

            $this->temp_act->reset("item_related");
            $this->temp_act->parse("item_related");
            return $this->temp_act->text("item_related");
        }
    }

    function list_other($arr_in = array()) {
        global $ims;

        $n = (isset($ims->setting['news']["num_order_detail"])) ? $ims->setting['news']["num_order_detail"] : 5;
        $n = (isset($arr_in["num_show"])) ? $arr_in["num_show"] : $n;
        $title = (isset($arr_in["title"])) ? $arr_in["title"] : $ims->lang['news']['other_news'];

        $output = '';

        $where = (isset($arr_in["where"])) ? $arr_in["where"] : "";
        $where .= (isset($arr_in["order_by"])) ? $arr_in["order_by"] : " order by date_update desc";

        $sql = "select item_id,title,friendly_link,date_update  
			from news 
			where is_show=1 
			and lang='" . $ims->conf["lang_cur"] . "' 
			" . $where . "		
			limit 0, " . $n;
        //echo $sql;

        $result = $ims->db->query($sql);
        $html_row = '';
        if ($num = $ims->db->num_rows($result)) {
            $i = 0;
            while ($row = $ims->db->fetch_row($result)) {
                $i++;
                $row['link'] = $ims->site_func->get_link('news', '', $row['friendly_link']);
                $row['date_update'] = date('d/m/Y H:i:s', $row['date_update']);

                $this->temp_act->assign('row', $row);
                $this->temp_act->parse("list_other.row");
            }

            $this->temp_act->reset("list_other");
            $this->temp_act->assign('data', array('title' => $title));
            $this->temp_act->parse("list_other");
            return $this->temp_act->text("list_other");
        }
    }


}

?>