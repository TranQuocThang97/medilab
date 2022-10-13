<?php
if (!defined('IN_ims')) { die('Access denied'); }
function load_setting ()
{
    global $ims;

    $ims->site_func->setting('user');
    $ims->site_func->setting('product');
    return true;
}
load_setting ();
$nts = new sMain();

use \Firebase\JWT\JWT;


class sMain
{

    var $modules = "product";
    var $action  = "ajax";

    function __construct (){
        global $ims;

        $ims->func->load_language($this->modules);
        $fun = (isset($ims->post['f'])) ? $ims->post['f'] : '';

        switch ($fun) {
            // Thêm sp giỏ hàng
            case "addCart":
                echo $this->do_addCart ();
                exit;
                break;
            // Cập nhật sp trong giỏ hàng
            case "updateCart":
                echo $this->do_updateCart ();
                exit;
                break;
            // Lấy giỏ hàng
            case "getCart":
                echo $this->do_getCart ();
                exit;
                break;
            // Xóa sp trong giỏ hàng
            case "cartRemoveItem":
                echo $this->do_cartRemoveItem ();
                exit;
                break;
            // Sử dụng mã giảm giá
            case "promotionCode":
                echo $this->do_promotionCode ();
                exit;
                break;
            // Xóa mã giảm giá
            case "cartremovePromotionCode":
                echo $this->do_cartremovePromotionCode ();
                exit;
                break;
            // Tính phí ship
            case "shippingFee":
                echo $this->do_shippingFee ();
                exit;
                break;
            // Khởi tạo đơn hàng mới
            case "createOrder":
                echo $this->do_createOrder();
                exit;
                break;
            // Lấy phiên bản sản phẩm
            case "loadProductVersion":
                echo $this->do_loadProductVersion();
                exit;
                break;

            case "search_trademark":
                echo $this->do_search_trademark ();
                exit;
                break;
            case "load_all_trademark":
                echo $this->do_load_trademark ();
                exit;
                break;
            case "add_address":
                echo $this->do_add_address();
                exit;
                break;
            case "load_products_ajax":
                echo $this->do_load_products_ajax();
                exit;
                break;
            case "load_gift_combo":
                echo $this->load_gift_combo();
                exit;
                break;
            case "load_include_combo":
                echo $this->load_include_combo();
                exit;
                break;
            case "update_cart_combo":
                echo $this->update_cart_combo();
                exit;
                break;
            case "delete_gift_include":
                echo $this->delete_gift_include();
                exit;
                break;
            case "loadPromotionCode":
                echo $this->do_loadPromotionCode();
                exit;
                break;
            case "load_order_discount":
                echo $this->do_load_order_discount();
                exit;
                break;
            case "load_bundled_product":
                echo $this->do_load_bundled_product();
                exit;
                break;
            case "load_bundled_select":
                echo $this->do_load_bundled_select();
                exit;
                break;
            case "update_cart_bundled":
                echo $this->do_update_cart_bundled();
                exit;
                break;
            case "delete_bundled_product":
                echo $this->do_delete_bundled_product();
                exit;
                break;



            case "add_edit_concern":
                echo $this->do_add_edit_concern();
                exit;
                break;
            case "delete_concern":
                echo $this->do_delete_concern();
                exit;
                break;
            case "delete_list_concern":
                echo $this->do_delete_list_concern();
                exit;
                break;

            default:
                echo '';
                exit;
                break;
        }
        flush();
        exit;
    }

    function do_loadPromotionCode(){
        global $ims;
        $output = array(
            'ok' => 1,
            'html' => '',
        );
        $data = $ims->func->if_isset($ims->post['data']);
        if($data){
            include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
            $dir_view = $ims->func->dirModules('product', 'views', 'path');
            $ims->temp_func = new XTemplate($dir_view."func.tpl");
            $ims->func->load_language('product');
            $result = $ims->db->load_item_arr('promotion', 'is_show = 1 and find_in_set(promotion_id,"'.implode(',', $data).'")', 'promotion_id, type_promotion, picture, short, value_type, value, num_use, max_use, list_user, list_product, date_end');
            if($result){
                foreach ($result as $row){
                    $row['promotion_code'] = $ims->func->get_friendly_link($row['promotion_id']);
                    $row['short'] = $ims->func->input_editor_decode($row['short']);
                    if($row['type_promotion'] == 'apply_freeship'){
                        $row['title'] = $ims->lang['product']['free_ship'];
                    }else{
                        $row['title'] = $ims->lang['product']['decrease'].' '.(($row['value_type'] == 1) ? $row['value'].'%' : number_format($row['value'],0,',','.').'đ');
                    }
                    $row['pic'] = $ims->conf['rooturl'].'resources/images/promotion.jpg';
                    if(!empty($row['picture'])){
                        $row['pic'] = $ims->func->get_src_mod($row['picture'], 60, 60);
                    }
                    $row['date_end'] = $ims->lang['product']['hsd'].': '.date('d/m/Y', $row['date_end']);
                    $row['copy'] = $ims->lang['product']['apply'];
                    $ims->temp_func->assign('row', $row);
                    $ims->temp_func->parse('list_item_promotion_code.row_item');
                }
                $data['title'] = $ims->lang['product']['saved_promotion_code'];
                $ims->temp_func->assign('data',$data);
                $ims->temp_func->parse('list_item_promotion_code');
                $output['ok'] = 1;
                $output['html'] = $ims->temp_func->text('list_item_promotion_code');
            }
        }
        return json_encode($output);
    }

    function do_load_order_discount(){
        global $ims;
        $out = array(
            'ok' => 0,
            'html' => '',
        );

        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $dir_view = $ims->func->dirModules('product', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view."ordering_cart.tpl");

        $arr_cart = Session::Get('cart_pro', array());
        // Sử dụng giỏ hàng tạm
        if($ims->site_func->checkUserLogin() == 1) {
            $arr_cart = $ims->db->load_row_arr('product_order_temp','user_id="'.$ims->data['user_cur']['user_id'].'"');
        }

        $total_item_cart = 0;
        foreach ($arr_cart as $item){
            $total_item_cart += $item['quantity'];
        }
        $row = $ims->db->load_row('promotion', ' is_show = 2');
        if($row){
            $row['title'] = $ims->site_func->get_lang('order_discount_program_title', 'product', array('[percent]' => trim(str_replace('.00', '', $ims->setting['product']['percent_discount'])).'%'));
            if($total_item_cart < $ims->setting['product']['min_cart_item_discount']){
                $mess = $ims->site_func->get_lang('not_enough_num_product', 'product', array('[num]' => $ims->setting['product']['min_cart_item_discount']));
                $row['mess'] = '<div class="alert alert-warning alert-dismissable">'.$mess.'</div>';
                $row['disabled'] = 'disabled';
                $row['promotion_id'] = '';
            }else{
                $code_cur = Session::Get('promotion_code', '');
                if($code_cur == $row['promotion_id']){
                    $row['mess'] = '<div class="alert alert-success alert-dismissable">'.$ims->lang['product']['promotion_success'].'</div>';
                    $row['disabled'] = 'disabled';
                    $row['promotion_id'] = '';
                }
            }
            $ims->temp_act->assign('row', $row);
            $ims->temp_act->assign('LANG', $ims->lang);
            $ims->temp_act->parse("order_discount.content_order_discount");
            $out['html'] = $ims->temp_act->text("order_discount.content_order_discount");
            $out['ok'] = 1;
        }
        return json_encode($out);
    }

