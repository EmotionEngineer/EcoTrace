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

// Запрос на получение всех уникальных товаров
$stmt = $db->prepare("SELECT DISTINCT id, product_name, product_image_path, calories, product_type, product_quantity, product_price, region FROM Contracts");
$stmt->execute();

// Получение результатов
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ярмарка - EcoTrace</title>
    <meta charset="utf-8">
    <!-- Materialize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Materialize Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Materialize JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <style>
        #logo {
            width: 200px;
            height: auto;
        }
        .nav-wrapper {
            background-color: #ffa618;
        }
        .brand-logo {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }
        .card {
            height: 100%;
        }
    </style>
</head>
<body>
<header>
    <nav>
        <div class="nav-wrapper">
            <a href="index.html" class="brand-logo center"><img id="logo" src="logo.png" alt="На главную"></a>
            <ul id="nav-mobile" class="right hide-on-med-and-down">
                <li><a href="cart.php" class="waves-effect waves-light btn"><i class="material-icons left">shopping_cart</i>Корзина (<span id="cart-quantity"><?php echo array_sum($_SESSION['cart']); ?></span>)</a></li>
            </ul>
        </div>
    </nav>
</header>
<main>
    <div class="container">
        <h1 class="center-align">Ярмарка</h1>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col s12 m6 l4">
                    <div class="card">
                        <div class="card-image">
                            <img src="<?php echo $product['product_image_path']; ?>">
                        </div>
                        <div class="card-content">
                            <span class="card-title"><?php echo $product['product_name']; ?></span>
                            <p>Калории: <?php echo $product['calories']; ?></p>
                            <p>Тип продукта: <?php echo $product['product_type']; ?></p>
                            <p>Количество: <?php echo $product['product_quantity']; ?></p>
                            <p>Цена: <?php echo $product['product_price']; ?></p>
                            <p>Регион: <?php echo $product['region']; ?></p>
                        </div>
                        <div class="card-action">
                            <a href="#" class="add-to-cart waves-effect waves-light btn" data-id="<?php echo $product['id']; ?>">Добавить в корзину</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>
<script>
    $(document).ready(function() {
        $('.add-to-cart').click(function(e) {
            e.preventDefault();

            var product_id = $(this).data('id');

            $.ajax({
                url: 'add_to_cart.php',
                type: 'GET',
                data: { id: product_id },
                success: function(result) {
                    // Обновить количество товаров в корзине
                    $('#cart-quantity').text(result);
                }
            });
        });
    });
</script>
</body>
</html>
