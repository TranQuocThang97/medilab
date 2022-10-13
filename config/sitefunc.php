<?php
if (!defined('IN_ims')) { die('Access denied'); }
use \Firebase\JWT\JWT;

class siteFunc {

    // Lấy danh sách phiên bản sản phẩm
    function versionProduct($info=array()){
        global $ims;

        // Sản phẩm nhiều phiên bản ----------------------------------
        $output = array(
            'count' => 0,
            'html' => '',
            'option' => array(),
        );

        $data = array();
        $option = array();
        if($info['arr_item']!=''){
            $data['arr_item'] = $ims->func->unserialize($info['arr_item']);
            foreach ($data['arr_item'] as $k => $row) {
                if($row['SelectName'] == 'Custom'){
                    $row['title'] = $row['CustomName'];
                }else{
                    $row['title'] = $ims->func->if_isset($ims->lang['product']['option_'.strtolower($row['SelectName'])], '');
                }
                $option[$k]['id'] = $k;
                $option[$k]['title'] = $row['title'];
                $option[$k]['group_id'] = strtolower($row['SelectName']);
                $option[$k]['group_name'] = 'option'.($k+1);
                $option[$k]['value'] = array();
            }

            $orderBy = 'ORDER BY date_create DESC';
            if($info['field_option'] != ''){
                $orderBy = 'ORDER BY '.$info['field_option'].', date_create DESC';
            }

            $arr_option = $ims->db->load_row_arr("product_option", "lang='".$ims->conf['lang_cur']."' AND is_show=1 AND ProductId='".$info['item_id']."' ".$orderBy);
            if(!empty($arr_option)){
                $output['count'] = count($arr_option);
                $i=0;
                foreach (($arr_option) as $k => $v) {
                    $i++;
                    if(count($arr_option)>1) {
                        if($v['Picture']!=''){
                            $data['pic_color'][$k]['picture'] = $v['Picture'];
                            $data['pic_color'][$k]['id'] = $v['id'];
                            $data['pic_color'][$k]['option']['0'] = $v['Option1'];
                            $data['pic_color'][$k]['option']['1'] = $v['Option2'];
                            $data['pic_color'][$k]['option']['2'] = $v['Option3'];
                        }
                        if($v['Option1'] != ''){  // Thuộc tính thứ 1 != ""
                            $option[0]['value'][$v['Option1']][] = $v['id'];
                        }
                        if($v['Option2'] != ''){ // Thuộc tính thứ 2 khác rỗng
                            $option[1]['value'][$v['Option2']][] = $v['id'];
                        }
                        if($v['Option3'] != ''){ // Thuộc tính thứ 3 khác rỗng
                            $option[2]['value'][$v['Option3']][] = $v['id'];
                        }
                    }
                    if($i==1){ // Lấy phiên bản đầu điên
                        $output['option_id'] = $data['option_id'] = $v['id'];
                        if (count($arr_option) > 1) {
                            $data['option_id'] = 0;
                        }
                        if ($v['Picture']!="") {
                            $data['picture_cart'] = $ims->func->get_src_mod($v['Picture']);
                        }else{
                            $data['picture_cart'] = $ims->func->get_src_mod($info['picture']);
                        }
                        // $data['price'] = $v['Price'];
                        // $data['price_buy'] = $v['PriceBuy'];
                        $data['tracking_policy'] = $v['useWarehouse'];
                        $data['order_out_stock'] = $v['is_OrderOutStock'];
                        // $data['max_quantity'] = $v['useWarehouse']==1?$v['Quantity']:1000;
//                        if($v['useWarehouse']==1 && $v['is_OrderOutStock']==0){
//                            $data['max_quantity'] = $v['Quantity'];
//                        }else{
//                            $data['max_quantity'] = 1000;
//                        }
                        $data['max_quantity'] = 1000;

                        $data['price'] = $v['Price'];
                        $data['price_buy'] = $v['PriceBuy'];
                    }
                } 
            } // End foreach
        } // End if arr_option
        $output['pic_color'] = $ims->func->if_isset($data['pic_color'],array());
        $output['option'] = $option;

        $arr_color = array();
        $arr_color_tmp = $ims->db->load_item_arr("product_color", "lang='".$ims->conf['lang_cur']."' AND is_show=1", "color_id, color, title");
        foreach ($arr_color_tmp as $color) {
            $color['name'] = $ims->func->vn_str_filter($color['title']);
            $arr_color[strtolower($color['name'])] = $color;
        }

        foreach ($option as $key => $value) {
            $output['html'] .= $this->versionProductItem($option[$key], $arr_color);
        }
        return $output;
    }

    // Chi tiết phiên bản
    function versionProductItem($option=array(), $arr_color=array()){
        global $ims;

        $temp = 'version';
        $ims->temp_act->reset($temp);
        $data = array();
        if(!empty($option)){
            $data['title']      = $ims->func->if_isset($option['title']);
            $data['group_id']   = $ims->func->if_isset($option['group_id']);
            $data['group_name'] = $ims->func->if_isset($option['group_name']);
            $data['selector']   = isset($option['id']) ? 'selector-option-'.$option['id'] : '';
            if(isset($option['value']) && count($option['value'])>0){
                $i=0;
                foreach ($option['value'] as $key => $value) {
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
                    $row['data_option'] = isset($option['id'])?'Option'.((int)$option['id']+1):'';
                    $row['class'] = '';
                    if($row['group_id'] == "color"){                        
                        $color = isset($ims->data['color'][$key])?$ims->data['color'][$key]:array();
                        $row['color_title'] = isset($color['title'])?strtolower($ims->func->vn_str_filter(trim($color['title']))):'';
                        $row['color'] = isset($color['color'])?$color['color']:'';
                        $row['data_color'] = 'data-color="'.str_replace(' ', '-', $row['color_title']).'"';
                        $row['title'] = '<span style="background-color: '.$row['color'].'" title="'.$key.'">'.$color['title'].'</span>';
                        $row['class'] = 'color';
                    }
                   
                    if($i==1){
//                         $row['active'] = '';
//                         $row['active'] = 'checked';
                    }

                    $ims->temp_act->assign('row',$row);
                    $ims->temp_act->parse($temp.'.row');   
                }
                $ims->temp_act->assign('data',$data);
                $ims->temp_act->parse($temp);    
            }
            return $ims->temp_act->text($temp);
        }
    }

    // Lấy trạng thái đơn hàng theo mã/ tên
    function getStatusOrder($string=''){
        global $ims;

        $ims->setting_ordering['product_order_status'] = $ims->load_data->data_table(
            'product_order_status', 
            'item_id', '*', 
            "lang='".$ims->conf['lang_cur']."' ORDER BY show_order DESC, date_create DESC", array()
        );

        $status = 0;
        $status_access = (isset($ims->setting_ordering['product_order_status'])) ? $ims->setting_ordering['product_order_status'] : array();
        foreach ($status_access as $key => $value) {
            $list_status_string = explode(',', $value['list_status_string']);
            if (in_array($string, $list_status_string)) {
                $status = $value['item_id'];
            }
        }
        return $status;
    }

