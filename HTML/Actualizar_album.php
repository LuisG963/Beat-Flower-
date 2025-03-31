<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Álbum</title>
    <link rel="stylesheet" href="../CSS/Datos.css"> <!-- Asegúrate de que este archivo CSS exista -->
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
        .main-content {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 80%;
            max-width: 600px;
        }
        header h1 {
            text-align: center;
            color: #333;
        }
        .album-form form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: bold;
            color: #333;
        }
        input[type="text"], input[type="file"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            width: 100%;
            background-color: #f9f9f9;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <header>
            <h1>Editar Información del Álbum</h1>
        </header>

        <section class="album-form">
        <?php
// Conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$dbname = "music_collection"; // Cambia al nombre correcto de tu base de datos

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

// Verificar si se pasa el ID del álbum a actualizar
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("❌ Error: No se proporcionó un ID válido.");
}

$id = $_GET['id'];

// Obtener los datos actuales del álbum desde la base de datos
$sql = "SELECT * FROM albums_info WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$album_info = $result->fetch_assoc();
$stmt->close();

if (!$album_info) {
    die("❌ Error: No se encontró el álbum con este ID.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capturar los datos del formulario
    $etiqueta = $_POST['etiqueta'];
    $cancion = $_POST['cancion'];
    $genero = $_POST['genero'];
    $compositor = $_POST['compositor'];

    // Validar que todos los campos estén llenos
    if (empty($etiqueta) || empty($cancion) || empty($genero) || empty($compositor)) {
        die("❌ Error: Todos los campos son obligatorios.");
    }

    // Manejo de la imagen (se almacena como BLOB)
    if (!empty($_FILES['imagen']['name'])) {
        // Verificar que el archivo se cargue correctamente
        if ($_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            // Leer el archivo de imagen en binario
            $image = file_get_contents($_FILES['imagen']['tmp_name']);
        } else {
            die("❌ Error al cargar el archivo de imagen.");
        }
    } else {
        // Si no se sube una nueva imagen, usar la imagen actual (si está disponible)
        $image = $album_info['album_image'];
    }

    // Actualizar los datos del álbum en la base de datos
    $sql = "UPDATE albums_info SET record_label = ?, song_name = ?, genre = ?, composer_name = ?, album_image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    // 'b' es para indicar que estamos manejando un BLOB
    $stmt->bind_param("ssssbi", $etiqueta, $cancion, $genero, $compositor, $image, $id);

    if ($stmt->execute()) {
        // Redirigir a Ver_album.php después de la actualización
        header("Location: ../PHP/Ver_album.php?id=" . $id);
        exit();
    } else {
        echo "❌ Error al actualizar el álbum: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>



        <form action="Actualizar_album.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
            <label for="etiqueta">Disquera:</label>
            <input type="text" id="etiqueta" name="etiqueta" value="<?php echo htmlspecialchars($album_info['record_label']); ?>" required><br><br>

            <label for="cancion">Nombre de la Canción:</label>
            <input type="text" id="cancion" name="cancion" value="<?php echo htmlspecialchars($album_info['song_name']); ?>" required><br><br>

            <label for="genero">Género:</label>
            <input type="text" id="genero" name="genero" value="<?php echo htmlspecialchars($album_info['genre']); ?>" required><br><br>

            <label for="compositor">Compositor:</label>
            <input type="text" id="compositor" name="compositor" value="<?php echo htmlspecialchars($album_info['composer_name']); ?>" required><br><br>

            <label for="imagen">Imagen del Álbum:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*"><br><br>

            <input type="submit" value="Actualizar Álbum">
        </form>

        <script>
            function validateForm() {
                const etiqueta = document.getElementById("etiqueta").value.trim();
                const cancion = document.getElementById("cancion").value.trim();
                const genero = document.getElementById("genero").value.trim();
                const compositor = document.getElementById("compositor").value.trim();
                const fileInput = document.getElementById("imagen");
                const filePath = fileInput.value;

                if (!/^[a-zA-Z ]{5,50}$/.test(etiqueta)) {
                    alert("❌ La etiqueta debe tener entre 5 y 50 caracteres.");
                    return false;
                }
                if (!/^[a-zA-Z ]{1,50}$/.test(cancion)) {
                    alert("❌ La canción debe tener entre 1 y 50 caracteres.");
                    return false;
                }
                if (!/^[a-zA-Z ]{1,25}$/.test(genero)) {
                    alert("❌ El género debe tener entre 1 y 25 caracteres.");
                    return false;
                }
                if (!/^[a-zA-Z ]{1,50}$/.test(compositor)) {
                    alert("❌ El compositor debe tener entre 1 y 50 caracteres.");
                    return false;
                }
                if (filePath) {
                    const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
                    if (!allowedExtensions.exec(filePath)) {
                        alert('❌ Solo puedes cargar imágenes (JPG, JPEG, PNG, GIF).');
                        fileInput.value = '';
                        return false;
                    }
                }
                return true;
            }
        </script>

        </section>
    </div>
</body>
</html>
