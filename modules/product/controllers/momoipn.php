<?php

/* ================================================================================*\
  Name code : view.php
  Copyright © 2013 by Tran Thanh Hiep
  @version : 1.0
  @date upgrade : 03/02/2013 by Tran Thanh Hiep
  \*================================================================================ */

if (!defined('IN_ims')) {
    die('Access denied');
}
$nts = new sMain();

class sMain {

    var $modules = "product";
    var $action = "momoipn";
    var $sub = "manage";
    var $check_promotion = 0;

    /**
     * function __construct ()
     * Khoi tao 
     * */
    function __construct() {
        global $ims;
        $ims->site_func->setting('product');
        header("content-type: application/json; charset=UTF-8");
        http_response_code(200); //200 - Everything will be 200 Oke
        if (!empty($ims->post)) {
            $response = array();
            try {
                $momo = $ims->db->load_item('order_method','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and name_action="momo"','arr_option');
                $momo = $ims->func->unserialize($momo);
                $partnerCode = $momo['PartnerCode'];
                $accessKey   = $momo['AccessKey'];
                $secretKey   = $momo['SecretKey'];                
                // $partnerCode = $ims->func->if_isset($ims->post["partnerCode"]);
                // $accessKey = $ims->func->if_isset($ims->post["accessKey"]);
                // $serectkey = $ims->func->if_isset($ims->setting['product']['momo_secretKey']);
                $orderId = $ims->func->if_isset($ims->post["orderId"]);
                $localMessage = $ims->func->if_isset($ims->post["localMessage"]);
                $message = $ims->func->if_isset($ims->post["message"]);
                $transId = $ims->func->if_isset($ims->post["transId"]);
                $orderInfo = $ims->func->if_isset($ims->post["orderInfo"]);
                $amount = $ims->func->if_isset($ims->post["amount"]);
                $errorCode = $ims->func->if_isset($ims->post["errorCode"]);
                $responseTime = $ims->func->if_isset($ims->post["responseTime"]);
                $requestId = $ims->func->if_isset($ims->post["requestId"]);
                $extraData = $ims->func->if_isset($ims->post["extraData"]);
                $payType = $ims->func->if_isset($ims->post["payType"]);
                $orderType = $ims->func->if_isset($ims->post["orderType"]);
                $extraData = $ims->func->if_isset($ims->post["extraData"]);
                $m2signature = $ims->func->if_isset($ims->post["signature"]); //MoMo signature

                //app
                $amount = $ims->func->if_isset($ims->post["amount"],0);
                $partnerRefId = $ims->func->if_isset($ims->post["partnerRefId"]);
                $partnerTransId = $ims->func->if_isset($ims->post["partnerTransId"]);
                $transType = $ims->func->if_isset($ims->post["transType"]);
                $momoTransId = $ims->func->if_isset($ims->post["momoTransId"]);
                $status = $ims->func->if_isset($ims->post["status"]);
                $message = $ims->func->if_isset($ims->post["message"]);
                $responseTime = $ims->func->if_isset($ims->post["responseTime"]);
                $storeId = $ims->func->if_isset($ims->post["storeId"]);
                $signature = $ims->func->if_isset($ims->post["signature"]);


                //Checksum
                $rawHash = "partnerCode=" . $partnerCode . "&accessKey=" . $accessKey . "&requestId=" . $requestId . "&amount=" . $amount . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo .
                    "&orderType=" . $orderType . "&transId=" . $transId . "&message=" . $message . "&localMessage=" . $localMessage . "&responseTime=" . $responseTime . "&errorCode=" . $errorCode .
                    "&payType=" . $payType . "&extraData=" . $extraData;

                $partnerSignature = hash_hmac("sha256", $rawHash, $serectkey);

                if ($m2signature == $partnerSignature) {
                    if ($errorCode == '0') {
                        $result = 'Capture Payment Success';
                        $arr_payment = array(
                            'is_show' => 1,
                            'is_payment' => 1,
                            'is_status_payment' => 3,
                            'transaction_id' => $transId,
                        );
                        $ok = $ims->db->do_update('product_order', $arr_payment,' order_code ="'.$orderId.'" ');
                    } else {
                        $arr_payment = array(
                            'is_show' => 0,
                            'is_ConfirmOrder' => 0,
                            'is_ConfirmPayment' => 0,
                            'transaction_id' => $transId,
                        );
                        $ok = $ims->db->do_update('product_order', $arr_payment,' order_code ="'.$orderId.'" ');
                        $result = $message;
                    }
                } else {
                    $result = 'This transaction could be hacked, please check your signature and returned signature';
                }

            } catch (Exception $e) {
                echo $response['message'] = $e;
            }

            $debugger = array();
            $debugger['rawData'] = $rawHash;
            $debugger['momoSignature'] = $m2signature;
            $debugger['partnerSignature'] = $partnerSignature;

            if ($m2signature == $partnerSignature) {
                $response['message'] = "Received payment result success";
            } else {
                $response['message'] = "ERROR! Fail checksum";
            }
            $response['debugger'] = $debugger;

            if(!empty($partnerRefId) && !empty($momoTransId)){
                if($status == 0){
                    $arr_payment = array(
                        'transaction_id' => $momoTransId,
                    );
                    $ok = $ims->db->do_update('product_order', $arr_payment,' order_code ="'.$partnerRefId.'" ');
                }
                $response = array(
                    "status" => $status,
                    "message" => $message,
                    "amount" => $amount,
                    "partnerRefId" => $partnerRefId,
                    "momoTransId" => $momoTransId,
                    "signature" => $signature,
                );
            }
            echo json_encode($response);
            die;
        }
    }
    // end class
}

?> 