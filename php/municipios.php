<?php
include 'conexion_bd.php';  
$query = "SELECT id, nombre FROM municipios WHERE estado_id = 1";
$result = mysqli_query($conexion, $query);  
$options = '';
while ($row = mysqli_fetch_assoc($result)) {
    $options .= '<option value="' . $row['id'] . '">' . $row['nombre'] . '</option>';
}
echo $options;
?>