<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "product";
	var $action  = "ordering_cart";

	function __construct (){
		global $ims;
		
		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => "ordering_cart",
			'js'  	 		 => "ordering",
			'css'  	 		 => "ordering",
			'use_func'  	 => "ordering", // Sử dụng func
			'use_navigation' => 0, // Sử dụng navigation
			'required_login' => 0, // Bắt buộc đăng nhập
		);
        $ims->func->loadTemplate($arrLoad);

		require_once ($this->modules."_func.php");
        $this->modFunc = new productFunc($this);

		require_once ("ordering_func.php");
        $this->orderiFunc = new OrderingFunc($this);

		$data = array();
		$data['content'] = $this->do_cart();
     	$ims->conf['class_full'] = 'cart';		
     	$ims->conf['container_layout'] = 'm';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	
	function do_cart (){
		global $ims;	

		$setting = $ims->setting["product"];
		$ims->load_data->data_color();
		$arr_cart = Session::Get('cart_pro', array());
		$arr_cart_list_pro = Session::Get('cart_list_pro', array());
		$err = '';

		// Sử dụng giỏ hàng tạm
        if($ims->site_func->checkUserLogin() == 1) {
	        $arr_cart = $ims->db->load_row_arr('product_order_temp', 'user_id="'.$ims->data['user_cur']['user_id'].'"');
	        $arr_cart_list_pro = array();
	        foreach ($arr_cart as $v) {
	        	$arr_cart_list_pro[$v['item_id']] = $v['item_id'];
	        }
	    }

        // Kiểm tra sp đã xóa hoặc combo hết hạn, quà hoặc sp mua kèm combo đã hết
        $check = $this->check_cart($arr_cart, $arr_cart_list_pro);
        if($check == 1){
            if($ims->site_func->checkUserLogin() == 1) {
                $arr_cart = $ims->db->load_row_arr('product_order_temp', 'user_id="'.$ims->data['user_cur']['user_id'].'"');
                $arr_cart_list_pro = array();
                foreach ($arr_cart as $v) {
                    $arr_cart_list_pro[$v['item_id']] = $v['item_id'];
                }
            }else{
                $arr_cart = Session::Get('cart_pro', array());
                $arr_cart_list_pro = Session::Get('cart_list_pro', array());
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

		$data = array();
		$data['product_link'] = $ims->site_func->get_link ($this->modules);
		$data['mess'] = '';

		// Giỏ hàng rỗng
		if (empty($arr_cart)) {
			$data['dir_images'] = $ims->func->dirModules('product', 'asset');
			$data['title_page'] = $ims->conf['meta_title'];
			$data['mess'] = $ims->lang['product']['cart_empty'];
			$ims->temp_act->assign('data', $data);
			$ims->temp_act->parse("table_cart_empty");
			return $ims->temp_act->text("table_cart_empty");
		}

		$out_stock = array();
		$data['cart_total'] = 0;
        $gift_include_payment = 0; // Giá trả thêm khi mua sp kèm combo
		if(is_array($arr_cart) && count($arr_cart) > 0){
			$pic_w = 80;
			$pic_h = 80;
			foreach($arr_cart as $cart_id => $row) {
                if($ims->site_func->checkUserLogin() == 1) {
                    $cart_id = $row['id'];
                }
                $product = $ims->func->if_isset($cartProduct[$row['item_id']], array());
                $option  = $ims->func->if_isset($cartOption[$row['option_id']], array());

				$row['link'] 	  = $ims->site_func->get_link ($this->modules, '', $product['friendly_link']);
				$row['cart_id']   = $cart_id;
                $row['item_code'] = $option['SKU'];
				$row['picture']   = $option['Picture'] != '' ? $option['Picture'] : $product['picture'];
				$row['title']     = $product['title'];				
				$row['quantity']  = $ims->func->if_isset($row['quantity'], 0);

				// sử dụng kho hàng + không cho phép đặt khi hết hàng
				if($option['useWarehouse']==1 && $option['is_OrderOutStock']==0){
                    $row['max_quantity'] = $option['Quantity'];
                }else{
                	// số lượng tối đa là 1000
                    $row['max_quantity'] = 1000;
                }

				$row['picture_zoom']  = $ims->func->get_src_mod($row["picture"]);
				$row['picture_thumb'] = $ims->func->get_src_mod($row["picture"], $pic_w, $pic_h, 1, 1);
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
						$ims->temp_act->parse("table_cart.row_item.option");
					}
                }
                $row['price'] 	  = $option['Price'];
        		$row['price_buy'] = ($option['PricePromotion'] > 0) ? $option['PricePromotion'] : $option['PriceBuy'];
            	$row['percent_discount'] = ($row['price_buy'] < $row['price'])?100-(round($row['price_buy']/$row['price'],4)*100):0;
				$row['price_text']     = $ims->func->get_price_format($row['price']);
				$row['price_buy_text'] = $ims->func->get_price_format($row['price_buy']);
                if($row['percent_discount'] > 0) {
                	$ims->temp_act->assign('row', $row);
					$ims->temp_act->parse("table_cart.row_item.discount");
                }

				// Sử dụng kho hàng + Không cho phép đặt khi hết hàng
				if($option['useWarehouse'] == 1 && $option['is_OrderOutStock'] == 0) {
					// Số lượng còn lại
					$row['max_quantity'] = $option['Quantity']; 
					// Đã hết hàng
					if($option['Quantity'] == 0) {	
						$data['attr_btn'] = 'disabled';
						$row["class"] = "out_stock";
						$data['mess'] .= $row["out_stock"] = $product["title"].' '.$ims->lang["product"]["out_of_stock"].'<br/>';	
						$out_stock[$row['option_id']] = $row['option_id'];			
					}
				}

				$row['total'] = $row['quantity'] * $row['price_buy'];
				$row['total_text'] = $ims->func->get_price_format($row['total']);
				// ------- Load danh sách quà tặng, sp mua kèm combo
				$gift_include = $this->combo_gift_include($row);
				$row['gift_include'] = $gift_include['content'];
                $gift_include_payment += $gift_include['add_payment'];

				$data['cart_total'] += $row['total'];

				$ims->temp_act->assign('row', $row);
				$ims->temp_act->parse("table_cart.row_item");
			}
		}
		$out_stock = Session::Set ('out_stock', $out_stock);
		$data['cart_payment'] = $data['cart_total'];

		// sử dụng mã giảm giá
		if(isset($setting['use_promotion_code']) && $setting['use_promotion_code'] == 1){
			// thêm vào tính tổng tiền
	     	$promotion_code = Session::Get('promotion_code');
	     	$result = array();
	     	$result['promotion_text'] = '<span class="minus">-</span>'.$ims->func->get_price_format(0, 0);
            $result['hide_code'] = 'style="display:none;"';
	     	if ($promotion_code != "") {
			    $result = $this->orderiFunc->promotion_info($data['cart_total'], $promotion_code);
				$data['err_promotion'] = ($result['type'] == 1 && !empty($result['mess']) && $result['mess'] != $ims->lang['product']['freeship']) ? $ims->html->html_alert ($result['mess'], "warning") : '';
				if($result['mess'] == '' && $result['price'] > 0) {
                    $result['promotion_text'] = '<span class="minus">-</span>'.$ims->func->get_price_format($result['price'], 0);
                    $result['hide_code'] = '';
                    $data['cart_payment'] -= $result['price'];
                }elseif ($result['mess'] == $ims->lang['product']['freeship']){
                    $result['promotion_text'] = '<span class="minus" style="display: none">-</span><span class="price_format"><span class="number" data-value="0"></span></span>';
                }else{
                    $result['promotion_text'] = '<span class="minus">-</span>'. $ims->func->get_price_format(0, 0);
                    $result['hide_code'] = 'style="display:none;"';
                }
	     	}
			$ims->temp_act->assign('promotion', $result);
			$ims->temp_act->parse("table_cart.promotional_box_show");

			// form mã khuyến mãi
			$where_promo = '';
			if(!empty($ims->data['user_cur'])){
				$where_promo .= "AND (type_promotion = 'apply_all'
							OR (type_promotion = 'apply_user' AND FIND_IN_SET('".$ims->data['user_cur']['user_id']."', list_user))
							OR (type_promotion = 'apply_email' AND FIND_IN_SET('".$ims->data['user_cur']['email']."', list_email))
							OR type_promotion = 'apply_product'
							OR type_promotion = 'apply_freeship')";
			}else{
				$where_promo .= "AND (type_promotion = 'apply_all'
							OR type_promotion = 'apply_product'
							OR type_promotion = 'apply_freeship')";
			}
			$arr_promotion_code = $ims->db->load_item_arr('promotion','is_show=1 '.$where_promo.' AND num_use < max_use_total AND date_end > "'.time().'" ORDER BY order_index asc, date_end asc','promotion_id,type_promotion,max_use,date_start,date_end,value_type,value,short,picture,num_use,total_min,value_max,date_create, 
				CASE WHEN date_end > "'.time().'" AND num_use < max_use THEN 1 ELSE 2 END as order_index');
			if (!empty($arr_promotion_code)) {
				$ims->func->load_language('user');
	            foreach ($arr_promotion_code as $row) {
	            	$row['promotion_code'] = $ims->func->get_friendly_link($row['promotion_id']);
	            	if($row['type_promotion'] == 'apply_freeship'){
	                    $row['title'] = $ims->lang['product']['free_ship'];
	                }else{
	                    $row['title'] = $ims->lang['product']['decrease'].' '.(($row['value_type'] == 1) ? $row['value'].'%' : number_format($row['value'],0,',','.').'đ');
	                }
	                $row['pic'] = $ims->conf['rooturl'].'resources/images/promotion.jpg';
	                if(!empty($row['picture'])){
	                    $row['pic'] = $ims->func->get_src_mod($row['picture'],60,60);
	                }
	            	$row['short'] = $ims->func->input_editor_decode($row['short']);
	            	$row['type'] = $ims->lang['user']['promo_'.$row['type_promotion']];
	            	$row['date_end'] = date('d/m/Y  H:i',$row['date_end']);
	            	$ims->temp_act->assign('row', $row);
					$ims->temp_act->parse("promotional_box.row_item");
	            }
	        }
	     	$ims->temp_act->assign('data', $data);
			$ims->temp_act->parse("promotional_box");
			$data['promotional_box'] = $ims->temp_act->text("promotional_box");
		}
		
		// sử dụng điểm tích lũy
		if(isset($setting["use_wcoin"]) && $setting["use_wcoin"] == 1) {
			$data['payment_wcoin2money_text'] = $ims->func->get_price_format(0, 0);
			if($ims->site_func->checkUserLogin() != 1){
			    $ims->site_func->setting('user');
				$data['link_login'] = $ims->site_func->get_link ('user', $ims->setting['user']['signin_link']);
				$ims->temp_act->assign('data', $data);
				$ims->temp_act->parse("wcoin_box.link_login");
			}else {
				$data['wcoin'] = $ims->data['user_cur']['wcoin'];
				$data['wcoin_to_money'] = $setting['wcoin_to_money'];

				$cart_info = Session::Get ('cart_info', array());
				if(isset($cart_info['wcoin_use']) && $cart_info['wcoin_use'] > 0) {
					$wcoin_use  = $cart_info['wcoin_use'];
					$user_wcoin = $ims->data['user_cur']['wcoin'];
					$max_wcoin  = $data['cart_payment'] / $setting["wcoin_to_money"];
					if($user_wcoin < $wcoin_use) {
						$wcoin_use = $user_wcoin;
						$cart_info['wcoin_use'] = $user_wcoin;
						Session::Set ('cart_info', $cart_info);
						$ims->html->redirect_rel($ims->site_func->get_link ($this->modules, '', $ims->setting[$this->modules]['ordering_cart_link']));
					}
					if($wcoin_use > $max_wcoin){
						$wcoin_use = $max_wcoin;
					}
					$money_use_wcoin = $wcoin_use * $setting["wcoin_to_money"];
					$data['wcoin_use'] = $wcoin_use;
					$data['payment_wcoin2money_text'] = $ims->func->get_price_format($money_use_wcoin, 0);
					$data['cart_payment'] -= $money_use_wcoin;
				}
				$ims->temp_act->assign('data', $data);
				$ims->temp_act->parse("wcoin_box.form_wcoin");
			}
			$ims->temp_act->assign('data', $data);
			$ims->temp_act->parse("wcoin_box");
			$data['wcoin_box'] = $ims->temp_act->text("wcoin_box");

			$ims->temp_act->assign('data', $data);
			$ims->temp_act->parse("table_cart.wcoin_box_show");
		}

		// Cộng thêm tiền mua sp kèm theo combo
        $data['cart_payment'] += $gift_include_payment;
        $data['cart_total'] += $gift_include_payment;

        // Cộng thêm tiền mua sản phẩm giá ưu đãi kèm theo đơn hàng
        if($ims->site_func->checkUserLogin() == 1) {
            $bundled_selected = ($arr_cart[0]['bundled_product'] != '') ? $ims->func->unserialize($arr_cart[0]['bundled_product']) : array();
        }else{
            $bundled_selected = Session::Get('bundled_selected', array());
        }
        if($bundled_selected){
            foreach ($bundled_selected as $item){
                $data['cart_payment'] += $item['endow_price'];
                $data['cart_total'] += $item['endow_price'];
            }
        }

		$data['wcoin_expected']  = round(($data['cart_payment']/100 * $setting["percentforwcoin"]) / $setting["money_to_wcoin"]);
		$data['percentforwcoin'] = $setting["percentforwcoin"];
		$data['money_to_wcoin']  = $setting["money_to_wcoin"];
		$data['cart_payment']    = $ims->func->get_price_format($data['cart_payment'], 0);
		$data['cart_total']      = $ims->func->get_price_format($data['cart_total'], 0);
		$data['title_page'] 	 = $ims->conf['meta_title'].' <span class="num">('.count($arr_cart).' '.$ims->lang['product']['products'].')</span>';
		$data['order_discount'] = $this->do_order_discount();
		$data['bundled_product'] = $this->do_bundled_product();

		$data['err'] = $err;

		if(!isset($ims->data['user_cur'])) {
			$data['link_continue'] = $ims->site_func->get_link ('user', '', $ims->setting['user']['signin_link']).'?url='.$ims->func->base64_encode($data['link_continue']);
		} else {
			$data['link_continue'] = $ims->site_func->get_link ($this->modules, $ims->setting[$this->modules]['ordering_method_link']);
		}

		if(!empty($ims->setting['product']['show_vat'])){			
			$data['text_vat'] = $ims->func->input_editor_decode($ims->setting['product']['text_vat']);
			$ims->temp_act->assign('data', $data);
			$ims->temp_act->parse("table_cart.vat");
		}
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("table_cart");
		return $ims->temp_act->text("table_cart");
	}
	function check_cart($arr_cart, $arr_cart_list_pro){
	    global $ims;
	    $out = 0;
        $cartProduct = $ims->load_data->data_table (
            'product',
            'item_id', 'item_id',
            ' FIND_IN_SET(item_id, "'.@implode(',', $arr_cart_list_pro).'")>0 '.$ims->conf['where_lang']
        );
        $cartOption = $ims->load_data->data_table(
            'product_option',
            'id', 'id',
            ' FIND_IN_SET(ProductId, "'.@implode(',',$arr_cart_list_pro).'")>0 '.$ims->conf['where_lang']
        );

        if(is_array($arr_cart) && count($arr_cart) > 0){
            foreach($arr_cart as $cart_id => $row) {
                if($ims->site_func->checkUserLogin() == 1) {
                    $cart_id = $row['id'];
                }
                $product = $ims->func->if_isset($cartProduct[$row['item_id']], array());
                $option  = $ims->func->if_isset($cartOption[$row['option_id']], array());
                // ---------- Sản phẩm đã bị xóa
                if (empty($product) || empty($option)) {
                    if($ims->site_func->checkUserLogin() == 1) {
                        $ims->db->query('DELETE FROM `product_order_temp` WHERE id="'.$cart_id.'" ');
                    }else{
                        unset($arr_cart[$cart_id]);
                        unset($arr_cart_list_pro[$row['item_id']]);
                        Session::Set ('cart_pro', $arr_cart);
                        Session::Set ('cart_list_pro', $arr_cart_list_pro);
                    }
                    $out = 1;
                }else{
                    $combo_id = $ims->db->load_item('product', $ims->conf['qr'].' and item_id = '.$row['item_id'], 'combo_id');
                    if($combo_id > 0){
                        // Combo đã hết thời hạn
                        $check_combo = $ims->db->load_item('combo', 'item_id = '.$combo_id.' and quantity_product > 0 and date_end > '.time(), 'item_id');
                        if(!$check_combo){
                            if($ims->site_func->checkUserLogin() == 1) {
                                $ims->db->query('DELETE FROM `product_order_temp` WHERE id="'.$cart_id.'" ');
                            }else{
                                unset($arr_cart[$cart_id]);
                                unset($arr_cart_list_pro[$row['item_id']]);
                                Session::Set ('cart_pro', $arr_cart);
                                Session::Set ('cart_list_pro', $arr_cart_list_pro);
                            }
                            $out = 1;
                        }else{
                            // Quà hoặc sp mua kèm combo đã hết
                            if($row['combo_info'] != ''){
                                $cb_info = $ims->func->unserialize($row['combo_info']);
                                $type = array_keys($cb_info);
                                $type = str_replace('_id', '', $type[0]);
                                $val = array_values($cb_info);

                                $table = ($type == 'include') ? 'product' : 'user_gift';
                                $quantity = ($type == 'include') ? 'quantity_include' : 'quantity_combo';
                                $result = $ims->db->load_item_arr($table, $ims->conf['qr'].' and item_id IN('.$val[0].')', 'item_id, '.$quantity);
                                if($result){
                                    $update_tmp = array();
                                    $ok = 1;
                                    foreach ($result as $item){
                                        if($item[$quantity] > 0){
                                            $update_tmp[] = $item['item_id'];
                                        }else{
                                            $ok = 0;
                                        }
                                    }
                                    $update = array();
                                    if($update_tmp){
                                        $update[$type.'_id'] = implode(',',$update_tmp);
                                    }
                                    if($ok == 0){
                                        $update = $ims->func->serialize($update);
                                        if($ims->site_func->checkUserLogin() == 1) {
                                            $col_tmp['combo_info'] = $update;
                                            $col_tmp['date_update'] = time();
                                            $ims->db->do_update('product_order_temp', $col_tmp, ' id="'.$cart_id.'" ');
                                        }else{
                                            $arr_cart[$cart_id]['combo_info'] = $update;
                                            Session::Set ('cart_pro', $arr_cart);
                                        }
                                        $out = 1;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $out;
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

	    $combo = $ims->db->load_row('product as pd, combo as cb', 'pd.is_show = 1 and pd.lang = "'.$ims->conf['lang_cur'].'" and pd.item_id = '.$row['item_id'].' and pd.combo_id = cb.item_id', 'cb.item_id, cb.title, cb.type, cb.value, cb.value_type');
        $row['type'] = ($combo['type'] == 1) ? '' : (($combo['type'] == 0) ? 'gift' : 'include');
	    $row['combo_id'] = $combo['item_id'];
	    if($combo){
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
    function do_order_discount(){
	    global $ims;

        if($ims->setting['product']['is_order_discount'] == 1){
            $ims->temp_act->parse("order_discount");
            return $ims->temp_act->text("order_discount");
        }
    }
    function do_bundled_product(){
	    global $ims;

	    if($ims->setting['product']['is_order_bundled'] == 1){
            $ims->temp_act->parse("bundled_product");
            return $ims->temp_act->text("bundled_product");
        }
    }
}
?>