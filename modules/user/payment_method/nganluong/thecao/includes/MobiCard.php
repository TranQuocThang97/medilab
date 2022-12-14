<?php 
  class Result {
	 var $error_code = "";
	 var $merchant_id = "";
	 var $merchant_account = "";				
	 var $pin_card = "";
	 var $card_serial = "";
	 var $type_card = "";
	 var $order_id = "";
	 var $client_fullname = "";
	 var $client_email = "";
	 var $client_mobile = "";
	 var $card_amount = "";
	 var $amount = "";
	 var $transaction_id = "";
	 var $error_message = "";
  } 
  
  class MobiCard{
	
	
    function CardPay($pin_card,$card_serial,$type_card,$_order_id,$client_fullname,$client_mobile,$client_email){
		 $params = array(
				'func'					=> Config::$_FUNCTION,
				'version'				=> Config::$_VERSION,
				'merchant_id'			=> Config::$_MERCHANT_ID,
				'merchant_account'		=> Config::$_EMAIL_RECEIVE_MONEY,
				'merchant_password'		=> MD5(Config::$_MERCHANT_ID.'|'.Config::$_MERCHANT_PASSWORD),
				'pin_card'				=> $pin_card,
				'card_serial'			=> $card_serial,
				'type_card'				=> $type_card,
				
				'ref_code'				=> $_order_id,
				'client_fullname'		=> $client_fullname,
				'client_email'			=> $client_email,
				'client_mobile'			=> $client_mobile,
			);					
			$post_field = '';
			foreach ($params as $key => $value){
				if ($post_field != '') $post_field .= '&';
				$post_field .= $key."=".$value;
			}
			
			$api_url = NGANLUONG_URL_CARD_POST;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$api_url);
		curl_setopt($ch, CURLOPT_ENCODING , 'UTF-8');
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field);
		$result = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
		$error = curl_error($ch);
		
		//print_r($result);
		//die();
		$kq = new Result();
		
		if ($result != '' && $status==200){
			$arr_result = explode("|",$result);
			if (count($arr_result) == 13) {
			   $kq->error_code	     = $arr_result[0];
			   $kq->merchant_id	     = $arr_result[1];
			   $kq->merchant_account = $arr_result[2];				
			   $kq->pin_card	         = $arr_result[3];
				$kq->card_serial     = $arr_result[4];
				$kq->type_card	     = $arr_result[5];
				$kq->order_id		 = $arr_result[6];
				$kq->client_fullname = $arr_result[7];
				$kq->client_email    = $arr_result[8];
				$kq->client_mobile   = $arr_result[9];
				$kq->card_amount     = $arr_result[10];
				$kq->amount			 = $arr_result[11];
				$kq->transaction_id	 = $arr_result[12];
				
				if ($kq->error_code == '00'){
				   $kq->error_message ="N???p th??? th??nh c??ng, m???nh gi?? th??? = ".$kq->card_amount;
				}
				else {
				   $kq->error_message = $this->GetErrorMessage($kq->error_code);
				}
			}
			
		}
		else $kq->error_message = $error;	
		
		return $kq;
	}
	
	function GetErrorMessage($error_code) {
		$arrCode = array(
		   '00'=>  'Giao d???ch th??nh c??ng',
		   '99'=>  'L???i, tuy nhi??n l???i ch??a ???????c ?????nh ngh??a ho???c ch??a x??c ?????nh ???????c nguy??n nh??n',
		   '01'=>  'L???i, ?????a ch??? IP truy c???p API c???a Ng??nL?????ng.vn b??? t??? ch???i',
		   '02'=>  'L???i, tham s??? g???i t??? merchant t???i Ng??nL?????ng.vn ch??a ch??nh x??c (th?????ng sai t??n tham s??? ho???c thi???u tham s???)',
		   '03'=>  'L???i, M?? merchant kh??ng t???n t???i ho???c merchant ??ang b??? kh??a k???t n???i t???i Ng??nL?????ng.vn',
		   '04'=>  'L???i, M?? checksum kh??ng ch??nh x??c (l???i n??y th?????ng x???y ra khi m???t kh???u giao ti???p gi???a merchant v?? Ng??nL?????ng.vn kh??ng ch??nh x??c, ho???c c??ch s???p x???p c??c tham s??? trong bi???n params kh??ng ????ng)',
		   '05'=>  'T??i kho???n nh???n ti???n n???p c???a merchant kh??ng t???n t???i',
		   '06'=>  'T??i kho???n nh???n ti???n n???p c???a merchant ??ang b??? kh??a ho???c b??? phong t???a, kh??ng th??? th???c hi???n ???????c giao d???ch n???p ti???n',
		   '07'=>  'Th??? ???? ???????c s??? d???ng ',
		   '08'=>  'Th??? b??? kh??a',
		   '09'=>  'Th??? h???t h???n s??? d???ng',
		   '10'=>  'Th??? ch??a ???????c k??ch ho???t ho???c kh??ng t???n t???i',
		   '11'=>  'M?? th??? sai ?????nh d???ng',
		   '12'=>  'Sai s??? serial c???a th???',
		   '13'=>  'M?? th??? v?? s??? serial kh??ng kh???p',
		   '14'=>  'Th??? kh??ng t???n t???i',
		   '15'=>  'Th??? kh??ng s??? d???ng ???????c',
		   '16'=>  'S??? l???n th??? (nh???p sai li??n ti???p) c???a th??? v?????t qu?? gi???i h???n cho ph??p',
		   '17'=>  'H??? th???ng Telco b??? l???i ho???c qu?? t???i, th??? ch??a b??? tr???',
		   '18'=>  'H??? th???ng Telco b??? l???i ho???c qu?? t???i, th??? c?? th??? b??? tr???, c???n ph???i h???p v???i Ng??nL?????ng.vn ????? tra so??t',
		   '19'=>  'K???t n???i t??? Ng??nL?????ng.vn t???i h??? th???ng Telco b??? l???i, th??? ch??a b??? tr??? (th?????ng do l???i k???t n???i gi???a Ng??nL?????ng.vn v???i Telco, v?? d??? sai tham s??? k???t n???i, m?? kh??ng li??n quan ?????n merchant)',
		   '20'=>  'K???t n???i t???i telco th??nh c??ng, th??? b??? tr??? nh??ng ch??a c???ng ti???n tr??n Ng??nL?????ng.vn');
		   
		   return $arrCode[$error_code];
	}
	
	function CardPayWebservice($pin_card,$card_serial,$type_card,$ref_code,$client_fullname,$client_mobile,$client_email){
							
			 $strparams = $pin_card.'|'. $type_card .'|'. $ref_code .'|'. Config::$_EMAIL_RECEIVE_MONEY .'|'. $client_fullname .'|'. $client_email .'|'. $client_mobile .'|'.$card_serial ;
						
			 $params = array(						
					'merchant_id'			=> Config::$_MERCHANT_ID,						
					'checksum'		=> MD5($strparams .'|'.Config::$_MERCHANT_PASSWORD),
					'params'		=> $strparams
				);
				
				$webservice = NGANLUONG_URL_CARD_SOAP ; //"https://nganluong.vn/mobile_card_api.php?wsdl";
				$client	= new nusoap_client($webservice, true);
				$result = $client->call('CardCharge_v2', $params);
			
				$kq = new Result();
				if ($result != ''){
					$arr_result = explode("|",$result);	
				
					if (count($arr_result) == 12) {
						$kq->error_code	     = $arr_result[0];
						$kq->merchant_id	 = $arr_result[1];
						$kq->transaction_id  = $arr_result[2];				
						$kq->amount	      	 = $arr_result[3];
						$kq->pin_card	     = $arr_result[4];
						$kq->type_card	     = $arr_result[5];
						$kq->ref_code     	 = $arr_result[6];
						$kq->merchant_account = $arr_result[7];
						$kq->client_fullname = $arr_result[8];
						$kq->client_email    = $arr_result[9];
						$kq->client_mobile   = $arr_result[10];
						$kq->card_amount     = $arr_result[11];
						
						if ($kq->error_code == '00'){
						   $kq->error_message ="N???p th??? th??nh c??ng, m???nh gi?? th??? = ".$kq->card_amount;
						}
						else {
						   $kq->error_message = $this->GetErrorMessageV2($kq->error_code);
						}
					}
					
				}else $kq->error_message = 'L???i g???i webservice';	
		
				return $kq;
				
		}
		
	function GetErrorMessageV2($error_code) {
			$arrCode = array(
			   '00' => 'Th??nh c??ng',
				'01' => 'L???i ch??a x??c minh',
				'05' => 'M?? th??? n???p kh??ng ????ng ho???c ???? ???????c s??? d???ng',
				'06' => 'L???i k???t n???i v???i h??? th???ng x??c th???c th???',
				'07' => 'T??i kho???n nh???n ti???n n???p kh??ng t???n t???i',
				'08' => 'T??i kho???n truy c???p h??? th???ng n???p th??? t???m th???i b??? kh??a',
				'09' => 'Kh??ch h??ng ??ang n???p th??? b??? kh??a (do nh???p sai m?? th??? li??n ti???p)',
				'10' => 'Kh??ng n???p ???????c ti???n v??o t??i kho???n Ng??nL?????ng.vn',
				'11' => 'H??? th???ng Ng??nL?????ng.vn kh??ng sinh ???????c phi???u thu',
				'12' => 'Phi???u thu t???i Ng??nL?????ng.vn kh??ng c???p nh???t ???????c tr???ng th??i ???? thu ti???n',
				'13' => 'Kh??ng chuy???n ti???n ???????c v??o t??i kho???n Ng??nL?????ng.vn c???a ng?????i nh???n',
				);
			   return $arrCode[$error_code];
		}
		
	function getParam($param_name){
				$result = '';
				if (!empty($_POST[$param_name]))		
					$result = trim($_POST[$param_name]);
				return $result;
		}
			
}
?>