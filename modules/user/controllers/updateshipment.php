<?php
if (! defined('IN_ims')) {
  die('Access denied');
}
$nts = new sMain();

class sMain
{
    var $modules = "user";
    var $action = "updateshipment";
    var $sub = "manage";
    /**
        * function __construct ()
        * Khoi tao 
    **/
    function __construct ()
    {
        global $ims;
        $token = isset($ims->get['token']) ? $ims->get['token'] : '';
        $label_id = isset($ims->input['label_id']) ? $ims->input['label_id'] : '';
        $status_id = isset($ims->input['status_id']) ? $ims->input['status_id'] : '';
        $action_time = isset($ims->input['action_time']) ? $ims->input['action_time'] : '';
        $reason_code = isset($ims->input['reason_code']) ? $ims->input['reason_code'] : '';
        $reason = isset($ims->input['reason']) ? $ims->input['reason'] : '';
        if ($token == 'mYiOjE1MzMxMTU1NDAsImV4cCI6MTUzMzIwMTkk') {
            if ($label_id != '') {
                $order = $ims->db->load_row('product_order', 'api_ghtk_id="'.$label_id.'" ');
                if (!empty($order)) {
                    $arr_up = array();
                    $arr_up['is_status'] = $status_id;
                    $arr_up['is_status_ghtk'] = $status_id;
                    $arr_up['api_ghtk_action_time'] = $action_time;
                    $arr_up['api_ghtk_reason_code'] = $reason_code;
                    $arr_up['api_ghtk_reason'] = $reason;
                    $arr_up['date_update'] = time();
                    $ok = $ims->db->do_update('product_order', $arr_up, 'order_id="'.$order['order_id'].'" ');
                    if ($ok) {
                        // Cộng tiền hoa hồng 
                        if ($arr_up['is_status_ghtk'] == 6 && $order['is_charged'] == 0) {
                            // Trích Xuất / hoàn trả điểm
                            $ims->site_func->setting('product');
                            $ims->site_func->setting('user');
                            $setting_product = $ims->setting('product');
                            $setting_user = $ims->setting('user');
                            if(isset($setting_product['percentforwcoin']) && $setting_product['percentforwcoin'] > 0 && $setting_product['percentforwcoin'] < 100) {
                                $orderInfo = $order;
                                if(count($orderInfo)) {
                                    $arr_exchange_type = array();
                                    if ($orderInfo['user_contributor'] > 0 && $orderInfo['amount_contributor'] > 0) {
                                        $arr_exchange_type[] = 'up_contributor'; //plus wcoin when not presenter
                                    }
                                    if($orderInfo['is_status'] == 0) {
                                        $arr_exchange_type[] = 'oreturn_wcoin'; 
                                    }elseif($orderInfo['is_status'] == 10 
                                        && $orderInfo['user_id'] > 0 
                                        && $orderInfo['wcoin_accumulation'] > 0) {
                                        $arr_exchange_type[] = 'ouser_wcoin'; 
                                    }
                                    foreach ($arr_exchange_type as $key => $exchange_type) {

                                        // Kiểm tra log đã có chưa
                                        $userELInfo = $ims->db->row(array(
                                            'from' => 'user_exchange_log',
                                            'where' => array(
                                                array('=', 'exchange_type', "'".$exchange_type."'"),
                                                array('=', 'dbtable', "'product_order'"),
                                                array('=', 'dbtable_id', "'".$order_id."'")
                                            ),
                                            'limit' => array(1)
                                        ));
                                        if(!$userELInfo && 
                                                (
                                                    $orderInfo['user_contributor'] || 
                                                    ($orderInfo['payment_wcoin'] && $orderInfo['user_id']) || 
                                                    ($orderInfo['wcoin_accumulation'] && $orderInfo['user_id'])  
                                                )
                                            ){
                                            $userInfo_where = array();
                                            switch ($exchange_type) {
                                                case 'up_contributor':
                                                    $userInfo_where[] = array('=', 'user_code', "'".$orderInfo['user_contributor']."'");
                                                    break;
                                                case 'ouser_wcoin':
                                                    $userInfo_where[] = array('=', 'user_id', "'".$orderInfo['user_id']."'");
                                                    break;
                                                case 'oreturn_wcoin':
                                                    $userInfo_where[] = array('=', 'user_id', "'".$orderInfo['user_id']."'");
                                                    break;
                                            }
                                            $userInfo = array();
                                            if(count($userInfo_where)) {
                                               $userInfo = $ims->db->row(array(
                                                    'from' => 'user',
                                                    'where' => $userInfo_where,
                                                    'limit' => array(1)
                                                )); 
                                            }                                
                                            if(count($userInfo)) {                                    
                                                $wcoin_dayexpired = isset($setting_user['wcoin_dayexpired']) && $setting_user['wcoin_dayexpired']>0 ? $setting_user['wcoin_dayexpired'] : 180;
                                                $money2wcoin = isset($setting_user['money_to_wcoin']) && $setting_user['money_to_wcoin']>0 ? $setting_user['money_to_wcoin'] : 1;
                                                $value_wcoin = 0;
                                                $wcoin_expires = (isset($userInfo['wcoin_expires']) && $userInfo['wcoin_expires']) ? $userInfo['wcoin_expires'] : 0;
                                                $exchange_log_note = '';
                                                switch ($exchange_type) {
                                                    case 'up_contributor':
                                                        $exchange_log_note = 'Nhận được điểm hoa hồng từ đơn hàng '.$orderInfo['order_code'];
                                                        $value_wcoin = $orderInfo['wcoin_contributor'];
                                                        $wcoin_expires = time()+$wcoin_dayexpired;
                                                        break;
                                                    case 'ouser_wcoin':
                                                        $exchange_log_note = 'Nhận được điểm trích xuất từ đơn hàng '.$orderInfo['order_code'];
                                                        $value_wcoin = $orderInfo['wcoin_accumulation'];
                                                        $wcoin_expires = time()+$wcoin_dayexpired;
                                                        break;
                                                    case 'oreturn_wcoin':                                            
                                                        $value_wcoin = $orderInfo['payment_wcoin'];
                                                        $exchange_log_note = 'Hoàn trả điểm từ đơn hàng '.$orderInfo['order_code'];
                                                        break;
                                                }
                                                $wcoin_after = $userInfo['wcoin']+$value_wcoin;
                                                $ims->db->do_insert("user_exchange_log", array(
                                                    'exchange_type' => $exchange_type,
                                                    'dbtable' => 'product_order',
                                                    'dbtable_id' => $orderInfo['order_code'],
                                                    'value_type' => 1,
                                                    'value' => $value_wcoin,
                                                    'wcoin_before' => $userInfo['wcoin'],
                                                    'wcoin_after' => $wcoin_after,
                                                    'total_amount' => $orderInfo['total_payment'],
                                                    'note' => $exchange_log_note,
                                                    'date_create' => time(),
                                                    'user_code' => $userInfo['user_code'],
                                                    'user_id' => $userInfo['user_id']
                                                ));
                                                $ims->db->query("UPDATE user SET wcoin=(wcoin+".$value_wcoin.")," ."wcoin_expires=(".$wcoin_expires.") WHERE user_id='".$userInfo['user_id'] . "'");
                                            }                                
                                        }
                                    }
                                }
                            }    
                        }

                        // email tai khoan nguoi dat hang
                        $user_mail =$ims->db->load_item('user','user_id ='.$order['user_id'].' and is_show=1','email');
                        $user_mail = '';
                        if ($user_mail !=''){
                            if ($arr_up['is_status_ghtk'] == 6) {
                                $ims->db->query('UPDATE deeplink_offer SET is_show=1 WHERE order_code="'.$order['order_code'].'" ');
                            }
                            // Delay lấy hàng
                            if ($arr_up['is_status_ghtk'] == 8) {
                                // Send email
                                $mail_arr_value = $order;
                                $mail_arr_key = array();
                                foreach($mail_arr_value as $k => $v) {
                                    $mail_arr_key[$k] = '{'.$k.'}';
                                }
                                $ims->func->send_mail_temp ('delay-take', $user_mail, $ims->conf['email'], $mail_arr_key, $mail_arr_value);
                            }
                            // Delay giao hàng
                            if ($arr_up['is_status_ghtk'] == 10) {
                                // Send email
                                $mail_arr_value = $order;
                                $mail_arr_key = array();
                                foreach($mail_arr_value as $k => $v) {
                                    $mail_arr_key[$k] = '{'.$k.'}';
                                }
                                $ims->func->send_mail_temp ('delay-receipt', $user_mail, $ims->conf['email'], $mail_arr_key, $mail_arr_value);
                            }
                            // gui mail kho status = 410, 49, 10, 9
                            if ($arr_up['is_status_ghtk'] == 410 || $arr_up['is_status_ghtk'] == 49 || $arr_up['is_status_ghtk'] == 10 || $arr_up['is_status_ghtk'] == 9) {
                                // Send email
                                $mail_arr_value = $order;
                                $mail_arr_key = array();
                                foreach($mail_arr_value as $k => $v) {
                                    $mail_arr_key[$k] = '{'.$k.'}';
                                }
                                $ims->func->send_mail_temp ('ordering-delay', $user_mail, $ims->conf['email'], $mail_arr_key, $mail_arr_value);
                            }
                            // Gửi đơn hàng khi đã lấy hàng, đã nhập kho
                            if ($arr_up['is_status_ghtk'] == 3) {
                                $arr_pro = array();
                                $order_info = $order;
                                $arr_cart = array();
                                $sql_cart = "SELECT * 
                                            FROM product_order_detail 
                                            WHERE order_id='".$order_info['order_id']."' ";
                                $result_cart = $ims->db->query($sql_cart);
                                while ($row_cart = $ims->db->fetch_row($result_cart)) {
                                    $arr_cart[$row_cart['detail_id']] = $row_cart;
                                }
                                $mail_arr_key = array(
                                    '{order_code}',
                                    '{list_cart}',
                                    '{o_full_name}',
                                    '{o_email}',
                                    '{o_phone}',
                                    '{o_address}',
                                );
                                $mail_arr_value = array(
                                    $order_info["order_code"],
                                    $this->do_cart ($order_info, $arr_cart, $arr_pro),
                                    $order_info["o_full_name"],
                                    $order_info["o_email"],
                                    $order_info["o_phone"],
                                    $ims->func->full_address($order_info, 'o_'),
                                );
                                $ims->func->send_mail_temp ('loading-warehousing', $order_info["o_email"], $ims->conf['smtp_username'], $mail_arr_key, $mail_arr_value);
                            }
                        }
                        $this->response(200, 'Ok');
                    }
                }
            }
        }
        $this->response(500, 'Error');
        return;
    }
    function do_cart ($order = array(), $arr_cart = array(), $arr_pro = array())
    {
        global $ims;    

        include_once($ims->conf["rootpath"].DS."inc".DS."xtemplate.class.php");
        $ims->temp_box = new XTemplate($ims->conf["rootpath"]."temp/default/html/global/box.tpl");

        $arr_color = $ims->load_data->data_table ('product_color', 'color_id', 'color_id,color,title', "    lang='".$ims->conf['lang_cur']."' order by show_order desc, date_create desc");
        $arr_size = $ims->load_data->data_table ('product_size', 'size_id', 'size_id,title', "  lang='".$ims->conf['lang_cur']."' order by show_order desc, date_create desc");

        $data = $order;
        $data['cart_total'] = 0;
        if(is_array($arr_cart) && count($arr_cart > 0)){
            foreach($arr_cart as $cart_id => $row) {
                $row_pro =  $ims->db->load_row('product', 'item_id="'.$row['type_id'].'" and lang="'.$ims->conf['lang_cur'].'" '); 
                $row['cart_id'] = $cart_id;
                $row['pic_w'] = 50;
                $row['pic_h'] = 50;
                $row['picture'] = (isset($row_pro['picture'])) ? $row_pro['picture'] : '';
                $row["picture"] = $ims->func->get_src_mod($row["picture"], $row['pic_w'], $row['pic_h'], 1, 0, array('fix_max' => 1));
                $row['price_buy'] = (isset($row_pro['price_sale'])) ? $row_pro['price_sale'] : 0;
                $row['title'] = (isset($row_pro['title'])) ? $row_pro['title'] : '';
                $row['quantity'] = (isset($row['quantity'])) ? $row['quantity'] : 0;

                $row['total'] = $row['quantity'] * $row['price_buy'];
                $data['cart_total'] += $row['total'];
                $row['color'] = (isset($row['color_id']) && array_key_exists($row['color_id'], $arr_color)) ? $row['color_id'] : 0;
                $color = (isset($arr_color[$row['color']]['color'])) ? '<div><span class="color" style="background:'.$arr_color[$row['color']]['color'].'; display:inline-block; width:100px; height:20px;border: 1px #ccc solid;">&nbsp;</span></div>' : '';
                $row['color'] = (isset($arr_color[$row['color']]['title'])) ? $color.$arr_color[$row['color']]['title'] : '';
                $row['size'] = (isset($row['size']) && array_key_exists($row['size'], $arr_size)) ? $row['size'] : 0;
                $row['size'] = (isset($arr_size[$row['size']]['title'])) ? $arr_size[$row['size']]['title'] : '';
                $row['price_buy'] = $ims->func->get_price_format_email($row['price_buy']);
                $row['total'] = $ims->func->get_price_format_email($row['total']);
                $row['cart_td_attr'] = ' style="background:#ffffff;"';
                $ims->temp_box->assign('row', $row);
                $ims->temp_box->parse("table_cart_ordering_method_mail.row_item");
            }
        } else {
            $ims->temp_box->assign('row', array('mess' => $ims->lang['product']['no_have_item']));
            $ims->temp_box->parse("table_cart_ordering_method_mail.row_empty");
        }
        
        $promotion_percent = $order['promotion_percent'];
        $promotion_price = $order['promotion_price'];
        $shipping_price = $order['shipping_price'];
        $voucher_amount_has = $order['voucher_amount'];
        
        // cart_payment
        $data['cart_payment'] = $data['cart_total'];
        if($promotion_percent > 0 && $promotion_percent < 100) {
            //$data['cart_payment'] = (100-$promotion_percent)/100 * $data['cart_payment'];                    
            $data['cart_payment'] -= $promotion_price;
        } //End
        
        // Shipping_price
        if($shipping_price > 0) {
            $data['cart_payment'] += $shipping_price;
        } //End
        
        if($order['method_price'] > 0 || $order['method_price'] < 0) {
            $data['cart_payment'] += $order['method_price'];
        }
        
        $data['cart_total'] = $ims->func->get_price_format_email($data['cart_total'], 0);
        $data['promotion_percent'] = $promotion_percent;
        $data['promotion_price'] = $promotion_price;
        if($shipping_price == -1){
            $data['shipping_price_out'] = 'Miễn phí';
        }
        else{
            $data['shipping_price_out'] = $ims->func->get_price_format_email($shipping_price, 0);
        }
        // $data['link_action'] = $ims->site->get_link ('product','',$ims->setting['product']['ordering_cart_link']);
        // $data['link_ordering_address'] = $ims->site->get_link ('product','',$ims->setting['product']['ordering_address_link']);
        
        //style 
        $data['cart_table_attr'] = ' style="background:#dbdbdb;"';
        $data['cart_th_attr'] = ' style="font-weight:bold; background:#efefef; text-align:center; color:#800080;"';
        $data['cart_total_attr'] = ' style="background:#ffffff;"';
        // end

        if($order['method_price'] > 0 || $order['method_price'] < 0) {
            if($order['method_price'] > 0){
                $data['save_method'] = ' +'. $ims->func->get_price_format_email($order['method_price']);
            }elseif($order['method_price'] < 0){
                $data['save_method'] = $ims->func->get_price_format_email($order['method_price']);
            }
            $ims->temp_box->assign('data', $data);
            $ims->temp_box->parse("table_cart_ordering_method_mail.save_method");
        }
                
        $ims->temp_box->assign('data', $data);
        if(isset($promotion_price) && $promotion_price != 0 && $promotion_price > 0){
            $data['promotion_percent'] = $promotion_percent.'%';
            $data['promotion_price']  = $promotion_price;
            $data['promotion_price_out'] = '-'.$ims->func->get_price_format_email($promotion_price, 0);
            $ims->temp_box->assign('data', $data);
            $ims->temp_box->parse("table_cart_ordering_method_mail.row_promotion_price");
        }
        $wcoin_use = $order['payment_wcoin'];
        // $money_use_wcoin = $wcoin_use * $ims->setting['user']['wcoin_to_money'];
        // $data['wcoin_price_out'] = $ims->func->get_price_format_email($money_use_wcoin, 0);
        // $ims->temp_box->assign('data', $data);
        // $ims->temp_box->parse("table_cart_ordering_method_mail.wcoin_box_show");
        // $data['cart_payment'] -= $money_use_wcoin;
        // $data['wcoin_expected'] = round($data['cart_payment']*$ims->setting[$this->modules]['percentforwcoin'] / $ims->setting['user']['money_to_wcoin']);
        $data['cart_payment'] = $ims->func->get_price_format_email($data['cart_payment'], 0);
        $data['num_product'] = count($arr_cart);
        $ims->temp_box->assign('data', $data);
        $ims->temp_box->parse("table_cart_ordering_method_mail");
        return $ims->temp_box->text("table_cart_ordering_method_mail");
    }
    function response($status_code, $data = NULL){
        header($this->_build_http_header_string($status_code));
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Content-Type: application/json");
        echo json_encode($data);
        die();
    }
    function _build_http_header_string($status_code){
        $status = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );
        return "HTTP/1.1 " . $status_code . " " . $status[$status_code];
    } 
}
?>