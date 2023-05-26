<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Трекинг продуктов</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .info-card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="jumbotron">
            <h1 class="display-4">Привет, дорогой пользователь!</h1>
            <p class="lead">Сегодня мы представляем вам уникальный взгляд на путь вашего заказа. Этот путь не просто линия от точки А к точке Б. Это сложный маршрут, полный удивительных историй о людях, местах и процессах, которые привели ваш заказ к вам.</p>
            <hr class="my-4">
            <p>На этой странице вы можете увидеть, кто является производителем, посредником и получателем вашего товара. Вы увидите имена организаций, их регионы и даты производства товара. Это ваш шанс ощутить связь с каждым участником процесса и оценить важность их ролей.</p>
        </div>
        <?php
        $db_host = 'localhost';
        $db_database = 'a0775494_ecotrace';
        $db_username = 'a0775494_ecotrace';
        $db_password = 'Osaka@123';

        try {
            $conn = new PDO("mysql:host=$db_host;dbname=$db_database;charset=utf8", $db_username, $db_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("ERROR: Could not connect. " . $e->getMessage());
        }

        $contractKey = $_GET['contract_key'] ?? '';

        if ($contractKey != '') {
            $stmt = $conn->prepare('SELECT * FROM Contracts WHERE contract_key = :contractKey');
            $stmt->execute(['contractKey' => $contractKey]);
            $contract = $stmt->fetch();

            if ($contract) {
                $stmt = $conn->prepare('SELECT * FROM users WHERE id = :id');
                $stmt->execute(['id' => $contract['user_id']]);
                $supplier = $stmt->fetch();

                echo '<div class="card info-card">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">Поставщик: ' . $supplier['organization_name'] . '</h5>';
                echo '<h6 class="card-subtitle mb-2 text-muted">Регион: ' . $supplier['region'] . '</h6>';
                echo '<p class="card-text">ИНН: ' . $supplier['inn'] . '</p>';
                echo '</div>';
                echo '</div>';

                $stmt = $conn->prepare('SELECT * FROM users WHERE inn = :inn');
                $stmt->execute(['inn' => $contract['receiver_inn']]);
                $receiver = $stmt->fetch();

                $stmt = $conn->prepare('SELECT * FROM users WHERE inn = (SELECT receiver_inn FROM Contracts WHERE contract_key = :contractKey)');
                $stmt->execute(['contractKey' => $contractKey]);
                $mediator = $stmt->fetch();

                if ($mediator) {
                    echo '<div class="card info-card">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">Посредник: ' . $mediator['organization_name'] . '</h5>';
                    echo '<h6 class="card-subtitle mb-2 text-muted">Регион: ' . $mediator['region'] . '</h6>';
                    echo '</div>';
                    echo '</div>';
                }

                echo '<div class="card info-card">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">Получатель: ' . $receiver['organization_name'] . '</h5>';
                echo '<h6 class="card-subtitle mb-2 text-muted">Регион: ' . $receiver['region'] . '</h6>';
                echo '</div>';
                echo '</div>';

                echo '<div class="card info-card">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">Клиент-Потребитель</h5>';
                echo '</div>';
                echo '</div>';
            } else {
                echo 'Контракт не найден.<br>';
            }
        } else {
            echo 'Ключ контракта не указан.<br>';
        }
        ?>
    </div>
</body>
</html>
