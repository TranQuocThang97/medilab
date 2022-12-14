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
                            //C???p nh??t order v???i token  $nl_result->token ????? s??? d???ng check ho??n th??nh sau n??y
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
                    $error = "B???n ch??a ch???n Ng??n h??ng";
                }
            } else {
                $error = "B???n ch??a nh???p ????? th??ng tin kh??ch h??ng";
            }
        }
        ?>

        <div class="navigator">
            <div class="header-top">
                <div class="container">
                    <div class="ht-left">
                        <p>S???n ph???m ???????c ki???m duy???t v?? v???n chuy???n ?????c quy???n b???i <b>ViettelPost</b></p>
                    </div><!-- ht-left -->
                    <div class="ht-right">
                        <p>Ch??m s??c kh??ch h??ng <span>0462.660.310 - 0976.067.796</span></p>
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
                                    <option>T???t c??? danh m???c</option>
                                    <option>Th???i trang</option>
                                    <option>??i???n t???</option>
                                </select>
                            </div>
                            <div class="bs-text">
                                <input name="search" type="text" class="text" placeholder="T??m ki???m, V?? d???: ?????m d??? ti???c, iphone 6, ipad" />
                            </div>
                            <button type="submit" class="btn-search"><i class="fa fa-search"></i></button>
                            <div class="sugget-search">
                                <ul>
                                    <li><a href="#"><span class="text-warning">Shop:</span> Chuy??n <b>iPhon</b>e h??ng ch??nh h??ng</a></li>
                                    <li><a href="#"><span class="text-warning">Shop:</span> iStore - <b>iPhon</b>e, iPad</a></li>
                                </ul>
                                <div class="ss-line"></div>
                                <ul>
                                    <li><a href="#"><span class="ss-product"><b>iPhon</b>e 6 Plus 16Gb h??ng ch??nh h??ng&nbsp;<span class="ss-gray">trong</span>&nbsp;<span class="text-warning">??i???n tho???i di ?????ng</span></span><span class="ss-price">19.000.000??</span></a></li>
                                    <li><a href="#"><span class="ss-product">V??? <b>iPhon</b>silicon&nbsp;<span class="ss-gray">trong</span>&nbsp;<span class="text-warning">Ph??? ki???n ??i???n tho???i</span></span><span class="ss-price">120.000??</span></a></li>
                                    <li><a href="#"><span class="ss-product">D??y c??p <b>iPhon</b>silicon&nbsp;<span class="ss-gray">trong</span>&nbsp;<span class="text-warning">Linh ki???n m??y t??nh</span></span><span class="ss-price">75.000??</span></a></li>
                                </ul>
                                <div class="ss-more"><a href="#"><i class="fa fa-caret-right"></i>C??n 120 k???t qu?? kh??c</a></div>
                                <div class="ss-line"></div>
                                <ul>
                                    <li><a href="#"><b>iPhon</b>e 3s</a></li>
                                    <li><a href="#"><b>iPhon</b>e 5 16GB</a></li>
                                    <li><a href="#"><b>iPhon</b>e 6 128GB</a></li>
                                    <li><a href="#"><b>iPhon</b>e 6 Plus 64GB</a></li>
                                </ul>
                                <div class="ss-more"><a href="#"><i class="fa fa-caret-right"></i>C??n 20 k???t qu?? kh??c</a></div>
                            </div><!-- sugget-search -->
                        </div><!-- bs-inner -->
                    </div><!-- box-search -->
                    <div class="box-admin">
                        <ul>
                            <li class="popout-hoverjs">
                                <div class="ba-nolink">
                                    <span class="rectangle-box"></span>
                                    <span class="ba-textsmall">Test</span>
                                    <span class="ba-textbig">T??i kho???n <i class="fa fa-caret-down" aria-hidden="true"></i></span>
                                </div>
                                <div class="popout-submenu popout-right">
                                    <div class="ps-inner">
                                        <i class="fa fa-caret-up"></i>
                                        <div class="ps-padding">
                                            <ul class="ps-list text-center">
                                                <li>
                                                    <p>Tr???i nghi???m ngay v???i t??i kho???n <b><i>Prime</i></b> ????? ???????c <b>mi???n ph?? v???n chuy???n</b></p>
                                                </li>
                                                <li>
                                                    <p><a class="btn btn-link btn-block" href="#">Xem chi ti???t</a></p>
                                                </li>
                                                <li>
                                                    <p><a class="btn btn-danger btn-block" href="#">D??NG TH??? T??I KHO???N PRIME</a></p>
                                                </li>
                                            </ul>
                                        </div><!-- ps-padding -->
                                    </div><!-- ps-inner -->
                                </div><!-- popout-submenu -->
                            </li>
                            <li class="popout-hoverjs">
                                <div class="ba-nolink">
                                    <span class="rectangle-box1"></span>
                                    <span class="ba-textsmall">Gi??? h??ng</span>
                                    <span class="ba-textbig">2 s???n ph???m</span>
                                </div>
                                <div class="popout-submenu popout-right">
                                    <div class="ps-inner">
                                        <i class="fa fa-caret-up"></i>
                                        <div class="ps-padding">
                                            <div class="ps-check-title">
                                                ????n h??ng c???a t??i<i class="fa fa-file-text"></i>
                                            </div>
                                            <div class="ps-check-form">
                                                <div class="form-group">
                                                    <input class="form-control" name="" type="text" placeholder="Nh???p s??? ??i???n tho???i ho???c email" />
                                                </div>
                                                <div class="form-group">
                                                    <input class="form-control ps-check-code" name="" type="text" placeholder="Nh???p m?? ????n h??ng" />
                                                    <button class="btn btn-danger" type="submit"  data-toggle="modal" data-target="#ModalCheck">Ki???m tra</button>
                                                </div>
                                                <div class="alert alert-danger" role="alert">
                                                    M?? ????n h??ng ho???c s??? ??i???n tho???i kh??ng ????ng.
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
                        <li><a href="#">Trang ch???</a></li>
                        <li><a href="#">?????c s???n mi???n b???c</a></li>
                        <li class="active">Qu???ng Ninh</li>
                    </ol>
                </div>
            </div>
            <div class="exhibition-box">
                <div class="exhibition-title">
                    <div class="modal-title">
                        <ul class="abs clearfix">
                            <li>
                                <div class="list-step">
                                    <a href="#"><span class="number"><i class="fa fa-check" aria-hidden="true"></i></span> ????ng nh???p </a>
                                    <span class="chevron"></span>
                                </div>
                            </li>
                            <li>
                                <div class="list-step">
                                    <a href="#"><span class="number">2</span> Th??ng tin ????n h??ng </a>
                                    <span class="chevron"></span>
                                </div>
                            </li>
                            <li class="active">
                                <div class="list-step">
                                    <a href="#"><span class="number">3</span> H??nh th???c thanh to??n </a> 
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
                                    <h3><b>B?????c 1: </b> ?????a ch??? nh???n h??ng</h3>
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
                                                <label for="inputEmail3" class="col-sm-3 control-label">S??? ti???n thanh to??n</label>
                                                <div class="col-sm-3">

                                                    <input type="text" id="total_amount" name="total_amount" class="field-check form-control" value="5000" required="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputEmail3" class="col-sm-3 control-label">H??? t??n</label>
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
                                                <label for="inputPassword3" class="col-sm-3 control-label">S??? ??i???n tho???i</label>
                                                <div class="col-sm-6">
                                                    <input type="text" id="buyer_mobile" name="buyer_mobile" class="field-check form-control" value="<?= @$_REQUEST['buyer_mobile'] ?>" required="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <ul class="list-content">

                                                    <li class="active">
                                                        <label><input checked="checked" type="radio" value="ATM_ONLINE" name="option_payment"> Thanh to??n online b???ng th??? ng??n h??ng n???i ?????a</label>
                                                        <div class="boxContent">
                                                            <p><i>
                                                                    <span style="color:#ff5a00;font-weight:bold;text-decoration:underline;">L??u ??</span>: B???n c???n ????ng k?? Internet-Banking ho???c d???ch v??? thanh to??n tr???c tuy???n t???i ng??n h??ng tr?????c khi th???c hi???n.</i></p>

                                                            <ul class="cardList clearfix">
                                                                <li class="bank-online-methods ">
                                                                    <label for="vcb_ck_on">
                                                                        <i class="BIDV" title="Ng??n h??ng TMCP ?????u t?? &amp; Ph??t tri???n Vi???t Nam"></i>
                                                                        <input type="radio" value="BIDV"  name="bankcode" >

                                                                    </label></li>
                                                                <li class="bank-online-methods ">
                                                                    <label for="vcb_ck_on">
                                                                        <i class="VCB" title="Ng??n h??ng TMCP Ngo???i Th????ng Vi???t Nam"></i>
                                                                        <input type="radio" value="VCB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="vnbc_ck_on">
                                                                        <i class="DAB" title="Ng??n h??ng ????ng ??"></i>
                                                                        <input type="radio" value="DAB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="tcb_ck_on">
                                                                        <i class="TCB" title="Ng??n h??ng K??? Th????ng"></i>
                                                                        <input type="radio" value="TCB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_mb_ck_on">
                                                                        <i class="MB" title="Ng??n h??ng Qu??n ?????i"></i>
                                                                        <input type="radio" value="MB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_vib_ck_on">
                                                                        <i class="VIB" title="Ng??n h??ng Qu???c t???"></i>
                                                                        <input type="radio" value="VIB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_vtb_ck_on">
                                                                        <i class="ICB" title="Ng??n h??ng C??ng Th????ng Vi???t Nam"></i>
                                                                        <input type="radio" value="ICB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_exb_ck_on">
                                                                        <i class="EXB" title="Ng??n h??ng Xu???t Nh???p Kh???u"></i>
                                                                        <input type="radio" value="EXB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_acb_ck_on">
                                                                        <i class="ACB" title="Ng??n h??ng ?? Ch??u"></i>
                                                                        <input type="radio" value="ACB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_hdb_ck_on">
                                                                        <i class="HDB" title="Ng??n h??ng Ph??t tri???n Nh?? TPHCM"></i>
                                                                        <input type="radio" value="HDB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_msb_ck_on">
                                                                        <i class="MSB" title="Ng??n h??ng H??ng H???i"></i>
                                                                        <input type="radio" value="MSB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_nvb_ck_on">
                                                                        <i class="NVB" title="Ng??n h??ng Nam Vi???t"></i>
                                                                        <input type="radio" value="NVB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_vab_ck_on">
                                                                        <i class="VAB" title="Ng??n h??ng Vi???t ??"></i>
                                                                        <input type="radio" value="VAB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_vpb_ck_on">
                                                                        <i class="VPB" title="Ng??n H??ng Vi???t Nam Th???nh V?????ng"></i>
                                                                        <input type="radio" value="VPB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_scb_ck_on">
                                                                        <i class="SCB" title="Ng??n h??ng S??i G??n Th????ng t??n"></i>
                                                                        <input type="radio" value="SCB"  name="bankcode" >

                                                                    </label></li>



                                                                <li class="bank-online-methods ">
                                                                    <label for="bnt_atm_pgb_ck_on">
                                                                        <i class="PGB" title="Ng??n h??ng X??ng d???u Petrolimex"></i>
                                                                        <input type="radio" value="PGB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="bnt_atm_gpb_ck_on">
                                                                        <i class="GPB" title="Ng??n h??ng TMCP D???u kh?? To??n C???u"></i>
                                                                        <input type="radio" value="GPB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="bnt_atm_agb_ck_on">
                                                                        <i class="AGB" title="Ng??n h??ng N??ng nghi???p &amp; Ph??t tri???n n??ng th??n"></i>
                                                                        <input type="radio" value="AGB"  name="bankcode" >

                                                                    </label></li>

                                                                <li class="bank-online-methods ">
                                                                    <label for="bnt_atm_sgb_ck_on">
                                                                        <i class="SGB" title="Ng??n h??ng S??i G??n C??ng Th????ng"></i>
                                                                        <input type="radio" value="SGB"  name="bankcode" >

                                                                    </label></li>	
                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_bab_ck_on">
                                                                        <i class="BAB" title="Ng??n h??ng B???c ??"></i>
                                                                        <input type="radio" value="BAB"  name="bankcode" >

                                                                    </label></li>
                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_bab_ck_on">
                                                                        <i class="TPB" title="T???n phong bank"></i>
                                                                        <input type="radio" value="TPB"  name="bankcode" >

                                                                    </label></li>
                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_bab_ck_on">
                                                                        <i class="NAB" title="Ng??n h??ng Nam ??"></i>
                                                                        <input type="radio" value="NAB"  name="bankcode" >

                                                                    </label></li>
                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_bab_ck_on">
                                                                        <i class="SHB" title="Ng??n h??ng TMCP S??i G??n - H?? N???i (SHB)"></i>
                                                                        <input type="radio" value="SHB"  name="bankcode" >

                                                                    </label></li>
                                                                <li class="bank-online-methods ">
                                                                    <label for="sml_atm_bab_ck_on">
                                                                        <i class="OJB" title="Ng??n h??ng TMCP ?????i D????ng (OceanBank)"></i>
                                                                        <input type="radio" value="OJB"  name="bankcode" >

                                                                    </label></li>





                                                            </ul>

                                                        </div>
                                                    </li>


                                                </ul>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputPassword3" class="col-sm-3 control-label">S??? th???</label>
                                                <div class="col-sm-6">
                                                    <input type="text" id="card_number" name="card_number" class="field-check form-control" value="<?= @$_REQUEST['card_number'] ?>" required="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputPassword3" class="col-sm-3 control-label">T??n ch??? th???</label>
                                                <div class="col-sm-6">
                                                    <input type="text" id="card_fullname" name="card_fullname" class="field-check form-control" value="<?= @$_REQUEST['card_fullname'] ?>" required="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputPassword3" class="col-sm-3 control-label">Ph??t h??nh</label>
                                                <div class="col-sm-6">
                                                    <div class="col-sm-6" style="padding-left: 0">
                                                        <input type="text" id="card_month" name="card_month" class="field-check form-control" value="<?= @$_REQUEST['card_month'] ?>" size="2" required="" placeholder="Th??ng ph??t h??nh">
                                                    </div>


                                                    <div class="col-sm-6" style="padding-right: 0">
                                                        <input type="text" id="card_year" name="card_year" class="field-check form-control" value="<?= @$_REQUEST['card_year'] ?>" size="2" required="" placeholder="N??m ph??t h??nh"> 
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">

                                                <input type="submit" name="nlpayment" value="Thanh to??n" class="btn btn-success"/>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="ec-right">
                            <div class="ec-detail-title">
                                <h3>????n h??ng ( 2 s???n ph???m )</h3>

                            </div>
                            <div class="ec-detail">
                                <div class="ecd-left">
                                    <div class="ecd-img">
                                        <a href="#"><img src="images/product1.png" alt=""></a>
                                    </div>
                                    <div class="ecd-text">
                                        <label for="">
                                            <a href="#">H???p Ch?? T??n C????ng ?????c S???n Th??i Nguy??n<br/> 200Gr</a> 
                                        </label>
                                    </div>
                                </div>
                                <div class="ecd-right">
                                    <div class="ecd-price">
                                        <p>2.000??</p>
                                        <p>x1</p>
                                        <p>2.000??</p>
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
                                            <a href="#">H???p Ch?? T??n C????ng ?????c S???n Th??i Nguy??n<br/> 200Gr</a> 
                                        </label>
                                    </div>
                                </div>
                                <div class="ecd-right">
                                    <div class="ecd-price">
                                        <p>2.000??</p>
                                        <p>x1</p>
                                        <p>2.000??</p>
                                    </div>
                                </div>
                            </div>
                            <hr/>
                            <div class="total-price">
                                <p>T???ng ti???n<span>5.000??</span></p>
                                <p>T???ng ph?? COD <span>Mi???n Ph??</span></p>
                                <p>M?? gi???m gi?? <span>-0??</span></p>
                            </div>
                            <hr/>
                            <div class="total-price">
                                <p>S??? ti???n c??n l???i c???n thanh to??n<span>5.000??</span></p>
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
                                    <label>S???n ph???m ?????t ti??u chu???n</label>
                                    <p>Ph??n ph???i h??ng Vi???t Nam ch??nh h??ng <br/>t??? nh?? s???n xu???t</p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="ft-detail">
                                <div class="ft-detail-left">
                                    <span class="icon-detial2"></span>
                                </div>
                                <div class="ft-detail-right">
                                    <label>S???n ph???m ?????t ti??u chu???n</label>
                                    <p>Ph??n ph???i h??ng Vi???t Nam ch??nh h??ng <br/>t??? nh?? s???n xu???t</p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="ft-detail">
                                <div class="ft-detail-left">
                                    <span class="icon-detial3"></span>
                                </div>
                                <div class="ft-detail-right">
                                    <label>S???n ph???m ?????t ti??u chu???n</label>
                                    <p>Ph??n ph???i h??ng Vi???t Nam ch??nh h??ng <br/>t??? nh?? s???n xu???t</p>
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
                                        <label for="">T??i kho???n c???a b???n</label>
                                    </li>
                                    <li><a href="#">????ng nh???p</a></li>
                                    <li><a href="#">????ng k??</a></li>
                                    <li><a href="#">Li??n k???t th???</a></li>
                                    <li><a href="#">chodientu.vn</a></li>
                                </ul>
                            </div>
                        </li>
                        <li class="list-footer">
                            <div class="fc-detail">
                                <ul>
                                    <li>
                                        <label for="">CH??M S??C KH??CH H??NG <br/><span>0462.660.310 - 0976.067.796</span></label>
                                    </li>
                                    <li><a href="#">??i???u kho???n mua b??n h??ng h??a</a></li>
                                    <li><a href="#">H?????ng d???n ?????t h??ng</a></li>
                                    <li><a href="#">H?????ng d???n ch???n ?????c s???n</a></li>
                                    <li><a href="#">Quy tr??nh giao h??ng</a></li>
                                    <li><a href="#">H?????ng d???n b???o qu???n h??ng</a></li>
                                    <li><a href="#">Ch??nh s??ch ????ng g??i h??ng</a></li>
                                </ul>
                            </div>
                        </li>
                        <li class="list-footer">
                            <div class="fc-detail">
                                <ul>
                                    <li>
                                        <label for="">V??? CH??NG T??I(VIETTEL POST)</label>
                                    </li>
                                    <li><a href="#">Gi???i thi???u Viettel Post</a></li>
                                    <li><a href="#">Quy ch??? ho???t ?????ng c???a s??n</a></li>
                                    <li><a href="#">Tuy???n d???ng</a></li>
                                    <li><a href="#">H???p th?? g??p ??</a></li>
                                </ul>
                            </div>
                        </li>
                        <li class="list-footer">
                            <div class="fc-detail">
                                <ul>
                                    <li>
                                        <label for="">????ng k?? nh???n tin t??? sds</label>
                                    </li>
                                    <li>
                                        <div class="">
                                            <div class="input-group">
                                                <input type="text" class="form-control" placeholder="Search for...">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button">G???i</button>
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                    <li><label for="">ch???ng ch???</label></li>
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
                        <p>?? 2016. S???n ph???m thu???c v??? T???ng c??ng ty C??? ph???n B??u Ch??nh Viettel</p>
                    </div>
                    <div class="fb-list-right">
                        <ul>
                            <li><a href="#">Gi???i thi???u</a></li>
                            <li><a href="#">??i???u kho???n b???o m???t</a></li>
                            <li><a href="#">Ch??nh s??ch s??? d???ng</a></li>
                            <li><a href="#">Li??n h???</a></li>
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