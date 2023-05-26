<?php
session_start();

// Проверяем, вошел ли пользователь в систему
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}

$db_host = 'localhost';
$db_database = 'a0775494_ecotrace';
$db_username = 'a0775494_ecotrace';
$db_password = 'Osaka@123';

try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_database;charset=utf8", $db_username, $db_password);
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION["user_id"]]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Личный кабинет - EcoTrace</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .navbar {
            margin-bottom: 20px;
        }

        .container {
            max-width: 960px;
        }

        .btn-primary {
            background-color: #388e3c;
            border-color: #2e7d32;
        }

        .btn-primary:hover {
            background-color: #2e7d32;
            border-color: #1b5e20;
        }
        
        .user-info {
            font-size: 1.1em;
        }
        .user-info span {
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href=".">EcoTrace</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="desk.php">Главная</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="desk-contracts.html">Контракты</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Выйти</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <h2 class="mb-4">Личный кабинет</h2>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Добро пожаловать, <?php echo $user["first_name"] . " " . $user["last_name"]; ?>!</h4>
                <p class="user-info">Тип организации: <span><?= htmlspecialchars($user["provider_type"]) ?></span></p>
                <p class="user-info">ИНН: <span><?= htmlspecialchars($user["inn"]) ?></span></p>
                <p class="user-info">Регион: <span><?= htmlspecialchars($user["region"]) ?></span></p>
                <p class="user-info">Электронная почта: <span><?= htmlspecialchars($user["email"]) ?></span></p>
                <p class="user-info">Номер телефона: <span><?= htmlspecialchars($user["phone_number"]) ?></span></p>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
