<?php

if (!defined('IN_ims')) {
    die('Access denied');
}
$nts = new sMain();

class sMain {

    var $modules = "product";
    var $action = "vnpayipn";
    var $sub = "manage";
    var $check_promotion = 0;

    function __construct() {
        global $ims;
        $method = $ims->db->load_row('order_method','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and name_action="vnpay"');
        $vnpay = $ims->func->unserialize($method['arr_option']);

        $table = 'product_order';

        $vnp_HashSecret = $vnpay['SecretKey']; //Chuỗi bí mật

        $inputData = array();
        $returnData = array();
        $data = $_REQUEST;

        foreach ($data as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        if(substr($inputData['vnp_TxnRef'], 0, 2) == 'TK'){
            $table = 'event_order';
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHashType']);
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);      
        $orderId = $inputData['vnp_TxnRef'];        

        try {
            //Check Orderid    
            //Kiểm tra checksum của dữ liệu
            if ($secureHash == $vnp_SecureHash) {
                //Lấy thông tin đơn hàng lưu trong Database và kiểm tra trạng thái của đơn hàng, mã đơn hàng là: $orderId            
                //Việc kiểm tra trạng thái của đơn hàng giúp hệ thống không xử lý trùng lặp, xử lý nhiều lần một giao dịch
                //Giả sử: $order = mysqli_fetch_assoc($result);
                $order = $ims->db->load_row($table, "order_code='".$orderId."'");
                if ($order != NULL) {
                    if ($order["is_status_payment"] != NULL && $order["is_status_payment"] == 0) {
                        $arr_payment = array(
                            'is_show' => 1,
                            'is_status_payment' => 1,
//                            'is_ConfirmOrder' => 1,
//                            'is_ConfirmPayment' => 1,
                            'transaction_id' => $ims->get['vnp_TransactionNo'],
                        );
                        $ok = $ims->db->do_update($table, $arr_payment,' order_code ="'.$orderId.'" ');
                        $returnData['RspCode'] = '00';
                        $returnData['Message'] = 'Confirm Success';
                    } elseif ($order["is_status_payment"] != NULL && $order["is_status_payment"] == 2) {
                        $arr_payment = array(
                            'is_show' => 0,
                            'is_ConfirmOrder' => 0,
                            'is_ConfirmPayment' => 0,
                            'transaction_id' => $ims->get['vnp_TransactionNo'],
                        );
                        $ok = $ims->db->do_update($table, $arr_payment,' order_code ="'.$orderId.'" ');
                        $returnData['RspCode'] = '00';
                        $returnData['Message'] = 'Confirm Cancel';
                    } else {
                        $returnData['RspCode'] = '02';
                        $returnData['Message'] = 'Order already confirmed';
                    }
                    if (($inputData['vnp_Amount'] / 100) != $order['total_payment']) {
                        $returnData['RspCode'] = '04';
                        $returnData['Message'] = 'Invalid amount';
                    }
                } else {
                    $returnData['RspCode'] = '01';
                    $returnData['Message'] = 'Order not found';
                }
            } else {
                $returnData['RspCode'] = '97';
                $returnData['Message'] = 'Chu ky khong hop le';
            }
        } catch (Exception $e) {
            $returnData['RspCode'] = '99';
            $returnData['Message'] = 'Unknow error';
        }
        //Trả lại VNPAY theo định dạng JSON
        echo json_encode($returnData);die;
    }
    // end class
}

?>