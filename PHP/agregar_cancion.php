<?php
session_start(); // Inicia la sesión

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$dbname = "music_collection"; // Nombre de la base de datos

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

// Verificar si hay un album_id guardado en la sesión
if (!isset($_SESSION['album_id'])) {
    die("❌ Error: No se ha encontrado un ID de álbum válido en la sesión.");
}

$albumId = $_SESSION['album_id']; // Recuperar el album_id de la sesión

// Variable para mostrar el mensaje
$message = "";

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $songName = isset($_POST["songName"]) ? $_POST["songName"] : null; // Asegúrate de que el nombre de la canción sea válido

    // Verificación de la canción
    if (empty($songName)) {
        $message = "❌ Error: El nombre de la canción no puede estar vacío.";
    }

    // Verificación del archivo
    if (!isset($_FILES["songFile"]) || $_FILES["songFile"]["error"] != 0) {
        $message = "❌ Error: No se ha subido ningún archivo de canción o hubo un problema con el archivo.";
    }

    // Subir el archivo al directorio correspondiente
    $uploadDir = "../music/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = basename($_FILES["songFile"]["name"]);
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES["songFile"]["tmp_name"], $filePath)) {
        // Insertar la canción en la base de datos
        $sql = "INSERT INTO songs (song_nem, song_audio, album_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $songName, $fileName, $albumId);

        if ($stmt->execute()) {
            $message = "✅ Canción agregada con éxito";
        } else {
            $message = "❌ Error al insertar la canción en la base de datos: " . $conn->error;
        }

        $stmt->close();
    } else {
        $message = "❌ Error al subir el archivo.";
    }
}

$conn->close();
?>
<style>
        /* Estilos para el contenedor del mensaje */
        .message-container {
            margin-bottom: 20px;
            font-weight: bold;
        }
        /* Estilos para el formulario */
        .form-container {
            width: 300px;
            margin: 0 auto;
        }
        .info-container {
            margin-bottom: 10px;
        }
        .image-container {
            text-align: center;
        }
    </style>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Canción</title>
    <link rel="stylesheet" href="../CSS/Datos.css">
</head>
<body>
    
<form action="agregar_cancion.php" method="POST" enctype="multipart/form-data">
<div class="form-container">
    <div class="image-container"><br>
        <div class="info-container">
             <!-- Mostrar mensaje de éxito o error -->
             <div class="message-container">
                <?php echo $message; ?>
            </div>

            <label for="songName">Nombre de la Canción:</label>
            <input type="text" name="songName" id="songName" required><br>

            <label for="songFile">Archivo de Canción:</label>
            <input type="file" name="songFile" id="songFile" required><br>

            <button type="submit">Agregar Canción</button>
            
            <!-- Botón para regresar a add_music.php -->
            <a href="add_music.php">
                <button type="button">Regresar al Reproductor</button>
            </a>
        </div>
    </div>
</div>
</form>
</body>
</html>
