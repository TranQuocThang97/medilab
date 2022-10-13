<?php 
session_start();
require('config.php');
require('Lib/Alepay.php');
if(isset($_POST['sendInstallment'])){
	$alepay = new Alepay($config);
	$data = array();


	parse_str(file_get_contents('php://input'), $params); // Lấy thông tin dữ liệu bắn vào
	//var_dump($params);


	$data['cancelUrl'] = URL_DEMO;
	$data['amount'] = intval(preg_replace('@\D+@', '', $params['amount']));
	$data['orderCode'] = date('dmY') . '_' . uniqid();
	$data['currency'] = $params['currency'];
	$data['orderDescription'] = $params['orderDescription'];
	$data['totalItem'] = intval($params['totalItem']);
	$data['checkoutType'] = 3; // Thanh toán trả góp + nội địa + quốc tế
	$data['buyerName'] = trim($params['buyerName']);
	$data['buyerEmail'] = trim($params['buyerEmail']);
	$data['buyerPhone'] = trim($params['phoneNumber']);
	$data['buyerAddress'] = trim($params['buyerAddress']);
	$data['buyerCity'] = trim($params['buyerCity']);
	$data['buyerCountry'] = trim($params['buyerCountry']);
	$data['month'] = 3;
	$data['paymentHours'] = 48; //48 tiếng :  Thời gian cho phép thanh toán (tính bằng giờ)
	

	foreach ($data as $k => $v) {
		if (empty($v)) {
			$alepay->return_json("NOK", "Bắt buộc phải nhập/chọn tham số [ " . $k . " ]");
			die();
		}
	}
	$data['allowDomestic'] = true;	


	$result = $alepay->sendOrderToAlepay($data); // Khởi tạo

	if (isset($result) && !empty($result->checkoutUrl)) {
		//$alepay->return_json('OK', 'Thành công', $result->checkoutUrl);
		echo '<meta http-equiv="refresh" content="0;url=' . $result->checkoutUrl. '">';
	} else {
		echo $result->errorDescription;
	}
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <title>Nhập Thông Tin</title>
        <style type="text/css">
            .require {
                color: red;
            }
            .form-group.col-sm-5{margin: 5px;}
        </style>
    </head>
    <body>
        <?php
        require_once 'config.php';
        ?>
        <div id="container" class="container">
            <div class="col-sm-5">
                <div class="col-sm-12">

                    <h2 id="titleItem">Điện thoại iphone 6 16GB</h2><br>

                    <img src="http://multimedia.bbycastatic.ca/multimedia/products/500x500/105/10528/10528315.jpg" width="250"  alt="Iporn 6"><br>

                    <p><b>16.000.000₫ (Còn hàng) / 1 Chiếc</b></p><br>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>Màn hình</td>
                                <td>LED-backlit IPS LCD, 7.9 inch</td>
                            </tr>
                            <tr>
                                <td>Hệ điều hành</td>
                                <td>iOS 8</td>
                            </tr>
                            <tr>
                                <td>Vi xử lí CPU</td>
                                <td>Dual - Core, 1 GHz</td>
                            </tr>
                            <tr>
                                <td>RAM</td>
                                <td>512 MB</td>
                            </tr>
                            <tr>
                                <td>Bộ nhớ trong</td>
                                <td>64 GB, 32 GB, 16 GB</td>
                            </tr>
                            <tr>
                                <td>Camera</td>
                                <td>5 MP(2592 x 1944 pixels)</td>
                            </tr>
                            <tr>
                                <td>Kết nối</td>
                                <td>Không 3G, Wifi chuẩn 802.11 a/b/g/n</td>
                            </tr>
                            <tr>
                                <td>Đàm thoại</td>
                                <td>Face Time</td>
                            </tr>
                            <tr>
                                <td>Dung lượng pin</td>
                                <td>4490mAh(16.3Wh)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-sm-7">
                <h2>Thông tin khách hàng</h2>
                <form class="form-" role="form" method="POST" id="formSubmit"  action="">
                    <div class="form-group col-sm-5">

                        <label class="control-label">Tổng Tiền <span class="require">(*)</span></label>

                        <input type="text" class="form-control"name="amount" id="amount" required >

                    </div>
                    <div class="form-group col-sm-5">     
                        <label class="control-label">Số Lượng <span class="require">(*)</span></label>

                        <input type="text" placeholder="Số Lượng" class="form-control"
                               name="totalItem"
                               id="totalItem" required value="1">

                    </div>

                    <!-- Text input-->
                    <div class="form-group col-sm-5">
                        <label class="control-label">Tiền Tệ <span
                                class="require">(*)</span></label>

                        <select name="currency" id="currency" class="form-control">
                            <option value="VND" selected>VND</option>
                            <option value="USD">USD</option>
                        </select>

                    </div>
                    <div class="form-group col-sm-5">     
                        <label class="control-label">Email <span
                                class="require">(*)</span></label>

                        <input type="text" placeholder="Email" class="form-control"
                               name="buyerEmail"
                               id="buyerEmail" required>

                    </div>
                    <!-- Text input-->
                    <div class="form-group col-sm-5">
                        <label class="control-label">Họ Tên <span  class="require">(*)</span></label>

                        <input type="text" placeholder="Tên" class="form-control"
                               name="buyerName"
                               id="buyerName" required>
                    </div>
                    <div class="form-group col-sm-5">     
                        <label class="control-label">Số Điện Thoại <span
                                class="require">(*)</span></label>

                        <input type="text" placeholder="Số Điện Thoại" class="form-control"
                               name="phoneNumber"
                               id="phoneNumber" required>


                    </div>
                    <!-- Text input-->
                    <div class="form-group col-sm-5">
                        <label class="control-label">Địa Chỉ <span
                                class="require">(*)</span></label>

                        <input type="text" placeholder="Địa Chỉ" class="form-control"
                               name="buyerAddress"
                               id="buyerAddress" required>

                    </div>
                    <div class="form-group col-sm-5">     
                        <label class="control-label">Quốc gia<span
                                class="require">(*)</span></label>

                        <input type="text" placeholder="" class="form-control"
                               name="buyerCountry"
                               id="buyerCountry" required value="Việt Nam">


                    </div>
                    <!-- Text input-->
                    <div class="form-group col-sm-5">
                        <label class="control-label" for="orderDescription">Mô Tả Hóa
                            Đơn<span class="require">(*)</span></label>

                        <textarea placeholder="Thông Tin Mô Tả Hóa Đơn" id="orderDescription"
                                  name="orderDescription"
                                  class="form-control" required=""></textarea>
                    </div>
                    <div class="form-group col-sm-5">     
                        <label class="control-label">Thành Phố <span
                                class="require">(*)</span></label>

                        <input type="text" placeholder="Thành Phố" class="form-control"
                               name="buyerCity"
                               id="buyerCity" required value="Hà Nội">


                    </div>
                    <div class="row"></div>
                    <div class="col-sm-12" id="alert"></div>
                    <div class="form-group col-sm-5">
                        <p>&nbsp;</p>
                        <button id="sendInstallment" type="submit" class="btn btn-info btn-lg" name="sendInstallment">
                            Thanh Toán Trả Góp
                        </button>

                    </div>

                </form>
            </div>

        </div>
        <!--    Start sendOrderToAlepayInstallment    -->


        <!--    End sendOrderToAlepayInstallment    -->

    </body>
</html>
