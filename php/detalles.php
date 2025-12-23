<?php
include("../php/conexion_bd.php"); 

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 

    $sql = "SELECT * FROM empleados WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $empleado = $result->fetch_assoc();
        echo json_encode($empleado); // Devuelve todos los campos como JSON
    } else {
        echo json_encode(['error' => 'Empleado no encontrado']);
    }

    $stmt->close();
    mysqli_close($conexion);
} else {
    echo json_encode(['error' => 'ID no proporcionado']);
}
?>