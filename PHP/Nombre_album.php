<?php
session_start(); // Inicia la sesión

// Configuración y conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$dbname = "music_collection";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $albumName = $_POST['albumName'];

    // Verificar si el nombre del álbum ya existe
    $stmt = $conn->prepare("SELECT * FROM albums WHERE album_name = ?");
    $stmt->bind_param("s", $albumName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<div class="error-message">El álbum ya existe. Elige otro nombre.</div>';
        echo '<script>setTimeout(function() { window.history.back(); }, 3000);</script>';
    } else {
        // Insertar el nuevo álbum
        $stmt = $conn->prepare("INSERT INTO albums (album_name) VALUES (?)");
        $stmt->bind_param("s", $albumName);

        if ($stmt->execute()) {
            $album_id = $stmt->insert_id;  // Obtener el ID del álbum recién insertado
            $_SESSION['album_id'] = $album_id;  // Guardar el album_id en la sesión

            echo '<div class="success-message">¡El álbum fue creado exitosamente!</div>';
            echo '<script>setTimeout(function() { window.location.href = "../HTML/Datos_album.html"; }, 3000);</script>';
        } else {
            echo "❌ Error al guardar el álbum: " . $stmt->error;
        }
    }
    if (isset($_SESSION['album_id'])) {
        $album_id = $_SESSION['album_id'];
        echo "El album_id es: " . $album_id;  // Muestra el album_id almacenado en la sesión
    } else {
        echo "❌ Error: No se ha proporcionado un ID de álbum válido.";
        // Redirigir a otra página si no hay un ID válido
        header("Location: ../HTML/Agregar_Album.html"); // Redirige si no se encuentra el album_id
        exit();
    }
    $stmt->close();
    $conn->close();
}
?>



<!-- Estilo para el mensaje de éxito y error -->
<style>
    /* Imagen de fondo para toda la página */
    body {
        margin: 0;
        padding: 0;
        background-image: url('../img/Mensaje\ de\ registro.jpg'); /* Reemplaza con la ruta de tu imagen */
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Estilo para el contenedor del mensaje de éxito */
    .success-message, .error-message {
        width: 80%;
        max-width: 400px;
        padding: 20px;
        text-align: center;
        border-radius: 15px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        font-family: Arial, sans-serif;
    }

    /* Estilo para el mensaje de éxito */
    .success-message {
        background-color: #6EC1E4; /* Azul suave */
        color: white;
    }

    /* Estilo para el mensaje de error */
    .error-message {
        background-color: #FF6347; /* Rojo */
        color: white;
    }

    .success-message h2, .error-message h2 {
        font-size: 28px;
        margin-bottom: 15px;
    }

    .success-message p {
        font-size: 18px;
        margin-top: 0;
    }
</style>
