<?php	

	// define('NGANLUONG_URL_CARD_POST', 'https://www.nganluong.vn/mobile_card.api.post.v2.php');
	define('NGANLUONG_URL_CARD_POST', 'https://sandbox.nganluong.vn:8088/nl30/mobile_card.api.post.v2.php');
	define('NGANLUONG_URL_CARD_SOAP', 'https://nganluong.vn/mobile_card_api.php?wsdl');
	class Config
	{
	  	public static $_FUNCTION = "CardCharge";
	  	public static $_VERSION = "2.0";
	  	//Thay đổi 3 thông tin ở phía dưới
    	public static $_MERCHANT_ID = "46135";
    	public static $_MERCHANT_PASSWORD = "d3a7383c56f91aaf8ecce556bb792313";
    	public static $_EMAIL_RECEIVE_MONEY = "vsard123@gmail.com";
	}
	
?>


	