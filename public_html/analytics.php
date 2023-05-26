<?php
session_start();

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

// Get the user_id from the session
$user_id = $_SESSION['user_id'];

// Retrieve the total number of contracts for the user_id
$query = $conn->prepare("SELECT COUNT(*) AS total_contracts FROM Contracts WHERE user_id = :user_id");
$query->execute(['user_id' => $user_id]);
$total_contracts = $query->fetch()['total_contracts'];

// Retrieve the sum of sales per month
$query = $conn->prepare("SELECT DATE_FORMAT(production_date, '%Y-%m') as month, SUM(product_quantity * product_price) as total_sales FROM Contracts WHERE user_id = :user_id GROUP BY month");
$query->execute(['user_id' => $user_id]);
$monthly_sales = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Статистика производителя</title>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>
<body>
  <div class="container">
    <div class="section">
      <div class="row">
        <div class="col s12 center">
          <h3><i class="mdi-content-send brown-text"></i></h3>
          <h4>Статистика производителя</h4>
          <p class="center-align light">На данной странице представлена статистика производителя, включая общее количество контрактов и продажи по месяцам.</p>
          <h5>Общее количество контрактов: <?= $total_contracts ?></h5>
        </div>
      </div>
    </div>

    <canvas id="salesChart"></canvas>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?= json_encode(array_column($monthly_sales, 'month')) ?>,
        datasets: [{
          label: 'Продажи по месяцам',
          backgroundColor: 'rgb(255, 99, 132)',
          borderColor: 'rgb(255, 99, 132)',
          data: <?= json_encode(array_map('intval', array_column($monthly_sales, 'total_sales'))) ?>,
        }]
      },
      options: {
        responsive: false,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: false
          }
        }
      }
    });
    </script>
  </div>
</body>
</html>
