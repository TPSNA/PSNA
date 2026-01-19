<?php
include __DIR__ . '/../admin/includes/database.php';

header('Content-Type: text/html; charset=utf-8');

$sql = "SELECT id, nombre FROM municipios WHERE estado_id = 1 ORDER BY nombre";
$result = $conn->query($sql);

$options = '';
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $options .= "<option value='{$row['id']}'>{$row['nombre']}</option>";
    }
} else {
    $options = "<option value=''>No hay municipios disponibles</option>";
}

echo $options;
$conn->close();
?>