<?php
session_start();

// Проверка наличия ID в запросе
if (!isset($_GET['id'])) {
    header('Location: cart.php');
    exit;
}

$id = $_GET['id'];

// Удаление товара из корзины
if (isset($_SESSION['cart'][$id])) {
    unset($_SESSION['cart'][$id]);
}

header('Location: cart.php');
exit;
?>