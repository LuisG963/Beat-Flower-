<?php
// Configurar la conexión con la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "music_collection";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT a.id, a.album_name, ai.album_image 
        FROM albums a 
        JOIN albums_info ai ON a.id = ai.album_id";  // Consulta con JOIN entre albums y albums_info

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Álbumes</title>
    <link rel="stylesheet" href="..CSS/style1.css">
</head>
<body>
    <div class="sidebar">
        <h2>Menú</h2>
        <ul>
        <?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "music_collection");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Consultas para obtener datos únicos de cada columna
$recordLabels = $conexion->query("SELECT DISTINCT record_label FROM albums_info");
$genres = $conexion->query("SELECT DISTINCT genre FROM albums_info");
$composers = $conexion->query("SELECT DISTINCT composer_name FROM albums_info");
?>
            <li><a href="../HTML/index.html">Inicio</a></li>
            
<div class="dropdown">
    <a href="javascript:void(0)" class="dropbtn">Álbumes <span class="arrow">▼</span></a>
    <ul class="dropdown-content">

        <!-- Disqueras -->
        <li class="submenu">
            <a href="javascript:void(0)" class="submenu-btn">Disquera <span class="arrow">▼</span></a>
            <ul class="submenu-content">
                <?php while ($row = $recordLabels->fetch_assoc()): ?>
                    <li><a href="#"><?php echo $row['record_label']; ?></a></li>
                <?php endwhile; ?>
            </ul>
        </li>

        <!-- Géneros -->
        <li class="submenu">
            <a href="javascript:void(0)" class="submenu-btn">Género <span class="arrow">▼</span></a>
            <ul class="submenu-content">
                <?php while ($row = $genres->fetch_assoc()): ?>
                    <li><a href="#"><?php echo $row['genre']; ?></a></li>
                <?php endwhile; ?>
            </ul>
        </li>

        <!-- Compositores -->
        <li class="submenu">
            <a href="javascript:void(0)" class="submenu-btn">Compositor <span class="arrow">▼</span></a>
            <ul class="submenu-content">
                <?php while ($row = $composers->fetch_assoc()): ?>
                    <li><a href="#"><?php echo $row['composer_name']; ?></a></li>
                <?php endwhile; ?>
            </ul>
        </li>
    </ul>
</div>

<?php
// Cerrar la conexión
$conexion->close();
?>
    </div>

    <div class="main-content">
        <header>
            <h1>Mis Álbumes</h1>
            <a class="btn-add-albu" href ="../HTML/agregar_Album.html" class="btn">Crear Álbum</a>
        </header>
        
        <section class="albums-container" id="albums-container">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="album">
                    <?php
                        $imageData = base64_encode($row['album_image']);
                        $imageSrc = "data:image/jpeg;base64," . $imageData;
                    ?>
                    <img src="<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($row['album_name']); ?>">
                    <p><?php echo htmlspecialchars($row['album_name']); ?></p>
                    
                    <div class="album-actions">
                        <form action="add_music.php?id=<?php echo $row['id']; ?>" method="get">
                            <button type="submit" class="btn-add-album">Agregar Música</button>
                        </form>

                        <form action="../PHP/Actualizar_album2.php?id=<?php echo $row['id']; ?>" method="get">
                            <button type="submit" class="btn-add-album">Actualizar</button>
                        </form>

                        <form action="javascript:void(0);" method="post" onsubmit="confirmDelete(<?php echo $row['id']; ?>)">
                            <button type="submit" class="btn-add-album">Eliminar</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </section>

        <?php
        $conn->close();
        ?>
    </div>


    <style>
        body {
    display: flex;
    background-color: #121212;
    color: white;
    font-family: Arial, sans-serif;
    margin: 0;
}

.sidebar {
    width: 250px;
    background: #181818;
    padding: 20px;
    height: 100vh;
}

.sidebar ul {
    list-style: none;
    padding: 0;
}

.sidebar ul li {
    margin: 20px 0;
}

.sidebar ul li a {
    color: white;
    text-decoration: none;
}

.dropdown {
    position: relative;
}

.dropbtn {
    background: none;
    color: white;
    font-size: 18px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
}

.arrow {
    font-size: 14px;
}

.dropdown-content {
    display: none;
    position: relative;
    background-color: #181818;
    min-width: 160px;
    margin-top: 10px;
    padding-left: 10px;
}

.submenu {
    position: relative;
}

.submenu-btn {
    color: white;
    display: flex;
    justify-content: space-between;
    width: 100%;
    padding: 12px 16px;
    text-decoration: none;
    background: none;
    border: none;
    cursor: pointer;
}

.submenu-content {
    display: none;
    background-color: #181818;
    list-style: none;
    padding-left: 20px;
}

.submenu-content a {
    color: white;
    padding: 8px 0;
    text-decoration: none;
    display: block;
}

.submenu-content a:hover {
    background-color: #181818;
}

.submenu.open .submenu-content {
    display: block;
}

.submenu.open .submenu-btn .arrow {
    transform: rotate(180deg);
}

.main-content {
    flex-grow: 1;
    padding: 20px;
}

.albums-container {
    display: flex;
    flex-wrap: wrap; /* Permite que los elementos se ajusten a varias filas */
    gap: 20px; /* Espacio entre los elementos */
    padding: 20px;
    justify-content: flex-start; /* Alinea los elementos al inicio del contenedor */
}


.album {
    width: 150px;
    text-align: center;
}

.album img {
    width: 100%; /* Hace que la imagen ocupe todo el ancho disponible del contenedor */
    height: 150px; /* Establece una altura fija para todas las imágenes */
    object-fit: cover; /* Asegura que la imagen no se deforme, se recortará si es necesario */
    border-radius: 10px;
}

