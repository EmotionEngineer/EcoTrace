<?php
include('qrcode/qrlib.php'); 

if (!isset($_GET['contract_key'])) {
    http_response_code(400);
    echo 'Ошибка: не указан ключ контракта.';
    exit;
}

$contractKey = $_GET['contract_key'];

// выводим изображение напрямую
QRcode::png($contractKey);
?>