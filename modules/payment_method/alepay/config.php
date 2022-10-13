<?php
global $ims;
//Thông tin cấu hình
// define('URL_DEMO', (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . '/alepay-installment/');
// define('URL_CALLBACK', URL_DEMO . '/result.php'); // URL đón nhận kết quả 
define('URL_CALLBACK', $ims->site->get_link('product',$ims->setting['product']['ordering_complete_link'])); // URL đón nhận kết quả 
//Alepay cung cấp 

$config = array(
    "apiKey" => "0COVspcyOZRNrsMsbHTdt8zesP9m0y", //Là key dùng để xác định tài khoản nào đang được sử dụng.
    "encryptKey" => "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCIh+tv4h3y4piNwwX2WaDa7lo0uL7bo7vzp6xxNFc92HIOAo6WPZ8fT+EXURJzORhbUDhedp8B9wDsjgJDs9yrwoOYNsr+c3x8kH4re+AcBx/30RUwWve8h/VenXORxVUHEkhC61Onv2Y9a2WbzdT9pAp8c/WACDPkaEhiLWCbbwIDAQAB", //Là key dùng để mã hóa dữ liệu truyền tới Alepay.
    "checksumKey" => "hjuEmsbcohOwgJLCmJlf7N2pPFU1Le", //Là key dùng để tạo checksum data.
    "callbackUrl" => URL_CALLBACK,
    "env" => "test",
);
?>