<?php
// Juangelyn_Sanchez
include 'conexion_bd.php'; 

// Función para convertir a mayúsculas (solo para strings)
function sanitizeInput($input) {
    if (is_array($input)) {
        $input = $input[0] ?? ''; 
    }
    $sanitized = htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    // Convertir a mayúsculas solo si es una cadena no vacía
    return !empty($sanitized) ? strtoupper($sanitized) : $sanitized;
}

// Función auxiliar para ints: ahora devuelve string o NULL
function sanitizeInt($input) {
    $value = trim($input ?? '');
    return !empty($value) ? strval(intval($value)) : null;  // String o NULL
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
// INTEGRACIÓN: Nuevas variables para ubicación (estado fijo en 1: Lara)
$estado_id = '1';  // Ahora string (fijo para Lara)
$municipio_id = sanitizeInt($_POST['municipio_id'] ?? '');  // String o NULL
$parroquia_id = sanitizeInt($_POST['parroquia_id'] ?? '');  // String o NULL
$direccion_ubicacion = sanitizeInput($_POST['direccion_ubicacion'] ?? '');  // Dirección específica
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

// --- INICIO CÓDIGO PARA MANEJO DE FOTO (INTEGRADO DEL EJEMPLO, AJUSTADO PARA EMPLEADOS) ---

$foto_path = '../php/uploads/default.png'; // Inicializamos con imagen por defecto

if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    
    // 1. Definir la carpeta de destino (ajustada para coincidir con tu estructura)
    $directorio_destino = '../php/fotos_trabajadores/'; // Asegúrate de que esta carpeta exista y tenga permisos
    
    // 2. Generar un nombre único para evitar conflictos
    $nombre_archivo = basename($_FILES['foto']['name']);
    $extension = pathinfo($nombre_archivo, PATHINFO_EXTENSION);
    $nuevo_nombre_archivo = uniqid() . "." . $extension;
    $ruta_completa_destino = $directorio_destino . '/' . $nuevo_nombre_archivo;

    // 3. Validar tipo de archivo (solo imágenes) y tamaño (máx 5MB) - MANTENIDO DE TU CÓDIGO ORIGINAL
    $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($_FILES['foto']['type'], $tipos_permitidos) && $_FILES['foto']['size'] <= 5000000) {
        // Mover el archivo del temporal al directorio de destino
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_completa_destino)) {
            // Guardamos la ruta relativa que se usará en el campo 'src' de HTML
            $foto_path = $ruta_completa_destino; 
        } else {
            // Manejo de error si la subida falla - QUEDA CON DEFAULT
            echo "Error al subir la imagen.";
        }
    } else {
        // Tipo no permitido o demasiado grande - QUEDA CON DEFAULT
        echo "Tipo de archivo no permitido o demasiado grande.";
    }
}
// Si no se subió una foto, ya está inicializada con default

// --- FIN CÓDIGO PARA MANEJO DE FOTO ---

// Agregar fecha_registro con la fecha y hora actual
$fecha_registro = date('Y-m-d H:i:s');

// VALIDACIÓN PREVIA: Verificar que municipio_id y parroquia_id existan si no son NULL (sin cambios)
if ($municipio_id !== null) {
    $check_sql = "SELECT id FROM municipios WHERE id = ?";
    $check_stmt = $conexion->prepare($check_sql);
    $check_stmt->bind_param("s", $municipio_id);  // Cambiado a 's'
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if ($check_result->num_rows == 0) {
        die("Error: El municipio_id seleccionado ($municipio_id) no existe en la base de datos.");
    }
    $check_stmt->close();
}
if ($parroquia_id !== null) {
    $check_sql = "SELECT id FROM parroquias WHERE id = ?";
    $check_stmt = $conexion->prepare($check_sql);
    $check_stmt->bind_param("s", $parroquia_id);  // Cambiado a 's'
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if ($check_result->num_rows == 0) {
        die("Error: El parroquia_id seleccionado ($parroquia_id) no existe en la base de datos.");
    }
    $check_stmt->close();
}
// Lista de variables bind para empleados (sin cambios)
$bind_vars_empleados = [
    $nacionalidad, $ci, $primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido, $fecha_nacimiento, $sexo, $estado_civil, 
    $estado_id, $municipio_id, $parroquia_id, $direccion_ubicacion, $telefono, $correo, $cuenta_bancaria, $tipo_trabajador, $grado_instruccion, $cargo, $sede, $dependencia, 
    $fecha_ingreso, $cod_siantel, $ubicacion_estante, $estatus, $fecha_egreso, $motivo_retiro, $ubicacion_estante_retiro, 
    $tipo_sangre, $lateralidad, $peso_trabajador, $altura_trabajador, $calzado_trabajador, $camisa_trabajador, $pantalon_trabajador, $foto_path, $fecha_registro
];
// Tipos de bind: todos 's' para simplificar
$types_empleados = str_repeat('s', count($bind_vars_empleados)); 

// INTEGRACIÓN: SQL actualizado con estado_id, municipio_id, parroquia_id
$sql_empleados = "INSERT INTO empleados (
    nacionalidad, ci, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, fecha_nacimiento, sexo, estado_civil, 
    estado_id, municipio_id, parroquia_id, direccion_ubicacion, telefono, correo, cuenta_bancaria, tipo_trabajador, grado_instruccion, cargo, sede, dependencia, 
    fecha_ingreso, cod_siantel, ubicacion_estante, estatus, fecha_egreso, motivo_retiro, ubicacion_estante_retiro, 
    tipo_sangre, lateralidad, peso_trabajador, altura_trabajador, calzado_trabajador, camisa_trabajador, pantalon_trabajador, foto, fecha_registro
) VALUES (" . str_repeat('?, ', count($bind_vars_empleados) - 1) . "?)";

$stmt_empleados = mysqli_prepare($conexion, $sql_empleados);
if ($stmt_empleados) {
    mysqli_stmt_bind_param($stmt_empleados, $types_empleados, ...$bind_vars_empleados);
    $ejecutar_empleados = mysqli_stmt_execute($stmt_empleados);
    if ($ejecutar_empleados) {
        $id_empleado = mysqli_insert_id($conexion);
        echo "Empleado registrado correctamente. ID: $id_empleado<br>";
        
        // Insertar familiares si existen (sin cambios)
        if (isset($_POST['cedula_familiar']) && is_array($_POST['cedula_familiar']) && !empty($_POST['cedula_familiar'])) {
            $num_familiares = count($_POST['cedula_familiar']);
            echo "Insertando $num_familiares familiares.<br>";
            
            // consulta para familiares
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
                    
                    // Verificar que al menos cedula_familiar y parentesco estén presentes 
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
