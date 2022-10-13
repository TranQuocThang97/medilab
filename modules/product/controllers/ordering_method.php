<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "product";
	var $action  = "ordering_method";
	var $show_method_online = true;

	function __construct () {
		global $ims;

		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => "ordering_method",
			'js'  	 		 => "ordering",
			'css'  	 		 => "ordering",
			'use_navigation' => 0, // Sử dụng navigation
			'required_login' => $ims->func->if_isset($ims->setting['product']["required_login_order"], 0), // Bắt buộc đăng nhập
		);
        $ims->func->loadTemplate($arrLoad);
		include ($this->modules."_func.php");

		require_once ("ordering_func.php");
        $this->orderiFunc = new OrderingFunc($this);

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
	
	function do_main () {
		global $ims;		

		$a = 'a:2:{i:0;a:2:{s:2:"id";s:2:"99";s:7:"item_id";s:2:"90";}i:1;a:2:{s:2:"id";s:3:"100";s:7:"item_id";s:2:"91";}}';
//		print_arr($ims->func->unserialize($a));
        $ims->func->load_language("user");
		$data['content'] = '';

		// địa chỉ giao hàng
        if($ims->site_func->checkUserLogin() == 1) {
            $data['box_address'] = $this->do_address ();
        }else{
            $data['box_address_form'] = $this->do_address ();
        }
		// phương thức giao hàng
		$data['content'] .= $this->do_shipping ();

		// phương thức thanh toán
		$data['content'] .= $this->do_payment ();

		// yêu cầu thêm + xuất hóa đơn
		$data['content'] .= $this->do_request_more ();

		// giỏ hàng
		$data['box_column'] = $this->do_cart ();
		if(isset($ims->data['user_cur']['arr_address_book']) && !empty($ims->data['user_cur']['arr_address_book'])){
			$arr_address = $ims->func->unserialize($ims->data['user_cur']['arr_address_book']);			
			foreach ($arr_address as $row) {			
				if($row['is_default'] == 1){
					$data['default'] = $row['id'];
				}
			}
		}

		$data['link_action']   = $ims->data['link_lang'][$ims->conf['lang_cur']];
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("ordering_method");
		$ims->output .=  $ims->temp_act->text("ordering_method");
	}

	function do_address () {
		global $ims;

		$ims->func->load_language('user');

        if($ims->site_func->checkUserLogin() == 1) {
            $data['default'] = 0;
            // sổ địa chỉ
            if(isset($ims->data['user_cur']['arr_address_book']) && !empty($ims->data['user_cur']['arr_address_book'])){
                $arr_address = $ims->func->unserialize($ims->data['user_cur']['arr_address_book']);
                $i=0;
                usort($arr_address, function ($a, $b) {return $a['is_default'] < $b['is_default'];});
                // array_multisort(array_column($arr_address,'is_default'), SORT_ASC, $arr_address);
//                 print_arr($arr_address);
                foreach ($arr_address as $key => $row) {
                    $row['id_value'] = $row['id'];
                	$row['id'] = $key;
                    $row['full_addess'] = $ims->func->full_address($row);
                    if($row['is_default'] == 1){
                        $data['default'] = $row['id'];
                        $row['class'] = 'check';
                        $ims->temp_act->assign('item', $row);
                        $ims->temp_act->parse("box_address.item.default");
                    }
                    if ($i<=2) {
                        $ims->temp_act->assign('LANG', $ims->lang);
                        $ims->temp_act->assign('item', $row);
                        $ims->temp_act->parse("box_address.item");
                    }else{
                        $ims->temp_act->assign('LANG', $ims->lang);
                        $ims->temp_act->assign('item', $row);
                        $ims->temp_act->parse("box_address.more.item_more");
                    }
                    $i++;
                }
                if (!empty($arr_address) && count($arr_address)>3) {
                    $ims->temp_act->parse("box_address.more");
                }
            }
        }
		
		$data["list_location_province"] = $ims->site_func->selectLocation (
        	"province", "vi", '',
        	" class='form-control select_location_province' data-district='district' data-ward='ward' id='province' ", 
        	array('title' => $ims->lang["user"]["select_title"], 'required' => 'required'),
        	"province"
        );
		$data["list_location_district"] = $ims->site_func->selectLocation (
			"district", '', '',
			" class='form-control select_location_district' data-ward='ward' id='district' ", 
        	array('title' => $ims->lang["user"]["select_title"], 'required' => 'required'),
			""
		);
		$data["list_location_ward"] = $ims->site_func->selectLocation (
			"ward", '', '',
			" class='form-control' id='ward' ", 
        	array('title' => $ims->lang["user"]["select_title"], 'required' => 'required'),
			""
		);

        if($ims->site_func->checkUserLogin() == 1) {
            $ims->temp_act->assign('LANG', $ims->lang);
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("box_address");
            return $ims->temp_act->text("box_address");
        }else{
            $ims->temp_act->assign('LANG', $ims->lang);
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("box_address_form");
            return $ims->temp_act->text("box_address_form");
        }
	}
	
	function do_shipping ($cur='') {
		global $ims;

		$output = '';
		$arr_shipping = $ims->db->load_row_arr('order_shipping', '1 '.$ims->conf['where_lang'].' ORDER BY show_order DESC, date_update DESC');
		if (!empty($arr_shipping)) {
			$i = 0;
			foreach ($arr_shipping as $key => $row) {
				$i++;
				if($cur > 0) {				
					$row['shipping_checked'] = ($row['shipping_id'] == $cur) ? ' checked="checked"' : '';
				} else {
					$row['shipping_checked'] = ($i == 1) ? ' checked="checked"' : '';
				}				
				$row['price'] = $ims->func->get_price_format($row['price'], 0);
				$row['content'] = $ims->func->input_editor_decode($row['content']);
				if ($row["picture"]!="") {
					$row["picture"] = $ims->func->get_pic_mod($row["picture"], 40, 40, 1, 1);
				}
				if ($row['shipping_type'] != "") {
					if ($row['is_connect'] == 1) {
						$ims->temp_act->assign('row', $row);
						$ims->temp_act->parse("box_shipping.row");
					}
				}else{
					$ims->temp_act->assign('row', $row);
					$ims->temp_act->parse("box_shipping.row");
				}
			}
			$ims->temp_act->parse("box_shipping");
			$output = $ims->temp_act->text("box_shipping");
		}
		return $output;
	}
	
	function do_payment ($cur = '') {
		global $ims;
		
		$output = '';

		$arr_payment = $ims->db->load_row_arr('order_method', '1 '.$ims->conf['where_lang'].' ORDER BY show_order DESC, date_update DESC');
		if (!empty($arr_payment)) {
			$i = 0;
			foreach ($arr_payment as $key => $row) {
				$i++;
				if($this->show_method_online == false && $row['name_action']) { continue; }				
				if($cur > 0) {				
					$row['method_checked'] = ($row['method_id'] == $cur) ? ' checked="checked"' : '';
				} else {
					$row['method_checked'] = ($i == 1) ? ' checked="checked"' : '';
				}				
				$row['content'] = $ims->func->input_editor_decode($row['content']);	
				if ($row["picture"]!="") {
					$row["picture"] = $ims->func->get_pic_mod($row["picture"], 40, 40, 1, 1);
				}
				if ($row['name_action'] != "") {
					$ims->temp_act->assign('row', $row);
					$ims->temp_act->parse("box_payment.row.".$row['name_action']);
					if ($row['is_connect'] == 1) {
						$ims->temp_act->assign('row', $row);
						$ims->temp_act->parse("box_payment.row");
					}
				}else{
					$ims->temp_act->assign('row', $row);
					$ims->temp_act->parse("box_payment.row");
				}

			}			
			$ims->temp_act->parse("box_payment");
			$output = $ims->temp_act->text("box_payment");
		}		
		return $output;
	}

	function do_request_more() {
		global $ims;
		
		$output = '';
		$data = array();
		$data['display'] = "display: none";
		$data['link_buy_more'] = $ims->site_func->get_link ('product');
		if(!empty($ims->data['user_cur'])){
			$data['invoice_company'] = $ims->data['user_cur']['invoice_company'];
			$data['invoice_tax_code'] = $ims->data['user_cur']['invoice_tax_code'];
			$data['invoice_address'] = $ims->data['user_cur']['invoice_address'];
			$data['invoice_email'] = $ims->data['user_cur']['invoice_email'];
		}
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("box_requestmore.request_more");
		$ims->temp_act->parse("box_requestmore");
		$output = $ims->temp_act->text("box_requestmore");

		return $output;
	}

	function do_cart () {
		global $ims;	
		
		$setting = $ims->setting["product"];
		$ims->load_data->data_color();
		$arr_cart 			= Session::Get('cart_pro', array());
		$arr_cart_list_pro 	= Session::Get('cart_list_pro');
		$out_stock			= Session::Get('out_stock',array()); // check stock

		if($ims->site_func->checkUserLogin() == 1) {
	        $arr_cart = $ims->db->load_row_arr('product_order_temp','user_id="'.$ims->data['user_cur']['user_id'].'"');
	        $arr_cart_list_pro = array();
	        foreach ($arr_cart as $v) {
	        	$arr_cart_list_pro[$v['item_id']] = $v['item_id'];
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
			' FIND_IN_SET(ProductId, "'.@implode(',', $arr_cart_list_pro).'")>0 '.$ims->conf['where_lang']
		);

	    // giỏ hàng rỗng => quay về giỏ hàng
	    if (empty($cartProduct) || !is_array($cartProduct) || !count($cartProduct) > 0 || !empty($out_stock)) {
	    	$ims->html->redirect_rel($ims->site_func->get_link_default ('', $ims->setting['product']['ordering_cart_link']));
	    }

		$data = array();
		$out_stock = array();
		$data['mess'] = '';
		$data['cart_total'] = 0;
        $gift_include_payment = 0; // Giá trả thêm khi mua sp kèm combo

		// giỏ hàng
		if(is_array($arr_cart) && !empty($arr_cart)){
			$pic_w = 80;
			$pic_h = 80;
			foreach($arr_cart as $cart_id => $row) {
				if($ims->site_func->checkUserLogin() == 1) {
					$cart_id = $row['id'];
				}		
				$product = $ims->func->if_isset($cartProduct[$row['item_id']], array()); 
				$option  = $ims->func->if_isset($cartOption[$row['option_id']], array());
				if (empty($product) || empty($option)) {
					// Sản phẩm đã bị xóa
					if($ims->site_func->checkUserLogin() == 1) {
						$ims->db->query('DELETE FROM `product_order_temp` WHERE id="'.$cart_id.'" ');
					}else{
						unset($arr_cart[$cart_id]);
						unset($arr_cart_list_pro[$row['item_id']]);
						Session::Set ('cart_pro', $arr_cart);
						Session::Set ('cart_list_pro', $arr_cart_list_pro);
					}
					continue;
				}

				$row['cart_id']   = $cart_id;	
                $row['item_code'] = $option['SKU'];
				$row['picture']   = $option['Picture'] != '' ? $option['Picture'] : $product['picture'];
				$row['title']     = $product['title'];				
				$row['quantity']  = $ims->func->if_isset($row['quantity'], 0);

				$row['picture_thumb'] = $ims->func->get_src_mod($row["picture"], $pic_w, $pic_h, 1, 1);
                $row['price'] 	  = $option['Price'];
//        		$row['price_buy'] = $option['PriceBuy'];
                $row['price_buy'] = ($option['PricePromotion'] > 0) ? $option['PricePromotion'] : $option['PriceBuy'];
            	$row['percent_discount'] = ($row['price_buy'] < $row['price'])?100-(round($row['price_buy']/$row['price'],4)*100):0;
				$row['price_text']     = $ims->func->get_price_format($row['price']);
				$row['price_buy_text'] = $ims->func->get_price_format($row['price_buy']);
                if($row['percent_discount'] > 0) {
                	$ims->temp_act->assign('row', $row);
					$ims->temp_act->parse("box_cart.row_item.discount");
                }

				$row['link'] = $ims->site_func->get_link ($this->modules, '', $product['friendly_link']);

		        $row['title'] = (isset($product['title'])) ? $product['title']: '';
//		        $arr_item  = $ims->func->unserialize($product['arr_item']);
//                foreach ($arr_item as $key => $value) {
//                	$value['value'] = $ims->func->if_isset($option['Option'.($key + 1)]);
//	                if(mb_strtolower($value['SelectName']) == "color"){
//	                	$value['value'] = $ims->data['color'][$value['value']]['title'];
//	                }
//	                $row['title'] .= !empty($value['value'])?$value['value']:'';
//                }
                $arr_item  = $ims->func->unserialize($product['arr_item']);
                foreach ($arr_item as $key => $value) {
                    if($value['SelectName'] == 'Custom'){
                        $value['name'] = $value['CustomName'];
                    }else{
                        $value['name'] = $ims->func->if_isset($ims->lang['product']['option_'.strtolower($value['SelectName'])]);
                    }
                    $value['value'] = $ims->func->if_isset($option['Option'.($key + 1)]);
                    if(mb_strtolower($value['SelectName']) == "color"){
                        $value['value'] = $ims->data['color'][$value['value']]['title'];
                    }
                    if($value['value'] != '' && $value['value'] != 'Default Title'){
                        $ims->temp_act->assign('row', $value);
                        $ims->temp_act->parse("box_cart.row_item.option");
                    }
                }
				// Sử dụng kho hàng + Không cho phép đặt khi hết hàng
				if($option['useWarehouse'] == 1 && $option['is_OrderOutStock'] == 0) {
					// Số lượng còn lại
					$row['max_quantity'] = $option['Quantity']; 
					// Đã hết hàng
					if($option['Quantity'] == 0) {	
						$row["class"] = "out_stock";
						$out_stock[$row['option_id']] = $row['option_id'];			
						$data['attr_btn'] = 'disabled';
						$data['mess'] .= $row["title"] = $product["title"].' '.$ims->lang["product"]["out_of_stock"].'<br/>';
					}
				}

                // ------- Load danh sách quà tặng, sp mua kèm combo
                $gift_include = $this->combo_gift_include($row);
                $row['combo_info'] = $gift_include['content'];
                $gift_include_payment += $gift_include['add_payment'];

				$data['cart_total'] += $row['quantity'] * $row['price_buy'];
				$ims->temp_act->assign('row', $row);
				$ims->temp_act->parse("box_cart.row_item");
			}
		} else {
			$ims->temp_act->assign('row', array('mess' => $ims->lang['product']['no_have_item']));
			$ims->temp_act->parse("box_cart.row_empty");
		}
		
		$out_stock = Session::Set ('out_stock', $out_stock);
		$data['cart_payment'] = $data['cart_total'];

		// sử dụng mã giảm giá
		if(isset($setting['use_promotion_code']) && $setting['use_promotion_code'] == 1){
			// thêm vào tính tổng tiền
	     	$promotion_code = Session::Get('promotion_code');
	     	$result = array();
	     	$result['promotion_text'] = '- ' . $ims->func->get_price_format(0, 0);
	     	if ($promotion_code != "") {
			    $result = $this->orderiFunc->promotion_info($data['cart_total'], $promotion_code);
				$data['err_promotion'] = (!empty($result['mess'])) ? $ims->html->html_alert ($result['mess'], "warning") : '';
				if($result['mess'] == '' && $result['price'] > 0) {
		         	$data['cart_payment'] -= $result['price'];
				    $result['promotion_text'] = '-'.$ims->func->get_price_format($result['price'], 0);
				}elseif ($result['mess'] == $ims->lang['product']['freeship']){
                    $result['promotion_text'] = '<span class="price_format"><span class="number" data-value="0"></span></span>';
                }else{
                    $result['promotion_text'] = '-'. $ims->func->get_price_format(0, 0);
                    $result['hide_code'] = 'style="display:none;"';
                }
                $ims->temp_act->assign('promotion', $result);
				$ims->temp_act->parse("box_cart.promotional_box_show");
	     	}
		}

		$data['shipping_price_out'] = $ims->func->get_price_format(0, 0);
	
		// sử dụng điểm tích lũy
		if(isset($setting["use_wcoin"]) && $setting["use_wcoin"] == 1) {
	        $cart_info = Session::Get ('cart_info', array());
	        if(isset($cart_info['wcoin_use']) && $cart_info['wcoin_use'] > 0){
	            $wcoin_use  = $cart_info['wcoin_use'];
	            $user_wcoin = $ims->data['user_cur']['wcoin'];
	            $max_wcoin  = $data['cart_payment'] / $ims->setting['product']['wcoin_to_money'];
	            if($user_wcoin < $wcoin_use){
	                $wcoin_use = $user_wcoin;
	                $cart_info['wcoin_use'] = $user_wcoin;
	                Session::Set ('cart_info', $cart_info);
	            }
	            if($wcoin_use > $max_wcoin){
	                $wcoin_use = $max_wcoin;
	            }
	            $money_use_wcoin = $wcoin_use * $ims->setting['product']['wcoin_to_money'];
	            $data['cart_payment'] -= $money_use_wcoin;
				$data['wcoin_price_out'] = $ims->func->get_price_format($money_use_wcoin, 0);
				
		        $ims->temp_act->assign('data', $data);
				$ims->temp_act->parse("box_cart.wcoin_box_show");
	        }
        }
        // Cộng thêm tiền mua sp kèm theo combo
        $data['cart_payment'] += $gift_include_payment;
        $data['cart_total'] += $gift_include_payment;
        $data['vat_price'] = round($data['cart_total']*10/100,0);

        // Cộng thêm tiền mua sản phẩm giá ưu đãi kèm theo đơn hàng
        if($ims->site_func->checkUserLogin() == 1) {
            $bundled_selected = ($arr_cart[0]['bundled_product'] != '') ? $ims->func->unserialize($arr_cart[0]['bundled_product']) : array();
        }else{
            $bundled_selected = Session::Get('bundled_selected', array());
        }
        if($bundled_selected){
            $data['bundled_product'] = $this->do_bundled_product($bundled_selected);
            foreach ($bundled_selected as $item){
                $data['cart_payment'] += $item['endow_price'];
                $data['cart_total'] += $item['endow_price'];
            }
        }

		$data['wcoin_expected'] = round($data['cart_payment']/100 * $ims->setting[$this->modules]['percentforwcoin'] / $ims->setting['product']['money_to_wcoin']);
		$data['cart_total'] = $ims->func->get_price_format($data['cart_total'], 0);
		$data['cart_payment'] = $ims->func->get_price_format($data['cart_payment'], 0);
		$data['vat_price'] = $ims->func->get_price_format($data['vat_price'], 0);
		$data['link_cart_edit'] = $ims->site_func->get_link ('product', $ims->setting['product']["ordering_cart_link"]);

		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("box_cart");
		return $ims->temp_act->text("box_cart");
	}
    function combo_gift_include($row){
        global $ims;
        $out = array(
            'content' => '',
            'add_payment' => 0
        );
        if($row['combo_info'] != ''){
            $combo_info =  $ims->func->unserialize($row['combo_info']);
            $k_combo = array_keys($combo_info);
            $k_combo = str_replace('_id', '', $k_combo[0]);
            $v_combo = array_values($combo_info);
            $row[$k_combo] = $v_combo[0];
        }
        $type = $ims->db->load_row('product as pd, combo as cb', 'pd.is_show = 1 and pd.lang = "'.$ims->conf['lang_cur'].'" and pd.item_id = '.$row['item_id'].' and pd.combo_id = cb.item_id', 'cb.type');
        $row['type'] = ($type['type'] == 1) ? '' : (($type['type'] == 0) ? 'gift' : 'include');
        $row['combo_id'] = $combo_id = $ims->db->load_item('product', $ims->conf['qr'].' and item_id = '.$row['item_id'], 'combo_id');
        if($combo_id > 0){
            $combo = $ims->db->load_row('combo', $ims->conf['qr'].' and item_id="'.$combo_id.'"','item_id, title, type, value, value_type');
            if($combo['type'] != 1){
                if((!isset($row['gift']) || empty($row['gift'])) && (!isset($row['include']) || empty($row['include']))){
                    $row['select'] = $ims->lang['product']['choose'].' '.mb_strtolower($ims->lang['product'][$row['type']]);
                }else{
                    $type = (isset($row['include'])) ? 'include' : 'gift';
                    $row['select'] = $ims->lang['product']['change_'.$type];
                }

                if(isset($row['gift']) && $row['gift'] != ''){
                    $arr_gift = $ims->db->load_item_arr('user_gift', $ims->conf['qr'].' and item_id IN ('.$row['gift'].') order by FIELD(item_id,"'.$row['gift'].'") desc', 'title, picture, product_id');
                    if($arr_gift){
                        foreach ($arr_gift as $gift){
                            $gift['picture'] = $ims->func->get_src_mod($gift['picture']);
                            if($gift['product_id'] > 0){
                                $link = $ims->db->load_item('product', $ims->conf['qr'].' and item_id = '.$gift['product_id'], 'friendly_link');
                                $gift['link'] = 'href="'.$ims->func->get_link($link, '').'"';
                            }else{
                                $gift['link'] = '';
                            }
                            $ims->temp_act->assign('gift', $gift);
                            $ims->temp_act->parse("combo_gift_include.ul.gift");
                        }
                        $ims->temp_act->assign('row', $row);
                        $ims->temp_act->parse("combo_gift_include.ul");
                    }
                }
                if(isset($row['include']) && $row['include'] != ''){
                    $arr_include = $ims->db->load_item_arr('product', $ims->conf['qr'].' and item_id IN('.$row['include'].') order by FIELD(item_id,"'.$row['include'].'") desc', 'title, picture, price_buy, friendly_link');
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
                            $out['add_payment'] += $include['price_buy'];
                            $include['price_buy_text'] = number_format($include['price_buy'],0,',','.').'đ';
                            $include['link'] = $ims->func->get_link($include['friendly_link'], '');
                            $ims->temp_act->assign('incl', $include);
                            $ims->temp_act->parse("combo_gift_include.ul.include");
                        }
                        $ims->temp_act->assign('row', $row);
                        $ims->temp_act->parse("combo_gift_include.ul");
                    }
                }
                $ims->temp_act->assign('data', $row);
                $ims->temp_act->reset("combo_gift_include");
                $ims->temp_act->parse("combo_gift_include");
                $out['content'] = $ims->temp_act->text("combo_gift_include");
            }
        }

        return $out;
    }
  	// End class
    function do_bundled_product($bundled_selected){
	    global $ims;
	    $bundled_product_price = 0;

        foreach ($bundled_selected as $item){
            $row = $ims->db->load_row('product', $ims->conf['qr'].' and item_id = '.$item['item_id'], 'title, friendly_link, picture, price_buy');
            if($row){
                $bundled_product_price += $item['endow_price'];
                $row['endow_price'] = $item['endow_price'];
                $row['price'] = ($row['price_buy'] > 0 && $row['price_buy'] > $row['endow_price']) ? '<div class="price">'.number_format($row['price_buy'],0,',','.').' vnđ</div>' : '';
                $row['endow_price'] = number_format($row['endow_price'],0,',','.').' vnđ';
                $row['link'] = $ims->func->get_link($row['friendly_link'], '');
                $row['picture'] = $ims->func->get_src_mod($row['picture'],  80, 80, 1, 1);
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("bundled_product.item");
            }
        }
        $ims->temp_act->assign("bundled_product_price", $bundled_product_price);
        $ims->temp_act->parse("bundled_product");
        return $ims->temp_act->text("bundled_product");
    }
}
?>