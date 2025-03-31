<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "music_collection";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

// Verificar si se proporcionó un ID válido para eliminar
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $songId = $_GET['id'];

    // Preparar la consulta para eliminar la canción
    $stmt = $conn->prepare("DELETE FROM songs WHERE id = ?");
    $stmt->bind_param("i", $songId);

    if ($stmt->execute()) {
        echo "✅ Canción eliminada correctamente.";
    } else {
        echo "❌ Error al eliminar la canción: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "❌ ID de canción no válido.";
}

$conn->close();

// Redirigir de vuelta a add_music.php después de la eliminación
header("Location: add_music.php");
exit();
