<?php
$servername = "localhost";
$username = "root";
$password = "161904"; // Tu contraseña
$dbname = "algebritos_db";
$port = 3306;

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Configurar charset para acentos y caracteres especiales
$conn->set_charset("utf8mb4");
?>





