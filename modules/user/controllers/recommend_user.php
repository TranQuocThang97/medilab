<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules 	  = "user";
	var $action 	  = "recommend_user";
	var $sub 		  = "manage";
	var $check_search = 0;
	

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
		$data['content'] = '';

		$order_code = $ims->conf["cur_act_url"];

		$check = $ims->db->load_row('product_order', 'is_show = 1 and order_code = "'.$order_code.'"', 'order_id, deeplink_user');
		if($order_code){
            if($check['deeplink_user'] != $ims->data['user_cur']['user_id'] || $ims->data['user_cur']['show_cart_detail_other_user'] == 0){
                $ims->html->redirect_rel($ims->site_func->get_link($this->modules, $ims->setting['user']["recommend_user_link"]));
            }else{
                $data['content'] .= $this->do_detail_order($check['order_id'], $order_code);
            }
        }elseif(isset($ims->input['phone']) || isset($ims->input['email']) || isset($ims->input['user_id'])){
            $data['content'] .= $this->do_referred_list_order();
        }elseif($ims->site_func->checkUserLogin() == 1) {
			$data['content'] .= $this->do_manage();
		}

		$data['box_left'] = box_left($this->action);
		$ims->conf["class_full"] = 'user';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}

	function manage_row($row){
		global $ims;

		if($row['referred_user_id'] > 0){
            $user_info = $ims->db->load_row('user', 'user_id = '.$row['referred_user_id'], 'full_name, email, phone, picture');
            $row['full_name'] = $user_info['full_name'];
            $row['email'] = $user_info['email'];
            $row['phone'] = $user_info['phone'];
        }else{
            $user_info = $ims->db->load_row('user', 'phone = "'.$row['referred_phone'].'" and email = "'.$row['referred_email'].'"', 'full_name, email, phone, picture');
            if($user_info){
                $row['full_name'] = $user_info['full_name'];
                $row['email'] = $user_info['email'];
                $row['phone'] = $user_info['phone'];
//            $row['picture'] = $user_info['picture'];
            }else{
                $row['full_name'] = $row['referred_full_name'];
                $row['email'] = $row['referred_email'];
                $row['phone'] = $row['referred_phone'];
//            $row['picture'] = '';
            }
        }

        $row['link'] = $ims->site_func->get_link($this->modules,$ims->setting[$this->modules]["recommend_user_link"], '', array('phone' => $row['phone'], 'email' => $row['email']));
//		if(empty($row["picture"])){
//			$row["picture"] = '<img src="'.$ims->dir_images.'user.png'.'" alt="'.$row['full_name'].'">';
//		}else{
//			$row["picture"] = '<img src="'.$ims->func->get_src_mod($row["picture"], 40, 40, 1, 0, array('fix_width'=>1)).'" alt="'.$row['full_name'].'">';
//		}
		if($row['type'] == 'deeplink'){
		    $row['recommend_link'] = $ims->conf['rooturl'].$row['recommend_link'];
        }else{
            $row['recommend_link'] = $ims->conf['rooturl'].'?'.$row['recommend_link'];
        }
//        $check_children = $ims->site_func->check_children($row);
//		if($check_children == 1){
//            $row["children"] = 1;
//            $row["box_children"] = '<div class="box_children"></div>';
//            $row["item_parent"] = "item_parent";
//        }else{
//            $row["children"] = 0;
//            $row["box_children"] = "";
//            $row["item_parent"] = "";
//        }
//		$row['count'] = $ims->db->do_get_num('user', 'is_show = 1 AND user_contributor = "'.$row['user_code'].'"');

		$row['date_create'] = date('d/m/Y', $row['date_create']);
		$ims->temp_act->assign('row', $row);
		$ims->temp_act->parse("manage.row_item");
		$output = $ims->temp_act->text("manage.row_item");

		return $output;
	}
	
	//-----------
	function do_manage(){
		global $ims;
		
		$err = '';
        $where = ' and recommend_user_id = '.$ims->data['user_cur']['user_id'];
        $ext = '';
        $is_search = 0;
        $p = (isset($ims->input["p"])) ? $ims->input["p"] : 1;

		$res_num = $ims->db->query('select id from user_recommend_log where is_show = 1'.$where);
        $num_total = $ims->db->num_rows($res_num);
		$n = 20;
		$num_products = ceil($num_total / $n);
		if ($p > $num_products)
		  $p = $num_products;
		if ($p < 1)
		  $p = 1;
		$start = ($p - 1) * $n;
		
		$link_action = $ims->site_func->get_link($this->modules, $ims->setting[$this->modules]["recommend_user_link"]);
		$where .= " order by date_create DESC";
        $sql = "select * from user_recommend_log where is_show = 1".$where." limit $start,$n";
		$nav = $ims->site->paginate ($link_action, $num_total, $n, $ext, $p);
		$result = $ims->db->query($sql);
    	$i = 0;
		$data['row_item'] = '';
	    if ($ims->db->num_rows($result)){
			while ($row = $ims->db->fetch_row($result)){
				$i++;
				$row['stt'] = $start + $i;
				$data['row_item'] .= $this->manage_row($row);
			}
		}else{
			$ims->temp_act->assign('row', array("mess"=>$ims->lang["user"]["no_have_data_userconcontributor"]));
			$ims->temp_act->parse("manage.row_empty");
		}
		$data['nav'] = $nav;
		$data['err'] = $err;
		$data['link_action_search'] = $link_action;
		$data['link_action'] = $link_action."&p=".$p.$ext;
		$data['form_search_class'] = ($is_search == 1) ? ' expand' : '';
		$data['page_title'] = $ims->conf["meta_title"];
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("manage");
		return $ims->temp_act->text("manage");
	}

	function do_referred_list_order(){
		global $ims;

        $p = (isset($ims->input["p"])) ? $ims->input["p"] : 1;
        $phone = isset($ims->get['phone']) ? $ims->get['phone'] : '';
        $email = isset($ims->get['email']) ? $ims->get['email'] : '';
        $ext = '&phone='.$phone.'&email='.$email;

        if ($phone != '' || $email != ''){
            $where = ' and ((o_phone = "'.$phone.'" or o_email = "'.$email.'") and user_id != '.$ims->data['user_cur']['user_id'].')';
            $recommend_user_id = $ims->db->load_item('user_recommend_log', 'is_show = 1 and referred_phone = "'.$ims->get['phone'].'" or referred_email = "'.$ims->get['email'].'"', 'recommend_user_id');
            if(!$recommend_user_id || $recommend_user_id != $ims->data['user_cur']['user_id']){
                $ims->html->redirect_rel($ims->site_func->get_link($this->modules, $ims->setting['user']["recommend_user_link"]));
            }
            $full_name = $ims->db->load_item('user', 'phone = "'.$phone.'" and email = "'.$email.'"', 'full_name'); // Lấy tên người giới thiệu theo user trước
            if(!$full_name){
                $full_name = $ims->db->load_item('user_recommend_log', 'is_show = 1 and referred_phone = "'.$phone.'" or referred_email = "'.$email.'"', 'referred_full_name');
            }
        }else{
            $ims->html->redirect_rel($ims->site_func->get_link($this->modules, $ims->setting['user']["recommend_user_link"]));
        }

		$res_num = $ims->db->query('select order_id from product_order where is_show = 1'.$where);
		$num_total = $ims->db->num_rows($res_num);
		$n = 20;
		$num_products = ceil($num_total / $n);
		if ($p > $num_products)
		    $p = $num_products;
		if ($p < 1)
		    $p = 1;
		$start = ($p - 1) * $n;

		$link_action = $ims->site_func->get_link($this->modules,$ims->setting[$this->modules]["recommend_user_link"], '');
		$where .= " order by date_create DESC";
		$sql = "select * from product_order where is_show = 1".$where." limit $start,$n";
		$nav = $ims->site->paginate ($link_action, $num_total, $n, $ext, $p);
		$result = $ims->db->query($sql);
    	$i = 0;

		$complete_status = $ims->db->load_item('product_order_status', $ims->conf['qr'].' and is_complete = 1', 'item_id');
        $temp = ($ims->data['user_cur']['show_cart_detail_other_user'] == 1) ? 'show_detail_order' : 'normal';

        $order_status = $ims->load_data->data_table("product_order_status", "item_id",  "*", $ims->conf['qr']);
	    if ($ims->db->num_rows($result)){
			while ($row = $ims->db->fetch_row($result)){
				$i++;
				$row['stt'] = $start + $i;
				$row['date_create'] = date('d/m/Y', $row['date_create']);
				$row['status_order'] = $order_status[$row['is_status']]['title'];
				$status = $order_status[$row['is_status']]['statusclass'];
                if($status == 'danger'){
                    $row['commission_status'] = $ims->lang['user']['not_added'];
                }else{
                    $row['commission_status'] = ($row['is_status'] == $complete_status) ? $ims->lang['user']['added'] : $ims->lang['user']['not_yet_added'];
                }
                $row['total_order_after_promotion'] = $row['total_order'] - $row['promotion_price'];
                $row['link'] = $link_action.'/'.$row['order_code'];
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("referred_list_order.".$temp.".row_item");
			}
		}else{
			$ims->temp_act->assign('row', array("mess"=>$ims->lang["user"]["no_have_data_userconcontributor"]));
			$ims->temp_act->parse("referred_list_order.row_empty");
		}

//	    $data['total_expected_commission'] = $ims->db->load_item_sum('product_order', 'is_show = 1'.$where, 'deeplink_total');
	    $data['total_commission_received'] = $ims->db->load_item_sum('product_order', 'is_show = 1 and is_status = '.$complete_status.$where, 'deeplink_total');
		$data['nav'] = $nav;
		$ims->conf['meta_title'] = $data['page_title'] = $ims->lang['user']['list_order_of'].' '.$full_name;

		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("referred_list_order.".$temp);
		$ims->temp_act->parse("referred_list_order");
		return $ims->temp_act->text("referred_list_order");
	}

	function do_detail_order($order_id, $order_code){
	    global $ims;

        $order = $ims->db->load_row('product_order as pod, user_deeplink_log as udl', 'pod.order_id = '.$order_id.' and udl.order_id = '.$order_id, 'pod.order_code, pod.total_order, pod.promotion_price, pod.deeplink_total, pod.is_use_deeplink_old, pod.is_status, udl.deeplink_detail');
        $ims->conf['meta_title'] = $order['page_title'] = $ims->lang['user']['order_detail'].' #'.$order_code;
        if($order){
	        $is_use_deeplink_old = $order['is_use_deeplink_old'];
	        $deeplink_detail = $ims->func->unserialize($order['deeplink_detail']);
            foreach ($deeplink_detail as $row){
                $order['amount_deeplink_default'] = $amount_deeplink_default = (float)$row['max_deeplink_default_per_item'];
                $info = $ims->db->load_row('product', $ims->conf['qr'].' and item_id = '.$row['item_id'], 'title, picture');
                $row['title'] = $info['title'];
                $row['picture'] = $ims->func->get_src_mod($row['picture']);
                $row['into_money'] = $row['price_buy'] * $row['quantity'];
                $row['price_minus'] = $row['into_money'] - $row['price_use_commisson'];
                $percent_deeplink_old = ((float)$row['percent_deeplink_group_old'] > 0) ? (float)$row['percent_deeplink_group_old'] : (float)$row['percent_deeplink_default_old'];
                $percent_deeplink_new = ((float)$row['percent_deeplink_group_new'] > 0) ? (float)$row['percent_deeplink_group_new'] : (float)$row['percent_deeplink_default_new'];
                $row['percent_deeplink'] = ($is_use_deeplink_old == 1) ? $percent_deeplink_old : $percent_deeplink_new;
                $row['commission'] = round((float)$row['price_use_commisson'] * $row['percent_deeplink'] / 100, 2);
                if($row['commission'] > $amount_deeplink_default){
                    $row['commission'] = $amount_deeplink_default;
                }
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("detail_order.row_item");
                if($row['arr_deeplink_include'] != ''){
                    foreach ($row['arr_deeplink_include'] as $include){
                        $include_info = $ims->db->load_row('product', $ims->conf['qr'].' and item_id = '.$include['item_id'], 'title, picture');
                        $include['title'] = $include_info['title'];
                        $include['picture'] = $ims->func->get_src_mod($include_info['picture']);
                        $include['quantity'] = 1;
                        $include['price_buy'] = $include['price_buy_discounted'];
                        $include['into_money'] = $include['price_buy_discounted'];
                        $include['price_minus'] = 0;
                        $include_percent_deeplink_old = ((float)$include['percent_deeplink_group_old'] > 0) ? (float)$include['percent_deeplink_group_old'] : (float)$row['percent_deeplink_default_old'];
                        $include_percent_deeplink_new = ((float)$include['percent_deeplink_group_new'] > 0) ? (float)$include['percent_deeplink_group_new'] : (float)$row['percent_deeplink_default_new'];
                        $include['percent_deeplink'] = ($is_use_deeplink_old == 1) ? $include_percent_deeplink_old : $include_percent_deeplink_new;
                        $include['commission'] = round((float)$include['price_buy_discounted'] * $include['percent_deeplink'] / 100, 2);
                        if($include['commission'] > $amount_deeplink_default){
                            $include['commission'] = $amount_deeplink_default;
                        }
                        $include['include'] = '<p><span class="badge badge-info">'.$ims->lang['user']['include'].'</span></p>';
                        $ims->temp_act->assign('row', $include);
                        $ims->temp_act->parse("detail_order.row_item");
                    }
                }
            }
        }
        $complete_status = $ims->db->load_item('product_order_status', $ims->conf['qr'].' and is_complete = 1', 'item_id');
        $status = $ims->db->load_item('product_order_status', $ims->conf['qr'].' and item_id = '.$order['is_status'], 'statusclass');
        $order['total_order_after_promotion'] = $order['total_order'] - $order['promotion_price'];
        $order['order_status'] = $ims->db->load_item('product_order_status', $ims->conf['qr'].' and item_id = '.$order['is_status'], 'title');
        if($status == 'danger'){
            $order['commission_status'] = $ims->lang['user']['not_added'];
        }else{
            $order['commission_status'] = ($order['is_status'] == $complete_status) ? $ims->lang['user']['added'] : $ims->lang['user']['not_yet_added'];
        }
        $ims->temp_act->assign('data', $order);
        $ims->temp_act->parse("detail_order");
        return $ims->temp_act->text("detail_order");
    }
	
  // end class
}
?>