<?php
// usuario/includes/database.php
// Conexión a la base de datos SAINA

$host = "localhost";
$user = "root";
$pass = "";
$database = "nueva_bd_saina";

// Crear conexión
$conn = new mysqli($host, $user, $pass, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Configurar caracteres
$conn->set_charset("utf8mb4");

// Función para limpiar datos de entrada
function limpiar_dato($dato) {
    global $conn;
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato);
    return $conn->real_escape_string($dato);
}

// Función para verificar si es usuario
function es_usuario() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'usuario';
}

// Función para requerir login de usuario
function requerir_usuario() {
    session_start();
    if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'usuario') {
        header("Location: ../index.php");
        exit();
    }
}
?>