    public function paypal_api($endpoint = '', $data = array()){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "grant_type=client_credentials",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded",
                "Post: ",
                "Authorization: Basic ".base64_encode($data['client_key'].':'.$data['secret_key'])
            ),
        ));
        $result = curl_exec($curl);
        curl_close($curl);
        $jsonResult = json_decode($result, true);  // decode json
        return $jsonResult;
    }

    // Khởi tạo thanh toán
    function paymentCustom($method=array(), $order=array(), $order_up=array(), $ordering_address=array(), $table='product_order', $link_go = ''){
        global $ims;

        $data_return = array();
        $data_return['ok'] = 0;

        if (!empty($method)) {
            if($method['name_action'] == 'nganluong'){
                // version 3.1 nang cao
                $config = $ims->conf['rootpath'].'modules'.DS.'payment_method'.DS.$method['name_action'].DS.'v31/config.php';
                $file   = $ims->conf['rootpath'].'modules'.DS.'payment_method'.DS.$method['name_action'].DS.'v31/include/NL_Checkoutv3.php';
                if(file_exists($file) && file_exists($config)){
                    require_once ($config);
                    require_once ($file);
                    $nlcheckout     = new NL_CheckOutV3(MERCHANT_ID,MERCHANT_PASS,RECEIVER,URL_API);
                    $total_amount   = (int)$order['total_payment'];
                    $array_items[0] = array('item_name1' => 'Product',
                        'item_quantity1'                => 1,
                        'item_amount1'                  => $total_amount,
                        'item_url1'                     => 'http://nganluong.vn/'
                    );
                    $array_items        = array();
                    $payment_method     = 'ATM_ONLINE';
                    $bank_code          = $ordering_address['bankcode'];
                    $order_code         = $order['order_code'];
                    $payment_type       = '';
                    $order_description  = '';
                    $discount_amount    = 0;
                    $tax_amount         = 0;
                    $fee_shipping       = 0;
                    $return_url         = $this->get_link('product', $ims->setting['product']['ordering_complete_link']);
                    $cancel_url         = urlencode($this->get_link('product', $ims->setting['product']['ordering_complete_link']).'?orderid='.$order_code) ;
                    $buyer_fullname     = $order['o_full_name'];
                    $buyer_email        = $order['o_email'];
                    $buyer_mobile       = $order['o_phone'];
                    $buyer_address      = '';
                    if($payment_method !='' && $buyer_email !="" && $buyer_mobile !="" && $buyer_fullname !="" && filter_var( $buyer_email, FILTER_VALIDATE_EMAIL )  ){
                        if($payment_method =="ATM_ONLINE" && $bank_code !='' ){
                            $nl_result = $nlcheckout->BankCheckout($order_code,$total_amount,$bank_code,$payment_type,$order_description,$tax_amount,
                                $fee_shipping,$discount_amount,$return_url,$cancel_url,$buyer_fullname,$buyer_email,$buyer_mobile,
                                $buyer_address,$array_items) ;
                        }
                        if ($nl_result->error_code =='00'){
                            Session::Delete('cart_pro');
                            Session::Delete('cart_info');
                            Session::Delete('cart_list_pro');
                            Session::Delete('ordering_address');
                            Session::Delete('promotion_code');
                            Session::Delete('user_contributor');
                            unset($_COOKIE['type_contributor']);
                            setcookie('type_contributor', '', time() - 3600, '/');
                            unset($_COOKIE['user_contributor']);
                            setcookie('user_contributor', '', time() - 3600, '/');

                            // Cập nhât order với token  $nl_result->token để sử dụng check hoàn thành sau này
                            $ims->db->query("UPDATE product_order SET token='".$nl_result->token."' WHERE order_code='".$order['order_code']."' ");
                            $result = (array)$nl_result->checkout_url;
                            $data_return['ok'] = 1;
                            $data_return['link'] = $result[0];
                        }else{
                            echo $nl_result->error_message;
                        }
                    }else{
                        echo "<h3> Bạn chưa nhập đủ thông tin khách hàng </h3>";
                    }
                }
            } elseif($method['name_action'] == 'vnpay'){
                $vnpay = $ims->func->unserialize($method['arr_option']);

                $vnp_TmnCode    = $vnpay['TerminalId']; //Mã website tại VNPAY                 
                $vnp_HashSecret = $vnpay['SecretKey']; //Chuỗi bí mật
                $vnp_Url        = (!empty($method['is_prod']))?$method['link_api']:$method['link_sanbox'];
                $vnp_Returnurl  = ($link_go != '') ? $link_go : $ims->site_func->get_link('product', $ims->setting['product']['ordering_complete_link']);
                
                $vnp_TxnRef     = $order['order_code']; // Mã đơn hàng.
                $vnp_OrderInfo  = "Thanh toán cho đơn hàng #".$vnp_TxnRef;
                $vnp_OrderType  = "billpayment";
                $vnp_Amount     = (int)$order['total_payment'] * 100;
                $vnp_Locale     = "vn";
                $vnp_BankCode   = "";
                $vnp_IpAddr     = $_SERVER['REMOTE_ADDR'];
                $vnp_Bill_Address = !empty($ims->conf['address'])?$ims->conf['address']:'VN';
                $vnp_Bill_City = "Ho Chi Minh";
                $vnp_Bill_Country = "VN";
                $vnp_Inv_Phone = str_replace(' ','',$ims->conf['hotline']);
                $vnp_Inv_Email = !empty($ims->conf['email'])?$ims->conf['email']:"mail@gmail.com";
                $vnp_Inv_Customer = !empty($ims->conf['company'])?$ims->conf['company']:"Công ty";
                $vnp_Inv_Address = !empty($ims->conf['address'])?$ims->conf['address']:'VN';
                $vnp_Inv_Company = !empty($ims->conf['company'])?$ims->conf['company']:"Công ty";
                $vnp_Inv_Taxcode = !empty($ims->conf['mst'])?str_replace(' ','',$ims->conf['mst']):0;
                $vnp_Inv_Type = "I";
                $startTime = date("YmdHis");
                
                if(isset($ims->data['user_cur']['full_name']) && trim($ims->data['user_cur']['full_name'])!=''){
                    $name = explode(' ', $ims->data['user_cur']['full_name']);
                    $vnp_Bill_FirstName = array_shift($name);
                    $vnp_Bill_LastName = array_pop($name);
                    $vnp_Bill_LastName = !empty($vnp_Bill_LastName)?$vnp_Bill_LastName:$vnp_Bill_FirstName;
                }

                $inputData = array(
                    "vnp_Version" => "2.1.0",
                    "vnp_TmnCode" => $vnp_TmnCode,
                    "vnp_Amount" => $vnp_Amount,
                    "vnp_Command" => "pay",
                    "vnp_CreateDate" => $startTime,
                    "vnp_CurrCode" => "VND",
                    "vnp_IpAddr" => $vnp_IpAddr,
                    "vnp_Locale" => $vnp_Locale,
                    "vnp_OrderInfo" => $vnp_OrderInfo,
                    "vnp_OrderType" => $vnp_OrderType,
                    "vnp_ReturnUrl" => $vnp_Returnurl,
                    "vnp_TxnRef" => $vnp_TxnRef,
                    "vnp_ExpireDate"=> date('YmdHis',strtotime('+15 minutes',strtotime($startTime))),
                    "vnp_Bill_Mobile"=> str_replace(' ','',$order['o_phone']),
                    "vnp_Bill_Email"=> !empty($ims->conf['email']) ? $ims->conf['email'] : 'abc@gmail.com',
                    "vnp_Bill_FirstName"=>$vnp_Bill_FirstName,
                    "vnp_Bill_LastName"=>$vnp_Bill_LastName,
                    "vnp_Bill_Address"=>$vnp_Bill_Address,
                    "vnp_Bill_City"=>$vnp_Bill_City,
                    "vnp_Bill_Country"=>$vnp_Bill_Country,
                    "vnp_Inv_Phone"=>$vnp_Inv_Phone,
                    "vnp_Inv_Email"=>$vnp_Inv_Email,
                    "vnp_Inv_Customer"=>$vnp_Inv_Customer,
                    "vnp_Inv_Address"=>$vnp_Inv_Address,
                    "vnp_Inv_Company"=>$vnp_Inv_Company,
                    "vnp_Inv_Taxcode"=>$vnp_Inv_Taxcode,
                    "vnp_Inv_Type"=>$vnp_Inv_Type
                );

                if (isset($vnp_BankCode) && $vnp_BankCode != "") {
                    $inputData['vnp_BankCode'] = $vnp_BankCode;
                }
                ksort($inputData);
                $query = "";
                $i = 0;
                $hashdata = "";
                foreach ($inputData as $key => $value) {
                    if ($i == 1) {
                        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                    } else {
                        $hashdata .= urlencode($key) . "=" . urlencode($value);
                        $i = 1;
                    }
                    $query .= urlencode($key) . "=" . urlencode($value) . '&';
                }
                $vnp_Url = $vnp_Url."?".$query;
                if (isset($vnp_HashSecret)) {
                    // $vnpSecureHash = hash('sha512', $vnp_HashSecret . $hashdata);
                    $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
                }

                Session::Delete('cart_pro');
                Session::Delete('cart_info');
                Session::Delete('cart_list_pro');
                Session::Delete('ordering_address');
                Session::Delete('promotion_code');
                Session::Delete('user_contributor');

                Session::Delete('ticket_selected');
                Session::Delete('vat');
                unset($_COOKIE['type_contributor']);
                setcookie('type_contributor', '', time() - 3600, '/');
                unset($_COOKIE['user_contributor']);
                setcookie('user_contributor', '', time() - 3600, '/');
                // Cập nhât order với token  $nl_result->token để sử dụng check hoàn thành sau này
                $ims->db->query("UPDATE $table SET token='".$vnpSecureHash."', is_show=0 WHERE order_code='".$order['order_code']."' ");
                $data_return['ok'] = 1;
                $data_return['link'] = $vnp_Url;
            } elseif ($method['name_action'] == 'onepay') {
                $arr_option = $ims->func->unserialize($method['arr_option']);
                $vpcURL = "https://mtf.onepay.vn/onecomm-pay/vpc.op"."?";
                $SECURE_SECRET = $arr_option['SecureHash'];
                $arr_onepay = array(
                    "vpc_Merchant"       => $arr_option['Merchant'],
                    "vpc_AccessCode"     => $arr_option['AccessCode'],
                    "Title"              => "VPC 3-Party",
                    "vpc_MerchTxnRef"    => $order['order_code'],
                    "vpc_OrderInfo"      => "Thanh toán cho đơn hàng #".$order['order_code'],
                    "vpc_Amount"         => (int)$order['total_payment']*100,
                    "vpc_ReturnURL"      => $this->get_link('product', $ims->setting['product']['ordering_complete_link']),
                    "vpc_Version"        => "2",
                    "vpc_Command"        => "pay",
                    "vpc_Locale"         => "vn",
                    "vpc_Currency"       => "VND",
                    "vpc_TicketNo"       => $_SERVER['REMOTE_ADDR'],
                    "vpc_SHIP_Street01"  => $order['d_address'],
                    "vpc_SHIP_Provice"   => "",
                    "vpc_SHIP_City"      => "",
                    "vpc_SHIP_Country"   => "Viet Nam",
                    "vpc_Customer_Phone" => $ims->func->if_isset($order['d_phone'], ""),
                    "vpc_Customer_Email" => $ims->func->if_isset($order['d_full_name'], ""),
                    "vpc_Customer_Id"    => $ims->func->if_isset($order['user_id'], ""),
                );
                $stringHashData = "";
                ksort ($arr_onepay);
                $appendAmp = 0;
                foreach($arr_onepay as $key => $value) {
                    if (strlen($value) > 0) {
                        if ($appendAmp == 0) {
                            $vpcURL .= urlencode($key).'='.urlencode($value);
                            $appendAmp = 1;
                        } else {
                            $vpcURL .= '&'.urlencode($key)."=".urlencode($value);
                        }
                        if ((strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
                            $stringHashData .= $key."=".$value."&";
                        }
                    }
                }
                $stringHashData = rtrim($stringHashData, "&");
                if (strlen($SECURE_SECRET) > 0) {
                    $vpcURL .= "&vpc_SecureHash=" . strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*',$SECURE_SECRET)));
                }

                Session::Delete('cart_pro');
                Session::Delete('cart_info');
                Session::Delete('cart_list_pro');
                Session::Delete('ordering_address');
                Session::Delete('promotion_code');
                Session::Delete('user_contributor');
                unset($_COOKIE['type_contributor']);
                setcookie('type_contributor', '', time() - 3600, '/');
                unset($_COOKIE['user_contributor']);
                setcookie('user_contributor', '', time() - 3600, '/');
                // Cập nhât order với token  $nl_result->token để sử dụng check hoàn thành sau này
                $ims->db->query("UPDATE product_order SET token='".$stringHashData."' WHERE order_code='".$order['order_code']."' ");
                $data_return['ok'] = 1;
                $data_return['link'] = $vpcURL;
            } elseif ($method['name_action'] == 'momo') {
                $arr_option = $ims->func->unserialize($method['arr_option']);                
                // $endpoint    = $method['link_api']."/gw_payment/transactionProcessor";
                // $endpoint    = $method['link_sanbox']."/gw_payment/transactionProcessor";
                $endpoint    = ((!empty($method['is_prod']))?$method['link_api']:$method['link_sanbox']);
                $endpoint    .= "/gw_payment/transactionProcessor";
                $partnerCode = $arr_option["PartnerCode"];
                $accessKey   = $arr_option["AccessKey"];
                $secretKey   = $arr_option["SecretKey"];
                $orderInfo   = "Thanh toán qua MoMo cho đơn hàng ".$order['order_code'];
                $amount      = "".$order['total_payment']."";
                $orderId     = $order['order_code']."";                
                $returnUrl   = $this->get_link('product', $ims->setting['product']['ordering_complete_link']); // Result
                $notifyurl   = $this->get_link('product', $ims->setting['product']['momoipn_link']); //ipn_momo.php";
                
                // Lưu ý: link notifyUrl không phải là dạng localhost
                $extraData   = "merchantName=MoMo Partner";
                $requestId   = time() . "";
                $requestType = "captureMoMoWallet";
                $rawHash = "partnerCode=" . $partnerCode . "&accessKey=" . $accessKey . "&requestId=" . $requestId . "&amount=" . $amount . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&returnUrl=" . $returnUrl . "&notifyUrl=" . $notifyurl . "&extraData=" . $extraData;
                $signature = hash_hmac("sha256", $rawHash, $secretKey);
                $data = array(
                    'partnerCode' => $partnerCode,
                    'accessKey'   => $accessKey,
                    'requestId'   => $requestId,
                    'amount'      => $amount,
                    'orderId'     => $orderId,
                    'orderInfo'   => $orderInfo,
                    'returnUrl'   => $returnUrl,
                    'notifyUrl'   => $notifyurl,
                    'extraData'   => $extraData,
                    'requestType' => $requestType,
                    'signature'   => $signature
                );
                $ch = curl_init($endpoint);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: '.strlen(json_encode($data)))
                );
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                $result = curl_exec($ch);
                curl_close($ch);                
                $jsonResult = json_decode($result, true);  // decode json                                
                $ims->db->query("UPDATE product_order SET token='".$signature."',is_show=0 WHERE order_code='".$order['order_code']."' ");
                $data_return['ok'] = 1;
                $data_return['link'] = $jsonResult['payUrl'];  
            } elseif ($method['name_action'] == 'alepay') {
                $order_code = $order['order_code'];
                $config     = $ims->conf['rootpath'].'modules'.DS.'payment_method'.DS.$method['name_action'].DS.'config.php';
                $file       = $ims->conf['rootpath'].'modules'.DS.'payment_method'.DS.$method['name_action'].DS.'Lib/Alepay.php';

                if (file_exists($file) && file_exists($config)) {                               
                    require_once($config);
                    require_once($file);                                
                    $alepay = new Alepay($config);
                    $data_alepay = array();
                    $data_alepay['cancelUrl'] = $this->get_link('product',$ims->setting['product']['ordering_complete_link']);
                    $data_alepay['amount']    = (int)$order['total_payment'];
                    $data_alepay['orderCode'] = $order['order_code'];
                    $data_alepay['currency'] = 'VND';
                    $data_alepay['orderDescription'] = "Thanh toán cho đơn hàng ".$order['order_code'];
                    $data_alepay['totalItem'] = 1;
                    $data_alepay['checkoutType'] = 1; // Thanh toán trả góp
                    $data_alepay['buyerName'] = trim($order['o_full_name']);
                    $data_alepay['buyerEmail'] = trim($order['o_email']);
                    $data_alepay['buyerPhone'] = trim($order['o_phone']);
                    $data_alepay['buyerAddress'] = trim($order['o_address']);
                    $data_alepay['buyerCity'] = trim($ims->func->location_name('province', $order['o_province']));
                    $data_alepay['buyerCountry'] = 'Việt Nam';
                    $data_alepay['month'] = 3;
                    $data_alepay['paymentHours'] = 48; //48 tiếng :  Thời gian cho phép thanh toán (tính bằng giờ)          
                    foreach ($data_alepay as $k => $v) {
                        if (empty($v)) {
                            $alepay->return_json("NOK", "Bắt buộc phải nhập/chọn tham số [ " . $k . " ]");
                            die();
                        }
                    }
                    $data_alepay['allowDomestic'] = false;
                    $result = $alepay->sendOrderToAlepay($data_alepay); // Khởi tạo
                    if (isset($result) && !empty($result->checkoutUrl)) {
                        Session::Delete('cart_pro');
                        Session::Delete('cart_info');
                        Session::Delete('cart_list_pro');
                        Session::Delete('ordering_address');
                        Session::Delete('promotion_code');
                        Session::Delete('user_contributor');
                        unset($_COOKIE['type_contributor']);
                        setcookie('type_contributor', '', time() - 3600, '/');
                        unset($_COOKIE['user_contributor']);
                        setcookie('user_contributor', '', time() - 3600, '/');
                        $ims->db->query("UPDATE product_order SET token='".$result->token."' WHERE order_code='".$order['order_code']."' ");
                        $alepay->return_json('OK', 'Thành công', $result->checkoutUrl);
                        $ims->html->redirect_rel($result->checkoutUrl);
                    } else {
                        echo $result->errorDescription;
                    }
                }
            } elseif($method['name_action'] == 'paypal') {
                $arr_option = $ims->func->unserialize($method['arr_option']);
                $tmp = explode(',', $ims->conf['email']);
                $email = $tmp[0];
                $paypal = array(
                    'client_key' => $arr_option["ClientId"],
                    'secret_key' => $arr_option["SecretKey"],
                );
                $paypal_url      = (!empty($method['is_prod']))?$method['link_api']:$method['link_sanbox'];
                // $paypal_token = $this->paypal_api("https://api.sandbox.paypal.com/v1/oauth2/token",$paypal);
                $paypal_token = $this->paypal_api($paypal_url."v1/oauth2/token",$paypal);

                $exchange_rate = $ims->db->load_item('event_setting', 'setting_key = "exchange_rate"', 'setting_value');
                $order_up['total_payment'] = round((INT)$order_up['total_payment'] / ((INT)$exchange_rate), 2);
                $request_params = array(
                    "intent" => "CAPTURE",
                    "purchase_units" => array(array(                       
                        "amount" => array(
                            "currency_code" => "USD",                           
                            "value" => (string)$order_up['total_payment'],
                        ),
                    )),
                    "application_context" => array(
                        "return_url" => ($link_go != '') ? $link_go : $this->get_link('product', $ims->setting['product']['ordering_complete_link']),
                        "cancel_url" => ($link_go != '') ? $link_go."?cancel=1" : $this->get_link('product', $ims->setting['product']['ordering_complete_link'])."?cancel=1",
                    ),
                );
                $request_params = json_encode($request_params);
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    // CURLOPT_URL => "https://api.sandbox.paypal.com/v2/checkout/orders?",
                    CURLOPT_URL => $paypal_url."v2/checkout/orders?",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => false,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                        "Authorization: Bearer ".$paypal_token['access_token'],
                    ),
                    CURLOPT_POSTFIELDS => $request_params,
                ));
                $result = curl_exec($curl);
                curl_close($curl);
                parse_str($result, $parse_result);                
                $jsonResult = json_decode($result, true);
                $link_go = '';            
                if(isset($jsonResult['status']) && $jsonResult['status']=="CREATED"){
                    $ims->db->query("UPDATE $table SET token='".$jsonResult['id']."',is_show=0 WHERE order_code='".$order['order_code']."' ");
                    foreach ($jsonResult['links'] as $row) {
                        if($row['rel'] == 'approve'){
                            $link_go = $row['href'];
                        }
                    }
                    if(!empty($link_go)){
                        Session::Delete('cart_pro');
                        Session::Delete('cart_info');
                        Session::Delete('cart_list_pro');
                        Session::Delete('ordering_address');
                        Session::Delete('promotion_code');
                        Session::Delete('user_contributor');

                        Session::Delete('ticket_selected');
                        Session::Delete('vat');
                        unset($_COOKIE['type_contributor']);
                        setcookie('type_contributor', '', time() - 3600, '/');
                        unset($_COOKIE['user_contributor']);
                        setcookie('user_contributor', '', time() - 3600, '/');
                        // $ims->html->redirect_rel($link_go);
                        $data_return['ok'] = 1;
                        $data_return['link'] = $link_go;
                    }
                }
            }
        }
        return $data_return;
    }

    // Hoàn tất/ Hủy thanh toán
    function paymentCustomComplete($type=array(), $link = '', $table = 'product_order'){
        global $ims;
        $ims->func->load_language('product');

        $output = array();
        $output['notification_payment'] = 'Đã xảy ra lỗi hệ thống. Vui lòng liên hệ hotline để được hỗ trợ';
        $output['status_payment'] = 'danger';
        $method = $ims->db->load_row('order_method', 'name_action="'.$type.'" AND lang="'.$ims->conf['lang_cur'].'" AND is_show=1');
        $arr_option = $ims->func->unserialize($method['arr_option']);
        if ($type == 'vnpay') {
            $vnpay = $arr_option;
            $vnp_TmnCode    = $vnpay['TerminalId']; //Mã website tại VNPAY
            $vnp_HashSecret = $vnpay['SecretKey']; //Chuỗi bí mật
            $vnp_Url        = (!empty($method['is_prod']))?$method['link_api']:$method['link_sanbox'];
            $vnp_Returnurl  = ($link != '') ? $link : $this->get_link('product', $ims->setting['product']['ordering_complete_link']);
            $vnp_SecureHash = $ims->get['vnp_SecureHash'];
            $inputData = array();
            foreach ($ims->get as $key => $value) {
                if (substr($key, 0, 4) == "vnp_") {
                    $inputData[$key] = $value;
                }
            }

            unset($inputData['vnp_SecureHashType']);
            unset($inputData['vnp_SecureHash']);
            ksort($inputData);
            $i = 0;
            $hashData = "";
            // print_arr($inputData);
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
            }            
            $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
            if ($secureHash == $vnp_SecureHash) {
                if ($ims->get['vnp_ResponseCode'] == '00') {
                    // Thành công
                    $order_code = $ims->get['vnp_TxnRef']; // Mã đơn hàng.
                    $arr_payment = array(
                        'is_check_payment_online' => 1
                    );
                    $ok = $ims->db->do_update($table, $arr_payment,' order_code ="'.$order_code.'" ');
                    $order = $ims->db->load_row($table, "order_code='".$order_code."'");
                    if($order){
                        if($order['is_status_payment'] == 3){
                            $output['notification_payment'] = 'Bạn vừa thanh toán thành công cho đơn hàng #'.$order_code;
                            $output['status_payment'] = 'success';
                        }//elseif($order['is_status_payment'] == 3){
//                            $output['notification_payment'] = 'Đơn hàng #'.$order_code.' đã được thanh toán. Vui lòng quay trở lại';
//                            $output['status_payment'] = 'info';
//                        }
                    }//else{
//                        $order = $ims->db->load_row("user_package_log", "order_code='".$order_code."'");
//                        if($order['is_paymented'] == 0){
//                            $output['notification_payment'] = 'Mua gói sao thành công';
//                            $output['status_payment'] = 'success';
//                        }elseif($order['is_paymented'] == 1){
//                            $output['notification_payment'] = 'Mua gói sao thành công';
//                            $output['status_payment'] = 'info';
//                        }
//                    }
                }elseif($ims->get['vnp_ResponseCode'] == '24'){
                    $output['notification_payment'] = 'Bạn đã hủy thanh toán.';
                    $output['status_payment'] = 'danger';
                }elseif($ims->get['vnp_ResponseCode'] == '99'){
                    $output['notification_payment'] = 'Bạn đã hủy thanh toán.';
                    $output['status_payment'] = 'danger';
                }
            }
        } elseif ($type == 'nganluong') {
            $config = $ims->conf['rootpath'].'modules'.DS.'payment_method'.DS.$type.DS.'v31/config.php';
            $file   = $ims->conf['rootpath'].'modules'.DS.'payment_method'.DS.$type.DS.'v31/include/NL_Checkoutv3.php';
            require_once($config);
            require_once($file );

            $nlcheckout= new NL_CheckOutV3 (MERCHANT_ID,MERCHANT_PASS,RECEIVER,URL_API);
            $nl_result = $nlcheckout->GetTransactionDetail($ims->get['token']);
            $notification_payment = $status_payment ='';
            $orderInfo = $ims->db->load_row("product_order", "order_code='".$ims->get['order_code']."' and token='".$ims->get['token']."'");
            if($nl_result && !empty($orderInfo) && $orderInfo['is_status_payment'] == 0){
                $nl_errorcode           = (string)$nl_result->error_code;
                $nl_transaction_status  = (string)$nl_result->transaction_status;
                $nl_transaction_id      = (string)$nl_result->transaction_id;
                $token_nl               = $ims->get['token'];
                $payment_id             = $ims->get['order_id'];
                $order_code             = $ims->get['order_code'];
                if($nl_errorcode == '00') {
                    if($nl_transaction_status == '00') {
                        $arr_payment = array(
                            'is_show' => 1,
                            'is_status_payment' => 3,
//                            'is_ConfirmOrder' => 1,
//                            'is_ConfirmPayment' => 1,
                            'transaction_id' => $nl_transaction_id,
                        );
                        $ok = $ims->db->do_update('product_order', $arr_payment,' order_code ="'.$order_code.'" ');
                        if ($ok){
                            $output['notification_payment'] = 'Bạn vừa thanh toán thành công cho đơn hàng #'.$order_code;
                            $output['status_payment'] = 'success';
                        }
                    }
                }
            }
        } elseif ($type == 'onepay') {
            $SECURE_SECRET = "";
            $merchTxnRef = $this->null2unknownONEPAY($ims->get["vpc_MerchTxnRef"]);
            $Order = $ims->db->load_row("product_order", "order_code='".$merchTxnRef."'");
            if (!empty($Order)) {
                $method = $ims->db->load_row("order_method", "method_id='".$Order['method']."'");
                if (!empty($method)) {
                    $arr_option = $ims->func->unserialize($method['arr_option']);
                    $SECURE_SECRET = $arr_option['SecureHash'];
                }
            }

            $vpc_Txn_Secure_Hash = $ims->get["vpc_SecureHash"];
            $errorExists = false;
            ksort ($ims->get);
            if (strlen ( $SECURE_SECRET ) > 0 && $ims->get ["vpc_TxnResponseCode"] != "7" && $ims->get ["vpc_TxnResponseCode"] != "No Value Returned") {
                $stringHashData = "";
                foreach ($ims->get as $key => $value ) {
                    if ($key != "vpc_SecureHash" && (strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
                        $stringHashData .= $key."=".$value."&";
                    }
                }
                $stringHashData = rtrim($stringHashData, "&");  
                if (strtoupper($vpc_Txn_Secure_Hash) == strtoupper(hash_hmac('SHA256',$stringHashData,pack('H*',$SECURE_SECRET)))) {
                    $hashValidated = "CORRECT";
                } else {
                    $hashValidated = "INVALID HASH";
                }
            } else {
                $hashValidated = "INVALID HASH";
            }
            $amount          = $this->null2unknownONEPAY($ims->get["vpc_Amount"]);
            $locale          = $this->null2unknownONEPAY($ims->get["vpc_Locale"]);
            $command         = $this->null2unknownONEPAY($ims->get["vpc_Command"]);
            $version         = $this->null2unknownONEPAY($ims->get["vpc_Version"]);
            $orderInfo       = $this->null2unknownONEPAY($ims->get["vpc_OrderInfo"]);
            $merchantID      = $this->null2unknownONEPAY($ims->get["vpc_Merchant"]);
            $transactionNo   = $this->null2unknownONEPAY($ims->get["vpc_TransactionNo"]);
            $txnResponseCode = $this->null2unknownONEPAY($ims->get["vpc_TxnResponseCode"]);
            $transStatus     = "";
            if($hashValidated == "CORRECT" && $txnResponseCode=="0"){
                $Order = $ims->db->load_row("product_order", "order_code='".$merchTxnRef."' AND is_status_payment=0");
                if (!empty($Order)) {
                    $arr_payment = array(
                        'is_status_payment' => 3,
                        'is_ConfirmOrder'   => 1,
                        'is_ConfirmPayment' => 1,
                        'transaction_id'    => $transactionNo,
                    );
                    $ok = $ims->db->do_update('product_order', $arr_payment,' order_code ="'.$merchTxnRef.'" ');
                    if ($ok){
                        $output['notification_payment'] = 'Bạn vừa thanh toán thành công cho đơn hàng #'.$merchTxnRef;
                        $output['status_payment'] = 'success';
                    }
                }
            }elseif ($hashValidated=="INVALID HASH" && $txnResponseCode=="0"){
                $output['notification_payment'] = 'Thanh toán đang chờ. Vui lòng liên hệ hotline để được hỗ trợ';
                $output['status_payment'] = 'warning';
            }else {
                $output['notification_payment'] = 'Đã xảy ra lỗi hệ thống. Vui lòng liên hệ hotline để được hỗ trợ';
                $output['status_payment'] = 'danger';
            }
        } elseif ($type == 'momo') {

            $secretKey      = $arr_option["SecretKey"];
            $partnerCode    = $ims->get["partnerCode"];
            $accessKey      = $ims->get["accessKey"];
            $orderId        = $ims->get["orderId"];
            $localMessage   = $ims->get["localMessage"];
            $message        = $ims->get["message"];
            $transId        = $ims->get["transId"];
            $orderInfo      = $ims->get["orderInfo"];
            $amount         = $ims->get["amount"];
            $errorCode      = $ims->get["errorCode"];
            $responseTime   = $ims->get["responseTime"];
            $requestId      = $ims->get["requestId"];
            $extraData      = $ims->get["extraData"];
            $payType        = $ims->get["payType"];
            $orderType      = $ims->get["orderType"];
            $extraData      = $ims->get["extraData"];
            $m2signature    = $ims->get["signature"]; //MoMo signature
            
            //Checksum
            $rawHash = "partnerCode=" . $partnerCode . "&accessKey=" . $accessKey . "&requestId=" . $requestId . "&amount=" . $amount . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo .
                "&orderType=" . $orderType . "&transId=" . $transId . "&message=" . $message . "&localMessage=" . $localMessage . "&responseTime=" . $responseTime . "&errorCode=" . $errorCode .
                "&payType=" . $payType . "&extraData=" . $extraData;
            $partnerSignature = hash_hmac("sha256", $rawHash, $secretKey);
            if ($m2signature == $partnerSignature) {
                if ($errorCode == '0') {
                    $Order = $ims->db->load_row("product_order", "order_code='".$orderId."'");
                    if (!empty($Order)) {
                        $arr_payment = array(
                            'is_show' => 1,
                            'is_status_payment' => 3,
                            'is_ConfirmOrder' => 1,
                            'is_ConfirmPayment' => 1,
                            'transaction_id' => $transId,
                        );
                        $ok = $ims->db->do_update('product_order', $arr_payment,' order_code ="'.$orderId.'" ');
                        if ($ok){
                            $output['notification_payment'] = 'Bạn vừa thanh toán thành công cho đơn hàng #'.$orderId;
                            $output['status_payment'] = 'success';
                        }
                    }
                } 
            } 
        } elseif ($type == 'alepay') {
            $config = $ims->conf['rootpath'].'modules'.DS.'payment_method'.DS.$type.DS.'config.php';
            $file   = $ims->conf['rootpath'].'modules'.DS.'payment_method'.DS.$type.DS.'Lib/Alepay.php';
            if (file_exists($file) && file_exists($config)) {   
                require_once($config);
                require_once($file);
                $alepay   = new Alepay($config);
                $utils    = new AlepayUtils();
                $result   = $utils->decryptCallbackData($ims->get['data'], $config['encryptKey']);
                $obj_data = json_decode($result);
                if (!empty($obj_data)) {
                    if (isset($obj_data->errorCode) && $obj_data->errorCode == '000') {
                        $Order = $ims->db->load_row("product_order", "token='".$obj_data->data."' AND is_status_payment=0");
                        if (!empty($Order)) {
                            $arr_payment = array(
                                'is_status_payment' => 3,
                                'is_ConfirmOrder' => 1,
                                'is_ConfirmPayment' => 1
                            );
                            $ok = $ims->db->do_update('product_order', $arr_payment,' order_code ="'.$Order['order_code'].'" ');
                            if ($ok){
                                $output['notification_payment']='Bạn vừa thanh toán thành công cho đơn hàng #'.$Order['order_code'];
                                $output['status_payment'] = 'success';
                            }
                        }
                    }
                }
            }
        } else if ($type == 'paypal') {
            $token = $ims->get['token'];
            $payerid = isset($ims->get['PayerID']) ? $ims->get['PayerID'] : '';
            $cancel = isset($ims->get['cancel']) ? $ims->get['cancel'] : '';
            $Order = $ims->db->load_row($table, "token='".$token."'");
            if(!$cancel){
                if (!empty($Order)) {
                    $method = $ims->db->load_row("order_method", "method_id='".$Order['method']."'");
                    if (!empty($method)) {
                        $arr_option = $ims->func->unserialize($method['arr_option']);
                        $paypal = array(
                            'client_key' => $arr_option["ClientId"],
                            'secret_key' => $arr_option["SecretKey"],
                        );

                        $paypal_url      = (!empty($method['is_prod'])) ? $method['link_api'] : $method['link_sanbox'];
                        // $paypal_token = $this->paypal_api("https://api.sandbox.paypal.com/v1/oauth2/token",$paypal);
                        $paypal_token = $this->paypal_api($paypal_url."v1/oauth2/token",$paypal);
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            // CURLOPT_URL => "https://api.sandbox.paypal.com/v2/checkout/orders/".$token,
                            CURLOPT_URL => $paypal_url."v2/checkout/orders/".$token,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => false,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "GET",
                            CURLOPT_HTTPHEADER => array(
                                "Content-Type: application/json",
                                "Authorization: Bearer ".$paypal_token['access_token']
                            ),
                        ));

                        $result = curl_exec($curl);
                        curl_close($curl);
                        $result = json_decode($result,true);

                        if($result['status'] == 'APPROVED'){
                            if (!empty($Order)) {
                                $auth = $paypal_url."v2/checkout/orders/".$token."/capture";
                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                    CURLOPT_URL => $auth,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => "",
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => false,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => "POST",
                                    CURLOPT_HTTPHEADER => array(
                                        "Content-Type: application/json",
                                        "Authorization: Bearer ".$paypal_token['access_token']
                                    ),
                                ));
                                $auth_result = curl_exec($curl);
                                curl_close($curl);
                                $auth_result = json_decode($auth_result,true);
                                $arr_payment = array(
                                    'is_show' => 1,
                                    'transaction_id' => $payerid,
                                    'is_status_payment' => 3,
//                                'is_ConfirmOrder' => 1,
//                                'is_ConfirmPayment' => 1,
                                    'is_check_payment_online' => 1
                                );
                                $ok = $ims->db->do_update($table, $arr_payment,' order_code ="'.$Order['order_code'].'" ');
                                if ($ok){
                                    $output['notification_payment'] = 'Bạn vừa thanh toán thành công cho đơn hàng #'.$Order['order_code'];
                                    $output['status_payment'] = 'success';
                                }
                            }
                        }else{
                            $arr_payment = array(
                                'is_check_payment_online' => 1,
                            );
                            $ok = $ims->db->do_update($table, $arr_payment,' order_code ="'.$Order['order_code'].'" ');
                            if ($ok){
                                $output['notification_payment'] = 'Có lỗi xảy ra!';
                                $output['status_payment'] = 'danger';
                            }
                        }
                    }
                }
            }else{
                $arr_payment = array(
                    'is_check_payment_online' => 1,
                );
                $ok = $ims->db->do_update($table, $arr_payment,' order_code ="'.$Order['order_code'].'" ');
                if ($ok){
                    $output['notification_payment'] = 'Bạn đã hủy thanh toán';
                    $output['status_payment'] = 'danger';
                }
            }
        }
        return $output;
    }

    function null2unknownONEPAY($data) {
        if ($data == "") {
            return "No Value Returned";
        } else {
            return $data;
        }
    }

    function getResponseDescriptionONEPAY($responseCode) {
        switch ($responseCode) {
            case "0" :
                $result = "Giao dịch thành công - Approved";
                break;
            case "1" :
                $result = "Ngân hàng từ chối giao dịch - Bank Declined";
                break;
            case "3" :
                $result = "Mã đơn vị không tồn tại - Merchant not exist";
                break;
            case "4" :
                $result = "Không đúng access code - Invalid access code";
                break;
            case "5" :
                $result = "Số tiền không hợp lệ - Invalid amount";
                break;
            case "6" :
                $result = "Mã tiền tệ không tồn tại - Invalid currency code";
                break;
            case "7" :
                $result = "Lỗi không xác định - Unspecified Failure ";
                break;
            case "8" :
                $result = "Số thẻ không đúng - Invalid card Number";
                break;
            case "9" :
                $result = "Tên chủ thẻ không đúng - Invalid card name";
                break;
            case "10" :
                $result = "Thẻ hết hạn/Thẻ bị khóa - Expired Card";
                break;
            case "11" :
                $result = "Thẻ chưa đăng ký sử dụng dịch vụ - Card Not Registed Service(internet banking)";
                break;
            case "12" :
                $result = "Ngày phát hành/Hết hạn không đúng - Invalid card date";
                break;
            case "13" :
                $result = "Vượt quá hạn mức thanh toán - Exist Amount";
                break;
            case "21" :
                $result = "Số tiền không đủ để thanh toán - Insufficient fund";
                break;
            case "99" :
                $result = "Người sủ dụng hủy giao dịch - User cancel";
                break;
            default :
                $result = "Giao dịch thất bại - Failured";
        }
        return $result;
    }

    function header($id=0, $tokenApi=''){
        global $ims; 

        $data = array();
        $ims->site_func->setting('product');
        //=================================================================
        $box_lang = $ims->site->box_lang ($ims->conf["lang_cur"], 0);

//        $box_lang = '';
        $data['box_lang'] = $box_lang;
        $data['logo'] = $ims->site->get_logo ('logo');
//        $data['box_search'] = $ims->site->box_search ('product');
//        $data['header_user'] = $ims->site->header_user ();
//        $data['header_cart'] = $ims->site->header_cart ();
        $data['list_menu'] = $ims->site->list_menu ('menu_header', 'menu_bootstrap');
//        $data['create_event_link'] = $ims->func->get_link($ims->setting['product']['create_link'],'');

        // Not default
//        $data['banner_top'] = $ims->site->get_banner('banner-top-page', 1, 0);
        // Not default

//        $data['notifications_link'] = $this->get_link ("user",$ims->setting["user"]["notifications_link"]);
//        $data['link_action'] = $this->get_link ("user",$ims->setting["user"]["ordering_link"]);
//        $data['num_no'] = 0;
//        if ($ims->site_func->checkUserLogin() == 1) {
//            $sql = "SELECT * FROM user_notification WHERE is_show = 1 AND lang ='" . $ims->conf['lang_cur'] . "' and (type=0 OR find_in_set('".$ims->data['user_cur']['user_id']."', user_id)) ";
//            $query = $ims->db->query($sql);
//            $num_spam = $ims->db->num_rows($query);
//            if ($num_spam > 0) {
//                while ($row = $ims->db->fetch_row($query)) {
//                    if (!empty($row['is_view'])) {
//                        $row['is_view'] = explode(",", $row['is_view']);
//                        if (in_array($ims->data['user_cur']['user_id'], $row['is_view'])) {
//                        } else {
//                            $data['num_no']++;
//                        }
//                    } else {
//                        $data['num_no']++;
//                    }
//                }
//            }
//        }else{
//            $data['num_no'] = $ims->db->do_get_num('user_notification','lang="'.$ims->conf['lang_cur'].'" AND is_show=1 AND type="0" AND "'.time().'" < date_create+(3600*24*30)');
//        }
        if ($ims->site_func->checkUserLogin() == 1) {
            $picture = $ims->db->load_item('user', 'user_id = '.$ims->data['user_cur']['user_id'], 'picture');
            if($picture){
                $data['pic_user'] = '<img src="'.$ims->func->get_src_mod($picture, 38, 38, 1, 1).'">';
            }else{
                $data['pic_user'] = '<i class="fas fa-user"></i>';
            }
        }else{
            $signin_link = $ims->db->load_item('user_setting', 'setting_key = "signin_link"', 'setting_value');
            $data['pic_user'] = '<a href="'.$ims->func->get_link($signin_link, '').'"><i class="fas fa-user"></i></a>';
        }

        $ims->temp_html->assign('CONF',$ims->conf);
        $ims->temp_html->assign('LANG',$ims->lang);
        $ims->temp_html->assign('data',$data);
        $ims->temp_html->parse('header');
        return $ims->temp_html->text('header');
    }

    function footer($id=0,$tokenApi=''){
        global $ims;

        $data = array();
        //=================================================================        
        $data['bg_footer'] = $ims->site->get_bg_img ('bg-footer');
        $data['payment'] = $ims->site->get_banner ('payment');
        $data['dkbct'] = $ims->site->get_banner ('dkbct');
        $data['menu_footer'] = $ims->site->list_menu('menu_footer', 'menu');
        $data['menu_footer1'] = $ims->site->list_menu('menu_footer1', 'menu');
        $data['menu_footer2'] = $ims->site->list_menu('menu_footer2', 'menu');
        $data['menu_aside'] = $ims->site->list_menu ('menu_mobile','menu_aside');
        $data['footer_logo'] = $ims->site->get_banner ('logo-footer');
        $data['footer'] = $ims->site->get_banner ('footer');
        $data['app'] = $ims->site->get_banner('app',2);        
        $data['payment'] = $ims->site->get_banner ('payment',0);
        $data['bank'] = $ims->site->get_banner ('bank-list',0);
        $data['feature'] = $ims->site->get_banner ('feature',3);
        $data['register_mail'] = $ims->site->register_mail ();
        $data['popup'] = $ims->site->popup_banner();
        $data['contact_link'] = $this->get_link('contact');
        //=================================================================        
        // $id = $ims->db->load_item('interface_page','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and footer!=0','footer');
        $interface = array();
        // Lấy token api
        // $tokenApi = $this->getToken();
        $response = $this->sendPostData("http://theme.thietkewebsite.info.vn/api.php/getInterface", "", "get", $tokenApi);
        $response = json_decode($response);
        if (isset($response->error)) {
            $error = $response->error;
            if ($error->error_code == 0) {
                $interfaceAPI = $response->data;
                foreach ($interfaceAPI as $k => $v) {                    
                    $interface[$v->item_id]['item_id'] = $v->item_id;
                    $interface[$v->item_id]['picture'] = $v->picture;
                    $interface[$v->item_id]['type'] = $v->type;
                    $interface[$v->item_id]['path'] = $v->path;
                    $interface[$v->item_id]['short'] = $v->short;
                }
            }
        }
        $footer = '';
        if(isset($interface[$id])){
            $temp = $interface[$id]['path'];
            $dir = (isset($_SERVER["HTTPS"])?'https':'http').'://'."theme.thietkewebsite.info.vn/temp/default/interface/".$temp;
            // $ims->func->include_css ($dir.'/'.$temp.'.css');
            $ims->func->include_part_css($dir.'/'.$temp.'.css');
            if($interface[$id]['short']!=''){
                $footer = $interface[$id]['short'];            
            }else{           
                $footer = file_get_contents($dir.'/'.$temp.'.tpl');           
            }
            // $dir = $ims->conf["rootpath"]."temp".DS.$ims->conf['webtempfolder'].DS.'interface'.DS.$temp;        
            $ims->conf['tag_footer'] = $ims->func->input_editor_decode($ims->conf['tag_footer']);
            $ims->temp_footer = new XTemplate($footer);
            $ims->temp_footer->assign('CONF',$ims->conf);
            $ims->temp_footer->assign('LANG',$ims->lang);
            $ims->temp_footer->assign('data',$data);
            $ims->temp_footer->parse('footer');
            return $ims->temp_footer->text('footer');
        }
    }


    function sendPostDataGHTK($url, $post, $method = 'post', $token = 0, $type = 0){
        $curl = curl_init();
        switch ($method) {
            case 'put':
                $header = array(
                    "Content-Type:multipart/form-data",
                    "Retailer:vinafecom", 
                    "Authorization:Bearer ".$token."");
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_URL             => $url,
                    CURLOPT_CUSTOMREQUEST   => "PUT",
                    CURLOPT_HTTPHEADER      => $header,
                    CURLOPT_SSL_VERIFYPEER  => 0,
                    CURLOPT_POSTFIELDS      => $post
                ));
                break;
            case 'post':
                $header = array(
                    "Content-Type:application/json",
                    "Token: ".$token."");
                if ($type == 1) {
                    $header = array("Content-Type:application/x-www-form-urlencoded");
                }
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_URL             => $url,
                    CURLOPT_POST            => 1,
                    CURLOPT_HTTPHEADER      => $header,
                    CURLOPT_SSL_VERIFYPEER  => 0,
                    CURLOPT_POSTFIELDS      => $post
                ));
                break;
            case 'get':
                $header = array(
                    "Token: ".$token."
                ");
                curl_setopt_array($curl, array(
                    CURLOPT_URL             => $url,
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST   => "GET",
                    CURLOPT_HTTPHEADER      => $header,
                ));
                break;
            case 'delete':
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_URL             => $url,
                    CURLOPT_CUSTOMREQUEST   => "DELETE",
                    CURLOPT_HTTPHEADER      => $header,
                    CURLOPT_SSL_VERIFYPEER  => 0,
                    CURLOPT_POSTFIELDS      => $post
                ));
            default:
                break;
        }
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp;
    }

    public function apiGHN($name='', $dataAPI = array(), $token=''){
        global $ims;

        $output = array();
        $urlRootGHN = $ims->conf['URL_API_GHN'];
        switch ($name) {
            case 'Getfee':
                // Gethubs GHN 
                $urlAPI = $urlRootGHN."shiip/public-api/v2/shipping-order/fee";
                $dataAPI = json_encode($dataAPI);
                $result  = $this->sendPostDataGHN($urlAPI, $dataAPI, 'post', $token, 0, 2);
                $result  = json_decode($result);
                $output  = $result;
                break;
            case 'GetShop':
                // Lấy tất cả cửa hàng
                $urlAPI = $urlRootGHN."shiip/public-api/v2/shop/all";
                $dataAPI = json_encode($dataAPI);
                $result  = $this->sendPostDataGHN($urlAPI, $dataAPI, 'post', $token, 0, 2);
                $result  = json_decode($result);
                $output  = $result;
                break;
            case 'SignIn':
                // Signin GHN 
                $urlAPI  = $urlRootGHN.'SignIn';
                $dataAPI = array(
                    "token"    => "TokenStaging",
                    "Email"    => $username,
                    "Password" => $password
                );
                $dataAPI = json_encode($dataAPI);
                $result  = $this->sendPostData($urlAPI, $dataAPI, 'post', 2);
                $result  = json_decode($result);
                $output  = $result;
                break;
            case 'GetDistricts':
                // Gethubs GHN 
                $urlAPI = $urlRootGHN."GetDistricts";
                $dataAPI = array(
                    "token"    => $token,
                );
                $dataAPI = json_encode($dataAPI);
                $result  = $this->sendPostData($urlAPI, $dataAPI, 'post', 2);
                $result  = json_decode($result);
                $output  = $result;
                break;
            case 'FindAvailableServices':
                // FindAvailableServices GHN 
                $urlAPI  = $urlRootGHN."FindAvailableServices";
                $dataAPI = json_encode($dataAPI);
                $result  = $this->sendPostData($urlAPI, $dataAPI, 'post', 2);
                $result  = json_decode($result);
                $output  = $result;
                break;
            case 'CreateOrder':
                // CreateOrder GHN 
                $urlAPI  = $urlRootGHN."shipping-order/create";
                $dataAPI = json_encode($dataAPI);
                $result  = $this->sendPostDataGHN($urlAPI, $dataAPI, 'post', $token, 1257, 2);
                $result  = json_decode($result);
                $output  = $result;
                break;
            case 'GetWards':
                // GetWards GHN 
                $urlAPI  = $urlRootGHN."GetWards";
                $dataAPI = json_encode($dataAPI);
                $result  = $this->sendPostData($urlAPI, $dataAPI, 'post', 2);
                $result  = json_decode($result);
                $output  = $result;
                break;
            default:
                break;
        }
        return $output;
    }


    function sendPostDataGHN($url, $post, $method = 'post', $token = 0, $ShopId = 0, $type = 0){
        $curl = curl_init();
        switch ($method) {
            case 'put':
                $header = array(
                    "Content-Type:multipart/form-data",
                    "Retailer:vinafecom", 
                    "Authorization:Bearer ".$token."");
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_URL             => $url,
                    CURLOPT_CUSTOMREQUEST   => "PUT",
                    CURLOPT_HTTPHEADER      => $header,
                    CURLOPT_SSL_VERIFYPEER  => 0,
                    CURLOPT_POSTFIELDS      => $post
                ));
                break;
            case 'post':
                $header = array(
                    "token: ".$token."",
                    "Content-Type:application/json",
                    "ShopId:".$ShopId.""
                );
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_URL             => $url,
                    CURLOPT_POST            => 1,
                    CURLOPT_HTTPHEADER      => $header,
                    CURLOPT_SSL_VERIFYPEER  => 0,
                    CURLOPT_POSTFIELDS      => $post
                ));
                break;
            case 'get':
                $header = array(
                    "Token: ".$token."
                ");
                curl_setopt_array($curl, array(
                    CURLOPT_URL             => $url,
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST   => "GET",
                    CURLOPT_HTTPHEADER      => $header,
                ));
                break;
            case 'delete':
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_URL             => $url,
                    CURLOPT_CUSTOMREQUEST   => "DELETE",
                    CURLOPT_HTTPHEADER      => $header,
                    CURLOPT_SSL_VERIFYPEER  => 0,
                    CURLOPT_POSTFIELDS      => $post
                ));
            default:
                break;
        }
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp;
    }

    public function sendPostData($url, $post, $method = 'post', $type = 0, $token='', $header = array()){
        $curl = curl_init();
        switch ($method) {
            case 'get':
                if (empty($header)) {
                    $header = array("Authorization: Bearer ".$token."");
                }
                curl_setopt_array($curl, array(
                    CURLOPT_URL             => $url,
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST   => "GET",
                    CURLOPT_HTTPHEADER      => $header,
                ));
                break;
            case 'put':
                $header = array(
                    "Content-Type:multipart/form-data",
                    "Authorization:Bearer ".$token."");
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_URL             => $url,
                    CURLOPT_CUSTOMREQUEST   => "PUT",
                    CURLOPT_HTTPHEADER      => $header,
                    CURLOPT_SSL_VERIFYPEER  => 0,
                    CURLOPT_POSTFIELDS      => $post
                ));
                break;
            case 'post':                
                if ($type == 0) {
                    $header = array(
                        "Content-Type:multipart/form-data"
                    );                    
                }
                if ($type == 1) {
                    $header = array("Content-Type:application/x-www-form-urlencoded");
                }
                if ($type == 2) {
                    $header = array(
                        "Content-Type:application/json"
                    );
                }
                if ($type == 3) {
                    $header = array(
                        "Content-Type:application/json",
                        "Token: ".$token,
                        "Content-Length: ".strlen($post),
                    );
                }                
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_URL             => $url,
                    CURLOPT_POST            => 1,
                    CURLOPT_HTTPHEADER      => $header,
                    CURLOPT_SSL_VERIFYPEER  => 0,
                    CURLOPT_POSTFIELDS      => $post
                ));
                break;
            case 'delete':
                $header = array(
                        "Content-Type:application/json",
                        "Retailer:vinafecom", 
                        "Authorization:Bearer ".$token."");
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_URL             => $url,
                    CURLOPT_CUSTOMREQUEST   => "DELETE",
                    CURLOPT_HTTPHEADER      => $header,
                    CURLOPT_SSL_VERIFYPEER  => 0,
                    CURLOPT_POSTFIELDS      => $post
                ));
            default:
                break;
        }
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp;
    }

    function getRestfulToken(){
        global $ims;

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
                "exp" => $now_seconds+(60*60*24),  // Maximum expiration time is 24h
            );
            $jwt = JWT::encode($payload, $private_key, "HS256");
            $ims->db->query("DELETE FROM api_token WHERE date_expired<'".time()."' ");
            $arr_ins                 = array();
            $arr_ins['account_id']   = 1;
            $arr_ins['token']        = $token = $jwt;
            $arr_ins['date_expired'] = $now_seconds+(60*15*10);
            $arr_ins['date_create']  = $now_seconds;
            $arr_ins['date_update']  = $now_seconds;
            $ims->db->do_insert("api_token", $arr_ins);
        }
        return $token;
    }
    
    function form_rate($type='', $id=''){
        global $ims;

        $ims->func->include_css($ims->dir_js . "starrr/starrr.css");
        $ims->func->include_js($ims->dir_js  . "starrr/starrr.js");
        $data = array();
        if ($type == 'product') {
            $data['product'] = $ims->db->load_row('product', 'item_id="'.$id.'" and is_show=1 and lang="'.$ims->conf['lang_cur'].'" ');            
        }

        $total_rate = 0;
        $data['average'] = 0;
        $data['count_1star'] = 0;
        $data['count_2star'] = 0;
        $data['count_3star'] = 0;
        $data['count_4star'] = 0;
        $data['count_5star'] = 0;
        $data['count_1percent'] = 0;
        $data['count_2percent'] = 0;
        $data['count_3percent'] = 0;
        $data['count_4percent'] = 0;
        $data['count_5percent'] = 0;
        $arr = $ims->db->load_item_arr("shared_rate", " type_id = '".$id."' AND type='".$type."' AND is_show = 1 ", 'rate');
        $data['num'] = count($arr);
        if (!empty($arr)) {
            foreach ($arr as $key => $value) {
                if ($value['rate']==1) {
                    $data['count_1star']++;
                }
                if ($value['rate']==2) {
                    $data['count_2star']++;
                }
                if ($value['rate']==3) {
                    $data['count_3star']++;
                }
                if ($value['rate']==4) {
                    $data['count_4star']++;
                }
                if ($value['rate']==5) {
                    $data['count_5star']++;
                }
                $total_rate += $value['rate'];
            }

        }
        if ($data['num']>0) {
            $data['count_1percent'] = ($data['count_1star']/$data['num'])*100;
            $data['count_2percent'] = ($data['count_2star']/$data['num'])*100;
            $data['count_3percent'] = ($data['count_3star']/$data['num'])*100;
            $data['count_4percent'] = ($data['count_4star']/$data['num'])*100;
            $data['count_5percent'] = ($data['count_5star']/$data['num'])*100;
        }
        if($total_rate != 0){
            $data['average'] = round($total_rate/$data['num'],1);
        }
        $data['total_rate'] = $total_rate;
        $ims->temp_box->assign('data', $data);
        $ims->temp_box->assign('LANG', $ims->lang);
        $ims->temp_box->parse('form_rate');
        return $ims->temp_box->text('form_rate'); 
    }

    function form_comment($type='', $id='', $type_get=0, $temp='form_comment', $num_rate=0){
        global $ims;
        $ims->func->include_js($ims->dir_js . "starrr/starrr.js");
        $ims->func->include_css($ims->dir_js . "starrr/starrr.css");

        $ims->func->include_js ($ims->func->dirModules('shared_comment', 'assets', 'js').'/shared_comment.js');
        $ims->func->include_css ($ims->func->dirModules('shared_comment', 'assets', 'css').'/shared_comment.css');

        $ims->site_func->setting('user');
        $ims->site_func->setting('shared');

        $data = array();
        $showform = 1;
        if (isset($ims->setting['shared']['is_requiredlogin'])
            && $ims->setting['shared']['is_requiredlogin']==1
            && $ims->site_func->checkUserLogin() != 1) {
            $showform = 0;
        }
        if($showform == 1 || $showform == 0) {

            $num_show = $ims->func->if_isset($ims->setting['shared']['numshow_comment'], 5);

            $data['type']    = $type;
            $data['item_id'] = $id;
            $data['link_root'] = $ims->conf['rooturl'];
            $data['form_id_pre'] = 'root_';

            if ($ims->site_func->checkUserLogin() == 1) {
                $data['attr'] = ' readonly="readonly" ';
            }
            if ($temp == 'form_comment_rate') {
                $data['rate'] = '<input type="hidden" name="rate" value="" required>';
            }
            $data['user_name']  = $ims->func->if_isset($ims->data['user_cur']['nickname']);
            $data['user_email'] = $ims->func->if_isset($ims->data['user_cur']['username']);
            $data['user_phone'] = $ims->func->if_isset($ims->data['user_cur']['phone']);
            $ims->temp_box->assign('LANG', $ims->lang);
            $ims->temp_box->assign('data', $data);
            $ims->temp_box->reset("content_comment");
            $ims->temp_box->parse("content_comment");
            $data['content_comment'] = $ims->temp_box->text("content_comment");

            $ims->temp_box->assign('LANG', $ims->lang);
            $ims->temp_box->assign('data', $data);
            $ims->temp_box->parse($temp.".is_login");

            // Danh sách nhận xét
            $num_rows_count = $ims->db->do_get_num("shared_comment", "parent_id=0 AND is_show=1 AND type_id=".$id." AND type='".$type."' AND  lang='".$ims->conf['lang_cur']."' ");
            if($num_rows_count <= $num_show){
                $num_show = $num_rows_count;
            }
            if ($type_get == 1) {
                return $num_rows_count;
            }
            $arr_comment = $ims->db->load_row_arr("shared_comment"," is_show=1 AND parent_id=0 AND type_id=".$id." AND type='".$type."' AND lang='".$ims->conf['lang_cur']."' ORDER BY date_create DESC LIMIT 0, ".$num_show."");
            $num_rows = count($arr_comment);
            if($num_rows > 0) {
                // $temp                  = 'list_comment';
                $data['start']         = $num_show;
                $data['max']           = $num_rows_count;
                $data['total_comment'] = $num_rows_count;
                if($num_show >= $num_rows_count){
                    $data['class']     = 'none';
                }
                if (!empty($arr_comment)) {
                    foreach ($arr_comment as $key => $row) {
                        $row['item_comment'] = $ims->site_func->item_comment($row, $data);
                        $ims->temp_box->assign('row', $row);
                        $ims->temp_box->parse($temp.".list_comment.item_comment");
                    }
                }
                $ims->temp_box->assign('data', $data);
                $ims->temp_box->parse($temp.'.list_comment');
            }
        } else{
            $url = $ims->func->base64_encode($_SERVER['REQUEST_URI']);
            $url = (!empty($url)) ? '/?url='.$url : '';
            $data['link_login'] = $this->get_link ('user', $ims->setting['user']['signin_link']).$url;
            $ims->temp_box->assign('LANG', $ims->lang);
            $ims->temp_box->assign('data', $data);
            $ims->temp_box->parse($temp.'.not_login');
        }
        if($temp == 'form_comment_rate'){ //rate
            $total_rate = 0;
            $data['average'] = 0;
            $data['count_1star'] = 0;
            $data['count_2star'] = 0;
            $data['count_3star'] = 0;
            $data['count_4star'] = 0;
            $data['count_5star'] = 0;
            $data['count_1percent'] = 0;
            $data['count_2percent'] = 0;
            $data['count_3percent'] = 0;
            $data['count_4percent'] = 0;
            $data['count_5percent'] = 0;
            $arr = $ims->db->load_item_arr("shared_comment", " type_id = '".$id."' AND type='".$type."' AND is_show = 1 and rate!=0", 'rate');
            $data['num'] = count($arr);
            if (!empty($arr)) {
                foreach ($arr as $key => $value) {
                    if ($value['rate']==1) {
                        $data['count_1star']++;
                    }
                    if ($value['rate']==2) {
                        $data['count_2star']++;
                    }
                    if ($value['rate']==3) {
                        $data['count_3star']++;
                    }
                    if ($value['rate']==4) {
                        $data['count_4star']++;
                    }
                    if ($value['rate']==5) {
                        $data['count_5star']++;
                    }
                    $total_rate += $value['rate'];
                }

            }
            if ($data['num']>0) {
                $data['count_1percent'] = ($data['count_1star']/$data['num'])*100;
                $data['count_2percent'] = ($data['count_2star']/$data['num'])*100;
                $data['count_3percent'] = ($data['count_3star']/$data['num'])*100;
                $data['count_4percent'] = ($data['count_4star']/$data['num'])*100;
                $data['count_5percent'] = ($data['count_5star']/$data['num'])*100;
            }
            if($total_rate != 0){
                $data['average'] = round($total_rate/$data['num'],1);
                $data['star'] = '';
                $star = $data['average'];
                $int = (int) $star;
                $decimal = $star - $int;
                for ($i=0; $i < 5; $i++) {
                    if($star >= 1){
                        $data['star'] .= '<i class="fas fa-star"></i>';
                        $star --;
                    }else{
                        if($decimal>=0.5 && $star>=0.5){
                            $data['star'] .= '<i class="fas fa-star-half-alt"></i>';
                            $star -= 0.5;
                        }else{
                            $data['star'] .= '<i class="fal fa-star"></i>';
                        }
                    }
                }
                $ims->temp_box->assign('data', $data);
                $ims->temp_box->parse($temp.'.star');
            }else{
                $data['average'] = $num_rate;
                $data['star'] = '';
//                for ($i=0; $i < 5; $i++) {
//                    $data['star'] .= '<i class="fal fa-star"></i>';
//                }
//                $ims->temp_box->assign('data', $data);
//                $ims->temp_box->parse($temp.'.star');

                $star = $data['average'];
                $int = (int) $star;
                $decimal = $star - $int;
                for ($i=0; $i < 5; $i++) {
                    if($star >= 1){
                        $data['star'] .= '<i class="fas fa-star"></i>';
                        $star --;
                    }else{
                        if($decimal>=0.5 && $star>=0.5){
                            $data['star'] .= '<i class="fas fa-star-half-alt"></i>';
                            $star -= 0.5;
                        }else{
                            $data['star'] .= '<i class="fal fa-star"></i>';
                        }
                    }
                }
                $ims->temp_box->assign('data', $data);
                $ims->temp_box->parse($temp.'.star');
            }
            $data['total_rate'] = $num_rows_count;
            if($data['total_rate'] > 0){
                $ims->temp_box->assign('data', $data);
                $ims->temp_box->parse($temp.'.total_rate');
            }
            $ims->temp_box->assign('data', $data);
        }
        $ims->temp_box->assign('LANG', $ims->lang);
        $ims->temp_box->parse($temp);
        return $ims->temp_box->text($temp);
    }

    function item_comment($row=array(), $data=array()){
        global $ims;

        $num_show = $ims->func->if_isset($ims->setting['shared']['numshow_comment_sub'], 5);

        $row['content']    = $ims->func->input_editor_decode($row['content']);
        $row['time']       = $ims->func->time_to_text($row['date_update']);
        $row['pic_comment'] = $ims->dir_images.'user.png';

        if (isset($row['load_sub'])&&$row['load_sub']==1) {
            // Không có form
        }else{
            // Load form nhận xét
            $data['parent_id']   = $row['item_id'];
            $data['form_id_pre'] = 'sub_'.$row['item_id'];
            if ($ims->site_func->checkUserLogin() == 1) {
                $data['attr'] = ' readonly="readonly" ';
            }
            if($data['parent_id']){
                $data['rate'] = '';
            }
            $data['user_name'] = $ims->func->if_isset($ims->data['user_cur']['nickname']);
            $data['user_email'] = $ims->func->if_isset($ims->data['user_cur']['username']);
            $data['user_phone'] = $ims->func->if_isset($ims->data['user_cur']['phone']);
            $ims->temp_box->assign('LANG', $ims->lang);
            $ims->temp_box->assign('data', $data);
            $ims->temp_box->reset("content_comment");
            $ims->temp_box->parse("content_comment");
            $row['content_comment'] = $ims->temp_box->text("content_comment");
        }

        // Load danh sách trả lời
        $row['item_comment'] = '';
        if (isset($data['item_id'])) {
            $num_rows_count = $ims->db->do_get_num("shared_comment", "is_show=1 AND parent_id=".$row['item_id']." AND type_id=".$data['item_id']." AND type='".$data['type']."' AND  lang='".$ims->conf['lang_cur']."' ");
            $arr_sub_comment = $ims->db->load_row_arr("shared_comment"," is_show=1 AND parent_id=".$row['item_id']." AND type_id=".$data['item_id']." AND type='".$data['type']."' AND lang='".$ims->conf['lang_cur']."' ORDER BY date_create DESC LIMIT 0, ".$num_show);
            if (!empty($arr_sub_comment)) {
                foreach ($arr_sub_comment as $row_sub) {
                    $row_sub['content']    = $ims->func->input_editor_decode($row_sub['content']);
                    $row_sub['time']       = $ims->func->time_to_text($row_sub['date_update']);
                    $row_sub['item_id_base64'] = $ims->func->base64_encode($row_sub['item_id']);
                    $row_sub['class_like'] = 'ficon-thumbs-up';
                    $row_sub['pic_comment'] = $ims->dir_images.'user.png';

                    if ($ims->site_func->checkUserLogin() == 1) {
                        $check = $ims->db->load_row("shared_favorite", " type_id = '".$row_sub['item_id']."' AND type='shared_comment' AND user_id='".$ims->data['user_cur']['user_id']."' AND is_show = 1 ", 'id');
                        if (!empty($check)) {
                            $row_sub['class_like'] = 'ficon-thumbs-up-alt';
                        }
                    }
                    $row_sub['full_name_first'] = '<div>'.substr($row_sub['full_name'], 0, 1).'</div>';
                    if ($row_sub['user_id']>0) {
                        $picture = $ims->db->load_item('user','user_id="'.$row_sub['user_id'].'"','picture');
                        if ($picture != '') {
                            $row_sub['pic_comment'] = $ims->func->get_src_mod($picture, 65, 65, 1, 1);
                            if(strpos($row_sub['pic_comment'], 'nophoto')=== false){
                                $row_sub['full_name_first'] = '<img src="'.$row_sub['pic_comment'].'" alt="'.$row['full_name'].'">';
                            }
                        }
                    }

                    $arr_picture_sub = $ims->func->unserialize($row_sub['picture']);
                    if (!empty($arr_picture_sub)) {
                        foreach ($arr_picture_sub as $k => $v_sub) {
                            $arr_v= array();
                            $arr_v['full_name'] = $row['full_name'];
                            $arr_v['picture_full'] = $ims->func->get_src_mod($v_sub);
                            $arr_v['picture'] = $ims->func->get_src_mod($v_sub, 270, 0, 1, 0, array('fix_width' => 1));
                            $ims->temp_box->assign('row', $arr_v);
                            $ims->temp_box->parse("item_comment.pic");
                        }
                    }
                    if ($row_sub['video']!='') {
                        $row_sub['video'] = $ims->func->get_youtube_link($row_sub['video']);
                        $ims->temp_box->assign('row', $row_sub);
                        $ims->temp_box->parse("item_comment.video");
                    }
                    $ims->temp_box->assign('LANG', $ims->lang);
                    $ims->temp_box->assign('row', $row_sub);
                    $ims->temp_box->reset("item_comment");
                    $ims->temp_box->parse("item_comment");
                    $row['item_comment'] .= $ims->temp_box->text("item_comment");
                }
            }
            if ($num_rows_count > $num_show) {
                $row['start'] = $num_show;
                $ims->temp_box->assign('LANG', $ims->lang);
                $ims->temp_box->assign('row', $row);
                $ims->temp_box->parse("item_comment.more");
            }
        }
        $row['item_id_base64'] = $ims->func->base64_encode($row['item_id']);
        $row['class_like'] = 'fal fa-thumbs-up';
        $row['full_name_first'] = '<div>'.substr($row['full_name'], 0, 1).'</div>';
        $row['num_comment'] = $ims->db->do_get_num("shared_comment", "is_show=1 AND parent_id=".$row['item_id']." AND type_id=".$data['item_id']." AND type='".$data['type']."' AND  lang='".$ims->conf['lang_cur']."' ");
        if ($ims->site_func->checkUserLogin() == 1) {
            $check = $ims->db->load_row("shared_favorite", " type_id = '".$row['item_id']."' AND type='shared_comment' AND user_id='".$ims->data['user_cur']['user_id']."' AND is_show = 1 ", 'id');
            if (!empty($check)) {
                $row['class_like'] = 'fas fa-thumbs-up';
            }
            if ($row['user_id']>0) {
                $picture = $ims->db->load_item('user','user_id="'.$row['user_id'].'"','picture');
                if ($picture != '') {
                    $row['pic_comment'] = $ims->func->get_src_mod($picture, 65, 65, 1, 1);
                    if(strpos($row['pic_comment'], 'nophoto')=== false){
                        $row['full_name_first'] = '<img src="'.$row['pic_comment'].'" alt="'.$row['full_name'].'">';
                    }
                }
                if(isset($row['rate']) && $row['rate'] > 0){
                    $row['rated'] = '<span class="rated">';
                    for ($i=0; $i < $row['rate']; $i++) {
                        $row['average'] = "<i class='fas fa-star' title ='".$row['rate']." sao' style='color: #ffc120'></i>";
                        $row['rated'] .= $row['average'];
                    }
                    for ($i=0; $i < 5-$row['rate']; $i++) {
                        $row['average'] = "<i class='fas fa-star' title ='".$row['rate']." sao' style='color: #b8b8b8'></i>";
                        $row['rated'] .= $row['average'];
                    }
                    $row['rated'] .= '</span>';
                }
            }
        }
        // Load nội dung nhận xét
        // $row['full_name_first'] = substr($row['full_name'], 0, 1);

        $arr_picture = $ims->func->unserialize($row['picture']);
        if (!empty($arr_picture)) {
            foreach ($arr_picture as $k => $v) {
                $arr_v= array();
                $arr_v['full_name'] = $row['full_name'];
                $arr_v['picture_full'] = $ims->func->get_src_mod($v);
                $arr_v['picture'] = $ims->func->get_src_mod($v, 270, 0, 1, 0, array('fix_width' => 1));
                $ims->temp_box->assign('row', $arr_v);
                $ims->temp_box->parse("item_comment.pic");
            }
        }
        if ($row['video']!='') {
            $row['video'] = $ims->func->get_youtube_link($row['video']);
            $ims->temp_box->assign('row', $row);
            $ims->temp_box->parse("item_comment.video");
        }
        $ims->temp_box->assign('LANG', $ims->lang);
        $ims->temp_box->assign('row', $row);
        $ims->temp_box->reset("item_comment");
        $ims->temp_box->parse("item_comment");
        return $ims->temp_box->text("item_comment");
    }

    function upload_image_multi($folder_upload, $name_input='picture', $nodup=0, $w=1000, $h=0){
        global $ims;

        $output = array(
            'ok'    => 0,
            'mess'    => '',
        );
        $arr_picture = array();

        if (is_array($_FILES[$name_input])) {
            foreach ($_FILES[$name_input] as $k_files => $v_files) {
                if ($k_files=='name') {
                    $i=0;
                    foreach ($v_files as $k => $v) {
                        if ($v!='') {
                            $target_dir =  $ims->conf['rooturl_web'].$folder_upload;
                            $target_file = $target_dir.basename($v);
                            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                            // Check if image file is a actual image or fake image
                            $check = getimagesize($_FILES[$name_input]["tmp_name"][$i]);
                            if($check !== false) {
                                //$output['mess'] =  "File is an image - " . $check["mime"] . ".";
                                $output['ok'] = 1;
                            } else {
                                $output['mess'] =  "Only image upload file!";
                                $output['ok'] = 0;
                            }
                            // Check if file already exists
                            if (file_exists($target_file)) {
                                $output['mess'] =  "Sorry, file already exists.";
                                $output['ok'] = 0;
                                return $output;
                            }
                            // Check file size
                            if ($_FILES[$name_input]["size"][$i] > 10000000) {
                                $output['mess'] =  "Sorry, the upload file must be less than 10mb.";
                                $output['ok'] = 0;
                                return $output;
                            }
                            // Allow certain file formats
                            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                                && $imageFileType != "gif" && $imageFileType != "webp") {
                                $output['mess'] =  "Sorry, only uploaded .JPG, .JPEG, .NGNG, .WEBP & .GIF files";
                                $output['ok'] = 0;
                                return $output;
                            }
                            // Check if $uploadOk is set to 0 by an error
                            if ($output['ok'] == 0) {
                                $output['mess'] =  "Upload failed!";
                                return $output;
                                // if everything is ok, try to upload file
                            } else {
                                $size = @getimagesize($_FILES[$name_input]["tmp_name"][$i]);
                                if(empty($w) && !empty($size[0])){
                                    $w = $size[0];
                                }
                                $ims->func->rmkdir($folder_upload);
                                // Save file
                                $_FILES[$name_input]["name"][$i] = str_replace(' ', '_', $_FILES[$name_input]["name"][$i]);
                                $_FILES[$name_input]["name"][$i] = $ims->func->fix_file_name($_FILES[$name_input]["name"][$i]);
                                $picname = $folder_upload.'/'.($nodup==0?time():'').'_'.$_FILES[$name_input]["name"][$i];

                                // Rotate image correctly!
                                $this->ResizeImage($_FILES[$name_input]["tmp_name"][$i], $ims->conf['rootpath'].'uploads/'.$picname, 2, $w, $h);
                                // move_uploaded_file($_FILES[$name_input]["tmp_name"], 
                                // $image = imagecreatefromstring(file_get_contents($_FILES[$name_input]["tmp_name"][$i]));
                                // $exif = exif_read_data($_FILES[$name_input]["tmp_name"][$i]);

                                // if (!empty($exif['Orientation'])) {
                                //     switch ($exif['Orientation']) {
                                //         case 1: // nothing
                                //             break;
                                //         case 2: // horizontal flip
                                //             // imageflip($image, IMG_FLIP_HORIZONTAL);
                                //             break;
                                //         case 3: // 180 rotate left
                                //             // $image = imagerotate($image, 180, 0);
                                //             break;
                                //         case 4: // vertical flip
                                //             // imageflip($image, IMG_FLIP_VERTICAL);
                                //             break;
                                //         case 5: // vertical flip + 90 rotate right
                                //             // imageflip($image, IMG_FLIP_VERTICAL);
                                //             // $image = imagerotate($image, -90, 0);
                                //             break;
                                //         case 6: // 90 rotate right
                                //             $this->correctImageOrientation($ims->conf['rootpath'].'uploads/'.$picname, 6);
                                //             break;
                                //         case 7: // horizontal flip + 90 rotate right
                                //             // imageflip($image, IMG_FLIP_HORIZONTAL);
                                //             // $image = imagerotate($image, -90, 0);
                                //             break;
                                //         case 8:    // 90 rotate left
                                //             // $image = imagerotate($image, 90, 0);
                                //             break;
                                //     }
                                // }
                                $output['url_picture']  = $picname;
                                $arr_picture[] = $output['url_picture'];
                            }
                            $i++;
                        }
                    }
                }
            }
        }

        if (is_array($arr_picture) && count($arr_picture) > 0) {
            $output['ok'] = 1;
            $arr_picture  = $ims->func->serialize($arr_picture);
        } else {
            $arr_picture = '';
        }
        $output['url_picture'] = $arr_picture;

        return $output;
    }

    function correctImageOrientation($filename, $orientation) {
        if($orientation != 1){
            $image = imagecreatefromjpeg($filename);
            switch ($orientation) {
                case 2: // horizontal flip
                    imageflip($image, IMG_FLIP_HORIZONTAL);
                    break;
                case 3: // 180 rotate left
                    $image = imagerotate($image, 180, 0);
                    break;
                case 4: // vertical flip
                    imageflip($image, IMG_FLIP_VERTICAL);
                    break;
                case 5: // vertical flip + 90 rotate right
                    imageflip($image, IMG_FLIP_VERTICAL);
                    $image = imagerotate($image, -90, 0);
                    break;
                case 6: // 90 rotate right
                    $image = imagerotate($image, -90, 0);
                    break;
                case 7: // horizontal flip + 90 rotate right
                    imageflip($image, IMG_FLIP_HORIZONTAL);
                    $image = imagerotate($image, -90, 0);
                    break;
                case 8:    // 90 rotate left
                    $image = imagerotate($image, 90, 0);
                    break;
            }
            imagejpeg($image, $filename, 40);
        } // if there is some rotation necessary
    }

    function ResizeImage($input, $output, $mode, $w, $h = 0) {

        $info = @getimagesize($input);
        $mime = $info[2];
        $fext = $mime == 1 ? 'image/gif' : ($mime == 2 ? 'image/jpeg' : ($mime == 3 ? 'image/png' : ($mime == 18 ? 'image/webp' : NULL)));
        switch($fext)
        {
            case "image/png":
                $img = ImageCreateFromPng($input);
                break;
            case "image/gif":
                $img = ImageCreateFromGif($input);
                break;
            case "image/jpeg":
                $img = ImageCreateFromJPEG ($input);
                break;
            case "image/webp":
                $img = ImageCreateFromWEBP ($input);
                break;
            default:
                break;
        }

        $image['sizeX'] = imagesx($img);
        $image['sizeY'] = imagesy($img);
        switch ($mode){
            case 1: //Quadratic Image
                $thumb = imagecreatetruecolor($w,$w);
                if($image['sizeX'] > $image['sizeY']) {
                    imagecopyresampled($thumb, $img, 0,0, ($w / $image['sizeY'] * $image['sizeX'] / 2 - $w / 2),0, $w,$w, $image['sizeY'],$image['sizeY']);
                } else {
                    imagecopyresampled($thumb, $img, 0,0, 0,($w / $image['sizeX'] * $image['sizeY'] / 2 - $w / 2), $w,$w, $image['sizeX'],$image['sizeX']);
                }
                break;
            case 2: //Biggest side given
                if($image['sizeX'] > $image['sizeY'] && $image['sizeX'] > $w) {
                    $thumb = imagecreatetruecolor($w, $w / $image['sizeX'] * $image['sizeY']);
                    imagecopyresampled($thumb, $img, 0,0, 0,0, imagesx($thumb),imagesy($thumb), $image['sizeX'],$image['sizeY']);
                }else {
                    if ($image['sizeX'] > $w || $image['sizeY'] > $w) {
                        $thumb = imagecreatetruecolor($w / $image['sizeY'] * $image['sizeX'],$w);
                        imagecopyresampled($thumb, $img, 0,0, 0,0, imagesx($thumb),imagesy($thumb), $image['sizeX'],$image['sizeY']);
                    }else{
                        move_uploaded_file($input, $output);
                    }
                }
                break;
            case 3; //Both sides given (cropping)
                $thumb = imagecreatetruecolor($w,$h);
                if($h / $w > $image['sizeY'] / $image['sizeX']) {
                    imagecopyresampled($thumb, $img, 0,0, ($image['sizeX']-$w / $h * $image['sizeY'])/2,0, $w,$h, $w / $h * $image['sizeY'],$image['sizeY']);
                } else {
                    imagecopyresampled($thumb, $img, 0,0, 0,($image['sizeY']-$h / $w * $image['sizeX'])/2, $w,$h, $image['sizeX'],$h / $w * $image['sizeX']);
                }
                break;
            case 0:
                $thumb = imagecreatetruecolor($w,$w / $image['sizeX']*$image['sizeY']);
                imagecopyresampled($thumb, $img, 0,0, 0,0, $w,$w / $image['sizeX']*$image['sizeY'], $image['sizeX'],$image['sizeY']);
            break;
        }           
        if(!file_exists($output)) imagejpeg($thumb, $output, 90);
    }

    function upload_image($folder_upload, $name_input='picture'){
        global $ims;

        $output = array(
            'ok'    => 0,
            'mess'  => '',
        );

        $target_dir =  $ims->conf['rooturl_web'].$folder_upload;
        $target_file = $target_dir . basename($_FILES[$name_input]["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES[$name_input]["tmp_name"]);
        if($check !== false) {
            //$output['mess'] =  "File is an image - " . $check["mime"] . ".";
            $output['ok'] = 1;
        } else {
            $output['mess'] =  "Chỉ được upload file hình ảnh!";
            $output['ok'] = 0;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            $output['mess'] =  "Sorry, file already exists.";
            $output['ok'] = 0;
            return $output;
        }

        // Check file size
        if ($_FILES[$name_input]["size"] > 5000000) {
            $output['mess'] =  "Xin lỗi, file upload phải nhỏ hơn 5mb.";
            $output['ok'] = 0;
            return $output;
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
            $output['mess'] =  "Xin lỗi, chỉ được upload file .JPG, .JPEG, .PNG & .GIF";
            $output['ok'] = 0;
            return $output;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($output['ok'] == 0) {
            $output['mess'] =  "Quá trình upload không thành công!";
            return $output;

        // if everything is ok, try to upload file
        } else {
            // ------------- old -------------------
//            $ims->func->rmkdir($folder_upload);
            // Save file
//            $_FILES[$name_input]["name"] = strtolower(str_replace(' ', '_', $_FILES[$name_input]["name"]));
//            $_FILES[$name_input]["name"] = $ims->func->fix_file_name($_FILES[$name_input]["name"]);
//            move_uploaded_file($_FILES[$name_input]["tmp_name"], $ims->conf['rootpath'].'uploads/'.$folder_upload.'/'.time().'_'.$_FILES[$name_input]["name"]);
//            $output['url_picture']  = $folder_upload.'/'.time().'_'.$_FILES[$name_input]["name"];
            // ------------- old -------------------

            $ims->func->rmkdir($folder_upload);
            // Save file
            $_FILES[$name_input]["name"] = strtolower(str_replace(' ', '_', $_FILES[$name_input]["name"]));
            $_FILES[$name_input]["name"] = $ims->func->fix_file_name($_FILES[$name_input]["name"]);
            $picname = $folder_upload.'/'.time().'_'.$_FILES[$name_input]["name"];

            // Rotate image correctly!
            $this->ResizeImage($_FILES[$name_input]["tmp_name"], $ims->conf['rootpath'].'uploads/'.$picname, 2, 700);
            // move_uploaded_file($_FILES[$name_input]["tmp_name"],
            // $image = imagecreatefromstring(file_get_contents($_FILES[$name_input]["tmp_name"][$i]));
            // $exif = exif_read_data($_FILES[$name_input]["tmp_name"][$i]);

            // if (!empty($exif['Orientation'])) {
            //     switch ($exif['Orientation']) {
            //         case 1: // nothing
            //             break;
            //         case 2: // horizontal flip
            //             // imageflip($image, IMG_FLIP_HORIZONTAL);
            //             break;
            //         case 3: // 180 rotate left
            //             // $image = imagerotate($image, 180, 0);
            //             break;
            //         case 4: // vertical flip
            //             // imageflip($image, IMG_FLIP_VERTICAL);
            //             break;
            //         case 5: // vertical flip + 90 rotate right
            //             // imageflip($image, IMG_FLIP_VERTICAL);
            //             // $image = imagerotate($image, -90, 0);
            //             break;
            //         case 6: // 90 rotate right
            //             $this->correctImageOrientation($ims->conf['rootpath'].'uploads/'.$picname, 6);
            //             break;
            //         case 7: // horizontal flip + 90 rotate right
            //             // imageflip($image, IMG_FLIP_HORIZONTAL);
            //             // $image = imagerotate($image, -90, 0);
            //             break;
            //         case 8:    // 90 rotate left
            //             // $image = imagerotate($image, 90, 0);
            //             break;
            //     }
            // }
            $output['url_picture']  = $picname;
        }
        return $output;
    }

    // kiểm tra các tài khoản con
    function checkUserChilden($row=array()) {
        global $ims;

        $output = 0;
        $arr_parent = $ims->db->load_row('user', 'is_show=1 AND user_contributor="'.$row['user_code'].'"');
        if(!empty($arr_parent)){
            $output = 1;
        }
        
        return $output;
    }

    function list_product_bypromotion($row='',$flag=1){
        global $ims;

        $where = $temp = ''; 
        $array_where = array();
        $list_product = $row['apply_product'];
        $list_group = $row['apply_group'];
        if($list_group == '' && $list_product == ''){
            return ' and find_in_set(item_id, "") ';
        } 
        if (strpos($list_group, ',') !== false) {
            $arr_group_nav = explode(',',$list_group);
            $i = 0;
            foreach($arr_group_nav as $value){
                $i++;
                array_push($array_where, "find_in_set('" . $value . "', group_nav)");
            }
            $temp = implode(' or ', $array_where);
            if($list_product != ''){
                $temp .= " or find_in_set(item_id, '".$list_product."') ";
            }
            $where = ' and ('.$temp.') ';
            if($flag!=1){
                $where = ' and !('.$temp.') ';
            }
        }
        else{
            if($list_group != ''){
                $temp = " find_in_set('" . $list_group . "', group_nav) ";
            }
            if($list_product != ''){
                if($temp != ''){
                    $temp .= 'or';
                }
                $temp .= " find_in_set(item_id, '".$list_product."') ";
            }
            $where = ' and ('.$temp.') ';
            if($flag!=1){
                $where = ' and !('.$temp.') ';
            }
        }
        return $where;
    }

    // function check_product_not_promo(){
    //     global $ims;
    //     $data = $where = $where2 = '';
    //     $arr_temp = array(
    //         'apply_product' => '',
    //         'apply_group' => '',
    //     );
    //     $arr_promo = $ims->db->load_item_arr('product_promotion','lang="'.$ims->conf['lang_cur'].'" AND time_begin < "'.date('H:i:s').'" AND time_end > "'.date('H:i:s').'" AND date_begin < "'.time().'" AND date_end > "'.time().'" AND is_show = 1 ORDER BY show_order DESC , date_update DESC','apply_product,apply_group');
    //     if($arr_promo){
    //         foreach ($arr_promo as $row) {
    //             if($row['apply_product'] != ''){
    //                 $arr_temp['apply_product'] .= ','.$row['apply_product'];    
    //             }
    //             if($row['apply_group'] != ''){
    //                 $arr_temp['apply_group'] .= ','.$row['apply_group'];
    //             }                
    //         }
    //     } 
    //     $arr_temp['apply_product'] = substr($arr_temp['apply_product'],1);
    //     $arr_temp['apply_group'] = substr($arr_temp['apply_group'],1);
    //     if($arr_temp['apply_product'] != '' || $arr_temp['apply_group'] !=''){
    //         $ims->data['product_promotion'] = $arr_temp;
    //         $where .= $this->list_product_bypromotion($arr_temp,0);
    //         $arr_product = $ims->db->load_item_arr('product','lang="'.$ims->conf['lang_cur'].'" and is_show=1'.$where,'item_id,price_promotion');
    //         if($arr_product){
    //             foreach ($arr_product as $row) {
    //                 if($row['price_promotion']!=0){
    //                     $SQL_UPDATE = "UPDATE product SET price_promotion = 0 WHERE is_show = 1 AND lang = '".$ims->conf['lang_cur']."' AND item_id='".$row['item_id']."'";
    //                     $ims->db->query($SQL_UPDATE);
    //                 }
    //             }
    //         }
    //     }        
    // }
       
    function get_info_promotion($row=array(), $type=0){
        global $ims;

        $data = array();
        $output = array();
        $array_where = array();
        $where = "";
        if (strpos($row['group_nav'], ',') !== false) {
            $arr_group_nav = explode(',',$row['group_nav']);
            $i = 0;
            foreach($arr_group_nav as $value){
                $i++;
                array_push($array_where, "find_in_set('" . $value . "', apply_group)");
            }
            $where = implode(' or ', $array_where);
            if($row['item_id'] != ''){
                $where .= " or find_in_set('".$row['item_id']."', apply_product) ";
            }
            $where = 'AND ('.$where.') ';
        }
        else{
            if($row['group_nav'] != ''){
                $where = " find_in_set('" . $row['group_nav'] . "', apply_group) ";
            }
            if($row['item_id'] != ''){
                if($where != ''){
                    $where .= 'or';
                }
                $where .= " find_in_set('".$row['item_id']."', apply_product) ";
            }
            $where = 'AND ('.$where.') ';
        }
        // echo $where;
        $sql = "SELECT * FROM product_promotion WHERE time_begin < '".date('H:i:s')."' AND time_end > '".date('H:i:s')."' AND date_begin < ".time()." AND date_end > ".time()." AND is_show = 1 ". $where ." ORDER BY show_order DESC , date_update DESC LIMIT 0,1";        
        $result = $ims->db->query($sql);
        if($result){
            $row_promotion = $ims->db->fetch_row($result);
            if (!empty($row_promotion)) {
                if(time() > $row_promotion['date_begin'] && time() < $row_promotion['date_end']){                
                    $output['content'] = $row_promotion['content'];
                    $output['short'] = $row_promotion['short'];
                    $output['link'] = $this->get_link('product', $row_promotion['friendly_link']);
                    $output['title'] = $row_promotion['title'];                
                    $output['quantity'] = isset($row_promotion['quantity'])?$row_promotion['quantity']:0;                
                    $output['date_begin'] = $row_promotion['date_begin'];
                    $output['date_end'] = $row_promotion['date_end'];
                    $output['value_type'] = $row_promotion['value_type'];
                    $output['value'] = $row_promotion['value'];
                    // if(isset($row_promotion['value_type']) && $row_promotion['value_type'] == 0){
                    //     $output['price_buy'] = $row['price_sale'] - $row_promotion['value'];
                    //     if($output['price_buy'] <= 0){
                    //         $output['price_buy'] = $row['price_sale'];
                    //     }
                    // }elseif(isset($row_promotion['value_type']) && $row_promotion['value_type'] == 1){
                    //     $output['price_buy'] = $row['price_sale'] - ($row['price_sale']*$row_promotion['value']/100);
                    // }               
                    
                }            
            }
        }
        return $output;
    }
   
    function checkContributor(){
        global $ims;
        $ok = 0;

        if($ims->site_func->checkUserLogin() == 1){
            $check_referred = $ims->db->load_item('user_recommend_log', 'is_show = 1 and referred_user_id = '.$ims->data['user_cur']['user_id'], 'id');
            if(!$check_referred ){
                $ok = 1;
            }
        }else{
            if(!isset($_COOKIE['deeplink']) && !isset($_COOKIE['user_contributor'])){
                $ok = 1;
            }
        }

        if($ok == 1){
            $this->setting('user');
            $contributor_old = $contributor = isset($ims->get['contributor']) ? $ims->get['contributor'] : '';
            $type = isset($ims->get['type']) ? $ims->get['type'] : '';
            if($contributor != ''){
                $contributor = $ims->func->base64_decode($contributor);
                $recommend_user = $ims->db->load_row('user', 'is_show = 1 and user_code = "'.$contributor.'"', 'user_id');
                if($recommend_user){
                    $cookie_time = time()+86400*30;
                    setcookie('user_contributor', $contributor, $cookie_time,"/");
                    setcookie('type_contributor', $type, $cookie_time,"/");
                    Session::Set('user_contributor', $contributor);
                    Session::Set('type_contributor', $type);

                    if($ims->site_func->checkUserLogin() == 1 && $recommend_user['user_id'] != $ims->data['user_cur']['user_id']) {
                        // Thêm data vào bảng user_recommend_log
                        $recommend_log = array(
                            'type' => 'contributor',
                            'recommend_user_id' => $recommend_user['user_id'],
                            'recommend_link' => 'contributor='.$contributor_old.'&type='.$type,
                            'referred_user_id' => $ims->data['user_cur']['user_id'],
                            'referred_full_name' => $ims->data['user_cur']["full_name"],
                            'referred_phone' => $ims->data['user_cur']["phone"],
                            'referred_email' => $ims->data['user_cur']["email"],
                            'is_show' => 1,
                            'date_create' => time(),
                            'date_update' => time(),
                        );
                        $ims->db->do_insert("user_recommend_log", $recommend_log);
                    }
                    if ($type == 'fb') {
                        $share_link = $this->get_link('user', '', $ims->setting['user']['share_link'], array('user' => $contributor_old));
                        header("Location: $share_link");
                        EXIT;
                    }
                }
            }
        }
    }

    // Get avatar Google
    function getAvatarGoogle($image='', $fullpath = null, $filename=''){
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $img_save_location = 'uploads/'.$fullpath.$filename.'.'.$ext;
        file_put_contents($img_save_location, file_get_contents($image));
        return $fullpath.$filename.'.'.$ext;
    }

    // Get avatar Facebook
    function getAvatarFacebook($fid='', $fullpath = null, $filename=''){
        $ext = 'jpg';
        /*Facebook user image width*/
        $width="200";
        /*Facebook user image height*/
        $height="200";
        /*This is the actual url of the Facebook users image*/
        $fb_url  = "https://graph.facebook.com/".$fid."/picture?width=$width&height=$height";

        /*Path to the location to save the image on your server*/
        $img_save_location = 'uploads/'.$fullpath.$filename.'.'.$ext;
        /*Use file_put_contents to get and save image*/
        file_put_contents($img_save_location, file_get_contents($fb_url));

        return $fullpath.$filename.'.'.$ext;
    }

    // đăng nhập bằng google
    function loginWithGoogle(){
        @session_start();
        global $ims;

        require_once ($ims->conf["rootpath"].DS."config".DS."gg".DS."autoload.php");

        //Insert your cient ID and secret 
        //You can get it from : https://console.developers.google.com/
        $client_id = client_id_google;
        $client_secret = client_secret_google;
        $redirect_uri = redirect_uri_google;


        //incase of logout request, just unset the session var
        if (isset($ims->get['logout'])) {
          unset($_SESSION['access_token']);
        }

        /************************************************
          Make an API request on behalf of a user. In
          this case we need to have a valid OAuth 2.0
          token for the user, so we need to send them
          through a login flow. To do this we need some
          information from our API console project.
         ************************************************/
        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        $client->addScope("email");
        $client->addScope("profile");

        /************************************************
          When we create the service here, we pass the
          client to it. The client then queries the service
          for the required scopes, and uses that when
          generating the authentication URL later.
         ************************************************/
        $service = new Google_Service_Oauth2($client);

        /************************************************
          If we have a code back from the OAuth 2.0 flow,
          we need to exchange that with the authenticate()
          function. We store the resultant access token
          bundle in the session, and redirect to ourself.
        */

          
          
        if (isset($ims->get['code'])) {
          $client->authenticate($ims->get['code']);
          $_SESSION['access_token'] = $client->getAccessToken();
        }

        /************************************************
          If we have an access token, we can make
          requests, else we generate an authentication URL.
         ************************************************/
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
          $client->setAccessToken($_SESSION['access_token']);
        } else {
          $authUrl = $client->createAuthUrl();
        }

        if (isset($authUrl)){ 
            //show login url
            echo '<div align="center">';
            echo '<h3>Login with Google -- Demo</h3>';
            echo '<div>Please click login button to connect to Google.</div>';
            echo '<a class="login" href="' . $authUrl . '"><img src="images/google-login-button.png" /></a>';
            echo '</div>';
            
        } else {
            
            $user = $service->userinfo->get(); //get user info             
            // connect to database
            if(isset($user->name) && $user->name != ''){
                $sql = "SELECT * from user where gg_id = '".$user->id."' limit 0,1";
                $num_rows = $ims->db->num_rows($ims->db->query($sql));
                $result = $ims->db->fetch_row($ims->db->query($sql));


                if($num_rows > 0){
                    Session::Set('user_cur', array(
                        'userid' => $result['user_id'],
                        'gg_id' => $result["gg_id"],
                        'username' => $result['username'],
                        'password' => '',
                        'session' => ''
                    ));
                    $link_go = $ims->site_func->get_link('user');
                    $ims->html->redirect_rel($link_go);
                }elseif(!empty($ims->data['user_cur']['user_id']) && empty($ims->data['user_cur']['gg_id'])){
                    $arr_up = array();
                    $arr_up['gg_id'] = $user->id;
                    $ok = $ims->db->do_update('user', $arr_up, ' user_id="'.$ims->data['user_cur']['user_id'].'" ');
                    if($ok){
                        $link_go = $ims->site_func->get_link('user',$ims->setting['user']['account_link']);
                        $ims->html->redirect_rel($link_go);
                    }
                }else{
                    // insert usert

                    $arr_in["email"] = $user->email;
                    $arr_in["username"] = !empty($user->email)?$user->email:$ims->func->fix_name_action($user->name).'-'.$user->id;
                    $arr_in["full_name"] = $user->name;
                    $arr_in["gg_id"] = $user->id;
                    $arr_in["folder_upload"] = $ims->db->getAutoIncrement ('user').'c'.$ims->func->random_str(4);
                    $arr_in["user_code"] = $ims->db->getAutoIncrement ('user').'c'.$ims->func->random_str(10);
                    // T?o thu m?c upload
                    $folder_conf = 'user/' . $ims->func->fix_name_action($arr_in["folder_upload"]);
                    $folder_conf .= '/';        
                    $folder_conf .= date('Y_m').'/';
                    $ims->func->rmkdir($folder_conf);
                    if ($ims->func->rmkdir($folder_conf)) {
                       $arr_in['picture'] = $this->getAvatarGoogle($user->picture, $folder_conf, $arr_in["user_code"]);
                    } 
                    $arr_in["show_order"] = 0;
                    $arr_in["is_show"] = 1;
                    $arr_in["date_login"] = time();
                    $arr_in["date_create"] = time();
                    $arr_in["date_update"] = time();
                    $ok = $ims->db->do_insert("user", $arr_in);
                    if($ok) {
                        $userid = $ims->db->insertid();
                        // $promotion['promotion_id'] = $ims->func->random_str(5, 'un');
                        // $promotion['user_id_use'] = $userid;
                        // $promotion['value_type'] = $ims->setting['promotion']['value_type'];
                        // $promotion['value'] = $ims->setting['promotion']['value'];
                        // $promotion['total_min'] = $ims->setting['promotion']['total_min'];
                        // $promotion['date_start'] = ($ims->setting['promotion']['num_use_after_day']*86400) + time();
                        // $promotion['date_end'] = $promotion['date_start'] + ($ims->setting['promotion']['num_day']*86400);
                        // $promotion['is_show'] = 1;
                        // $promotion['date_create'] = time();
                        // $promotion['date_update'] = time();
                        // $ok_code = $ims->db->do_insert("promotion", $promotion);
                        // $mail_arr_key = array(
                        //     '{nickname}',
                        //     '{username}',
                        //     '{link_active}',
                        //     '{promotion_code}'
                        // );
                        // $mail_arr_value = array(
                        //     $arr_in["nickname"],
                        //     $arr_in['username'],
                        //     $ims->site_func->get_link ('user', $ims->setting['user']["active_link"])."?code=".$arr_in["user_code"],
                        //     $promotion['promotion_id']
                        // );
                        // //send to customer
                        // $ims->func->send_mail_temp ('signup-'.$ims->setting['user']['signup_type'], $arr_in["email"], $ims->conf['email'], $mail_arr_key, $mail_arr_value);

                        if($arr_in["is_show"] == 1) {
                            Session::Set('user_cur', array(
                                'userid' => $userid,
                                'gg_id' => $arr_in["gg_id"],
                                'username' => $arr_in['username'],
                                'password' => '',
                                'session' => ''
                            ));

                            $check_log_cookie = $ims->db->load_item('user_recommend_log', 'is_show = 1 and referred_user_id = 0 and referred_email = "'.$arr_in['email'].'"', 'id');
                            if($check_log_cookie){
                                $ims->db->do_update('user_recommend_log', array('referred_user_id' => $userid), 'id = '.$check_log_cookie);
                            }else{
                                // Nhập log deeplink hoặc contributor
                                $check = $ims->db->load_item('user_recommend_log', 'is_show = 1 and referred_user_id = '.$userid, 'id');
                                if(!$check){
                                    $recommend_log = array();
                                    if(isset($_COOKIE['deeplink'])){
                                        $deeplink = $ims->db->load_row('user_deeplink', ' is_show = 1 and id = '.$_COOKIE['deeplink'], 'id, referred_member, user_id, short_code');
                                        if($deeplink){
                                            if($deeplink['user_id'] != $userid){
                                                if($deeplink['referred_member'] == ''){
                                                    $referred_member = $userid;
                                                }else{
                                                    $referred_member = $deeplink['referred_member'].','.$userid;
                                                }
                                                $ims->db->do_update('user_deeplink', array('referred_member' => $referred_member), ' id = "'.$_COOKIE['deeplink'].'"');
                                                $recommend_log = array(
                                                    'type' => 'deeplink',
                                                    'recommend_user_id' => $deeplink['user_id'],
                                                    'recommend_link' => $deeplink['short_code'],
                                                    'deeplink_id' => $deeplink['id'],
                                                    'referred_user_id' => $userid,
                                                    'is_show' => 1,
                                                    'date_create' => time(),
                                                    'date_update' => time(),
                                                );
                                            }else{
                                                setcookie('deeplink', '', time() - 3600, '/');
                                            }
                                        }
                                    }elseif(isset($_COOKIE['user_contributor'])){
                                        $contributor = $_COOKIE['user_contributor'];
                                        $type = isset($_COOKIE['type_contributor']) ? $_COOKIE['type_contributor'] : '';
                                        $recommend_user = $ims->db->load_row('user', 'is_show = 1 and user_code = "'.$contributor.'"');
                                        if($recommend_user){
                                            if($recommend_user['user_code'] != $user['user_code']){
                                                $recommend_log = array(
                                                    'type' => 'contributor',
                                                    'recommend_user_id' => $recommend_user['user_id'],
                                                    'recommend_link' => 'contributor='.$ims->func->base64_encode($contributor).'&type='.$type,
                                                    'referred_user_id' => $userid,
                                                    'is_show' => 1,
                                                    'date_create' => time(),
                                                    'date_update' => time(),
                                                );
                                            }else{
                                                setcookie('user_contributor', '', time() - 3600, '/');
                                                setcookie('type_contributor', '', time() - 3600, '/');
                                            }
                                        }
                                    }
                                    if($recommend_log){
                                        $ims->db->do_insert("user_recommend_log", $recommend_log);
                                    }
                                }
                            }
                            $link_go = $ims->site_func->get_link('user');
                            $ims->html->redirect_rel($link_go);
                        }else{
                            return -1;
                        }
                    }
                }
            }
        }
        return $user;
    }

    // đăng nhập bằng facebook
    function loginWithFacebook(){
        global $ims;

        $app_id       = app_id_facebook;
        $app_secret   = app_secret_facebook;
        $redirect_uri = redirect_uri_facebook;    
        
        // Get code value
        $code = $ims->get['code'];
        
        // Get access token info
        $facebook_access_token_uri = "https://graph.facebook.com/oauth/access_token?client_id=$app_id&redirect_uri=$redirect_uri&client_secret=$app_secret&code=$code";    
        
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $facebook_access_token_uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    
            
        $response = curl_exec($ch); 
        curl_close($ch);
        
        // Get access token
        $response = json_decode($response);
        $access_token = $response->access_token;
        // Get user infomation
        $ch = curl_init("https://graph.facebook.com/me?fields=id,name,email&access_token=$access_token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $user = json_decode($response);

        if(isset($user->name) && $user->name != ''){
                $sql = "SELECT * from user where (fb_id='".$user->id."') limit 0,1";
                $num_rows = $ims->db->num_rows($ims->db->query($sql));
                $result = $ims->db->fetch_row($ims->db->query($sql));
                if($num_rows > 0){
                    Session::Set('user_cur', array(
                        'userid' => $result['user_id'],
                        'fb_id' => $result["fb_id"],
//                            'username' => ($result['username'] != '') ? $result['username'] : $result['nickname'],
                        'username' => $result['username'],
                        'password' => '',
                        'session' => ''
                    ));
                    $link_go = $ims->site_func->get_link('user');
                    $ims->html->redirect_rel($link_go);
                }elseif(!empty($ims->data['user_cur']['user_id']) && empty($ims->data['user_cur']['fb_id'])){
                    $arr_up = array();
                    $arr_up['fb_id'] = $user->id;
                    $ok = $ims->db->do_update('user', $arr_up, ' user_id="'.$ims->data['user_cur']['user_id'].'" ');
                    if($ok){
                        $link_go = $ims->site_func->get_link('user',$ims->setting['user']['account_link']);
                        $ims->html->redirect_rel($link_go);
                    }
                }else{
                    $arr_in["username"] = !empty($user->email)?$user->email:$ims->func->fix_name_action($user->name).'-'.$user->id;
                    $arr_in["full_name"] = $user->name;
                    $arr_in["fb_id"] = $user->id;
                    $arr_in["folder_upload"] = $ims->db->getAutoIncrement ('user').'c'.$ims->func->random_str(4);
                    $arr_in["user_code"] = $ims->db->getAutoIncrement ('user').'c'.$ims->func->random_str(10);
                    // tạo thư mục upload
                    $folder_conf = 'user/' . $ims->func->fix_name_action($arr_in["folder_upload"]);
                    $folder_conf .= '/';        
                    $folder_conf .= date('Y_m').'/';
                    $ims->func->rmkdir($folder_conf);
                    if ($ims->func->rmkdir($folder_conf)) {
                       $arr_in['picture'] = $this->getAvatarFacebook($user->id, $folder_conf, $arr_in["user_code"]);
                    } 

                    $arr_in["show_order"] = 0;
                    // $arr_in["is_show"] = ($ims->setting['user']['signup_type'] == 0) ? 1 : 0;
                    $arr_in["is_show"] = 1;
                    $arr_in["date_login"] = time();
                    $arr_in["date_create"] = time();
                    $arr_in["date_update"] = time();
                    $ok = $ims->db->do_insert("user", $arr_in);

                    if($ok) {
                        $userid = $ims->db->insertid();
                        // $promotion['promotion_id'] = $ims->func->random_str(5, 'un');
                        // $promotion['user_id_use'] = $userid;
                        // $promotion['value_type'] = $ims->setting['promotion']['value_type'];
                        // $promotion['value'] = $ims->setting['promotion']['value'];
                        // $promotion['total_min'] = $ims->setting['promotion']['total_min'];
                        // $promotion['date_start'] = ($ims->setting['promotion']['num_use_after_day']*86400) + time();
                        // $promotion['date_end'] = $promotion['date_start'] + ($ims->setting['promotion']['num_day']*86400);
                        // $promotion['is_show'] = 1;
                        // $promotion['date_create'] = time();
                        // $promotion['date_update'] = time();
                        // $ok_code = $ims->db->do_insert("promotion", $promotion);
                        // $mail_arr_key = array(
                        //     '{nickname}',
                        //     '{username}',
                        //     '{link_active}',
                        //     '{promotion_code}'
                        // );
                        // $mail_arr_value = array(
                        //     $arr_in["nickname"],
                        //     $arr_in['username'],
                        //     $ims->site_func->get_link ('user', $ims->setting['user']["active_link"])."?code=".$arr_in["user_code"],
                        //     $promotion['promotion_id']
                        // );
                        // //send to customer
                        // $ims->func->send_mail_temp ('signup-'.$ims->setting['user']['signup_type'], $arr_in["email"], $ims->conf['email'], $mail_arr_key, $mail_arr_value);

                        if($arr_in["is_show"] == 1) {
                            Session::Set('user_cur', array(
                                'userid' => $userid,
                                'fb_id' => $arr_in["fb_id"],
//                                'username' => ($arr_in['username'] != '') ? $arr_in['username'] : $arr_in['nickname'],
                                'username' => $arr_in['username'],
                                'password' => '',
                                'session' => ''
                            ));

                            $check_log_cookie = $ims->db->load_item('user_recommend_log', 'is_show = 1 and referred_user_id = 0 and referred_email = "'.$arr_in['email'].'"', 'id');
                            if($check_log_cookie){
                                $ims->db->do_update('user_recommend_log', array('referred_user_id' => $userid), 'id = '.$check_log_cookie);
                            }else{
                                // Nhập log deeplink hoặc contributor
                                $check = $ims->db->load_item('user_recommend_log', 'is_show = 1 and referred_user_id = '.$userid, 'id');
                                if(!$check){
                                    $recommend_log = array();
                                    if(isset($_COOKIE['deeplink'])){
                                        $deeplink = $ims->db->load_row('user_deeplink', ' is_show = 1 and id = '.$_COOKIE['deeplink'], 'id, referred_member, user_id, short_code');
                                        if($deeplink){
                                            if($deeplink['user_id'] != $userid){
                                                if($deeplink['referred_member'] == ''){
                                                    $referred_member = $userid;
                                                }else{
                                                    $referred_member = $deeplink['referred_member'].','.$userid;
                                                }
                                                $ims->db->do_update('user_deeplink', array('referred_member' => $referred_member), ' id = "'.$_COOKIE['deeplink'].'"');
                                                $recommend_log = array(
                                                    'type' => 'deeplink',
                                                    'recommend_user_id' => $deeplink['user_id'],
                                                    'recommend_link' => $deeplink['short_code'],
                                                    'deeplink_id' => $deeplink['id'],
                                                    'referred_user_id' => $userid,
                                                    'is_show' => 1,
                                                    'date_create' => time(),
                                                    'date_update' => time(),
                                                );
                                            }else{
                                                setcookie('deeplink', '', time() - 3600, '/');
                                            }
                                        }
                                    }elseif(isset($_COOKIE['user_contributor'])){
                                        $contributor = $_COOKIE['user_contributor'];
                                        $type = isset($_COOKIE['type_contributor']) ? $_COOKIE['type_contributor'] : '';
                                        $recommend_user = $ims->db->load_row('user', 'is_show = 1 and user_code = "'.$contributor.'"');
                                        if($recommend_user){
                                            if($recommend_user['user_code'] != $user['user_code']){
                                                $recommend_log = array(
                                                    'type' => 'contributor',
                                                    'recommend_user_id' => $recommend_user['user_id'],
                                                    'recommend_link' => 'contributor='.$ims->func->base64_encode($contributor).'&type='.$type,
                                                    'referred_user_id' => $userid,
                                                    'is_show' => 1,
                                                    'date_create' => time(),
                                                    'date_update' => time(),
                                                );
                                            }else{
                                                setcookie('user_contributor', '', time() - 3600, '/');
                                                setcookie('type_contributor', '', time() - 3600, '/');
                                            }
                                        }
                                    }
                                    if($recommend_log){
                                        $ims->db->do_insert("user_recommend_log", $recommend_log);
                                    }
                                }
                            }
                            $link_go = $ims->site_func->get_link('user');
                            $ims->html->redirect_rel($link_go);
                        }else{
                            return -1;
                        }
                    }
                }
            }
    }

    // đăng nhập bằng zalo
    function loginWithZalo(){
        global $ims;
        // Get code value
        $code = $ims->get['code'];
        
        // Get access token info
        $request_url = 'https://oauth.zaloapp.com/v3/access_token?app_id='.app_id_zalo.'&app_secret='.app_secret_zalo.'&code='.$code;
        $response_access = file_get_contents($request_url);
        $response_access = json_decode( $response_access, true );        
        if (!empty($response_access)){
            $this->setting('user');
            $access_token = $response_access['access_token'];
            $profile = file_get_contents('https://graph.zalo.me/v2.0/me?access_token='.$access_token.'&fields=id,birthday,name,gender,picture.type(normal),email');
            $profile = json_decode( $profile, true );            
            // https://oauth.zaloapp.com/v3/permission?app_id=1642521612167028844&redirect_uri=https://vingreen.com.vn/thanh-vien/&state=%27            
            $sql = "SELECT * from user where zl_id = '".$profile['id']."' limit 0,1";
            $num_rows = $ims->db->num_rows($ims->db->query($sql));
            $result = $ims->db->fetch_row($ims->db->query($sql));
            if($num_rows > 0){
                Session::Set('user_cur', array(
                    'userid' => $result['user_id'],
                    'zl_id' => $result["zl_id"],
                    'username' => ($result['username'] != '') ? $result['username'] : $result['full_name'],
                    'password' => '',
                    'session' => ''
                ));
                $link_go = $ims->site->get_link ('user',$ims->setting['user']['account_link']);
                $ims->html->redirect_rel($link_go);
            }elseif(!empty($ims->data['user_cur']['user_id']) && empty($ims->data['user_cur']['zl_id'])){
                $arr_up = array();
                $arr_up['zl_id'] = $profile['id'];
                $ok = $ims->db->do_update('user', $arr_up, ' user_id="'.$ims->data['user_cur']['user_id'].'" ');
                if($ok){
                    $link_go = $ims->site_func->get_link('user',$ims->setting['user']['account_link']);
                    $ims->html->redirect_rel($link_go);
                }
            }else{
                // insert usert
                $arr_in["username"] = isset($profile['email'])?$profile['email']:$ims->func->fix_name_action($profile['name']).'-'.$profile['id'];
                $arr_in["full_name"] = $profile['name'];
                $arr_in["zl_id"] = $profile['id'];
                $arr_in["folder_upload"] = $ims->db->getAutoIncrement ('user').'c'.$ims->func->random_str(4);
                $arr_in["user_code"] = $ims->db->getAutoIncrement ('user').'c'.$ims->func->random_str(10);
                 // T?o thu m?c upload
                $folder_conf = 'user/' . $ims->func->fix_name_action($arr_in["folder_upload"]);
                $folder_conf .= '/';        
                $folder_conf .= date('Y_m').'/';
                $ims->func->rmkdir($folder_conf);
                if ($ims->func->rmkdir($folder_conf)) {
                   $arr_in['picture'] = $this->getAvatarGoogle($profile['picture']['data']['url'], $folder_conf, $arr_in["user_code"]);
                }
                $arr_in["show_order"] = 0;
                // $arr_in["is_show"] = ($ims->setting['user']['signup_type'] == 0) ? 1 : 0;
                $arr_in["is_show"] = 1;
                $arr_in["date_login"] = time();
                $arr_in["date_create"] = time();
                $arr_in["date_update"] = time();                 
                $ok = $ims->db->do_insert("user", $arr_in);

                if($ok) {
                    $userid = $ims->db->insertid();
                    // $promotion['promotion_id'] = $ims->func->random_str(5, 'un');
                    // $promotion['user_id_use'] = $userid;
                    // $promotion['value_type'] = $ims->setting['promotion']['value_type'];
                    // $promotion['value'] = $ims->setting['promotion']['value'];
                    // $promotion['total_min'] = $ims->setting['promotion']['total_min'];
                    // $promotion['date_start'] = ($ims->setting['promotion']['num_use_after_day']*86400) + time();
                    // $promotion['date_end'] = $promotion['date_start'] + ($ims->setting['promotion']['num_day']*86400);
                    // $promotion['is_show'] = 1;
                    // $promotion['date_create'] = time();
                    // $promotion['date_update'] = time();
                    // $ok_code = $ims->db->do_insert("promotion", $promotion);
                    // $mail_arr_key = array(
                    //     '{nickname}',
                    //     '{username}',
                    //     '{link_active}',
                    //     '{promotion_code}'
                    // );
                    // $mail_arr_value = array(
                    //     $arr_in["nickname"],
                    //     $arr_in['username'],
                    //     $ims->site_func->get_link ('user', $ims->setting['user']["active_link"])."?code=".$arr_in["user_code"],
                    //     $promotion['promotion_id']
                    // );
                    // //send to customer
                    // $ims->func->send_mail_temp ('signup-'.$ims->setting['user']['signup_type'], $arr_in["email"], $ims->conf['email'], $mail_arr_key, $mail_arr_value);

                    if($arr_in["is_show"] == 1) {
                        Session::Set('user_cur', array(
                            'userid' => $userid,
                            'fb_id' => $arr_in["fb_id"],
                            'username' => ($arr_in['username'] != '') ? $arr_in['username'] : $arr_in['full_name'],
                            'password' => '',
                            'session' => ''
                        ));
                        $link_go = $ims->site->get_link ('user',$ims->setting['user']['account_link']);
                        $ims->html->redirect_rel($link_go);
                    }else{
                        return -1;
                    }
                }
            }
        }
    }

    // kiểm tra tài khoản đăng nhập
    function checkUserLogin() {
        global $ims;

        $login = 0;
        $session_user_cur = Session::Get('user_cur', array());
        $ims->data['user_cur'] = (isset($ims->data['user_cur'])) ? $ims->data['user_cur'] : array();        
        if (count($ims->data['user_cur']) >= 4) {

            if ($ims->data['user_cur']["user_id"] == $session_user_cur["userid"] && $ims->data['user_cur']["username"] == $session_user_cur["username"] && $ims->data['user_cur']["password"] == $session_user_cur["password"] && $ims->data['user_cur']["session"] == $session_user_cur["session"]) {
                return 1;
            } else {
                if(isset($ims->data['user_cur']["fb_id"]) && $ims->data['user_cur']["fb_id"] != '' && $ims->data['user_cur']["fb_id"] != 0){
                        if ($ims->data['user_cur']["user_id"] == $session_user_cur["userid"] && $ims->data['user_cur']["fb_id"] == $session_user_cur["fb_id"]) {
                                return 1;
                        }
                }
                elseif(isset($ims->data['user_cur']["gg_id"]) && $ims->data['user_cur']["gg_id"] != '' && $ims->data['user_cur']["gg_id"] != 0){
                        if ($ims->data['user_cur']["user_id"] == $session_user_cur["userid"] && $ims->data['user_cur']["gg_id"] == $session_user_cur["gg_id"]) {
                                return 1;
                        }
                }
                else{
                $arr_user = Session::Get('user_cur', array(
                            'userid' => '',
                            'username' => '',
                            'password' => '',
                            'session' => ''
                ));
                return 0;
                }
            }
        } else {
            $arr_user = Session::Get('user_cur', array(
                        'userid' => '',
                        'fb_id' => '',
                        'gg_id' => '',
                        'username' => '',
                        'password' => '',
                        'session' => ''
            ));
            if(isset($arr_user["userid"]) && $arr_user["userid"] > 0) {
                $query = "SELECT * from user WHERE is_show=1 AND user_id='" . $arr_user["userid"] . "'";
                $result = $ims->db->query($query);
                if ($row = $ims->db->fetch_row($result)) {
                    if ($row["user_id"] == $arr_user["userid"] && $row["username"] == $arr_user["username"] && $row["password"] == $arr_user["password"] && $row["session"] == $arr_user["session"]) {
                        $ims->data['user_cur'] = $row;
                        $login = 1;
                    }
                    if(isset($arr_user['fb_id']) && $arr_user['fb_id'] != '' && $arr_user['fb_id'] != 0){
                        if ($row["user_id"] == $arr_user["userid"] && $row["fb_id"] == $arr_user["fb_id"]) {
                              $ims->data['user_cur'] = $row;
                              $login = 1;
                        }
                    }
                    if(isset($arr_user['gg_id']) && $arr_user['gg_id'] != '' && $arr_user['gg_id'] != 0){
                        if ($row["user_id"] == $arr_user["userid"] && $row["gg_id"] == $arr_user["gg_id"]) {
                              $ims->data['user_cur'] = $row;
                              $login = 1;
                        }
                    }
                } 
            }  
        }
        return $login;
    }

    // danh sách đã xem lưu cookie
    function addListWatchedWithCookie($item_id='') {
        global $ims;

        $status = -1;
        $cookie_name = 'list_watched';
        $cookie_time = (3600 * 24 * 30); // 30 days
        if(isset($cookie_name)){
            if(isset($_COOKIE[$cookie_name])){
                parse_str($_COOKIE[$cookie_name]);
                if($list != ''){
                    $pos = strpos($list, ',');
                    if ($pos !== false) {
                        $list_check = explode(',', $list);
                        if (in_array($item_id, $list_check)) {
                            return $list;
                        }
                        else{
                            $list = $list.','.$item_id;  
                            setcookie ($cookie_name, "list=".$list, time() + $cookie_time);
                            return $list;
                        }
                    }
                    elseif ($list != $item_id){
                         $list = $list.','.$item_id;  
                         setcookie ($cookie_name, "list=".$list, time() + $cookie_time);
                         return $list;
                    }
                    return $list;
                }
                else{
                    setcookie ($cookie_name, "list=".$item_id, time() + $cookie_time);
                    return $item_id;
                }
            }
            else{
                $cookie_name = 'list_watched';
                setcookie ($cookie_name, "list=".$item_id, time() + $cookie_time);
                return $item_id;
            }
        }
    }

    // danh sách đã xem theo tài khoản
    function addListWatched($item_id='') {
        global $ims;

        if ($ims->site_func->checkUserLogin() == 1) {
            $update = array();
            $list_watched = $ims->data['user_cur']['list_watched'];
            if($list_watched == ''){
                $arr_watched[$item_id]['id'] = $item_id;
                $arr_watched[$item_id]['date_create'] = time();
                $update['list_watched'] = $ims->func->serialize($arr_watched);                
            }else{
                $arr_search = array();
                $arr_watched = $ims->func->unserialize($list_watched);
                function sortByAmount($x, $y) {
                    if(!empty($y['date_create']) && !empty($x['date_create'])){
                        return $y['date_create'] - $x['date_create'];
                    }
                }
                usort($arr_watched, 'sortByAmount');
                $i = 0;
                foreach ($arr_watched as $key => $value){
                    if(array_search($item_id, $value)){
                        $arr_search = $arr_watched[$key];
                    }
                    if ($i>7) {
                        unset($arr_watched[$key]);
                    }
                    $i++;
                }
                if(empty($arr_search)){
                    $count = count($arr_watched) + 1;
                    $arr_watched[$count]['id'] = $item_id;
                    $arr_watched[$count]['date_create'] = time();
                    $update['list_watched'] = serialize($arr_watched);
                }
            }
            $ims->db->do_update("user", $update, "user_id='".$ims->data['user_cur']['user_id']."'");
        }
    }
   
    // ly link website theo ngôn ngữ 
    function get_link_lang_bo($lang, $modules, $action = "", $item = "", $arr_ext = array()) {
        global $ims;

        $link_out = $ims->conf['rooturl'];
        $arr_full_link = array();
        
        $this->setting($modules);

        if (in_array($modules, $arr_full_link)) {
            $link_out .= (!empty($modules)) ? $ims->setting[$modules.'_'.$lang][$modules.'_link'] . '/' : '';
            if (!empty($action)) {
                $link_out .= (!empty($action)) ? $action . '/' : '';
            }
            if (!empty($item)) {
                $link_out .= (!empty($item)) ? $item . '.html' : '';
            }
        } else {
            if (!empty($action)) {
                $link_out .= (!empty($action)) ? $action . '/' : '';
                if (!empty($item)) {
                    $link_out .= (!empty($item)) ? $item . '.html' : '';
                }
            } elseif (!empty($item)) {
                $link_out .= (!empty($item)) ? $item . '.html' : '';
            } else {
                $link_out .= (!empty($modules)) ? $ims->setting[$modules.'_'.$lang][$modules.'_link'] . '/' : '';
            }
        }
        $i = 0;
        foreach ($arr_ext as $k => $v) {
            $i++;
            $link_out .= ($i == 1) ? '/?' : '&';
            $link_out .= $k . "=" . $v;
        }
        return $link_out;
    }
    function get_link_lang($lang, $modules, $action = "", $item = "", $arr_ext = array()) {
        global $ims;

        $link_out = $ims->conf['rooturl'];
        $arr_full_link = array();

        $this->setting($modules);

        if (in_array($modules, $arr_full_link)) {
            $link_out .= (!empty($modules)) ? $ims->setting[$modules.'_'.$lang][$modules.'_link'] : '';
            if (!empty($action)) {
                $link_out .= (!empty($action)) ? $action : '';
            }
            if (!empty($item)) {
                $link_out .= (!empty($item)) ? $item : '';
            }
        } else {
            if (!empty($action)) {
                $link_out .= (!empty($action)) ? $action : '';
                if (!empty($item)) {
                    $link_out .= (!empty($item)) ? $item : '';
                }
            } elseif (!empty($item)) {
                $link_out .= (!empty($item)) ? $item : '';
            } else {
                $link_out .= (!empty($modules)) ? $ims->setting[$modules.'_'.$lang][$modules.'_link'] : '';
            }
        }
        $i = 0;
        foreach ($arr_ext as $k => $v) {
            $i++;
            $link_out .= ($i == 1) ? '/?' : '&';
            $link_out .= $k . "=" . $v;
        }
        return $link_out;
    }

    // lấy link website
    function get_link($modules, $action = "", $item = "", $arr_ext = array()) {
        global $ims;

        return $this->get_link_lang($ims->conf["lang_cur"], $modules, $action, $item, $arr_ext);
    }

    // lấy link website
    function get_link_default($modules, $action = "", $item = "", $arr_ext = array()) {
        global $ims;
        
        if ($modules == "") {
            $modules = $ims->conf['cur_mod'];
        }
        return $this->get_link_lang($ims->conf["lang_cur"], $modules, $action, $item, $arr_ext);
    }

    function get_link_ajax($modules, $fun = "mamage", $action = "", $arr_ext = array()) {
        global $ims;

        $link_out = $ims->conf['rooturl'] . "ajax.php?m=" . $modules . "&f=" . $fun;
        if($action) {
            $link_out .= "&a=" . $action;
        }
        if (!array_key_exists('lang', $arr_ext)) {
            $link_out .= "&lang=" . $ims->conf["lang_cur"];
        }
        foreach ($arr_ext as $k => $v) {
            $link_out .= "&" . $k . "=" . $v;
        }

        return $link_out;
    }

    // get link menu website
    function get_link_menu($link, $link_type = 'site') {
        global $ims;

        $arr_data = array(
            'site' => 'Nội bộ trang',
            'web'  => 'Liên kết web khác',
            'mail' => 'Thư điện tử',
            'neo'  => 'Neo trong trang',
        );
        switch ($link_type) {
            case "site":
                $link = ($link != '') ? $ims->conf['rooturl'] . $link : '';
                break;
            case "web":
                $link = $link;
                break;
            case "mail":
                $link = 'mailto:' . $link;
                break;
            case "neo":
                $link = '#' . $link;
                break;
        }
        return $link;
    }

    // get lang with module
    function get_lang($key, $module = '', $arr_replace = array()) {
        global $ims;

        $module = ($module) ? trim($module) : 'global';
        if (!isset($ims->lang[$module])) {
            $ims->func->load_language($module);
        }

        $output = (isset($ims->lang[$module][$key]) ? $ims->lang[$module][$key] : (isset($ims->lang['global'][$key]) ? $ims->lang['global'][$key] : $key));
        if (count($arr_replace)) {
            $arr_key = array_keys($arr_replace);
            $arr_value = array_values($arr_replace);
            $output = str_replace($arr_key, $arr_value, $output);
        }

        return $output;
    }

    // select location custom
    function selectLocation($select_name="area_code", $parent_code='', $cur = "", $ext = "", $arr_more = array(), $type="") {
        global $ims;

        $where = '';
        if ($type=="area") {
        } elseif ($type=="country") {
//            $where = " AND area_code='".$parent_code."' ";

        } elseif ($type=="province") {
            $where = " AND country_code='".$parent_code."' ";

        } elseif ($type=="district") {
            $where = " AND province_code='".$parent_code."' ";

        } elseif ($type=="ward") {
            $where = " AND district_code='".$parent_code."' ";
        }

        $data = $ims->load_data->data_table(
            "location_".$type,
            "code", 
            "code, title", 
            "is_show=1 AND lang='".$ims->conf['lang_cur']."' $where ORDER BY show_order DESC, title ASC"
        );

        return $ims->html->select($select_name, $data, $cur, $ext, $arr_more);
    }

    function loadSidebar($sidebar_id) {
        global $ims;

        $data = $ims->load_data->data_table("sidebar", "sidebar_id",  "*", "is_show=1" );
        if (isset($data[$sidebar_id]['list_widget'])) {
            return $ims->func->load_widget_list($data[$sidebar_id]['list_widget']);
        } else {
            return '';
        }
    }

    // Load setting by module
    function setting($module, $arr_more = array()) {
        global $ims;

        $ims->setting = (isset($ims->setting)) ? $ims->setting : array();
        if (!isset($ims->setting[$module])) {
            $ims->setting[$module] = array();
            if ($module == "product"
            || $module == "contact"
            || $module == "advisory"
            || $module == "about"
            || $module == "dealer"
            || $module == "download"
            || $module == "gallery"
            || $module == "news"
            || $module == "home"
            || $module == "page"
            || $module == "project"
            || $module == "promotion"
            || $module == "recruitment"
            || $module == "service"
            || $module == "user"
            || $module == "search"
            || $module == "video"
            || $module == "shared"
            || $module == "advisory"
            || $module == "kiotviet"
            || $module == "store"
            || $module == "event"
            || $module == "support"
            ) {
                $all = $ims->db->load_row_arr($module . "_setting" ,"is_show=1");
                if (!empty($all)) {
                    foreach ($all as $k => $row) {
                        if(isset($arr_more['editor'])) {
                            $arr_tmp = explode(',',$arr_more['editor']);
                            foreach($arr_tmp as $key) {
                                $row[$key] = isset($row[$key]) ? $ims->func->input_editor_decode($row[$key]) : '';
                            }
                        }
                        $ims->setting[$module . '_'. $row['lang']][$row['setting_key']] = $row['setting_value'];
                        if ($ims->conf['lang_cur'] == $row['lang']) {
                            $ims->setting[$module][$row['setting_key']] = $row['setting_value'];
                        }
                    }
                }
            }else{
                $result = $ims->db->query("SELECT * FROM " . $module . "_setting");
                while ($row = $ims->db->fetch_row($result)) {
                    if(isset($arr_more['editor'])) {
                        $arr_tmp = explode(',',$arr_more['editor']);
                        foreach($arr_tmp as $key) {
                            $row[$key] = $ims->func->input_editor_decode($row[$key]);
                        }
                    }
                    $ims->setting[$module . '_' . $row['lang']] = $row;
                    if ($ims->conf['lang_cur'] == $row['lang']) {
                        $ims->setting[$module] = $row;
                    }
                }
            }
        }
    }
    
    // Điều kiện đã load
    function whereLoaded($module='') {
        global $ims;

        $ims->data['loaded_'.$module] = isset($ims->data['loaded_'.$module]) ? $ims->data['loaded_'.$module] : array();
        $output = " ";
        // $output .= " and is_approve=1 ";
        // if (count($ims->data['loaded_'.$datatype])) {
            // $output .= " and !find_in_set(item_id, '" . implode(',', $ims->data['loaded_'.$datatype]) . "') ";
        // }
        return $output;
    }

    // Thêm mới vào danh sách đã load
    function addLoaded($module, $id=0) {
        global $ims;

        $ims->data['loaded_'.$module] = isset($ims->data['loaded_'.$module]) ? $ims->data['loaded_'.$module] : array();

        if ($id && is_array($id)) {
            foreach ($id as $k => $v) {
                $ims->data['loaded_'.$module][] = $v;
            }
        } elseif ($id) {
            $ims->data['loaded_'.$module][] = $id;
        }
        $ims->data['loaded_'.$module] = array_unique($ims->data['loaded_'.$module]);
        return $ims->data['loaded_'.$module];
    }

    //up hình bằng file manager
    function get_form_pic($html_name = 'picture', $picture = '', $folder_upload = '', $dir = '') {
        global $ims;

        $output = '';

        $data = array();
        $data['picture'] = $picture;
        if (!empty($picture)) {
            if (strpos($picture, '/uploads') !== false) {
                $picture = $ims->func->get_input_pic($picture);
            }
            $data["pic"] = '<div class="item"><a href="javascript:;" class="btn-remove-pic" data-id="'.$html_name.'"></a>' . $ims->func->get_pic_mod($picture, '', '', '', 1, 0, array('fix_width' => 1)) . '
            </a></div>';
            if(strpos($picture, '/resources/images') !== false){
                $data["pic"] = '<div class="item"><a href="javascript:;" class="btn-remove-pic" data-id="'.$html_name.'"></a><img src="' . $picture . '">
                </a></div>';
            }
        }

        $data['html_name'] = $html_name;
        $data['html_id'] = str_replace(array('[', ']'), array('_', ''), $html_name);
        $data['folder_upload'] = $folder_upload;

        $data["link_up"] = $ims->conf['rooturl'] . 'ajax.php?m=library&sub=popup_library&type=1&fldr='.$data['folder_upload']. '&editor=mce_0&field_id=' . $data['html_id'];


        $output = $ims->html->temp_box('html_form_pic', $data);

        return $output;
    }
    function check_children($row = array()) {
        global $ims;
        $output = 0;
        $arr_parent = $ims->db->load_row_arr('user', 'is_show = 1 AND user_contributor = "'.$row['user_code'].'"');
        if(!empty($arr_parent)){
            $output = 1;
        }

        return $output;
    }



    public function getTokenKiotviet(){
        global $ims;

        $token_api = '';
        $this->setting('kiotviet');
        $key = $ims->db->load_Row('sysoptions', 'option_key="token_kiotviet"');        
        if (!empty($key)) {                        
            if ($key['option_value'] == '' || $key['date_create'] == 0) {
                // Chưa có token
                $arr_token = "scopes=PublicApi.Access&grant_type=client_credentials&client_id=".$ims->setting['kiotviet']['client_id_kiotviet']."&client_secret=".$ims->setting['product']['client_secret_kiotviet'];
                $token = $this->sendPostData('https://id.kiotviet.vn/connect/token', $arr_token, 'post', 1, '');
                if (!empty($token)) {
                    $token = json_decode($token);
                    if (isset($token->access_token) && $token->access_token != '') {
                        $token_api = $token->access_token;
                        // Cập nhật vào option
                        $up = array();
                        $up['option_value'] = $token_api;
                        $up['date_create'] = time();
                        $ims->db->do_update('sysoptions', $up, ' option_key="token_kiotviet" ');
                    }
                }
            }else{
                // Đã có token
                if ((time()-$key['date_create'])>86400) {
                    // Key hết hạn, lấy key mới
                    $arr_token = "scopes=PublicApi.Access&grant_type=client_credentials&client_id=".$ims->setting['kiotviet']['client_id_kiotviet']."&client_secret=".$ims->setting['product']['client_secret_kiotviet'];
                    $token = $this->sendPostData('https://id.kiotviet.vn/connect/token', $arr_token, 'post', 1, '');
                    if (!empty($token)) {
                        $token = json_decode($token);
                        if (isset($token->access_token) && $token->access_token != '') {
                            $token_api = $token->access_token;
                            // Cập nhật vào option
                            $up = array();
                            $up['option_value'] = $token_api;
                            $up['date_create'] = time();
                            $ims->db->do_update('sysoptions', $up, ' option_key="token_kiotviet" ');
                        }
                    }
                }else{
                    $token_api = $key['option_value'];
                }
            }
        }
        return $token_api;
    }

    function do_switch_toapp($return_api=1, $item_id_api=0, $option_id=0){
        global $ims;
        
        $output = array(
            'ok' => 0,
            'mess' => '',
        );
        $app_id = 0;

        $item_id = isset($ims->post['item_id']) ? $ims->post['item_id'] : '';
        if ($return_api==1) {
            $item_id = $item_id_api;
        }
        $col = $ims->db->load_row('product', ' lang="'.$ims->conf['lang_cur'].'" and item_id="'.$item_id.'" ');
        $infoProduct = $col;
        if (!empty($col)) {
            $this->setting('product');
            $token_api = $this->getTokenKiotviet();
            // API Thêm sản phẩm
            $api_categoryId = $ims->db->load_item('product_group',"  group_id='" . $col['group_id'] . "' and lang='" . $ims->conf["lang_cur"] . "' ",'api_categoryId');
            $array_pic = array();
            $array_pic[] = $ims->conf['rooturl_web'].'uploads/'.$col['picture'];
            $detail = $ims->db->load_row("product_detail","product_id='".$item_id."'");
            $arr_picture = $ims->func->unserialize($detail['arr_picture']);
            if (!empty($arr_picture)) {
                foreach ($arr_picture as $k => $v) {
                    if ($v!="") {
                       $array_pic[] = $ims->conf['rooturl_web'].'uploads/'.$v;
                    }
                }
            }
            $TitleOption = array(
                'Title'    => 'Tiêu đề',
                'Size'     => 'Kích thước',
                'Color'    => 'Màu sắc',
                'Material' => 'Chất liệu',
                'Style'    => 'Hình dạng',
                'Custom'   => 'Tạo tùy chọn mới',
            );

            $picture_default = $ims->func->get_src_mod($col['picture']);
            // Lấy tất cả phiên bản chưa có Id kiotviet
            $orderBy = 'ORDER BY date_create DESC';
            if($col['field_option'] != ''){
                $orderBy = 'ORDER BY '.$col['field_option'].', date_create DESC';
            }
            $arr_version = $ims->db->load_row_arr('product_option', 'ProductId="'.$item_id.'" AND api_id="" '.$orderBy);
            $masterProductId = ''; // Id sản phẩm cùng loại
            $api_retailerId  = ''; 

            if (!empty($arr_version)) {
                $arr_properties = $ims->func->unserialize($infoProduct['arr_option']);
                $op1 = '';
                $op2 = '';
                $op3 = '';
                $count_op = 1;
                foreach($arr_properties as $k => $v) {
                    if ($count_op == 1) {
                        $op1 = $TitleOption[$v['SelectName']];
                        if ($v['SelectName'] == 'Custom') {
                            $op1 = $v['CustomName'];
                        }
                    }elseif ($count_op == 2) {
                        $op2 = $TitleOption[$v['SelectName']];
                        if ($v['SelectName'] == 'Custom') {
                            $op2 = $v['CustomName'];
                        }
                    }elseif ($count_op == 3) {
                        $op3 = $TitleOption[$v['SelectName']];
                        if ($v['SelectName'] == 'Custom') {
                            $op3 = $v['CustomName'];
                        }
                    }
                    $count_op++;
                }
                $i = 0;


                foreach ($arr_version as $key => $option) {
                    
                    $attributes = array();
                    if ($option['Option1']!="" && $op1!="") {
                        if ($op1=='Tiêu đề' && $option['Option1']=='Default Title') {

                        }else{
                            $attributes[] = array(
                                "attributeName" => mb_strtoupper($op1, "UTF-8"),
                                "attributeValue" => $option['Option1'],
                            );
                        }
                    }
                    if ($option['Option2']!="" && $op2!="") {
                        $attributes[] = array(
                            "attributeName" => mb_strtoupper($op2, "UTF-8"),
                            "attributeValue" => $option['Option2'],
                        );
                    }
                    if ($option['Option3']!="" && $op3!="") {
                        $attributes[] = array(
                            "attributeName" => mb_strtoupper($op3, "UTF-8"),
                            "attributeValue" => $option['Option3'],
                        );
                    }
                    $data_api = array(
                        "name" => $col['title'],
                        "code" => $option['SKU'],
                        "fullName" => $col['title'],
                        "categoryId" => $api_categoryId,
                        "description" => '',
                        "images" => ($option['Picture']!="") ? $ims->func->get_src_mod($option['Picture']) : $picture_default,
                        "unit" => '',
                        "basePrice" => $option['PriceBuy'],
                        "allowsSale" => true,
                        "hasVariants" => true,
                        "attributes" => $attributes
                    );
                    if ($i>0) {
                        $data_api["masterProductId"] = $masterProductId;
                    }
                    if (!empty($infoProduct['api_id'])) {
                        $data_api["masterProductId"] = $infoProduct['api_id'];
                    }
                    // Gửi xuống kiotviet
                    $url_send = "https://public.kiotapi.com/products";
                    $data_api = json_encode($data_api);
                    $header = array(
                        "Retailer: " .$ims->setting['kiotviet']['retailer_kiotviet'],
                        "Authorization: Bearer ".$token_api,
                        "Content-Type: application/json",
                    );
                    $Response = $this->sendPostData($url_send, $data_api, 'post', 0, 10, $header);                    
                    if (!empty($Response)) {
                        $Response = json_decode($Response);
                        if (isset($Response->id) && $Response->id > 0) {
                            if ($i==0) {
                                $masterProductId = $Response->id;
                                $api_retailerId = $Response->retailerId;
                            }
                            $col_up = array();
                            $col_up['api_id'] = $Response->id;
                            $col_up['api_retailerId'] = $Response->retailerId;
                            $col_up['date_update'] = time();
                            $ok = $ims->db->do_update('product_option', $col_up, " id='".$option['id']."' AND lang='".$ims->conf["lang_cur"]."' ");
                            if ($return_api==1 && $option_id==$option['id']) {
                                $app_id = $col_up['api_id'];
                            }
                            if ($ok) {
                                $output['ok'] = 1;
                            }
                        }
                        $i++;
                    }
                }
            }
            if ($masterProductId>0) {
                // Cập nhật lại giá sản phẩm
                $arr_up_product = array();
                $arr_up_product['api_id'] = $masterProductId;
                $arr_up_product['api_retailerId'] = $api_retailerId;
                $ims->db->do_update('product', $arr_up_product, ' item_id="'.$infoProduct['item_id'].'" AND lang="'.$ims->conf['lang_cur'].'" AND api_id="" ');
            }
        }
        if ($return_api==1) {
            return $app_id;
        }
        return json_encode($output);
    }

    function face_detect($picture = ''){
        global $ims;
        if(!empty($picture)){
            $curl = curl_init();

            $request_headers = [
                "X-RapidAPI-Host: face-detection6.p.rapidapi.com",
                "X-RapidAPI-Key: b9ec6d9e69msh2983269ee4fb068p18170fjsn08b4e5523391",
                "content-type: application/json",
            ];

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://face-detection6.p.rapidapi.com/img/face",
                CURLOPT_RETURNTRANSFER => true,                
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "{\r
                    \"url\": \"".$picture."\",\r
                    \"accuracy_boost\": 2\r
                }",
                CURLOPT_HTTPHEADER => $request_headers,
            ]);
            $response = curl_exec($curl);
            $err = curl_error($curl);

            return $response;
        }
    }
    // End classs
}
?>