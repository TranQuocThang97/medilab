<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "user";
	var $action  = "checkin";
	var $sub 	 = "manage";
	var $template = "checkin";
	
	/**
		* Khởi tạo
		* Quản lý sự kiện
	**/
	function __construct() {
        global $ims;
        $ims->func->load_language('user');
        //info test
        $arr_in = array(        	
        	'detail_id' => '1',
        	'order_id' => '1',
        	'event_id' => '17',
        );
        $link_test = $ims->func->encrypt_decrypt('encrypt', json_encode($arr_in), 'qrcode', 'QRCode');	
        // print_arr($link_test);
        //info test 

        $inputData = array();
        $returnData = array(
        	'ok' => 0,
        );
        $str = $ims->conf['cur_act_url'];
        $event = !empty($ims->input['event'])?$ims->input['event']:0;
        if(!empty($str) && !empty($event)){
        	$arr = $ims->func->encrypt_decrypt('decrypt', $str, 'qrcode', 'QRCode');
        	$inputData = json_decode($arr, true);        	
	        $inputData['detail_id'] = trim($inputData['detail_id']);
	        $inputData['event_id'] = trim($inputData['event_id']);

	        if($inputData['event_id'] != $event){
	        	$returnData['mess'] = $ims->lang['user']['event_notvalid_mes'];
	        	echo json_encode($returnData); die;
	        }

	        $order = $ims->db->load_row('event_order','is_show=1 and order_id="'.$inputData['order_id'].'"');
	        if(!$order){
	        	$returnData['mess'] = $ims->lang['user']['ticket_notvalid_mes'];
	        	echo json_encode($returnData); die;
	        }

	        $cancel = $ims->db->load_row('product_order_status','is_show=1 and is_cancel=1');
	        if($order['is_cancel'] == 1 || $order['is_status'] == $cancel['item_id']){
	        	$returnData['mess'] = $ims->lang['user']['ticket_cancel_mes'];
	        	echo json_encode($returnData); die;
	        }

	        $event = $ims->db->load_row('event','is_show=1 and item_id="'.$inputData['event_id'].'"');
	        if(!$event){
	        	$returnData['mess'] = $ims->lang['user']['event_notvalid_mes'];
	        	echo json_encode($returnData); die;
	        }

	        if($event['date_begin'] > time()){
	        	$returnData['mess'] = $ims->lang['user']['event_notopen_mes'];
	        	echo json_encode($returnData); die;	
	        }

	        if($event['date_end'] < time()){
	        	$returnData['mess'] = $ims->lang['user']['event_end_mes'];
	        	echo json_encode($returnData); die;	
	        }

	        $ticket = $ims->db->load_row('event_order_detail','detail_id="'.$inputData['detail_id'].'" and event_id="'.$inputData['event_id'].'"');
	        if(!$ticket){
	        	$returnData['mess'] = $ims->lang['user']['ticket_notvalid_mes'];
	        	echo json_encode($returnData); die;
	        }

	        if($ticket['is_checkin'] == 0){
	        	$arr_up = array();
	        	$arr_up['is_checkin'] = 1;
	        	$arr_up['date_checkin'] = time();
	        	$ok = $ims->db->do_update('event_order_detail', $arr_up, ' detail_id = "'.$inputData['detail_id'].'" ');
	        	if($ok){
	        		$returnData['ok'] = 1;
	        		$returnData['mess'] = $ims->lang['user']['ticket_checkin_mes'];
	        		$returnData['full_name'] = $ticket['full_name'];
		        	$returnData['email'] = $ticket['email'];
		        	$returnData['phone'] = $ticket['phone'];
		        	$returnData['age'] = $ticket['age'];
	        		echo json_encode($returnData); die;
	        	}
	        }
	        if($ticket['is_checkin'] == 1){
	        	$returnData['ok'] = 1;
        		$returnData['mess'] = $ims->lang['user']['ticket_checkedin_mes'];
	        	$returnData['full_name'] = $ticket['full_name'];
	        	$returnData['email'] = $ticket['email'];
	        	$returnData['phone'] = $ticket['phone'];
	        	$returnData['age'] = $ticket['age'];
	        }	        
        }

        //Trả lại VNPAY theo định dạng JSON
        echo json_encode($returnData);die;
    }
	
  	// End class
}
?>