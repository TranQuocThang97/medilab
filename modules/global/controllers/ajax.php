<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain {

    var $modules = "global";
    var $action = "ajax";

    /**
        * function __construct ()
        * Khoi tao 
    **/
    function __construct() {
        global $ims;

        $ims->func->load_language($this->modules);

        $fun = (isset($ims->input['f'])) ? $ims->input['f'] : '';

        switch ($fun) {
            case "captcha":
                $this->do_captcha();
                exit;
                break;
            case "captcha_refresh":
                $this->do_captcha_refresh();
                exit;
                break;
            case "uploadmuti":
                require_once ($ims->conf["rootpath"].'modules/global/include/UploadHandler.php');
                $upload_handler = new UploadHandler();
                exit;
                break;
            case "uploadpicmuti":
                require_once ($ims->conf["rootpath"].'modules/global/include/UploadHandler.php');
                $upload_handler = new UploadHandler();
                exit;
                break;
            case "popup":
                $this->do_popup();
                exit;
                break;
            case "loadLocationWith":
                echo $this->do_loadLocationWith ();
                exit;
                break;

            case "create_step1":
                echo $this->do_create_step1();
                exit;
                break;
            case "create_step2":
                echo $this->do_create_step2();
                exit;
                break;
            case "create_step3":
                echo $this->do_create_step3();
                exit;
                break;
            case "add_price":
                echo $this->do_add_price();
                exit;
                break;
            case "addEvent":
                echo $this->do_addEvent();
                exit;
                break;
            case "load_formPromo":
                echo $this->do_load_formPromo();
                exit;
                break;
            case "createPromo":
                echo $this->do_createPromo();
                exit;
                break;
            case "updateStatus":
                echo $this->do_updateStatus();
                exit;
                break;
            case "load_preview":
                echo $this->do_load_preview();
                exit;
                break;

            default:
                flush();
                echo '';
                exit;
                break;
        }
        flush();
        exit;
    }

    function do_loadLocationWith (){
        global $ims;
        
        $output = array(
            'ok' => 1,
            'html' => ''
        );
        
//        $output['html'] = '<option value="">'.$ims->lang['global']['select_title'].'</option>';
        $parent_id = $ims->func->if_isset($ims->post["parent_id"], 0);
        $type      = $ims->func->if_isset($ims->post["type"], "");
        $output['html'] = '<option value="">'.$ims->lang['global']['select_'.$type].'</option>';
        $where = '';
        $tbl   = '';

        switch ($type) {
            case "ward":
            $tbl  = "location_ward";
            $where .= " AND district_code='".$parent_id."'";
            break;
            case "district":
            $tbl  = "location_district";
            $where .= " AND province_code='".$parent_id."'";
            break;
            case "province":
            $tbl  = "location_province";
            $where .= " AND country_code='".$parent_id."'";
            break;
            case "country":
            $tbl  = "location_country";
            $where .= " AND area_code='".$parent_id."'";
            break;
            default:
            break;
        }
        if(empty($where) || $parent_id=0 || $tbl=='') {
            //$output['html'] = '<option value="">'.$ims->lang['global']['select_title'].'</option>';
            return json_encode($output);
        }
        
        $data = $ims->load_data->data_table (
            $tbl, 
            "code", 
            "code, title", 
            "is_show=1 AND lang='".$ims->conf['lang_cur']."' ".$where." ORDER BY title ASC"
        );
        $output['html'] .= $ims->html->select_op ($data, "", 'root');
        return json_encode($output);
    }
    function do_captcha() {
        global $ims;
        Captcha::pic();
    }
    function do_captcha_refresh() {
        global $ims;

        Captcha::Set();
        Captcha::pic();
    }
    function do_popup() {
        global $ims;
        $output = '';
        
        $limit = 1;
        $group_id = (isset($ims->input['banner_pos']) && $ims->input['banner_pos']) ? trim($ims->input['banner_pos']) : '';
        if($group_id) {
            $ims->load_data->data_banner_group();
            $ims->load_data->data_banner();
        }
        if (isset($ims->data["banner_group"][$group_id]) && isset($ims->data["banner"][$group_id])) {
            $i = 0;
            foreach ($ims->data["banner"][$group_id] as $banner) {
                $i++;
                $w = $ims->data["banner_group"][$group_id]['width'];
                $h = $ims->data["banner_group"][$group_id]['height'];

                $style_pic = '';
                $style_frame = '';
                if ($ims->data["banner_group"][$group_id]['type_show'] == 'fixed') {
                    $style_frame = "width:" . $w . "px;";
                    $style_frame .= ($h > 0) ? "height:" . $h . "px;overflow:hidden;" : "";
                    $style_frame = ($w > 0 || $h > 0) ? $style : '';
                } elseif ($ims->data["banner_group"][$group_id]['type_show'] == 'full') {
                    // $style_pic = "width:100%;";
                }

                $banner['link'] = $ims->site_func->get_link_menu($banner['link'], $banner['link_type']);

                if ($banner['type'] == 'image') {
                    //$banner['content'] = '<img src="'.$ims->conf["rooturl"].'uploads/banner/'.$banner['content'].'" alt="'.$banner['title'].'" />';
                    $banner['content'] = '<a href="' . $banner['link'] . '" target="' . $banner['target'] . '">' . $ims->func->get_pic_mod($banner['content'], $w, $h, " alt=\"" . $banner['title'] . "\" style=\"" . $style_pic . "\"", 1, 0, array('fix_width' => 1)) . '</a>';
                } elseif ($banner['type'] == 'flash' && $banner['content']) {
                    //$ims->func->include_js ($ims->dir_js.'swfobject/swfobject.js');

                    $w = ($w) ? $w : '100%';
                    $h = ($h) ? $h : $w;
                    $tl = ((int) $h / (int) $w) * 100;
                    $tmp = 'flash_file_' . $ims->func->random_str('6');
                    $banner['content'] = '<div style="position:relative; padding-bottom:' . $tl . '%;"><div id="' . $tmp . '"></div></div>
                    <script type="text/javascript">
                    swfobject.embedSWF("' . $ims->conf['rooturl_web'] . 'uploads/' . $banner['content'] . '", "' . $tmp . '", "' . $w . '", "' . $h . '", "9.0.0", "' . $ims->dir_js . 'swfobject/expressInstall.swf");
                    </script>';
                }
                $output .= '<div class="banner_item" style="' . $style_frame . '">' . $banner['content'] . '</div>';
                if ($i >= $limit && $limit > 0) {
                    break;
                }
            }
            
            $output='<div class="popup_banner">'.$output.'</div>';
        }
        echo $output;
    }
    function do_create_step1(){
        global $ims;
        $ims->func->load_language('event');
        $out = array(
            'ok' => 0,
            'mess' => ''
        );

        if($ims->site_func->checkUserLogin() == 1) {
            $input_tmp = $ims->post;
            $attachment = !empty($_FILES["arr_picture"]) ? $_FILES["arr_picture"] : array();
            $file = $arr_file = $file_arr = array();
            if(!empty($attachment)){
                $folder_upload = "user/".$ims->data['user_cur']['folder_upload'].'/tmp';
                foreach ($attachment as $key => $value) {
                    foreach ($value as $k => $val) {
                        $file[$k][$key] = $val;
                    }
                }
                if($file){
                    foreach ($file as $key => $value) {
                        $file_upload = $this->upload_file($folder_upload, $value);
                        if($file_upload['ok'] == 1){
                            $arr_file[] = $file_upload['link'];
                        }else{
                            $out['mess'] = $file_upload['mess'];
                            break;
                        }
                    }
                }
            }
            if($out['mess'] == ''){
                $arr_pic = !empty($input_tmp['arr_pic']) ? $input_tmp['arr_pic'] : array();
                $file_arr = array_merge($arr_pic, $arr_file);

                $date_begin = str_replace('/', '-', $input_tmp['date_begin']);
                $date_begin = date('Y-m-d', strtotime($date_begin));
                $date_begin = strtotime($date_begin.' '.$input_tmp['time_begin']);

                $date_end = str_replace('/', '-', $input_tmp['date_end']);
                $date_end = date('Y-m-d', strtotime($date_end));
                $date_end = strtotime($date_end.' '.$input_tmp['time_end']);

                $arr_step1 = array();
                if($input_tmp){
                    $arr_step1['title1'] = $input_tmp['title1'];
                    $arr_step1['title'] = $input_tmp['title'];
                    $arr_step1['organizer'] = $input_tmp['organizer'];
                    $arr_step1['organizer_phone'] = $input_tmp['organizer_phone'];
                    $arr_step1['arr_logo'] = !empty($file_arr) ? $ims->func->serialize($file_arr) : '';
                    $arr_step1['group_id'] = $input_tmp['group_id'];
                    $arr_step1['tag_list'] = !empty($input_tmp['tag_list']) ? implode(',', $input_tmp['tag_list']) : '';
                    $arr_step1['type_event'] = $input_tmp['type_event'] == 0 ? 'offline' : 'online';
                    $arr_step1['province'] = !empty($input_tmp['province']) ? $input_tmp['province'] : '';
                    $arr_step1['address'] = !empty($input_tmp['address']) ? $input_tmp['address'] : '';
                    $arr_step1['frame_maps'] = !empty($input_tmp['iframe']) ? $input_tmp['iframe'] : '';
                    $arr_step1['link_event'] = !empty($input_tmp['link_event']) ? $input_tmp['link_event'] : '';
                    $arr_step1['frequency'] = $input_tmp['frequency'];
                    $arr_step1['date_begin'] = $date_begin;
                    $arr_step1['date_end'] = $date_end;
                }
                Session::Set('arr_step1', $arr_step1);
                $out['ok'] = 1;
            }
        }else{
            $out['mess'] = $ims->lang['event']['need_login'];
        }
        return json_encode($out);
    }
    function do_create_step2(){
        global $ims;
        $ims->func->load_language('event');
        $out = array(
            'ok' => 0,
            'mess' => ''
        );

        if($ims->site_func->checkUserLogin() == 1) {
            $input_tmp = $ims->post;

            $link = !empty($input_tmp['picture']) ? $input_tmp['picture'] : '';
            $attachment = !empty($_FILES['picture']) ? $_FILES['picture'] : '';
            if($input_tmp['content'] == ''){
                $out['mess'] = $ims->lang['event']['input_content_require'];
            }elseif (empty($link)){
                if($attachment['error'] === 4){
                    $out['mess'] = $ims->lang['event']['input_image_require'];
                }elseif( $attachment['error'] === 0){
                    $folder_upload = "user/".$ims->data['user_cur']['folder_upload'].'/tmp';

                    $upload_file = $this->upload_file($folder_upload, $attachment);
                    if($upload_file['ok'] == 1){
                        $link = $upload_file['link'];
                    }else{
                        $out['mess'] = $upload_file['mess'];
                    }
                }
            }

            $arr_step2 = array();
            if($out['mess'] == ''){
                $arr_step2['picture'] = $link;
                $arr_step2['short'] = $input_tmp['short'];
                $arr_step2['content'] = $input_tmp['content'];

                Session::Set('arr_step2', $arr_step2);
                $out['ok'] = 1;
            }
        }else{
            $out['mess'] = $ims->lang['event']['need_login'];
        }

        return json_encode($out);
    }
    function do_create_step3(){
        global $ims;

        $dir_view = $ims->func->dirModules('event', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."create.tpl");

        $ims->func->load_language('event');
        $ims->site_func->setting('event');
        $ims->temp_act->assign('LANG', $ims->lang);

        $out = array(
            'ok' => 0,
            'html' => '',
            'mess' => '',
        );

        if($ims->site_func->checkUserLogin() == 1) {
            $input_tmp = $ims->post;

            $pay = isset($input_tmp['pay']) ? $input_tmp['pay'] : array();
            $free = isset($input_tmp['free']) ? $input_tmp['free'] : array();
            $donate = isset($input_tmp['donate']) ? $input_tmp['donate'] : array();
            if($pay){
                $pay_tmp = array();
                foreach ($pay as $k => $item){
                    if($item['\'title\''] == '' || $item['\'num_ticket\''] == '' || $item['\'price\''] == ''){
                        $out['mess'] = $ims->lang['event']['empty_price_pay'];
                        return json_encode($out);
                    }else{
                        foreach ($item as $k1 => $v){
                            $pay_tmp[str_replace("'", '', $k)][str_replace("'", '', $k1)] = $v;
                        }
                    }
                }
                $pay = $pay_tmp;
            }
            if($free){
                $free_tmp = array();
                foreach ($free as $k => $item){
                    if($item['\'title\''] == '' || $item['\'num_ticket\''] == '' || $item['\'price\''] == ''){
                        $out['mess'] = $ims->lang['event']['empty_price_free'];
                        return json_encode($out);
                    }else{
                        foreach ($item as $k1 => $v){
                            $free_tmp[str_replace("'", '', $k)][str_replace("'", '', $k1)] = $v;
                        }
                    }
                }
                $free = $free_tmp;
            }
            if($donate){
                $donate_tmp = array();
                foreach ($donate as $k => $item){
                    if($item['\'title\''] == '' || $item['\'num_ticket\''] == '' || $item['\'price\''] == ''){
                        $out['mess'] = $ims->lang['event']['empty_price_donate'];
                        return json_encode($out);
                    }else{
                        foreach ($item as $k1 => $v){
                            $donate_tmp[str_replace("'", '', $k)][str_replace("'", '', $k1)] = $v;
                        }
                    }
                }
                $donate = $donate_tmp;
            }
            $arr_price = array_merge($free, $pay, $donate);
            if(!empty($arr_price)){
                $arr_price = $ims->func->serialize($arr_price);
            }

            $arr_step3 = array();
            if($input_tmp){
                $date_begin = str_replace('/', '-', $input_tmp['date_begin']);
                $date_begin = date('Y-m-d', strtotime($date_begin));
                $date_begin = strtotime($date_begin.' '.$input_tmp['time_begin']);

                $date_end = str_replace('/', '-', $input_tmp['date_end']);
                $date_end = date('Y-m-d', strtotime($date_end));
                $date_end = strtotime($date_end.' '.$input_tmp['time_end']);

                $arr_step3['arr_price'] = $arr_price;
                $arr_step3['date_begin_ticket'] = $date_begin;
                $arr_step3['date_end_ticket'] = $date_end;
                $arr_step3['min_ticket'] = $input_tmp['min_card'];
                $arr_step3['max_ticket'] = $input_tmp['max_card'];
            }

            if($arr_step3){
                $out['ok'] = 2;
                Session::Set('arr_step3', $arr_step3);
                Session::Set('arr_price', $arr_price);

                if(!empty($arr_price)){
                    $arr_price = $ims->func->unserialize($arr_price);
                    foreach ($arr_price as $key => $value) {
                        $value['price_text'] = number_format($value['price'],0,',','.');
                        $value['date'] = date('d/m/Y', $arr_step3['date_end_ticket']);
                        if(empty($input_tmp['id_edit'])){
                            $value['link'] = $ims->site_func->get_link($this->modules,$ims->setting['event']['create_link']).'?step=3&change='.$value['type_ticket'];
                        }else{
                            $value['link'] = $ims->site_func->get_link($this->modules,$ims->setting['event']['create_link']).'?edit='.$input_tmp['id_edit'].'&step=3&change='.$value['type_ticket'];
                        }
                        $ims->temp_act->assign('row', $value);
                        $ims->temp_act->parse("col_price");
                    }
                    $out['html'] = $ims->temp_act->text("col_price");
                }
            }else{
                $out['mess'] = $ims->lang['event']['input_text_info'];
            }
        }else{
            $out['mess'] = $ims->lang['event']['need_login'];
        }

        return json_encode($out);
    }

    function do_add_price(){
        global $ims;
        $dir_view = $ims->func->dirModules('event', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."create.tpl");

        $ims->func->load_language('event');
        $ims->temp_act->assign('LANG', $ims->lang);

        $out = array('html' => '');

        $post = $ims->post['data'];
        $type = $ims->post['type'];
        if(!empty($post)){
            foreach ($post as $item){
                if(in_array(substr($item['name'], 0, 3), array('pay', 'fre', 'don'))){
                    eval("$".$item['name'].' = "'.$item['value'].'";');
                }
            }
        }
        $arr_key = array();
        if(!empty($pay)){
            foreach ($pay as $k => $v){
                $arr_key[] = $k;
            }
        }
        if(!empty($free)){
            foreach ($free as $k1 => $v){
                $arr_key[] = $k1;
            }
        }
        if(!empty($donate)){
            foreach ($donate as $k2 => $v){
                $arr_key[] = $k2;
            }
        }

        $ims->temp_act->assign('clear_div', '<p class="clear_div"><span class="del"><i class="far fa-times"></i></span></p>');
        $ims->temp_act->assign('type_ticket', $type);
        $ims->temp_act->assign('index', $ims->func->random_str(16, 'l'));
        if($type == 'free'){
            $ims->temp_act->parse("col_right.step3.add.free");
        }else{
            $ims->temp_act->parse("col_right.step3.add.price");
        }
        $ims->temp_act->reset("col_right.step3.add");
        $ims->temp_act->parse("col_right.step3.add");
        $out ['html'] = $ims->temp_act->text("col_right.step3.add");

        return json_encode($out);
    }

    function do_addEvent(){
        global $ims;

        $dir_view = $ims->func->dirModules('event', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."create.tpl");

        $ims->func->load_language('event');
        $ims->site_func->setting('event');
        $ims->temp_act->assign('LANG', $ims->lang);

        $out = array(
            'ok' => 0,
            'mess' => '',
        );

        if($ims->site_func->checkUserLogin() == 1) {
            $lang_cur = $ims->post['lang_cur'];
            if(empty($ims->post['id_edit'])){
                $arr_step1 = Session::Get('arr_step1', array());
                $arr_step2 = Session::Get('arr_step2', array());
                $arr_step3 = Session::Get('arr_step3', array());
                $arr_info = array_merge($arr_step1, $arr_step2, $arr_step3);
            }else{
                $arr_info = $ims->db->load_row('event', 'lang = "'.$lang_cur.'" and item_id = '.$ims->func->base64_decode($ims->post['id_edit']));
            }

            $folder_upload = "user/".$ims->data['user_cur']['folder_upload'].'/'.date('Y_m',time()).'/event';
            $ims->func->rmkdir($folder_upload);

            $folder_upload_user = "user/".$ims->data['user_cur']['folder_upload'].'/tmp';

            $arr_logo_user = array();
            if(!empty($arr_info['arr_logo'])){
                $user_logo = $ims->func->unserialize($arr_info['arr_logo']);
                if(!empty($user_logo)){
                    foreach ($user_logo as $key => $value) {
                        $arr_logo_user[] = str_replace($folder_upload_user, $folder_upload, $value);
                        $file_move = str_replace($folder_upload_user, $folder_upload, $value);
                        rename($ims->conf['rootpath'].'uploads/'.$value, $ims->conf['rootpath'].'uploads/'.$file_move);
                    }
                }
            }
            $user_pic = '';
            if(!empty($arr_info['picture'])){
                $user_pic = str_replace($folder_upload_user, $folder_upload, $arr_info['picture']);
                rename($ims->conf['rootpath'].'uploads/'.$arr_info['picture'], $ims->conf['rootpath'].'uploads/'.$user_pic);
            }

            if(empty($ims->post['id_edit'])){
                $arr_info['item_id'] = $ims->db->getAutoIncrement('event');
            }else{
                $arr_info['item_id'] = $ims->func->base64_decode($ims->post['id_edit']);
            }
            $arr_info['group_nav'] = $ims->db->load_item('event_group', 'is_show = 1 and group_id = '.$arr_info['group_id'], 'group_nav');
            $arr_info['arr_logo'] = $ims->func->serialize($arr_logo_user);
            $arr_info['picture'] = $user_pic;
            $arr_info['meta_title'] = $arr_info['title'];
            $arr_info['meta_key'] = $arr_info['title'];
            $arr_info['is_show'] = -1;
            $arr_info['date_update'] = time();

            if(empty($ims->post['id_edit'])){
                $arr_info['user_id'] = $ims->data['user_cur']['user_id'];
                $arr_info['friendly_link'] = $ims->func->get_friendly_link_db($arr_info['title'], 'event', '', $arr_info['item_id']);
                $arr_info['date_create']  = time();
                $ok = $ims->db->do_insert("event", $arr_info);
            }else{
                $ok = $ims->db->do_update("event", $arr_info, ' item_id = "'.$arr_info['item_id'].'" and user_id = '.$ims->data['user_cur']['user_id']);
            }
            if($ok){
                $out['ok'] = 1;
                Session::Delete('arr_step1');
                Session::Delete('arr_step2');
                Session::Delete('arr_step3');
                Session::Delete('arr_price');
                $out['link'] = $ims->site_func->get_link('event', '', $ims->setting['event']['create_link']).'?step=4&edit='.$ims->func->base64_encode($arr_info['item_id']);
            }else{
                $out['mess'] = $ims->lang['event']['input_text_info'];
            }
        }else{
            $out['mess'] = $ims->lang['event']['need_login'];
        }

        return json_encode($out);    
    }
    function do_load_formPromo(){
        global $ims;

        $out = array(
            'ok' => 1,
            'html' => ''
        );
        $data = array();
        $data['id'] = $ims->post['id'];
        $dir_view = $ims->func->dirModules('event', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."create.tpl");

        $ims->site_func->checkUserLogin();
        $ims->func->load_language('event');
        $ims->temp_act->assign('LANG', $ims->lang);

        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("form_promotion");
        $out['html'] = $ims->temp_act->text("form_promotion");

        return json_encode($out);    
    }
    function do_createPromo(){
        global $ims;

        $dir_view = $ims->func->dirModules('event', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."create.tpl");

        $ims->func->load_language('event');
        $ims->site_func->checkUserLogin();

        $out = array(
            'ok' => 0,
            'mess' => '',
            'num' => 0,
        );
        $input_tmp = $ims->func->unserialize_array ($ims->func->if_isset($ims->post['data'], array()));
        $arr_info = array();
        $arr_info['id'] = $ims->db->getAutoIncrement('promotion');

        if($input_tmp['hand_code'] == ''){
            $arr_info['promotion_id'] = $ims->func->random_str(6);
        }
        $arr_info['type_promotion'] = $input_tmp['type_code'];
        $arr_info['value_type'] = $input_tmp['value_type'];
        $arr_info['list_product'] = $input_tmp['id'];

        if($input_tmp['max_use']){
            $arr_info['max_use'] = $input_tmp['max_use'];
        }
        if($input_tmp['value_type'] == 0){
            $arr_info['value'] = $input_tmp['price0'];
        }elseif($input_tmp['value_type'] == 1){            
            $arr_info['value'] = $input_tmp['price1'];
        }
        if($input_tmp['date_begin']){
            $arr_info['date_start'] = strtotime($input_tmp['date_begin']);
        }
        if($input_tmp['date_end']){
            $arr_info['date_end'] = strtotime($input_tmp['date_end']);
        }
        if($input_tmp['list_email']){
            $list_mail = explode(',',$input_tmp['list_email']);
            $mail = array();
            foreach ($list_mail as $key => $value) {
                $mail[] = trim($value);
            }
            $arr_info['list_email'] = implode(',',$mail);
        }
        $arr_info['date_create']  = time();
        $arr_info['date_update']  = time();

        if($ims->site_func->checkUserLogin() == 1) {
            $ok = $ims->db->do_insert("promotion", $arr_info);
            if($ok){
                $num = Session::Get('num_promo',0);
                if ($num >= 0) {
                    $num += 1;
                }
                $promo = $ims->db->load_row_arr('promotion', 'is_show = 1 and find_in_set("'.$input_tmp['id'].'" ,list_product)');
                if($promo){
                    $num += count($promo);
                }

                $number = Session::Set('num_promo',$num);
                $out['num'] = 'Bạn đã tạo <span>'.$number.'</span> mã giảm giá';
                $out['ok'] = 1;

                $event = $ims->db->load_row('event', ' item_id = "'.$input_tmp['id'].'" ');
                $arr_in['full_name'] = $ims->data['user_cur']['full_name'];
                $arr_in['code'] = $event['title'];
                $arr_in['event'] = $arr_info['promotion_id'];

                if($mail){
                    //Send email
                    $mail_arr_value = $arr_in;
                    $mail_arr_value['date_create'] = $ims->func->get_date_format($mail_arr_value["date_create"]);
                    $mail_arr_value['domain'] = $_SERVER['HTTP_HOST'];
                    $mail_arr_key = array();
                    foreach($mail_arr_value as $k => $v) {
                        $mail_arr_key[$k] = '{'.$k.'}';
                    }                
                    // send to admin
                    // $ims->func->send_mail_temp ('admin-contact', $ims->conf['email'], $ims->conf['email'], $mail_arr_key, $mail_arr_value);
                    foreach ($mail as $key => $value) {
                        //send to contact
                        $ims->func->send_mail_temp ('send-code', $value, $ims->conf['email'], $mail_arr_key, $mail_arr_value);
                    }
                    //End Send email
                }
            }else{
                $out['mess'] = $ims->lang['event']['input_text_info'];
            }
        }else{
            $out['mess'] = $ims->lang['event']['need_login'];
        }

        return json_encode($out);    
    }
    function do_updateStatus(){
        global $ims;

        $dir_view = $ims->func->dirModules('event', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."create.tpl");

        $ims->func->load_language('event');
        $ims->site_func->setting('event');

        $out = array(
            'ok' => 0,
            'mess' => '',
        );

        if($ims->site_func->checkUserLogin() == 1) {
            $input_tmp = $ims->func->unserialize_array ($ims->func->if_isset($ims->post['data'], array()));
            $arr_info = array();

            $arr_info['is_expected'] = $input_tmp['is_expected'];
            if($input_tmp['day_expected'] == 1){
                $arr_info['is_show'] = 1;
                $arr_info['day_expected'] = 0;
            }elseif($input_tmp['day_expected'] == 0){
                $date_begin = str_replace('/', '-', $input_tmp['day_event_start']);
                $date_begin = date('Y-m-d', strtotime($date_begin));
                $date_begin = strtotime($date_begin.' '.$input_tmp['time_event_start']);
                $arr_info['day_expected'] = $date_begin;

                $arr_info['is_show'] = -2;
            }
            $ok = $ims->db->do_update("event", $arr_info, ' item_id = "'.$ims->func->base64_decode($input_tmp['id_edit']).'" and user_id = '.$ims->data['user_cur']['user_id']);
            if($ok){
                $out['ok'] = 1;
                $out['link'] = $ims->site_func->get_link('');
            }else{
                $out['mess'] = $ims->lang['event']['input_text_info'];
            }
        }else{
            $out['mess'] = $ims->lang['event']['need_login'];
        }
        return json_encode($out);    
    }
    function do_load_preview(){
        global $ims;

        $ims->site_func->setting('event');
        include_once($ims->conf["rootpath"].DS."config/site.php");        
        $ims->site = new Site($this);

        $dir_view = $ims->func->dirModules('event', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."create.tpl");

        $ims->temp_html = new XTemplate($ims->path_html."html.tpl");

        $ims->func->load_language('event');
        $ims->temp_act->assign('LANG', $ims->lang);
        $out = array(
            'ok' => 0,
        );

        if($ims->site_func->checkUserLogin() == 1){
            $data = $ims->db->load_row('event', ' item_id = "'.$ims->func->base64_decode($ims->post['id']).'" and user_id = '.$ims->data['user_cur']['user_id']);

            $qr = 'is_show = 1 and lang = "'.$ims->post['lang_cur'].'"';
            if($data){
                $out['ok'] = 1;
            }
            $favorite = $ims->site->check_favorite($data['item_id'], 'event');
            if (!empty($favorite)) {
                $data['i_favorite'] = $ims->func->if_isset($favorite["class"]);
                $data['added'] = $ims->func->if_isset($favorite["added"]);
            }
            $data['background'] = ($data['picture'] != '') ? $ims->func->get_src_mod($data['picture'], 1366, 768, 1, 1) : $ims->conf['rooturl'].'resources/images/bg_detail.png';
            $data['pic_zoom'] = $ims->func->get_src_mod($data['picture']);
            $data['picture_form'] = $ims->func->get_src_mod($data['picture'], 540, 250, 1, 1);
            $data['picture'] = $ims->func->get_src_mod($data['picture'], 720, 360);
            $data['title'] = $ims->func->input_editor_decode($data['title']);
            $data['title1'] = ($data['title1'] != '') ? $data['title1'].':&nbsp' : '';
            $data['e_title'] = strip_tags($data['title']);
            $data['content'] = $ims->func->input_editor_decode($data['content']);
            $data['event_same_organization'] = $this->do_same_organization($data, $qr);
//            $data['event_other'] = $this->do_event_other($data);
//            if($data['event_other'] != '' || $data['event_same_organization'] != ''){
//                $data['border'] = 'borders';
//            }
//            $data['event_product'] = $this->do_event_product($data['item_id']);
            $data['link_share'] = $ims->site_func->get_link('event', $data['friendly_link']);
            if($data['organizer'] != ''){
                $data['organizational'] = '<div class="organizational">'.$ims->lang['event']['organizational'].': <span>'.$data['organizer'].'</span></div>';
            }

            $user_info = $ims->db->load_row('user', 'user_id = '.$data['user_id'], 'num_follow');
            if($user_info){
                $follow = $ims->site->check_follow($data['item_id']);
                $num_follow = number_format($user_info['num_follow'],0,',','.');

                $btn_follow = '';
                if(!isset($follow['none_follow']) || (isset($follow['none_follow']) && $follow['none_follow'] != 'none')){
                    $btn_follow = '<p class="btn_follow '.$follow['none_follow'].'"><button data-item="'.$ims->func->base64_encode($data['item_id']).'" class="'.$follow['class_follow'].'">'.$follow['text_follow'].'</button></p>';
                }
                $data['follow'] = '<div class="follow">
                                    <p class="num">'.$ims->lang['event']['num_follow'].': <span>'.$num_follow.'</span></p>
                                    '.$btn_follow.'
                                </div>';
            }

            $date_begin = $data['date_begin'];
            $data['date_begin'] = $ims->lang['global']['day_'.date('N', $data['date_begin'])].date(', d/m, h:i A', $data['date_begin']);
            if(date('d', $date_begin) == date('d',$data['date_end']) && date('m', $date_begin) == date('m',$data['date_end'])){
                $data['time'] = $data['date_begin'].' - '.date('h:i A', $data['date_end']);
            }else{
                $data['time'] = $data['date_begin'].' - '.$ims->lang['global']['day_'.date('N', $data['date_end'])].date(', d/m, h:i A', $data['date_end']);
            }
            if($data['date_end'] < time()){
                $data['register_text'] = $ims->lang['event']['ended_event'];
                $data['register_disable'] = 'disabled';
            }else{
                $data['register_text'] = $ims->lang['event']['register'];
            }

            $arr_price = $ims->func->unserialize($data['arr_price']);
            $price = array();
            foreach ($arr_price as $row){
                $price[] = $row['price'];
            }
            if($price){
                sort($price);
                $data['price'] = number_format($price[0],0,',','.').' '.$ims->lang['global']['unit'];
                if(count($price) > 1){
                    $data['price'] .= ' - '.number_format($price[count($price) - 1],0,',','.').' '.$ims->lang['global']['unit'];
                }
            }
            if($data['type_event'] == 'offline'){
                $data['maps'] = '<div class="maps">'.$ims->func->input_editor_decode($data['frame_maps']).'</div>';
                $data['link_event_maps'] = '<div class="see_maps"><a href="#address" class="goto">'.$ims->lang['event']['see_maps'].'</a></div>';
                $data['location'] = $ims->lang['event']['location'];
            }else{
                $data['address'] = '';
                $data['link_event_maps'] = '<div class="see_maps"><a href="'.$data['link_event'].'" target="_blank">'.$data['link_event'].'</a></div>';
                $data['link_event_text'] = '<div class="see_maps"><span>'.$ims->lang['event']['link_event'].':</span> <a href="'.$data['link_event'].'" target="_blank">'.$data['link_event'].'</a></div>';
                $data['location'] = $ims->lang['event']['link_event'];
            }
            $data['item_id'] = $ims->func->base64_encode($data['item_id']);

            if(isset($ims->data['user_cur']['user_id']) && $data['user_id'] == $ims->data['user_cur']['user_id']){
                $data['register_disable'] = 'disabled';
            }
            $data['link_edit'] = $ims->site_func->get_link('event', $ims->setting['event']['create_link']).'?edit='.$data['item_id'];

            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("detail");
            $out['html'] = $ims->temp_act->text("detail");
        }

        return json_encode($out);
    }
    function do_same_organization($data, $qr){
        global $ims;

        $arr_in = array(
            'where' => ' and item_id != '.$data['item_id'].' and user_id = '.$data['user_id'],
            'paginate' => 0,
            'num_list' => $ims->setting['event']['num_order_detail']
        );
        $check = $ims->db->load_item('event', $qr.$arr_in['where'], 'item_id');
        if ($check){
            $content = $this->modFunc->html_list_item($arr_in);
            return '<div class="other same_organization"><div class="other_title">'.$ims->lang['event']['same_organization'].'</div>'.$content.'</div>';
        }else{
            return '';
        }
    }
    function do_event_other($data, $qr){
        global $ims;

        $arr_in = array(
            'where' => ' and item_id != '.$data['item_id'].' and (find_in_set('.$data['group_id'].', group_nav) or find_in_set('.$data['group_id'].', group_related))',
            'paginate' => 0,
            'num_list' => $ims->setting['event']['num_order_detail']
        );
        $check = $ims->db->load_item('event', $qr.$arr_in['where'], 'item_id');
        if ($check){
            $content = $this->modFunc->html_list_item($arr_in);
            return '<div class="other event_other"><div class="other_title">'.$ims->lang['event']['other_event'].'</div>'.$content.'</div>';
        }else{
            return '';
        }
    }
    function do_event_product($list_prd_store, $item_id){
        global $ims;

        if($list_prd_store){
            $where = ' and item_id IN ('.$list_prd_store.') order by show_order desc, date_create desc';
            $result = $ims->db->load_item_arr('event_product', $ims->conf['qr'].$where.' limit '.$ims->setting['event']['num_list_store'], 'item_id, title1, title, price, picture');
            $total = $ims->db->do_count('event_product', $ims->conf['qr'].$where, 'item_id');
            $show_more = '';
            if($total > count($result)){
                $show_more = '<div class="show_more"><input type="hidden" name="start" value="'.count($result).'" data-it="'.$item_id.'"><button>'.$ims->lang['event']['load_more'].'<i></i></button></div>';
            }
            if($result){
                foreach ($result as $row){
                    $row['picture'] = $ims->func->get_src_mod($row['picture'], 210, 165, 1, 1);
                    $row['price'] = $ims->lang['event']['price_buy'].': '.number_format($row['price'], 0, ',', '.').' vnđ';
                    $row['title1'] = ($row['title1'] != '') ? $row['title1'].': ' : '';
                    $row['item_id'] = $ims->func->base64_encode($row['item_id']);
                    $ims->temp_act->assign('row', $row);
                    $ims->temp_act->parse("event_product.item_product");
                }
                $ims->temp_act->assign('show_more', $show_more);
                $ims->temp_act->parse("event_product");
                return $ims->temp_act->text("event_product");
            }
        }
    }

    function upload_file($folder_upload, $file_upload){
        global $ims;
        $output = array(
            'ok'    => 1,
            'mess'  => ''
        );

        $target_dir =  $ims->conf['rooturl_web'].'uploads/'.$folder_upload.'/';
        $target_file = $target_dir . basename($file_upload["name"]);
        $FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        if (file_exists($target_file)) {
            $output['mess'] = $ims->lang['global']['file_exist'];
            $output['ok'] = 0;
            return $output;
        }
        if ($file_upload["size"] == 0){
            $output['mess'] = $ims->lang['global']['file_error'];
            $output['ok'] = 0;
            return $output;
        }
        if ($file_upload["size"] > 4000000) {
            $output['mess'] = $ims->lang['global']['file_size'];
            $output['ok'] = 0;
            return $output;
        }
        $arr_format = ["png","jpg","jpeg","JPG","PNG","JPEG"];
        if(!in_array($FileType, $arr_format)) {
            $output['mess'] = $ims->lang['global']['file_type'];
            $output['ok'] = 0;
            return $output;
        }
        $ims->func->rmkdir($folder_upload);
        $file_upload["name"] = str_replace(' ', '_', $file_upload["name"]);
        $file_name = time().'_'.$file_upload["name"];
        move_uploaded_file($file_upload["tmp_name"], $ims->conf['rootpath'].'uploads/'.$folder_upload.'/'.$file_name);
        $output['link']  = $folder_upload.'/'.$file_name;
        return $output;
    }
    // end class
}
?>