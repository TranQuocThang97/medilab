<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain {

    var $modules = "event";
    var $action  = "event";

    function __construct() {
        global $ims;
        
        $arrLoad = array(
            'modules'        => $this->modules,
            'action'         => $this->action,
            'template'       => $this->modules,
            'js'             => $this->modules,
            'css'            => $this->modules,
            'use_func'       => "", // Sử dụng func
            'use_navigation' => 0, // Sử dụng navigation
            'required_login' => 0, // Bắt buộc đăng nhập
        );
        $ims->func->loadTemplate($arrLoad);

        require($this->modules . "_func.php");
        $this->modFunc = new eventFunc($this);

        $data = array();        
        if (isset($ims->conf['cur_group'])) {
            
            // Current menu
            $arr_group_nav = (!empty($row["group_nav"])) ? explode(',', $row["group_nav"]) : array();
            foreach ($arr_group_nav as $v) {
                $ims->conf['menu_action'][] = $this->modules.'-group-'.$v;
            }
            // End current menu
            //Make link lang
            $load_lang = $ims->db->load_item_arr("event_group", " group_id='".$ims->conf['cur_group']."' ", "friendly_link, lang");
            foreach ($load_lang as $key => $row_lang) {
                $ims->data['link_lang'][$row_lang['lang']] = $ims->site_func->get_link_lang($row_lang['lang'], $this->modules, $row_lang['friendly_link']);
            }
            //End Make link lang

            $ims->site->get_seo($ims->data['cur_group']);
            $ims->conf["cur_group_nav"] = $ims->data['cur_group']["group_nav"];

            $data['content']    = $this->do_list($ims->data['cur_group'], $ims->site_func->get_link('event', $ims->data['cur_group']['friendly_link']));
        } else {
            foreach ($ims->data['lang'] as $row_lang) {
                $ims->data['link_lang'][$row_lang['name']] = $ims->site_func->get_link_lang($row_lang['name'], $this->modules);
            }
            $ims->site->get_seo (array(
                'meta_title' => $ims->func->if_isset($ims->setting[$this->modules][$this->action."_meta_title"]),
                'meta_key'   => $ims->func->if_isset($ims->setting[$this->modules][$this->action."_meta_key"]),
                'meta_desc'  => $ims->func->if_isset($ims->setting[$this->modules][$this->action."_meta_desc"])
            ));
            $ims->conf["cur_group"] = 0;                
            $data['content'] = $this->do_list(array(), $ims->site_func->get_link('event'));
        }
        $data['box_banner'] = $this->do_banner();

        $ims->conf['container_layout'] = 'm';
        $ims->conf["class_full"] = 'event';
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("main");
        $ims->output .= $ims->temp_act->text("main");
    }

    function do_list($info = array(), $link_action = '') {
        global $ims;
        $title = !empty($info) ? $info['title'] : $ims->lang['event']['mod_title'];

        $where_root = !empty($info) ? ' and (find_in_set('.$info['group_id'].', group_nav) or find_in_set('.$info['group_id'].', group_related)) ' : '';
        $where_province = !empty($info) ? ' and find_in_set('.$info['group_id'].', ev.group_nav) ' : '';

        $keyword = !empty($ims->get['keyword']) ? $ims->get['keyword'] : '';
        if ($keyword) {
            $title = $ims->lang['global']['search_result_title'];
            $arr_key = explode(' ', $keyword);
            $arr_tmp = array();
            $arr_tmp_prv = array();
            foreach ($arr_key as $value) {
                $value = trim($value);
                if (!empty($value)) {
                    $arr_tmp[] = "title LIKE '%".$value."%'";
                    $arr_tmp_prv[] = "ev.title LIKE '%".$value."%'";
                }
            }
            if (count($arr_tmp) > 0) {
                $where_root .= " AND (".implode(" AND ", $arr_tmp).")";
                $where_province .= " AND (".implode(" AND ", $arr_tmp_prv).")";
            }
        }

        $list_province = $ims->db->load_item_arr('location_province as prv, event as ev', ' ev.province = prv.code and ev.is_show = 1 and prv.is_show = 1 '.$where_province.' order by prv.title asc', 'DISTINCT (prv.code), prv.title');
        if($list_province){
            $select_location = $ims->lang['event']['select_location'];
            foreach ($list_province as $prv){
                $ims->temp_act->assign('prv', $prv);
                $ims->temp_act->parse("list_event.main.select.item");
            }
            $ims->temp_act->assign('select_location', $select_location);
            $ims->temp_act->assign('province_cur', -1);
            $ims->temp_act->parse("list_event.main.select");
        }

        $day = date('N', time()); // Thứ hiện tại
        $date_cur = date('m/d/Y', time()); // Ngày hiện tại

        $today_min = strtotime($date_cur.' 0:0:0');
        $today_max = strtotime($date_cur.' 23:59:59');

        $weekend_min = ($day < 5) ? $today_min + (5 - $day) * 24 * 60 * 60 + (5 * 60 * 60) : $today_min + 24 * 60 * 60 + (5 * 60 * 60);
        $weekend_max = ($day < 7) ? $today_min + (8 - $day) * 24 * 60 * 60 : $today_min + 24 * 60 * 60;

        $arr_nav = array(
            array(
                'title' => $ims->lang['event']['all'],
                'group_id' => 'a'
            ),
            array(
                'title' => $ims->lang['event']['today'],
                'where' => ' and (date_create >= '.$today_min.' and date_create <= '.$today_max.') ',
                'group_id' => 'b',
                'type_show' => 'today'
            ),
            array(
                'title' => $ims->lang['event']['weekend'],
                'where' => ' and (date_begin >= '.$weekend_min.' and date_end <= '.$weekend_max.') ',
                'group_id' => 'c',
                'type_show' => 'weekend'
            ),
        );

        $list_data = $arr_nav;
        $i = 0;
        foreach ($list_data as $row){
            $i++;
            $row['active'] = ($i == 1) ? 'active' : '';
            $row['active_content'] = ($i == 1) ? '' : 'd-none';
            $where = $where_root;
            if(isset($row['where'])){
                $where .= $row['where'];
            }

            $arr_in = array(
                'group_id' => !empty($info['group_id']) ? $info['group_id'] : 0,
                'where' => $where.' order by show_order desc, date_create desc',
                'viewmore_ajax' => 1,
                'type_show' => isset($row['type_show']) ? $row['type_show'] : '',
            );
            $row['content'] = $ims->call->mFunc('event', 'html_list_item', array($arr_in));
            $ims->temp_act->assign('row', $row);
            $ims->temp_act->parse("list_event.main.li");
            $ims->temp_act->parse("list_event.main.content");
        }

        $ims->temp_act->assign('title', $title);
        $ims->temp_act->assign('none_event_at', 'd-none');
        $ims->temp_act->parse("list_event.main");
        $ims->temp_act->parse("list_event.script");
        $ims->temp_act->parse("list_event");
        return $ims->temp_act->text("list_event");

    }
    function do_banner(){
        global $ims;

        $data = array();
        $data['list_menu'] = $ims->site->list_menu ('menu_header_top', 'menu');

        $arr_banner = $ims->data["banner"]["banner-main"];
        if($arr_banner){
            foreach ($arr_banner as $banner) {
                $banner['link'] = $ims->site_func->get_link_menu($banner['link'], $banner['link_type']);
                $banner['alt'] = ($banner['title']!='') ? $banner['title'] : "img";
                $banner['picture'] = $ims->func->get_src_mod($banner['content'], 964, 371, 1, 1);
                $ims->temp_act->assign('row', $banner);
                $ims->temp_act->parse("banner.row");
            }
        }

        $ims->temp_act->assign('data',$data);
        $ims->temp_act->parse('banner');
        return $ims->temp_act->text('banner');
    }
    // End class
}
