<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules 	  = "user";
	var $action 	  = "user_contributor";
	var $sub 		  = "manage";
	var $check_search = 0;
	
	/**
		* function __construct ()
		* Danh sách người giới thiệu
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
		$data['content'] = '';
		$order_code = $ims->conf["cur_item_url"];		
		
		$where = " and user_id='".$ims->data['user_cur']['user_id']."' and order_code='".$order_code."' ";
		$sql = "select * from product_order where is_show=10 ".$where;
		$result = $ims->db->query($sql);
    	if ($row = $ims->db->fetch_row($result)){
			$data['content'] .= $this->do_edit($row['order_id']);
		} elseif($ims->site_func->checkUserLogin() == 1) {
			$data['content'] .= $this->do_manage();
		}
		
		$data['box_left'] = box_left($this->action);
		$ims->conf["class_full"] = 'user';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
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
	
	function table_cart ($order = array()){
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
				echo "string";
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("table_cart");
		return $ims->temp_act->text("table_cart");
	}
	
	//-----------
	function do_edit($order_id){
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
		if(empty($row["picture"])){			
			$row["picture"] = '<img src="'.$ims->dir_images.'user.png'.'" alt="'.$row['full_name'].'">';
		}
		else{
			// print_arr($row["picture"]);
			$row["picture"] = '<img src="'.$ims->func->get_src_mod($row["picture"], 40, 40, 1, 0, array('fix_width'=>1)).'" alt="'.$row['full_name'].'">';
		}
        $check_childen = $ims->site_func->check_childen($row);
		if($check_childen == 1){
            $row["children"] = 1;
            $row["box_children"] = '<div class="box_children"></div>';
            $row["item_parent"] = "item_parent";
        }else{
            $row["children"] = 0;
            $row["box_children"] = "";
            $row["item_parent"] = "";
        }
		$row['count'] = $ims->db->do_get_num('user', 'is_show = 1 AND user_contributor = "'.$row['user_code'].'"');
		$row['link'] = $ims->site_func->get_link($this->modules,$ims->setting[$this->modules]["contributor_link"], '', array('user_id' => $row['user_id']));
		$row['date_create'] = date('d/m/Y', $row['date_create']);
		$ims->temp_act->assign('row', $row);
		$ims->temp_act->parse("manage.row_item");
		$output = $ims->temp_act->text("manage.row_item");

		return $output;
	}
	
	//-----------
	function do_manage($is_show=""){
		global $ims;
		
		$err = "";
		$p = (isset($ims->input["p"])) ? $ims->input["p"] : 1;
		$where = " ";
		$ext = "";
		$is_search = 0;

		$where .= " is_show = 1 AND user_contributor='".$ims->data['user_cur']['user_code']."' ";
    
		$num_total = 0;
		$res_num = $ims->db->query("select user_id from user ".$where."  ");
			$num_total = $ims->db->num_rows($res_num);
		$n = 20;
		$num_products = ceil($num_total / $n);
		if ($p > $num_products)
		  $p = $num_products;
		if ($p < 1)
		  $p = 1;
		$start = ($p - 1) * $n;
		
		$link_action = $ims->site_func->get_link($this->modules,$ims->setting[$this->modules]["ordering_link"]);
		$where .= " order by date_create DESC";
        $sql = "select * from user where ".$where." limit $start,$n";
        // echo $sql;
		$nav = $ims->site->paginate ($link_action, $num_total, $n, $ext, $p);
		$result = $ims->db->query($sql);
    	$i = 0;
		$data['row_item'] = '';
	    $html_row = "";
	    if ($num = $ims->db->num_rows($result)){
			while ($row = $ims->db->fetch_row($result)){
				$i++;
				$row['stt'] = $start + $i;
				$data['row_item'] .= $this->manage_row($row);
			}
		}
		else{
			$ims->temp_act->assign('row', array("mess"=>$ims->lang["user"]["no_have_data_userconcontributor"]));
			$ims->temp_act->parse("manage.row_empty");
		}
		$data['html_row'] = $html_row;
		$data['nav'] = $nav;
		$data['err'] = $err;
		$data['link_action_search'] = $link_action;
		$data['link_action'] = $link_action."&p=".$p.$ext;
		if(isset($ims->data['user_cur']['wcoin_expires']) && $ims->data['user_cur']['wcoin_expires'] != 0){
			$data['wcoin_expires'] = date('d/m/Y H:i:s', $ims->data['user_cur']['wcoin_expires']);
		}
		// $data['wcoin_dayexpired'] = $ims->setting[$this->modules]['wcoin_dayexpired'];
		// $data['wcoin2money'] = $ims->func->get_price_format($ims->setting[$this->modules]['wcoin_to_money'], 0);
		$data['form_search_class'] = ($is_search == 1) ? ' expand' : '';
		$data['page_title'] = $ims->conf["meta_title"];
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("manage");
		return $ims->temp_act->text("manage");
	}
	
  // end class
}
?>