.album p {
    margin-top: 10px;
    font-size: 1rem;
}
#album-form-container {
    display: none; /* Ocultarlo por defecto */
}

.player {
    position: fixed;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    background: #282828;
    width: 100%;
    max-width: 400px;
    padding: 10px;
    text-align: center;
    border-radius: 10px 10px 0 0;
}

.controls {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 10px;
}

.controls button {
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
}



.close-btn:hover {
    color: #333;
}




.form-content label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}

.form-content input[type="text"],
.form-content input[type="file"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}

.form-content button {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
}

.form-content button:hover {
    background-color: #45a049;
}

/* Estilo de encabezado y el botón "Álbum+" */
header {
    display: flex;
    flex-direction: column; /* Coloca los elementos en una columna */
    justify-content: center; /* Centra los elementos verticalmente */
    align-items: center; /* Centra los elementos horizontalmente */
    gap: 10px; /* Espacio entre el título y el botón */
    padding: 20px 0;
}

/* Estilos para el título */
header h1 {
    font-size: 2rem;
    margin: 0;
}

/* Estilos para el botón "Álbum+" */
header .btn-add-album {
    padding: 10px 20px;
    font-size: 1.2rem;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

header .btn-add-album:hover {
    background-color: #45a049;
}
/* Estilo de los botones "Eliminar", "Actualizar" y "Agregar Música" */
.btn-add-album {
    padding: 6px 1px;  /* Reducir el espacio dentro del botón */
    font-size: 14px;  /* Ajustar el tamaño de la fuente */
    background-color: #4CAF50;  /* Color de fondo */
    color: white;  /* Color del texto */
    border: none;  /* Eliminar borde */
    border-radius: 12px;  /* Bordes más redondeados */
    cursor: pointer;  /* Cursor tipo mano */
    transition: background-color 0.3s ease;  /* Transición suave en el color de fondo */
    margin: 2px 0;  /* Espacio definido entre botones (8px en la parte superior e inferior) */
    width: 100%;  /* Hace que los botones ocupen todo el ancho disponible en el contenedor */
}


.btn-add-album:hover {
    background-color: #45a049;  /* Cambiar el color de fondo al pasar el ratón */
}

.btn-add-album:active {
    background-color:rgb(56, 57, 142);  /* Cambiar el color de fondo cuando se hace clic */
}
/* Estilo de los botones "Eliminar", "Actualizar" y "Agregar Música" */
.btn-add-albu {
    padding: 8px 16px;  /* Reducir el espacio dentro del botón */
    font-size: 14px;  /* Ajustar el tamaño de la fuente */
    background-color:rgb(29, 0, 134);  /* Color de fondo */
    color: white;  /* Color del texto */
    border-radius: 5px;  /* Bordes más redondeados */
    cursor: pointer;  /* Cursor tipo mano */
    transition: background-color 0.3s ease;  /* Transición suave en el color de fondo */
}

.btn-add-albu:hover {
    background-color: #45a049;  /* Cambiar el color de fondo al pasar el ratón */
}

.btn-add-albu:active {
    background-color: #388e3c;  /* Cambiar el color de fondo cuando se hace clic */
}


    </style>

    <script>
        // Función para abrir el formulario emergente
        function openForm() {
            document.getElementById("album-form-container").style.display = "block";
        }

        // Función para cerrar el formulario emergente
        function closeForm() {
            document.getElementById("album-form-container").style.display = "none";
        }

        // Función para agregar un álbum
        function addAlbum() {
            const albumName = document.getElementById("album-name").value || "Álbum sin nombre";
            const fileInput = document.getElementById("album-image");
            const albumsContainer = document.getElementById("albums-container");

            let imageUrl = "img/disco-vinilo.png"; // Imagen por defecto

            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                imageUrl = URL.createObjectURL(file);
            }

            const albumDiv = document.createElement("div");
            albumDiv.classList.add("album");
            albumDiv.innerHTML = `
                <img src="${imageUrl}" alt="${albumName}">
                <p>${albumName}</p>
            `;

            albumsContainer.appendChild(albumDiv);
            closeForm();
        }
        function addAlbum() {
    const albumName = document.getElementById("album-name").value || "Álbum sin nombre";
    const fileInput = document.getElementById("album-image");
    const albumsContainer = document.getElementById("albums-container");

    let imageUrl = "img/disco-vinilo.png"; // Imagen por defecto

    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        imageUrl = URL.createObjectURL(file);
    }

    const albumDiv = document.createElement("div");
    albumDiv.classList.add("album");

    // Envolvemos el contenido del álbum en un enlace
    const albumLink = document.createElement("a");
    albumLink.href = "Datos_album.html";  // Redirige a la página de detalles del álbum

    albumLink.innerHTML = `
        <img src="${imageUrl}" alt="${albumName}">
        <p>${albumName}</p>
    `;

    albumDiv.appendChild(albumLink);
    albumsContainer.appendChild(albumDiv);
    closeForm();
}

        // Mostrar y ocultar el menú de subcategorías de "Álbumes"
        const dropdown = document.querySelector('.dropdown > .dropbtn');
        const dropdownContent = document.querySelector('.dropdown-content');

        dropdown.addEventListener('click', () => {
            dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
        });

        // Controlar el despliegue de las subcategorías dentro de "Álbumes"
        const submenuBtns = document.querySelectorAll('.submenu-btn');
        submenuBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const submenu = this.parentElement;
                submenu.classList.toggle('open');
            });
        });
      
        function confirmDelete(id) {
            if (confirm("¿Estás seguro de que quieres eliminar este álbum?")) {
                // Si el usuario confirma, redirige a la URL de eliminación
                window.location.href = "../PHP/eliminar_album.php?id=" + id;
            }
        }
    </script>
</body>
</html>