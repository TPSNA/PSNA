<?php
// admin/includes/database.php
// Conexion a la base de datos SAINA

$host = "localhost";
$user = "root";
$pass = "";
$database = "nueva_bd_saina";

// Crear conexion
$conn = new mysqli($host, $user, $pass, $database);

// Verificar conexion
if ($conn->connect_error) {
    die("Error de conexion: " . $conn->connect_error);
}

// Configurar caracteres para tildes y n
$conn->set_charset("utf8mb4");

// Funcion para limpiar datos de entrada
function limpiar_dato($dato) {
    global $conn;
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato);
    return $conn->real_escape_string($dato);
}

// Funcion para verificar si es administrador
function es_admin() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

// Funcion para requerir login de admin
function requerir_admin() {
    session_start();
    if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
        header("Location: ../login.php");
        exit();
    }
}
?>