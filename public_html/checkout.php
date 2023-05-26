<?php
session_start();

$host = 'localhost'; // адрес сервера 
$database = 'a0775494_ecotrace'; // имя базы данных
$user = 'a0775494_ecotrace'; // имя пользователя
$password = 'Osaka@123'; // пароль

try {
    $db = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

// Получение и обработка данных формы
$email = $_POST['email'];
$address = $_POST['address'];
$card_number = $_POST['card_number'];
$payment_method = $_POST['payment_method'];
$subscription = $_POST['subscription'];

// Обработка заказа и отправка писем продавцам
foreach ($_SESSION['cart'] as $id => $quantity) {
    $stmt = $db->prepare("SELECT * FROM Contracts WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $seller_email = $product['seller_email'];
        $subject = "Новый заказ на EcoTrace";
        $message = "Вы получили новый заказ на {$product['product_name']} в количестве {$quantity}. Покупатель: {$email}. Адрес доставки: {$address}. Способ оплаты: {$payment_method}. Подписка: {$subscription}.";

        // Отправка письма
        mail($seller_email, $subject, $message);
    }
}

// Очистка корзины
$_SESSION['cart'] = [];

?>

<!DOCTYPE html>
<html>
<head>
    <title>Завершение заказа - EcoTrace</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
</head>
<body>
<div class="container">
    <h1 class="center-align">Завершение заказа</h1>
    <p class="center-align">Ваш заказ успешно оформлен. Информация о заказе отправлена продавцам.</p>
    <p class="center-align"><a href="shop.php" class="btn">Вернуться на ярмарку</a></p>
</div>
</body>
</html>
