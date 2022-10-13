<?php
require('Lib/Alepay.php');
require 'config.php';

$encryptKey = $config['encryptKey'];

if (isset($_REQUEST['data']) && isset($_REQUEST['checksum'])) {
    $alepay = new Alepay($config);
    $utils = new AlepayUtils();
    $result = $utils->decryptCallbackData($_REQUEST['data'], $encryptKey);
    //  echo '<pre>';
    // print_r($result);
    //  echo '</pre>';
    $obj_data = json_decode($result);
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <!-- Compiled and minified CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/css/materialize.min.css">

        <!-- Compiled and minified JavaScript -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/js/materialize.min.js"></script>
        <link rel="stylesheet" href="style/style.css">
        <title>Show Data</title>
    </head>
    <body>
        <div id="container">
            <div class="row">
                <div class="col s3"></div>
                <div class="col s6 center">
                    <h3>Kết quả</h3>
                    <ul class="collection col-md-8">
                        <li class="collection-item">
                            <div>
                                <h6>Mã Lỗi : </h6>
                                <p>
                                    <?php
                                    if ($obj_data->errorCode === '155') {
                                        // Xử lý khi giao dịch chờ xác nhận
                                        echo "Giao Dịch Đã Thành Công!<br> Bạn Nên Xác Nhận Phía Ngân Hàng Để Hoàn Thành Nốt Thủ Tục Giao Dịch";
                                    } else {
                                        echo "Giao Dịch Thất Bại Mời Tra Trong Tài Liệu Để Biết Rõ Hơn ! mã lỗi: " . $obj_data->errorCode;
                                    }
                                    ?>
                                </p>
                            </div>
                        </li>
                        <li class="collection-item">
                            <div>
                                <h6>Mã Giao Dịch : </h6>
                                <p><?php
                                    echo $obj_data->data;
                                    echo '<br>Lấy thông tin giao dịch trả góp<br>';
                                    $info = json_decode($alepay->getTransactionInfo($obj_data->data));
                                    echo '<pre>';
                                    var_dump($info);
                                    echo '</pre>';
                                    ?></p>
                            </div>
                        </li>
                    </ul>
                    <ul class="collection col-md-8">
                        <li class="collection-item">
                            <div>
                                <a href="<?php echo ('http://' . $_SERVER['SERVER_NAME'] . '/alepay-installment/index.php') ?>">Nhấn Vào Đây Nếu Bạn Muốn Mua Tiếp</a>
                            </div>
                        </li>
                    </ul>

                </div>
            </div>
        </div>
    </body>
</html>
