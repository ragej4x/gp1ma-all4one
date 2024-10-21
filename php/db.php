<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connection details extracted from your connection string
$host = 'autorack.proxy.rlwy.net'; // Host
$dbname = 'gp1ma_db'; // Database name
$user = 'jimbot'; // Username
$pass = 'NNLBeuMGYlyLIRptsCxYOLYLQNBRQzpV'; // Password

try {
    $pdo = new PDO("mysql:host=$host;port=22208;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful!";
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage()); // Log the error
    die("Database connection failed. Check the logs for more details.");
}
