<?php
// admin/usuarios/eliminar.php
require_once '../includes/database.php';
session_start();

// Verificar que sea administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Verificar que se pasó un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: listar.php?mensaje=ID de usuario no válido&tipo=error");
    exit();
}

$id_usuario = (int)$_GET['id'];

// Verificar que no sea el mismo usuario que está logueado
if ($id_usuario == $_SESSION['usuario_id']) {
    header("Location: listar.php?mensaje=No puedes eliminar tu propio usuario&tipo=error");
    exit();
}

// Verificar que no sea el único administrador
$sql_check = "SELECT COUNT(*) as total_admins FROM usuarios WHERE rol = 'admin' AND id != ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $id_usuario);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$total_admins = $result_check->fetch_assoc()['total_admins'];
$stmt_check->close();

if ($total_admins == 0) {
    header("Location: listar.php?mensaje=No se puede eliminar el único administrador&tipo=error");
    exit();
}

// Obtener nombre del usuario para el mensaje
$sql_nombre = "SELECT username FROM usuarios WHERE id = ?";
$stmt_nombre = $conn->prepare($sql_nombre);
$stmt_nombre->bind_param("i", $id_usuario);
$stmt_nombre->execute();
$result_nombre = $stmt_nombre->get_result();
$usuario = $result_nombre->fetch_assoc();
$username = $usuario['username'];
$stmt_nombre->close();

// Eliminar usuario
$sql = "DELETE FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);

if ($stmt->execute()) {
    $mensaje = "Usuario '$username' eliminado correctamente";
    $tipo = "success";
} else {
    $mensaje = "Error al eliminar usuario: " . $conn->error;
    $tipo = "error";
}

$stmt->close();
$conn->close();

header("Location: listar.php?mensaje=" . urlencode($mensaje) . "&tipo=" . $tipo);
exit();
?>