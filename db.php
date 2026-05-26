<?php
$host     = "localhost";
$username = "root";
$password = "";
$database = "notebook";
$port     = 3306;
$charset  = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$database;port=$port;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
    PDO::ATTR_EMULATE_PREPARES   => false,                  
];

try {
    $conn = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Database connection failed safely: " . $e->getMessage());
}
?>