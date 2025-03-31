<?php
// Conectar a la base de datos
$mysqli = new mysqli("localhost", "usuario", "contraseña", "Cancion");

if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

$query = "SELECT * FROM canciones WHERE titulo LIKE '%" . $_GET['query'] . "%'";

$result = $mysqli->query($query);

$canciones = [];
while ($row = $result->fetch_assoc()) {
    $canciones[] = $row;
}

echo json_encode($canciones);

// Cerrar la conexión
$mysqli->close();
?>
