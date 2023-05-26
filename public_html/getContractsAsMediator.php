<?php
session_start();
$db_host = 'localhost';
$db_database = 'a0775494_ecotrace';
$db_username = 'a0775494_ecotrace';
$db_password = 'Osaka@123';

try {
    // Создаем новый экземпляр класса PDO для подключения к базе данных
    $conn = new PDO("mysql:host=$db_host;dbname=$db_database;charset=utf8", $db_username, $db_password);

    // Задаем режим обработки исключений
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Если возникла ошибка подключения, выводим ее
    echo $e->getMessage();
}

header('Content-Type: application/json');

// ID текущего пользователя, который будет использоваться для выбора контрактов
$user_id = $_SESSION['user_id']; // замените на способ получения ID пользователя в вашем приложении

try {
    // Получаем ИНН пользователя
    $sql = "SELECT inn FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
    $user_inn = $stmt->fetch(PDO::FETCH_ASSOC)['inn'];

    // Получаем контракты, где пользователь является посредником
    $sql = "SELECT * FROM Contracts WHERE receiver_inn = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_inn]);
    $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($contracts);
} catch (PDOException $e) {
    // Если возникает ошибка, отправьте её клиенту
    echo json_encode(["error" => $e->getMessage()]);
}
?>
