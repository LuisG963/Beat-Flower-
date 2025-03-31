<?php
// Archivo: conexion.php

$host = 'localhost';       // Servidor de la base de datos (usualmente 'localhost' para XAMPP)
$user = 'root';            // Usuario de la base de datos (por defecto en XAMPP es 'root')
$password = '';            // Contraseña del usuario (por defecto en XAMPP es vacío '')
$database = 'music_collection';  // Nombre de tu base de datos

// Crear conexión
$conn = new mysqli($host, $user, $password, $database);

// Verificar si la conexión falló
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Establecer conjunto de caracteres a utf8 para soportar acentos y caracteres especiales
$conn->set_charset("utf8");

?>
