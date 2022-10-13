<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();
class sMain {
    var $modules = "event";
    var $action  = "create";
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
//            $data['content']    = $this->do_list($ims->data['cur_group'], $ims->func->get_link($ims->data['cur_group']['friendly_link'], ''));
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
            $edit = isset($ims->get['edit']) ? $ims->get['edit'] : '';

            if(empty($edit)){
                $ims->conf['column_left'] = $this->do_left();
                $data['content'] = $this->do_create();
            }else{
                $item_id = $ims->func->base64_decode($edit);
                $check = $ims->db->load_row('event', 'item_id = '.$item_id.' and user_id = '.$ims->data['user_cur']['user_id']);
                if(!empty($check)){
                    $ims->conf['column_left'] = $this->do_left($edit, $check);
                    $data['content'] = $this->do_create($edit, $check);
                }else{
                    $ims->html->redirect_rel($ims->site_func->get_link('event', '', $ims->setting['event']['create_link']));
                }
            }
        }

        $ims->conf['container_layout'] = 'c-m';
        $ims->conf["class_full"] = 'event';
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("main");
        $ims->output .= $ims->temp_act->text("main");
    }

    public function do_left($edit = '', $info = array()){
        global $ims;
        $data = array();
        $data['link_event'] = $ims->site_func->get_link('event','');
        $data['time'] = $ims->func->rebuild_date('l', time() ).', '.date('d/m, H:i A');
        $cur_step = Session::Get('cur',1);
        $cur = isset($ims->get['step']) ? $ims->get['step'] : 1;
        $step = array(
            '1' => $ims->lang['event']['create_step1'],
            '2' => $ims->lang['event']['create_step2'],
            '3' => $ims->lang['event']['create_step3'],
            '4' => $ims->lang['event']['create_step4']
        );

        $edit_link = ($edit != '') ? '&edit='.$edit : '';
        $check_step = array(1,2,3,4);
        foreach ($step as $key => $value) {
            $val['stt'] = $key;
            $val['title'] = $value;
            $val['cur'] = ($cur == $key) ? 'active' : '';
            $val['link'] = $ims->site_func->get_link('event', $ims->setting['event']['create_link']).'?step='.$key.$edit_link;
            $ims->temp_act->assign('row', $val);
            $ims->temp_act->parse("col_left.row");
        }
        if($edit == '' || empty($info)){
            if(in_array($cur, $check_step)){
                if($cur > $cur_step){
                    Session::Set('cur', $cur);
                }
            }else{
                $ims->html->redirect_rel($ims->site_func->get_link($this->modules, $ims->setting['event']['create_link']).'?step='.$cur_step );
            }
        }else{
            Session::Delete('cur');
        }
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("col_left");
        return $ims->temp_act->text("col_left");
    }

    public function do_create($edit = '', $info = array()){
        global $ims;
        $ims->func->include_js($ims->conf['rooturl']."library/tinymce/tinymce.min.js");
        $ims->func->include_js($ims->dir_js."timepicker/jquery-ui-timepicker-addon.js");
        $ims->func->include_css($ims->dir_js."timepicker/jquery-ui-timepicker-addon.css");
        // $ims->func->include_js($ims->dir_js."jquery-timepicker-custom/dist/wickedpicker.min.js");
        // $ims->func->include_css($ims->dir_js."jquery-timepicker-custom/dist/wickedpicker.min.css");
        $ims->func->load_language('user');

        $arr_step1 = Session::Get('arr_step1', array());
        $arr_step2 = Session::Get('arr_step2', array());
        $arr_step3 = Session::Get('arr_step3', array());
        $arr_price = Session::Get('arr_price', array());

        if(!empty($info)){
            $arr_step1 = empty($arr_step1) ? $info : $arr_step1;
            $arr_step2 = empty($arr_step2) ? $info : $arr_step2;
            $arr_step3 = empty($arr_step3) ? $info : $arr_step3;
            $arr_price = empty($arr_price) ? $info['arr_price'] : $arr_price;
        }

        $cur_step = isset($ims->get['step']) ? $ims->get['step'] : 1;
        $data = array();
        if($edit != '' && !empty($info)){
            $data['id_edit'] = '<input type="hidden" name="id_edit" value="'.$edit.'">';
        }
        $edit = $edit != '' ? '&edit='.$edit : '';
        if($cur_step == 1){
            $option = $ims->db->load_item_arr('event_group', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and group_level = 1 order by title asc', 'group_id, title');
            if($option){
                foreach ($option as $key => $value) {
                    $value['cur'] = (!empty($arr_step1['group_id']) && $value['group_id'] == $arr_step1['group_id']) ? 'selected' : '';
                    $ims->temp_act->assign('option', $value);
                    $ims->temp_act->parse("col_right.step1.option");
                }
            }
            $data['arr_picture'] = $ims->site->get_form_upload_muti('arr_picture');
            $data['link'] = $ims->site_func->get_link($this->modules, $ims->setting['event']['create_link']).'?step=2'.$edit;
            $province = '';
            if(!empty($arr_step1)){
                $data = array_merge($data, $arr_step1);
                $data['date_begin'] = $arr_step1['date_begin'] > 0 ? date('d/m/Y', $arr_step1['date_begin']) : '';
                $data['time_begin'] = $arr_step1['date_begin'] > 0 ? date('H:i', $arr_step1['date_begin']) : '';
                $data['date_end'] = $arr_step1['date_end'] > 0 ? date('d/m/Y', $arr_step1['date_end']) : '';
                $data['time_end'] = $arr_step1['date_end'] > 0 ? date('H:i', $arr_step1['date_end']) : '';
                $province = !empty($arr_step1['province']) ? $arr_step1['province'] : '';
                if(!empty($arr_step1['tag_list'])){
                    $tag_list = explode(',', $arr_step1['tag_list']);
                    foreach ($tag_list as $key => $value) {
                        $ims->temp_act->assign('row', $value);
                        $ims->temp_act->parse("col_right.step1.list_tag");
                    }
                }
                if(!empty($arr_step1['arr_logo'])){
                    $arr_pic = $ims->func->unserialize($arr_step1['arr_logo']);
                    if(!empty($arr_pic)){
                        foreach ($arr_pic as $key => $value) {
                            $val['src'] = $ims->func->get_src_mod($value);
                            $val['src_o'] = $value;
                            $ims->temp_act->assign('pic', $val);
                            $ims->temp_act->parse("col_right.step1.arr_picture");
                        }
                    }
                }
            }
            $data['active_offline'] = 'checked';
            $data['hide_online'] = 'hide';
            $data['disable_online'] = 'disabled';
            if(!empty($data['type_event'])){
                if($data['type_event'] == 'online'){
                    $data['hide_online'] = '';
                    $data['active_online'] = 'checked';
                    $data['hide_offline'] = 'hide';
                    $data['active_offline'] = '';
                    $data['disable_online'] = '';
                }
            }
            $data['active_once'] = 'active';
            $data['disabled_event'] = 'disabled';
            $data['hide_select'] = 'hide';
            if(!empty($arr_step1['frequency'])){
                if($arr_step1['frequency'] != 'once'){
                    $data['active_once'] = '';
                    $data['active_daily'] = 'active';
                    $data['disabled_once'] = 'disabled';
                    $data['disabled_event'] = '';
                    $data['hide_select'] = '';
                }
            }
            $data["list_province"] = $ims->site_func->selectLocation (
                "province",
                "vi", 
                $province,
                " class='form-control select_location_province' data-district='district' data-ward='ward' id='province' required ",
                array('title' => $ims->lang["user"]["select_title"]),
                "province"
            );
            $frequency_option = array(
                array(
                    'val' => 'daily',
                    'title' => 'Hàng ngày'
                ),
                array(
                    'val' => 'weekly',
                    'title' => 'Hàng tuần'
                ),
                array(
                    'val' => 'monthly',
                    'title' => 'Hàng tháng'
                ),
                array(
                    'val' => 'yearly',
                    'title' => 'Hàng năm'
                ),
            );
            foreach ($frequency_option as $row){
                $row['selected'] = (!empty($arr_step1['frequency']) && $arr_step1['frequency'] == $row['val']) ? 'selected' : '';
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("col_right.step1.frequency_option");
            }
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("col_right.step1");
        }elseif($cur_step == 2){
            if(($edit == '' || empty($info)) && empty($arr_step1)){
                $ims->html->redirect_rel($ims->site_func->get_link($this->modules, $ims->setting['event']['create_link']).'?step=1' );
            }
            $content = '';
            if(!empty($arr_step2) ){
                $data['short'] = !empty($arr_step2['short']) ? $ims->func->input_editor_decode($arr_step2['short']) : '';
                $content = !empty($arr_step2['content']) ? $ims->func->input_editor_decode($arr_step2['content']) : '';
                if($arr_step2['picture']){
                    $data['src_o'] = $arr_step2['picture'];
                    $data['src'] = $ims->func->get_src_mod($arr_step2['picture']);
                    $ims->temp_act->assign('data', $data);
                    $ims->temp_act->parse("col_right.step2.img");
                }
            }
            $data['html_content'] = $ims->editor->load_editor ("content", "content", $content, "", "mini", array());
            $data['link'] = $ims->site_func->get_link($this->modules, $ims->setting['event']['create_link']).'?step=3'.$edit;
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("col_right.step2");
        }elseif($cur_step == 3){
            if(($edit == '' || empty($info)) && empty($arr_step2)){
                $ims->html->redirect_rel($ims->site_func->get_link($this->modules, $ims->setting['event']['create_link']).'?step=2' );
            }
            $change = isset($ims->get['change']) ? $ims->get['change'] : 'pay';
            $data['type_pay'] = $data['type_free'] = $data['type_donate'] = '';
            $data['hide_pay'] = $data['hide_free'] = $data['hide_donate'] = 'hide';
            if($change == 'pay'){
                $data['type_pay'] = 'active';
                $data['hide_pay'] = '';
            }elseif($change == 'free'){
                $data['type_free'] = 'active';
                $data['hide_free'] = '';
            }elseif($change == 'donate'){
                $data['type_donate'] = 'active';
                $data['hide_donate'] = '';
            }
            if(!empty($arr_step3) ){
                $data['date_begin'] = date('d/m/Y', $arr_step3['date_begin_ticket']);
                $data['date_end'] = date('d/m/Y', $arr_step3['date_end_ticket']);
                $data['time_begin'] = date('H:i', $arr_step3['date_begin_ticket']);
                $data['time_end'] = date('H:i', $arr_step3['date_end_ticket']);
                $data['min_card'] = $arr_step3['min_ticket'];
                $data['max_card'] = $arr_step3['max_ticket'];
            }
            if(!empty($arr_price) ){
                $arr_price = $ims->func->unserialize($arr_price);
                foreach ($arr_price as $key => $value) {
                    $value['index'] = $key;
                    if($edit != '' && !empty($info)){
                        $value['type_num_remain'] = 'number';
                    }else{
                        $value['type_num_remain'] = 'hidden';
                    }
                    $value['num_ticket_class'] = 'num_ticket';
                    if($value['type_ticket'] == 'pay'){
                        $ims->temp_act->assign('col', $value);
                        $ims->temp_act->parse("col_right.step3.pay");
                    }
                    if($value['type_ticket'] == 'free'){
                        $ims->temp_act->assign('col', $value);
                        $ims->temp_act->parse("col_right.step3.free");
                    }
                    if($value['type_ticket'] == 'donate'){
                        $ims->temp_act->assign('col', $value);
                        $ims->temp_act->parse("col_right.step3.donate");
                    }
                }
            }else{
                $ims->temp_act->assign('type_ticket', 'pay');
                $ims->temp_act->assign('index', $ims->func->random_str(16, 'l'));
                $ims->temp_act->parse("col_right.step3.add.price");
                $ims->temp_act->parse("col_right.step3.add");
            }
            $data['link'] = $ims->site_func->get_link($this->modules, $ims->setting['event']['create_link']).'?step=4'.$edit;
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("col_right.step3");
        }elseif($cur_step == 4){
            $dir_css = $ims->func->dirModules('event', 'assets', 'path');
            $ims->func->include_css($dir_css."css/event.css");

            if(($edit == '' || empty($info)) && empty($arr_step3)){
                $ims->html->redirect_rel($ims->site_func->get_link($this->modules, $ims->setting['event']['create_link']).'?step=3' );
            }

            if($edit != '' && !empty($info)){
                $event = $info;
                $data['picture'] = $ims->func->get_src_mod($event['picture'], 398, 258, 1, 1);
                $data['title'] = $event['title'];
                $data['id'] = $event['item_id'];
                $data['time'] = $ims->func->rebuild_date('l', $event['date_begin'] ).', '.date('d/m/Y, H:i A',$event['date_begin']);
                $data['address'] = $event['address'];
                $data['short'] = $ims->func->short($event['short'], 150);
                $data['people'] = 0;
                $price_min = 0;
                $arr_price = $ims->func->unserialize($event['arr_price']);
                if(!empty($arr_price)){
                    foreach ($arr_price as $item) {
                        $data['people'] += $item['num_ticket'];
                        if($item['price'] < $price_min){
                            $price_min = $item['price'];
                        }
                    }
                }
                $data['price'] = number_format($price_min,0,',','.').' '.$ims->lang['global']['unit'];
                $data['link_share'] = $ims->site_func->get_link('event', '', $event['friendly_link']);
                if($event['is_expected'] == 1){
                    $data['checked_all'] = 'checked';
                }else{
                    $data['checked_people'] = 'checked';
                }
                if($event['day_expected']){
                    $data['checked_booking'] = 'checked';
                }else{
                    $data['checked_now'] = 'checked';
                    $data['disable_day_time'] = 'disabled';
                }

                $ims->temp_act->assign('data', $data);
                $ims->temp_act->parse("col_right.step4");
            }
        }
        // $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("col_right");
        return $ims->temp_act->text("col_right");
    }
    // End class
}
?>