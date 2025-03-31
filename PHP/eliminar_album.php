<?php
if (isset($_GET['id'])) {
    $album_id = intval($_GET['id']);  // Asegúrate de convertir el id a entero por seguridad

    // Configurar la conexión con la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "music_collection";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Primero, eliminar los datos relacionados en 'albums_info'
    $sql_delete_info = "DELETE FROM albums_info WHERE album_id = ?";
    $stmt_info = $conn->prepare($sql_delete_info);
    $stmt_info->bind_param("i", $album_id);
    $stmt_info->execute();
    $stmt_info->close();

    // Luego, eliminar el álbum de la tabla 'albums'
    $sql_delete_album = "DELETE FROM albums WHERE id = ?";
    $stmt_album = $conn->prepare($sql_delete_album);
    $stmt_album->bind_param("i", $album_id);
    $stmt_album->execute();

    if ($stmt_album->affected_rows > 0) {
        echo "Álbum eliminado correctamente.";
    } else {
        echo "Hubo un error al eliminar el álbum.";
    }

    $stmt_album->close();
    $conn->close();
    
    // Redirigir de vuelta a la página de álbumes
    header("Location: ../PHP/album.php");
    exit;  // Asegúrate de detener la ejecución después de redirigir
}
?>
