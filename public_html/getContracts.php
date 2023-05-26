<?php
session_start(); // Начинаем сессию
header('Content-Type: application/json');

// connect to the database
$servername = "localhost";
$username = "a0775494_ecotrace";
$password = "Osaka@123";
$dbname = "a0775494_ecotrace";

// Создание подключения
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    echo $conn->connect_error;
}

// Получаем ID пользователя из сессии
$userId = $_SESSION['user_id']; // убедитесь, что user_id установлен в сессии после входа в систему

// Подготовка запроса. Предполагаем, что в вашей таблице contracts есть поле user_id, которое связывает контракты с пользователями
$stmt = $conn->prepare("SELECT id, product_name, contract_key FROM Contracts WHERE user_id = ?");
$stmt->bind_param('i', $userId); // 'i' означает, что $userId является целым числом

$stmt->execute();
$result = $stmt->get_result();

$contracts = [];

while ($row = $result->fetch_assoc()) {
    $contracts[] = $row;
}

echo json_encode($contracts); // Возвращаем контракты в формате JSON

$stmt->close();
$conn->close();
?>
