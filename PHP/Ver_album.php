<?php
// Conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$dbname = "music_collection";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

// Consulta para obtener los datos almacenados
$sql = "SELECT * FROM albums_info ORDER BY id DESC LIMIT 1"; // Obtener el último registro insertado
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Mostrar los datos
    $row = $result->fetch_assoc();
    $recordLabel = $row['record_label'];
    $songName = $row['song_name'];
    $genre = $row['genre'];
    $composerName = $row['composer_name'];
    
    // Recuperar la imagen como binario y convertirla a base64
    $imageData = base64_encode($row['album_image']);
} else {
    echo "❌ No se encontraron registros.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Álbum</title>
    <link rel="stylesheet" href="../CSS/Datos.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .album-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 80%;
            max-width: 600px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .album-details {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .album-details p {
            font-size: 16px;
            color: #555;
        }
        .album-details img {
            max-width: 200px;
            border-radius: 8px;
            margin-top: 15px;
            align-self: center;
        }
        .album-details .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .form-group label {
            font-weight: bold;
            color: #333;
        }
        .form-group input {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            width: 100%;
            background-color: #f9f9f9;
        }
        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .button-container a {
            text-decoration: none;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #4CAF50; /* Botón verde para "Crear el Álbum" */
            font-size: 16px;
        }
        .button-container a.update-btn {
            background-color: #f39c12; /* Botón amarillo para "Actualizar Datos" */
        }
    </style>
</head>
<body>
<div class="album-container">
        <h1>Información del Álbum</h1>
        <div class="album-details">
            <div class="form-group">
                <label for="recordLabel">Etiqueta Discográfica:</label>
                <input type="text" id="recordLabel" value="<?php echo htmlspecialchars($recordLabel); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="songName">Nombre de la Canción:</label>
                <input type="text" id="songName" value="<?php echo htmlspecialchars($songName); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="genre">Género:</label>
                <input type="text" id="genre" value="<?php echo htmlspecialchars($genre); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="composerName">Compositor:</label>
                <input type="text" id="composerName" value="<?php echo htmlspecialchars($composerName); ?>" disabled>
            </div>
            <img src="data:image/jpeg;base64,<?php echo base64_encode($row['album_image']); ?>" alt="Imagen del álbum" class="album-image">
            <div class="button-container">
            <a href="../PHP/album.php">Crear el Álbum</a>
<!-- Cambia este enlace -->
<a href="../HTML/Actualizar_album.php?id=<?php echo $row['id']; ?>" class="update-btn">Actualizar Datos del Álbum</a>

        </div>
</body>
</html>

