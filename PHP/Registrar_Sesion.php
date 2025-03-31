<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - MusicVibe</title>
    <link rel="stylesheet" href="../CSS/Registrar_Sesion.css"> <!-- Archivo CSS separado -->

    <style>
        body {
            background-image: url('../img/Icono_Musica_2.jpg');


            background-size: cover; /* Ajusta la imagen para cubrir toda la pantalla */
            background-position: center; /* Centra la imagen */
            background-repeat: no-repeat; /* Evita que la imagen se repita */
            height: 100vh; /* Asegura que cubra toda la altura */
            margin: 0; /* Elimina márgenes por defecto */
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "Canciones"; // Nombre de la base de datos
$errores = '';

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $errores = '';

    if (strlen($usuario) < 3 || strlen($usuario) > 50 || !preg_match("/^[a-zA-Z]+$/", $usuario)) {
        $errores .= '<div class="error">Nombre de usuario debe contener solo letras, no tener espacios en blanco y tener entre 3 y 50 caracteres.</div>';
    }
    
    if (strlen($email) < 10 || strlen($email) > 50) {
        $errores .= '<div class="error">Error: El correo electrónico debe tener entre 10 y 50 caracteres.</div>';
    }
    
    if (strlen($password) < 8 || strlen($password) > 15) {
        $errores .= '<div class="error">Error: La contraseña debe tener entre 8 y 15 caracteres de tipo numerico.</div>';
    }
    
    // Si hay errores, los mostramos dentro de un contenedor con el botón de regresar adentro
    if ($errores != '') {
        echo '<div class="errores-container">
                ' . $errores . '
                <div class="boton-container">
                    <button onclick="window.location.href=\'../HTML/Registrar_Sesion.html\'">Regresar</button>
                </div>
              </div>';
        die(); // Detenemos el script después de mostrar los errores
    }

    // Encriptar la contraseña
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    // Insertar en la base de datos
    $sql = "INSERT INTO usuarios (usuario, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $usuario, $email, $password_hash);

    if ($stmt->execute()) {
        echo '
        <div class="mensaje-contenedor">
            <div class="mensaje-titulo">
                Registro exitoso. ¡Bienvenido a MusicVibe!
            </div>
            <div class="boton-container">
                <button onclick="window.location.href=\'../HTML/index.html\'">Ir a Inicio</button>
            </div>
        </div>';
    } else {
        echo "Error en el registro: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
}    
?>

</body>
</html>
