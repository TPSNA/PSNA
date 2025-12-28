<?php
// usuario/php/parroquias.php
include __DIR__ . '/../admin/includes/database.php';

header('Content-Type: text/html; charset=utf-8');

$municipio_id = $_GET['municipio_id'] ?? '';

if (!empty($municipio_id) && is_numeric($municipio_id)) {
    $sql = "SELECT id, nombre FROM parroquias WHERE municipio_id = ? ORDER BY nombre";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $municipio_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $options = '';
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $options .= "<option value='{$row['id']}'>{$row['nombre']}</option>";
        }
    } else {
        $options = "<option value=''>No hay parroquias para este municipio</option>";
    }
    
    echo $options;
    $stmt->close();
} else {
    echo "<option value=''>Selecciona un municipio primero</option>";
}

$conn->close();
?>