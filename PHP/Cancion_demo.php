<?php
// Conectar a la base de datos (ajusta tus parámetros)
$mysqli = new mysqli("localhost", "usuario", "contraseña", "Cancion");

if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

// Insertar una canción demo
$query = "INSERT INTO canciones (titulo, artista, url) VALUES ('Canción Demo', 'Artista Demo', 'path_to_song.mp3')";
if ($mysqli->query($query) === TRUE) {
    echo "Canción demo agregada correctamente.";
} else {
    echo "Error: " . $query . "<br>" . $mysqli->error;
}

// Cerrar la conexión
$mysqli->close();
?>
