<?php
if (!defined('IN_ims')) { die('Access denied'); }

class OrderingFunc {

    public $modules     = "ordering";
    public $parent      = null;
    public $parent_mod  = "ordering";
    public $parent_act  = "ordering";
    public $temp_act    = "";

    public function __construct($parent = null) {
        global $ims;

        return true;
    }

	function promotion_info ($cart_total, &$code) {
	   	global $ims;
	   	if(!isset($ims->lang['product'])){
	   	    $ims->func->load_language('product');
        }

	   	$output = array(
		    'price'	 	   => 0,
		    'promotion_id' => 0,
		    'value' 	   => 0,
		    'value_type'   => 0,
		    'value_max'    => 0,
		    'percent' 	   => 0,
		    'mess'    	   => $ims->lang['product']['err_promotion_wrong'],
            'type'         => 1
	   	);
	   
	   	$err_promotion = '';

		$arr_cart 		   = Session::Get('cart_pro', array());		
//		$arr_cart_list_pro = Session::Get('cart_list_pro');
        $arr_cart_list_pro = array();

		// giỏ hàng tạm
		if($ims->site_func->checkUserLogin() == 1) {
	        $arr_cart = $ims->db->load_row_arr('product_order_temp','user_id="'.$ims->data['user_cur']['user_id'].'"');
	        foreach ($arr_cart as $v) {
	        	$arr_cart_list_pro[$v['item_id']] = $v['item_id'];
	        }
	    }else{
		    foreach ($arr_cart as $item){
                $arr_cart_list_pro[$item['item_id']] = $item['item_id'];
            }
        }
	    if (empty($arr_cart)) {
	    	$output['mess'] = $ims->lang['product']['cart_empty'];
	   		return $output;
	    }
	    $cartProduct = $ims->load_data->data_table (
			'product', 
			'item_id', '*', 
			' FIND_IN_SET(item_id, "'.@implode(',', $arr_cart_list_pro).'")>0 '.$ims->conf['where_lang']
		);
		$cartOption = $ims->load_data->data_table(
			'product_option',
			'id', '*',
			' FIND_IN_SET(ProductId, "'.@implode(',', $arr_cart_list_pro).'")>0 '.$ims->conf['where_lang']
		);

        $cart_total = 0;
        $cart_total_include_combo = 0;
        foreach ($arr_cart as $k => $v) {
            $option  = $ims->func->if_isset($cartOption[$v['option_id']], array());
            $v['price_buy'] = $option['PriceBuy'];
            $v['total'] = $v['quantity'] * $v['price_buy'];            
            if($cartProduct[$v['item_id']]['is_use_promotion_code'] != 2){
                $cart_total += $v['total'];
                // Tính thêm tiền cho các sp mua kèm theo combo
                $gift_include = $this->combo_gift_include($v);
                $cart_total_include_combo += $v['total'];
                $cart_total_include_combo += $gift_include['add_payment'];
            }
        }
        if(empty($cart_total_include_combo)){
            $err_promotion = $ims->lang['product']['err_promotion_total_price'];
        }
	   	$promotion_code = (isset($code) && $code) ? trim($code) : Session::Get('promotion_code');
        $code = $ims->db->load_row("promotion", " (is_show=1 or is_show=2) AND promotion_id='".$promotion_code."' ");

        if (!empty($code)) {
            $check_use = array();
            $cancel_order = $ims->db->load_item('product_order_status', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and is_cancel = 1', 'item_id');
            $check_product_apply = 0;
            $list_product = array();

            // áp dụng cho email hoặc thành viên
            if($code['is_show'] == 2){
                $arr_cart = Session::Get('cart_pro', array());
                // Sử dụng giỏ hàng tạm
                if($ims->site_func->checkUserLogin() == 1) {
                    $arr_cart = $ims->db->load_row_arr('product_order_temp','user_id="'.$ims->data['user_cur']['user_id'].'"');
                }

                $total_item_cart = 0;
                foreach ($arr_cart as $item){
                    $total_item_cart += $item['quantity'];
                }
                if($total_item_cart < $ims->setting['product']['min_cart_item_discount']){
                    $err_promotion = $ims->site_func->get_lang('not_enough_num_product', 'product', array('[num]' => $ims->setting['product']['min_cart_item_discount']));
                }
                $output['type'] = 2;
            }else{
                if ($code['type_promotion'] == 'apply_email' || $code['type_promotion'] == 'apply_user'){
                    if($ims->site_func->checkUserLogin() != 1) {
                        $err_promotion = $ims->lang['product']['err_promotion_login'];
                    }else{
                        $err_promotion = $ims->lang['product']['err_promotion_user'];
                        // áp dụng cho email
//                    if($code['type_promotion'] == 'apply_email' && isset($ims->data['user_cur']['email'])){
//                        if (in_array($ims->data['user_cur']['email'], explode(',',$code['list_email']))){
//                            $err_promotion = '';
//                        }
//                        $check_use_tmp = $ims->db->load_item_arr('product_order as po, product_order_detail as pod', 'po.lang = "'.$ims->conf['lang_cur'].'" and po.is_status != '.$cancel_order.' and po.promotion_id = "'.$promotion_code.'" and po.order_id = pod.order_id and pod.type_id IN('.$code['list_product'].')', 'pod.type_id');
//                        if($check_use_tmp){
//                            foreach ($check_use_tmp as $item){
//                                $check_use[] = $item['type_id'];
//                            }
//                            $check_use = array_count_values($check_use); // Mảng kiểm tra số lần sử dụng mã cho từng user
//                        }
//
//                    }else
                        // áp dụng cho thành viên
                        if($code['type_promotion'] == 'apply_user' && isset($ims->data['user_cur']['user_id'])){
                            if(in_array($ims->data['user_cur']['user_id'], explode(',',$code['list_user']))){
                                $err_promotion = '';
                            }
                            $total_use = $ims->db->load_item_arr('product_order', 'lang = "'.$ims->conf['lang_cur'].'" and is_status != '.$cancel_order.' and promotion_id = "'.$promotion_code.'" and user_id = '.$ims->data['user_cur']['user_id'], 'order_id');
                            if(count($total_use) >= $code['max_use']){
                                $err_promotion = $ims->lang['product']['err_promotion_numover'];
                            }
                        }
                    }
                }elseif ($code['type_promotion'] == 'apply_product'){
                    $check_use_tmp = $ims->db->load_item_arr('product_order as po, product_order_detail as pod', 'po.lang = "'.$ims->conf['lang_cur'].'" and po.is_status != '.$cancel_order.' and po.promotion_id = "'.$promotion_code.'" and po.order_id = pod.order_id and pod.type_id IN('.$code['list_product'].')', 'pod.type_id');
                    if($check_use_tmp){
                        foreach ($check_use_tmp as $item){
                            $check_use[] = $item['type_id'];
                        }
                        $check_use = array_count_values($check_use); // Mảng kiểm tra số lần sử dụng mã cho từng sp
                    }
                    $err_promotion = $ims->lang['product']['err_promotion_product'];

                    foreach($arr_cart_list_pro as $product) {
                        // sản phẩm được áp dụng có trong giỏ hàng
                        if(in_array($product, explode(',', $code['list_product']))){
                            $err_promotion = '';
                            if(isset($check_use[$product])){
                                if($check_use[$product] < $code['max_use']){
                                    $list_product[] = $product;
                                }
                            }else{
                                $list_product[] = $product;
                            }
                            if(empty($list_product)){
                                $err_promotion = $ims->lang['product']['err_promotion_numover'];
                            }else{
                                $check_product_apply = 1;
                            }
                        }
                    }
                }
            }

            if($err_promotion == ''){
                // chưa tới ngày sử dụng
                if($code['date_start'] > time()) {
                    $err_promotion = $ims->lang['product']['err_promotion_notyet_timetouse'];

                    // mã hết hạn
                }elseif($code['date_end'] < time()) {
                    $err_promotion = $ims->lang['product']['err_promotion_date_end'];

                    // mã này đã hết lượt sử dụng, đối với loại dùng chung tất cả, hoặc freeship
                }elseif (!in_array($code['type_promotion'], array('apply_product', 'apply_user')) && $code['num_use'] >= $code['max_use']) {
                    $err_promotion = $ims->lang['product']['err_promotion_numover'];

                    // giá trị đơn hàng tối thiểu chưa đủ
                }elseif($code['total_min'] > 0 && round($code['total_min']) > round($cart_total_include_combo)) {
                    $err_promotion = str_replace('{min_cart}', $ims->func->get_price_format($code['total_min'], 0), $ims->lang['product']['err_promotion_min_cart']);

                    // mã thường
                }elseif ($code['type_promotion'] != 'apply_freeship' && $err_promotion == ''){

                    // áp dụng thành công + áp dụng cho tất cả sp
                    if($check_product_apply == 0){
                        $tmp_price = 0;
                        switch ($code['value_type']){
                            case 1:
                                $tmp_percent = $code['value'];
                                $tmp_price = round(($tmp_percent * $cart_total) / 100, 2);
                                if($code['value_max'] > 0 && $tmp_price > $code['value_max']){
                                    $tmp_price = $code['value_max'];
                                }
                                break;
                            default:
                                $tmp_price = $code['value'];
                                $tmp_percent = round(($tmp_price * 100) / $cart_total, 2);
                                if($tmp_price > $cart_total){
                                    $tmp_price = $cart_total;
                                    $tmp_percent = 100;
                                }
                                break;
                        }

                        $output['value_type']   = $code['value_type'];
                        $output['value'] 	    = $code['value'];
                        $output['value_max']    = $code['value_max'];
                        $output['total_min']    = $code['total_min'];
                        $output['price'] 	    = $tmp_price;
                        $output['percent']      = $tmp_percent;
                        $output['promotion_id'] = Session::Set ('promotion_code', $code['promotion_id']);

                        // áp dụng thành công + có sản phẩm áp dụng trong giỏ hàng
                    }elseif($check_product_apply == 1){
                        $tmp_price = 0;
                        if(!empty($arr_cart)){
                            foreach($arr_cart as $row) {
                                if (in_array($row['item_id'], $list_product)) {
//                                    $row_product = $ims->func->if_isset($cartProduct[$row['item_id']], array());
                                    $row_option  = $ims->func->if_isset($cartOption[$row['option_id']], array());
                                    switch ($code['value_type']){
                                        case 1:
                                            $tmp_percent = $code['value'];
                                            $price = round(($tmp_percent * $row_option['PriceBuy']*$row['quantity']) / 100, 2);
                                            if($code['value_max'] > 0 && $price > $code['value_max']){
                                                $price = $code['value_max'];
                                            }
                                            $tmp_price += $price;

                                            break;
                                        default:
                                            $tmp_price += $code['value'];
                                            break;
                                    }
                                }
                            }
                        }
                        if($tmp_price > $cart_total){
                            $tmp_price = $cart_total;
                            $tmp_percent = 100;
                        }else{
                            $tmp_percent = round(($tmp_price * 100) / $cart_total, 2);
                        }

                        $output['value_type'] 	= $code['value_type'];
                        $output['value'] 	  	= $code['value'];
                        $output['value_max'] 	= $code['value_max'];
                        $output['total_min'] 	= $code['total_min'];
                        $output['price'] 		= $tmp_price;
                        $output['percent']      = $tmp_percent;
                        $output['promotion_id'] = Session::Set ('promotion_code', $code['promotion_id']);
                    }else{
                        Session::Set ('promotion_code', '');
                    }

                    // mã freeship
                }elseif($code['type_promotion'] == 'apply_freeship'){
                    $output['freeship'] = 1;
                    $err_promotion = $ims->lang['product']['freeship'];
                    $output['promotion_id'] = Session::Set ('promotion_code', $code['promotion_id']);
                }
            }
        }else {
            $err_promotion = $ims->lang['product']['err_promotion_wrong'];
            Session::Set ('promotion_code', '');
        }
        $output['type_promotion'] = $code['type_promotion'];
	   	$output['mess'] = $err_promotion;
	   	return $output;
	}

    function check_promotion ($code, $item_id) {
        global $ims;

        $output = 1;

        $cancel_order = $ims->db->load_item('product_order_status', 'is_show = 1 and lang = "' . $ims->conf['lang_cur'] . '" and is_cancel = 1', 'item_id');

        // áp dụng cho email hoặc thành viên
        if ($code['type_promotion'] == 'apply_email' || $code['type_promotion'] == 'apply_user') {
            // áp dụng cho email
//                    if($code['type_promotion'] == 'apply_email' && isset($ims->data['user_cur']['email'])){
//                        if (in_array($ims->data['user_cur']['email'], explode(',',$code['list_email']))){
//                            $err_promotion = '';
//                        }
//                        $check_use_tmp = $ims->db->load_item_arr('product_order as po, product_order_detail as pod', 'po.lang = "'.$ims->conf['lang_cur'].'" and po.is_status != '.$cancel_order.' and po.promotion_id = "'.$promotion_code.'" and po.order_id = pod.order_id and pod.type_id IN('.$code['list_product'].')', 'pod.type_id');
//                        if($check_use_tmp){
//                            foreach ($check_use_tmp as $item){
//                                $check_use[] = $item['type_id'];
//                            }
//                            $check_use = array_count_values($check_use); // Mảng kiểm tra số lần sử dụng mã cho từng user
//                        }
//
//                    }else
            // áp dụng cho thành viên
            if ($code['type_promotion'] == 'apply_user' && isset($ims->data['user_cur']['user_id'])) {
                $total_use = $ims->db->load_item_arr('product_order', 'lang = "' . $ims->conf['lang_cur'] . '" and is_status != ' . $cancel_order . ' and promotion_id = "' . $code['promotion_id'] . '" and user_id = ' . $ims->data['user_cur']['user_id'], 'order_id');
                if (count($total_use) >= $code['max_use']) {
                    $output = 0;
                }
            }
        } elseif ($code['type_promotion'] == 'apply_product') {
            $check_use_tmp = $ims->db->load_item_arr('product_order as po, product_order_detail as pod', 'po.lang = "' . $ims->conf['lang_cur'] . '" and po.is_status != ' . $cancel_order . ' and po.promotion_id = "' . $code['promotion_id'] . '" and po.order_id = pod.order_id and pod.type_id = ' . $item_id, 'pod.type_id');
            if (count($check_use_tmp) >= $code['max_use']) {
                $output = 0;
            }
        }

        return $output;
    }

    function promotion_discount_per_item ($item_id, $price, $promotion_code) {
        global $ims;
        if(!isset($ims->lang['product'])){
            $ims->func->load_language('product');
        }

        $output = array(
            'price_minus' => 0,
        );

        $err_promotion = '';

        $arr_cart 		   = Session::Get('cart_pro', array());
//        $arr_cart_list_pro = Session::Get('cart_list_pro');
        $arr_cart_list_pro = array();

        // giỏ hàng tạm
        if($ims->site_func->checkUserLogin() == 1) {
            $arr_cart = $ims->db->load_row_arr('product_order_temp','user_id="'.$ims->data['user_cur']['user_id'].'"');
            foreach ($arr_cart as $v) {
                $arr_cart_list_pro[$v['item_id']] = $v['item_id'];
            }
        }else{
            foreach ($arr_cart as $item){
                $arr_cart_list_pro[$item['item_id']] = $item['item_id'];
            }
        }
        if (empty($arr_cart)) {
            $output['mess'] = $ims->lang['product']['cart_empty'];
            return $output;
        }
        $cartProduct = $ims->load_data->data_table (
            'product',
            'item_id', '*',
            ' FIND_IN_SET(item_id, "'.@implode(',', $arr_cart_list_pro).'")>0 '.$ims->conf['where_lang']
        );
        $cartOption = $ims->load_data->data_table(
            'product_option',
            'id', '*',
            ' FIND_IN_SET(ProductId, "'.@implode(',', $arr_cart_list_pro).'")>0 '.$ims->conf['where_lang']
        );

        $cart_total = 0;
        $cart_total_include_combo = 0;
        foreach ($arr_cart as $k => $v) {
            $option  = $ims->func->if_isset($cartOption[$v['option_id']], array());
            $v['price_buy'] = $option['PriceBuy'];
            $v['total'] = $v['quantity'] * $v['price_buy'];
            if($cartProduct[$v['item_id']]['is_use_promotion_code'] != 2){
                $cart_total += $v['total'];

                // Tính thêm tiền cho các sp kèm theo combo
                $gift_include = $this->combo_gift_include($v);
                $cart_total_include_combo += $v['total'];
                $cart_total_include_combo += $gift_include['add_payment'];
            }
        }

        $code = $ims->db->load_row("promotion", " is_show=1 AND promotion_id='".$promotion_code."' ");

        if (!empty($code)) {
            $check_use = array();
            $cancel_order = $ims->db->load_item('product_order_status', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and is_cancel = 1', 'item_id');
            $check_product_apply = 0;
            $list_product = array();

            // áp dụng cho email hoặc thành viên
            if ($code['type_promotion'] == 'apply_email' || $code['type_promotion'] == 'apply_user'){
                if($ims->site_func->checkUserLogin() != 1) {
                    $err_promotion = $ims->lang['product']['err_promotion_login'];
                }else{
                    $err_promotion = $ims->lang['product']['err_promotion_user'];
                    // áp dụng cho email
//                    if($code['type_promotion'] == 'apply_email' && isset($ims->data['user_cur']['email'])){
//                        if (in_array($ims->data['user_cur']['email'], explode(',',$code['list_email']))){
//                            $err_promotion = '';
//                        }
//                        $check_use_tmp = $ims->db->load_item_arr('product_order as po, product_order_detail as pod', 'po.lang = "'.$ims->conf['lang_cur'].'" and po.is_status != '.$cancel_order.' and po.promotion_id = "'.$promotion_code.'" and po.order_id = pod.order_id and pod.type_id IN('.$code['list_product'].')', 'pod.type_id');
//                        if($check_use_tmp){
//                            foreach ($check_use_tmp as $item){
//                                $check_use[] = $item['type_id'];
//                            }
//                            $check_use = array_count_values($check_use); // Mảng kiểm tra số lần sử dụng mã cho từng user
//                        }
//
//                    }else
                    // áp dụng cho thành viên
                    if($code['type_promotion'] == 'apply_user' && isset($ims->data['user_cur']['user_id'])){
                        if(in_array($ims->data['user_cur']['user_id'], explode(',',$code['list_user']))){
                            $err_promotion = '';
                        }
                        $total_use = $ims->db->load_item_arr('product_order', 'lang = "'.$ims->conf['lang_cur'].'" and is_status != '.$cancel_order.' and promotion_id = "'.$promotion_code.'" and user_id = '.$ims->data['user_cur']['user_id'], 'order_id');
                        if(count($total_use) >= $code['max_use']){
                            $err_promotion = $ims->lang['product']['err_promotion_numover'];
                        }
                    }
                }
            }elseif ($code['type_promotion'] == 'apply_product'){
                $check_use_tmp = $ims->db->load_item_arr('product_order as po, product_order_detail as pod', 'po.lang = "'.$ims->conf['lang_cur'].'" and po.is_status != '.$cancel_order.' and po.promotion_id = "'.$promotion_code.'" and po.order_id = pod.order_id and pod.type_id IN('.$code['list_product'].')', 'pod.type_id');
                if($check_use_tmp){
                    foreach ($check_use_tmp as $item){
                        $check_use[] = $item['type_id'];
                    }
                    $check_use = array_count_values($check_use); // Mảng kiểm tra số lần sử dụng mã cho từng sp
                }
                $err_promotion = $ims->lang['product']['err_promotion_product'];

                // sản phẩm được áp dụng có trong giỏ hàng
                if(in_array($item_id, explode(',', $code['list_product']))){
                    $err_promotion = '';
                    if(isset($check_use[$item_id])){
                        if($check_use[$item_id] < $code['max_use']){
                            $list_product[] = $item_id;
                        }
                    }else{
                        $list_product[] = $item_id;
                    }
                    if(empty($list_product)){
                        $err_promotion = $ims->lang['product']['err_promotion_numover'];
                    }else{
                        $check_product_apply = 1;
                    }
                }
            }

            if($err_promotion == ''){
                // chưa tới ngày sử dụng
                if($code['date_start'] > time()) {
                    $err_promotion = $ims->lang['product']['err_promotion_notyet_timetouse'];

                    // mã hết hạn
                }elseif($code['date_end'] < time()) {
                    $err_promotion = $ims->lang['product']['err_promotion_date_end'];

                    // mã này đã hết lượt sử dụng, đối với loại dùng chung tất cả, hoặc freeship
                }elseif (!in_array($code['type_promotion'], array('apply_product', 'apply_user')) && $code['num_use'] >= $code['max_use']) {
                    $err_promotion = $ims->lang['product']['err_promotion_numover'];

                    // giá trị đơn hàng tối thiểu chưa đủ
                }elseif($code['total_min'] > 0 && round($code['total_min']) > round($cart_total_include_combo)) {
                    $err_promotion = str_replace('{min_cart}', $ims->func->get_price_format($code['total_min'], 0), $ims->lang['product']['err_promotion_min_cart']);

                    // mã thường
                }elseif ($code['type_promotion'] != 'apply_freeship' && $err_promotion == ''){

                    // áp dụng thành công + áp dụng cho tất cả sp
                    if($check_product_apply == 0){
                        switch ($code['value_type']){
                            case 1:
                                $tmp_percent = $code['value'];
                                $tmp_price = round($tmp_percent * $cart_total / 100, 2);
                                if($code['value_max'] > 0 && $tmp_price > $code['value_max']){
                                    $tmp_price = $code['value_max'];
                                }
                                break;
                            default:
                                $tmp_price = $code['value'];
                                if($tmp_price > $cart_total){
                                    $tmp_price = $cart_total;
                                }
                                break;
                        }
                        $percent = round($tmp_price/$cart_total*100, 2);
                        $output['price_minus'] = $price * $percent / 100;

                    // áp dụng thành công + có sản phẩm áp dụng trong giỏ hàng
                    }elseif($check_product_apply == 1){
                        $tmp_price = 0;
                        if(!empty($arr_cart)){
                            if (in_array($item_id, $list_product)) {
                                switch ($code['value_type']){
                                    case 1:
                                        $tmp_percent = $code['value'];
                                        $tmp_price = round($tmp_percent * $price / 100, 2);
                                        if($code['value_max'] > 0 && $tmp_price > $code['value_max']){
                                            $tmp_price = $code['value_max'];
                                        }
                                        break;
                                    default:
                                        $tmp_price = $code['value'];
                                        break;
                                }
                            }
                        }
                        if($tmp_price > $price){
                            $tmp_price = $price;
                        }
                        $output['price_minus'] = $tmp_price;
                    }
                }
            }
        }

        return $output;
    }

    function combo_gift_include($row){
        global $ims;
        $out = array(
            'add_payment' => 0
        );
        if($row['combo_info'] != ''){
            $combo_info =  $ims->func->unserialize($row['combo_info']);
            $k_combo = array_keys($combo_info);
            $k_combo = str_replace('_id', '', $k_combo[0]);
            $v_combo = array_values($combo_info);
            $row[$k_combo] = $v_combo[0];
        }

        $combo = $ims->db->load_row('product as pd, combo as cb', 'pd.is_show = 1 and pd.lang = "'.$ims->conf['lang_cur'].'" and pd.item_id = '.$row['item_id'].' and pd.combo_id = cb.item_id', 'cb.item_id, cb.title, cb.type, cb.value, cb.value_type');
        if($combo){
            if($combo['type'] != 1){
                if(isset($row['include']) && $row['include'] != ''){
                    $arr_include = $ims->db->load_item_arr('product', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id IN('.$row['include'].') order by FIELD(item_id,"'.$row['include'].'") desc', 'title, picture, price_buy, friendly_link');
                    if($arr_include){
                        foreach ($arr_include as $include){
                            $include['price_buy'] = ($combo['value_type'] == 1) ? $include['price_buy']*((100 - $combo['value'])/100) : ($include['price_buy'] - $combo['value']);
                            if($include['price_buy'] < 0){
                                $include['price_buy'] = 0;
                            }
                            $out['add_payment'] += $include['price_buy'];
                        }
                    }
                }
            }
        }

        return $out;
    }
}
?>