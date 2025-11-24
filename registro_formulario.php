<?php
// Juangelyn_Sanchez
include 'conexion_bd.php'; 

// Función para convertir a mayúsculas
function sanitizeInput($input) {
    if (is_array($input)) {
        $input = $input[0] ?? ''; 
    }
    $sanitized = htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    // Convertir a mayúsculas solo si es una cadena no vacía
    return !empty($sanitized) ? strtoupper($sanitized) : $sanitized;
}

// Paso 1: Información Personal
$nacionalidad = sanitizeInput($_POST['nacionalidad'] ?? '');
$ci = sanitizeInput($_POST['ci'] ?? ''); 
$primer_nombre = sanitizeInput($_POST['primer_nombre'] ?? '');
$segundo_nombre = sanitizeInput($_POST['segundo_nombre'] ?? '');
$fecha_nacimiento = sanitizeInput($_POST['fecha_nacimiento'] ?? ''); 
$primer_apellido = sanitizeInput($_POST['primer_apellido'] ?? '');
$segundo_apellido = sanitizeInput($_POST['segundo_apellido'] ?? '');
$sexo = sanitizeInput($_POST['sexo'] ?? '');
$estado_civil = sanitizeInput($_POST['estado_civil'] ?? '');
$direccion_ubicacion = sanitizeInput($_POST['direccion_ubicacion'] ?? '');
$telefono = sanitizeInput($_POST['telefono'] ?? ''); 
$correo = sanitizeInput($_POST['correo'] ?? ''); 
$cuenta_bancaria = sanitizeInput($_POST['cuenta_bancaria'] ?? '');

// Paso 2: Información Laboral
$tipo_trabajador = sanitizeInput($_POST['tipo_trabajador'] ?? '');
$grado_instruccion = sanitizeInput($_POST['grado_instruccion'] ?? '');
$cargo = sanitizeInput($_POST['cargo'] ?? '');
$sede = sanitizeInput($_POST['sede'] ?? '');
$dependencia = sanitizeInput($_POST['dependencia'] ?? '');
$fecha_ingreso = sanitizeInput($_POST['fecha_ingreso'] ?? ''); 
$cod_siantel = sanitizeInput($_POST['cod_siantel'] ?? '');
$ubicacion_estante = sanitizeInput($_POST['ubicacion_estante'] ?? '');
$estatus = sanitizeInput($_POST['estatus'] ?? '');
$fecha_egreso = sanitizeInput($_POST['fecha_egreso'] ?? ''); 
$motivo_retiro = sanitizeInput($_POST['motivo_retiro'] ?? '');
$ubicacion_estante_retiro = sanitizeInput($_POST['ubicacion_estante_retiro'] ?? '');

// Paso 3: Información General
$tipo_sangre = sanitizeInput($_POST['tipo_sangre'] ?? '');
$lateralidad = sanitizeInput($_POST['lateralidad'] ?? '');
$peso_trabajador = sanitizeInput($_POST['peso_trabajador'] ?? '');
$altura_trabajador = sanitizeInput($_POST['altura_trabajador'] ?? ''); 
$calzado_trabajador = sanitizeInput($_POST['calzado_trabajador'] ?? ''); 
$camisa_trabajador = sanitizeInput($_POST['camisa_trabajador'] ?? '');
$pantalon_trabajador = sanitizeInput($_POST['pantalon_trabajador'] ?? '');

// Manejo de la foto: Verificar si se subió un archivo y crear carpeta si no existe
$upload_dir = __DIR__ . '/uploads/'; 
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true); 
}
$foto_path = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $foto_tmp = $_FILES['foto']['tmp_name'];
    $foto_name = basename($_FILES['foto']['name']);
    $foto_path = $upload_dir . $foto_name;
    if (move_uploaded_file($foto_tmp, $foto_path)) {
      
    } else {
        echo "Error al mover el archivo de foto.";
        $foto_path = null;
    }
} else {
    $foto_path = null; // No se subió foto
}

// Lista de variables bind para empleados 
$bind_vars_empleados = [
    $nacionalidad, $ci, $primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido, $fecha_nacimiento, $sexo, $estado_civil, 
    $direccion_ubicacion, $telefono, $correo, $cuenta_bancaria, $tipo_trabajador, $grado_instruccion, $cargo, $sede, $dependencia, 
    $fecha_ingreso, $cod_siantel, $ubicacion_estante, $estatus, $fecha_egreso, $motivo_retiro, $ubicacion_estante_retiro, 
    $tipo_sangre, $lateralidad, $peso_trabajador, $altura_trabajador, $calzado_trabajador, $camisa_trabajador, $pantalon_trabajador, $foto_path
];

