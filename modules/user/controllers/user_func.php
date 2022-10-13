<?php
if (! defined('IN_ims')) { die('Hacking attempt!'); }
define('DIR_MOD_UPLOAD', $ims->conf['rooturl'].'uploads/user/');

function setting(){
	global $ims;
	if(!isset($ims->setting_voucher)){
		$ims->setting_voucher = array();
		$result = $ims->db->query("select * from voucher_setting where lang='".$ims->conf['lang_cur']."' ");
		if($row = $ims->db->fetch_row($result)){
			$ims->setting_voucher = $row;
		}
	}
	$ims->setting_promotion = array();	
	$ims->setting_promotion['status'] = array(
		0 => array(
			'title' => !empty($ims->lang['user']['promotion_status_0']) ? $ims->lang['user']['promotion_status_0'] : '',
			'color' => '#000',
			'background_color' => '#fff',
			'border_color' => '#ddd',
		),
		1=> array(
			'title' => !empty($ims->lang['user']['promotion_status_1']) ? $ims->lang['user']['promotion_status_1'] : '',
			'color' => '#fff',
			'background_color' => '#f44336',
			'border_color' => '#f44336',
		)
	);
	
	return false;
}

setting();

function get_navigation($modules, $action) {
    global $ims;

    $arr_nav = array(
        array(
            'title' => '<i class="fal fa-home"></i> '.$ims->lang['global']['homepage'],
            'link' => $ims->site->get_link('home')
        ),
        array(
            'title' => $ims->setting[$modules][$action.'_meta_title'],
            'link' => $ims->site->get_link($modules, $ims->setting[$modules][$action.'_link'])
        )
    );

    return $ims->site->html_arr_navigation($arr_nav);
}

function promotion_status_info ($status=0) {
	global $ims;
	
	$output = (isset($ims->setting_promotion['status'][$status])) ? $ims->setting_promotion['status'][$status] : array();
	return $output;
}

// function box_menu ($cur="") {
// 	global $ims;
	
