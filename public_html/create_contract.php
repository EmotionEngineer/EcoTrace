<?php
session_start();
// connect to the database
$servername = "localhost";
$username = "a0775494_ecotrace";
$password = "Osaka@123";
$dbname = "a0775494_ecotrace";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// generate a unique contract key
$contractKey = uniqid();

// upload product image
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["product_image"]["name"]);
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
$check = getimagesize($_FILES["product_image"]["tmp_name"]);
if($check === false) {
    die("File is not an image.");
}

// Check if file already exists
if (file_exists($target_file)) {
    die("Sorry, file already exists.");
}

// Check file size (5MB max)
if ($_FILES["product_image"]["size"] > 5000000) {
    die("Sorry, your file is too large.");
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
    die("Sorry, only JPG, JPEG & PNG files are allowed.");
}

// Try to upload file
if (!move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
    die("Sorry, there was an error uploading your file.");
}

// get user_id from session or form input
$user_id = $_SESSION['user_id']; // make sure user_id is set in the session when user logs in

// prepare an insert statement
$sql = "INSERT INTO Contracts (user_id, contract_key, receiver_inn, receiver_company, product_name, product_image_path, calories, product_type, product_quantity, product_price, production_date, region) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

// bind variables to the prepared statement as parameters
$stmt->bind_param("isssssisidss", $user_id, $contractKey, $_POST['receiver_inn'], $_POST['receiver_company'], $_POST['product_name'], $target_file, $_POST['calories'], $_POST['product_type'], $_POST['product_quantity'], $_POST['product_price'], $_POST['production_date'], $_POST['region']);

// execute the statement
if ($stmt->execute()) {
    // redirect to contracts page
    header("Location: desk-contracts.php");
} else {
    echo "Ошибка: " . $sql . "<br>" . $conn->error;
}

// close connection
$stmt->close();
$conn->close();
?>