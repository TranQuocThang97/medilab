<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain {

    var $modules = "product";
    var $action  = "concern";

    function __construct() {
        global $ims;
        
        $arrLoad = array(
            'modules'        => $this->modules,
            'action'         => $this->action,
            'template'       => $this->action,
            'js'             => $this->action,
            'css'            => $this->action,
            'use_func'       => "", // Sử dụng func
            'use_navigation' => 0, // Sử dụng navigation
            'required_login' => 1, // Bắt buộc đăng nhập
        );
        $ims->func->loadTemplate($arrLoad);

        require($this->modules . "_func.php");
        $this->modFunc = new productFunc($this);

        $data = array();
        if (isset($ims->conf['cur_group'])) {
            
            // Current menu
            $arr_group_nav = (!empty($row["group_nav"])) ? explode(',', $row["group_nav"]) : array();
            foreach ($arr_group_nav as $v) {
                $ims->conf['menu_action'][] = $this->modules.'-group-'.$v;
            }
            // End current menu
            //Make link lang
            $load_lang = $ims->db->load_item_arr("product_group", " group_id='".$ims->conf['cur_group']."' ", "friendly_link, lang");
            foreach ($load_lang as $key => $row_lang) {
                $ims->data['link_lang'][$row_lang['lang']] = $ims->site_func->get_link_lang($row_lang['lang'], $this->modules, $row_lang['friendly_link']);
            }
            //End Make link lang

            $ims->site->get_seo($ims->data['cur_group']);
            $ims->conf["cur_group_nav"] = $ims->data['cur_group']["group_nav"];

            $data['content']    = $this->do_list($ims->data['cur_group'], $ims->func->get_link($ims->data['cur_group']['friendly_link'], ''));
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

            if(!empty($ims->get['act']) && in_array($ims->get['act'], array('add', 'edit'))){
                $data['content'] = $this->do_add_concern();
            }else{
                $data['content'] = $this->do_list();
            }
        }

        $ims->conf['container_layout'] = 'm';
        $ims->conf["class_full"] = 'product';
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("main");
        $ims->output .= $ims->temp_act->text("main");
    }

    function do_list(){
        global $ims;
        if(isset($ims->get['trash']) && $ims->get['trash'] == 1){
            $ims->conf['qr'] = ' is_show = 0 and lang = "'.$ims->conf['lang_cur'].'"';
        }
        $data = array();
        $data["list_province"] = $ims->site_func->selectLocation (
            "province",
            "vi",
            !empty($ims->get['province']) ? $ims->get['province'] : '',
            " class='form-control select_location_province' data-district='district' data-ward='ward' id='province' ",
            array('title' => $ims->lang["product"]["province_select"]),
            "province"
        );

        $where = '';
        $keyword = (!empty($ims->input['keyword'])) ? trim($ims->input['keyword']) : '';
        if($keyword) {
            $data['text_search'] = $keyword;
            $arr_key = explode(' ', $keyword);
            $arr_title = array();
            $arr_phone = array();
            $arr_tax = array();
            foreach($arr_key as $value) {
                $value = trim($value);
                if(!empty($value)) {
                    $arr_title[] = "title like '%".$value."%'";
                    $arr_phone[] = "phone like '%".$value."%'";
                    $arr_tax[] = "tax_number like '%".$value."%'";
                }
            }
            if(count($arr_title) > 0 && count($arr_phone) > 0) {
                $where .= ' and (('.implode(' and ', $arr_title).') or ('.implode(' and ', $arr_phone).') or ('.implode(' and ', $arr_tax).'))';
            }
        }
        $province = (!empty($ims->input['province'])) ? trim($ims->input['province']) : '';
        if($province){
            $where .= ' and province = "'.$province.'"';
        }

        parse_str($_SERVER['QUERY_STRING'], $pr);
        $pr_tmp = array();
        foreach ($pr as $k => $v){
            if($v == ''){
                unset($pr[$k]);
            }else{
                $pr_tmp[] = $k.'='.$v;
            }
        }
        $param = implode('&', $pr_tmp);
        $ext = ($param != '') ? '&'.$param : '';
        if(isset($ims->get['p'])){
            $pos_p =  strpos($ext, 'p=');
            $exclude_p = ($pos_p == 0) ? 'p='.$ims->get['p'] : '&p='.$ims->get['p'];
            $ext = str_replace($exclude_p, '', $ext);
        }

        $where = ' and user_id = '.$ims->data['user_cur']['user_id'].$where;
        $result = $ims->db->load_item('product_concern', $ims->conf['qr'].$where, 'item_id');
        $nav = '';
        $link_action = $ims->site_func->get_link('product', '', $ims->setting['product']['concern_link']);
        if($result){
            $n = $ims->setting['product']['num_list'];
            $p = $ims->func->if_isset($ims->input["p"], 1);
            $num_total = $ims->db->do_get_num('product_concern', $ims->conf['qr'].$where);
            $num_items = ceil($num_total / $n);
            if ($p > $num_items)
                $p = $num_items;
            if ($p < 1)
                $p = 1;
            $start = ($p - 1) * $n;
            $nav = $ims->site->paginate($link_action, $num_total, $n, $ext, $p);

            $result = $ims->db->load_item_arr('product_concern', $ims->conf['qr'].$where.' order by show_order desc, date_create desc LIMIT '.$start.','.$n, 'title, picture, item_id, tax_number, province, website, phone');
            $i = 0;
            foreach ($result as $row){
                $i++;
                $row['stt'] = $i;
                $row['picture'] = $ims->func->get_src_mod($row['picture'], 80, 80, 1, 1);
                $row['item_id'] = $ims->func->base64_encode($row['item_id']);
                $row['add'] = $ims->db->load_item('location_province', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and code = "'.$row['province'].'"', 'title');
                $row['link_action'] = $link_action;
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("concern.item");
            }
        }else{
            $ims->temp_act->parse("concern.empty");
        }
        $ims->temp_act->assign('link_action', $link_action);
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->assign('nav', $nav);
        $ims->temp_act->parse("concern");
        return $ims->temp_act->text("concern");
    }

    function do_add_concern(){
        global $ims;
        $data = array();

        if(!empty($ims->get['item'])){
            $item_id = $ims->func->base64_decode($ims->get['item']);
            $data = $ims->db->load_row('product_concern', 'is_show != 0 and item_id = '.$item_id.' and user_id = '.$ims->data['user_cur']['user_id']);
            if(empty($data)){
                require_once ($ims->conf["rootpath"]."404.php");die;
            }else{
                $data['edit_item'] = '<input type="hidden" name="item" value="'.$ims->get['item'].'">';
            }
        }
        $data["list_country"] = $ims->site_func->selectLocation (
            "country",
            "",
            !empty($data['country']) ? $data['country'] : 'vi',
            " class='form-control select_location_country' data-province='province' data-district='district' data-ward='ward' id='country' ",
            '',
            "country"
        );
        $data["list_province"] = $ims->site_func->selectLocation (
            "province",
            !empty($data['country']) ? $data['country'] : 'vi',
            !empty($data['province']) ? $data['province'] : '',
            " class='form-control select_location_province' data-district='district' data-ward='ward' id='province' ",
            array('title' => $ims->lang["global"]["select_province"]),
            "province"
        );
        $data["list_district"] = $ims->site_func->selectLocation (
            "district",
            !empty($data['province']) ? $data['province'] : '',
            !empty($data['district']) ? $data['district'] : '',
            " class='form-control select_location_district' data-ward='ward' id='district' ",
            array('title' => $ims->lang["global"]["select_district"]),
            "district"
        );
        $data["list_ward"] = $ims->site_func->selectLocation (
            "ward",
            !empty($data['district']) ? $data['district'] : '',
            !empty($data['ward']) ? $data['ward'] : '',
            " class='form-control' id='ward' ",
            array('title' => $ims->lang["global"]["select_ward"]),
            "ward"
        );
        if(!empty($data['picture'])){
            $data['src'] = $ims->func->get_src_mod($data['picture']);
            $data['src_ori'] = $data['picture'];
            $data['show_photo'] = 'show';
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("add_concern.picture");
        }
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("add_concern");
        return $ims->temp_act->text("add_concern");
    }
    // End class
}

?>