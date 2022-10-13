<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "user";
	var $action = "commission";
	var $sub = "manage";
	var $check_search = 0;
	
	/**
		* Quản lý hoa hồng
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

	function manage_row($row){
		global $ims;

		$row['date_create'] = date('d/m/Y H:s',$row['date_create']);
		$row['recommend_link'] = $ims->conf['rooturl'].$ims->db->load_item('user_deeplink', 'id = '.$row['deeplink_id'], 'short_code');
        if($row['order_user'] != 0){
            $row['full_name'] = $ims->db->load_item('user', 'user_id = '.$row['order_user'], 'full_name');
        }else{
            $row['full_name'] = $ims->db->load_item('user', 'phone = "'.$row['o_phone'].'" and email = "'.$row['o_email'].'"', 'full_name');
            if(!$row['full_name']){
                $row['full_name'] = $ims->db->load_item('user_recommend_log', 'referred_phone = "'.$row['o_phone'].'" and referred_email = "'.$row['o_email'].'"', 'referred_full_name');
                if(!$row['full_name']){
                    $row['full_name'] = $row['o_full_name'];
                }
            }
        }
        if($ims->data['user_cur']['show_cart_detail_other_user'] != 0){
            $row['detail_order_link'] = 'href="'.$row['detail_order_link'].'/'.$row['order_code'].'"';
        }
		$ims->temp_act->assign('row', $row);
		$ims->temp_act->parse("manage.row_item");
		$output = $ims->temp_act->text("manage.row_item");
		$ims->temp_act->reset("manage.row_item");

		return $output;
	}
	
	//-----------
	function do_manage(){
		global $ims;
		$data = array();

		$err = "";
		$ext = "";
		$where = "";
        $recommend_link = $ims->site_func->get_link($this->modules,$ims->setting[$this->modules]["recommend_user_link"], '');
		// Dành cho phần tính tổng hoa hồng
        $where_deeplink_id = '';

        $is_search = 0;
		$p 		   		   = $ims->func->if_isset($ims->input['p'], 1);
        $search_date_begin = $ims->func->if_isset($ims->input["search_date_begin"]);
        $search_date_end   = $ims->func->if_isset($ims->input["search_date_end"]);

		$list_deeplink = $ims->db->load_item_arr('user_deeplink', $ims->conf['qr'].' and user_id = '.$ims->data['user_cur']['user_id'], 'id');
		if($list_deeplink){
		    $list_tmp = array();
		    foreach ($list_deeplink as $item){
		        $list_tmp[] = $item['id'];
            }
		    $list_deeplink = implode(',', $list_tmp);
		    $where .= ' and log.deeplink_id IN ('.$list_deeplink.')';
            $where_deeplink_id = ' and deeplink_id IN ('.$list_deeplink.')';
        }else{
            $where .= ' and log.deeplink_id = -1'; // Không có dữ liệu
            $where_deeplink_id = ' and deeplink_id = -1'; // Không có dữ liệu
        }

		$where_date = '';
		if($search_date_begin || $search_date_end ){
			$tmp1 = @explode("/", $search_date_begin);
			$time_begin = @mktime(0, 0, 0, $tmp1[1], $tmp1[0], $tmp1[2]);
			
			$tmp2 = @explode("/", $search_date_end);
			$time_end = @mktime(23, 59, 59, $tmp2[1], $tmp2[0], $tmp2[2]);
			
			$where .= " AND (log.date_create BETWEEN {$time_begin} AND {$time_end} ) ";
            $where_date = " AND (date_create BETWEEN {$time_begin} AND {$time_end} ) ";
			$ext.="&search_date_begin=".$search_date_begin."&search_date_end=".$search_date_end;
			$is_search = 1;
		}

		$res_num = $ims->db->query("select log.id from user_deeplink_log as log where log.is_show = 1 and log.is_added = 10 ".$where);
        $num_total = $ims->db->num_rows($res_num);
		$n = 20;
		$num_products = ceil($num_total / $n);
		if ($p > $num_products)
		    $p = $num_products;
		if ($p < 1)
		    $p = 1;
		$start = ($p - 1) * $n;

		$link_action = $ims->site_func->get_link($this->modules,$ims->setting[$this->modules]["commission_link"]);
		$where .= " ORDER BY log.date_create DESC";
		$sql = "SELECT log.*, od.o_full_name, od.order_code, od.total_order, od.o_email, od.o_phone FROM user_deeplink_log as log, product_order as od where log.is_show = 1 and log.is_added = 1 and log.order_id = od.order_id $where LIMIT $start,$n";

		$nav = $ims->site->paginate ($link_action, $num_total, $n, $ext, $p);
		$result = $ims->db->query($sql);
    	$i = 0;

        $data['row_item'] = '';
        if ($ims->db->num_rows($result)){
            $total = array();
            $total['total_commissions'] = $ims->db->load_item_sum('user_deeplink_log', 'is_added = 1 '.$where_deeplink_id.$where_date, 'commission_add'); //Tổng hoa hồng nhận được lọc theo thời gian
            $total['total_order'] = $ims->db->load_item_sum('product_order', 'deeplink_added = 1 '.$where_deeplink_id.$where_date, 'total_order'); //Tổng hoa hồng nhận được lọc theo thời gian
            while ($row = $ims->db->fetch_row($result)){
				$i++;
				$row['stt'] = $start + $i;
                $row['detail_order_link'] = $recommend_link;
				$data['row_item'] .= $this->manage_row($row);
			}

			$ims->temp_act->assign('total', $total);
			$ims->temp_act->reset("row_item_total");
			$ims->temp_act->parse("row_item_total");
			$data['row_item'] .= $ims->temp_act->text("row_item_total");
		}else{
			$ims->temp_act->assign('row', array("mess"=>$ims->lang["user"]["no_have_data_commission"]));
			$ims->temp_act->parse("manage.row_empty");
		}
        $data['total_commissions'] = $ims->db->load_item_sum('user_deeplink_log', 'is_added = 1 '.$where_deeplink_id, 'commission_add'); // Tổng hoa hồng nhận được
        $data['total_swap_commmission'] = $ims->db->load_item_sum('user_exchange_log', 'exchange_type = "swap_commission" and user_id = '.$ims->data['user_cur']['user_id'], 'total_amount'); // Tổng hoa hồng đã đổi sang điểm
        $data['user_commission'] = $ims->data['user_cur']['commission'];
        $data['nav'] = $nav;
        $data['err'] = $err;
        $data['link_action'] = $link_action."&p=".$p.$ext;
        $data['wcoin2money'] = $ims->func->get_price_format($ims->setting['product']['wcoin_to_money'], 0);
        $data['form_search_class'] = ($is_search == 1) ? ' expand' : '';
        $data['search_date_begin'] = $search_date_begin;
        $data['search_date_end'] = $search_date_end;

        if($ims->data['user_cur']['commission'] > 0){
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