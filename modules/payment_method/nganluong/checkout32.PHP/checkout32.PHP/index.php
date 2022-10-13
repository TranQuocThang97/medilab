<?php
date_default_timezone_set('Asia/Bangkok'); // Set Time Zone required tu PHP 5
ob_start();
session_start();
include('config.php');
include('include/NL_Checkoutv3.php');
?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <link rel="stylesheet" type="text/css" href="css/animate.min.css">
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="css/fonts.css">
        <link rel="stylesheet" type="text/css" href="css/form.css">

        <link rel="stylesheet" type="text/css" href="css/owl.carousel.css">
        <link rel="stylesheet" type="text/css" href="css/header.css">
        <link rel="stylesheet" type="text/css" href="css/footer.css">
        <link rel="stylesheet" type="text/css" href="css/exhibition.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" type="text/css" href="css/common.css">

    </head>
    <body>
        <?php
        $error = '';
        if (@$_POST['nlpayment']) {


            $nlcheckout = new NL_CheckOutV3(MERCHANT_ID, MERCHANT_PASS, RECEIVER, URL_API);
            $total_amount = $_POST['total_amount'];

            $array_items[0] = array('item_name1' => 'Product name',
                'item_quantity1' => 1,
                'item_amount1' => $total_amount,
                'item_url1' => 'http://nganluong.vn/');

            $array_items = array();
            $payment_method = $_POST['option_payment'];
            $bank_code = @$_POST['bankcode'];
            $order_code = "TESTCHECKOUT32_" . time();

            $payment_type = '';
            $discount_amount = 0;
            $order_description = '';
            $tax_amount = 0;
            $fee_shipping = 0;
            $return_url = URL . 'payment_success.php';
            $cancel_url = urlencode(URL . '?orderid=' . $order_code);

            $buyer_fullname = $_POST['buyer_fullname'];
            $buyer_email = $_POST['buyer_email'];
            $buyer_mobile = $_POST['buyer_mobile'];
            $card_number = $_POST['card_number'];
            $card_fullname = $_POST['card_fullname'];
            $card_month = $_POST['card_month'];
            $card_year = $_POST['card_year'];
            $buyer_address = '';

            if ($payment_method != '' && $buyer_email != "" && $buyer_mobile != "" && $buyer_fullname != "" && filter_var($buyer_email, FILTER_VALIDATE_EMAIL)) {

                if ($payment_method == "ATM_ONLINE" && $bank_code != '') {
                    $nl_result = $nlcheckout->BankCheckout($order_code, $total_amount, $bank_code, $payment_type, $order_description, $tax_amount, $fee_shipping, $discount_amount, $return_url, $cancel_url, $buyer_fullname, $buyer_email, $buyer_mobile, $buyer_address, $array_items, $card_number, $card_fullname, $card_month, $card_year);
                    if ($nl_result->error_code == '00') {

                        if ($nl_result->auth_site != 'NL') {
                            //echo $nl_result->auth_site;
                            header('Location: ' . (string) $nl_result->auth_url);
                            //echo(string) $nl_result->auth_url;
                            die();
                            //Cập nhât order với token  $nl_result->token để sử dụng check hoàn thành sau này
                        } else {
                            $_SESSION['auth_url'] = (string) $nl_result->auth_url;
                            $_SESSION['token'] = (string) $nl_result->token;
                            header('Location: ' . URL . 'authen.php');
                            die();
                            // $nl_authen = $nlcheckout->AuthenTransaction($nl_result->token, '123214', $nl_result->auth_url);
                            // var_dump($nl_authen);
                        }
                    } else {
                       // $error = $nl_result->error_message;
                        $error = $nl_result->description;
                    }
                } else {
                    $error = "Bạn chưa chọn Ngân hàng";
                }
            } else {
                $error = "Bạn chưa nhập đủ thông tin khách hàng";
            }
        }
        ?>

        <div class="navigator">
            <div class="header-top">
                <div class="container">
                    <div class="ht-left">
                        <p>Sản phẩm được kiểm duyệt và vận chuyển độc quyền bởi <b>ViettelPost</b></p>
                    </div><!-- ht-left -->
                    <div class="ht-right">
                        <p>Chăm sóc khách hàng <span>0462.660.310 - 0976.067.796</span></p>
                    </div><!-- ht-right -->
                </div><!-- container -->
            </div><!-- header-top -->
            <div class="header">
                <div class="container">
                    <div class="logo"><a href="#"><img src="images/logo.png" alt=""></a></div>
                    <div class="box-search">
                        <div class="bs-inner">
                            <div class="bs-select">
                                <select class="bs-inputselect">
                                    <option>Tất cả danh mục</option>
                                    <option>Thời trang</option>
                                    <option>Điện tử</option>
                                </select>
                            </div>
                            <div class="bs-text">
                                <input name="search" type="text" class="text" placeholder="Tìm kiếm, Ví dụ: đầm dự tiệc, iphone 6, ipad" />
                            </div>
                            <button type="submit" class="btn-search"><i class="fa fa-search"></i></button>
                            <div class="sugget-search">
                                <ul>
                                    <li><a href="#"><span class="text-warning">Shop:</span> Chuyên <b>iPhon</b>e hàng chính hãng</a></li>
                                    <li><a href="#"><span class="text-warning">Shop:</span> iStore - <b>iPhon</b>e, iPad</a></li>
                                </ul>
                                <div class="ss-line"></div>
                                <ul>
                                    <li><a href="#"><span class="ss-product"><b>iPhon</b>e 6 Plus 16Gb hàng chính hãng&nbsp;<span class="ss-gray">trong</span>&nbsp;<span class="text-warning">Điện thoại di động</span></span><span class="ss-price">19.000.000đ</span></a></li>
                                    <li><a href="#"><span class="ss-product">Vỏ <b>iPhon</b>silicon&nbsp;<span class="ss-gray">trong</span>&nbsp;<span class="text-warning">Phụ kiện điện thoại</span></span><span class="ss-price">120.000đ</span></a></li>
                                    <li><a href="#"><span class="ss-product">Dây cáp <b>iPhon</b>silicon&nbsp;<span class="ss-gray">trong</span>&nbsp;<span class="text-warning">Linh kiện máy tính</span></span><span class="ss-price">75.000đ</span></a></li>
                                </ul>
                                <div class="ss-more"><a href="#"><i class="fa fa-caret-right"></i>Còn 120 kết quá khác</a></div>
                                <div class="ss-line"></div>
                                <ul>
                                    <li><a href="#"><b>iPhon</b>e 3s</a></li>
                                    <li><a href="#"><b>iPhon</b>e 5 16GB</a></li>
                                    <li><a href="#"><b>iPhon</b>e 6 128GB</a></li>
                                    <li><a href="#"><b>iPhon</b>e 6 Plus 64GB</a></li>
                                </ul>
                                <div class="ss-more"><a href="#"><i class="fa fa-caret-right"></i>Còn 20 kết quá khác</a></div>
                            </div><!-- sugget-search -->
                        </div><!-- bs-inner -->
                    </div><!-- box-search -->
                    <div class="box-admin">
                        <ul>
                            <li class="popout-hoverjs">
                                <div class="ba-nolink">
                                    <span class="rectangle-box"></span>
                                    <span class="ba-textsmall">Test</span>
                                    <span class="ba-textbig">Tài khoản <i class="fa fa-caret-down" aria-hidden="true"></i></span>
                                </div>
                                <div class="popout-submenu popout-right">
                                    <div class="ps-inner">
                                        <i class="fa fa-caret-up"></i>
                                        <div class="ps-padding">
                                            <ul class="ps-list text-center">
                                                <li>
                                                    <p>Trải nghiệm ngay với tài khoản <b><i>Prime</i></b> để được <b>miễn phí vận chuyển</b></p>
                                                </li>
                                                <li>
                                                    <p><a class="btn btn-link btn-block" href="#">Xem chi tiết</a></p>
                                                </li>
                                                <li>
                                                    <p><a class="btn btn-danger btn-block" href="#">DÙNG THỦ TÀI KHOẢN PRIME</a></p>
                                                </li>
                                            </ul>
                                        </div><!-- ps-padding -->
                                    </div><!-- ps-inner -->
                                </div><!-- popout-submenu -->
                            </li>
                            <li class="popout-hoverjs">
                                <div class="ba-nolink">
                                    <span class="rectangle-box1"></span>
                                    <span class="ba-textsmall">Giỏ hàng</span>
                                    <span class="ba-textbig">2 sản phẩm</span>
                                </div>
                                <div class="popout-submenu popout-right">
                                    <div class="ps-inner">
                                        <i class="fa fa-caret-up"></i>
                                        <div class="ps-padding">
                                            <div class="ps-check-title">
                                                Đơn hàng của tôi<i class="fa fa-file-text"></i>
                                            </div>
                                            <div class="ps-check-form">
                                                <div class="form-group">
                                                    <input class="form-control" name="" type="text" placeholder="Nhập số điện thoại hoạc email" />
                                                </div>
                                                <div class="form-group">
                                                    <input class="form-control ps-check-code" name="" type="text" placeholder="Nhập mã đơn hàng" />
                                                    <button class="btn btn-danger" type="submit"  data-toggle="modal" data-target="#ModalCheck">Kiểm tra</button>
                                                </div>
                                                <div class="alert alert-danger" role="alert">
                                                    Mã đơn hàng hoặc số điện thoại không đúng.
                                                </div>
                                            </div>
                                        </div><!-- ps-padding -->
                                    </div><!-- ps-inner -->
                                </div><!-- popout-submenu -->
                            </li>


                        </ul>
                    </div><!-- box-admin -->
                </div><!-- container -->
            </div><!-- header -->
        </div><!-- navigator -->
        <!-- Breadcrumbs -->

        <!-- End Breadcrumbs -->
        <!-- Content -->
        <div class="exhibition-all">
            <div class="breadcrumbs-all">
                <div class="container">
                    <ol class="breadcrumb">
                        <li><a href="#">Trang chủ</a></li>
                        <li><a href="#">Đặc sản miền bắc</a></li>
                        <li class="active">Quảng Ninh</li>
                    </ol>
                </div>
            </div>
            <div class="exhibition-box">
                <div class="exhibition-title">
                    <div class="modal-title">
                        <ul class="abs clearfix">
                            <li>
                                <div class="list-step">
                                    <a href="#"><span class="number"><i class="fa fa-check" aria-hidden="true"></i></span> Đăng nhập </a>
                                    <span class="chevron"></span>
                                </div>
                            </li>
                            <li>
                                <div class="list-step">
                                    <a href="#"><span class="number">2</span> Thông tin đơn hàng </a>
                                    <span class="chevron"></span>
                                </div>
                            </li>
                            <li class="active">
                                <div class="list-step">
                                    <a href="#"><span class="number">3</span> Hình thức thanh toán </a> 
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="container">
                    <div class="exhibition-content bg-none">
                        <div class="ec-step-left">
                            <div class="ec-step-detail">
                                <div class="ec-step-title">
                                    <h3><b>Bước 1: </b> Địa chỉ nhận hàng</h3>
                                </div>
                                <div class="ec-step-content">

                                    <div class="form-address">
                                        <form class="form-horizontal"  name="NLpayBank" method="post">
                                            <?php if (!empty($error)) { ?>  
                                                <div class="alert alert-danger">
                                                    <?php echo $error; ?>
                                                </div>
                                            <?php } ?>
                                            <div class="form-group">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Số tiền thanh toán</label>
                                                <div class="col-sm-3">

                                                    <input type="text" id="total_amount" name="total_amount" class="field-check form-control" value="5000" required="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Họ tên</label>
                                                <div class="col-sm-6">

                                                    <input type="text" id="buyer_fullname" name="buyer_fullname" class="field-check form-control" value="<?= @$_REQUEST['buyer_fullname'] ?>" required="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Email</label>
                                                <div class="col-sm-6">

                                                    <input type="text" id="buyer_email" name="buyer_email" class="field-check form-control" value="<?= @$_REQUEST['buyer_email'] ?>" required="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputPassword3" class="col-sm-3 control-label">Số Điện thoại</label>
                                                <div class="col-sm-6">
                                                    <input type="text" id="buyer_mobile" name="buyer_mobile" class="field-check form-control" value="<?= @$_REQUEST['buyer_mobile'] ?>" required="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <ul class="list-content">

                                                    <li class="active">
                                                        <label><input checked="checked" type="radio" value="ATM_ONLINE" name="option_payment"> Thanh toán online bằng thẻ ngân hàng nội địa</label>
                                                        <div class="boxContent">
                                                            <p><i>
                                                                    <span style="color:#ff5a00;font-weight:bold;text-decoration:underline;">Lưu ý</span>: Bạn cần đăng ký Internet-Banking hoặc dịch vụ thanh toán trực tuyến tại ngân hàng trước khi thực hiện.</i></p>

                                                            <ul class="cardList clearfix">
                                                                <li class="bank-online-methods ">
                                                                    <label for="vcb_ck_on">
                                                                        <i class="BIDV" title="Ngân hàng TMCP Đầu tư &amp; Phát triển Việt Nam"></i>
                                                                        <input type="radio" value="BIDV"  name="bankcode" >

                                                                    </label></li>
                                                                <li class="bank-online-methods ">
                                                                    <label for="vcb_ck_on">
                                                                        <i class="VCB" title="Ngân hàng TMCP Ngoại Thương Việt Nam"></i>
                                                                        <input type="radio" value="VCB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="vnbc_ck_on">
                                                                        <i class="DAB" title="Ngân hàng Đông Á"></i>
                                                                        <input type="radio" value="DAB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="tcb_ck_on">
                                                                        <i class="TCB" title="Ngân hàng Kỹ Thương"></i>
                                                                        <input type="radio" value="TCB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_mb_ck_on">
                                                                        <i class="MB" title="Ngân hàng Quân Đội"></i>
                                                                        <input type="radio" value="MB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_vib_ck_on">
                                                                        <i class="VIB" title="Ngân hàng Quốc tế"></i>
                                                                        <input type="radio" value="VIB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_vtb_ck_on">
                                                                        <i class="ICB" title="Ngân hàng Công Thương Việt Nam"></i>
                                                                        <input type="radio" value="ICB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_exb_ck_on">
                                                                        <i class="EXB" title="Ngân hàng Xuất Nhập Khẩu"></i>
                                                                        <input type="radio" value="EXB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_acb_ck_on">
                                                                        <i class="ACB" title="Ngân hàng Á Châu"></i>
                                                                        <input type="radio" value="ACB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_hdb_ck_on">
                                                                        <i class="HDB" title="Ngân hàng Phát triển Nhà TPHCM"></i>
                                                                        <input type="radio" value="HDB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_msb_ck_on">
                                                                        <i class="MSB" title="Ngân hàng Hàng Hải"></i>
                                                                        <input type="radio" value="MSB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_nvb_ck_on">
                                                                        <i class="NVB" title="Ngân hàng Nam Việt"></i>
                                                                        <input type="radio" value="NVB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_vab_ck_on">
                                                                        <i class="VAB" title="Ngân hàng Việt Á"></i>
                                                                        <input type="radio" value="VAB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_vpb_ck_on">
                                                                        <i class="VPB" title="Ngân Hàng Việt Nam Thịnh Vượng"></i>
                                                                        <input type="radio" value="VPB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_scb_ck_on">
                                                                        <i class="SCB" title="Ngân hàng Sài Gòn Thương tín"></i>
                                                                        <input type="radio" value="SCB"  name="bankcode" >

                                                                    </label></li>



                                                                <li class="bank-online-methods ">
                                                                    <label for="bnt_atm_pgb_ck_on">
                                                                        <i class="PGB" title="Ngân hàng Xăng dầu Petrolimex"></i>
                                                                        <input type="radio" value="PGB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="bnt_atm_gpb_ck_on">
                                                                        <i class="GPB" title="Ngân hàng TMCP Dầu khí Toàn Cầu"></i>
                                                                        <input type="radio" value="GPB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="bnt_atm_agb_ck_on">
                                                                        <i class="AGB" title="Ngân hàng Nông nghiệp &amp; Phát triển nông thôn"></i>
                                                                        <input type="radio" value="AGB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="bnt_atm_sgb_ck_on">
                                                                        <i class="SGB" title="Ngân hàng Sài Gòn Công Thương"></i>
                                                                        <input type="radio" value="SGB"  name="bankcode" >

                                                                    </label></li>	
                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_bab_ck_on">
                                                                        <i class="BAB" title="Ngân hàng Bắc Á"></i>
                                                                        <input type="radio" value="BAB"  name="bankcode" >

                                                                    </label></li>
                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_bab_ck_on">
                                                                        <i class="TPB" title="Tền phong bank"></i>
                                                                        <input type="radio" value="TPB"  name="bankcode" >

                                                                    </label></li>
                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_bab_ck_on">
                                                                        <i class="NAB" title="Ngân hàng Nam Á"></i>
                                                                        <input type="radio" value="NAB"  name="bankcode" >

                                                                    </label></li>
                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_bab_ck_on">
                                                                        <i class="SHB" title="Ngân hàng TMCP Sài Gòn - Hà Nội (SHB)"></i>
                                                                        <input type="radio" value="SHB"  name="bankcode" >

                                                                    </label></li>
                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_bab_ck_on">
                                                                        <i class="OJB" title="Ngân hàng TMCP Đại Dương (OceanBank)"></i>
                                                                        <input type="radio" value="OJB"  name="bankcode" >

                                                                    </label></li>





                                                            </ul>

                                                        </div>
                                                    </li>


                                                </ul>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputPassword3" class="col-sm-3 control-label">Số thẻ</label>
                                                <div class="col-sm-6">
                                                    <input type="text" id="card_number" name="card_number" class="field-check form-control" value="<?= @$_REQUEST['card_number'] ?>" required="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputPassword3" class="col-sm-3 control-label">Tên chủ thẻ</label>
                                                <div class="col-sm-6">
                                                    <input type="text" id="card_fullname" name="card_fullname" class="field-check form-control" value="<?= @$_REQUEST['card_fullname'] ?>" required="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputPassword3" class="col-sm-3 control-label">Phát hành</label>
                                                <div class="col-sm-6">
                                                    <div class="col-sm-6" style="padding-left: 0">
                                                        <input type="text" id="card_month" name="card_month" class="field-check form-control" value="<?= @$_REQUEST['card_month'] ?>" size="2" required="" placeholder="Tháng phát hành">
                                                    </div>


                                                    <div class="col-sm-6" style="padding-right: 0">
                                                        <input type="text" id="card_year" name="card_year" class="field-check form-control" value="<?= @$_REQUEST['card_year'] ?>" size="2" required="" placeholder="Năm phát hành"> 
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">

                                                <input type="submit" name="nlpayment" value="Thanh toán" class="btn btn-success"/>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="ec-right">
                            <div class="ec-detail-title">
                                <h3>Đơn hàng ( 2 sản phẩm )</h3>

                            </div>
                            <div class="ec-detail">
                                <div class="ecd-left">
                                    <div class="ecd-img">
                                        <a href="#"><img src="images/product1.png" alt=""></a>
                                    </div>
                                    <div class="ecd-text">
                                        <label for="">
                                            <a href="#">Hộp Chè Tân Cương Đặc Sản Thái Nguyên<br/> 200Gr</a> 
                                        </label>
                                    </div>
                                </div>
                                <div class="ecd-right">
                                    <div class="ecd-price">
                                        <p>2.000đ</p>
                                        <p>x1</p>
                                        <p>2.000đ</p>
                                    </div>
                                </div>
                            </div>
                            <div class="ec-detail">
                                <div class="ecd-left">
                                    <div class="ecd-img">
                                        <a href="#"><img src="images/product1.png" alt=""></a>
                                    </div>
                                    <div class="ecd-text">
                                        <label for="">
                                            <a href="#">Hộp Chè Tân Cương Đặc Sản Thái Nguyên<br/> 200Gr</a> 
                                        </label>
                                    </div>
                                </div>
                                <div class="ecd-right">
                                    <div class="ecd-price">
                                        <p>2.000đ</p>
                                        <p>x1</p>
                                        <p>2.000đ</p>
                                    </div>
                                </div>
                            </div>
                            <hr/>
                            <div class="total-price">
                                <p>Tổng tiền<span>5.000đ</span></p>
                                <p>Tổng phí COD <span>Miễn Phí</span></p>
                                <p>Mã giảm giá <span>-0đ</span></p>
                            </div>
                            <hr/>
                            <div class="total-price">
                                <p>Số tiền còn lại cần thanh toán<span>5.000đ</span></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>    
        </div>
        <!-- End Content -->
        <div class="footer-all">
            <div class="container">
                <div class="footer-top">
                    <ul>
                        <li>
                            <div class="ft-detail">
                                <div class="ft-detail-left">
                                    <span class="icon-detial1"></span>
                                </div>
                                <div class="ft-detail-right">
                                    <label>Sản phẩm đạt tiêu chuẩn</label>
                                    <p>Phân phối hàng Việt Nam chính hãng <br/>từ nhà sản xuất</p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="ft-detail">
                                <div class="ft-detail-left">
                                    <span class="icon-detial2"></span>
                                </div>
                                <div class="ft-detail-right">
                                    <label>Sản phẩm đạt tiêu chuẩn</label>
                                    <p>Phân phối hàng Việt Nam chính hãng <br/>từ nhà sản xuất</p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="ft-detail">
                                <div class="ft-detail-left">
                                    <span class="icon-detial3"></span>
                                </div>
                                <div class="ft-detail-right">
                                    <label>Sản phẩm đạt tiêu chuẩn</label>
                                    <p>Phân phối hàng Việt Nam chính hãng <br/>từ nhà sản xuất</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-center">
                <div class="container">
                    <ul>
                        <li class="list-footer">
                            <div class="fc-detail">
                                <ul>
                                    <li>
                                        <label for="">Tài khoản của bạn</label>
                                    </li>
                                    <li><a href="#">Đăng nhập</a></li>
                                    <li><a href="#">Đăng ký</a></li>
                                    <li><a href="#">Liên kết thẻ</a></li>
                                    <li><a href="#">chodientu.vn</a></li>
                                </ul>
                            </div>
                        </li>
                        <li class="list-footer">
                            <div class="fc-detail">
                                <ul>
                                    <li>
                                        <label for="">CHĂM SÓC KHÁCH HÀNG <br/><span>0462.660.310 - 0976.067.796</span></label>
                                    </li>
                                    <li><a href="#">Điều khoản mua bán hàng hóa</a></li>
                                    <li><a href="#">Hướng dẫn đặt hàng</a></li>
                                    <li><a href="#">Hướng dẫn chọn đặc sản</a></li>
                                    <li><a href="#">Quy trình giao hàng</a></li>
                                    <li><a href="#">Hướng dẫn bảo quản hàng</a></li>
                                    <li><a href="#">Chính sách đóng gói hàng</a></li>
                                </ul>
                            </div>
                        </li>
                        <li class="list-footer">
                            <div class="fc-detail">
                                <ul>
                                    <li>
                                        <label for="">VỀ CHÚNG TÔI(VIETTEL POST)</label>
                                    </li>
                                    <li><a href="#">Giới thiệu Viettel Post</a></li>
                                    <li><a href="#">Quy chế hoạt động của sàn</a></li>
                                    <li><a href="#">Tuyển dụng</a></li>
                                    <li><a href="#">Hộp thư góp ý</a></li>
                                </ul>
                            </div>
                        </li>
                        <li class="list-footer">
                            <div class="fc-detail">
                                <ul>
                                    <li>
                                        <label for="">đăng ký nhận tin từ sds</label>
                                    </li>
                                    <li>
                                        <div class="">
                                            <div class="input-group">
                                                <input type="text" class="form-control" placeholder="Search for...">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button">Gửi</button>
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                    <li><label for="">chứng chỉ</label></li>
                                    <li><a href="#"><img src="images/certificate.png" alt=""></a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="container">
                    <div class="fb-list-left">
                        <p>© 2016. Sản phẩm thuộc về Tổng công ty Cổ phần Bưu Chính Viettel</p>
                    </div>
                    <div class="fb-list-right">
                        <ul>
                            <li><a href="#">Giới thiệu</a></li>
                            <li><a href="#">Điều khoản bảo mật</a></li>
                            <li><a href="#">Chính sách sử dụng</a></li>
                            <li><a href="#">Liên hệ</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script src="js/jquery.slimscroll.js"></script>
        <script type="text/javascript" src="js/wow.min.js"></script>
        <script type="text/javascript" src="js/owl.carousel.js"></script>
        <script type="text/javascript" src="js/style.js"></script>
        <script>
            $(document).ready(function () {
                $(".check-hide").click(function () {
                    $(".rc-toggle").hide('slow');
                });
                $(".check-show").click(function () {
                    $(".rc-toggle").show('slow');
                });
            });
        </script>
        <script language="javascript">
            $('input[name="option_payment"]').bind('click', function () {
                $('.list-content li').removeClass('active');
                $(this).parent().parent('li').addClass('active');
            });
        </script> 	
    </body>
</html> 