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

// Consulta para obtener todas las canciones del álbum actual
$sql = "SELECT * FROM songs WHERE album_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $albumId);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reproductor de Música</title>
    <link rel="stylesheet" href="../CSS/styles2.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #121212;
    margin: 0;
    padding: 0;
    color: #fff;
    display: flex;
    flex-direction: column;
    height: 100vh; /* Hace que el body ocupe toda la altura de la pantalla */
}

/* Barra de navegación */
nav {
    background-color: #1a1a1a;
    padding: 15px 20px;
    position: fixed;
    width: 100%;
    top: 0;
    left: 0;
    z-index: 100;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3); /* Sombra para un efecto flotante */
}

/* Lista de elementos en la barra de navegación */
nav ul {
    list-style: none;
    display: flex;
    margin: 0;
    padding: 0;
}

/* Estilo para cada elemento de la lista */
nav ul li {
    margin: 0 20px;
}

/* Estilo para los enlaces */
nav ul li a {
    text-decoration: none;
    color: #fff;
    font-size: 18px;
    font-weight: 600;
    padding: 10px 20px;
    transition: color 0.3s ease, transform 0.3s ease;
}

/* Efecto hover para los enlaces */
nav ul li a:hover {
    color: #1DB954; /* Verde de Spotify */
    transform: scale(1.1); /* Animación de escala */
}
nav ul li a.active {
    color: #1DB954;
    font-weight: bold;
    text-decoration: underline;
}

.container {
    display: flex;
    justify-content: space-between;
    padding: 80px 10px 10px; /* Ajuste para no tapar contenido con la navbar */
    flex-grow: 1;
    gap: 20px; /* Añadido para dar un mejor espacio entre las columnas */
}

.left-column, .right-column {
    width: 49%; /* Ajustado para que las columnas tengan casi el mismo tamaño */
}

.left-column {
    background-color: #222; /* Fondo oscuro para la tabla */
    border-radius: 8px;
    padding: 10px;
    overflow-x: auto;
}

.right-column {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    background-color: #222;
    border-radius: 8px;
    padding: 20px;
}

.vinyl {
    width: 300px;
    height: 300px;
    border-radius: 50%;
    transition: all 1s ease;
    margin-top: 20px;
}

