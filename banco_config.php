<?php
$host = 'localhost';
$db = 'hospital_db';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Falha de conexão: " . $conn->connect_error);
}

$conn->close();
?>
