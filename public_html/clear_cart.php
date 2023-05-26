<?php
session_start();

// Очистка корзины
$_SESSION['cart'] = [];

header('Location: cart.php');
exit;
?>