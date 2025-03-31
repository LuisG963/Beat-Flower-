<?php
// Conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$dbname = "Canciones";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

// Obtener los datos del último álbum ingresado
$sql = "SELECT * FROM albums_info ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Mostrar los datos
    $row = $result->fetch_assoc();
    $recordLabel = $row['record_label'];
    $songName = $row['song_name'];
    $genre = $row['genre'];
    $composerName = $row['composer_name'];
    $imagePath = $row['album_image'];

    // Crear el HTML para el álbum
    $albumHTML = "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Mis Álbumes</title>
        <link rel='stylesheet' href='styles.css'>
    </head>
    <body>
        <div class='sidebar'>
            <h2>Menú</h2>
            <ul>
                <li class='dropdown'>
                    <ul>
                        <li><a href='index.html'>Inicio</a></li>
                    </ul>
                    <a href='javascript:void(0)' class='dropbtn'>Álbumes <span class='arrow'>▼</span></a>
                    <ul class='dropdown-content'>
                        <li><a href='#'>Artista 1</a></li>
                        <li><a href='#'>Compositor 1</a></li>
                        <li><a href='#'>Género 1</a></li>
                        <li><a href='#'>Disquera 1</a></li>
                        <li><a href='#'>Playlist 1</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        
        <div class='main-content'>
            <header>
                <h1>Mis Álbumes</h1>
            </header>
            <section class='albums-container'>
                <div class='album'>
                    <img src='../$imagePath' alt='$songName'>
                    <p>$songName - $genre</p>
                    <a href='album.html'>
                        <button class='conocer-btn'>Conocer mi álbum</button>
                    </a>
                </div>
            </section>
        </div>

        <div class='add-album-container'>
            <button class='add-album-btn' onclick='window.location.href=\"agregar_album.html\"'>
                <span class='plus-icon'>+</span> Agregar Álbum
            </button>
        </div>

    </body>
    </html>";

    // Guardar el HTML generado en un archivo
    file_put_contents("nuevo_album.html", $albumHTML);
    echo "✅ ¡Álbum creado exitosamente!";
} else {
    echo "❌ No se encontraron registros para crear el álbum.";
}

$conn->close();
?>
