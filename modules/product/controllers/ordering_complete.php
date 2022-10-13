<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "product";
	var $action  = "ordering_complete";

	function __construct (){
		global $ims;

		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->action,
			'js'  	 		 => "ordering",
			'css'  	 		 => "ordering",
			'use_func'  	 => "ordering", // Sử dụng func
			'use_navigation' => 0, // Sử dụng navigation
			'required_login' => $ims->func->if_isset($ims->setting['product']["required_login_order"], 0), // Bắt buộc đăng nhập
		);
        $ims->func->loadTemplate($arrLoad);

        if(!isset($ims->data['user_cur'])){
            $ims->site_func->checkUserLogin();
        }

		$data = array();
		$data['content'] = $this->do_main();
     	$ims->conf['class_full'] = 'cart';
        $ims->conf['container_layout'] = 'm';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	
	function do_main (){
		global $ims;
        $data = array();

	    $output_mess = array();
		$output_mess['notification_payment'] = '';
        $output_mess['status_payment'] = '';

		$ordering_payment = Session::Get ('ordering_payment', array());
		// ********************* Thanh toán bằng VNPAY
		if (isset($ims->get['vnp_TxnRef']) && isset($ims->get['vnp_SecureHash'])) {
            $output_mess = $ims->site_func->paymentCustomComplete("vnpay");
		}
		// ********************* End Thanh toán bằng VNPAY

		// ********************* Thanh toán bằng ONEPAY
		if (isset($ims->get['vpc_Merchant']) && $ims->get['vpc_Merchant'] == 'ONEPAY') {
            $output_mess = $ims->site_func->paymentCustomComplete("onepay");
		}
		// ********************* Thanh toán bằng ONEPAY

		// ********************* Thanh toán bằng MOMO
		if (isset($ims->get['orderType']) && $ims->get['orderType'] == 'momo_wallet') {
            $output_mess = $ims->site_func->paymentCustomComplete("momo");
		}
		// ********************* Thanh toán bằng MOMO

		// ********************* Thanh toán bằng NGANLUONG
		if (isset($ims->get['order_code']) && isset($ims->get['token'])) {
            $output_mess = $ims->site_func->paymentCustomComplete("nganluong");
        }
		// ********************* End Thanh toán bằng NGANLUONG

		// ********************* Thanh toán bằng ALEBAY
		if (isset($ims->get['data']) && isset($ims->get['checksum'])) {
            $output_mess = $ims->site_func->paymentCustomComplete("alepay");
		}
		// ********************* End Thanh toán bằng ALEBAY

		// ********************* Deeplink
//        if (isset($_COOKIE['deeplink'])) {
//        	unset($_COOKIE['deeplink']);
//			setcookie('deeplink', '', time() - 3600);
//        }
		// ********************* Deeplink

		if(!is_array($ordering_payment) || !count($ordering_payment) > 0) {
			 $ims->html->redirect_rel($ims->site_func->get_link (''));
		}

	    $cartProduct = $ims->load_data->data_table (
			'product', 
			'item_id', '*', 
			' FIND_IN_SET(item_id, "'.@implode(',', $ordering_payment['arr_cart_list_pro']).'")>0 '.$ims->conf['where_lang']
		);
		$cartOption = $ims->load_data->data_table(
			'product_option',
			'id', '*',
			' FIND_IN_SET(ProductId, "'.@implode(',',$ordering_payment['arr_cart_list_pro']).'")>0 '.$ims->conf['where_lang']
		);

		$order_info = $ims->db->load_row('product_order', 'is_show = 1 and order_code="'.$ordering_payment['order_code'].'"');
		if (!empty($order_info)) {
			$shipping = $ims->db->load_row('order_shipping', ' shipping_id="'.$order_info['shipping'].'" '.$ims->conf['where_lang']);
			$payment  = $ims->db->load_row('order_method', ' method_id="'.$order_info['method'].'" '.$ims->conf['where_lang']);

			$arr_detail = $ims->db->load_row_arr('product_order_detail', 'order_id="'.$order_info['order_id'].'"');
			$arr_cart = array();
			foreach ($arr_detail as $key => $value) {
				$arr_cart[$value['detail_id']] = $value;				
			}

			$mess_method = '';
			if($order_info['method_price'] > 0){
				$mess_method = ' - Đơn hàng bị cộng thêm '.$ims->func->get_price_format_email($order_info['method_price']);
			}elseif($order_info['method_price'] < 0){
				$mess_method = ' - Đơn hàng được giảm thêm '.$ims->func->get_price_format_email($order_info['method_price']);
			}

			$mail_arr_key = array(
				'{domain}',
				'{full_name}',
				'{list_cart}',
				'{o_full_name}',
				'{o_email}',
				'{o_phone}',
				'{o_address}',
				'{o_full_address}',
				'{d_full_name}',
				'{d_email}',
				'{d_phone}',
				'{d_address}',
				'{d_full_address}',
				'{shipping}',
				'{method}',
				'{request_more}',
				'{order_code}',
				'{date_create}',
				'{invoice_company}',
				'{invoice_tax_code}',
				'{invoice_address}',
			);
			$mail_arr_value = array(
				$ims->conf['rooturl'],
				$order_info["o_full_name"],
				$this->do_cart ($order_info, $arr_cart, $cartProduct, $cartOption),
				$order_info["o_full_name"],
				$order_info["o_email"],
				$order_info["o_phone"],
				$order_info["o_address"],
				$ims->func->full_address($order_info, 'o_'),
				$order_info["d_full_name"],
				$order_info["d_email"],
				$order_info["d_phone"],
				$order_info["d_address"],
				$ims->func->full_address($order_info, 'd_'),
				$shipping['title'],
				$payment['title'] .$mess_method ,
				$order_info["request_more"],
				$order_info["order_code"],
				$ims->func->get_date_format($order_info["date_create"]),
				($order_info['invoice_company']!='')?$ims->lang['product']['company_name'].": ".$order_info['invoice_company']:'',
				($order_info['invoice_tax_code']!='')?$ims->lang['product']['tax_code'].": ".$order_info['invoice_tax_code']:'',
				($order_info['invoice_address']!='')?$ims->lang['product']['address'].": ".$order_info['invoice_address']:'',
			);
			// Send to admin
//			 $ims->func->send_mail_temp ('admin-ordering-complete', $ims->conf['email'], $ims->conf['email'], $mail_arr_key, $mail_arr_value);
			// Send to customer
			if(isset($order_info['o_email']) && $order_info['o_email'] != ''){
//				 $ims->func->send_mail_temp ('ordering-complete', $order_info['o_email'], $ims->conf['email'], $mail_arr_key, $mail_arr_value);
			}else{
				if(empty($order_info['user_id'])){
					$ims->site_func->setting('user');
					$sms_content = str_replace('{total}', $col_up['total_payment'], $ims->setting['user']['esms_Contentorder']);
					$sms_content = str_replace('{datetime}', date('d/m/Y H:i',time()), $sms_content);
					$sms_content = str_replace('{order}', $col_up['order_code'], $sms_content);
	            	
			        $data_sms = array(
			            'ApiKey'    => $ims->setting['user']['esms_ApiKey'],
			            'SecretKey' => $ims->setting['user']['esms_SecretKey'],
			            'Brandname' => $ims->setting['user']['esms_Brandname'],
			            'Phone'     => $arr_in["o_phone"],
			            'Content'   => $sms_content,
			            'SmsType'   => 2,
			            'Sandbox'   => 0,
			        );
			        
			        $data_sms = http_build_query ($data_sms);
			        $curl = curl_init();
			        $header = array("Content-Type:application/x-www-form-urlencoded");
			        curl_setopt_array($curl, array(
	                    CURLOPT_RETURNTRANSFER  => 1,
	                    CURLOPT_URL             => 'http://rest.esms.vn/MainService.svc/json/SendMultipleMessage_V4_post/',
	                    CURLOPT_POST            => 1,
	                    CURLOPT_HTTPHEADER      => $header,
	                    CURLOPT_SSL_VERIFYPEER  => 0,
	                    CURLOPT_POSTFIELDS      => $data_sms
	                ));
	                $resp = curl_exec($curl);
	        		curl_close($curl);
			        $SMS = $resp;  			        
			        if (!empty($SMS)) {
			            $SMS = json_decode($SMS);	            
			            if (isset($SMS->CodeResult) && $SMS->CodeResult==100) {
			            	$data['sms'] = $ims->lang['api']['success'];
			            }else{
			            	$data['sms'] = "Có lỗi xảy ra";
			            }
			        }else{
			        	$data['sms'] = "Có lỗi xảy ra";
			        }
				}
			}
			Session::Delete('ordering_payment');

            $data['content'] = $this->do_review($order_info['order_id'], $output_mess);
            $data['ordering_method'] = $this->ordering_method();
            $data['ordering_shipping'] = $this->ordering_shipping();
		}else{
            $data['content'] = '<div style="text-align: center">'.$ims->lang['product']['cancel_order'].'</div>';
        }
		
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("ordering_complete");
		$output = $ims->temp_act->text("ordering_complete");
		return $output;
	}
	
	function do_cart ($order = array(), $arr_cart = array(), $arr_pro = array(), $arr_op = array()){
		global $ims;

		$data = $order;
		$num_product = 0;
		if(is_array($arr_cart) && count($arr_cart) > 0){
			foreach($arr_cart as $cart_id => $row) {
				$row_pro = $ims->func->if_isset($arr_pro[$row['type_id']], array());
				$row_op  = $ims->func->if_isset($arr_op[$row['option_id']], array());		
				$row['cart_id'] = $cart_id;
				$row['pic_w'] = 50;
				$row['pic_h'] = 50;
				$row['picture'] = (isset($row['picture'])) ? $row['picture'] : '';
				$row["picture"] = $ims->func->get_src_mod($row["picture"], $row['pic_w'], $row['pic_h'], 1, 0, array('fix_max' => 1));
				$row['price_buy'] = (isset($row['price_buy'])) ? $row['price_buy'] : 0;
				if($row['option1'] != '' && $row['option1'] != "Default Title"){
					$row['title'] .= ' / '.$row['option1'];
				}
				if($row['option2'] != ''){
					$row['title'] .= ' / '.$row['option2'];
				}
				if($row['option3'] != ''){
					$row['title'] .= ' / '.$row['option3'];
				}
				$row['quantity'] = (isset($row['quantity'])) ? $row['quantity'] : 0;
				// Danh sách quà tặng hoặc sp mua kèm combo
				$gift_include = $this->combo_gift_include($row['arr_gift_include']);
				$row['gift_include'] = $gift_include['html'];
                $num_product += $gift_include['num_product'];
                $row['total'] = $row['quantity']*$row['price_buy'];

				/*$row['code_pic'] = (isset($row['code_pic']) && array_key_exists($row['code_pic'], $arr_code_pic)) ? $row['code_pic'] : 0;
				$code_pic = (isset($arr_code_pic[$row['code_pic']]['code_pic'])) ? '<div><span class="code_pic" style="background:'.$arr_code_pic[$row['code_pic']]['code_pic'].';">&nbsp;</span></div>' : '';
				$row['code_pic'] = (isset($arr_code_pic[$row['code_pic']]['title'])) ? $code_pic.$arr_code_pic[$row['code_pic']]['title'] : '';*/
				
				$row['price_buy'] = $ims->func->get_price_format_email($row['price_buy']);
				$row['total'] = $ims->func->get_price_format_email($row['total']);				
				$row['cart_td_attr'] = ' style="background:#ffffff;"';
				$ims->temp_act->assign('row', $row);
				$ims->temp_act->parse("table_cart_ordering_method_mail.row_item");
			}
		} else {
			$ims->temp_act->assign('row', array('mess' => $ims->lang['product']['no_have_item']));
			$ims->temp_act->parse("table_cart_ordering_method_mail.row_empty");
		}

		if($order['shipping_price'] == 0){
			$data['shipping_price_out'] = 'Miễn phí';
		}else{
			$data['shipping_price_out'] = $ims->func->get_price_format_email($order['shipping_price'], 0);
		}
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("table_cart_ordering_method_mail.shipping_price");

		if($order['method_price'] > 0 || $order['method_price'] < 0) {
			if($order['method_price'] > 0){
				$data['save_method'] = ' +'. $ims->func->get_price_format_email($order['method_price']);
			}elseif($order['method_price'] < 0){
				$data['save_method'] = $ims->func->get_price_format_email($order['method_price']);
			}
			$ims->temp_act->assign('data', $data);
			$ims->temp_act->parse("table_cart_ordering_method_mail.save_method");
		}

		if(isset($order['promotion_price']) && $order['promotion_price'] != 0 && $order['promotion_price'] > 0){
		 	$data['promotion_percent'] = $order['promotion_percent'].'%';
			$data['promotion_price']  = $order['promotion_price'];
			$data['promotion_price_out'] = '-'.$ims->func->get_price_format_email($order['promotion_price'], 0);
			$ims->temp_act->assign('data', $data);
			$ims->temp_act->parse("table_cart_ordering_method_mail.promotional_box_show");
        }

        // sử dụng điểm tích lũy
        if ($data['payment_wcoin'] > 0) {
            $data['wcoin_price_out'] = $ims->func->get_price_format_email($data['payment_wcoin2money'], 0);
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("table_cart_ordering_method_mail.wcoin_box_show");
        }

        $data['cart_total'] = $ims->func->get_price_format_email($data['total_order'], 0);
		$data['cart_payment'] = $ims->func->get_price_format_email($data['total_payment'], 0);
		$data['num_product'] = count($arr_cart) + $num_product;

		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("table_cart_ordering_method_mail");
		return $ims->temp_act->text("table_cart_ordering_method_mail");
	}
	function combo_gift_include($gift_include){
	    global $ims;
	    $out = array(
	        'html' => '',
            'add_payment' => 0,
            'num_product' => 0
        );
	    if($gift_include){
            $gift_include = $ims->func->unserialize($gift_include);
            foreach ($gift_include as $key => $value){
                $title = $ims->lang['product'][$key];
                foreach ($value as $row){
                    $row['price'] = (isset($row['price_buy_combo'])) ? $ims->func->get_price_format_email($row['price_buy_combo']) : '';
                    $out['add_payment'] += (isset($row['price_buy_combo'])) ? $row['price_buy_combo'] : 0;
                    $out['num_product'] += ($key == 'include') ? 1 : 0;
                    $ims->temp_act->assign('row', $row);
                    $ims->temp_act->parse("combo_gift_include.item");
                }
                $ims->temp_act->assign('title', $title);
                $ims->temp_act->reset("combo_gift_include");
                $ims->temp_act->parse("combo_gift_include");
                $out['html'] = $ims->temp_act->text("combo_gift_include");
            }
            return $out;
        }
    }
	//Show cart complete
	function table_promotion ($order_create){
		global $ims;
		
		$output = '';	
		$data = array();
		$sql = "SELECT * FROM promotion WHERE order_create='".$order_create."' ORDER BY promotion_id ASC";
		//echo $sql;
		$result = $ims->db->query($sql);
		if ($num = $ims->db->num_rows($result)) {
			while ($row = $ims->db->fetch_row($result)) { 
				$row['percent'] = $ims->func->format_number($row['percent']);				
				$row['date_end'] = $ims->func->get_date_format($row['date_end'],1);
				$ims->temp_act->assign('row', $row);
				$ims->temp_act->parse("table_promotion.row_item");
			}
			$ims->temp_act->assign('data', $data);
			$ims->temp_act->parse("table_promotion");
			$output = $ims->temp_act->text("table_promotion");
		}
		return $output;
	}
	
	function table_cart ($order = array(), $address){
		global $ims;
		
		$data = $order;
		$sql = "SELECT * FROM product_order_detail WHERE order_id='".$order['order_id']."' ORDER BY detail_id ASC";
		// echo $sql;die;

		$result = $ims->db->query($sql);
		$html_row = "";
		if ($num = $ims->db->num_rows($result)) {
			$pic_w = 80;
			$pic_h = 80;
			while ($row = $ims->db->fetch_row($result)) { 				
				$row["picture"]   = $ims->func->get_src_mod($row["picture"], $pic_w, $pic_h, 1, 1);
				$row['quantity']  = $row['quantity'];
				$row['total']     = $row['quantity'] * $row['price_buy'];
				$row['price_buy'] = $ims->func->get_price_format($row['price_buy']);
				$row['total']     = $ims->func->get_price_format($row['total']);
				if($row['option1'] != '' && $row['option1'] != "Default Title"){
					$row['title'] .= ' / '.$row['option1'];
				}
				if($row['option2'] != ''){
					$row['title'] .= ' / '.$row['option2'];
				}
				if($row['option3'] != ''){
					$row['title'] .= ' / '.$row['option3'];
				}
				$ims->temp_act->assign('row', $row);
				$ims->temp_act->parse("table_cart_complete.row_item");
			}
		} else {
			$ims->temp_act->assign('row', array('mess' => $ims->lang['user']['no_have_item']));
			$ims->temp_act->parse("table_cart_complete.row_empty");
		}

		$data['total_order'] 		 = $ims->func->get_price_format($data['total_order'], 0);
		$data['shipping_price'] = '+'.$ims->func->get_price_format($data['shipping_price'], 0);

		if($data['shipping_price'] == -1){
			$data['shipping_price'] = $ims->site_func->get_lang('free', 'global');
		}
		$data['total_payment'] = $ims->func->get_price_format($data['total_payment'], 0);
        $data['delivery_address'] = $address;
		// sử dụng mã giảm giá
		if ($data['promotion_price']>0) {
			$data['promotion_price'] = $ims->func->get_price_format($data['promotion_price'], 0);
			$ims->temp_act->assign('data', $data);
			$ims->temp_act->parse("table_cart_complete.promotional_box_show");
		}
		// sử dụng điểm tích lũy
		if ($data['payment_wcoin']>0) {
			$data['payment_wcoin2money'] = $ims->func->get_price_format($data['payment_wcoin2money'], 0);
			$ims->temp_act->assign('data', $data);
			$ims->temp_act->parse("table_cart_complete.wcoin_box_show");
		}

		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("table_cart_complete");
		return $ims->temp_act->text("table_cart_complete");
	}
	
	//-----------
	function do_review($order_id = 0, $output_mess = array()) {

		global $ims;
		$ims->func->load_language('user');
		$ims->temp_act->assign('LANG', $ims->lang);
		$err = "";


		$arr_order_shipping = $ims->load_data->data_table ('order_shipping', 'shipping_id', 'shipping_id,title,content', "is_show=1 and lang='".$ims->conf['lang_cur']."' order by show_order desc, date_create desc", array(), array('editor'=>'content'));
		$arr_order_method = $ims->load_data->data_table ('order_method', 'method_id', 'method_id,title,content', "is_show=1 and lang='".$ims->conf['lang_cur']."' order by show_order desc, date_create desc", array(), array('editor'=>'content'));
		
		// $sql = "select * from product_order where order_id='".$order_id."'";
		// $result = $ims->db->query($sql);
	 //    if($data = $ims->db->fetch_row($result)){

		// }
		$data = $ims->db->load_row("product_order",'is_show=1 and order_id="'.$order_id.'"');

		if($data){
			$data["err"] = $err;
//			$data["table_cart"] 	 = $this->table_cart ($data);
			$data["table_promotion"] = $this->table_promotion ($order_id);
			if(!empty($data["table_promotion"])) {
				$ims->temp_act->assign('data', $data);
				$ims->temp_act->parse("review.list_promotion");
			}elseif($data['is_status'] >= 2) {
				$ims->temp_act->assign('data', $data);
				$ims->temp_act->parse("review.create_promotion");
			}
			
			//$data["list_status_order"] = list_status_order ('is_status',$data['is_status'], " class=\"form-control\"");
			$data["status_order"] = $this->status_order_info($data["is_status"]);
			
			$data['shipping'] = (isset($data['shipping']) && array_key_exists($data['shipping'], $arr_order_shipping)) ? $arr_order_shipping[$data['shipping']] : array();
			$data['method'] = (isset($data['method']) && array_key_exists($data['method'], $arr_order_method)) ? $arr_order_method[$data['method']] : array();

			$address = array($data['d_address'], '');
			if(($data['d_province'] != '' && $data['d_province'] != 0) || ($data['d_district'] != '' && $data['d_district'] != 0)){
                $address[] = $data['ward'] = $ims->func->location_name('ward', $data['d_ward']);
                $address[] = $data['district'] = $ims->func->location_name('district', $data['d_district']);
                $address[] = $data['province'] = $ims->func->location_name('province', $data['d_province']);
				$ims->temp_act->assign('data', $data);
				$ims->temp_act->parse("review.province_ship");
			}
            $address = (implode(', ', array_filter($address)));
			if (isset($data['is_payment']) && $data['is_payment'] == 1) {
				$data['is_payment'] = 'Đã thanh toán qua Ngân lượng';
				$ims->temp_act->assign('data', $data);
				$ims->temp_act->parse("review.is_payment");
			}
			if($data['invoice_company'] != ''){
				$ims->temp_act->assign('data', $data);
				$ims->temp_act->parse("review.invoice");	
			}
//			if(!empty($output_mess) && $output_mess['status_payment']!=""){
//				$data['status_payment'] = $output_mess['status_payment'];
//				$data['notification_payment'] = $output_mess['notification_payment'];
//				$ims->temp_act->assign('data', $data);
//				$ims->temp_act->parse("review.output_mess");
//			}
            $data["table_cart"] 	 = $this->table_cart ($data, $address);
			$data['title_page'] = $ims->conf['meta_title'];
			$data['content_page'] = str_replace('[name]', $data['d_full_name'], $ims->func->input_editor_decode($ims->setting['product']['ordering_complete']));
			$data['link_buy_more'] = $ims->site_func->get_link ('product');
			$data['manage_order_link'] = $ims->site_func->get_link("user", $ims->db->load_item('user_setting', $ims->conf['qr'].' and setting_key = "ordering_link"', 'setting_value'));
			$ims->temp_act->assign('data', $data);
			$ims->temp_act->parse("review");
		}
		return $ims->temp_act->text("review");
	}

	function status_order_info ($status=0) {
		global $ims;
		$output =  $ims->func->if_isset($ims->setting_ordering['status_order'][$status], array()) ;
		return $output;
	}
    function ordering_method(){
	    global $ims;

        $arr_payment = $ims->db->load_row_arr('order_method', '1 '.$ims->conf['where_lang'].' ORDER BY show_order DESC, date_create DESC');
        if($arr_payment){
            foreach ($arr_payment as $row){
                $row['picture'] = ($row['picture'] != '') ? '<img src="'.$ims->func->get_src_mod($row['picture']).'" alt="'.$row['title'].'">' : '';
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("ordering_method.item");
            }
            $ims->temp_act->parse("ordering_method");
            return $ims->temp_act->text("ordering_method");
        }
    }
    function ordering_shipping(){
	    global $ims;

        $arr_shipping = $ims->db->load_row_arr('order_shipping', '1 '.$ims->conf['where_lang'].' ORDER BY show_order DESC, date_create DESC');
        if($arr_shipping){
            foreach ($arr_shipping as $row){
                $row['picture'] = ($row['picture'] != '') ? '<img src="'.$ims->func->get_src_mod($row['picture']).'" alt="'.$row['title'].'">' : '';
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("ordering_shipping.item");
            }
            $ims->temp_act->parse("ordering_shipping");
            return $ims->temp_act->text("ordering_shipping");
        }
    }
  	// End class
}
?>