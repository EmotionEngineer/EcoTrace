<?php
session_start();
if(isset($_SESSION["user_id"])) {
    header("Location: desk.php");
    exit();
}

if(!isset($_POST['email'])) {
    header("Location: registration.html");
    exit();
}

$db_host = 'localhost';
$db_database = 'a0775494_ecotrace';
$db_username = 'a0775494_ecotrace';
$db_password = 'Osaka@123';

try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_database;charset=utf8", $db_username, $db_password);
} catch(PDOException $e) {
    echo "ERROR";
}

$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$lastName = $_POST['lastName'];
$firstName = $_POST['firstName'];
$middleName = $_POST['middleName'];
$providerType = $_POST['providerType'];
$inn = $_POST['inn'];
$phoneNumber = $_POST['phoneNumber'];
$region = $_POST['region'];
$epSignature = isset($_POST['epSignature']) ? 1 : 0;

// Проверка и обработка файла устава
$organizationRules = 0;
if (isset($_FILES['organizationRules'])) {
    $fileSize = $_FILES['organizationRules']['size'];
    $fileType = pathinfo($_FILES['organizationRules']['name'], PATHINFO_EXTENSION);
    
    if ($fileSize <= 100000 && strtolower($fileType) == 'pdf') {
        $organizationRules = 1;
    }
}

$query = $db->prepare("INSERT INTO users (email, password, last_name, first_name, middle_name, provider_type, inn, phone_number, organization_rules, ep_signature, region) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$result = $query->execute([$email, $password, $lastName, $firstName, $middleName, $providerType, $inn, $phoneNumber, $organizationRules, $epSignature, $region]);

if ($result) {
    // Получаем последний добавленный ID
    $last_id = $db->lastInsertId();

    // Сохраняем ID в сессию
    $_SESSION["user_id"] = $last_id;

    // Перенаправляем пользователя на страницу desk.php
    header("Location: desk.php");
} else {
    header("Location: login.html");
}
?>