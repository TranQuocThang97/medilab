<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules 	  = "user";
	var $action  	  = "ordering";
	var $sub 	 	  = "manage";
	var $check_search = 0;

	function __construct (){
		global $ims;

		if(isset($ims->get["by_phone"]) && $ims->get["by_phone"] != ''){
			$this->check_search = 1;
		}
		if(isset($ims->conf["cur_act_url"]) && $ims->conf["cur_act_url"] != ''){
			$this->check_search = 1;
		}

		// //ajax api
  //       if(isset($ims->post["f"])){
  //           switch ($ims->post["f"]) {
  //               case 'reload':
  //                   echo $this->do_reload();
  //                   break;                
  //               default:
  //                   // code...
  //                   break;
  //           }
  //           die;
  //       }
		$check = $ims->site_func->checkUserLogin();		
		if($this->check_search == 0 && $check != 1) {
			$url = $ims->func->base64_encode($_SERVER['REQUEST_URI']);
			$url = (!empty($url)) ? '/?url='.$url : '';	
			$link_go = $ims->site_func->get_link($this->modules, $ims->setting[$this->modules]["signin_link"]).$url;
			$ims->html->redirect_rel($link_go);
		}		
		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->action,
			'js'  	 		 => $this->modules,
			'css'  	 		 => $this->modules,
			'use_func'  	 => $this->modules, // Sử dụng func
            'use_navigation' => 0, // Sử dụng navigation
			'required_login' => 0, // Bắt buộc đăng nhập
		);
        $ims->func->loadTemplate($arrLoad);

		include ($this->action."_func.php");		
		
		$data = array();
		$order_code = $ims->conf["cur_act_url"];

		if($order_code == ''){
			$order_code = '}z9H8!R),&;("H"L';
		}
		if($this->check_search == 1) {
			$ims->conf["class_full"] = 'user order';
			$where = " AND order_code='".$order_code."' ";
			if(isset($ims->get["by_phone"]) && $ims->get["by_phone"] != ''){
				$where = " AND (d_phone='".$ims->get["by_phone"]."' OR o_phone='".$ims->get["by_phone"]."') ";
			}
		} else{
			$ims->conf["class_full"] = 'user';
			$where = " AND user_id='".$ims->data['user_cur']['user_id']."' AND order_code='".$order_code."' ";
		}
		$sql = "SELECT * FROM product_order WHERE is_show=1 ".$where;
		$result = $ims->db->query($sql);
    	if ($row = $ims->db->fetch_row($result)){
			$data['content'] = $this->do_edit($row['order_id']);
			if(isset($ims->get["by_phone"]) && $ims->get["by_phone"] != ''){
				// $data['content'] .= $this->do_manage();
			}
		} elseif($ims->site_func->checkUserLogin() == 1) {
			$data['content'] = $this->do_manage();
		} else{
			$url = $ims->func->base64_encode($_SERVER['REQUEST_URI']);
			$url = (!empty($url)) ? '/?url='.$url : '';
			$link_go = $ims->site_func->get_link($this->modules, $ims->setting[$this->modules]["signin_link"]).$url;
			$ims->html->redirect_rel($link_go);
		}

		if($ims->site_func->checkUserLogin() != 1 && $this->check_search == 1)
		{
			$ims->conf['container_layout'] = 'm';
			$ims->conf["class_full"] = 'user search_order';
		}
		else{
			$data['box_left'] = box_left($this->action);
		}
		$ims->temp_act->assign('data', $data);
		if($ims->site_func->checkUserLogin() != 1 && $this->check_search == 1)
		{
			$ims->temp_act->parse("main_1");
			$ims->output .=  $ims->temp_act->text("main_1");
		}
		else{
			$ims->temp_act->parse("main");
			$ims->output .=  $ims->temp_act->text("main");
		}
	}
	
	function table_promotion ($order_create){
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
			while ($row = $ims->db->fetch_row($result)){
				
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
	
	function table_cart ($order = array()){
		global $ims;

		$order_id = $order['order_id'];	

		// $arr_color = $ims->load_data->data_table ('product_color', 'color_id', 'color_id,color,title', "is_show=1 and lang='".$ims->conf['lang_cur']."' order by show_order desc, date_create desc");
		// $arr_size = $ims->load_data->data_table ('product_size', 'size_id', 'size_id,title', "is_show=1 and lang='".$ims->conf['lang_cur']."' order by show_order desc, date_create desc");
		
		$data = array();
		$data['cart_total'] = 0;
		
		$sql = "select *  
						from product_order_detail  
						where order_id='".$order_id."'   
						order by detail_id asc";
		// echo $sql;
		$result = $ims->db->query($sql);
		$html_row = "";		
		if ($num = $ims->db->num_rows($result)) {
			while ($row = $ims->db->fetch_row($result)) { 
				$row['pic_w'] = '';
				$row['pic_h'] = '';
				$row["picture"] = $ims->func->get_src_mod($row["picture"], $row['pic_w'], $row['pic_h'], 1, 0, array('fix_max' => 1));
				$row['quantity'] = (isset($row['quantity'])) ? $row['quantity'] : 0;				
				if($row['option1'] != ''){
					$row['title'] .= ' / '.$row['option1'];
				}
				if($row['option2'] != ''){
					$row['title'] .= ' / '.$row['option2'];
				}
				if($row['option3'] != ''){
					$row['title'] .= ' / '.$row['option3'];
				}
				$row['total'] = $row['quantity'] * $row['price_buy'];
				$data['cart_total'] += $row['total'];
				$arr_gift_include = $ims->func->unserialize($row['arr_gift_include']);
				if(isset($arr_gift_include['include'])){
					foreach ($arr_gift_include['include'] as $key => $value) {
						$data['cart_total'] += $value['price_buy_combo'];
					}
				}
				// if($row['item_related']!='' && $row['item_related']!=0){
				// 	$row['gift'] = $ims->lang['product']['gift'].": ";
				// 	$row['item_related_title'] = $ims->db->load_item('product','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and item_id="'.$row['item_related'].'"','title');
				// 	$row['item_related_pic_src'] = $ims->db->load_item('product','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and item_id="'.$row['item_related'].'"','picture');
				// 	$row['item_related_pic'] = '<img src="'.$ims->func->get_src_mod($row['item_related_pic_src'],30,30,1,0,array()).'">';
				// }
				$row['price_buy'] = $ims->func->get_price_format($row['price_buy']);
				$row['total'] = $ims->func->get_price_format($row['total']);
				if(!empty($row['color'])){
					$row['color_title'] = $row['color']['title'];
					$row['color_value'] = $row['color']['color'];
				}
				if ($row['combo_id']>0 && $row['arr_gift_include']!='') {					
                    $arr_gift_include = $ims->func->unserialize($row['arr_gift_include']);
                    if (!empty($arr_gift_include)) {
                        $ims->temp_act->assign('row', $row);
                        $ims->temp_act->parse("table_cart.row_item.default");
                        $ims->temp_act->parse("table_cart.row_item");
                        if (isset($arr_gift_include['include'])) {
                        	$row['class_type'] = "combo";
                            foreach ($arr_gift_include['include'] as $k => $v) {
                                $v["picture"] = $ims->func->get_src_mod($v["picture"]);
                                $v['price'] = $ims->func->get_price_format($v['price_buy_combo']);
                                // $v["link_combo"] = $ims->admin->get_link_admin($row['type'], $row['type'], 'edit', array("id" => $v['item_id']));
                                $ims->temp_act->assign('item', $v);
                                $ims->temp_act->parse("table_cart.row_item.combo.item");

                                $ims->temp_act->assign('row', $row);
                                $ims->temp_act->parse("table_cart.row_item.combo");
                            }
                        }
                        if (isset($arr_gift_include['gift'])) {
                        	$row['class_type'] = "combo";
                            foreach ($arr_gift_include['gift'] as $k => $v) {                                
                                $v["picture"] = $ims->func->get_src_mod($v["picture"]);
                                $v['price'] = $ims->func->get_price_format($v['price']);
                                // $v["link_combo"] = $ims->admin->get_link_admin($row['type'], $row['type'], 'edit', array("id" => $v['item_id']));
                                $ims->temp_act->assign('item', $v);
                                $ims->temp_act->parse("table_cart.row_item.gift.item");

                                $ims->temp_act->assign('row', $row);
                                $ims->temp_act->parse("table_cart.row_item.gift");
                            }
                        }
                    }
                }else{
                	$row['class_type'] = "";
                    $ims->temp_act->assign('row', $row);
                    $ims->temp_act->parse("table_cart.row_item.default");
                }
				$ims->temp_act->assign('row', $row);
				$ims->temp_act->parse("table_cart.row_item");
			}
		} else {
			$ims->temp_act->assign('row', array('mess' => $ims->lang['user']['no_have_item']));
			$ims->temp_act->parse("table_cart.row_empty");
		}
		$data['wcoin_accumulation'] = $order['wcoin_accumulation'];
		$data['payment_wcoin2money'] = $ims->func->get_price_format($order['payment_wcoin2money'], 0);
		$data['cart_total'] = $ims->func->get_price_format($data['cart_total'], 0);
		$data['promotion_percent'] = $order['promotion_percent'];
		$data['promotion_price'] = $ims->func->get_price_format($order['promotion_price'], 0);
		if($order['method_price'] < 0){
			$data['method_price'] = '-'.$ims->func->get_price_format($order['method_price']*-1, 0);
		}else{
			$data['method_price'] = '+'.$ims->func->get_price_format($order['method_price'], 0);
		}
		$data['shipping_price'] = '+'.$ims->func->get_price_format($order['shipping_price'], 0);
		if($order['shipping_price'] == -1){
			$data['shipping_price'] = $ims->site_func->get_lang('free', 'global');
		}
		$data['voucher_amount'] = isset($order['voucher_amount'])?$ims->func->get_price_format($order['voucher_amount'], 0):'';
		$data['total_payment'] = $ims->func->get_price_format($order['total_payment'], 0);
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("table_cart");
		return $ims->temp_act->text("table_cart");
	}
	
	//-----------
	function do_edit($order_id){
		global $ims;
		
		$err = "";
		
		$arr_order_shipping = $ims->load_data->data_table ('order_shipping', 'shipping_id', 'shipping_id,title,content', "is_show=1 and lang='".$ims->conf['lang_cur']."' order by show_order desc, date_create desc", array(), array('editor'=>'content'));
		$arr_order_method = $ims->load_data->data_table ('order_method', 'method_id', 'method_id,title,content,name_action', "is_show=1 and lang='".$ims->conf['lang_cur']."' order by show_order desc, date_create desc", array(), array('editor'=>'content'));
		
		$sql = "select * from product_order where order_id='".$order_id."'";

	    $result = $ims->db->query($sql);
	    if($data = $ims->db->fetch_row($result)){

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
		
		//$data["list_status_order"] = list_status_order ('is_status',$data['is_status'], " class=\"form-control\"");
		$data["status_order"] = status_order_info ($data["is_status"]);
		
		$data['shipping'] = (isset($data['shipping']) && array_key_exists($data['shipping'], $arr_order_shipping)) ? $arr_order_shipping[$data['shipping']] : array();
		$data['method'] = (isset($data['method']) && array_key_exists($data['method'], $arr_order_method)) ? $arr_order_method[$data['method']] : array();
		$data['o_address'] = $data['o_address'].', 
						   '.get_name_location('location_ward', $data['o_ward']).', 
						   '.get_name_location('location_district', $data['o_district']).', 
						   '.get_name_location('location_province', $data['o_province']);
		$data['d_address'] = $data['d_address'].', 
						   '.get_name_location('location_ward', $data['d_ward']).', 
						   '.get_name_location('location_district', $data['d_district']).', 
						   '.get_name_location('location_province', $data['d_province']);



		if(($data['d_province'] != '' && $data['d_province'] != 0) || ($data['d_district'] != '' && $data['d_district'] != 0)){
			$data['district'] = $this->get_name_localtion('location_district', $data['d_district']);
			$data['province'] = $this->get_name_localtion('location_province', $data['d_province']);
			$ims->temp_act->assign('data', $data);
			$ims->temp_act->parse("edit.province_ship");
		}
		if($data['invoice_company'] != ''){
			$ims->temp_act->assign('data', $data);
			$ims->temp_act->parse("edit.invoice");	
		}
		$arr_order_log = $ims->db->load_row_arr('product_order_log','is_show=1 and order_id="'.$data['order_id'].'" order by date_create desc');
		if($arr_order_log){
			foreach ($arr_order_log as $row) {
				$row['time'] = date('H:i:m d/m/Y',$row['date_create']);
				$ims->temp_act->assign('row', $row);
				$ims->temp_act->parse("edit.order_log.row_log");
			}
		}
		if(!empty($ims->data["user_cur"]["user_id"]) && $ims->data["user_cur"]["user_id"] == $data["user_id"]){
			$arr_cancel = [17,27,29,31];
			if(!in_array($data['is_status'],$arr_cancel)){
				$ims->temp_act->assign('row', $row);
				$ims->temp_act->parse("edit.order_log");
				$ims->temp_act->parse("edit.row_cancel");
				$ims->temp_act->parse("edit.row_cancel2");
			}else{
				$cancel = $ims->db->load_row('product_order_status','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and is_cancel=1');
				$data['cancel'] = '<span class="badge badge-danger p-2">'.$cancel['title'].'</span>';
			}
		}
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("edit");		
		return $ims->temp_act->text("edit");
	}
	
	//-----------
	function manage_row($row){
		global $ims;
		$output = '';
		
		if(!empty($row["picture"])){
			$row["picture"] = '<a class="fancybox-effects-a" title="'.$row["picture"].'" href="'.DIR_UPLOAD.$row["picture"].'">
				'.$ims->func->get_pic_mod($row["picture"], 50, 50, '', 1, 0, array('fix_width'=>1)).'
			</a>';
		}
		
		$row['link'] = $ims->site_func->get_link("user",$ims->setting["user"]["ordering_link"], "/".$row["order_code"]);
		if($row['is_status'] == 0){
			$row['class'] = 'cancel_order';
		}
		$row['status_order'] = status_order_info ($row["is_status"]);
		if($row['is_ConfirmDelivery'] == 1){
			$status_delivery = $ims->db->load_item('product_order_delivery',' order_id="'.$row['order_id'].'"','is_status');			
			$row['status_delivery'] = status_delivery_info ($status_delivery);	
		}else{
			$row['status_delivery'] = status_delivery_info (0);
		}		
		// print_arr($row['status_delivery']);		
		$row['total_order'] = $ims->func->get_price_format($row['total_order']);
		$row['total_payment'] = $ims->func->get_price_format($row['total_payment']);
		$row['time_create'] = date('H:i',$row['date_create']);
		$row['date_create'] = date('d/m/Y',$row['date_create']);
		$row['date_update'] = date("d/m/Y h:i:a", $row['date_update']);
		$ims->temp_act->assign('row', $row);
		
		$ims->temp_act->parse("manage.row_item");
		$output = $ims->temp_act->text("manage.row_item");
		$ims->temp_act->reset("manage.row_item");
		
		return $output;
	}
	
	//-----------
	function do_manage($is_show=""){
		global $ims;
		
		$err = "";

		$p = (isset($ims->input["p"])) ? $ims->input["p"] : 1;
		$search_date_begin = (isset($ims->get["date_begin"])) ? trim($ims->get["date_begin"]) : "";
		$search_date_end = (isset($ims->get["date_end"])) ? trim($ims->get["date_end"]) : "";
		$search_group_id = (isset($ims->input["search_group_id"])) ? trim($ims->input["search_group_id"]) : 0;
		$search_brand_id = (isset($ims->input["search_brand_id"])) ? trim($ims->input["search_brand_id"]) : 0;
		$search_title = (isset($ims->input["search_title"])) ? trim($ims->input["search_title"]) : "";

		$where = " ";
		$ext = $ext_p = "";
		$is_search = 0;
		
		if(isset($ims->get["by_phone"]) && $ims->get["by_phone"] != '' && $ims->site_func->checkUserLogin() != 1){
			$where .= " o.is_show=1";
		}else{
			$where .= " o.is_show=1 and o.user_id='".$ims->data['user_cur']['user_id']."' ";
		}		
		if($search_date_begin || $search_date_end ){
			$tmp1 = @explode("/", $search_date_begin);
			$time_begin = @mktime(0, 0, 0, $tmp1[1], $tmp1[0], $tmp1[2]);
			
			$tmp2 = @explode("/", $search_date_end);
			$time_end = @mktime(23, 59, 59, $tmp2[1], $tmp2[0], $tmp2[2]);
			if($search_date_begin && $search_date_end==''){
				$where .= " and date_create >= {$time_begin} ";
			}elseif($search_date_end && $search_date_begin==''){
				$where .= " and date_create <= {$time_end} ";
			}elseif($search_date_begin && $search_date_end){
				$where.=" AND (date_create BETWEEN {$time_begin} AND {$time_end} ) ";
			}
			// $ext.="&date_begin=".$search_date_begin."&date_end=".$search_date_end;
			$is_search = 1;
		}
		
		if(!empty($search_group_id)){
			$where .=" and find_in_set('".$search_group_id."', group_nav)>0 ";			
			// $ext.="&search_group_id=".$search_group_id;
			$is_search = 1;
		}
		
		if(!empty($search_brand_id)){
			$where .=" and brand_id='".$search_brand_id."' ";			
			// $ext.="&search_brand_id=".$search_brand_id;
			$is_search = 1;
		}
		
		if(!empty($search_title)){
			$where .=" and (order_code='$search_title' or title like '%$search_title%') ";			
			// $ext.="&search_title=".$search_title;
			$is_search = 1;
		}
		if(isset($ims->get["by_phone"]) && $ims->get["by_phone"] != ''){
			$where .=" and (d_phone = '".$ims->get["by_phone"]."' OR o_phone = '".$ims->get["by_phone"]."') ";			
			// $ext.="&by_phone=".$ims->get["by_phone"];
			$is_search = 1;
		}
		$arr_filter = array(
			'all' => $ims->lang['user']['all_order'],
            'new' => $ims->lang['user']['new_order'],
            'deliverying' => $ims->lang['user']['deliverying'],
            'not-payment' => $ims->lang['user']['not_payment'],
            'has-payment' => $ims->lang['user']['has_payment'],
            'refund' => $ims->lang['user']['refund'],
            'complete' => $ims->lang['user']['order_complete'],
            'cancel' => $ims->lang['user']['order_cancel'],
        );
		if(isset($ims->get['filter'])){			
			switch (trim($ims->get['filter'])){
				case 'new':
					$where .=" and is_status=19 ";
					break;
				case 'deliverying':
					$where .=" and is_status=23 ";
					break;
				case 'not-payment':
					$where .=" and (is_status_payment=0 or is_status_payment=1) ";
					break;
				case 'has-payment':
					$where .=" and is_status_payment=3 ";
					break;
				case 'refund':
					$where .=" and is_status_payment=7 ";
					break;
				case 'complete':
					$where .=" and is_status=25 ";
					break;
				case 'cancel':
					$where .=" and (is_status=17 or is_status=27 or is_status=29 or is_status=31)";
					break;
				default:
					break;
			}			
		}
		$arr_param = isset($ims->get)?$ims->get:array();
		if(count($arr_param)>0){
            foreach ($arr_param as $k => $v) {
                if($k!='filter'){
                    $ext .= '&'.$k.'='.$v;
                }
            }
        }
    	// echo $where;
		$num_total = 0;
		$res_num = $ims->db->query("select o.order_id from product_order o where ".$where." ");
			$num_total = $ims->db->num_rows($res_num);
		$n = 7;//($ims->conf["n_list"]) ? $ims->conf["n_list"] : 20;
		$num_products = ceil($num_total / $n);
		if ($p > $num_products)
		  $p = $num_products;
		if ($p < 1)
		  $p = 1;
		$start = ($p - 1) * $n;		
		$link_action = $ims->site_func->get_link($this->modules,$ims->setting[$this->modules]["ordering_link"]);
		$filter = (isset($ims->get['filter'])) ? '&filter='.trim($ims->get['filter']) : '';
		$begin = (isset($ims->get["date_begin"])) ? '&date_begin='.trim($ims->get["date_begin"]) : "";
		$end = (isset($ims->get["date_end"])) ? '&date_end='.trim($ims->get["date_end"]) : "";
		$text = (isset($ims->input["search_title"])) ? '&search_title='.trim($ims->input["search_title"]) : "";
		$ext_p = $filter.$begin.$end.$text;
		$nav = $ims->site->paginate ($link_action, $num_total, $n, $ext_p, $p);

		$where .= " group by o.order_id order by date_create DESC";
		$list_var = 'group_concat(d.title separator ",") as title, 
					o.order_id, 
					o.order_code, 
					o.date_create, 
					o.date_update, 
					o.total_order, 
					o.total_payment, 
					o.is_status, 
					o.is_ConfirmPayment, 
					o.is_ConfirmDelivery,
					o.sales_channel';
    	$sql = "select ".$list_var." from product_order o, product_order_detail d where o.order_id=d.order_id and ".$where." limit $start,$n";
    	// echo $sql;
		// print_arr($ims->setting_ordering);		
		
		
		$result = $ims->db->query($sql);
    	$i = 0;
		$data['row_item'] = '';
	    $html_row = "";
	    if($num = $ims->db->num_rows($result)){
			while ($row = $ims->db->fetch_row($result)){
				$i++;
				$row['stt'] = $start + $i;
				$row['product'] = $this->get_product_name($row['order_id']);
				$data['row_item'] .= $this->manage_row($row);
			}
		}
		else
		{
			$ims->temp_act->assign('row', array("mess"=>$ims->lang["user"]["no_have_data"]));
			$ims->temp_act->parse("manage.row_empty");
		}
		
		$data['html_row'] = $html_row;
		$data['nav'] = $nav;
		$data['err'] = $err;		
		$data['link_action_search'] = $link_action;
		foreach ($arr_filter as $k => $v) {
            $row_f = array();
            $row_f['title'] = $v;
            $row_f['link'] = $link_action.'?filter='.$k.$ext;
            if($k == 'all'){
            	$row_f['link'] = $link_action;

            }elseif($k == 'new'){
            	$text = (isset($ims->input["search_title"])) ? '&search_title='.trim($ims->input["search_title"]) : "";
            	$row_f['link'] = $link_action.'?filter='.$k.$text;
            }
            if(isset($ims->get['filter']) && $k == trim($ims->get['filter'])){                
                $row_f['active'] = "active";
            }elseif(!isset($ims->get['filter']) && $k == "all"){
            	$row_f['active'] = "active";
            }

            $ims->temp_act->assign("row", $row_f);
            $ims->temp_act->parse('manage.row_filter');
        }   
		// $data['link_action'] = $link_action."?p=".$p.$ext;
		
		$data['search_date_begin'] = $search_date_begin;
		$data['search_date_end'] = $search_date_end;
		$data['search_title'] = $search_title;
		$data['form_search_class'] = ($is_search == 1) ? ' show' : '';

		$data['page_title'] = $ims->conf["meta_title"];
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("manage");
		return $ims->temp_act->text("manage");
	}

	function get_product_name($id=''){
		global $ims;
		$output = '';
		$arr_product = $ims->db->load_item_arr('product_order_detail','order_id="'.$id.'"','title');
		if($arr_product){
			if(count($arr_product)>1){
				$num = count($arr_product)<10?'0'.(count($arr_product)-1):count($arr_product)-1;				
				foreach ($arr_product as $k => $row) {
					$output = $ims->func->short($arr_product[0]['title'],50).' '.$ims->site_func->get_lang('other_product','user',array('[num]' => $num));
				}
			}else{
				$output = $ims->func->short($arr_product[0]['title'],50);
			}
		}
		return $output;
	}

	function get_name_localtion($table = '', $id= ''){
		global $ims;
		$output = '';
		
		$sql = "select title from ".$table."
						where is_show=1 
						and lang='".$ims->conf['lang_cur']."' and code = '".$id."'";
		$result = $ims->db->query($sql);
		$arr = $ims->db->fetch_row($result);
		$output = isset($arr['title']) ? $arr['title'] : '';
		return $output;

	}
	
  // end class
}
?>