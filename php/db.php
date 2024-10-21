<?php
$host = 'mysql.railway.internal'; // Your Railway host
$dbname = 'gp1ma_db'; // Your Railway database name
$user = 'jimbot'; // Your Railway username
$pass = 'NNLBeuMGYlyLIRptsCxYOLYLQNBRQzpV'; // Your Railway password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
