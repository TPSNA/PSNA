<?php
include 'conexion_bd.php';
$municipioId = $_GET['municipio_id'];

// Usa prepared statement para seguridad
$stmt = mysqli_prepare($conexion, "SELECT id, nombre FROM parroquias WHERE municipio_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $municipioId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$options = '';
while ($row = mysqli_fetch_assoc($result)) {
    $options .= '<option value="' . $row['id'] . '">' . $row['nombre'] . '</option>';
}
echo $options;
mysqli_stmt_close($stmt);
?>