<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "user";
	var $action = "contributor";
	var $sub = "manage";
	var $check_search = 0;
	
	/**
		* function __construct ()
		* Khoi tao 
		* Quản lý hoa hồng
	**/
	function __construct ()
	{
		global $ims;

		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->action,
			'js'  	 		 => $this->modules,
			'css'  	 		 => $this->modules,
			'use_func'  	 => $this->modules, // Sử dụng func
            'use_navigation' => 0, // Sử dụng navigation
			'required_login' => 1, // Bắt buộc đăng nhập
		);
        $ims->func->loadTemplate($arrLoad);

        $data = array();
		$data['content']  = $this->do_manage();
		$data['box_left'] = box_left($this->action);
		$ims->conf["class_full"] = 'user';		
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	
	function table_promotion ($order_create)
	{
		global $ims;
		
		$output = '';	
		
		$data = array();
		
		$sql = "select *  
						from promotion   
						where order_create='".$order_create."'   
						order by promotion_id asc";
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
	
	function table_cart ($order = array())
	{
		global $ims;
		
		$order_id = $order['order_id'];	
		
		$arr_color = $ims->load_data->data_table ('product_color', 'color_id', 'color_id,color,title', "is_show=1 and lang='".$ims->conf['lang_cur']."' order by show_order desc, date_create desc");
		$arr_size = $ims->load_data->data_table ('product_size', 'size_id', 'size_id,title', "is_show=1 and lang='".$ims->conf['lang_cur']."' order by show_order desc, date_create desc");
		
		$data = array();
		$data['cart_total'] = 0;
		
		$sql = "select *  
						from product_order_detail  
						where order_id='".$order_id."'   
						order by detail_id asc";
		//echo $sql;
		$result = $ims->db->query($sql);
		$html_row = "";
		if ($num = $ims->db->num_rows($result)) {
			while ($row = $ims->db->fetch_row($result)) { 
				
				$row['pic_w'] = 50;
				$row['pic_h'] = 50;
				$row["picture"] = $ims->func->get_src_mod($row["picture"], $row['pic_w'], $row['pic_h'], 1, 0, array('fix_max' => 1));
				$row['quantity'] = (isset($row['quantity'])) ? $row['quantity'] : 0;
				
				$row['total'] = $row['quantity'] * $row['price_buy'];
				$data['cart_total'] += $row['total'];
				
				$row['color'] = (isset($arr_color[$row['color_id']])) ? $arr_color[$row['color_id']] : array();
				$row['size'] = (isset($arr_size[$row['size_id']])) ? $arr_size[$row['size_id']] : array();
				
				$row['price_buy'] = $ims->func->get_price_format($row['price_buy']);
				$row['total'] = $ims->func->get_price_format($row['total']);
				if(!empty($row['color'])){
					$row['color_title'] = $row['color']['title'];
				}
				$ims->temp_act->assign('row', $row);
				$ims->temp_act->parse("table_cart.row_item");
			}
		} else {
			$ims->temp_act->assign('row', array('mess' => $ims->lang['user']['no_have_item']));
			$ims->temp_act->parse("table_cart.row_empty");
		}
		
		$data['cart_total'] = $ims->func->get_price_format($data['cart_total'], 0);
		$data['promotion_percent'] = $order['promotion_percent'];
		
		$data['promotion_price'] = $ims->func->get_price_format($order['promotion_price'], 0);
		$data['shipping_price'] = $ims->func->get_price_format($order['shipping_price'], 0);
		$data['voucher_amount'] = $ims->func->get_price_format($order['voucher_amount'], 0);
		$data['total_payment'] = $ims->func->get_price_format($order['total_payment'], 0);
		$data['total_contributor'] = $ims->func->get_price_format($order['amount_contributor'], 0);
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("table_cart");
		return $ims->temp_act->text("table_cart");
	}
	
	//-----------
	function do_edit($order_id)
	{
		global $ims;
		
		$err = "";
		
		$arr_order_shipping = $ims->load_data->data_table ('order_shipping', 'shipping_id', 'shipping_id,title,content', "is_show=1 and lang='".$ims->conf['lang_cur']."' order by show_order desc, date_create desc", array(), array('editor'=>'content'));
		$arr_order_method = $ims->load_data->data_table ('order_method', 'method_id', 'method_id,title,content', "is_show=1 and lang='".$ims->conf['lang_cur']."' order by show_order desc, date_create desc", array(), array('editor'=>'content'));
		
		$sql = "select * from product_order where order_id='".$order_id."'";
    $result = $ims->db->query($sql);
    if ($data = $ims->db->fetch_row($result)){
		}
		
		$data["err"] = $err;
		$data["table_cart"] = $this->table_cart ($data);
		
		$data["table_promotion"] = $this->table_promotion ($order_id);
		if(!empty($data["table_promotion"])) {
			$ims->temp_act->assign('data', $data);
			$ims->temp_act->parse("edit.list_promotion");
		}elseif($data['is_status'] >= 2) {
			$ims->temp_act->assign('data', $data);
			$ims->temp_act->parse("edit.create_promotion");
		}
		
		
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("edit");
		return $ims->temp_act->text("edit");
	}
	
	//-----------
	function manage_row($row)
	{
		global $ims;
		$output = '';
		if(!empty($row["picture"])){
			$row["picture"] = '<a class="fancybox-effects-a" title="'.$row["picture"].'" href="'.DIR_UPLOAD.$row["picture"].'">
				'.$ims->func->get_pic_mod($row["picture"], 50, 50, '', 1, 0, array('fix_width'=>1)).'
			</a>';
		}
		$row['order_id'] = $row['dbtable_id'];
		$row['wcoin_withdrawals'] = $row['wcoin_withdrawals'];
		$row['wcoin_buy'] = $row['wcoin_buy'];
		$row['plus_minus'] = $row['value_type'] == 1 ? '+' : '-';
		$row['total_order'] = $ims->func->get_price_format($row['total_amount']);
		$row['total_payment'] = $ims->func->get_price_format($row['total_amount']);
		$row['date_create'] = date('d/m/Y H:s',$row['date_create']);
		$row['total_contributor'] = $ims->func->get_price_format($row['amount_contributor'], 0);
		$row['user'] = $ims->db->load_item('user','is_show = 1 AND user_id = "'.$row['user_id'].'" ', 'nickname');
		$exchange_type_old = $row['exchange_type'];

		$row['value_save'] = '---';
		if($row['total_amount'] == 0 && $row['exchange_type'] == 'admin_minus_withdraw'){
			$row['total_payment'] = '---';
			$row['wcoin_withdrawals'] = $row['value'];
			$row['plus_minus_wcoin_withdrawals'] = substr($row['value_type'], 0, -1);
			$row['wcoin_buy'] = '---';
			// $row['plus_minus'] = '';
			$row['total_payment'] = '---';
			$row['value_save'] = $row['value'];
			$row['value'] = '---';
			$row['class_minus'] = 'none';
			$row['class_wcoin_buy'] = 'none';
		}		
		$row['value_buy'] = 0;
		if($row['exchange_type'] == 'buy'){
			$row['total_payment'] = '---';
			$row['total_payment'] = '---';
			$row['plus_minus_wcoin_buy'] = ($row['value_type'] == 1) ? '+' : '-';
			$row['plus_minus_wcoin_withdrawals'] = ($row['value_type'] == 1) ? '+' : '-';
			$row['value_buy'] = $row['value'];
			$row['plus_minus'] = $row['value_type'] == 1 ? '+' : '-';
		}
		if($row['exchange_type'] == 'up_contributor'){
			$user_id = $ims->db->load_item($row['dbtable'],'is_show = 1 AND order_code = "'.$row['dbtable_id'].'" ', 'user_id');
			$row['user'] = $ims->db->load_item('user','is_show = 1 AND user_id = "'.$user_id.'" ', 'nickname');
			$row['exchange_type'] = 'Giới thiệu';
		}elseif ($row['exchange_type'] == 'ouser_wcoin') {
			$row['exchange_type'] = 'Tích điểm';
		}elseif ($row['exchange_type'] == 'admin_minus_withdraw') {
			$row['exchange_type'] = 'Rút điểm';
		}elseif ($row['exchange_type'] == 'buy') {
			$row['exchange_type'] = 'Mua hàng';
		}elseif ($row['exchange_type'] == 'deeplink') {
			$row['exchange_type'] = 'Tiếp thị liên kết';
		}elseif ($row['exchange_type'] == 'up_contributor_root') {
			$row['exchange_type'] = 'Giới thiệu cấp con';
		}elseif ($row['exchange_type'] == 'up_contributor_department') {
			$row['exchange_type'] = 'Giới thiệu mua gian hàng đã giới thiệu';
		}elseif ($row['exchange_type'] == 'shared_comment') {
			$Comment = $ims->db->load_row('shared_comment', ' item_id="'.$row['dbtable_id'].'" and lang="'.$ims->conf['lang_cur'].'"');
			$Product = $ims->db->load_row('product', ' item_id="'.$Comment['type_id'].'" and lang="'.$ims->conf['lang_cur'].'" and is_show=1 ');
			$link_product = $ims->conf['rooturl_web'].$Product['friendly_link'].'#root_form';
			$row['exchange_type'] = '<a href="'.$link_product.'">'.'Bình luận sản phẩm'.'</a>'.'<div><a class="fancybox-effects-a" href="#show_comment'.$row['id'].'">Chi tiết bình luận</a></div><div id="show_comment'.$row['id'].'" style="display:none;padding: 20px;min-width:500px;">'.$Comment['content'].'</div>';
		}
		
		if($row['user'] == ''){
			$row['user'] = 'Chưa đăng ký';
		}

        if($row['dbtable'] == 'product_order'){
            $arr_order = $ims->db->load_row('product_order', ' is_show = 1 AND order_code = "'.$row['dbtable_id'].'" ');
            if(!empty($arr_order)){
                if($exchange_type_old == 'up_contributor' || $exchange_type_old == 'up_contributor_root'){
                    $row['exchange_type'] .=  '<b style="display:block">'.$ims->db->load_item('user', ' is_show = 1 AND user_id = "'. $arr_order['user_id'] .'" ', 'nickname').'</b>';
                }
            }
        }
		$ims->temp_act->assign('row', $row);
		$ims->temp_act->parse("manage.row_item");
		$output = $ims->temp_act->text("manage.row_item");
		$ims->temp_act->reset("manage.row_item");
		
		return $output;
	}
	
	//-----------
	function do_manage($is_show="")
	{
		global $ims;
		$data = array();
		$err = "";
		$ext = "";
		$where = "";
		$is_search = 0;
		$p 		   		   = $ims->func->if_isset($ims->input['p'], 1);
		$user_id 		   = $ims->func->if_isset($ims->input['user_id']);
		$user_code   	   = $ims->func->if_isset($ims->input['user_code']);
		$search_date_end   = $ims->func->if_isset($ims->input["search_date_end"]);		
		$search_date_begin = $ims->func->if_isset($ims->input["search_date_begin"]);
		
		$user_id_get   = $ims->data['user_cur']['user_id'];
		$user_code_get = $ims->data['user_cur']['user_code'];	

		if($user_code != '' || $user_id != ''){
			if($user_code != ''){
				$user_code_get = $user_code;
				$user_id_get = $ims->db->load_item('user', 'is_show = 1 AND user_code ="'.$user_code_get.'" ' ,'user_id');
				$where .= " where is_show = 1 AND (user_code='".$user_code."') AND ( exchange_type = 'up_contributor' OR exchange_type = 'admin_minus_withdraw' OR  exchange_type = 'buy' OR  exchange_type = 'up_contributor_department' OR exchange_type ='shared_comment' OR exchange_type = 'ouser_wcoin' OR exchange_type = 'up_contributor_root' OR exchange_type='deeplink' ) ";
			}else{
				$user_id_get = $user_id;
				$user_code_get = $ims->db->load_item('user', 'is_show = 1 AND user_id ="'.$user_id_get.'" ' ,'user_id');
				$where .= " where is_show = 1 AND (user_id='".$user_id."') AND ( exchange_type = 'up_contributor' OR  exchange_type = 'up_contributor_department' OR exchange_type ='shared_comment' OR exchange_type = 'admin_minus_withdraw' OR  exchange_type = 'buy' OR exchange_type = 'ouser_wcoin' OR exchange_type = 'up_contributor_root' OR exchange_type='deeplink' ) ";

			}
		}else{
			$where .= " where is_show = 1 AND (user_code='".$ims->data['user_cur']['user_code']."' OR user_id='".$ims->data['user_cur']['user_id']."') AND ( exchange_type = 'up_contributor' OR  exchange_type = 'up_contributor_department' OR exchange_type ='shared_comment' OR exchange_type = 'admin_minus_withdraw' OR  exchange_type = 'buy' OR exchange_type = 'ouser_wcoin' OR exchange_type = 'up_contributor_root' OR exchange_type='deeplink' ) ";
		}

		if($search_date_begin || $search_date_end ){
			$tmp1 = @explode("/", $search_date_begin);
			$time_begin = @mktime(0, 0, 0, $tmp1[1], $tmp1[0], $tmp1[2]);
			
			$tmp2 = @explode("/", $search_date_end);
			$time_end = @mktime(23, 59, 59, $tmp2[1], $tmp2[0], $tmp2[2]);
			
			$where.=" AND (date_create BETWEEN {$time_begin} AND {$time_end} ) ";
			$ext.="&search_date_begin=".$search_date_begin."&search_date_end=".$search_date_end;
			$is_search = 1;
		}
    
		$num_total = 0;
		$res_num = $ims->db->query("select id from user_exchange_log ".$where."  ");
			$num_total = $ims->db->num_rows($res_num);
		$n = 20;
		$num_products = ceil($num_total / $n);
		if ($p > $num_products)
		  $p = $num_products;
		if ($p < 1)
		  $p = 1;
		$start = ($p - 1) * $n;
		
		$link_action = $ims->site_func->get_link($this->modules,$ims->setting[$this->modules]["contributor_link"]);
		$where .= " ORDER BY date_create DESC";
        $sql = "SELECT * FROM user_exchange_log ".$where." LIMIT $start,$n";
        // echo $sql;die; 
		$nav = $ims->site->paginate ($link_action, $num_total, $n, $ext, $p);
		$result = $ims->db->query($sql);
    	$i = 0;
		$data['row_item'] = '';
		$data['total_wcoin_buy'] = 0;
		$data['total_wcoin_withdrawals'] = 0;
		$data['user_wcoin'] = 0;
	    $html_row = "";
	    $total_contributor_all = 0;    
	    $total_wcoin_accumulation = 0;    
	    if ($num = $ims->db->num_rows($result)){
			$total = array();
			$total['total_payment'] = 0;
			$total['value'] = 0;
			$total['wcoin_withdrawals'] = 0;
			$total['wcoin_buy'] = 0;
			$total['total_wcoin_withdrawals'] = 0;
			$total['total_wcoin_buy'] = 0;
			$total['total_wcoin_withdrawals'] = $ims->db->load_item_sum('user_exchange_log', "exchange_type = 'admin_minus_withdraw' AND (user_code='".$user_code_get."' OR user_id='".$user_id_get."')",'value');;
			$total['total_wcoin_buy'] = $ims->db->load_item_sum('user_exchange_log', "exchange_type = 'buy' AND (user_code='".$user_code_get."' OR user_id='".$user_id_get."')",'value');
			$total['wcoin_withdrawals'] = $ims->db->load_item_sum_where('user_exchange_log', "(user_code='".$user_code_get."' OR user_id='".$user_id_get."')",'wcoin_withdrawals', 'value_type');
			$total['wcoin_buy'] = $ims->db->load_item_sum_where('user_exchange_log', "(user_code='".$user_code_get."' OR user_id='".$user_id_get."')",'wcoin_buy', 'value_type');
			$total['value'] = $ims->db->load_item_sum_where('user_exchange_log', "(user_code='".$user_code_get."' OR user_id='".$user_id_get."')",'value', 'value_type');
			$total['total_payment'] = $ims->db->load_item_sum('user_exchange_log', "(user_code='".$user_code_get."' OR user_id='".$user_id_get."')",'total_amount');
			if($total['total_wcoin_withdrawals'] == NULL){
                $total['total_wcoin_withdrawals'] = 0;
			}
			if($total['total_wcoin_buy'] == NULL){
				$total['total_wcoin_buy'] = 0;
			}
			while ($row = $ims->db->fetch_row($result)){
				$i++;
				$row['stt'] = $start + $i;
				if($row['value_type'] == 1){
					$total_contributor_all += $row['amount_contributor'];
				}
				$data['row_item'] .= $this->manage_row($row);
			}

			$data['total_wcoin_buy'] = $total['total_wcoin_buy'];
			$data['total_wcoin_withdrawals'] = $total['total_wcoin_withdrawals'];
			$total['total_payment']  = $ims->func->get_price_format($total['total_payment'], 0);
			$total['user_wcoin']  = $ims->db->load_item('user', 'is_show = 1 AND user_id ="'.$user_id_get.'" ' ,'wcoin');
			$data['user_wcoin'] = $total['user_wcoin'];
			$ims->temp_act->assign('total', $total);
			$ims->temp_act->reset("row_item_total");
			$ims->temp_act->parse("row_item_total");
			$data['row_item'] .= $ims->temp_act->text("row_item_total");
		}
		else{
			$ims->temp_act->assign('row', array("mess"=>$ims->lang["user"]["no_have_data_concontributor"]));
			$ims->temp_act->parse("manage.row_empty");
		}
		$data['total_wcoin_accumulation'] = $ims->data['user_cur']['wcoin'];
		$data['total_contributor_all'] = $ims->func->get_price_format($total_contributor_all, 0);
		$data['html_row'] = $html_row;
		$data['user'] = $ims->data['user_cur'];
		$data['nav'] = $nav;
		$data['err'] = $err;
		$data['link_action_search'] = $link_action;
		$data['link_action'] = $link_action."&p=".$p.$ext;
		if(isset($ims->data['user_cur']['wcoin_expires']) && $ims->data['user_cur']['wcoin_expires'] != 0){
			$data['wcoin_expires'] = date('d/m/Y H:i:s', $ims->data['user_cur']['wcoin_expires']);
		}
		$data['wcoin_dayexpired'] = isset($ims->setting[$this->modules]['wcoin_dayexpired']) ? $ims->setting[$this->modules]['wcoin_dayexpired'] : 0;
		$data['wcoin2money'] = $ims->func->get_price_format($ims->setting['product']['wcoin_to_money'], 0);
		$data['form_search_class'] = ($is_search == 1) ? ' expand' : '';

		$data['search_date_begin'] = $search_date_begin;
		$data['search_date_end'] = $search_date_end;
	
		$newuser = $ims->db->load_row('user', 'user_contributor="'.$ims->data['user_cur']['user_code'].'" ');
        // Kiểm tra mỗi tháng có 5 thành viên mới
        $data['count_newuser'] = 0;
        if (!empty($newuser)) {
            foreach ($newuser as $k_newuser => $v_newuser) {
                if ($k_newuser == 'date_create') {
                    if (date('m/Y', $v_newuser) == date('m/Y')){
                        $data['count_newuser']++;
                    }
                }else{
                    continue;
                }
            }
        }
        $neworder = $ims->db->load_row('product_order', 'user_id="'.$ims->data['user_cur']['user_id'].'" and is_status=10 ');
        // Kiểm tra mỗi tháng có 1 đơn hàng mới
        $data['count_neworder'] = 0;
        if (!empty($neworder)) {
            foreach ($neworder as $k_neworder => $v_neworder) {
                if ($k_neworder == 'date_create') {
                    if (date('m/Y', $v_neworder) == date('m/Y')){
                        $data['count_neworder']++;
                    }
                }else{
                    continue;
                }
            }
        }
        $list_user = $ims->db->load_item_arr('user', ' root_id="'.$ims->data['user_cur']['user_id'].'" ','user_id');
        $list_user_tmp = array();
        if (!empty($list_user)) {
            foreach ($list_user as $k_user => $v_user) {
                $list_user_tmp[] = $v_user['user_id'];
            }
            if (!empty($list_user_tmp)) {
                $list_user_tmp = implode(',', $list_user_tmp);
            }else{
                $list_user_tmp = '';
            }
        }else{
            $list_user_tmp = '';
        }
        $newbuy = $ims->db->load_row('product_order', ' find_in_set(user_id, "'.$list_user_tmp.'") and is_status=10 ');
        // Kiểm tra mỗi tháng có 10 thành viên con mua hàng
        $data['count_newbuy'] = 0;
        if (!empty($newbuy)) {
            foreach ($newbuy as $k_newbuy => $v_newbuy) {
                if ($k_newbuy == 'date_create') {
                    if (date('m/Y', $v_newbuy) == date('m/Y')){
                        $data['count_newbuy']++;
                    }
                }else{
                    continue;
                }
            }
        }
        $data['m_year'] = date('m/Y');

		if($user_code != '' || $user_id != ''){
		}else{
			$ims->temp_act->assign('LANG', $ims->lang);
			$ims->temp_act->assign('data', $data);
			$ims->temp_act->parse("manage.warning_wcoin");
		}
		$data['page_title'] = $ims->conf["meta_title"];
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("manage");
		return $ims->temp_act->text("manage");
	}
	
  // end class
}
?>