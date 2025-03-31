<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - MusicVibe</title>
    <link rel="stylesheet" href="../CSS/Iniciar_Sesion.css"> <!-- Archivo CSS separado -->

    <?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "Canciones"; // Nombre de la base de datos
$errores = '';
$mensaje = '';

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    
    $sql = "SELECT * FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Mostrar el nombre de usuario en el mensaje de bienvenida
            $mensaje = "¡Bienvenido usuario BeatFlow, " . htmlspecialchars($user['usuario']) . "!";
            $redireccion_url = "../HTML/Index.html"; // Página a la que redirigir
        } else {
            $mensaje = "Contraseña incorrecta. Intenta de nuevo.";
            $redireccion_url = "../HTML/Iniciar_Sesion.html"; // Redirigir a la página de inicio de sesión
        }
    } else {
        $mensaje = "Aún no eres un usuario BeatFlow, ¿qué esperas?";
        $redireccion_url = "../HTML/registrar_sesion.html"; // Redirigir a la página de registro
    }

    $stmt->close();
    $conn->close();
    
    echo "<div class='mensaje-contenedor'>
            <div class='mensaje-titulo'>$mensaje</div>
            <div class='boton-container'>
                <button onclick='window.location.href=\"$redireccion_url\"'>Ir a la página</button>
            </div>
          </div>";

    // Redireccionamiento automático después de 3 segundos
    echo "<script>
            setTimeout(function() {
                window.location.href = '$redireccion_url';
            }, 3000); // 3000 milisegundos = 3 segundos
          </script>";
}
?>