// 	$data = array(
// 		'title' => $ims->lang['user']['menu_title'],
// 		'content' => ''
// 	);
// 	$src = $ims->conf['rooturl'].'resources/images/user/';
// 	$arr_is_login = array(
// 		'user' => array(
// 			'title' => '<i class="icon-user"><img src="'.$src.'user.png"/></i> '.$ims->setting["user"]["user_meta_title"],
// 			'link' => $ims->site_func->get_link("user"),
// 		),
// 		'account' => array(
// 			'title' => '<i class="icon-user"><img src="'.$src.'user-info.png"/></i> '.$ims->setting["user"]["account_meta_title"],
// 			'link' => $ims->site_func->get_link("user",$ims->setting["user"]["account_link"])
// 		),
// 		'change_pass' => array(
// 			'title' => '<i class="icon-user"><img src="'.$src.'user-password.png"/></i> '.$ims->setting["user"]["change_pass_meta_title"],
// 			'link' => $ims->site_func->get_link("user",$ims->setting["user"]["change_pass_link"])
// 		),		
// 		'address_book' => array(
// 			'title' => '<i class="icon-user"><img src="'.$src.'user-address.png"/></i> '.$ims->setting["user"]["address_book_meta_title"],
// 			'link' => $ims->site_func->get_link("user",$ims->setting["user"]["address_book_link"])
// 		),
// 		'notifications' => array(
// 			'title' => '<i class="icon-user"><img src="'.$src.'user-noti.png"/></i> '.$ims->setting["user"]["notifications_meta_title"],
// 			'link' => $ims->site_func->get_link("user",$ims->setting["user"]["notifications_link"]),
// 			'count' => check_notification(),
// 		),
// 		'promotion_code' => array(
// 			'title' => '<i class="icon-user"><img src="'.$src.'user-coupon.png"/></i> '.$ims->setting["user"]["promotion_code_meta_title"],
// 			'link' => $ims->site_func->get_link("user",$ims->setting["user"]["promotion_code_link"]),
// 			'count' => check_promotion_code(),
// 		),		
// 		'ordering' => array(
// 			'title' => '<i class="icon-user"><img src="'.$src.'user-ordering.png"/></i> '.$ims->setting["user"]["ordering_meta_title"],
// 			'link' => $ims->site_func->get_link("user",$ims->setting["user"]["ordering_link"])
// 		),
//         'accumulate_points' => array(
//             'title' => '<i class="icon-user"><img src="'.$src.'user-ordering.png"/></i> '.$ims->setting["user"]["accumulate_points_meta_title"],
//             'link' => $ims->site_func->get_link("user",$ims->setting["user"]["accumulate_points_link"])
//         ),
// 		'commission' => array(
// 			'title' => '<i class="icon-user"><img src="'.$src.'user-commission.png"/></i> '.$ims->setting["user"]["commission_meta_title"],
// 			'link' => $ims->site_func->get_link("user",$ims->setting["user"]["commission_link"])
// 		),
// 		'swap_commission_history' => array(
// 			'title' => '<i class="icon-user"><img src="'.$src.'user-commission.png"/></i> '.$ims->setting["user"]["swap_commission_history_meta_title"],
// 			'link' => $ims->site_func->get_link("user",$ims->setting["user"]["swap_commission_history_link"])
// 		),
// 		'recommend_user' => array(
// 			'title' => '<i class="icon-user"><img src="'.$src.'user-presenter.png"/></i> '.$ims->setting["user"]["recommend_user_meta_title"],
// 			'link' => $ims->site_func->get_link("user",$ims->setting["user"]["recommend_user_link"])
// 		),
// 		'deeplink_account' => array(
//             'title' => '<i class="icon-user"><img src="'.$src.'user-deeplink.png"/></i> '.$ims->setting["user"]["deeplink_account_meta_title"],
//             'link' => $ims->site_func->get_link("user",$ims->setting["user"]["deeplink_account_link"])
//         ),
// 		'deeplink' => array(
//             'title' => '<i class="icon-user"><img src="'.$src.'user-deeplink.png"/></i> '.$ims->setting["user"]["deeplink_meta_title"],
//             'link' => $ims->site_func->get_link("user",$ims->setting["user"]["deeplink_link"])
//         ),
// //        'deeplink_statistics' => array(
// //            'title' => '<i class="icon-user"><img src="'.$src.'user-deeplink-statistical.png"/></i> '.$ims->setting["user"]["deeplink_statistics_meta_title"],
// //            'link' => $ims->site_func->get_link("user",$ims->setting["user"]["deeplink_statistics_link"])
// //        ),
// 		'list_watched' => array(
// 			'title' => '<i class="icon-user"><img src="'.$src.'user-viewed-product.png"/></i> '.$ims->setting["user"]["list_watched_meta_title"],
// 			'link' => $ims->site_func->get_link("user",$ims->setting["user"]["list_watched_link"])
// 		),
// 		'list_favorite' => array(
// 			'title' => '<i class="icon-user"><img src="'.$src.'user-favorite-product.png"/></i> '.$ims->setting["user"]["list_favorite_meta_title"],
// 			'link' => $ims->site_func->get_link("user",$ims->setting["user"]["list_favorite_link"])
// 		),
// 		'list_save' => array(
// 			'title' => '<i class="icon-user"><img src="'.$src.'user-save-later.png"/></i> '.$ims->setting["user"]["list_save_meta_title"],
// 			'link' => $ims->site_func->get_link("user",$ims->setting["user"]["list_save_link"])
// 		),		
// 		// 'my_comment' => array(
// 		// 	'title' => '<i class="fal fa-star-half"></i> '.$ims->setting["user"]["my_comment_meta_title"],
// 		// 	'link' => $ims->site_func->get_link("user",$ims->setting["user"]["my_comment_link"])
// 		// ),
// 		// 'my_question' => array(
// 		// 	'title' => '<i class="fal fa-question-square"></i> '.$ims->setting["user"]["my_question_meta_title"],
// 		// 	'link' => $ims->site_func->get_link("user",$ims->setting["user"]["my_question_link"])
// 		// ),
// 		// 'promotion' => array(
// 		// 	'title' => $ims->setting["user"]["promotion_meta_title"],
// 		// 	'link' => $ims->site_func->get_link("user",$ims->setting["user"]["promotion_link"])
// 		// ),
// 		// 'voucher' => array(
// 		// 	'title' => $ims->setting["user"]["voucher_meta_title"],
// 		// 	'link' => $ims->site_func->get_link("user",$ims->setting["user"]["voucher_link"])
// 		// ),
// 		// 'api' => array(
// 		// 	'title' => '<i class="fal fa-dumpster"></i> '.$ims->setting["user"]["api_meta_title"],
// 		// 	'link' => $ims->site_func->get_link("user",$ims->setting["user"]["api_link"])
// 		// ),
// 		// 'api_product' => array(
// 		// 	'title' => '<i class="fal fa-dumpster"></i> '.$ims->setting["user"]["api_product_meta_title"],
// 		// 	'link' => $ims->site_func->get_link("user",$ims->setting["user"]["api_product_link"])
// 		// ),
// 		// 'api_ordering' => array(
// 		// 	'title' => '<i class="fal fa-dumpster"></i> '.$ims->setting["user"]["api_ordering_meta_title"],
// 		// 	'link' => $ims->site_func->get_link("user",$ims->setting["user"]["api_ordering_link"])
// 		// ),	
// 		'signout' => array(
// 			'title' => '<i class="icon-user"><img src="'.$src.'user-sign-out.png"/></i> '.$ims->lang['user']['signout'],
// 			'link' => "javascript:void(0)",
// 			'attr_link' => "onclick=\"imsUser.signout('')\""
// 		)
// 	);
// 	if ($ims->data['user_cur']['is_affiliates'] == 1){
//         unset($arr_is_login['deeplink_account']);
//     }else{
//         unset($arr_is_login['deeplink']);
//         unset($arr_is_login['deeplink_statistics']);
//     }
// 	$menu_sub = '';
// 	$i = 0;
// 	$num = count($arr_is_login);
// 	foreach($arr_is_login as $key => $row) {
// 		$i++;
// 		$arr_class_li = array();
// 		if($i == 1) {
// 			$arr_class_li[] = 'first';
// 		}
// 		if($i == $num) {
// 			$arr_class_li[] = 'last';
// 		}
// 		$row['class_li'] = (count($arr_class_li) > 0) ? ' class="'.implode(' ',$arr_class_li).'"' : '';
// 		$row['class'] = ($key == $cur) ? ' class="current"' : '';
// 		$row['menu_sub'] = '';