    function do_load_bundled_product(){
        global $ims;
        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $dir_view = $ims->func->dirModules('product', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view."ordering_cart.tpl");
        $ims->temp_act->assign("LANG", $ims->lang);
        $out = array(
            'ok' => 0,
            'html' => '',
        );

        if($ims->setting['product']['is_order_bundled'] == 1 && $ims->setting['product']['arr_product_bundled'] != ''){
            $arr_product_bundled = $ims->func->unserialize($ims->setting['product']['arr_product_bundled']);
            if($arr_product_bundled){
                foreach ($arr_product_bundled as $item){
                    $arr_product[$item['item_id']] = $ims->db->load_row('product', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id = '.$item['item_id'], 'item_id, title, picture, price_buy, friendly_link');
                    $arr_product[$item['item_id']]['endow_price'] = $item['price'];
                }

                if($arr_product){
                    $data = array();
                    $data['btn_apply'] = $ims->lang['product']['chose_bundled_product'];

                    $arr_cart = Session::Get('cart_pro', array());
                    // Sử dụng giỏ hàng tạm
                    if($ims->site_func->checkUserLogin() == 1) {
                        $arr_cart = $ims->db->load_row_arr('product_order_temp', 'user_id="'.$ims->data['user_cur']['user_id'].'"');
                    }

                    $total_item_cart = 0;
                    foreach ($arr_cart as $item){
                        $total_item_cart += $item['quantity'];
                    }
                    if($total_item_cart < $ims->setting['product']['min_cart_item_bundled']){
                        $mess = $ims->site_func->get_lang('not_enough_num_product', 'product', array('[num]' => $ims->setting['product']['min_cart_item_bundled']));
                        $data['mess'] = '<div class="alert alert-warning alert-dismissable">'.$mess.'</div>';
                        $data['disabled'] = 'disabled';
                        Session::Delete('bundled_selected');
                        if($ims->site_func->checkUserLogin() == 1) {
                            $col_tmp['bundled_product'] = '';
                            $col_tmp['date_update'] = time();
                            $ims->db->do_update('product_order_temp', $col_tmp, 'user_id="'.$ims->data['user_cur']['user_id'].'"');
                        }
                    }else{
                        // Sản phẩm mua kèm đã chọn
                        if($ims->site_func->checkUserLogin() == 1) {
                            $chosed = ($arr_cart[0]['bundled_product'] != '') ? $ims->func->unserialize($arr_cart[0]['bundled_product']) : array();
                        }else{
                            $chosed = Session::Get('bundled_selected', array());
                        }
                        // Sản phẩm mua kèm đã chọn

                        // Kiểm tra sản phẩm mua kèm đã chọn có còn nằm trong ds sp ưu đãi không
                        $list_bundled = array();
                        foreach ($arr_product_bundled as $prd){
                            $list_bundled[] = $prd['item_id'];
                        }
                        foreach ($chosed as $chose_item){
                            if(!in_array($chose_item['item_id'], $list_bundled)){
                                unset($chosed[$chose_item['item_id']]);
                            }
                        }
                        if($chosed){
                            $data['btn_apply'] = $ims->lang['product']['change_bundled_product'];
                            $chosed_item = array();
                            foreach ($chosed as $k => $v){
                                $chosed_item[] = $arr_product[$k];
                            }
                            if($chosed_item){
                                $endow_price = 0;
                                foreach ($chosed_item as $row){
                                    $endow_price += $row['endow_price'];
                                    $row['price_buy'] = ($row['price_buy'] > 0 && $row['price_buy'] > $row['endow_price']) ? '<div class="price_buy">'.number_format($row['price_buy'], 0, ',', '.').' vnđ</div>' : '';
                                    $row['endow_price'] = number_format($row['endow_price'], 0, ',', '.').' vnđ';
                                    $row['link'] = $ims->func->get_link($row['friendly_link'], '');
                                    $row['picture'] = $ims->func->get_src_mod($row['picture'], 80, 80, 1, 1);
                                    $ims->temp_act->assign("row", $row);
                                    $ims->temp_act->parse("bundled_product.content_bundled_product.list_item_chose.item");
                                }
                                $ims->temp_act->assign("endow_price", $endow_price);
                                $ims->temp_act->parse("bundled_product.content_bundled_product.list_item_chose");
                            }
                        }else{
                            Session::Delete('bundled_selected');
                            if($ims->site_func->checkUserLogin() == 1) {
                                $col_tmp['bundled_product'] = '';
                                $col_tmp['date_update'] = time();
                                $ims->db->do_update('product_order_temp', $col_tmp, 'user_id="'.$ims->data['user_cur']['user_id'].'"');
                            }
                        }
                    }
                    $ims->temp_act->assign("data", $data);
                    $ims->temp_act->parse("bundled_product.content_bundled_product");
                    $out['html'] = $ims->temp_act->text("bundled_product.content_bundled_product");
                    $out['ok'] = 1;
                }
            }
        }
        return json_encode($out);
    }

    function do_load_bundled_select(){
        global $ims;
        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $dir_view = $ims->func->dirModules('product', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view."ordering_cart.tpl");

        $out = array(
            'ok' => 0,
            'html' => '',
        );

        if($ims->setting['product']['arr_product_bundled'] != ''){
            $arr_product_bundled = $ims->func->unserialize($ims->setting['product']['arr_product_bundled']);
            if($arr_product_bundled){
                foreach ($arr_product_bundled as $item){
                    $arr_product[$item['item_id']] = $ims->db->load_row('product', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id = '.$item['item_id'], 'item_id, title, picture, price_buy, friendly_link');
                    $arr_product[$item['item_id']]['endow_price'] = $item['price'];
                }
                if($arr_product){
                    $data = array();

                    $arr_cart = Session::Get('cart_pro', array());
                    // Sử dụng giỏ hàng tạm
                    if($ims->site_func->checkUserLogin() == 1) {
                        $arr_cart = $ims->db->load_row_arr('product_order_temp','user_id="'.$ims->data['user_cur']['user_id'].'"');
                    }

                    $total_item_cart = 0;
                    foreach ($arr_cart as $item){
                        $total_item_cart += $item['quantity'];
                    }

                    foreach ($arr_product as $row){
                        $row['price_buy'] = ($row['price_buy'] > 0 && $row['price_buy'] > $row['endow_price']) ? number_format($row['price_buy'], 0, ',', '.').' vnđ' : '';
                        $row['endow_price'] = number_format($row['endow_price'], 0, ',', '.').' vnđ';
                        $row['link'] = $ims->func->get_link($row['friendly_link'], '');
                        $row['picture'] = $ims->func->get_src_mod($row['picture'], 80, 80, 1, 1);
                        $row['disabled'] = 'disabled';
                        $row['input'] = '';
                        if($total_item_cart >= $ims->setting['product']['min_cart_item_bundled']){
                            $row['disabled'] = '';
                            $row['input'] = '<input class="checkbox" type="checkbox" id="bd_'.$row['item_id'].'" value="'.$row['item_id'].'" name="bundled_selected" '.$row['disabled'].'>';
                        }

                        $ims->temp_act->assign("row", $row);
                        $ims->temp_act->parse("list_bundled_product.row");
                    }
                    if($total_item_cart >= $ims->setting['product']['min_cart_item_bundled']){
                        $note = $ims->lang['product']['max_num_chose_bundled'];
                    }else{
                        $note = $ims->site_func->get_lang('not_enough_num_product', 'product', array('[num]' => $ims->setting['product']['min_cart_item_bundled']));
                    }
                    $ims->temp_act->assign("LANG", $ims->lang);
                    $ims->temp_act->assign("data", $data);
                    $ims->temp_act->assign("note", $note);
                    $ims->temp_act->parse("list_bundled_product");
                    $out['html'] = $ims->temp_act->text("list_bundled_product");
                    $out['ok'] = 1;
                }
            }
        }
        return json_encode($out);
    }

    function do_update_cart_bundled(){
        global $ims;

        $output = array(
            'ok' => 0,
            'mess' => $ims->lang['product']['not_yet_chose_include']
        );
        $arr_data = $ims->post['data'];
        if($arr_data){
            $arr_bundled_tmp = $ims->func->unserialize($ims->setting['product']['arr_product_bundled']);
            $arr_bundled = array();
            foreach ($arr_bundled_tmp as $item){
                $arr_bundled[$item['item_id']] = $item;
            }
            foreach ($arr_data as $k => $v){
                if(!isset($arr_bundled[$v])){
                    unset($arr_data[$k]);
                }
            }
            $arr_bundled_selected = array();
            $data = ($arr_data) ? implode(',', $arr_data) : array();
            if($data){
                $list_item = $ims->db->load_item_arr('product', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id IN('.$data.')', 'item_id, price, price_buy');
                if($list_item){
                    foreach ($list_item as $item){
                        $item['endow_price'] = $arr_bundled[$item['item_id']]['price'];
                        $arr_bundled_selected[$item['item_id']] = $item;
                    }
                    Session::Set('bundled_selected', $arr_bundled_selected);
                    if($ims->site_func->checkUserLogin() == 1) {
                        $col_tmp['bundled_product'] = $ims->func->serialize($arr_bundled_selected);
                        $col_tmp['date_update'] = time();
                        $ims->db->do_update('product_order_temp', $col_tmp, 'user_id="'.$ims->data['user_cur']['user_id'].'"');
                    }
                    $output['ok'] = 1;
                }
            }
        }
        return json_encode($output);
    }

    function do_delete_bundled_product(){
        global $ims;
        $out = array(
            'ok' => 1
        );

        Session::Delete('bundled_selected');
        if($ims->site_func->checkUserLogin() == 1) {
            $col_tmp['bundled_product'] = '';
            $col_tmp['date_update'] = time();
            $ims->db->do_update('product_order_temp', $col_tmp, 'user_id="'.$ims->data['user_cur']['user_id'].'"');
        }
        return json_encode($out);
    }

    function do_check_in_stock($arr_cart=array(), $arr_pro=array(), $arr_op=array()){
        global $ims;

        $output = array(
            'ok' => 1,
        );
        if(is_array($arr_cart) && count($arr_cart) > 0){
            foreach ($arr_cart as $row) {
                $row_pro = $arr_pro[$row['item_id']];
                $row_op = $arr_op[$row['option_id']];
                // Có quản lý tồn kho
                $useWarehouse = ($row_op['is_OrderOutStock'] == 1) ? 0 : (int)$ims->setting['product']['use_ware_house'];
                if($useWarehouse == 1){
                    // Nếu số lượng tồn > số lượng đặt
                    if($row_op['Quantity'] >= $row['quantity']){
                        $row_op['Quantity'] = $row_op['Quantity'] - $row['quantity'];
                        // $SQL_UPDATE = "UPDATE product_option SET Quantity = ".$row_op['Quantity']." WHERE lang = '".$ims->conf['lang_cur']."' AND is_show = 1 AND ProductId = ".$row['item_id']." AND id=".$row['option_id']." ";
                        // $ims->db->query($SQL_UPDATE);
                        $output['ok'] = 1;
                    }else{
                        // Nếu số lượng tồn < số lượng đặt
                        if($row_op['is_OrderOutStock']==0){
                            $out_stock[$row['option_id']] = $ims->lang['global']['remaining'].': '.$row_op['Quantity'];
                            Session::Set('out_stock', $out_stock);
                            $output['ok'] = 0;
                        }else{
                            // Cho phép đặt khi hết hàng
                            $output['ok'] = 1;
                        }
                    }
                }
            }
        }
        return $output;
    }

    function do_createOrder(){
        global $ims;
        require_once ("ordering_func.php");
        $this->orderiFunc = new OrderingFunc($this);
        $link_cart = $ims->site_func->get_link('product', '', $ims->setting['product']['ordering_cart_link']);

        $output = array(
            'ok' => 0,
            'mess' => '',
            'link' => '',
        );
        $ims->load_data->data_color();
        $input = $ims->func->unserialize_array ($ims->func->if_isset($ims->post['data'], array()));

        $arr_cart 			= Session::Get('cart_pro', array());
        $arr_cart_list_pro  = Session::Get('cart_list_pro');
        $cart_info 			= Session::Get('cart_info', array());

        if($ims->site_func->checkUserLogin() == 1) {
            $input['address'] = array();
            // Sử dụng giỏ hàng tạm
            $arr_cart = $ims->db->load_row_arr('product_order_temp','user_id="'.$ims->data['user_cur']['user_id'].'"');
            $arr_cart_list_pro = array();
            foreach ($arr_cart as $k => $v) {
                $arr_cart_list_pro[] = $v['item_id'];
            }
            // Lấy địa chỉ theo sổ địa chỉ của thành viên
            $arr_address = $ims->func->unserialize($ims->data['user_cur']['arr_address_book']);
            foreach ($arr_address as $key => $value) {
                if ($value['id'] == $input['address_book']) {
                    $input['address'] = $value;
                }
            }
        }

        // Danh sách sản phẩm mua sau
        $list_save = array();
        if(isset($ims->data['user_cur']['list_save']) && $ims->data['user_cur']['list_save'] != ''){
            $list_save_tmp = $ims->func->unserialize($ims->data['user_cur']['list_save']);
            foreach ($list_save_tmp as $item){
                $list_save[$item['item_id']] = $item;
            }
        }

        $cartProduct = $ims->load_data->data_table (
            'product',
            'item_id', '*',
            ' FIND_IN_SET(item_id, "'.@implode(',', $arr_cart_list_pro).'")>0 '.$ims->conf['where_lang']
        );
        $cartOption = $ims->load_data->data_table(
            'product_option',
            'id', '*',
            ' FIND_IN_SET(ProductId, "'.@implode(',',$arr_cart_list_pro).'")>0 '.$ims->conf['where_lang']
        );
        $orderShipping = $ims->load_data->data_table (
            'order_shipping',
            'shipping_id',
            '*',
            '1 '.$ims->conf['where_lang']
        );
        $orderMethod = $ims->load_data->data_table (
            'order_method',
            'method_id',
            '*',
            '1 '.$ims->conf['where_lang']
        );

        // Kiểm tra địa chỉ
        if (isset($input['shipping']) && isset($orderShipping[$input['shipping']])) {
            $shipping = $orderShipping[$input['shipping']];
            if ($shipping['is_no_address_required'] == 1) {
                // Không yêu cầu địa chỉ
            }else{
                // Yêu cầu địa chỉ
                if (empty($input['address'])) {
                    $output['mess'] = $ims->lang['product']['eror_order_address'];
                    return json_encode($output);
                }
            }
        }

        $order = array();
        $arr_k = array('full_name','email','phone','address','province','district','ward');
        foreach($arr_k as $k) {
            if($ims->site_func->checkUserLogin() == 1) {
                $order['o_'.$k] = $ims->func->if_isset($ims->data['user_cur'][$k]);
                $order['d_'.$k] = $ims->func->if_isset($input['address'][$k]);
            }else{
                $address_tmp[$k] = $input[$k];
                $order['o_'.$k] = $input[$k];
                $order['d_'.$k] = $input[$k];
            }
        }

        if($ims->site_func->checkUserLogin() != 1) {
            $input['address'] = $address_tmp;
        }

        $recommend_type = '';
        if($ims->site_func->checkUserLogin() == 1){
            $info_deeplink = $ims->db->load_row('user_recommend_log', 'is_show = 1 and referred_user_id = '.$ims->data['user_cur']['user_id'], 'type, recommend_user_id, deeplink_id');
            if($info_deeplink){
                $recommend_type = $info_deeplink['type'];
            }elseif (isset($_COOKIE['deeplink'])){
                $recommend_type = 'deeplink';
            }elseif (isset($_COOKIE['user_contributor'])){
                $recommend_type = 'contributor';
            }
        }else{
            $recommend_type_log = $ims->db->load_row('user_recommend_log', 'is_show = 1 and referred_phone = "'.$order['o_phone'].'" or referred_email = "'.$order['o_email'].'"', 'type, recommend_user_id, recommend_link, deeplink_id');
            if($recommend_type_log){
                $recommend_type = $recommend_type_log['type'];
                if($recommend_type == 'deeplink'){
                    $_COOKIE['deeplink'] = $recommend_type_log['deeplink_id'];
                    unset($_COOKIE['user_contributor']);
                }elseif ($recommend_type == 'contributor'){
                    $_COOKIE['user_contributor'] = $ims->db->load_item('user', 'is_show = 1 and user_id = '.$recommend_type_log['recommend_user_id'], 'user_code');
                    parse_str($recommend_type_log['recommend_link'], $type);
                    $_COOKIE['type_contributor'] = $type['type'];
                    unset($_COOKIE['deeplink']);
                }
            }elseif(isset($_COOKIE['deeplink'])){
                $recommend_type = 'deeplink';
            }elseif (isset($_COOKIE['user_contributor'])){
                $recommend_type = 'contributor';
            }
        }

        $order_full_name = $order['o_full_name'];
        if(empty($err)){
            // ------------------- Setup debug
            $__debug = 0;

            $order['shipping'] 	       = $ims->func->if_isset($input['shipping']);
            $order['shipping_price']   = $ims->func->if_isset($cart_info['shipping_fee'], 0);
            $order['method'] 		   = $ims->func->if_isset($input['method']);
            $order["request_more"]     = $ims->func->if_isset($input['request_more']);
            $order["user_id"] 	       = $ims->func->if_isset($ims->data['user_cur']["user_id"], 0);
            $order["is_show"]          = $order['method']==3 ? 1 : 0;
            $order['is_status']        = $ims->site_func->getStatusOrder(1);
            $order["show_order"] 	   = 0;
            $order["sales_channel"]    = 'web';
            $order["date_create"]      = time();
            $order["date_update"]      = time();
            if(!empty($input['invoice'])){
                $order["invoice_company"]  = $ims->func->if_isset($input['invoice_company']);
                $order["invoice_tax_code"] = $ims->func->if_isset($input['invoice_tax_code']);
                $order["invoice_address"]  = $ims->func->if_isset($input['invoice_address']);
                $order["invoice_email"]    = $ims->func->if_isset($input['invoice_email']);
            }
            $phone  			       = $ims->func->if_isset($ims->data['user_cur']["phone"], '');

            $order['deeplink_id'] = 0;
            $deeplink_user_id = 0;
            $contributor_user_id = 0;
            if($recommend_type == 'deeplink'){
                if($ims->site_func->checkUserLogin() == 1){ // check deeplink theo database
                    if(isset($info_deeplink) && $info_deeplink){
                        $deeplink_user_id = $info_deeplink['recommend_user_id'];
                        $order['deeplink_id'] = $info_deeplink['deeplink_id'];
                    }
                }elseif(isset($_COOKIE["deeplink"])){ // check deeplink theo cookie
                    $deeplink_user_id = $ims->db->load_item('user_deeplink', 'is_show = 1 and id = '.$_COOKIE["deeplink"], 'user_id');
                    $order['deeplink_id'] = $_COOKIE["deeplink"];
                }
                if($deeplink_user_id > 0){
                    $info_recommend_user = $ims->db->load_row('user', 'user_id = '.$deeplink_user_id.' AND is_show = 1', 'full_name, user_id');
                    $order['request_more']   = 'DL_'.$info_recommend_user["full_name"].'_id_'.$info_recommend_user["user_id"].'--'.$ims->lang['product']['text_note'].' ('.date('d-m-Y H:i:s A',time()).')'.$order['request_more'];
                    $order['deeplink_valid'] = 1;
                    $order['deeplink_user']  = $deeplink_user_id;
                }else{ // Không có deeplink
                    $order['request_more'] = $phone.' '.$ims->lang['product']['text_note'].' ('.date('d-m-Y H:i:s A',time()).')'.$order['request_more'];
                }
            }elseif($recommend_type == 'contributor'){
                if($ims->site_func->checkUserLogin() == 1){
                    $contributor_user_id = $ims->db->load_item('user_recommend_log', 'is_show = 1 and type = "contributor" and referred_user_id = '.$ims->data['user_cur']['user_id'], 'recommend_user_id');
                }elseif(isset($_COOKIE["user_contributor"])){
                    $contributor_user_id = $ims->db->load_item('user', 'is_show = 1 and user_code = "'.$_COOKIE["user_contributor"].'"', 'user_id');
                }
                if($contributor_user_id > 0){
                    $info_recommend_user = $ims->db->load_row('user', 'user_id = '.$contributor_user_id.' AND is_show = 1', 'full_name, user_id');
                    $order['request_more']   = 'CT_'.$info_recommend_user["full_name"].'_id_'.$info_recommend_user["user_id"].'--'.$ims->lang['product']['text_note'].' ('.date('d-m-Y H:i:s A',time()).')'.$order['request_more'];
                }else{
                    $order['request_more'] = $phone.' '.$ims->lang['product']['text_note'].' ('.date('d-m-Y H:i:s A',time()).')'.$order['request_more'];
                }
            }else{
                $order['request_more'] = $phone.' '.$ims->lang['product']['text_note'].' ('.date('d-m-Y H:i:s A',time()).')'.$order['request_more'];
            }

            $ok = 0;
            $check_in_stock       = $this->do_check_in_stock($arr_cart, $cartProduct, $cartOption);
            if($check_in_stock['ok']){
                if ($__debug == 0) {
                    $ok = $ims->db->do_insert("product_order", $order);
                }
            }else{
                $output['link'] = $link_cart;
            }
            if ($__debug == 1) {
                $ok = $__debug;
            }

            $combo_payment = 0;
            $deeplink_total = 0; // Hoa hồng tiếp thị liên kết
            $deeplink_total_old_temp = 0; // Hoa hồng tiếp thị liên kết tạm dành cho người mua cũ
            $is_use_deeplink_old = 0; // Dùng deeplink cho người mua cũ
            if($ok){
                $total_order 	 = 0;
                $order_info 	 = $order;
                $order_id 		 = $ims->db->insertid();
                $order_id_random = $order_id ? ($order_id+99999) : 100000;
                //kiotviet
                $info_api = $order;
                $info_gift_include = array();
                // Kiểm tra KH đã có đơn hàng thành công hay chưa (dành cho deeplink)
                $completed_order_status = $ims->db->load_item('product_order_status', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and is_complete = 1', 'item_id');
                if($ims->site_func->checkUserLogin() == 1){
                    $check_old_order = $ims->db->load_item('product_order', 'is_show = 1 and ((user_id = '.$ims->data['user_cur']['user_id'].') or (user_id = 0 and (o_email = "'.$ims->data['user_cur']['email'].'" or o_phone = "'.$ims->data['user_cur']['phone'].'"))) and is_status = '.$completed_order_status, 'order_id');
                }else{
                    $check_old_order = $ims->db->load_item('product_order', 'is_show = 1 and (o_email = "'.$order['o_email'].'" or o_phone = "'.$order['o_phone'].'") and is_status = '.$completed_order_status, 'order_id');
                }

                if(is_array($arr_cart) && count($arr_cart) > 0){
                    foreach($arr_cart as $cart_id => $row) {
                        $product = $cartProduct[$row['item_id']];
                        $option  = $cartOption[$row['option_id']];
                        if (!empty($product) && !empty($option)) {
                            $arr_item  = $ims->func->unserialize($product['arr_item']);
                            foreach ($arr_item as $key => $value) {
                                $color = $ims->func->if_isset($option['Option'.($key + 1)]);
                                if(mb_strtolower($value['SelectName']) == "color"){
                                    $option['Option'.($key + 1)] = $ims->data['color'][$color]['title'];
                                }
                            }
                            $picture = $option['Picture'] != "" ? $option['Picture'] : $product['picture'];
                            $col 			    = array();
                            $col['type']        = 'product';
                            $col['order_id']    = $order_id;
                            $col['type_id']     = $product['item_id'];
                            $col['title']       = $product['title'];
                            $col['quantity']    = $ims->func->if_isset($row['quantity'], 0);
                            $col['option_SKU']  = $ims->func->if_isset($option['SKU'], '--');
                            $col['option_id']   = $option['id'];
                            $col['option1']     = $option['Option1'] != "Default Title"? $option['Option1'] : '' ;
                            $col['option2']     = $option['Option2'];
                            $col['option3']     = $option['Option3'];
                            $col['price_buy']   = ($option['PricePromotion'] > 0) ? $option['PricePromotion'] : $option['PriceBuy'];
                            $col['picture']     = $picture;
                            $col['date_create'] = time();
                            $col['option_barcode'] = $option['Barcode'];
                            $col['combo_id']     = $product['combo_id'];

                            // Thông tin gói combo
                            $combo_info = $ims->db->load_row('combo', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id = '.$col['combo_id'], 'item_id, picture, title, arr_product, type, value, value_type, arr_gift, arr_include');
                            if($combo_info){
                                $col['combo_info'] = $ims->func->serialize($combo_info);
                            }
                            //Danh sách quà hoặc sp mua kèm combo
                            $arr_gift_include = $this->do_arr_gift_include($row, $combo_info, $check_old_order, $recommend_type, $deeplink_user_id);
                            $info_gift_include[$col['type_id']]['include'] = !empty($arr_gift_include['arr_deeplink_include'])?$arr_gift_include['arr_deeplink_include']:array();
                            $info_gift_include[$col['type_id']]['gift'] = !empty($arr_gift_include['arr']['gift'])?$arr_gift_include['arr']['gift']:array();
                            $col['arr_gift_include'] = $ims->func->serialize($arr_gift_include['arr']);
                            $combo_payment += $arr_gift_include['add_payment']; //Cộng thêm tiền mua sp kèm combo

                            if ($__debug == 0) {
                                $ok = $ims->db->do_insert("product_order_detail", $col);
                                if($ok){
                                    if(isset($list_save[$row['item_id']])){
                                        unset($list_save[$row['item_id']]);
                                    }
                                }
                            }
                            $total_order += $col['price_buy'] * $col['quantity'];

                            // Kiểm tra sản phẩm tồn tại trên kiotviet
                            // if (empty($option['api_id'])) { // chưa có thì đồng bộ lên
                            //     $api_id = $ims->site_func->do_switch_toapp(1, $product['item_id'],$option['id']);
                            //     if ($api_id>0) {
                            //         $orderDetails[] = array(
                            //             'productId'     => $api_id,
                            //             'productCode'   => $option['SKU'],
                            //             'productName'   => $product['title'],
                            //             'quantity'      => $col['quantity'],
                            //             'price'         => $col['price_buy'],
                            //             'discount'      => 0,
                            //             'discountRatio' => 0,
                            //         );
                             //        if(!empty($info_gift_include[$col['type_id']]['gift'])){
                             //         foreach ($info_gift_include[$col['type_id']]['gift'] as $ki => $vi) {
                                //          $gift = $ims->db->load_row('user_gift','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$vi['item_id'].'"');
                                            // $orderDetails[] = array(
                                      //           'productId'     => $gift['api_id'],
                                      //           'productCode'   => 'GIFT'.$gift['item_id'],
                                      //           'productName'   => $gift['title'],
                                      //           'quantity'      => 1,
                                      //           'price'         => 0,
                                      //           'discount'      => 0,
                                      //           'discountRatio' => 0,
                                      //       );
                                //      }
                             //        }
                                //     if(!empty($info_gift_include[$col['type_id']]['include'])){
                                //         foreach ($info_gift_include[$col['type_id']]['include'] as $ki => $vi) {
                                //             $prdct = $ims->db->load_row('product','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$vi['item_id'].'"');
                                //             $order_by = ' ORDER BY date_create';
                                //             if(!empty($prdct['field_option'])){
                                //                 $order_by = ' ORDER BY '.$prdct['field_option'].', date_create DESC';
                                //             }
                                //             $opt = $ims->db->load_row('product_option','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and ProductId="'.$vi['item_id'].'" '.$order_by);
                                //             $orderDetails[] = array(
                                //                 'productId'     => $prdct['api_id'],
                                //                 'productCode'   => $opt['SKU'],
                                //                 'productName'   => $prdct['title'],
                                //                 'quantity'      => 1,
                                //                 'price'         => 0,
                                //                 'discount'      => 0,
                                //                 'discountRatio' => 0,
                                //             );
                                //         }
                                //     }
                                // }
                            // }else{
                            if(!empty($option['api_id'])){
                                $orderDetails[] = array(
                                    'productId'     => $option['api_id'],
                                    'productCode'   => $option['SKU'],
                                    'productName'   => $product['title'],
                                    'quantity'      => $col['quantity'],
                                    'price'         => $col['price_buy'],
                                    'discount'      => 0,
                                    'discountRatio' => 0,
                                );
                            //     if(!empty($info_gift_include[$col['type_id']]['gift'])){
                         //         foreach ($info_gift_include[$col['type_id']]['gift'] as $ki => $vi) {
                            //          $gift = $ims->db->load_row('user_gift','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$vi['item_id'].'"');
                                        // $orderDetails[] = array(
                                  //           'productId'     => $gift['api_id'],
                                  //           'productCode'   => 'GIFT'.$gift['item_id'],
                                  //           'productName'   => $gift['title'],
                                  //           'quantity'      => 1,
                                  //           'price'         => 0,
                                  //           'discount'      => 0,
                                  //           'discountRatio' => 0,
                                  //       );
                            //      }
                         //        }
                                if(!empty($info_gift_include[$col['type_id']]['include'])){
                                    foreach ($info_gift_include[$col['type_id']]['include'] as $ki => $vi) {
                                        $prdct = $ims->db->load_row('product','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$vi['item_id'].'"');
                                        $order_by = ' ORDER BY date_create';
                                        if(!empty($prdct['field_option'])){
                                            $order_by = ' ORDER BY '.$prdct['field_option'].', date_create DESC';
                                        }
                                        $opt = $ims->db->load_row('product_option','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and ProductId="'.$vi['item_id'].'" '.$order_by);
                                        $orderDetails[] = array(
                                            'productId'     => $prdct['api_id'],
                                            'productCode'   => $opt['SKU'],
                                            'productName'   => $prdct['title'],
                                            'quantity'      => 1,
                                            'price'         => $vi['price_buy_discounted'],
                                            'discount'      => 0,
                                            'discountRatio' => 0,
                                        );
                                    }
                                }
                            }

                            // Tính hoa hồng tiếp thị liên kết trên từng sản phẩm
                            if ($recommend_type == 'deeplink' && $deeplink_user_id > 0){
                                $price_use_commisson = (float)$col['price_buy'] * (int)$col['quantity']; // Giá sản phẩm dành cho tính hoa hồng

                                $promotion_code = Session::Get('promotion_code', '');
                                if($promotion_code != ''){
                                    $promotion = $this->orderiFunc->promotion_discount_per_item($product['item_id'], $price_use_commisson, $promotion_code);
                                    $price_use_commisson -= (float)$promotion['price_minus']; // Trừ tiền khuyến mãi
                                }
                                if((int)$product['group_id'] == 0){
                                    $percent_deeplink_old = (float)$ims->setting['product']['percent_deeplink_default_old'];
                                    $percent_deeplink_new = (float)$ims->setting['product']['percent_deeplink_default_new'];
                                }else{
                                    $group_nav = explode(',', $product['group_nav']);
                                    $group_id = $group_nav[0];
                                    $percent_deeplink_group = $ims->db->load_row('product_group', 'is_show = 1 and lang = "'.$ims->conf["lang_cur"].'" and group_id = '.$group_id, 'percent_deeplink_old, percent_deeplink_new');
                                    $percent_deeplink_old = ((float)$percent_deeplink_group['percent_deeplink_old'] > 0) ? (float)$percent_deeplink_group['percent_deeplink_old'] : (float)$ims->setting['product']['percent_deeplink_default_old'];
                                    $percent_deeplink_new = ((float)$percent_deeplink_group['percent_deeplink_new'] > 0) ? (float)$percent_deeplink_group['percent_deeplink_new'] : (float)$ims->setting['product']['percent_deeplink_default_new'];
                                }

                                if($check_old_order){
                                    $deeplink_item_tmp = ($price_use_commisson * $percent_deeplink_old/100);
                                    $deeplink_total += ($deeplink_item_tmp > (float)$ims->setting['product']['amount_deeplink_default']) ? (float)$ims->setting['product']['amount_deeplink_default'] : $deeplink_item_tmp;
                                    $is_use_deeplink_old = 1;
                                }else{
                                    $deeplink_item_new_tmp = ($price_use_commisson * $percent_deeplink_new/100);
                                    $deeplink_item_old_tmp = ($price_use_commisson * $percent_deeplink_old/100);

                                    $deeplink_total += ($deeplink_item_new_tmp > (float)$ims->setting['product']['amount_deeplink_default']) ? (float)$ims->setting['product']['amount_deeplink_default'] : $deeplink_item_new_tmp;
                                    $deeplink_total_old_temp += ($deeplink_item_old_tmp > (float)$ims->setting['product']['amount_deeplink_default']) ? (float)$ims->setting['product']['amount_deeplink_default'] : $deeplink_item_old_tmp;
                                }
                                $deeplink_total += (isset($arr_gift_include['deeplink_total_include'])) ? (float)$arr_gift_include['deeplink_total_include'] : 0;
                                $deeplink_total_old_temp += (isset($arr_gift_include['deeplink_total_include_old'])) ? (float)$arr_gift_include['deeplink_total_include_old'] : 0;

                                $deeplink_detail[] = array(
                                    'item_id' => $col['type_id'],
                                    'picture' => $col['picture'],
                                    'option_id' => $col['option_id'],
                                    'price_buy' => $col['price_buy'],
                                    'quantity' => $col['quantity'],
                                    'price_use_commisson' => $price_use_commisson,
                                    'root_group' => ($product['group_id'] > 0) ? $group_id : 0,
                                    'percent_deeplink_group_old' => ((int)$product['group_id'] > 0 && isset($percent_deeplink_group['percent_deeplink_old'])) ? $percent_deeplink_group['percent_deeplink_old'] : 0,
                                    'percent_deeplink_group_new' => ((int)$product['group_id'] > 0 && isset($percent_deeplink_group['percent_deeplink_new'])) ? $percent_deeplink_group['percent_deeplink_new'] : 0,
                                    'percent_deeplink_default_old' => $ims->setting['product']['percent_deeplink_default_old'],
                                    'percent_deeplink_default_new' => $ims->setting['product']['percent_deeplink_default_new'],
                                    'max_deeplink_default_per_item' => $ims->setting['product']['amount_deeplink_default'],
                                    'arr_deeplink_include' => (isset($arr_gift_include['arr_deeplink_include'])) ? $arr_gift_include['arr_deeplink_include'] : ''
                                );
                            }
                        }
                    }
                }

                // Nhập lịch sử hoa hồng theo từng sản phẩm
                if($recommend_type == 'deeplink' && $deeplink_user_id > 0){
                    $deeplink_log = array(
                        'order_id' => $order_id,
                        'deeplink_id' => $order['deeplink_id'],
                        'order_user' => (isset($ims->data['user_cur']['user_id'])) ? $ims->data['user_cur']['user_id'] : 0,
                        'deeplink_detail' => $ims->func->serialize($deeplink_detail),
                        'commission_add' => $deeplink_total,
                        'commission_add_old_temp' => $deeplink_total_old_temp,
                        'is_show' => 1,
                        'is_added' => 0,
                        'date_create' => time(),
                        'date_update' => time(),
                    );
                    $ims->db->do_insert("user_deeplink_log", $deeplink_log);
                }

                // Cập nhật lại danh sách sản phẩm đã lưu
                $list_save = array_values($list_save);
                $list_save = $ims->func->serialize($list_save);
                $ims->db->do_update('user', array('list_save' => $list_save), 'user_id = '.$ims->data['user_cur']['user_id']);

                $total_payment = $total_order;

                // ---------------- promotion_percent
                $promotion_code    = Session::Get('promotion_code', '');
                $promotion_info    = $this->orderiFunc->promotion_info($total_order, $promotion_code);
                $promotion_percent = $promotion_info['percent'];
                $promotion_code    = $promotion_info['promotion_id'];
                $promotion_price   = $promotion_info['price'];
                if($promotion_info['mess'] == $ims->lang['product']['freeship'] || ($promotion_info['mess'] == '' && $promotion_info['price'] > 0)) {
                    $total_payment -= $promotion_price;
                }
                // ----------------  End promotion_percent

                // ----------------  shipping_price
                if(isset($cart_info['shipping_fee']) && $cart_info['shipping_fee'] > 0) {
                    $total_payment += $cart_info['shipping_fee'];
                }
                // ----------------  End shipping_price

                if(isset($cart_info['wcoin_use']) && $cart_info['wcoin_use'] > 0){
                    $wcoin_use 	= $cart_info['wcoin_use'];
                    $user_wcoin = $ims->data['user_cur']['wcoin'];
                    // $user_wcoin_expires = $ims->data['user_cur']['wcoin_expires'];
                    $max_wcoin = $total_payment / $ims->setting['product']['wcoin_to_money'];
                    if($user_wcoin < $wcoin_use){
                        $cart_info['wcoin_use'] = $user_wcoin;
                        Session::Set ('cart_info', $cart_info);
                        $ims->html->redirect_rel($link_cart);
                    }
                    // if($user_wcoin_expires!='' && $user_wcoin_expires < time()){
                    // 	$cart_info['wcoin_use'] = 0;
                    // 	Session::Set ('cart_info', $cart_info);
                    // 	$ims->html->redirect_rel($link_cart);
                    // }
                    if($wcoin_use > $max_wcoin){
                        $wcoin_use = $max_wcoin;
                    }
                    $money_use_wcoin = $wcoin_use * $ims->setting['product']['wcoin_to_money'];
                    $data['wcoin_use'] = $wcoin_use;
                    $data['wcoin_price_out'] = $ims->func->get_price_format($money_use_wcoin, 0);
                    $total_payment -= $money_use_wcoin;
                }

                // ---------------- product_order_log
                $arr_ins                    = array();
                $arr_ins['is_show']         = 1;
                $arr_ins['order_id']        = $order_id;
                $arr_ins['date_create']     = time();
                $arr_ins['date_update']     = time();
                $arr_ins['title'] = $ims->site_func->get_lang('create_new_order','global',array('{order_id}' => '#'.$order_id_random, '{order_name}' => $order_full_name));
                if ($__debug == 0) {
                    $ims->db->do_insert('product_order_log', $arr_ins);
                }
                // ---------------- end product_order_log

                // ---------------- promotion log
                $promo_log = array();
                $promo_log['promotion_id'] = $promotion_code;
                $promo_log['user_id'] = $ims->func->if_isset($ims->data['user_cur']["user_id"], 0);
                $promo_log['order_id'] = $order_id;
                $promo_log['is_show'] = 1;
                $promo_log['date_create'] = time();
                if ($__debug == 0 && $promotion_info['type'] == 1) {
                    $ok = $ims->db->do_insert("promotion_log", $promo_log);
                    $ims->db->query("UPDATE promotion SET num_use=num_use+1, date_update=".time()." WHERE promotion_id='".$promotion_code."'");
                }
                $event_promotion = array();
                if($promotion_info['type'] == 2){
                    if($ims->setting['product']['is_order_discount'] == 1){
                        $event_promotion['order_discount'] = array(
                            'percent_discount' => $ims->setting['product']['percent_discount'],
                            'min_cart_item_discount' => $ims->setting['product']['min_cart_item_discount'],
                            'promotion_price' => $promotion_price
                        );
                    }
                }
                // ---------------- END promotion log

                // ---------------- freeship event
                $shipping_price0 = Session::Get('shipping_price0');
                if($ims->setting['product']['is_freeship'] == 1){
                    $event_promotion['freeship'] = array(
                        'shipping_price' => $shipping_price0,
                        'ototal_freeship' => $ims->setting['product']['ototal_freeship'],
                        'arr_price' => $ims->setting['product']['arr_price'],
                    );
                }
                // ---------------- END freeship event

                // ---------------- Mua kèm sp giá ưu đãi
                if($ims->site_func->checkUserLogin() == 1) {
                    $chosed = ($arr_cart[0]['bundled_product'] != '') ? $ims->func->unserialize($arr_cart[0]['bundled_product']) : array();
                }else{
                    $chosed = Session::Get('bundled_selected', array());
                }
                if($chosed){
                    foreach ($chosed as $prd){
                        $total_payment += $prd['endow_price'];
                        $total_order += $prd['endow_price'];
                    }
                    $event_promotion['bundled_product'] = $chosed;
                }
                // ---------------- END Mua kèm sp giá ưu đãi

                $col_up = array();
                $col_up["promotion_id"] 	 = $promotion_code;
                $col_up["order_code"]        = $order_id_random;
                $col_up["total_order"] 		 = $total_order + $combo_payment;
                $col_up["promotion_id"] 	 = ($promotion_info['type'] == 1) ? $promotion_code : '';
                $col_up["promotion_percent"] = ($promotion_info['type'] == 1) ? $promotion_percent : 0;
                $col_up["promotion_price"]   = ($promotion_info['type'] == 1) ? $promotion_price : 0;
                $col_up['event_promotion']   = $ims->func->serialize($event_promotion);
                if(isset($cart_info['wcoin_use']) && $cart_info['wcoin_use'] > 0){
                    // UPDATE WCOIN USER
                    if($ims->site_func->checkUserLogin() == 1){
                        $wcoin_use = $cart_info['wcoin_use'];
                        $user_log['wcoin_before'] = $ims->data['user_cur']['wcoin'];
                        $user_log['wcoin_after'] = $ims->data['user_cur']['wcoin'] - $wcoin_use;
                        $SQL_UPDATE = "UPDATE user SET wcoin = wcoin - ".$wcoin_use." WHERE is_show = 1 AND user_id = ".$ims->data['user_cur']['user_id'];
                        $ims->db->query($SQL_UPDATE);
                        $user_log['exchange_type'] = 'buy';
                        $user_log['dbtable'] = 'product_order';
                        $user_log['dbtable_id'] = $order_id;
                        $user_log['value_type'] = -1;
                        $user_log['note'] = $ims->lang['global']['note_use_wcoin'].$order_id_random;
                        $user_log['is_show'] = 1;
                        $user_log['date_create'] = time();
                        $user_log['user_code'] = $ims->data['user_cur']['user_code'];
                        $user_log['user_id'] = $ims->data['user_cur']['user_id'];
                        $user_log['value'] = $wcoin_use;
                        $ok = $ims->db->do_insert("user_exchange_log", $user_log);
                    }
                }
                if(isset($cart_info['wcoin_use']) && $cart_info['wcoin_use'] > 0){
                    $col_up['payment_wcoin'] = $cart_info['wcoin_use'];
                    $col_up['payment_wcoin2money'] = $cart_info['wcoin_use'] * $ims->setting['product']['wcoin_to_money'];
                }

                $total_payment += $combo_payment; //Cộng thêm tiền mua sp kèm combo
                $col_up["total_payment"] = $total_payment;
                $col_up['wcoin_accumulation'] = round($total_payment * ($ims->setting['product']['percentforwcoin']/100) / $ims->setting['product']['money_to_wcoin']);

                // Cập nhật hoa hồng cho người giới thiệu link thường
                if($recommend_type == 'contributor' && $contributor_user_id != 0){
                    if($ims->site_func->checkUserLogin() == 1){
                        $col_up["user_contributor"] = $ims->data['user_cur']['user_contributor'];
                    }elseif (isset($_COOKIE['user_contributor'])){
                        $col_up["user_contributor"] = $_COOKIE['user_contributor'];
                    }
                    $col_up["wcoin_contributor"] = $col_up['wcoin_accumulation'] * $ims->setting['product']['percentforcontributor']/100;
                }

                // Cập nhật log giới thiệu khi không đăng nhập
                if($ims->site_func->checkUserLogin() != 1){
                    $check_inserted = $ims->db->load_item('user_recommend_log', 'is_show = 1 and referred_email = "'.$order['o_email'].'" or referred_phone = "'.$order['o_phone'].'"', 'id');
                    if(!$check_inserted){
                        $recommend_log = array();
                        if($recommend_type == 'contributor' && $contributor_user_id != 0){
                            $recommend_log = array(
                                'type' => 'contributor',
                                'recommend_user_id' => $contributor_user_id,
                                'recommend_link' => 'contributor='.$ims->func->base64_encode($_COOKIE['user_contributor']).'&type='.$_COOKIE['type_contributor'],
                                'referred_user_id' => 0,
                                'referred_full_name' => $order['o_full_name'],
                                'referred_email' => $order['o_email'],
                                'referred_phone' => $order['o_phone'],
                                'is_show' => 1,
                                'date_create' => time(),
                                'date_update' => time(),
                            );
                        }elseif ($recommend_type == 'deeplink' && $deeplink_user_id != 0){
                            $recommend_log = array(
                                'type' => 'deeplink',
                                'recommend_user_id' => $deeplink_user_id,
                                'recommend_link' => $ims->db->load_item('user_deeplink', 'is_show = 1 and id = '.$_COOKIE["deeplink"], 'short_code'),
                                'deeplink_id' => $_COOKIE["deeplink"],
                                'referred_user_id' => 0,
                                'referred_full_name' => $order['o_full_name'],
                                'referred_email' => $order['o_email'],
                                'referred_phone' => $order['o_phone'],
                                'is_show' => 1,
                                'date_create' => time(),
                                'date_update' => time(),
                            );
                        }
                        if($recommend_log){
                            $ims->db->do_insert("user_recommend_log", $recommend_log);
                        }
                    }
                }

                if($recommend_type == 'deeplink' && $deeplink_user_id != 0){ // Update hoa hồng tiếp thị liên kết vào product_order
                    $col_up['deeplink_total'] = $deeplink_total;
                    $col_up['deeplink_total_old_temp'] = $deeplink_total_old_temp;
                    $col_up['is_use_deeplink_old'] = $is_use_deeplink_old;
                }

                // up đơn hàng lên kiotviet
                $customer = array();
                $customer['id'] = 0;
                $token_api = $ims->site_func->getTokenKiotviet();
                if (!empty($token_api)) {
                    $url_customer = 'https://public.kiotapi.com/customers/?contactNumber='.rawurlencode($info_api['o_phone']);
                    $header = array(
                        "Retailer: " .$ims->setting['kiotviet']['retailer_kiotviet'],
                            "Authorization: Bearer ".$token_api,
                            "Content-Type: application/json",
                    );
                    $Response_customer = $ims->site_func->sendPostData($url_customer, array(), 'get', 10, '', $header);
                    if (!empty($Response_customer)) {
                        $Response_customer = json_decode($Response_customer);
                        if (isset($Response_customer->data) && !empty($Response_customer->data)) {
                            foreach ($Response_customer->data as $k => $v) {
                                if ($info_api['o_phone'] != '') {
                                    if ($v->contactNumber == $info_api['o_phone']) {
                                        $customer['id'] = $v->id;
                                        $customer['code'] = $v->code;
                                        $customer['name'] = $v->name;
                                        $customer['contactNumber'] = $v->contactNumber;
                                        $customer['address'] = $v->address;
                                        // $customer['email'] = $v->email;
                                    }
                                }
                            }
                        }
                    }
                    if ($customer['id'] == 0) {
                        $data_customer = array(
                            "name" => $info_api['o_full_name'],
                            "contactNumber" => $info_api['o_phone'],
                            "address" => $info_api['o_address'],
                            "branchId" => $ims->setting['kiotviet']['branch_id_kiotviet'],
                            "email" => $info_api['o_email'],
                        );
                        $data_customer = json_encode($data_customer);
                        $Response_customer = $ims->site_func->sendPostData("https://public.kiotapi.com/customers", $data_customer, 'post', 10, '', $header);
                        if (!empty($Response_customer)) {
                            $Response_customer = json_decode($Response_customer);
                            if (isset($Response_customer->data) && !empty($Response_customer->data)) {
                                $tmp = $Response_customer->data;
                                $customer['id'] = $tmp->id;
                                $customer['code'] = $tmp->code;
                                $customer['name'] = $tmp->name;
                                $customer['contactNumber'] = $tmp->contactNumber;
                                $customer['address'] = $tmp->address;
                                $customer['email'] = $tmp->email;
                            }
                        }
                    }
                    if (!empty($customer['id'])) {
                        $id_surcharges = '';
                        $code_surcharges = '';
                        if (isset($order['shipping_price']) && $order['shipping_price']>0) {
                            $surcharges = $ims->site_func->sendPostData('https://public.kiotapi.com/surchages', array(), 'get', 10, '', $header);
                            if (!empty($surcharges)) {
                                $surcharges = json_decode($surcharges);
                                if (isset($surcharges->data)) {
                                    foreach ($surcharges->data as $k => $v) {
                                        if ($order['shipping_price'] == $v->value) {
                                            $id_surcharges = $v->id;
                                            $code_surcharges = $v->surchargeCode;
                                        }
                                    }
                                }
                            }
                        }
                        $data_api = array(
                            "purchaseDate" => time(),
                            "branchId" => $ims->setting['kiotviet']['branch_id_kiotviet'],
                            "discount" => $promotion_price,
                            "method" => $ims->db->load_item('order_method', ' method_id="'.$info_api['method'].'" and lang="'.$ims->conf['lang_cur'].'" ','title'),
                            "totalPayment" => 0,
                            "makeInvoice" => isset($ims->post['invoice']) ? true : false,
                            "orderDetails" => $orderDetails,
                            "description" => $info_api['request_more'],
                            "customer" => array(
                                "id" => $customer['id'],
                                "name" => $customer['name'],
                                "contactNumber" => $customer['contactNumber'],
                                "address" => $customer['address'],
                                // "email" => $customer['email'],
                            ),
                            "usingCod" => true,
                            "orderDelivery" => array(
                                'price' => $order['shipping_price'],
                                'receiver' => $info_api['o_full_name'],
                                "address" => $ims->func->full_address($info_api, 'o_'),
                                "contactNumber" => $info_api['o_phone'],
                                'partnerDeliveryId' => 0,
                                'partnerDelivery' => array(),
                            ),
                            "surchages" => array(
                                'id' => $id_surcharges,
                                'code' => $code_surcharges,
                            )
                        );
                        $data_api = json_encode($data_api);
                        $url_send = "https://public.kiotapi.com/orders/";

                        $Response = $ims->site_func->sendPostData($url_send, $data_api, 'post', 10, '', $header);
                        if (!empty($Response)) {
                            $Response = json_decode($Response);
                            if (!empty($Response->id)) {
                                $col_up['api_id'] = $Response->id;
                                $col_up['api_code'] = $Response->code;
                                $col_up['order_code'] = $col_up['api_code'];
                                $col_up['api_branchId'] = $Response->branchId;
                                $col_up['api_branchName'] = $Response->branchName;
                                $col_up['api_retailerId'] = $Response->retailerId;
                            }else{
                                if (isset($Response->responseStatus)) {
                                    $responseStatus = $Response->responseStatus;
                                    // if (strpos($responseStatus->message, 'Không đủ số lượng tồn kho cho sản phẩm') !== false) {
                                        $col_up["error_kiotviet"] = $responseStatus->message;
                                    // }
                                }
                            }
                        }
                    }
                }
                if(!empty($order["invoice_tax_code"])){
                    if(!empty($ims->data['user_cur']['user_id'])){
                        $arr_user = array();
                        $arr_user['invoice_company'] = $order['invoice_company'];
                        $arr_user['invoice_tax_code'] = $order['invoice_tax_code'];
                        $arr_user['invoice_address'] = $order['invoice_address'];
                        $arr_user['invoice_email'] = $order['invoice_email'];
                        $ims->db->do_update('user',$arr_user,' user_id="'.$ims->data['user_cur']['user_id'].'" ');
                    }
                    $col_up['vat_price'] = round($col_up["total_order"]*10/100,0);
                    $col_up["total_payment"] += $col_up['vat_price'];
                }
                // -------------------- Update order
                if ($__debug == 0) {
                    $ims->db->do_update("product_order", $col_up, " order_id='".$order_id."'");
                }
                $output['ok'] = 1;

                $order_info = array_merge($order_info, $col_up);
                // -------------------- End Update order

                $arr_cart = Session::Set ('ordering_payment', array(
                    'order_code' 		=> $order_info['order_code'],
                    'method' 			=> $order_info['method'],
                    'total_order' 		=> $order_info['total_order'],
                    'total_payment' 	=> $order_info['total_payment'],
                    'arr_cart_list_pro' => $arr_cart_list_pro,
                    'token' 			=> $ims->func->random_str(10)
                ));
                $cart_pro_deeplink = Session::Get('cart_pro');
                Session::Set ('cart_pro_deeplink',$cart_pro_deeplink);
                // print_arr($order_info);
                // ------------------------ *************** Thanh toán ONLINE
                if(isset($orderMethod[$order_info['method']]) && $orderMethod[$order_info['method']]['name_action']!='') {
                    //  Thanh toán qua ONLINE
                    $input['address']['bankcode'] = $ims->func->if_isset($input['bankcode'], 0);
                    $resurl = $ims->site_func->paymentCustom($orderMethod[$order_info['method']], $order_info, $col_up , $input['address']);
                    if (isset($resurl['ok']) && $resurl['ok']==1) {
                        $output['link'] = $resurl['link'];
                    }
                }else{
                    Session::Delete('cart_pro');
                    Session::Delete('cart_info');
                    Session::Delete('cart_list_pro');
                    Session::Delete('ordering_address');
                    Session::Delete('promotion_code');
                    Session::Delete('user_contributor');
                    $output['link'] = $ims->site_func->get_link ('product','',$ims->setting['product']['ordering_complete_link']);
                }
            }//End if ok
        }

        if ($output['ok'] == 1 && $ims->site_func->checkUserLogin() == 1) {
            // Xóa giỏ hàng tạm
            $ims->db->query('DELETE FROM `product_order_temp` WHERE user_id="'.$ims->data['user_cur']['user_id'].'" ');
            // Ghi log

        }

        return json_encode($output);
    }

    function do_arr_gift_include($row, $combo, $check_old_order, $recommend_type, $deeplink_user_id){
        global $ims;
        $out = array(
            'arr' => '',
            'add_payment' => 0,
            'deeplink_total_include' => 0,
            'deeplink_total_include_old' => 0
        );

        if($row['combo_info'] != ''){ //combo_info trong session (chứa id sp hoặc gift mua kèm)
            $cb_info =  $ims->func->unserialize($row['combo_info']);
            $k_combo = array_keys($cb_info);
            $k_combo = str_replace('_id', '', $k_combo[0]);
            $v_combo = array_values($cb_info);
            $row[$k_combo] = $v_combo[0];
        }

        if($combo){
            if($combo['type'] != 1){
                if(isset($row['gift']) && $row['gift'] != ''){
                    $arr_gift = $ims->db->load_item_arr('user_gift', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id IN ('.$row['gift'].') order by FIELD(item_id,"'.$row['gift'].'") desc', 'item_id, title, picture, product_id, price');
                    if($arr_gift){
                        $out['arr'] = array('gift' => $arr_gift);
                    }
                }
                if(isset($row['include']) && $row['include'] != ''){
                    $out['arr'] = array();
                    $arr_include = $ims->db->load_item_arr('product', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id IN('.$row['include'].') order by FIELD(item_id,"'.$row['include'].'") desc', 'item_id, title, picture, price, price_buy, group_id, group_nav');
                    if($arr_include){
                        foreach ($arr_include as $include){
                            $include['price_buy_combo'] = ($combo['value_type'] == 1) ? $include['price_buy']*((100 - $combo['value'])/100) : ($include['price_buy'] - $combo['value']);
                            if($include['price_buy_combo'] < 0){
                                $include['price_buy_combo'] = 0;
                            }
                            $out['arr']['include'][] = $include;
                            $out['add_payment'] += $include['price_buy_combo'];

                            // Tính hoa hồng tiếp thị liên kết trên từng sản phẩm
                            if ($recommend_type == 'deeplink' && $deeplink_user_id > 0){
                                if($include['group_id'] == 0){
                                    $percent_deeplink_old = $ims->setting['product']['percent_deeplink_default_old'];
                                    $percent_deeplink_new = $ims->setting['product']['percent_deeplink_default_new'];
                                }else{
                                    $group_nav = explode(',', $include['group_nav']);
                                    $group_id = $group_nav[0];
                                    $percent_deeplink_group = $ims->db->load_row('product_group', 'is_show = 1 and lang = "'.$ims->conf["lang_cur"].'" and group_id = '.$group_id, 'percent_deeplink_old, percent_deeplink_new');
                                    $percent_deeplink_old = ($percent_deeplink_group['percent_deeplink_old'] > 0) ? $percent_deeplink_group['percent_deeplink_old'] : $ims->setting['product']['percent_deeplink_default_old'];
                                    $percent_deeplink_new = ($percent_deeplink_group['percent_deeplink_new'] > 0) ? $percent_deeplink_group['percent_deeplink_new'] : $ims->setting['product']['percent_deeplink_default_new'];
                                }
                                if($check_old_order){
                                    $deeplink_item_tmp = ($include['price_buy_combo'] * $percent_deeplink_old/100);
                                    $out['deeplink_total_include'] += ($deeplink_item_tmp > $ims->setting['product']['amount_deeplink_default']) ? $ims->setting['product']['amount_deeplink_default'] : $deeplink_item_tmp;
                                }else{
                                    $deeplink_item_new_tmp = ($include['price_buy_combo'] * $percent_deeplink_new/100);
                                    $deeplink_item_old_tmp = ($include['price_buy_combo'] * $percent_deeplink_old/100);
                                    $out['deeplink_total_include'] += ($deeplink_item_new_tmp > $ims->setting['product']['amount_deeplink_default']) ? $ims->setting['product']['amount_deeplink_default'] : $deeplink_item_new_tmp;
                                    $out['deeplink_total_include_old'] += ($deeplink_item_old_tmp > $ims->setting['product']['amount_deeplink_default']) ? $ims->setting['product']['amount_deeplink_default'] : $deeplink_item_old_tmp;
                                }

                                $arr_deeplink_include[] = array(
                                    'item_id' => $include['item_id'],
                                    'picture' => $include['picture'],
                                    'price_buy' => $include['price_buy'],
                                    'price_buy_discounted' => $include['price_buy_combo'],
                                    'root_group' => ($include['group_id'] > 0) ? $group_id : 0,
                                    'percent_deeplink_group_old' => ($include['group_id'] > 0 && isset($percent_deeplink_group['percent_deeplink_old'])) ? $percent_deeplink_group['percent_deeplink_old'] : 0,
                                    'percent_deeplink_group_new' => ($include['group_id'] > 0 && isset($percent_deeplink_group['percent_deeplink_new'])) ? $percent_deeplink_group['percent_deeplink_new'] : 0
                                );
                            }
                        }
                    }
                }
            }
        }
        $out['arr_deeplink_include'] = (isset($arr_deeplink_include) && $arr_deeplink_include) ? $arr_deeplink_include : '';

        return $out;
    }

    function do_add_address(){
        global $ims;

        require_once ("ordering_func.php");
        $output = array(
            'ok' => 0,
            'address' => '',
        );
        $lang_cur = $ims->func->if_isset($ims->post['lang_cur']);
        $input_tmp = $ims->func->if_isset($ims->post['data'], array());
        $arr_tmp = array();
        if(isset($ims->post['data'])) {
            foreach($ims->post['data'] as $key) {
                eval('$'.$key['name'].' = "'.$key['value'].'";');
                $arr_tmp[$key['name']] = $key['value'];
            }
        }
        $arr_k = array('full_name','email','phone','address','province','district','ward');
        $col = array();
        foreach($arr_k as $k) {
            $col['o_'.$k] = $col['d_'.$k] = (isset($arr_tmp[$k])) ? $arr_tmp[$k] : '';
        }
        $ordering_address = Session::Set('ordering_address', $col);
        if(is_array($ordering_address) && count($ordering_address)>0){
            $output['ok'] = 1;
            $output['address'] = $arr_tmp['address'].', 
						   	'.get_name_location('location_ward', $arr_tmp['ward']).', 
						   	'.get_name_location('location_district', $arr_tmp['district']).', 
						   	'.get_name_location('location_province', $arr_tmp['province']);
        }
        return json_encode($output);
    }

    function do_shippingFee(){
        global $ims;

        $output = array(
            'ok' => 0,
            'mess' => '',
            'price_out' => 0,
            'shipping_price0' => 0,
        );
        $cart_info 		  = Session::Get ('cart_info', array());
        $arr_cart         = Session::Get ('cart_pro', array());
        $shipping_id      = $ims->func->if_isset($ims->post['shipping_id'], 0);
        $method_id        = $ims->func->if_isset($ims->post['method_id'], 0);

        $total_money      = $ims->func->if_isset($ims->post['total_money'], 0);
        $total_promotion  = $ims->func->if_isset($ims->post['total_promotion'], 0);
        $total_wcoin  	  = $ims->func->if_isset($ims->post['total_wcoin'], 0);

        $oaddress   = !empty($ims->post['oaddress'])?$ims->func->unserialize_array($ims->post['oaddress']):array();
        $address    = $ims->func->if_isset($oaddress['address']);
        $province   = $ims->func->if_isset($oaddress['province']);
        $district   = $ims->func->if_isset($oaddress['district']);
        $ward       = $ims->func->if_isset($oaddress['ward']);

        // if(!$address){ // Lấy quận huyện khi ko đăng nhập

        // }else{
            // if($ims->site_func->checkUserLogin() == 1) {
            //     $arr_address = $ims->func->unserialize($ims->data['user_cur']['arr_address_book']);
            //     usort($arr_address, function ($a, $b) {return $a['is_default'] < $b['is_default'];});
            //     $province = $arr_address[$address_id]['province'];
            //     $district = $arr_address[$address_id]['district'];
            //     $ward     = $arr_address[$address_id]['ward'];
            // }else{
            //     $province = $ims->func->if_isset($ims->post['province'], 0);
            //     $district = $ims->func->if_isset($ims->post['district'], 0);
            //     $ward     = $ims->func->if_isset($ims->post['ward'], 0);
            // }
        // }
        foreach ($arr_cart as $key => $value) {
            $arr_cart_list_pro[] = $value['item_id'];
            $arr_cart_list_op[] = $value['option_id'];
        }
        $products = $ims->load_data->data_table ('product_option', 'ProductId', '*', " is_show=1 AND lang='".$ims->conf['lang_cur']."' AND find_in_set(ProductId,'".@implode(',', $arr_cart_list_pro)."')>0 AND find_in_set(id,'".@implode(',', $arr_cart_list_op)."')>0 ORDER BY show_order DESC, date_create ASC");
        $totalweight = 0;
        $length = 20;
        $width  = 20;
        $height = 20;
        $multiplicationMax = 0;
        if (!empty($arr_cart)) {
            foreach ($arr_cart as $key => $value) {
                $product = !empty($products[$value['item_id']])?$products[$value['item_id']]:array();
                if (!empty($product)) {
                    $totalweight += $product["Weight"] * $value['quantity'];
                    $multiplication = $product["Length"] * $product['Width'] * $product['Height'];
                    if ($multiplicationMax>0) {
                        if ($multiplication > $multiplicationMax) {
                            $multiplicationMax = $multiplication;
                            $length = $product["Length"];
                            $width  = $product['Width'];
                            $height = $product['Height'];
                        }
                    }else{
                        $multiplicationMax = $multiplication;
                        $length = $product["Length"];
                        $width  = $product['Width'];
                        $height = $product['Height'];
                    }
                }
            }
        }

        if ($totalweight == 0) {
            $totalweight = 1000;
        }

        $shippingInfo = $ims->db->load_row("order_shipping", " shipping_id = '".$shipping_id."' ".$ims->conf['where_lang']);
        if (!empty($shippingInfo)) {
            $output['price_method'] = 0;
            $output['price_out_no_format'] = 0;
            $output['price_out'] = 0;
            if ($shippingInfo['shipping_type']=="GHTK") {
                $arr_connect = $ims->func->unserialize ($shippingInfo['arr_connect']);
                $arr_option  = $ims->func->unserialize ($shippingInfo['arr_option']);
                $warehouse = $ims->db->load_row("product_order_address", "is_default=1 AND is_show=1 AND lang='".$ims->conf['lang_cur']."' ");
                $warehouse_id = 0;
                if (!empty($warehouse)) {
                    $warehouse_id = $arr_connect[$warehouse['item_id']];
                }
                $data = array(
                    "address"           => $address,
                    "province"          => $ims->func->location_name('province', $province),
                    "district"          => $ims->func->location_name('district', $district),
                    "ward"              => $ims->func->location_name('ward', $ward),
                    "pick_address_id"   => $warehouse_id,
                    "pick_district"     => $ims->func->location_name('district', $warehouse['province']),
                    "pick_province"     => $ims->func->location_name('province', $warehouse['district']),
                    "weight"            => $totalweight,
                    "deliver_option"    => "xfast",
                    "transport"         => "fly",
                    "value"             => $total_money,
                );
                $url = $ims->conf['URL_API_GHTK'].'services/shipment/fee?'.http_build_query($data);
                $resp = $ims->site_func->sendPostDataGHTK($url, array(), 'get', $arr_option['Token']);
                $resp = json_decode($resp);
                // print_r($resp);
                if (isset($resp->fee->fee)) {
                    $output['ok'] = 1;
                    $get_price = $resp->fee->fee;
                    $output['price_out_no_format'] = $get_price;
                    $output['price_out'] = $ims->func->get_price_format($get_price,0);
                }
            } elseif ($shippingInfo['shipping_type'] == "GHN") {
                $arr_connect = $ims->func->unserialize ($shippingInfo['arr_connect']);
                $arr_option  = $ims->func->unserialize ($shippingInfo['arr_option']);
                $warehouse = $ims->db->load_row("product_order_address", "is_default=1 AND is_show=1 AND lang='".$ims->conf['lang_cur']."' ");
                $warehouse_id = 0;
                if (!empty($warehouse)) {
                    $warehouse_id = $arr_connect[$warehouse['item_id']];
                }
                $arr_input = array(
                   "offset" => 0,
                   "limit" => 50,
                   "client_phone" => ""
                );

                $resp = $ims->site_func->apiGHN("GetShop", $arr_input, $arr_option['Token']);
                $from_district_id = 0;
                foreach ($resp->data->shops as $k => $v) {
                    if ($v->_id == $warehouse_id) {
                        $from_district_id = $v->district_id;
                    }
                }
                $arr_input = array(
                    "from_district_id"  => $from_district_id,
                    "service_type_id"   => 2,
                    "to_district_id"    => (int)$district,
                    "to_ward_code"      => $ward,
                    "weight"            => $totalweight,
                    "height"            => (int)$height,
                    "length"            => (int)$length,
                    "width"             => (int)$width,
                    "insurance_fee"     => 0,
                    "coupon"            =>  null
                );
                $resp = $ims->site_func->apiGHN("Getfee", $arr_input, $arr_option['Token']);
                if (isset($resp->data->service_fee)) {
                    $output['ok'] = 1;
                    $get_price = $resp->data->service_fee;
                    $output['price_out_no_format'] = $get_price;
                    $output['price_out'] = $ims->func->get_price_format($get_price,0);
                }
            }else{
                $arr_price = $ims->func->unserialize ($shippingInfo['arr_price']);

                if(isset($arr_price) && is_array($arr_price) && !empty($arr_price)){
                    foreach($arr_price as $value){
                        $get_price = 0;
                        if($value['province']==$province && $value['district']==$district){
                            $output['ok'] = 1;
                            $get_price = $value['value'] + $value['value1'];
                            $output['price_out_no_format'] = $get_price;
                            $output['price_out'] = $ims->func->get_price_format($get_price,0);
                        }
                    }
                }
                if($output['ok'] == 0){
                    $output['ok'] = 3;
                    $output['price_out_no_format'] = $shippingInfo['price'];
                    $output['price_out'] = $ims->func->get_price_format($shippingInfo['price'] , 0);
                }
                if($total_money >= $shippingInfo['ototal_freeship']){
                    $output['ok'] = 2;
                    $output['price_out_no_format'] = 0;
                }
            }

            $promotion_code = Session::Get('promotion_code');
            if($promotion_code){
                require_once ("ordering_func.php");
                $this->orderiFunc = new OrderingFunc($this);
                $promotion_info = $this->orderiFunc->promotion_info($total_money, $promotion_code);
                if(isset($promotion_info['freeship']) && $promotion_info['freeship'] == 1){
                    $output['ok'] = 2;
                    $output['price_out_no_format'] = 0;
                    $output['price_out'] = 0;
                }
            }
            if(!empty($ims->setting['product']['is_freeship'])){
                $from = '';
                $to = $address.', '.$ims->func->location_name('ward', $ward).', '.$ims->func->location_name('district', $district).', '.$ims->func->location_name('province', $province);
                $arr_price = $ims->func->unserialize ($ims->setting['product']['arr_price']);
                if(!empty($arr_price) && is_array($arr_price)){
                    $radius = 0;
                    $arr_check = array();
                    foreach($arr_price as $key => $value){
                        $radius = $value['radius'];
                        if($value['province']==$province){
                            $arr_check[] = $key;
                            $from = $ims->db->load_item('product_order_address','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$value['warehouse'].'"','address');
                        }
                    }
                    if($total_money>=$ims->setting['product']['ototal_freeship']){
                        $tmp = $ims->func->getMultiDistance($from,$to,$arr_check);
                        if(!empty($tmp) && min($tmp)<=$radius){
                            $output['ok'] = 2;
                            $output['shipping_price0'] = $output['price_out_no_format'];
                            $output['price_out_no_format'] = 0;
                            $output['price_out'] = 0;
                        }
                    }
                }
            }
            $cart_info['shipping_fee'] = $output['price_out_no_format'];
            Session::Set ('shipping_price0', $output['shipping_price0']);
            Session::Set ('cart_info', $cart_info);
        }

        $methodInfo = $ims->db->load_row("order_method", " method_id = '".$method_id."' ".$ims->conf['where_lang']);

        if (!empty($methodInfo)) {
            $total_money = $total_money - $total_promotion - $total_wcoin;
            if($methodInfo['value_type'] == -1){
                if($output['price_out_no_format'] == -1){
                    $output['price_method'] = -round($total_money*$methodInfo['value']/100, -3);
                    $output['method_price_out'] = round($total_money*$methodInfo['value']/100, -3);
                    $output['method_value'] = '(-'.$methodInfo['value'].'%)';
                }
                else{
                    $output['price_method'] = -round(($total_money+$output['price_out_no_format'])*$methodInfo['value']/100, -3);
                    $output['method_price_out'] = round(($total_money+$output['price_out_no_format'])*$methodInfo['value']/100, -3);
                    $output['method_value'] = '(-'.$methodInfo['value'].'%)';
                }
            }
            else{
                if($output['price_out_no_format'] == 1){
                    $output['price_method'] = round($total_money*$methodInfo['value']/100, -3);
                    $output['method_price_out'] = round($total_money*$methodInfo['value']/100, -3);
                    $output['method_value'] = '(+'.$methodInfo['value'].'%)';
                }
                else{
                    $output['price_method'] = round(($total_money-$output['price_out_no_format'])*$methodInfo['value']/100, -3);
                    $output['method_price_out'] = round(($total_money-$output['price_out_no_format'])*$methodInfo['value']/100, -3);
                    $output['method_value'] = '(+'.$methodInfo['value'].'%)';
                }
            }
        }

        $output['total_payment'] = $total_money + $output['price_out_no_format'];
        return json_encode($output);
    }

    function do_updateCart () {
        global $ims;

        $output = array(
            'ok' => 1,
            'mess' => '',
            'mess_class' => 'success',
            'update_promotion' => 0
        );

        $arr_mess = array();

        $arr_cart = Session::Get('cart_pro');
        $arr_cart_list_pro = Session::Get('cart_list_pro');
        $arr_quantity = $ims->func->if_isset($ims->post['quantity'], array());

        if($ims->site_func->checkUserLogin() == 1) {
            $arr_cart = $ims->db->load_row_arr('product_order_temp','user_id="'.$ims->data['user_cur']['user_id'].'"');
            $arr_cart_list_pro = array();
            foreach ($arr_cart as $v) {
                $arr_cart_list_pro[$v['item_id']] = $v['item_id'];
            }
        }

        $value_max = 0;
        $promotion = $ims->db->load_row('promotion',' is_show=1 AND promotion_id="'.Session::Get('promotion_code').'" ');
        if(isset($promotion['value_type']) && $promotion['value_type']==1){
            $output['value_max'] = $promotion['value_max'];
        }

        $cartProduct = $ims->load_data->data_table (
            'product',
            'item_id', '*',
            ' FIND_IN_SET(item_id, "'.@implode(',', $arr_cart_list_pro).'")>0 '.$ims->conf['where_lang']
        );
        $cartOption = $ims->load_data->data_table(
            'product_option',
            'id', '*',
            ' FIND_IN_SET(ProductId, "'.@implode(',',$arr_cart_list_pro).'")>0 '.$ims->conf['where_lang']
        );

        foreach($arr_cart as $key => $value) {
            $option = $cartOption[$value["option_id"]];
            $product = $cartProduct[$value["item_id"]];
            if($ims->site_func->checkUserLogin() == 1) {
                $quantity = (isset($arr_quantity[$value['id']]) && $arr_quantity[$value['id']] > 0) ? $arr_quantity[$value['id']] : 0;
            }else{
                $quantity = (isset($arr_quantity[$key]) && $arr_quantity[$key] > 0) ? $arr_quantity[$key] : 0;
            }
            if ($option["useWarehouse"] == 1) {
                // Sử dụng kho hàng
                if ($option["is_OrderOutStock"] == 1) {
                    // Không giới hạn

                } else {
                    // Số lượng còn lại
                    if ($option["Quantity"] < $quantity && $option["Quantity"]>0) {
                        $quantity = $option["Quantity"];
                        $arr_mess[] = $ims->lang['product']['remaining_quantity_product'].$option["Quantity"];
                        $output['mess_class'] = 'warning';
                    } elseif($option["Quantity"] == 0) {
                        $ops = '';
                        if(!empty($option['Option1'])){
                            $ops .= ' '.$option['Option1'];
                        }
                        if(!empty($option['Option2'])){
                            $ops .= ' '.$option['Option2'];
                        }
                        if(!empty($option['Option3'])){
                            $ops .= ' '.$option['Option3'];
                        }
                        $arr_mess[] = str_replace('[title]', $product["title"].$ops, $ims->lang['product']['product_out_stock']);
                        $output['mess_class'] = 'error';
                    }
                }
            }
            $arr_cart[$key]['quantity'] = $quantity;
        }
        if($ims->site_func->checkUserLogin() == 1) {
            foreach ($arr_cart as $k => $v) {
                $up = array();
                $up['quantity'] = $v['quantity'];
                $up['date_update'] = time();
                $up['date_update'] = time();
                $ims->db->do_update('product_order_temp', $up, ' user_id="'.$ims->data['user_cur']['user_id'].'" AND id="'.$v['id'].'" ');
            }
            $ims->db->do_update('user', array('session_cart_pro'=>serialize($arr_cart)), ' user_id=' . $ims->data['user_cur']['user_id'] . ' ');
        }else{
            Session::Set ('cart_pro', $arr_cart);
        }

        $code = Session::Get('promotion_code', '');
        if($code){
            require_once ("ordering_func.php");
            $this->orderiFunc = new OrderingFunc($this);

            $promotion_info = $this->orderiFunc->promotion_info(0, $code);

            $output['update_promotion'] = 1;
            $output['promotion_mess'] = ($promotion_info['type'] == 1 && !empty($promotion_info['mess']) && $promotion_info['mess'] != $ims->lang['product']['freeship']) ? $ims->html->html_alert ($promotion_info['mess'], "warning") : '';
            $output['promotion_type_promotion'] = $promotion_info['type_promotion'];
            $output['promotion_value_type'] = $promotion_info['value_type'];
            $output['promotion_value'] = $promotion_info['value'];
            $output['promotion_value_max'] = $promotion_info['value_max'];
            $output['promotion_price']  = $promotion_info['price'];
            $output['promotion_price_min']  = isset($promotion_info['total_min'])?$promotion_info['total_min']:'';
            $output['promotion_id']  = isset($promotion_info['promotion_id'])?$promotion_info['promotion_id']:'';
            $output['freeship'] = isset($promotion_info['freeship']) ? $promotion_info['freeship'] : 0;
        }

        $output['mess'] = (count($arr_mess) > 0) ? implode('<br />', $arr_mess) : $ims->lang['global']['update_success'];

        return json_encode($output);
    }

    function do_getCart () {
        global $ims;

        $output = array(
            'num_cart' => 0
        );

        $arr_cart = Session::Get('cart_pro', array());
        if(!empty($arr_cart)){
            foreach($arr_cart as $key => $value) {
                $output['num_cart'] += $value['quantity'];
            }
        }
        $output['num_cart'] = number_format($output['num_cart']);
        if($ims->site_func->checkUserLogin() == 1) {
            $output['num_cart'] = 0;
            $arr_quantity = $ims->db->load_item_arr('product_order_temp', 'user_id="'.$ims->data['user_cur']['user_id'].'" AND is_show=1', 'quantity');
            if(!empty($arr_quantity)){
                foreach ($arr_quantity as $item){
                    $output['num_cart'] += $item['quantity'];
                }
            }
        }
        $output['num_cart'] = ($output['num_cart'] < 10) ? '0'.$output['num_cart'] : $output['num_cart'];
        return json_encode($output);
    }

    function do_cartRemoveItem () {
        global $ims;
        $output = array(
            'ok' => 0,
            'empty' => 0,
            'delete_promotion' => 0
        );

        $arr_cart  = Session::Get('cart_pro');
        $arr_cart_list_pro = Session::Get('cart_list_pro', array());
        $cart_item = $ims->func->if_isset($ims->post['cart_item']);
        if(!empty($cart_item)) {
            if($ims->site_func->checkUserLogin() == 1) {
//                $k = -1;
                $item_id = $ims->db->load_item('product_order_temp', 'is_show = 1 and id = '.$cart_item.' and user_id = '.$ims->data['user_cur']['user_id'], 'item_id');
//                if(in_array($item_id, $arr_cart_list_pro)){
//                    $k = array_search($item_id, $arr_cart_list_pro);
//                }
                foreach ($arr_cart as $key => $value){
                    if($value['item_id'] == $item_id){
                        $cart_id = $key;
                    }
                }
                $ok = $ims->db->query('DELETE FROM `product_order_temp` WHERE id = "'.$cart_item.'" AND user_id="'.$ims->data['user_cur']['user_id'].'" ');
                if ($ok) {
//                    if($k > -1){
//                        unset($arr_cart_list_pro[$k]);
//                        Session::Set ('cart_list_pro', $arr_cart_list_pro);
//                    }
                    if(isset($cart_id)){
                        unset($arr_cart[$cart_id]);
                        Session::Set ('cart_pro', $arr_cart);
                    }
                    $output['ok'] = 1;
                }
            } else{
                if(isset($arr_cart[$cart_item])) {
//                    $item_id = $arr_cart[$cart_item]['item_id'];
//                    unset($arr_cart_list_pro[$item_id]);
                    unset($arr_cart[$cart_item]);
                    Session::Set ('cart_pro', $arr_cart);
//                    Session::Set ('cart_list_pro', $arr_cart_list_pro);
                    $output['ok'] = 1;
                }
            }
            if(empty($arr_cart)){
                $output['empty'] = 1;
                Session::Set ('cart_info', array());
                Session::Set ('promotion_code', '');
            }else{
                require_once ("ordering_func.php");
                $this->orderiFunc = new OrderingFunc($this);
                $code = Session::Get('promotion_code', '');

                if($code){
                    $promotion_info = $this->orderiFunc->promotion_info(0, $code);
                    if($promotion_info['price'] == 0 && $promotion_info['mess'] != $ims->lang['product']['freeship']){
                        $output['delete_promotion'] = 1;
                        $output['promotion_mess'] = $ims->html->html_alert ($promotion_info['mess'], "warning");
                    }
                }
            }
        }

        return json_encode($output);
    }

    function do_addCart () {
        global $ims;
        $output = array(
            'ok' => 0,
            'mess' => '',
        );
        $arr_cart = Session::Get('cart_pro', array());
        $arr_cart_list_pro = Session::Get('cart_list_pro', array());
        $input = $ims->func->unserialize_array ($ims->func->if_isset($ims->post['data'], array()));

        if ($input['option_id'] == 0) {
            $output['mess'] = 'Vui lòng chọn thuộc tính sản phẩm';
            return json_encode($output);
        }
        $arr_tmp = array();
        foreach ($input as $key => $value) {
            if ($key == 'item_id') {
                $value = $value!="" ? $ims->func->base64_decode($value) : 0;
            }
            $arr_tmp[$key] = $value;
        }

        if(isset($input['option_id']) && isset($input['item_id']) && !empty($arr_tmp)) {
            $item_id   = $input['item_id']!="" ? $ims->func->base64_decode($input['item_id']) : 0;
            $option_id = ($input['option_id'] > 0) ? $input['option_id'] : 0;
            $quantity  = (isset($input['quantity']) && $input['quantity'] > 0) ? $input['quantity'] : 1;
            $list_gift = isset($input['list_gift']) ? $input['list_gift'] : 0;
            $op1 = $ims->func->if_isset($option1);
            $op2 = $ims->func->if_isset($option2);
            $op3 = $ims->func->if_isset($option3);
            $option = $ims->db->load_row('product_option','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and ProductId="'.$item_id.'" AND id="'.$option_id.'"');

            if(!empty($option)) {
                if($ims->site_func->checkUserLogin() == 1) {
                    $arr_cart = $ims->db->load_row_arr('product_order_temp','user_id="'.$ims->data['user_cur']['user_id'].'"');
                    $arr_cart_list_pro = array();
                    foreach ($arr_cart as $v) {
                        $arr_cart_list_pro[$v['item_id']] = $v['item_id'];
                    }
                }
                $cart_id = md5($option_id.'_op1'.$op1.'_op2'.$op2.'_op3'.$op3.'_r'.$list_gift);
                $check_quantity = isset($arr_cart[$cart_id]['quantity']) ? $arr_cart[$cart_id]['quantity'] + $quantity : $quantity;
                if($option['useWarehouse'] == 1 && $option['is_OrderOutStock'] == 0){
                    $num_max = $option['Quantity'];
                    if($num_max < $check_quantity) {
                        $quantity = 0;
                        if(isset($arr_cart[$cart_id])) {
                            $arr_cart[$cart_id]['quantity'] = $arr_tmp['quantity'];
                        }
                        $output['ok'] = 0;
                        if($num_max == 0){
                            $output['mess'] = $ims->lang['product']['products'].' '.$ims->lang['product']['out_of_stock'];
                        }else{
                            $output['mess'] = $ims->site_func->get_lang('not_enough_quantity', 'product', array('[num]' => '<b>'.$num_max.'</b>'));
                        }
                    }else{
                        $arr_cart_list_pro[$item_id] = $item_id;
                        if(isset($arr_cart[$cart_id])) {
                            $arr_cart[$cart_id]['quantity'] += $quantity;
                        } else {
                            $arr_cart[$cart_id] = $arr_tmp;
                        }
                        $output['ok'] = 1;
                        $output['mess'] = $ims->lang['product']['add_cart_success'];
                    }
                }else{
                    $arr_cart_list_pro[$item_id] = $item_id;
                    if(isset($arr_cart[$cart_id])) {
                        $arr_cart[$cart_id]['quantity'] += $quantity;
                    } else {
                        $arr_cart[$cart_id] = $arr_tmp;
                    }
                    $output['ok'] = 1;
                    $output['mess'] = $ims->lang['product']['add_cart_success'];
                }
                $arr_cart[$cart_id]['combo_info'] = '';
                Session::Set ('cart_pro', $arr_cart);
                Session::Set ('cart_list_pro', $arr_cart_list_pro);

                // Add cart tmp
                if($ims->site_func->checkUserLogin() == 1) {
                    $check_exist = $ims->db->load_row('product_order_temp',' item_id="'.$item_id.'" AND option_id="'.$option_id.'" AND user_id="'.$ims->data['user_cur']['user_id'].'" ');
                    if (!empty($check_exist)) {
                        // Đã có => Cập nhật
                        $col_tmp                = array();
                        $col_tmp['quantity']    = $quantity + $check_exist['quantity'];
                        $col_tmp['date_update'] = time();
                        $ims->db->do_update('product_order_temp', $col_tmp, ' id="'.$check_exist['id'].'" ');
                    }else{
                        // Chưa có => thêm mới
                        $col_tmp                = array();
                        $col_tmp['item_id']     = $item_id;
                        $col_tmp['quantity']    = $quantity;
                        $col_tmp['option_id']   = $option_id;
                        $col_tmp['user_id']     = $ims->data['user_cur']['user_id'];
                        $col_tmp['date_create'] = time();
                        $col_tmp['date_update'] = time();
                        $ims->db->do_insert('product_order_temp', $col_tmp);
                    }
                }
            }else{
                $output['mess'] = 'Vui lòng chọn thuộc tính sản phẩm';
                return json_encode($output);
            }
        }
        return json_encode($output);
    }

    function do_cartremovePromotionCode(){

        $output = array(
            'ok' => 1,
            'mess' => '',
        );
        Session::Set ('promotion_code', "");

        return json_encode($output);
    }

    function do_promotionCode(){
        global $ims;

        require_once ("ordering_func.php");
        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $ims->temp_box = new XTemplate($ims->path_html."box.tpl");
        $output = array(
            'ok' => 0,
            'mess' => $ims->lang['product']['promotion_success'],
        );

        $input = $ims->func->unserialize_array ($ims->func->if_isset($ims->post['data'], array()));
        $code 	    = $ims->func->if_isset($input['promotional_code']);

        require_once ("ordering_func.php");
        $this->orderiFunc = new OrderingFunc($this);

        $promotion_info    = $this->orderiFunc->promotion_info(0, $code);
            // print_r($promotion_info);
        if($promotion_info['mess'] == $ims->lang['product']['freeship'] || ($promotion_info['mess'] == '' && $promotion_info['price'] > 0)) {
            $output['ok'] = 1;
        }else{
            $output['mess'] = (!empty($promotion_info['mess'])) ? $ims->html->html_alert ($promotion_info['mess'], "warning") : '';
        }

        $output['promotion_type_promotion'] = $promotion_info['type_promotion'];
        $output['promotion_value_type'] = $promotion_info['value_type'];
        $output['promotion_value'] = $promotion_info['value'];
        $output['promotion_value_max'] = $promotion_info['value_max'];
        $output['promotion_price']  = $promotion_info['price'];
        $output['promotion_price_min']  = isset($promotion_info['total_min']) ? $promotion_info['total_min'] : '';
        $output['promotion_id'] = isset($promotion_info['promotion_id']) ? $promotion_info['promotion_id'] : '';
        $output['freeship'] = isset($promotion_info['freeship']) ? $promotion_info['freeship'] : 0;
//        $output['promotion_price_out'] = '-'.$ims->func->get_price_format($promotion_price, 0);
        $output['type'] = $promotion_info['type'];

        return json_encode($output);
    }

    function do_search_trademark (){
        global $ims;
        $result_array = array();
        $dInput = $ims->func->if_isset($ims->post["dInput"]);
        $sql = "SELECT * FROM product_brand WHERE is_show = 1 AND lang = '".$ims->conf['lang_cur']."' AND title LIKE '%".$dInput."%' ORDER BY show_order ASC, date_create ASC";
        $result = $ims->db->query($sql);
        $i = 0;
        while ($row = $ims->db->fetch_row($result)) {
            $row['num_product'] = $ims->db->do_get_num('product', " is_show = 1 AND brand_id = '".$row['brand_id']."' AND lang = '".$ims->conf['lang_cur']."' ");
            if ($row['num_product'] != 0) {
                array_push($result_array, $row);
            }
            $i++;
        }
        return json_encode($result_array);
    }

    function do_load_trademark (){
        global $ims;
        $result_array = array();
        $dInput = $ims->func->if_isset($ims->post["dInput"]);
        $arr_trademark = $ims->db->load_row_arr('product_brand','lang="'.$ims->conf['lang_cur'].'" and is_show=1 ORDER BY show_order ASC, date_create ASC');
        if($arr_trademark){
            $i = 0;
            foreach ($arr_trademark as $row) {
                $i++;
                $row['num_product'] = $ims->db->do_get_num('product', " is_show = 1 AND brand_id = '".$row['brand_id']."' AND lang = '".$ims->conf['lang_cur']."' ");
                if ($row['num_product'] != 0) {
                    array_push($result_array, $row);
                }
            }
        }
        return json_encode($result_array);
    }

    function do_loadProductVersion(){
        global $ims;
        $ims->load_data->data_color();
        $dir_view = $ims->func->dirModules('product', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view."product.tpl");
        $ims->temp_act->assign('CONF', $ims->conf);
        $ims->temp_act->assign('LANG', $ims->lang);
        $ims->temp_act->assign('DIR_IMAGE', $ims->dir_images);

        $output = array(
            'lvl' => '',
            'html' => array(),
        );
        $where      	 = '';
        $lang_cur   	 = $ims->conf['lang_cur'];
        $input_tmp  	 = $ims->func->if_isset($ims->post['data'], array());
        $item_id    	 = $ims->func->if_isset($ims->post['id']);
        $max        	 = $ims->func->if_isset($ims->post['max']);
        $thisClickOption = $ims->func->if_isset($ims->post['thisClickOption']);
        $thisClickValue  = $ims->func->if_isset($ims->post['thisClickValue']);
        $item_id         = $ims->func->base64_decode($item_id);

        $option_enabled = array();

        $choose_first = 0;
        $where_show = "";
        $selected_finished = 0;
        foreach($input_tmp as $key => $value) {
            if($value!=''){
                $where_show .= " AND ".$key."='".$value."' ";
            }
        }
        $option_selected = $ims->load_data->data_table(
            "product_option",
            "id",
            "id, Option1, Option2, Option3",
            " lang='".$lang_cur."' AND is_show=1 ".$where_show." ".$where." "
        );
        if($item_id){
            $info = $ims->db->load_row('product','lang="'.$lang_cur.'" and is_show=1 and item_id="'.$item_id.'"');
            $order_by = 'ORDER BY date_create DESC';
            if($info['field_option'] != ''){
                $order_by = ' ORDER BY '.$info['field_option'].', date_create DESC';
            }
            $where .= ' AND ProductId="'.$item_id.'" '.$order_by;
            $url_api = '';
            if (isset($input_tmp['Option1'])) {
                $url_api .= '&option1='.rawurlencode($input_tmp['Option1']);
            }else{
                $url_api .= '&option1=';
            }

            if (isset($input_tmp['Option2'])) {
                $url_api .= '&option2='.rawurlencode($input_tmp['Option2']);
            }else{
                $url_api .= '&option2=';
            }

            if (isset($input_tmp['Option3'])) {
                $url_api .= '&option3='.rawurlencode($input_tmp['Option3']);
            }else{
                $url_api .= '&option3=';
            }
            $url_api = $ims->conf['rooturl'].'restfulapi/v1/staging/api.php/getProductOption?item_id='.$item_id.$url_api;
            $token = $ims->db->load_item('api_token',' date_expired>"'.time().'" ', 'token');

            if ($token=="") {
                require_once ($ims->dir_lib_path.'firebase/BeforeValidException.php');
                require_once ($ims->dir_lib_path.'firebase/ExpiredException.php');
                require_once ($ims->dir_lib_path.'firebase/SignatureInvalidException.php');
                require_once ($ims->dir_lib_path.'firebase/JWT.php');

                $now_seconds = time();
                $private_key = "RnvuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t0tyazyZ8JXw+KgXTxldMPEL95";
                $payload = array(
                    "iss" => 1,
                    "iat" => $now_seconds,
                    "exp" => $now_seconds+(60*60*24*365),  // Maximum expiration time is 15 minutes
                );
                $jwt = JWT::encode($payload, $private_key, "HS256");
                // $ims->db->query("DELETE FROM jwt_token WHERE date_expired<'".time()."' ");
                $arr_ins 				 = array();
                $arr_ins['account_id'] 	 = 1;
                $arr_ins['token'] 		 = $token = $jwt;
                $arr_ins['date_expired'] = $now_seconds+(60*60*24*365);
                $arr_ins['date_create']  = $now_seconds;
                $arr_ins['date_update']  = $now_seconds;
                $ims->db->do_insert("api_token", $arr_ins);
            }
            $result = $ims->site_func->sendPostData($url_api, array(), 0, 'get', $token);
            $result = json_decode($result);
            if (!empty($result)) {
                $option_enabled = (array)$result->data;
            }
        }

        $option = array();
        $max_option = 0;
        if($info['arr_item'] != ''){
            $data['arr_item'] = $ims->func->unserialize($info['arr_item']);
            foreach ($data['arr_item'] as $k => $row) {
                if($row['SelectName'] == 'Custom'){
                    $row['title'] = $row['CustomName'];
                }else{
                    $row['title'] = $ims->func->if_isset($ims->lang['product']['option_'.strtolower($row['SelectName'])]);
                }
                $option[$k]['id'] = $k;
                $option[$k]['title'] = $row['title'];
                $option[$k]['group_id'] = strtolower($row['SelectName']);
                $option[$k]['group_name'] = 'option'.($k+1);
                $option[$k]['value'] = array();
                $max_option++;
            }
        }

        foreach ($input_tmp as $k => $v) {
            if ($v!="") {
                $max_option--;
            }
        }
        if ($max_option == 0) {
            $selected_finished = 1;
        }

        $arr_option = $ims->db->load_row_arr("product_option", "lang='".$lang_cur."' AND is_show=1". $where);
        if($arr_option){
            $i=0;
            foreach ($arr_option as $k => $v) {
                $i++;
                if($v['Option1'] != ''){
                    $option[0]['value'][$v['Option1']][] = $v['id'];
                }
                if($v['Option2'] != ''){
                    $option[1]['value'][$v['Option2']][] = $v['id'];
                }
                if($v['Option3'] != ''){
                    $option[2]['value'][$v['Option3']][] = $v['id'];
                }
                if ($selected_finished == 1 && isset($option_selected[$v['id']])) {
                    $output['item_code'] 	   = $v['SKU'];
                    $output['option_id']       = $v['id'];
                    $output['tracking_policy'] = $v['useWarehouse'];
                    $output['order_out_stock'] = $v['is_OrderOutStock'];

                    $useWarehouse = ($v['is_OrderOutStock'] == 1)? 0 : (int)$ims->setting['product']['use_ware_house'];
                    if($useWarehouse == 1){
                        $output['max_quantity'] = $v['Quantity'];
                    }else{
                        $output['max_quantity'] = 1000;
                    }
                    $output['price'] = $v['Price'];
                    $output['price_buy'] = $v['PriceBuy'];
                    $output['num_stock'] = isset($v['Quantity']) ? $v['Quantity'] : 0;
                    $output['price_text'] = number_format($output['price'],0,',','.').'đ';
                    $output['price_buy_text'] = number_format($output['price_buy'],0,',','.').'đ';
                    if($output['price_buy'] <= $output['price'] && $output['price']!=0){
                        $output['percent_discount'] = number_format(100-($output['price_buy']/$output['price']*100),1,'.','');
                        $output['amount_discount'] = $ims->func->get_price_format($output['price']-$output['price_buy']);
                    }
                    // Nếu sp hết hàng thì cập nhật lại các nút thêm vào giỏ hàng
                    if($useWarehouse == 1){
                        if($output['num_stock'] == 0){
                            $output["type_btn"] = "button";
                            $output['btn_add_cart'] = $output['item_status'] = $ims->lang['product']['status_stock0'];
                            $output['btn_order'] = $ims->lang['global']['price_empty'];
                        }else{
                            $output["type_btn"] = "submit";
                            $output['btn_order'] = $ims->lang['product']['btn_add_cart'];
                            $output['btn_add_cart'] = $ims->lang['product']['btn_add_cart_now'];
                            $output['item_status'] = $ims->lang['product']['status_stock1'];
                        }
                    }else{
                        $output["type_btn"] = "submit";
                        $output['btn_order'] = $ims->lang['product']['btn_add_cart'];
                        $output['btn_add_cart'] = $ims->lang['product']['btn_add_cart_now'];
                        $output['item_status'] = $ims->lang['product']['status_stock1'];
                    }
                }
            }
        }
        for($i=0; $i<count($option); $i++){
            $output['lvl'] = 0;
            $i_enabled = $i+1;
            $output['html']['op'.$i] = $this->group_option($option[($i)], $input_tmp, $i, $thisClickOption, $thisClickValue, $option_enabled['option'.$i_enabled]);
        }
        return json_encode($output);
    }

    function group_option($arr_option=array(), $arr_checked=array(), $stt=0, $thisClickOption="", $thisClickValue="", $option_enabled= array()){
        global $ims;

        $temp = 'version_ajax';
        $ims->temp_act->reset($temp);
        $output = '';
        $data = array();
        if(count($arr_option)>0){
            $data['title'] = isset($arr_option['title'])?$arr_option['title']:'';
            $data['group_id'] = isset($arr_option['group_id'])?$arr_option['group_id']:'';
            $data['group_name'] = isset($arr_option['group_name'])?$arr_option['group_name']:'';
            $data['selector'] = isset($arr_option['id'])?'selector-option-'.$arr_option['id']:'';
            if(isset($arr_option['value']) and count($arr_option['value'])>0){
                $i=0;
                foreach ($arr_option['value'] as $key => $value) {
                    $i++;
                    $row = array();
                    if (is_array($value)) {
                        $value = implode(',', $value);
                    }
                    $row['id'] = $value;
                    $row['name'] = $key;
                    $row['data_value'] = $key;
                    $row['title'] = '<span>'.$key.'</span>';
                    $row['group_id'] = $data['group_id'];
                    $row['group_name'] = $data['group_name'];
                    $row['data_option'] = isset($arr_option['id'])?'Option'.((int)$arr_option['id']+1):'';
                    $row['class'] = '';
                    // print_arr($row);
                    // for($i=1; $i<=3; $i++){
                    if($row['group_id'] == "color"){
                        $color = isset($ims->data['color'][$key])?$ims->data['color'][$key]:array();
                        $row['color_title'] = isset($color['title'])?strtolower($ims->func->vn_str_filter(trim($color['title']))):'';
                        $row['color'] = isset($color['color'])?$color['color']:'';
                        $row['data_color'] = 'data-color="'.str_replace(' ', '-', $row['color_title']).'"';
                        $row['title'] = '<span style="background-color: '.$row['color'].'" title="'.$key.'">'.$color['title'].'</span>';
                        $row['class'] = 'color';
                    }else{

                    }
                    if (empty($option_enabled)) {
                        // Option1
                        if (strtolower($key)==strtolower($thisClickValue)) {
                            $row['active'] = 'checked';
                        }
                        // print_arr($row);
                        // print_arr($thisClickValue);
                        // print_arr($thisClickOption);die;
                    }else{
                        if ($stt==0) {
                            // Option1
                            if (isset($arr_checked['Option1']) &&
                                $key==$arr_checked['Option1']) {
                                $row['active'] = 'checked';
                            }
                        }elseif ($stt==1) {
                            // Option2
                            if (isset($arr_checked['Option2']) &&
                                $key==$arr_checked['Option2']) {
                                $row['active'] = 'checked';
                            }
                        }elseif ($stt==2) {
                            // Option3
                            if (isset($arr_checked['Option3']) &&
                                $key==$arr_checked['Option3']) {
                                $row['active'] = 'checked';
                            }
                        }
                    }
                    // print_r($option_enabled->value);
                    // Không disable option không có
                    $check_disable = 0;
                    if (isset($option_enabled->value->$key)) {
                        $row['disabled'] = 'enabled';
                        $check_disable = 1;
                        // $row['id'] = $v;
                    }
                    if ($check_disable ==0) {
                        $row['disabled'] = 'disabled';
                    }
                    $ims->temp_act->assign('row', $row);
                    $ims->temp_act->parse($temp.'.row');
                }
                $ims->temp_act->assign('data',$data);
                $ims->temp_act->reset($temp);
                $ims->temp_act->parse($temp);
            }
        }
        return $ims->temp_act->text($temp);
    }

    function do_load_products_ajax(){
        global $ims;
        include_once($ims->conf["rootpath"].DS."config/site.php");
        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $ims->site = new Site($this);

        $output = array(
            'num' => 0,
            'html' => '',
            'filter_product' => ''
        );

        $where = '';
        $start = isset($ims->post['num_cur']) ? $ims->post['num_cur'] : '';
        $group_id = isset($ims->post['group_id']) ? $ims->post['group_id'] : 0;
        $order_by = isset($ims->post['order_by']) ? $ims->post['order_by'] : '';
        $sort = isset($ims->post['sort']) ? $ims->post['sort'] : '';
        $keyword = isset($ims->post['keyword']) ? $ims->post['keyword'] : '';
        $focus = isset($ims->post['focus']) ? $ims->post['focus'] : '';
        $num_list = $ims->setting['product']['num_list'];

        if($group_id != 0){
            $where .= ' and (find_in_set('.$group_id.', group_nav) or find_in_set('.$group_id.', group_related))';
        }

        $arr_key = explode(' ', $keyword);
        if (!empty($arr_key)) {
            $arr_tmp = array();
            foreach ($arr_key as $value) {
                $value = trim($value);
                if (!empty($value)) {
                    $arr_tmp[] = "title LIKE '%".$value."%'";
                }
            }
            if (count($arr_tmp) > 0) {
                $where .= " AND (".implode(" AND ", $arr_tmp).")";
            }
        }
        if($focus){
            $where .= ' and is_'.$focus.' = 1';
        }

        if($sort){
            parse_str($sort, $arr_sort);
            $output['filter_product'] = $this->filter_product($arr_sort);
            if(isset($arr_sort['brand']) && $arr_sort['brand']){
                $where .= ' and brand_id IN('.$arr_sort['brand'].') ';
            }
            if(isset($arr_sort['nature']) && $arr_sort['nature']){
                $nature = explode(',', $arr_sort['nature']);
                $where_nt = array();
                foreach ($nature as $nt){
                    $where_nt[] = 'find_in_set('.$nt.', arr_nature)';
                }
                $where .= ' and ('.implode(' OR ', $where_nt).') ';
            }
            if(isset($arr_sort['price']) && $arr_sort['price']){
                $price = explode('-', $arr_sort['price']);
                if ($price[1] == 0) {
                    $where .= ' AND price_buy >= '.$price[0];
                }else{
                    $where .= ' and (price_buy BETWEEN '.$price[0].' and '.$price[1].')';
                }
            }
            if(isset($arr_sort['origin']) && $arr_sort['origin']){
                $where .= ' and origin_id IN('.$arr_sort['origin'].') ';
            }
        }
        if($order_by != ''){
            $order_by = ' order by '.str_replace('-', ' ', $order_by);
        }else{
            $order_by = ' order by show_order desc, date_create desc';
        }

        $arr_in = array(
            'where' => $where.$order_by.' limit '.$start.','.$num_list,
            'paginate' => 0,
            'temp' => 'list_item_ajax',
            'ajax' => 1,
        );

        $output['total'] = $ims->db->do_get_num("product", 'is_show = 1 and combo_id = 0 and lang = "'.$ims->conf['lang_cur'].'"' . $where);
        $result_total = $ims->db->do_get_num("product", 'is_show = 1 and combo_id = 0 and lang = "'.$ims->conf['lang_cur'].'"' . $arr_in['where']);
        if(($start + $result_total) == $output['total']){
            $output['num'] = 0;
        }else{
            $output['num'] = $start + $result_total;
            $output['more'] = (($output['total'] - $output['num']) > $num_list) ? $num_list : $output['total'] - $output['num'];
        }
        $output['html'] = $ims->call->mFunc('product','html_list_item', array($arr_in));
        return json_encode($output);
    }
    function filter_product($sort){
        global $ims;

        $check = 0;
        $output = '';

        if(isset($sort['price']) && $sort['price'] != ''){
            $check = 1;
            $list_price = explode(',', $sort['price']);
            foreach ($list_price as $row){
                $price = explode('-', $row);
                $item['type'] = 'price';
                $item['value'] = $row;
                if($price[0] == 0){
                    $item['title'] = $ims->lang['global']['below'].' '.$ims->func->get_price_text($price[1]);
                }elseif($price[1] == 0){
                    $item['title'] = $ims->lang['global']['over'].' '.$ims->func->get_price_text($price[0]);
                }else{
                    $item['title'] = $ims->lang['global']['from'].' '.$ims->func->get_price_text($price[0]).' - '.$ims->func->get_price_text($price[1]);
                }
                $ims->temp_box->assign("item", $item);
                $ims->temp_box->parse("filter_product_top.filter_item");
            }
        }

        if(isset($sort['brand']) && $sort['brand'] != ''){
            $check = 1;
            $list_brand = $ims->db->load_item_arr('product_brand', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and brand_id IN ('.$sort['brand'].') order by show_order desc, title asc', 'title, brand_id');
            foreach ($list_brand as $item){
                $item['type'] = 'brand';
                $item['value'] = $item['brand_id'];
                $ims->temp_box->assign("item", $item);
                $ims->temp_box->parse("filter_product_top.filter_item");
            }
        }

        if(isset($sort['nature']) && $sort['nature'] != ''){
            $check = 1;
            $list_nature = $ims->db->load_item_arr('product_nature', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id IN ('.$sort['nature'].') order by show_order desc, title asc', 'title, item_id');
            foreach ($list_nature as $item){
                $item['type'] = 'nature';
                $item['value'] = $item['item_id'];
                $ims->temp_box->assign("item", $item);
                $ims->temp_box->parse("filter_product_top.filter_item");
            }
        }

        if(isset($sort['origin']) && $sort['origin'] != ''){
            $check = 1;
            $list_brand = $ims->db->load_item_arr('product_origin', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id IN ('.$sort['origin'].') order by show_order desc, title asc', 'title, item_id');
            foreach ($list_brand as $item){
                $item['type'] = 'origin';
                $item['value'] = $item['item_id'];
                $ims->temp_box->assign("item", $item);
                $ims->temp_box->parse("filter_product_top.filter_item");
            }
        }

        if(isset($sort['color']) && $sort['color'] != ''){
            $check = 1;
            $list_color = $ims->db->load_item_arr('product_color', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and color_id IN ('.$sort['color'].') order by show_order desc, title asc', 'title, color_id');
            foreach ($list_color as $item){
                $item['type'] = 'nature';
                $item['value'] = $item['color_id'];
                $ims->temp_box->assign("item", $item);
                $ims->temp_box->parse("filter_product_top.filter_item");
            }
        }
        if(isset($sort['tag']) && $sort['tag'] != ''){
            $check = 1;
            $list_tag = explode(',', $sort['tag']);
            foreach ($list_tag as $row){
                $item['type'] = 'tag';
                $item['value'] = $row;
                $ims->temp_box->assign("item", $item);
                $ims->temp_box->parse("filter_product_top.filter_item");
            }
        }

        if($check == 1){
            $ims->temp_box->assign("LANG", $ims->lang);
            $ims->temp_box->parse("filter_product_top");
            $output .= $ims->temp_box->text("filter_product_top");
        }
        return $output;
    }

    function load_gift_combo(){
        global $ims;
        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $dir_view = $ims->func->dirModules('product', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view."product.tpl");

        $ims->temp_act->assign('LANG', $ims->lang);
        $ims->func->load_language('product');
        $ims->site_func->setting('product');
        $output = array(
            'ok' => 0,
            'html' => '',
        );
        $combo_id = isset($ims->post['combo_id']) ? $ims->post['combo_id'] : 0;
        $arr_cart = Session::get('cart_pro', array());

        if($ims->site_func->checkUserLogin() == 1) {
            $arr_cart = $ims->db->load_row_arr('product_order_temp','user_id="'.$ims->data['user_cur']['user_id'].'"');
        }

        if($combo_id > 0){
            $combo = $ims->db->load_row('combo','is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id = "'.$combo_id.'"', 'item_id, title, arr_product, type, arr_gift, num_chose');
            if($combo){
                $disable_button = '';
                $product_combo = $ims->db->load_row('product', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and combo_id = '.$combo['item_id'], 'item_id');
                $err = 1;
                foreach ($arr_cart as $cart) {
                    if($product_combo['item_id'] == $cart['item_id']){
                        $err = 0;
                    }
                }
                $arr_gift = $ims->load_data->data_table('user_gift','item_id','item_id, title, picture, price, quantity_combo, 1 as active', 'lang = "'.$ims->conf['lang_cur'].'" and is_show = 1 and item_id IN('.$combo['arr_gift'].') and quantity_combo > 0 order by FIELD(item_id,"'.$combo['arr_gift'].'") desc');
                if($arr_gift){
                    $total = count($arr_gift);
                    foreach ($arr_gift as $row) {
                        $row['title'] = $ims->func->input_editor_decode($row['title']);
                        $row['picture'] = $ims->func->get_src_mod($row['picture']);
                        $row['type'] = $ims->lang['product']['combo_type_'.$combo['type']];
                        $row['price'] = ($row['price'] > 0) ? '<div class="price">'.number_format($row['price'], 0, ',', '.').'đ'.'</div>' : '';
                        $row['price_buy'] = '0đ';
                        $row['disabled'] = 'disabled';
                        $row['input'] = '';
                        if($err == 0){
                            $row['disabled'] = '';
                            $row['input'] = '<input class="checkbox" type="checkbox" id="cb_'.$row['item_id'].'" value="'.$row['item_id'].'" name="selected_id[]" '.$row['disabled'].'>';
                        }
                        $ims->temp_act->assign('row', $row);
                        $ims->temp_act->parse("list_gift_combo.row");
                    }
                    $combo['link_go'] = $ims->site_func->get_link('product', $ims->setting['product']['ordering_cart_link']);
                    $combo['type'] = ($combo['type'] == 1) ? '' : (($combo['type'] == 0) ? 'gift' : 'include');
                    if($err == 1){
                        $note = $ims->lang['product']['list_include_note'];
                        $disable_button = 'disabled';
                    }else{
                        $combo['num_chose'] = ($total < $combo['num_chose']) ? $total : $combo['num_chose'];
                        $note = $ims->site_func->get_lang('note_chose_gift', 'product', array('[num]' => $combo['num_chose']));
                        $ims->temp_act->assign('data', $combo);
                        $ims->temp_act->parse("list_gift_combo.chose");
                    }
                    $ims->temp_act->assign('disable_button', $disable_button);
                    $ims->temp_act->assign('note', $note);
                    $ims->temp_act->assign('data', $combo);
                    $ims->temp_act->parse("list_gift_combo");
                    $output['html'] .= $ims->temp_act->text("list_gift_combo");
                }else{
                    $output['html'] .= $ims->lang['product']['out_of_gift'];
                }
            }
        }
        return json_encode($output);
    }
    function load_include_combo(){
        global $ims;
        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $dir_view = $ims->func->dirModules('product', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view."product.tpl");

        $ims->temp_act->assign('LANG', $ims->lang);
        $ims->func->load_language('product');
        $ims->site_func->setting('product');
        $output = array(
            'ok' => 0,
            'html' => '',
        );
        $combo_id = isset($ims->post['combo_id'])?$ims->post['combo_id']:'0';
        $arr_cart = Session::get('cart_pro', array());

        if($ims->site_func->checkUserLogin() == 1) {
            $arr_cart = $ims->db->load_row_arr('product_order_temp','user_id="'.$ims->data['user_cur']['user_id'].'"');
        }

        if($combo_id > 0){
            $combo = $ims->db->load_row('combo','is_show = 1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$combo_id.'"','item_id, title, arr_product, type, value, value_type, arr_gift, arr_include, num_chose');
            if($combo){
                $disable_button = '';
                $err = 1;
                foreach ($arr_cart as $v) {
                    $product_combo = $ims->db->load_row('product','is_show = 1 and lang="'.$ims->conf['lang_cur'].'" and combo_id="'.$combo['item_id'].'"','item_id, title, price_buy, quantity_combo');
                    if($product_combo['item_id'] == $v['item_id']){
                        $err = 0;
                    }
                }
                $arr_include = $ims->db->load_item_arr('product','lang="'.$ims->conf['lang_cur'].'" and is_show = 1 and item_id IN('.$combo['arr_include'].') and quantity_include > 0 order by FIELD(item_id,"'.$combo['arr_include'].'") desc', 'item_id, title, picture, price, price_buy');
                if($arr_include){
                    $total = count($arr_include);
                    foreach ($arr_include as $row) {
                        $row['title'] = $ims->func->input_editor_decode($row['title']);
                        $row['picture'] = $ims->func->get_src_mod($row['picture']);
                        $row['type'] = $ims->lang['product']['combo_type_'.$combo['type']];
                        $row['price'] = number_format($row['price_buy'], 0,',','.').'đ';
                        $price_buy = ($combo['value_type'] == 1) ? $row['price_buy']*((100 - $combo['value'])/100) : ($row['price_buy'] - $combo['value']);
                        if($price_buy < 0){
                            $price_buy = 0;
                        }
                        $row['price_buy'] = number_format($price_buy, 0,',','.').'đ';
                        $row['parent_product'] = $combo['arr_product'];
                        $row['disabled'] = 'disabled';
                        $row['input'] = '';
                        if($err == 0){
                            $row['disabled'] = '';
                            $row['input'] = '<input class="checkbox" type="checkbox" id="cb_'.$row['item_id'].'" value="'.$row['item_id'].'" name="selected_id[]" '.$row['disabled'].'>';
                        }

                        $ims->temp_act->assign('row', $row);
                        $ims->temp_act->parse("list_include_combo.row");
                    }
                    $combo['type'] = ($combo['type'] == 1) ? '' : (($combo['type'] == 0) ? 'gift' : 'include');
                    $combo['link_go'] = $ims->site_func->get_link('product',$ims->setting['product']['ordering_cart_link']);
                    if($err == 1){
                        $note = $ims->lang['product']['list_include_note'];
                        $disable_button = 'disabled';
                    }else{
                        $combo['num_chose'] = ($total < $combo['num_chose']) ? $total : $combo['num_chose'];
                        $note = $ims->site_func->get_lang('note_chose_include', 'product', array('[num]' => $combo['num_chose']));
                        $ims->temp_act->assign('data', $combo);
                        $ims->temp_act->parse("list_include_combo.chose");
                    }
                    $ims->temp_act->assign('disable_button', $disable_button);
                    $ims->temp_act->assign('note', $note);
                    $ims->temp_act->assign('data', $combo);
                    $ims->temp_act->parse("list_include_combo");
                    $output['html'] .= $ims->temp_act->text("list_include_combo");
                }else{
                    $output['html'] .= $ims->lang['product']['out_of_include'];
                }
            }
        }
        return json_encode($output);
    }
    function update_cart_combo(){
        global $ims;
        $ims->func->load_language('product');
        $output = array(
            'ok' => 0,
            'mess' => '',
            'html' => '',
            'text' => ''
        );
        $data = isset($ims->post['data']) ? implode(',', $ims->post['data']) : '';
        $type = isset($ims->post['type']) ? $ims->post['type'] : '';
        $combo_id = isset($ims->post['combo_id']) ? $ims->post['combo_id'] : 0;
        $arr_cart = Session::get('cart_pro', array());

        if($ims->site_func->checkUserLogin() == 1) {
            $arr_cart = $ims->db->load_row_arr('product_order_temp','user_id="'.$ims->data['user_cur']['user_id'].'"');
            $arr_cart_list_pro = array();
            foreach ($arr_cart as $v) {
                $arr_cart_list_pro[$v['item_id']] = $v['item_id'];
            }
        }

        $table_query = ($type == 'gift') ? 'user_gift' : 'product';
        if($data != ''){
            $quantity_check = ($table_query == 'product') ? 'quantity_include' : 'quantity_combo';
            $gift_include = $ims->db->load_item_arr($table_query, 'lang = "'.$ims->conf['lang_cur'].'" and is_show = 1 and item_id IN('.$data.')', 'item_id, title, '.$quantity_check);
            if($combo_id > 0 && count($gift_include) > 0){
                foreach ($arr_cart as $k => $v) {
                    $cb_id = $ims->db->load_item('product', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id = '.$v['item_id'], 'combo_id');
                    $combo_info = array();
                    if($ims->site_func->checkUserLogin() == 1) {
                        if($cb_id == $combo_id){
                            foreach ($gift_include as $item){
                                if($item[$quantity_check] <= 0){
                                    $output['mess'] = $ims->site_func->get_lang($type.'_out_of_stock','product',array('['.$type.']' => $item['title']));
                                }
                            }
                            if(empty($output['mess'])){
                                $combo_info[$type.'_id'] = $data;
                                $col_tmp['combo_info'] = $ims->func->serialize($combo_info);
                                $col_tmp['date_update'] = time();
                                $ims->db->do_update('product_order_temp', $col_tmp, ' id="'.$v['id'].'" ');
                            }
                        }
                    }elseif($cb_id == $combo_id){
                        foreach ($gift_include as $item){
                            if($item[$quantity_check] <= 0){
                                $output['mess'] = $ims->site_func->get_lang($type.'_out_of_stock','product',array('['.$type.']' => $item['title']));
                            }
                        }
                        if(empty($output['mess'])){
                            $combo_info[$type.'_id'] = $data;
                            $arr_cart[$k]['combo_info'] = $ims->func->serialize($combo_info);
                        }
                    }
                }
                if(empty($output['mess'])){
                    $output['ok'] = 1;
                    $output['mess'] = $ims->lang['product']['success_'.$type];
                    if($ims->site_func->checkUserLogin() == 0) {
                        Session::set ('cart_pro', $arr_cart);
                    }
                    // Load html combo dành cho trang giỏ hàng
                    $html_include_gift_cart = $this->html_include_gift_cart($combo_id, $type);
                    $output['html'] = $html_include_gift_cart['html'];
                    $output['text'] = $html_include_gift_cart['text'];
                }
            }
        }else{
            $output['mess'] = $ims->lang['product']['not_yet_chose_'.$type];
        }

        return json_encode($output);
    }
    function html_include_gift_cart($combo_id, $type){
        global $ims;
        $out = array(
            'html' => '',
            'text' => ''
        );

        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $dir_view = $ims->func->dirModules('product', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view."ordering_cart.tpl");
        $ims->temp_act->assign('LANG', $ims->lang);
        $arr_cart = Session::get('cart_pro', array());

        if($ims->site_func->checkUserLogin() == 1) {
            $arr_cart = $ims->db->load_row_arr('product_order_temp','user_id="'.$ims->data['user_cur']['user_id'].'"');
        }
        foreach ($arr_cart as $k => $v) {
            if($v['combo_info'] != ''){
                $combo_info =  $ims->func->unserialize($v['combo_info']);
                $k_combo = array_keys($combo_info);
                $k_combo = str_replace('_id', '', $k_combo[0]);
                $v_combo = array_values($combo_info);
                $arr_cart[$k][$k_combo] = $v_combo[0];
            }
        }
        $item_id = $ims->db->load_item('product', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and combo_id = '.$combo_id, 'item_id');
        foreach ($arr_cart as $row){
            $row['type'] = $type;
            if($row['item_id'] == $item_id){
                $combo = $ims->db->load_row('combo', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id="'.$combo_id.'"','item_id, title, type, value, value_type');
                if($combo['type'] != 1){
                    $out['text'] = $ims->lang['product']['change_'.$type];

                    if(isset($row['gift']) && $row['gift'] != ''){
                        $arr_gift = $ims->db->load_item_arr('user_gift', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id IN ('.$row['gift'].') order by FIELD(item_id,"'.$row['gift'].'") desc', 'title, picture, product_id');
                        if($arr_gift){
                            foreach ($arr_gift as $gift){
                                $gift['picture'] = $ims->func->get_src_mod($gift['picture']);
                                if($gift['product_id'] > 0){
                                    $link = $ims->db->load_item('product', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id = '.$gift['product_id'], 'friendly_link');
                                    $gift['link'] = 'href="'.$ims->func->get_link($link, '').'"';
                                }else{
                                    $gift['link'] = '';
                                }
                                $ims->temp_act->assign('gift', $gift);
                                $ims->temp_act->parse("combo_gift_include.ul.gift");
                            }
                            $ims->temp_act->assign('row', $row);
                            $ims->temp_act->reset("combo_gift_include.ul");
                            $ims->temp_act->parse("combo_gift_include.ul");
                            $out['html'] = $ims->temp_act->text("combo_gift_include.ul");
                        }
                    }
                    if(isset($row['include']) && $row['include'] != ''){
                        $arr_include = $ims->db->load_item_arr('product', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id IN('.$row['include'].') order by FIELD(item_id,"'.$row['include'].'") desc', 'title, picture, price_buy, friendly_link');
                        if($arr_include){
                            foreach ($arr_include as $include){
                                $include['title'] = $ims->func->input_editor_decode($include['title']);
                                $include['picture'] = $ims->func->get_src_mod($include['picture']);
                                if($include['price_buy'] == 0){
                                    $include['class_price'] = 'd-none';
                                }
                                $include['price'] = number_format($include['price_buy'],0,',','.').'đ';
                                $include['price_buy'] = ($combo['value_type'] == 1) ? $include['price_buy']*((100 - $combo['value'])/100) : ($include['price_buy'] - $combo['value']);
                                if($include['price_buy'] < 0){
                                    $include['price_buy'] = 0;
                                }
                                $include['price_buy_text'] = number_format($include['price_buy'],0,',','.').'đ';
                                $include['link'] = $ims->func->get_link($include['friendly_link'], '');
                                $ims->temp_act->assign('incl', $include);
                                $ims->temp_act->parse("combo_gift_include.ul.include");
                            }
                            $ims->temp_act->assign('row', $row);
                            $ims->temp_act->reset("combo_gift_include.ul");
                            $ims->temp_act->parse("combo_gift_include.ul");
                            $out['html'] = $ims->temp_act->text("combo_gift_include.ul");
                        }
                    }
                }
            }
        }

        return $out;
    }
    function delete_gift_include(){
        global $ims;
        $ims->func->load_language('product');
        $output = array(
            'ok' => 1
        );

        $type = isset($ims->post['type']) ? $ims->post['type'] : '';
        $item_id = isset($ims->post['item_id']) ? $ims->post['item_id'] : 0;
        $arr_cart = Session::get('cart_pro', array());

        $output['text'] = $ims->lang['product']['choose'].' '.mb_strtolower($ims->lang['product'][$type]);
        if($ims->site_func->checkUserLogin() == 1) {
            $arr_cart = $ims->db->load_row_arr('product_order_temp','user_id="'.$ims->data['user_cur']['user_id'].'"');
        }

        foreach ($arr_cart as $k => $v){
            if($v['item_id'] == $item_id){
                if($ims->site_func->checkUserLogin() == 1) {
                    $col_tmp['combo_info'] = '';
                    $col_tmp['date_update'] = time();
                    $ims->db->do_update('product_order_temp', $col_tmp, ' id="'.$v['id'].'" ');
                }else{
                    $arr_cart[$k]['combo_info'] = '';
                    Session::set ('cart_pro', $arr_cart);
                }
            }
        }
        return json_encode($output);
    }

    // End class
    function do_add_edit_concern(){
        global $ims;
        include_once($ims->conf["rootpath"].DS."config/site.php");
        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $ims->site = new Site($this);

        $out = array(
            'ok' => 0,
            'mess' => ''
        );

        if($ims->site_func->checkUserLogin() == 1) {
            $item_id = isset($ims->post['item']) ? $ims->func->base64_decode($ims->post['item']) : 0;
            $lang_cur = $ims->post['lang_cur'];
            $picture_available = isset($ims->post['picture_available']) ? $ims->post['picture_available'] : '';

            $arr_in = array();
            if($ims->post['province'] == ''){
                $out['mess'] = $ims->lang['global']['province_require'];
            }elseif($ims->post['district'] == ''){
                $out['mess'] = $ims->lang['global']['district_require'];
            }elseif($ims->post['ward'] == ''){
                $out['mess'] = $ims->lang['global']['ward_require'];
            }else{
                if(!empty($ims->post['phone'])){
                    $ims->post['phone'] = str_replace(' ', '',$ims->post['phone']);
                    $code_phone = substr($ims->post['phone'],0,3);
                    if($code_phone == '+84'){
                        $phone = substr($ims->post['phone'],3);
                        if(substr($phone,0,1) == 0){
                            $out['mess'] = 'Số điện thoại không hợp lệ';
                        }elseif(strlen($phone) != 9 || !preg_match ("/^[0-9]*$/", $phone)){
                            $out['mess'] = 'Số điện thoại không hợp lệ';
                        }else{
//                            $ims->post['phone'] = '0'.$phone;
                        }
                    }else{
                        $first = substr($ims->post['phone'],0,1);
                        if($first != 0){
                            $out['mess'] = 'Số điện thoại không hợp lệ';
                        }elseif(strlen($ims->post['phone']) != 10 || !preg_match ("/^[0-9]*$/", $ims->post['phone'])){
                            $out['mess'] = 'Số điện thoại không hợp lệ';
                        }
                    }
                }
            }
            if(!empty($ims->post['email']) && $out['mess'] == ''){
                if(!filter_var($ims->post['email'], FILTER_VALIDATE_EMAIL)) {
                    $out['mess'] = 'Email không hợp lệ';
                }
            }
            if($out['mess'] == ''){
                if(isset($_FILES['picture']) && $_FILES['picture']['error'] == 4 && $picture_available == ''){
                    $out['mess']  = 'Vui lòng tải lên ảnh đại diện của bạn!';
                }else{
                    if(isset($_FILES['picture']) && $_FILES['picture']['error'] == 0){
                        $folder_upload = "user/".$ims->data['user_cur']['folder_upload'].'/'.date('Y_m',time()).'/concern';
                        $pic_result = $ims->site_func->upload_image($folder_upload, 'picture');
                        if($pic_result['ok'] == 1){
                            $arr_in['picture'] = $pic_result['url_picture'];
                        }else{
                            $out['mess'] = $pic_result['mess'];
                        }
                    }else{
                        $arr_in['picture'] = $picture_available;
                    }
                }
            }
            if($out['mess'] == ''){
                $arr_in['title'] = $ims->post['title'];
                $arr_in['tax_number'] = !empty($ims->post['tax_number']) ? $ims->post['tax_number'] : '';
                $arr_in['country'] = !empty($ims->post['country']) ? $ims->post['country'] : '';
                $arr_in['province'] = !empty($ims->post['province']) ? $ims->post['province'] : '';
                $arr_in['district'] = !empty($ims->post['district']) ? $ims->post['district'] : '';
                $arr_in['ward'] = !empty($ims->post['ward']) ? $ims->post['ward'] : '';
                $arr_in['address'] = !empty($ims->post['address']) ? $ims->post['address'] : '';
                $arr_in['phone'] = !empty($ims->post['phone']) ? $ims->post['phone'] : '';
                $arr_in['email'] = !empty($ims->post['email']) ? $ims->post['email'] : '';
                $arr_in['website'] = !empty($ims->post['website']) ? $ims->post['website'] : '';
                $arr_in['date_update'] = time();
                if(!$item_id){
                    $arr_in['item_id'] = $ims->db->getAutoIncrement('product_concern');
                    $arr_in['user_id'] = $ims->data['user_cur']['user_id'];
                    $arr_in['show_order'] = 0;
                    $arr_in['is_show'] = 1;
                    $arr_in['date_create'] = time();
                    $list_lang = $ims->db->load_item_arr('lang', 'is_show = 1 order by is_default desc, show_order desc', 'name');
                    foreach ($list_lang as $lang){
                        $arr_in['lang'] = $lang['name'];
                        $ok = $ims->db->do_insert("product_concern", $arr_in);
                    }
                    if($ok){
                        $out['ok'] = 1;
                        $out['mess'] = 'Thêm mới doanh nghiệp thành công!';
                    }
                }else{
                    $ok = $ims->db->do_update("product_concern", $arr_in, 'item_id = '.$item_id.' and lang = "'.$lang_cur.'" and user_id = '.$ims->data['user_cur']['user_id']);
                    if($ok){
                        $out['ok'] = 1;
                        $out['mess'] = 'Cập nhật doanh nghiệp thành công!';
                    }
                }
            }
        }else{
            $out['mess'] = $ims->lang['global']['need_login'];
        }
        return json_encode($out);
    }
    function do_delete_concern(){
        global $ims;
        $out = array(
            'ok' => 0,
            'mess' => ''
        );
        if ($ims->site_func->checkUserLogin() == 1) {
            $item_id = isset($ims->post['item']) ? $ims->func->base64_decode($ims->post['item']) : 0;
            $check = $ims->db->load_item('product_concern', 'is_show = 1 and item_id = '.$item_id.' and user_id = '.$ims->data['user_cur']['user_id'], 'item_id');
            if($check){
                $ok = $ims->db->do_update("product_concern", array('is_show' => 0, 'date_update' => time()), 'item_id = '.$item_id.' and user_id = '.$ims->data['user_cur']['user_id']);
                if($ok){
                    $out['ok'] = 1;
                    $out['mess'] = 'Xóa doanh nghiệp thành công';
                }
            }
        }else{
            $out['mess'] = $ims->lang['global']['need_login'];
        }
        return json_encode($out);
    }

    function do_delete_list_concern(){
        global $ims;
        $out = array(
            'ok' => 0,
            'mess' => ''
        );

        if ($ims->site_func->checkUserLogin() == 1) {
            $arr_item = isset($ims->post['arit']) ? $ims->post['arit'] : array();
            if(!empty($arr_item)){
                foreach ($arr_item as $item){
                    $item_id = $ims->func->base64_decode($item);
                    $check = $ims->db->load_item('product_concern', 'is_show = 1 and item_id = '.$item_id.' and user_id = '.$ims->data['user_cur']['user_id'], 'item_id');
                    if($check){
                        $ok = $ims->db->do_update("product_concern", array('is_show' => 0, 'date_update' => time()), 'item_id = '.$item_id.' and user_id = '.$ims->data['user_cur']['user_id']);
                    }
                }
                if($ok){
                    $out['ok'] = 1;
                    $out['mess'] = 'Xóa doanh nghiệp thành công';
                }
            }
        }else{
            $out['mess'] = $ims->lang['global']['need_login'];
        }
        return json_encode($out);
    }
}
?>