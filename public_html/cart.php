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

// Получение товаров в корзине
$cart_products = [];
$total_price = 0;
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    foreach ($_SESSION['cart'] as $id => $quantity) {
        $stmt = $db->prepare("SELECT * FROM Contracts WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $product['quantity'] = $quantity;
            $total_price += $product['product_price'] * $quantity;
            $cart_products[] = $product;
        }
    }
}

// Расчет скидки
$discount = 0;
if ($total_price > 10000) {
    $discount = $total_price * 0.1; // 10% скидка
    $total_price -= $discount;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Оформление заказа</title>
    <meta charset="utf-8">
    <!-- Materialize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Materialize JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <style>
        .input-field {
            margin-top: 20px;
        }
        .btn {
            margin-top: 20px;
        }
        .navbar {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<nav>
    <div class="nav-wrapper">
        <a href="index.html" class="brand-logo">EcoTrace</a>
        <ul id="nav-mobile" class="right hide-on-med-and-down">
            <li><a href="shop.php">Вернуться на ярмарку</a></li>
        </ul>
    </div>
</nav>
<div class="container">
    <h1 class="center-align">Оформление заказа</h1>
    <h5 class="center-align">Скидка 10% предоставляется для покупок свыше 10000 руб.</h5>
    <?php foreach ($cart_products as $product): ?>
        <div class="row">
            <div class="col s12">
                <h5><?php echo $product['product_name']; ?> (<?php echo $product['quantity']; ?>)</h5>
                <p>Цена: <?php echo $product['product_price']; ?></p>
                <a href="remove_from_cart.php?id=<?php echo $product['id']; ?>" class="btn red">Удалить из корзины</a>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="row">
        <div class="col s12">
            <?php if ($discount > 0): ?>
                <h6>Скидка: <?php echo $discount; ?></h6>
            <?php endif; ?>
            <h4>Общая сумма: <?php echo $total_price; ?></h4>
            <a href="clear_cart.php" class="btn red">Очистить корзину</a>
        </div>
    </div>
    
    <!-- Форма оплаты и доставки -->
    <div class="row">
        <form class="col s12" action="checkout.php" method="POST">
            <div class="input-field col s6">
                <input id="email" type="email" class="validate" name="email" required>
                <label for="email">Email</label>
            </div>
            <div class="input-field col s6">
                <input id="address" type="text" class="validate" name="address" required>
                <label for="address">Адрес доставки</label>
            </div>
            <div class="input-field col s6">
                <input id="card_number" type="text" class="validate" name="card_number">
                <label for="card_number">Номер карты</label>
            </div>
            <div class="input-field col s6">
                <select name="payment_method" required>
                    <option value="" disabled selected>Выберите способ оплаты</option>
                    <option value="credit_card">Кредитная карта</option>
                    <option value="cash">Наличные при доставке</option>
                </select>
                <label>Способ оплаты</label>
            </div>
            <div class="input-field col s6">
                <select name="subscription" required>
                    <option value="" disabled selected>Выберите подписку</option>
                    <option value="weekly">Каждую неделю</option>
                    <option value="monthly">Каждый месяц</option>
                    <option value="seasonal">Каждый сезон</option>
                </select>
                <label>Подписка на продуктовую корзину</label>
            </div>
            <div class="col s6">
                <button class="btn waves-effect waves-light" type="submit" name="action">Оформить заказ
                    <i class="material-icons right"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Инициализация выпадающего списка Materialize -->
<script>
    $(document).ready(function(){
        $('select').formSelect();
    });
</script>
</body>
</html>
