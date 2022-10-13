<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "shipping";
	var $action  = "ajax";
	
	/**
		* function __construct()
		* Khoi tao 
	**/
	function __construct()
	{
		global $ims;
		
        $ims->site_func->setting("user");
		$ims->site_func->setting("product");
		$ims->func->load_language($this->modules);
		$fun = (isset($ims->post['f'])) ? $ims->post['f'] : '';
		switch ($fun) {
			case "shippingFee":
				echo $this->do_shippingFee ();
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

    /* ================================================================================*\
        Ok = 2: miễn ship
    \*================================================================================ */
	function do_shippingFee(){
		global $ims;
		
		$output = array(
			'ok' => 0,
            'shipping_fee' => 0,
            'total_payment' => 0,
			'mess' => '',
		);

        $shipping_id      = $ims->func->if_isset($ims->post['shipping_id'], 0);
        $method_id        = $ims->func->if_isset($ims->post['method_id'], 0);
        $address          = $ims->func->if_isset($ims->post['address'], 0);
        $total_money      = $ims->func->if_isset($ims->post['total_money'], 0);
        $total_promotion  = $ims->func->if_isset($ims->post['total_promotion'], 0);
        $total_wcoin      = $ims->func->if_isset($ims->post['total_wcoin'], 0);

        if($ims->site_func->checkUserLogin() != 1) {
            $output['mess'] = $ims->lang['global']['signin_false'];
            return json_encode($output);
        }

        $arr_cart          = Session::Get('cart_pro', array());
        $arr_cart_list_pro = Session::Get('cart_list_pro');

        // Sử dụng giỏ hàng tạm
        $arr_cart = $ims->db->load_row_arr('product_order_temp','user_id="'.$ims->data['user_cur']['user_id'].'"');
        $arr_cart_list_pro = array();
        foreach ($arr_cart as $k => $v) {
            $arr_cart_list_pro[] = $v['item_id'];
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

        // Lấy địa chỉ theo sổ địa chỉ của thành viên
        $address_book = array();
        $arr_address = $ims->func->unserialize($ims->data['user_cur']['arr_address_book']);
        foreach ($arr_address as $key => $value) {
            if ($value['id'] == $address) {
                $address_book = $value;
            }
        }

        $infoShipping = $ims->db->load_row("order_shipping", " shipping_id='".$shipping_id."' ".$ims->conf['where_lang']);
		if (!empty($infoShipping)) {
			// Miễn ship nếu vượt quá tiền
            if($total_money >= $infoShipping['ototal_freeship']){
				$output['ok'] = 2;
				$output['shipping_fee'] = 0;
			} else{

    			// GHTK 
    			$totalweight = 0;
    			$length = 20;
    			$width  = 20;
    			$height = 20;
    			$multiplicationMax = 0;
    			if (!empty($arr_cart)) {
    				foreach ($arr_cart as $key => $value) {
    					$product = $cartProduct[$value['item_id']];
    					if (!empty($product)) {
    						$totalweight += $product["weight"] * $value['quantity'];
    						$multiplication = $product["length"] * $product['width'] * $product['height'];
    						if ($multiplicationMax>0) {
    							if ($multiplication > $multiplicationMax) {
    								$multiplicationMax = $multiplication;
    								$length = $product["length"];
    								$width  = $product['width'];
    								$height = $product['height'];
    							}
    						}else{
    							$multiplicationMax = $multiplication;
    							$length = $product["length"]>0? $product["length"] : $length;
    							$width  = $product["width"]>0? $product["width"] : $width;
    							$height = $product["height"]>0? $product["height"] : $height;
    						}
    					}
    				}
    			}    
                if ($totalweight == 0) {
                    $totalweight = 500;
                }
                if ($infoShipping['shipping_type']=="GHTK") {
                    $arr_connect = $ims->func->unserialize ($infoShipping['arr_connect']);
            		$arr_option  = $ims->func->unserialize ($infoShipping['arr_option']);
    	            $warehouse = $ims->db->load_row("product_order_address", "is_default=1 AND is_show=1 AND lang='".$ims->conf['lang_cur']."' ");
    	            $warehouse_id = 0;
    	            if (!empty($warehouse)) {
    	            	$warehouse_id = $arr_connect[$warehouse['item_id']];
    	            }

                	$province    = $ims->func->if_isset($address_book['province'], 0);
    		        $district    = $ims->func->if_isset($address_book['district'], 0);
    		        $ward        = $ims->func->if_isset($address_book['ward'], 0);
    		        $address     = $ims->func->if_isset($address_book['address'], "");
                    $data = array(
                        "address"       	=> $address,
                        "province"     	 	=> $ims->func->location_name('province', $province),
                        "district"     	 	=> $ims->func->location_name('district', $district),
                        "ward"          	=> $ims->func->location_name('ward', $ward),
                        "pick_address_id" 	=> $warehouse_id,
                        "weight"        	=> $totalweight,
                        "transport"     	=> "fly",
                        "value"         	=> $total_money,
                    );
                    $url = $ims->conf['URL_API_GHTK'].'services/shipment/fee?'.http_build_query($data);
                    $resp = $this->sendPostDataGHTK($url, array(), 'get', $arr_option['Token']);
                    $resp = json_decode($resp);
                    if (isset($resp->fee->fee)) {
                        $output['ok'] = 1;
                        $output['shipping_fee'] = $resp->fee->fee;
                    }else{
                        $output['mess'] = 'Vui lòng nhập đầy đủ thông tin nhận hàng ở bước trước!';
                    }
                } elseif ($infoShipping['shipping_type'] == "GHN") {
                    $arr_connect = $ims->func->unserialize ($infoShipping['arr_connect']);
                	$arr_option  = $ims->func->unserialize ($infoShipping['arr_option']);
    	            $warehouse = $ims->db->load_row("product_order_address", "is_default=1 AND is_show=1 AND lang='".$ims->conf['lang_cur']."' ");
    	            $warehouse_id = 0;
    	            if (!empty($warehouse)) {
    	            	$warehouse_id = isset($arr_connect[$warehouse['item_id']]) ? $arr_connect[$warehouse['item_id']] : 0;
    	            }
    	            $arr_input = array(
    	               "offset" => 0,
    	               "limit" => 50,
    	               "client_phone" => ""
    	            );
    	            $resp = $this->apiGHN("GetShop", $arr_input, $arr_option['Token']);
    	            $from_district_id = 0;
    	            foreach ($resp->data->shops as $k => $v) {
    	                if ($v->_id == $warehouse_id) {
    	                    $from_district_id = $v->district_id;
    	                }
    	            }
                	$arr_input = array(
    					"from_district_id" 	=> $from_district_id,
    					"service_type_id"  	=> 2,
    					"to_district_id"   	=> (int)$address_book['district'],
    					"to_ward_code" 	   	=> $address_book['ward'],
    					"weight" 			=> $totalweight,
    					"height" 			=> (int)$height,
    					"length" 			=> (int)$length,
    					"width"  			=> (int)$width,
    					"insurance_fee" 	=> 0,
    					"coupon" 			=>  null
    				);
                	$resp = $this->apiGHN("Getfee", $arr_input, $arr_option['Token']);
                	if (isset($resp->data->service_fee)) {
                        $output['ok'] = 1;
                        $output['shipping_fee'] = $resp->data->service_fee;
                    }else{
                        $output['mess'] = 'Vui lòng nhập đầy đủ thông tin nhận hàng ở bước trước!';
                    }
                } else{
                    $province    = $ims->func->if_isset($address_book['province'], 0);
                    $district    = $ims->func->if_isset($address_book['district'], 0);
    				$check = 0;
    				$output['shipping_fee'] = 0;                    
    				$arr_price = $ims->func->unserialize ($infoShipping['arr_price']);	                                        
    				if(isset($arr_price) && is_array($arr_price) && !empty($arr_price)){
    					foreach($arr_price as $value){                            
    						$get_price = 0;                            
    						if($value['province'] == $province && ($value['district'] == $district || $value['district'] == '')){
    							$check = 1;
    							$output['ok'] = 1;
    							$get_price = $value['value'] + $value['value1'];
    							$output['shipping_fee'] = $get_price;
    						}
    					}
    				}
    				if($check == 0){
    					$output['ok'] = 1;
    					$output['shipping_fee'] = $infoShipping['price'];
    				}
                }
            }
		}

        // sử dụng mã giảm giá
        $promotion_code = Session::Get('promotion_code');
        if(isset($promotion_code) && $promotion_code != ''){
            $ims->func->load_language("product");
            require_once ($ims->conf['rootpath']."modules/product/controllers/ordering_func.php");
            $this->orderiFunc = new OrderingFunc($this);
            $promotion_info = $this->orderiFunc->promotion_info(0, $promotion_code);         
            if($promotion_info['percent'] > 0 && $promotion_info['percent'] < 100) {
                $total_money -= (float)$promotion_info['price'];
            }
        }

        // sử dụng điểm tích lũy
        $cart_info = Session::Get ('cart_info', array());
        if(isset($cart_info['wcoin_use']) && $cart_info['wcoin_use'] > 0){
            $wcoin_use  = $cart_info['wcoin_use'];
            $user_wcoin = $ims->data['user_cur']['wcoin'];
            $max_wcoin  = $total_money / $ims->setting['product']['wcoin_to_money'];
            if($user_wcoin < $wcoin_use){
                $wcoin_use = $user_wcoin;
                $cart_info['wcoin_use'] = $user_wcoin;
                Session::Set ('cart_info', $cart_info);             
            }
            if($wcoin_use > $max_wcoin){
                $wcoin_use = $max_wcoin;
            }
            $money_use_wcoin = $wcoin_use * $ims->setting['product']['wcoin_to_money'];
            $total_money -= $money_use_wcoin;
        }

        $cart_info['shipping_fee'] = $output['shipping_fee'];
        Session::Set ('cart_info', $cart_info);
        $output['total_payment'] = $total_money + $output['shipping_fee'];
		return json_encode($output);
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

  	// End class
}
?>