// 		$ims->temp_box->assign('row', $row);
// 		$ims->temp_box->parse("box_menu_user.menu_sub.row");
// 		$menu_sub .= $ims->temp_box->text("box_menu_user.menu_sub.row");
// 		$ims->temp_box->reset("box_menu_user.menu_sub.row");
// 	}		
// 	$group_id = 7;
// 	$arr_hd = $ims->db->load_row_arr('page', 'is_show=1 and lang ="'.$ims->conf['lang_cur'].'" and group_id="'.$group_id.'" order by show_order DESC, date_create ASC ');	
// 	if (!empty($arr_hd)) {
// 		foreach ($arr_hd as $k => $hd) {
// 			$hd['link'] = $ims->site->get_link('page', $hd['friendly_link']);
// 			$ims->temp_box->assign('hd', $hd);
// 			$ims->temp_box->parse("box_menu_user.row_hd");
// 		}
// 	}
// 	$data['user_manager'] = $ims->lang['user']['user_manager'];
// 	$data['guide_title'] = $ims->lang['user']['guide'];
// 	$data['picture'] = $ims->conf['rooturl'].'resources/images/user/default-avatar.png';
// 	if($ims->data['user_cur']['picture']){
// 		$data['picture'] = $ims->func->get_src_mod($ims->data['user_cur']['picture']);
// 	}
// 	$data['full_name'] = $ims->data['user_cur']['full_name'];
// 	if($ims->conf['cur_mod'] != 'user'){
// 	    $data['box_other'] = 'class="box_other"';
//     }
// 	$ims->temp_box->reset("box_menu_user.menu_sub");
// 	$ims->temp_box->assign('data', array('content' => $menu_sub));
// 	$ims->temp_box->parse("box_menu_user.menu_sub");
	
