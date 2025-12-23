<?php
// admin/trabajadores/eliminar.php
require_once '../includes/database.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['ci'])) {
    header("Location: index.php?error=invalid");
    exit();
}

$id_empleado = (int)$_GET['id'];
$ci_empleado = $_GET['ci'];

// Obtener datos para mensaje
$sql_info = "SELECT primer_nombre, primer_apellido FROM empleados WHERE id = ?";
$stmt_info = $conn->prepare($sql_info);
$stmt_info->bind_param("i", $id_empleado);
$stmt_info->execute();
$result_info = $stmt_info->get_result();

if ($result_info->num_rows === 0) {
    header("Location: index.php?error=notfound");
    exit();
}

$empleado = $result_info->fetch_assoc();
$nombre_completo = $empleado['primer_nombre'] . ' ' . $empleado['primer_apellido'];
$stmt_info->close();

// Iniciar transacción
$conn->begin_transaction();

try {
    // Eliminar familiares primero
    $sql_fam = "DELETE FROM familiares WHERE ci_trabajador = ?";
    $stmt_fam = $conn->prepare($sql_fam);
    $stmt_fam->bind_param("s", $ci_empleado);
    $stmt_fam->execute();
    $stmt_fam->close();
    
    // Eliminar empleado
    $sql_emp = "DELETE FROM empleados WHERE id = ?";
    $stmt_emp = $conn->prepare($sql_emp);
    $stmt_emp->bind_param("i", $id_empleado);
    $stmt_emp->execute();
    $stmt_emp->close();
    
    $conn->commit();
    
    header("Location: index.php?success=" . urlencode("Trabajador eliminado: $nombre_completo"));
    
} catch (Exception $e) {
    $conn->rollback();
    header("Location: index.php?error=" . urlencode("Error al eliminar: " . $e->getMessage()));
}

$conn->close();
exit();