<?php
$db_host = 'localhost';
$db_database = 'a0775494_ecotrace';
$db_username = 'a0775494_ecotrace';
$db_password = 'Osaka@123';

try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_database;charset=utf8", $db_username, $db_password);
} catch(PDOException $e) {
    echo "ERROR";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = $db->prepare("SELECT * FROM users WHERE email = :email");
    $query->bindParam(':email', $email);
    $query->execute();

    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['firstName'] = $user['first_name'];
        $_SESSION['lastName'] = $user['last_name'];
        header('Location: desk.php');
    } else {
        echo "Неверный адрес электронной почты или пароль!";
    }
}
?>