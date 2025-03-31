<?php
session_start();

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
    $albumName = $_POST["albumName"];
    $recordLabel = $_POST["recordLabel"];
    $songName = $_POST["songName"];
    $genre = $_POST["genre"];
    $composerName = $_POST["composerName"];
    
    // Verificación de la imagen
    if (isset($_FILES["albumImage"]) && $_FILES["albumImage"]["error"] == 0) {
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

        // Asegúrate de que la imagen no esté vacía
        if (empty($imageData)) {
            die("❌ La imagen está vacía.");
        }

        // Preparar la sentencia para actualizar los datos, incluyendo la imagen
        $stmtUpdate = $conn->prepare("UPDATE albums_info SET album_image = ?, record_label = ?, song_name = ?, genre = ?, composer_name = ? WHERE album_id = ?");
        $stmtUpdate->bind_param("sssssi", $albumName, $recordLabel, $songName, $genre, $composerName, $albumId);

        // Enviar los datos binarios de la imagen
        $stmtUpdate->send_long_data(0, $imageData);

        if ($stmtUpdate->execute()) {
            echo "✅ Datos actualizados correctamente en 'albums_info'.";
            header("Location: Ver_album.php"); // Redirige a Ver_album.php
            exit();
        } else {
            echo "❌ Error al actualizar los datos en 'albums_info': " . $conn->error;
        }

        $stmtUpdate->close();
    } else {
        // Si no se subió una nueva imagen, actualizamos solo los datos del álbum
        $stmtUpdate = $conn->prepare("UPDATE albums_info SET record_label = ?, song_name = ?, genre = ?, composer_name = ? WHERE album_id = ?");
        $stmtUpdate->bind_param("ssssi", $recordLabel, $songName, $genre, $composerName, $albumId);

        if ($stmtUpdate->execute()) {
            echo "✅ Datos actualizados correctamente en 'albums_info' sin cambiar la imagen.";
            header("Location: Ver_album.php"); // Redirige a Ver_album.php
            exit();
        } else {
            echo "❌ Error al actualizar los datos en 'albums_info': " . $conn->error;
        }

        $stmtUpdate->close();
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Álbum</title>
    <link rel="stylesheet" href="../CSS/Datos.css">
    <script>
        function previewImage(event) {
            const fileInput = document.getElementById('albumImage');
            const output = document.getElementById('albumImagePreview');
            const fileName = document.getElementById('fileName');
            const file = fileInput.files[0];

            // Mostrar vista previa de la imagen
            output.src = URL.createObjectURL(file);

            // Mostrar el nombre del archivo
            fileName.textContent = file.name;
        }
    </script>
</head>
<body>

<form action="../PHP/Actualizar_album2.php" method="POST" enctype="multipart/form-data">
    <div class="form-container">
        <div class="image-container">
            <!-- Vista previa de la imagen y nombre del archivo -->
            <div id="preview-container">
                <img id="albumImagePreview" src="" alt="Vista previa de la imagen" style="width: 100px; height: 100px; margin-bottom: 10px;">
                <p id="fileName">Ningún archivo seleccionado</p>
            </div>

            <!-- Selección de archivo -->
            <label for="albumImage">Imagen del Álbum:</label>
            <input type="file" name="albumImage" id="albumImage" accept="image/*" onchange="previewImage(event)" required>
        </div>

        <div class="info-container">
            <label for="albumName">Nombre del Álbum:</label>
            <input type="text" name="albumName" id="albumName" required>

            <label for="recordLabel">Nombre de la Disquera:</label>
            <input type="text" name="recordLabel" id="recordLabel" required>

            <label for="songName">Nombre de la Canción:</label>
            <input type="text" name="songName" id="songName" required>

            <label for="genre">Género:</label>
            <input type="text" name="genre" id="genre" required>

            <label for="composerName">Nombre del Compositor:</label>
            <input type="text" name="composerName" id="composerName" required>

            <button type="submit">Actualizar</button>
        </div>
    </div>
</form>

</body>
</html>