// 	$ims->temp_box->assign('data', $data);
// 	$ims->temp_box->assign('LANG', $ims->lang);
// 	$ims->temp_box->parse("box_menu_user");
// 	$output = $ims->temp_box->text("box_menu_user");
	
// 	return $output;
// }

function box_menu_checkin($cur = ''){
	global $ims;
	$output = '';
	$data = array();

	$src = $ims->conf['rooturl'].'resources/images/user/';
	$active = '';
	$arr_is_login = array(
		'user' => array(
			'icon' => '<i class="icon-user"><img src="'.$src.'user.svg"/></i>',
			'icon_active' => '<i class="icon-user-active"><img src="'.$src.'user-active.svg"/></i>',
			'title' => $ims->setting["user"]["user_meta_title"],
			'link' => $ims->site_func->get_link("user"),
		),	
		'event' => array(
			'icon' => '<i class="icon-user"><img src="'.$src.'event.svg"/></i>',
			'icon_active' => '<i class="icon-user-active"><img src="'.$src.'event-active.svg"/></i>',
			'title' => $ims->setting["user"]["event_meta_title"],
			'link' => $ims->site_func->get_link("user", $ims->setting['user']['event_link']),
		),
		'statistic' => array(
			'icon' => '<i class="icon-user"><img src="'.$src.'statistic.svg"/></i>',
			'icon_active' => '<i class="icon-user-active"><img src="'.$src.'statistic-active.svg"/></i>',
			'title' => $ims->setting["user"]["statistic_meta_title"],
			'link' => $ims->site_func->get_link("user", $ims->setting['user']['statistic_link']),
		),		
		'store' => array(
			'icon' => '<i class="icon-user"><img src="'.$src.'store.svg"/></i>',
			'icon_active' => '<i class="icon-user-active"><img src="'.$src.'store-active.svg"/></i>',
			'title' => $ims->setting["user"]["store_meta_title"],
			'link' => $ims->site_func->get_link("user", $ims->setting['user']['store_link']),
		),
		'image' => array(
			'icon' => '<i class="icon-user"><img src="'.$src.'image.svg"/></i>',
			'icon_active' => '<i class="icon-user-active"><img src="'.$src.'image-active.svg"/></i>',
			'title' => $ims->setting["user"]["image_meta_title"],
			'link' => $ims->site_func->get_link("user", $ims->setting['user']['image_link']),
		),
	);

	$data['content'] = '';
//	$i = 0;
	foreach($arr_is_login as $key => $row) {
//		$i++;
		$arr_class_li = array();
//		if($i == 1) {
//			$arr_class_li[] = 'first';
//		}
//		if($i == $num) {
//			$arr_class_li[] = 'last';
//		}
		$row['class_li'] = (count($arr_class_li) > 0) ? ' class="'.implode(' ',$arr_class_li).'"' : '';
		$row['class'] = ($key == $cur) ? ' class="current"' : '';
		$row['menu_sub'] = '';

		$ims->temp_box->assign('row', $row);
		$ims->temp_box->parse("box_menu_user_checkin.row");
		$data['content'] .= $ims->temp_box->text("box_menu_user_checkin.row");
		$ims->temp_box->reset("box_menu_user_checkin.row");
	}

	$ims->temp_box->assign('data', $data);
	$ims->temp_box->assign('LANG', $ims->lang);
	$ims->temp_box->parse("box_menu_user_checkin");
	$output = $ims->temp_box->text("box_menu_user_checkin");
	
	return $output;
}

