<?php
// Conectar a la base de datos (ajusta tus parámetros)
$mysqli = new mysqli("localhost", "usuario", "contraseña", "Cancion");

if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

// Datos de la canción demo
$titulo = 'Canción Demo';
$artista = 'Artista Demo';
$url = 'audio/demo_cancion.mp3';

// Insertar la canción demo
$query = "INSERT INTO canciones (titulo, artista, url) VALUES ('$titulo', '$artista', '$url')";

if ($mysqli->query($query) === TRUE) {
    echo "Canción demo agregada correctamente.";
} else {
    echo "Error: " . $query . "<br>" . $mysqli->error;
}

// Cerrar la conexión
$mysqli->close();
?>
