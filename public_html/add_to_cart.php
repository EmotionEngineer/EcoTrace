<?php
session_start();

$product_id = $_GET['id'];

if(isset($_SESSION['cart'])) {
    if(isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
} else {
    $_SESSION['cart'] = array($product_id => 1);
}

echo array_sum($_SESSION['cart']);
?>