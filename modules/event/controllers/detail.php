<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain {

    var $modules = "event";
    var $action  = "detail";

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

        require_once($this->modules . "_func.php");
        $this->modFunc = new eventFunc($this);

        if(isset($ims->get['dl']) && $ims->get['dl']!=''){
            $code  = preg_replace('/[^A-Za-z0-9\. -]/','', $ims->get['dl']);
            $check = $ims->db->load_row("user_deeplink", "short_code='".$code."' AND is_show=1");
            if ($check['id'] > 0){
                setcookie('deeplinkv2', $check['id'], time()+(86400 * 30));
                $num_view = $check['num_view'] + 1;
                $ims->db->do_update('user_deeplink', array('num_view' => $num_view), " short_code='".$code."' ");
            }
        }

        if (isset($ims->conf['cur_item'])) {
            //Make link lang
            $load_lang = $ims->db->load_item_arr("event", " item_id='".$ims->conf['cur_item']."' ", "friendly_link, lang");
            foreach ($load_lang as $key => $row_lang) {
                $ims->data['link_lang'][$row_lang['lang']] = $ims->site_func->get_link_lang($row_lang['lang'], $this->modules, '', $row_lang['friendly_link']);
            }
            //End Make link lang

            //SEO
//            $ims->site->get_seo($ims->data['cur_item']);
            $ims->conf["cur_group"]     = $ims->data['cur_item']["group_id"];
            $ims->conf["cur_group_nav"] = $ims->data['cur_item']["group_nav"];
            $ims->conf["meta_image"]    = $ims->func->get_src_mod($ims->data['cur_item']["picture"], 630, 420, 1, 1);

            //Current menu
            $arr_group_nav = (!empty($ims->conf["cur_group_nav"])) ? explode(',', $ims->conf["cur_group_nav"]) : array();
            foreach ($arr_group_nav as $v) {
                $ims->conf['menu_action'][] = $this->modules.'-group-'.$v;
            }
            $ims->conf['menu_action'][] = $this->modules.'-item-'.$ims->conf['cur_item'];
            //End current menu

            if(isset($ims->get['view_ticket']) && $ims->get['view_ticket'] == 1){
                $data['content'] = $this->do_view_ticket();
            }else{
                $data['content'] = $this->do_detail($ims->data['cur_item']);
            }
        } else {
            $ims->html->redirect_rel($ims->site_func->get_link($this->modules));
        }
        $temp = 'main';
        $ims->conf['class_full'] = 'detail';
        $ims->conf['container_layout'] = 'm';
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse($temp);
        $ims->output .= $ims->temp_act->text($temp);
    }

    function do_detail($data){
        global $ims;
        $ims->func->include_js($ims->dir_js.'html2canvas.js');

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
        $data['event_same_organization'] = $this->do_same_organization($data);
        $data['event_other'] = $this->do_event_other($data);
        if($data['event_other'] != '' || $data['event_same_organization'] != ''){
            $data['border'] = 'borders';
        }
        $data['event_product'] = $this->do_event_product($data['item_id']);
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

        // Thanh toán online
        $check_payment_online = 0;
        $type = '';
        if (isset($ims->get['vnp_TxnRef']) && isset($ims->get['vnp_SecureHash'])) {
            $check_payment_online = 1;
            $type = 'vnpay';
        }elseif (isset($ims->get['token']) && isset($ims->get['PayerID'])) {
            $check_payment_online = 1;
            $type = 'paypal';
        }
        if($check_payment_online == 1){
            $arr_info_booked = Session::Get('arr_info_booked', array());
            if(!empty($arr_info_booked)){
                $check_ok = $ims->db->load_item('event_order', 'order_id = '.$arr_info_booked['order_id'], 'is_check_payment_online');
                if($check_ok == 0){
                    $output_mess = $ims->site_func->paymentCustomComplete($type, $data['link_share'], 'event_order');
                    if($output_mess['status_payment'] == 'success'){
                        $ims->site->update_arr_price_event($arr_info_booked);

                        $event_ticket = $ims->site->create_image(); // Tạo vé sự kiện
                        $data['event_ticket'] = $event_ticket['content'];
                        $ims->func->include_js_content('imsEvent.upload_ticket("'.$event_ticket['arr_name'].'");');
                    }else{
                        Session::Delete('arr_info_booked');
                        $ims->func->include_js_content("
                            Swal.fire({
                                icon: 'error',
                                title: lang_js['aleft_title'],
                                text: '".$output_mess['notification_payment']."',
                            }).then((result) => {
                                go_link('".$data['link_share']."');
                            });
                        ");
                    }
                }else{
                    $ims->func->include_js_content('
                        $("#register").modal("show");
                        imsEvent.load_complete_order_event();
                        imsEvent.load_cart_info(3);
                    ');
                }
            }else{
                $ims->html->redirect_rel($data['link_share']);
            }
        }

        if(isset($ims->data['user_cur']['user_id']) && $data['user_id'] == $ims->data['user_cur']['user_id']){
            $data['register_disable'] = 'disabled';
        }

        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("detail");
        return $ims->temp_act->text("detail");
    }

    function do_same_organization($data){
        global $ims;

        $arr_in = array(
            'where' => ' and item_id != '.$data['item_id'].' and user_id = '.$data['user_id'],
            'paginate' => 0,
            'num_list' => $ims->setting['event']['num_order_detail']
        );
        $check = $ims->db->load_item('event', $ims->conf['qr'].$arr_in['where'], 'item_id');
        if ($check){
            $content = $this->modFunc->html_list_item($arr_in);
            return '<div class="other same_organization"><div class="other_title">'.$ims->lang['event']['same_organization'].'</div>'.$content.'</div>';
        }else{
            return '';
        }
    }

    function do_event_other($data){
        global $ims;

        $arr_in = array(
            'where' => ' and item_id != '.$data['item_id'].' and (find_in_set('.$data['group_id'].', group_nav) or find_in_set('.$data['group_id'].', group_related))',
            'paginate' => 0,
            'num_list' => $ims->setting['event']['num_order_detail']
        );
        $check = $ims->db->load_item('event', $ims->conf['qr'].$arr_in['where'], 'item_id');
        if ($check){
            $content = $this->modFunc->html_list_item($arr_in);
            return '<div class="other event_other"><div class="other_title">'.$ims->lang['event']['other_event'].'</div>'.$content.'</div>';
        }else{
            return '';
        }
    }

    function do_event_product($item_id){
        global $ims;

        if($item_id){
            $where = ' and find_in_set ('.$item_id.', event_id) order by show_order desc, date_create desc';
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
            }else{
                return '<div class="empty">'.$ims->lang['event']['no_have_event_product'].'</div>';
            }
        }
    }

    function do_view_ticket(){
        global $ims;
        $arr_info_booked = Session::Get('arr_info_booked', array());

        if($arr_info_booked){
            if($ims->site_func->checkUserLogin() == 1) {
                $user_order = $ims->db->load_item('event_order', 'order_id = '.$arr_info_booked['order_id'], 'user_id');
                if($user_order != $ims->data['user_cur']['user_id']){
                    Session::Delete('arr_info_booked');
                    $arr_info_booked = array();
                }
            }else{
                Session::Delete('arr_info_booked');
                $arr_info_booked = array();
            }
        }

        if($arr_info_booked){
            $data = array();
            $event = $ims->db->load_row('event', $ims->conf['qr'].' and item_id = '.$arr_info_booked['event_item']);
            $event['title1'] = ($event['title1'] != '') ? $event['title1'].': ' : '';
            $event['link'] = $ims->site_func->get_link('event', $event['friendly_link']);

            $province = $ims->db->load_item('location_province', 'lang = "'.$ims->conf['lang_cur'].'" and code = "'.$event['province'].'"', 'title');
            $date_begin = $event['date_begin'];
            $event['date_begin'] = $ims->lang['global']['day_'.date('N', $event['date_begin'])].date(', d/m, h:i A', $event['date_begin']);
            if(date('d', $date_begin) == date('d',$event['date_end']) && date('m', $date_begin) == date('m',$event['date_end'])){
                $event['event_info'] = $event['date_begin'].' - '.date('h:i A', $event['date_end']).', '.$province;
            }else{
                $event['event_info'] = $event['date_begin'].' - '.$ims->lang['global']['day_'.date('N', $event['date_end'])].date(', d/m, h:i A', $event['date_end']).', '.$province;
            }
            if(isset($ims->get['edit']) && $ims->get['edit'] == 1){
                $data['elm'] = 'form';
                $data['form_info'] = 'action="" method="post" id="edit_ticket"';
            }else{
                $data['elm'] = 'div';
            }
            $list_ticket = $ims->db->load_item_arr('event_order_detail', 'order_id = '.$arr_info_booked['order_id'].' order by detail_id asc', 'detail_id, full_name, phone, email, age, title');
            $i = 0;
            foreach ($list_ticket as $ticket){
                $i++;
                $ticket['disable'] = (isset($ims->get['edit']) && $ims->get['edit'] == 1) ? '' : 'disabled';
                $ticket['title'] = $ims->lang['event']['ticket'].' '.$i.' - '.$ticket['title'];
                $ticket['detail_id'] = $ims->func->base64_encode($ticket['detail_id']);
                if($i == 1){
                    $ticket['button_edit'] = '<a href="'.$event['link'].'/?view_ticket=1&edit=1"><img src="'.$ims->conf['rooturl'].'resources/images/use/edit.svg" />'.$ims->lang['event']['edit'].'</a>';
                }
                $ims->temp_act->assign('ticket', $ticket);
                $ims->temp_act->parse("view_ticket.item_ticket");
            }
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->assign('event', $event);
            if(isset($ims->get['edit']) && $ims->get['edit'] == 1){
                $ims->temp_act->parse("view_ticket.list_button");
            }
            $list_advisory = $ims->db->load_item_arr('advisory', $ims->conf['qr'].' order by show_order desc, date_create desc', 'title, friendly_link');
            if($list_advisory){
                foreach ($list_advisory as $row){
                    $row['link'] = $ims->site_func->get_link('advisory', $row['friendly_link']);
                    $ims->temp_act->assign('row', $row);
                    $ims->temp_act->parse("view_ticket.list_advisory.advisory");
                }
                $ims->temp_act->parse("view_ticket.list_advisory");
            }
            $check_payment = $ims->db->load_row('event_order', 'order_id = '.$arr_info_booked['order_id'], 'method, is_status_payment');
            if($check_payment['method'] == 3 || ($check_payment['method'] != 3 && !in_array($check_payment['is_status_payment'], array(3,5)))){
                $ims->temp_act->parse("view_ticket.cancel");
            }
            $ims->temp_act->parse("view_ticket");
            return $ims->temp_act->text("view_ticket");
        }else{
            $link = $ims->site_func->get_link('event', $ims->data['cur_item']['friendly_link']);
            $ims->html->redirect_rel($link);
        }
    }
}
?>