.spin {
    animation: spin 3s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.table-container {
    background-color: #333; /* Fondo más claro para el contenedor de la tabla */
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    width: 100%;
    max-width: 1200px; /* Max ancho para evitar que se estire demasiado */
    margin-bottom: 50px; /* Espaciado para evitar que toque el footer */
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px; /* Espaciado entre el contenedor y la tabla */
}

th, td {
    padding: 10px;
    text-align: left;
    color: white;
}

th {
    background-color: #444; /* Fondo ligeramente diferente para los encabezados */
}

td:first-child {
    width: 50px;
    text-align: center;
}

.add-song-btn {
    padding: 15px 30px;
    background-color: #1DB954;
    color: white;
    border: none;
    font-size: 18px;
    border-radius: 30px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 20px;
}

.add-song-btn:hover {
    background-color: #1ed760;
    transform: scale(1.05);
}

.player {
    width: 80%;
    text-align: center;
    margin-top: 50px;
}

.controls {
    margin-top: 30px;
    display: flex;
    justify-content: center;
    gap: 25px;
    align-items: center;
}

.controls button {
    width: 50px;
    height: 50px;
    background-color: #1DB954;
    color: white;
    border: none;
    font-size: 25px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
}

.controls button:hover {
    background-color: #1ed760;
    transform: scale(1.1);
}

.progress-container {
    width: 100%;
    height: 8px;
    background-color: #333;
    border-radius: 5px;
    margin-top: 20px;
    cursor: pointer;
}

.progress-bar {
    width: 0;
    height: 100%;
    background-color: #1DB954;
    border-radius: 5px;
}

.song-name {
    cursor: pointer;
    text-decoration: underline;
    color: #1DB954;
}

.song-name:hover {
    color: #1ed760;
}

.footer {
    background-color: #1a1a1a;
    color: white;
    text-align: center;
    padding: 15px 0;
    width: 100%;
    margin-top: auto; /* Empuja el footer al final */
}

.footer a {
    text-decoration: none;
    color: #1DB954;
    margin: 0 10px;
    font-weight: 600;
}

.footer a:hover {
    color: #1ed760;
}
/* Efecto hover para el botón */
nav ul li button.add-song-btn:hover {
    background-color: #1ed760;
    transform: scale(1.05);
}
/* Estilo para el botón "Agregar Canción" */
nav ul li button.add-song-btn {
    background-color: #1DB954;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 30px;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-left: auto; /* Mueve el botón a la derecha */
}

    </style>
</head>
<body>
    <div>
    <nav>
    <ul>
        <li><a href="../HTML/index.html">Inicio</a></li>
        <li><a href="album.php">Mis Álbumes</a></li>
        <li><button class="add-song-btn" onclick="location.href='agregar_cancion.php'">Agregar Canción</button></li>
    </ul>
</nav><br><br>

    <div class="container">
        <!-- Columna izquierda para la lista de canciones -->
        <div class="left-column">
            <table border="1">
            <thead>
    <tr>
        <th>Número</th>
        <th>Nombre de la Canción</th>
        <th>Duración</th>
        <th>Eliminar</th>
    </tr>
</thead>
<tbody>
    <?php
    // Mostrar las canciones de la base de datos
    $songs = [];
    $counter = 1;
    while ($row = $result->fetch_assoc()) {
        $songs[] = $row; // Guardamos las canciones en un array
        $audioFile = '../music/' . htmlspecialchars($row['song_audio']);
        echo "<tr>";
        echo "<td>" . $counter++ . "</td>";
        echo "<td><span class='song-name' data-audio='$audioFile'>" . htmlspecialchars($row['song_nem']) . "</span></td>";
        echo "<td class='song-duration' data-audio='$audioFile'>00:00</td>"; // Valor por defecto
        echo "<td><a href='eliminar_cancion.php?id=" . $row['id'] . "' onclick='return confirm(\"¿Estás seguro de que deseas eliminar esta canción?\")'><img src='../img/trash-icon.png' alt='Eliminar' style='width: 20px; cursor: pointer;'></a></td>";
        echo "</tr>";
    }
    ?>
</tbody>
            </table>
        </div>

        <div class="right-column">
    <!-- Imagen del disco -->
    <img src="../img/disco-vinilo2.png" id="vinyl" class="vinyl" alt="Disco de vinilo">
    
    <!-- Reproductor -->
    <div class="player" id="audioPlayer">
        <div class="controls">
            <button id="prevBtn">⏮</button>
            <button id="playPauseBtn">▶</button>
            <button id="nextBtn">⏭</button>
        </div>
        <div class="progress-container" id="progressContainer">
            <div class="progress-bar" id="progressBar"></div>
        </div>
    </div>
</div>
</div>
</div>

    <script>
       const songs = <?php echo json_encode($songs); ?>;
        let currentSongIndex = 0;
        const playPauseBtn = document.getElementById('playPauseBtn');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const songNames = document.querySelectorAll('.song-name');
        const songDurations = document.querySelectorAll('.song-duration');
        const progressBar = document.getElementById('progressBar');
        const progressContainer = document.getElementById('progressContainer');
        const vinyl = document.getElementById('vinyl');
        let audio = new Audio();
        function loadSong(index) {
    const song = songs[index];
    audio.src = '../music/' + song.song_audio;
    audio.load();
    playPauseBtn.textContent = "▶";
    vinyl.classList.remove("spin");
    currentSongIndex = index;
}
        function loadAndPlaySong(index) {
            const song = songs[index];
            audio.src = '../music/' + song.song_audio;
            audio.load();
            audio.play();
            playPauseBtn.textContent = "⏸";
            vinyl.classList.add("spin");  // Activar la animación del disco
            currentSongIndex = index;

            audio.onloadedmetadata = function() {
                const duration = audio.duration;
                const minutes = Math.floor(duration / 60);
                const seconds = Math.floor(duration % 60);
                const durationStr = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                songDurations[index].textContent = durationStr;
            };
        }

        songNames.forEach((songName, index) => {
            songName.addEventListener('click', function() {
                loadAndPlaySong(index);
            });
        });

        playPauseBtn.addEventListener('click', function() {
            if (audio.paused) {
                audio.play();
                playPauseBtn.textContent = "⏸";
                vinyl.classList.add("spin");
            } else {
                audio.pause();
                playPauseBtn.textContent = "▶";
                vinyl.classList.remove("spin");
            }
        });

        prevBtn.addEventListener('click', function() {
            if (currentSongIndex > 0) {
                loadAndPlaySong(currentSongIndex - 1);
            } else {
                loadAndPlaySong(songs.length - 1);
            }
        });

        nextBtn.addEventListener('click', function() {
            if (currentSongIndex < songs.length - 1) {
                loadAndPlaySong(currentSongIndex + 1);
            } else {
                loadAndPlaySong(0);
            }
        });

        audio.ontimeupdate = function() {
            const progress = (audio.currentTime / audio.duration) * 100;
            progressBar.style.width = progress + '%';
        };

        progressContainer.addEventListener('click', function(event) {
            const clickX = event.offsetX;
            const width = progressContainer.offsetWidth;
            const clickPercentage = (clickX / width) * 100;
            audio.currentTime = (clickPercentage / 100) * audio.duration;
        });

        loadSong(0); // Solo carga la canción pero no la reproduce
        vinyl.classList.remove("spin");
    </script>
  </body>
</body>
<footer class="footer">
    <p>&copy; 2025 Música Collection. Todos los derechos reservados.</p>
    <a href="../HTML/contacto.html">Contacto</a>
    <a href="../HTML/privacidad.html">Política de Privacidad</a>
</footer>
</html>
