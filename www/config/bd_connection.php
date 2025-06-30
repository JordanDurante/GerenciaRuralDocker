<?php
$servername = "db";
$username = "root";
$password = "root";
$database = "gerenciarural";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $database, $port, null);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

?>