function check_notification(){
	global $ims;
	$count = 0;
	if ($ims->site_func->checkUserLogin() == 1) {
		$sql = "SELECT * FROM user_notification WHERE is_show = 1 AND lang ='" . $ims->conf['lang_cur'] . "' and (type=0 OR find_in_set('".$ims->data['user_cur']['user_id']."', user_id)) and date_create>='".$ims->data["user_cur"]["date_create"]."'";
	    $query = $ims->db->query($sql);
	    $num_spam = $ims->db->num_rows($query);
	    if ($num_spam > 0) {
	        while ($row = $ims->db->fetch_row($query)) {
				if (!empty($row['is_view'])) {
	                $row['is_view'] = explode(",", $row['is_view']);
	                if (in_array($ims->data['user_cur']['user_id'], $row['is_view'])) {
	                } else {
						$count++;
	                }
	            } else {
					$count++;
	            }
	        }
	    }
	}else{
		$count = 0;
	}
	$output = '<span class="noti-count">'.$count.'</span>';
	return $output;
}

function check_promotion_code(){
	global $ims;
	$count = 0;
	if ($ims->site_func->checkUserLogin() == 1) {		
		$sql = "SELECT * FROM promotion WHERE is_show = 1 AND (type_promotion = 'apply_all'
			OR (type_promotion = 'apply_user' AND FIND_IN_SET('".$ims->data['user_cur']['user_id']."', list_user))
			OR (type_promotion = 'apply_email' AND FIND_IN_SET('".$ims->data['user_cur']['email']."', list_email))
			OR type_promotion = 'apply_product'
			OR type_promotion = 'apply_freeship') AND num_use < max_use AND date_end > '".time()."' ";
	    $query = $ims->db->query($sql);
	    $num_spam = $ims->db->num_rows($query);
	    if ($num_spam > 0) {
	        while ($row = $ims->db->fetch_row($query)) {
	        	if($row['type_promotion'] == 'apply_user' || $row['type_promotion'] == 'apply_email'){
					$row['check'] = $ims->db->do_get_num('promotion_log','is_show=1 and promotion_id="'.$row['promotion_id'].'" and user_id="'.$ims->data['user_cur']['user_id'].'"');
					if (empty($row['check'])) {
						$count++;
	                }
				}else{
					$count++;
				}
	        }
	    }
	}else{
		$count = 0;
	}
	$output = '<span class="noti-count">'.$count.'</span>';
	return $output;
}
//=================box_column===============
function box_left ($action)
{
	global $ims;
	
	$output = '';
	// $output = box_menu ($action);
	$output = box_menu_checkin ($action);
	//$output = $ims->site->block_left ();
	
	return $output;
}

//=================box_column===============
function box_column ()
{
	global $ims;
	
	$output = $ims->site->block_column ();
	
	return $output;
}

/*==============================SHOPPING==============================*/
function list_quantity ($select_name,$cur="", $ext="",$arr_more=array())
{
	global $ims;
	
	return $ims->site->list_number ($select_name, 1, 100, $cur, $ext,$arr_more);
}

function get_name_location($table = '', $id= ''){
	global $ims;
	$output = '';

	$sql = "select title from ".$table."
	      where is_show=1 
	      and lang='".$ims->conf['lang_cur']."' and code = '".$id."'";
	// echo $sql;
	$result = $ims->db->query($sql);
	$arr = $ims->db->fetch_row($result);
	$output = isset($arr['title']) ? $arr['title'] : '';
	return $output;
}

?>