// Verificación
if (count($bind_vars_empleados) !== 33) {
    die("Error: Número incorrecto de variables bind para empleados. Esperado: 33, Encontrado: " . count($bind_vars_empleados));
}

$types_empleados = str_repeat('s', count($bind_vars_empleados));

$sql_empleados = "INSERT INTO empleados (
    nacionalidad, ci, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, fecha_nacimiento, sexo, estado_civil, 
    direccion_ubicacion, telefono, correo, cuenta_bancaria, tipo_trabajador, grado_instruccion, cargo, sede, dependencia, 
    fecha_ingreso, cod_siantel, ubicacion_estante, estatus, fecha_egreso, motivo_retiro, ubicacion_estante_retiro, 
    tipo_sangre, lateralidad, peso_trabajador, altura_trabajador, calzado_trabajador, camisa_trabajador, pantalon_trabajador, foto
) VALUES (" . str_repeat('?, ', count($bind_vars_empleados) - 1) . "?)";

$stmt_empleados = mysqli_prepare($conexion, $sql_empleados);
if ($stmt_empleados) {
    mysqli_stmt_bind_param($stmt_empleados, $types_empleados, ...$bind_vars_empleados);
    $ejecutar_empleados = mysqli_stmt_execute($stmt_empleados);
    if ($ejecutar_empleados) {
        $id_empleado = mysqli_insert_id($conexion);
        echo "Empleado registrado correctamente. ID: $id_empleado<br>";
        
        // Insertar familiares si existen
        if (isset($_POST['cedula_familiar']) && is_array($_POST['cedula_familiar']) && !empty($_POST['cedula_familiar'])) {
            $num_familiares = count($_POST['cedula_familiar']);
            echo "Insertando $num_familiares familiares.<br>";
            
            // Preparar consulta para familiares
            $sql_familiares = "INSERT INTO familiares (ci_trabajador, cedula_familiar, parentesco, edad, peso, altura, talla_zapato, talla_camisa, talla_pantalon, tipo_sangre, fecha_registro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_familiares = mysqli_prepare($conexion, $sql_familiares);
            
            if ($stmt_familiares) {
                for ($i = 0; $i < $num_familiares; $i++) {
                    $cedula_familiar = sanitizeInput($_POST['cedula_familiar'][$i] ?? '');
                    $parentesco = sanitizeInput($_POST['parentesco'][$i] ?? '');
                    $edad = sanitizeInput($_POST['edad'][$i] ?? ''); 
                    $peso = sanitizeInput($_POST['peso'][$i] ?? ''); 
                    $altura = sanitizeInput($_POST['altura'][$i] ?? ''); 
                    $talla_zapato = sanitizeInput($_POST['talla_zapato'][$i] ?? ''); 
                    $talla_camisa = sanitizeInput($_POST['talla_camisa'][$i] ?? '');
                    $talla_pantalon = sanitizeInput($_POST['talla_pantalon'][$i] ?? '');
                    $tipo_sangre_fam = sanitizeInput($_POST['tipo_sangre'][$i] ?? '');
                    $fecha_registro = sanitizeInput($_POST['fecha_registro'][$i] ?? '');
                    
                    // Verificar que al menos cedula_familiar y parentesco estén presentes (campos requeridos)
                    if (!empty($cedula_familiar) && !empty($parentesco) && !empty($fecha_registro)) {
                        mysqli_stmt_bind_param($stmt_familiares, 'sssssssssss', $ci, $cedula_familiar, $parentesco, $edad, $peso, $altura, $talla_zapato, $talla_camisa, $talla_pantalon, $tipo_sangre_fam, $fecha_registro);
                        if (mysqli_stmt_execute($stmt_familiares)) {
                            echo "Familiar " . ($i + 1) . " registrado correctamente.<br>";
                        } else {
                            echo "Error al insertar familiar " . ($i + 1) . ": " . mysqli_error($conexion) . "<br>";
                        }
                    } else {
                        echo "Datos incompletos para familiar " . ($i + 1) . ", omitiendo.<br>";
                    }
                }
                mysqli_stmt_close($stmt_familiares);
            } else {
                echo "Error en la preparación de la consulta para familiares: " . mysqli_error($conexion) . "<br>";
            }
        } else {
            echo "No se detectaron datos de familiares.<br>";
        }
    } else {
        echo "Error al insertar empleado: " . mysqli_error($conexion);
    }
    mysqli_stmt_close($stmt_empleados);
} else {
    echo "Error en la preparación de la consulta para empleados: " . mysqli_error($conexion);
}

mysqli_close($conexion);
?>
