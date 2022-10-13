<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "user";
	var $action = "accumulate_points";
	var $sub = "manage";
	var $check_search = 0;
	
	/**
		* Quản lý điểm tích lũy
	**/
	function __construct (){
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

	function do_manage(){
		global $ims;
		$data = array();
		$err = "";
		$ext = "";
		$p 		   		   = $ims->func->if_isset($ims->input['p'], 1);
		$search_date_end   = $ims->func->if_isset($ims->input["search_date_end"]);
		$search_date_begin = $ims->func->if_isset($ims->input["search_date_begin"]);

        $user_id = $ims->data['user_cur']['user_id'];
        $where = ' and user_id = '.$user_id.' and exchange_type != "ouser_wcoin"';

        // Những đơn hàng chưa hoàn tất và chưa hủy
        $list_order_not_complete = $ims->db->load_item_arr('product_order', 'is_show = 1 and is_status NOT IN(17,27,29,31) and user_id = '.$user_id, 'order_id');
        if($list_order_not_complete){
            foreach ($list_order_not_complete as $itm){
                $where_order_not_complete[] = ' dbtable_id = "'.$itm['order_id'].'"';
            }
            $where_order_not_complete = implode(' OR ', $where_order_not_complete);
            $where_not_complete = ' and user_id = '.$user_id.' and (exchange_type != "ouser_wcoin" or (exchange_type = "ouser_wcoin" and dbtable = "product_order" and ('.$where_order_not_complete.')))';
        }

        // Chỉ lấy những đơn hàng đã hoàn tất
        $complete_status = $ims->db->load_item('product_order_status', $ims->conf['qr'].' and is_complete = 1', 'item_id');
        $list_order_id_tmp = $ims->db->load_item_arr('product_order', 'is_show = 1 and is_status = '.$complete_status.' and user_id = '.$user_id, 'order_id');
        if($list_order_id_tmp){
            foreach ($list_order_id_tmp as $it){
                $where_order[] = ' dbtable_id = "'.$it['order_id'].'"';
            }
            $where_order = implode(' OR ', $where_order);
            $where = ' and user_id = '.$user_id.' and (dbtable != "product_order" or (dbtable = "product_order" and ('.$where_order.')))';
        }

        $where_date = '';
		if($search_date_begin || $search_date_end ){
			$tmp1 = @explode("/", $search_date_begin);
			$time_begin = @mktime(0, 0, 0, $tmp1[1], $tmp1[0], $tmp1[2]);
			
			$tmp2 = @explode("/", $search_date_end);
			$time_end = @mktime(23, 59, 59, $tmp2[1], $tmp2[0], $tmp2[2]);
			
            $where_date =" AND (date_create BETWEEN {$time_begin} AND {$time_end} ) ";
			$ext.="&search_date_begin=".$search_date_begin."&search_date_end=".$search_date_end;
		}

		$res_num = $ims->db->query("select id from user_exchange_log where is_show = 1 ".$where);
        $num_total = $ims->db->num_rows($res_num);
		$n = 20;
		$num_products = ceil($num_total / $n);
		if ($p > $num_products){$p = $num_products;}
		if ($p < 1){$p = 1;}
		$start = ($p - 1) * $n;
		
		$link_action = $ims->site_func->get_link($this->modules,$ims->setting[$this->modules]["accumulate_points_link"]);
		$order_by = " ORDER BY date_create DESC";
		$sql = "SELECT * FROM user_exchange_log where is_show = 1".$where.$where_date.$order_by." LIMIT $start,$n";

		$nav = $ims->site->paginate ($link_action, $num_total, $n, $ext, $p);
		$result = $ims->db->query($sql);
    	$i = 0;
        $data['row_item'] = '';
	    if ($ims->db->num_rows($result)){
	        $total = array();
            $total['user_wcoin'] = number_format($ims->db->load_item_sum_where('user_exchange_log', 'is_show = 1'.$where.$where_date, 'value', 'value_type'), 1,',','.');
            $total['total_payment'] = $ims->db->load_item_sum('user_exchange_log', 'is_show = 1 '.$where.$where_date, 'total_amount');
//            $total['total_wcoin_buy'] = $ims->db->load_item_sum('user_exchange_log', "is_show = 1 and exchange_type = 'buy' $where $where_date", 'value');
            while ($row = $ims->db->fetch_row($result)){
				$i++;
				$row['stt'] = $start + $i;
				$data['row_item'] .= $this->manage_row($row);
			}

			$ims->temp_act->assign('total', $total);
			$ims->temp_act->parse("row_item_total");
			$data['row_item'] .= $ims->temp_act->text("row_item_total");
		}
		else{
			$ims->temp_act->assign('row', array("mess"=>$ims->lang["user"]["no_have_data_point"]));
			$ims->temp_act->parse("manage.row_empty");
		}
		$data['total_wcoin_buy'] = $ims->db->load_item_sum('user_exchange_log', "is_show = 1 and exchange_type = 'buy' $where", 'value');
		if(isset($where_not_complete)){
            $data['total_wcoin_buy_not_complete'] = $ims->db->load_item_sum('user_exchange_log', "is_show = 1 and exchange_type = 'buy' $where_not_complete", 'value');
        }else{
            $data['total_wcoin_buy_not_complete'] = 0;
        }
        $data['user_wcoin'] = $ims->data['user_cur']['wcoin'];
		$data['user'] = $ims->data['user_cur'];
		$data['nav'] = $nav;
		$data['err'] = $err;
		$data['link_action_search'] = $link_action;
		$data['link_action'] = $link_action."&p=".$p.$ext;
//		if(isset($ims->data['user_cur']['wcoin_expires']) && $ims->data['user_cur']['wcoin_expires'] != 0){
//			$data['wcoin_expires'] = date('d/m/Y H:i:s', $ims->data['user_cur']['wcoin_expires']);
//		}
//		$data['wcoin_dayexpired'] = isset($ims->setting[$this->modules]['wcoin_dayexpired']) ? $ims->setting[$this->modules]['wcoin_dayexpired'] : 0;
//		$data['wcoin2money'] = $ims->func->get_price_format($ims->setting['product']['wcoin_to_money'], 0);

		$data['search_date_begin'] = $search_date_begin;
		$data['search_date_end'] = $search_date_end;

//		$newuser = $ims->db->load_row('user', 'user_contributor="'.$ims->data['user_cur']['user_code'].'" ');
//        // Kiểm tra mỗi tháng có 5 thành viên mới
//        $data['count_newuser'] = 0;
//        if (!empty($newuser)) {
//            foreach ($newuser as $k_newuser => $v_newuser) {
//                if ($k_newuser == 'date_create') {
//                    if (date('m/Y', $v_newuser) == date('m/Y')){
//                        $data['count_newuser']++;
//                    }
//                }else{
//                    continue;
//                }
//            }
//        }
//        $neworder = $ims->db->load_row('product_order', 'user_id = "'.$ims->data['user_cur']['user_id'].'" and is_status = 10 ');
//        // Kiểm tra mỗi tháng có 1 đơn hàng mới
//        $data['count_neworder'] = 0;
//        if (!empty($neworder)) {
//            foreach ($neworder as $k_neworder => $v_neworder) {
//                if ($k_neworder == 'date_create') {
//                    if (date('m/Y', $v_neworder) == date('m/Y')){
//                        $data['count_neworder']++;
//                    }
//                }else{
//                    continue;
//                }
//            }
//        }
//        $list_user = $ims->db->load_item_arr('user', ' root_id="'.$ims->data['user_cur']['user_id'].'" ','user_id');
//        $list_user_tmp = array();
//        if (!empty($list_user)) {
//            foreach ($list_user as $k_user => $v_user) {
//                $list_user_tmp[] = $v_user['user_id'];
//            }
//            if (!empty($list_user_tmp)) {
//                $list_user_tmp = implode(',', $list_user_tmp);
//            }else{
//                $list_user_tmp = '';
//            }
//        }else{
//            $list_user_tmp = '';
//        }
//        $newbuy = $ims->db->load_row('product_order', ' find_in_set(user_id, "'.$list_user_tmp.'") and is_status=10 ');
//        // Kiểm tra mỗi tháng có 10 thành viên con mua hàng
//        $data['count_newbuy'] = 0;
//        if (!empty($newbuy)) {
//            foreach ($newbuy as $k_newbuy => $v_newbuy) {
//                if ($k_newbuy == 'date_create') {
//                    if (date('m/Y', $v_newbuy) == date('m/Y')){
//                        $data['count_newbuy']++;
//                    }
//                }else{
//                    continue;
//                }
//            }
//        }
        $data['m_year'] = date('m/Y');
		$data['page_title'] = $ims->conf["meta_title"];

		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("manage");
		return $ims->temp_act->text("manage");
	}

    function manage_row($row){
        global $ims;

        if(!empty($row["picture"])){
            $row["picture"] = '<a class="fancybox-effects-a" title="'.$row["picture"].'" href="'.DIR_UPLOAD.$row["picture"].'">
				'.$ims->func->get_pic_mod($row["picture"], 50, 50, '', 1, 0, array('fix_width'=>1)).'
			</a>';
        }

        $row['order_code'] = ($row['dbtable_id'] != '') ? $ims->db->load_item('product_order', 'order_id = '.$row['dbtable_id'], 'order_code') : '----';
        $row['plus_minus'] = $row['value_type'] == 1 ? '+' : '-';
        $row['total_payment'] = number_format($row['total_amount'],0,',','.');
        $row['date_create'] = date('d/m/Y H:s',$row['date_create']);
        $row['exchange_type_text'] = $ims->lang['user']['exchange_type_'.$row['exchange_type']];

        if(($row['total_amount'] == 0 && $row['exchange_type'] == 'admin_minus_withdraw') || $row['exchange_type'] == 'buy'){
            $row['total_payment'] = '---';
        }
        if ($row['exchange_type'] == 'shared_comment') {
            $Comment = $ims->db->load_row('shared_comment', ' item_id="'.$row['dbtable_id'].'" and lang="'.$ims->conf['lang_cur'].'"');
            $Product = $ims->db->load_row('product', ' item_id="'.$Comment['type_id'].'" and lang="'.$ims->conf['lang_cur'].'" and is_show=1 ');
            $link_product = $ims->conf['rooturl_web'].$Product['friendly_link'].'#root_form';
            $row['exchange_type'] = '<a href="'.$link_product.'">'.$row['exchange_type_text'].'</a>'.'<div><a class="fancybox-effects-a" href="#show_comment'.$row['id'].'">'.$ims->lang['user']['comment_detail'].'</a></div><div id="show_comment'.$row['id'].'" style="display:none;padding: 20px;min-width:500px;">'.$Comment['content'].'</div>';
        }

        if($row['dbtable'] == 'product_order'){
            $arr_order = $ims->db->load_row('product_order', ' is_show = 1 AND order_code = "'.$row['dbtable_id'].'" ');
            if(!empty($arr_order)){
                if($row['exchange_type'] == 'up_contributor' || $row['exchange_type'] == 'up_contributor_root'){
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
	
  // end class
}
?>