<?php
session_start(); // Inicia la sesión

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$dbname = "music_collection"; // Cambia al nombre correcto de tu base de datos

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

// Verificar si hay un album_id guardado en la sesión
if (!isset($_SESSION['album_id'])) {
    die("❌ Error: No se ha encontrado un ID de álbum válido en la sesión.");
}

$albumId = $_SESSION['album_id']; // Recuperar el album_id de la sesión

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $recordLabel = $_POST["recordLabel"];
    $songName = $_POST["songName"];
    $genre = $_POST["genre"];
    $composerName = $_POST["composerName"];
    
    // Verificación de la imagen
    if (!isset($_FILES["albumImage"]) || $_FILES["albumImage"]["error"] != 0) {
        die("❌ Error: No se ha subido ninguna imagen o hubo un problema con el archivo.");
    }

    $uploadDir = "../uploads/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = basename($_FILES["albumImage"]["name"]);
    $targetFile = $uploadDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    // Validar tipo de imagen
    if (!in_array($imageFileType, $allowedTypes)) {
        die("❌ Error: Solo se permiten imágenes JPG, JPEG, PNG y GIF.");
    }

    // Validar tamaño de archivo (opcional)
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($_FILES["albumImage"]["size"] > $maxSize) {
        die("❌ Error: El archivo de imagen es demasiado grande. El tamaño máximo permitido es 5MB.");
    }

    // Leer la imagen como binario
    $imageData = file_get_contents($_FILES["albumImage"]["tmp_name"]);

    // Insertar los datos en la tabla 'albums_info' (incluyendo la imagen como BLOB)
    $stmt = $conn->prepare("INSERT INTO albums_info (album_id, album_image, record_label, song_name, genre, composer_name) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    
    // Usar 'b' para el BLOB (binario) en el tipo de datos
    $stmt->bind_param("ibssss", $albumId, $null, $recordLabel, $songName, $genre, $composerName);

    $null = NULL;
    $stmt->send_long_data(1, $imageData); // Enviar los datos binarios a la base de datos

    if ($stmt->execute()) {
        echo "✅ Datos guardados exitosamente en 'albums_info'.";
        header("Location: Ver_album.php"); // Redirige a Ver_album.php
        exit();
    } else {
        echo "❌ Error al guardar los datos en 'albums_info': " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
