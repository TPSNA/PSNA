<?php
// usuario/trabajadores/procesar_trabajador.php
session_start();

// 1. Verificar sesión
if (!isset($_SESSION['username'])) {
    header("Location: ../../admin/login.php");
    exit();
}

// 2. Conectar a la base de datos
$database_file = __DIR__ . '/../../admin/includes/database.php';
if (!file_exists($database_file)) {
    die("Error crítico: No se puede conectar a la base de datos.");
}
require_once $database_file;

// 3. Verificar conexión
if (!isset($conn) || !$conn) {
    die("Error de conexión a la base de datos.");
}

// Función para sanitizar datos
function sanitizeInput($input) {
    if (is_array($input)) {
        $input = $input[0] ?? '';
    }
    $sanitized = htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    return !empty($sanitized) ? $sanitized : '';
}

function sanitizeInt($input) {
    $value = trim($input ?? '');
    return !empty($value) && is_numeric($value) ? intval($value) : null;
}

function sanitizeDecimal($input) {
    $value = trim($input ?? '');
    if (empty($value) || !is_numeric($value)) {
        return null;
    }
    return number_format(floatval($value), 2, '.', '');
}

// PROCESAR FORMULARIO
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ===== 1. DATOS DEL TRABAJADOR =====
    $datos = [
        'nacionalidad' => strtoupper(sanitizeInput($_POST['nacionalidad'] ?? '')),
        'ci' => sanitizeInput($_POST['ci'] ?? ''),
        'primer_nombre' => strtoupper(sanitizeInput($_POST['primer_nombre'] ?? '')),
        'segundo_nombre' => strtoupper(sanitizeInput($_POST['segundo_nombre'] ?? '')),
        'primer_apellido' => strtoupper(sanitizeInput($_POST['primer_apellido'] ?? '')),
        'segundo_apellido' => strtoupper(sanitizeInput($_POST['segundo_apellido'] ?? '')),
        'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? '',
        'sexo' => strtoupper(sanitizeInput($_POST['sexo'] ?? '')),
        'estado_civil' => strtoupper(sanitizeInput($_POST['estado_civil'] ?? '')),
        'estado_id' => 1, // Lara por defecto
        'municipio_id' => sanitizeInt($_POST['municipio_id'] ?? ''),
        'parroquia_id' => sanitizeInt($_POST['parroquia_id'] ?? ''),
        'direccion_ubicacion' => strtoupper(sanitizeInput($_POST['direccion_ubicacion'] ?? '')),
        'telefono' => sanitizeInput($_POST['telefono'] ?? ''),
        'correo' => filter_var($_POST['correo'] ?? '', FILTER_SANITIZE_EMAIL),
        'cuenta_bancaria' => sanitizeInput($_POST['cuenta_bancaria'] ?? ''),
        'tipo_trabajador' => strtoupper(sanitizeInput($_POST['tipo_trabajador'] ?? '')),
        'grado_instruccion' => strtoupper(sanitizeInput($_POST['grado_instruccion'] ?? '')),
        'cargo' => strtoupper(sanitizeInput($_POST['cargo'] ?? '')),
        'sede' => strtoupper(sanitizeInput($_POST['sede'] ?? '')),
        'dependencia' => strtoupper(sanitizeInput($_POST['dependencia'] ?? '')),
        'fecha_ingreso' => $_POST['fecha_ingreso'] ?? '',
        'cod_siantel' => strtoupper(sanitizeInput($_POST['cod_siantel'] ?? '')),
        'ubicacion_estante' => strtoupper(sanitizeInput($_POST['ubicacion_estante'] ?? '')),
        'estatus' => strtoupper(sanitizeInput($_POST['estatus'] ?? 'ACTIVO')),
        'tipo_sangre' => strtoupper(sanitizeInput($_POST['tipo_sangre'] ?? '')),
        'lateralidad' => strtoupper(sanitizeInput($_POST['lateralidad'] ?? '')),
        'peso_trabajador' => sanitizeDecimal($_POST['peso_trabajador'] ?? ''),
        'altura_trabajador' => sanitizeDecimal($_POST['altura_trabajador'] ?? ''),
        'calzado_trabajador' => sanitizeInput($_POST['calzado_trabajador'] ?? ''),
        'camisa_trabajador' => sanitizeInput($_POST['camisa_trabajador'] ?? ''),
        'pantalon_trabajador' => sanitizeInput($_POST['pantalon_trabajador'] ?? ''),
        'fecha_registro' => date('Y-m-d H:i:s')
    ];

    // Convertir campos opcionales vacíos a NULL
    $camposOpcionales = [
        'direccion_ubicacion', 'telefono', 'correo', 'cuenta_bancaria',
        'tipo_sangre', 'peso_trabajador', 'altura_trabajador',
        'calzado_trabajador', 'camisa_trabajador', 'pantalon_trabajador',
        'cod_siantel', 'ubicacion_estante', 'segundo_nombre', 'segundo_apellido',
        'municipio_id', 'parroquia_id'
    ];

    foreach ($camposOpcionales as $campo) {
        if (isset($datos[$campo]) && (is_null($datos[$campo]) || $datos[$campo] === '')) {
            $datos[$campo] = null;
        }
    }

    // ===== 2. PROCESAR FOTO =====
    $foto_path = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $uploadDir = __DIR__ . '/../../uploads/';
        
        // Crear directorio si no existe
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Validar archivo
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (in_array($_FILES['foto']['type'], $allowedTypes) && 
            $_FILES['foto']['size'] <= $maxSize) {
            
            // Generar nombre único
            $fileExtension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $fileName = 'empleado_' . $datos['ci'] . '_' . time() . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath)) {
                // Guardar ruta relativa para la base de datos
                $foto_path = '../../uploads/' . $fileName;
            }
        }
    }
    
    // Si no hay foto, usar una por defecto
    if (!$foto_path) {
        $foto_path = '../../uploads/default.png';
        
        // Crear directorio de uploads si no existe
        $uploadsDir = __DIR__ . '/../../uploads/';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }
        
        // Crear imagen por defecto si no existe
        $defaultDest = $uploadsDir . 'default.png';
        if (!file_exists($defaultDest)) {
            // Crear una imagen simple por defecto
            $image = imagecreatetruecolor(300, 300);
            $bgColor = imagecolorallocate($image, 106, 103, 240); // Color morado de tu tema
            imagefill($image, 0, 0, $bgColor);
            $textColor = imagecolorallocate($image, 255, 255, 255);
            imagestring($image, 5, 80, 140, 'SIN FOTO', $textColor);
            imagepng($image, $defaultDest);
            imagedestroy($image);
        }
    }

    // ===== 3. DATOS ADICIONALES PARA INACTIVOS =====
    if ($datos['estatus'] === 'INACTIVO') {
        $datos['fecha_egreso'] = !empty($_POST['fecha_egreso']) ? $_POST['fecha_egreso'] : null;
        $datos['motivo_retiro'] = strtoupper(sanitizeInput($_POST['motivo_retiro'] ?? ''));
        $datos['ubicacion_estante_retiro'] = strtoupper(sanitizeInput($_POST['ubicacion_estante_retiro'] ?? ''));
    } else {
        $datos['fecha_egreso'] = null;
        $datos['motivo_retiro'] = null;
        $datos['ubicacion_estante_retiro'] = null;
    }

    // ===== 4. DATOS DE FAMILIARES =====
    $familiares = [];
    if (isset($_POST['cedula_familiar']) && is_array($_POST['cedula_familiar'])) {
        foreach ($_POST['cedula_familiar'] as $index => $cedula) {
            if (!empty(trim($cedula))) {
                $familiares[] = [
                    'cedula' => sanitizeInput($cedula),
                    'nombre_familiar' => strtoupper(sanitizeInput($_POST['nombre_familiar'][$index] ?? '')),
                    'apellido_familiar' => strtoupper(sanitizeInput($_POST['apellido_familiar'][$index] ?? '')),
                    'parentesco' => strtoupper(sanitizeInput($_POST['parentesco'][$index] ?? '')),
                    'edad' => sanitizeInt($_POST['edad'][$index] ?? ''),
                    'peso' => sanitizeDecimal($_POST['peso'][$index] ?? ''),
                    'altura' => sanitizeDecimal($_POST['altura'][$index] ?? ''),
                    'talla_zapato' => sanitizeInput($_POST['talla_zapato'][$index] ?? ''),
                    'talla_camisa' => sanitizeInput($_POST['talla_camisa'][$index] ?? ''),
                    'talla_pantalon' => sanitizeInput($_POST['talla_pantalon'][$index] ?? ''),
                    'tipo_sangre' => strtoupper(sanitizeInput($_POST['tipo_sangre'][$index] ?? '')),
                    'fecha_registro' => !empty($_POST['fecha_registro'][$index]) ? $_POST['fecha_registro'][$index] : date('Y-m-d')
                ];
            }
        }
    }

    // ===== 5. VALIDACIONES =====
    $errores = [];
    
    // Validar campos obligatorios (EXCLUIR los que no son obligatorios)
    $camposObligatorios = [
        'nacionalidad', 'ci', 'primer_nombre', 'primer_apellido', 
        'fecha_nacimiento', 'sexo', 'estado_civil',
        'tipo_trabajador', 'grado_instruccion', 'cargo', 
        'sede', 'dependencia', 'fecha_ingreso', 'estatus',
        'lateralidad'
        // NOTA: Los siguientes campos se han hecho OPCIONALES:
        // 'direccion_ubicacion', 'telefono', 'correo', 
        // 'cuenta_bancaria', 'tipo_sangre'
    ];
    
    foreach ($camposObligatorios as $campo) {
        if (empty($datos[$campo])) {
            $errores[] = "El campo " . str_replace('_', ' ', $campo) . " es obligatorio.";
        }
    }
    
    // Validar CI único
    $sql_check = "SELECT id FROM empleados WHERE ci = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $datos['ci']);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows > 0) {
        $errores[] = "El número de cédula ya está registrado.";
    }
    $stmt_check->close();
    
    // ===== 6. INSERTAR EN BASE DE DATOS =====
    if (empty($errores)) {
        $conn->begin_transaction();
        
        try {
            // Insertar empleado - 37 columnas
            $sql_empleado = "INSERT INTO empleados (
                nacionalidad, ci, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido,
                fecha_nacimiento, sexo, estado_civil, direccion_ubicacion, telefono, correo,
                cuenta_bancaria, tipo_trabajador, grado_instruccion, cargo, sede, dependencia,
                fecha_ingreso, cod_siantel, ubicacion_estante, estatus, fecha_egreso, motivo_retiro,
                ubicacion_estante_retiro, tipo_sangre, lateralidad, peso_trabajador, altura_trabajador,
                calzado_trabajador, camisa_trabajador, pantalon_trabajador, foto, fecha_registro,
                estado_id, municipio_id, parroquia_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt_empleado = $conn->prepare($sql_empleado);
            
            // Verificar si hay error en la preparación de la consulta
            if (!$stmt_empleado) {
                throw new Exception("Error preparando consulta: " . $conn->error);
            }
            
            // Depuración: Mostrar consulta y valores (solo para desarrollo)
            error_log("SQL: " . $sql_empleado);
            error_log("Valores: " . print_r($datos, true));
            
            // CORREGIDO: Tipos de datos correctos - 's' para strings, 'i' para integers, 'd' para decimales
            $stmt_empleado->bind_param(
                "ssssssssssssssssssssssssssssssssssiii",
                $datos['nacionalidad'], 
                $datos['ci'], 
                $datos['primer_nombre'], 
                $datos['segundo_nombre'],
                $datos['primer_apellido'], 
                $datos['segundo_apellido'], 
                $datos['fecha_nacimiento'],
                $datos['sexo'], 
                $datos['estado_civil'], 
                $datos['direccion_ubicacion'], 
                $datos['telefono'], 
                $datos['correo'],
                $datos['cuenta_bancaria'], 
                $datos['tipo_trabajador'], 
                $datos['grado_instruccion'],
                $datos['cargo'], 
                $datos['sede'], 
                $datos['dependencia'], 
                $datos['fecha_ingreso'],
                $datos['cod_siantel'], 
                $datos['ubicacion_estante'], 
                $datos['estatus'], 
                $datos['fecha_egreso'],
                $datos['motivo_retiro'], 
                $datos['ubicacion_estante_retiro'], 
                $datos['tipo_sangre'],
                $datos['lateralidad'], 
                $datos['peso_trabajador'], 
                $datos['altura_trabajador'],
                $datos['calzado_trabajador'], 
                $datos['camisa_trabajador'], 
                $datos['pantalon_trabajador'],
                $foto_path, 
                $datos['fecha_registro'],
                $datos['estado_id'],
                $datos['municipio_id'],
                $datos['parroquia_id']
            );
            
            if (!$stmt_empleado->execute()) {
                throw new Exception("Error al registrar empleado: " . $stmt_empleado->error);
            }
            
            $empleado_id = $stmt_empleado->insert_id;
            $ci_trabajador = $datos['ci'];
            $stmt_empleado->close();
            
            // Insertar familiares con los nuevos campos
            if (!empty($familiares)) {
                $sql_familiar = "INSERT INTO familiares (
                    ci_trabajador, cedula_familiar, nombre_familiar, apellido_familiar, 
                    parentesco, edad, peso, altura, talla_zapato,
                    talla_camisa, talla_pantalon, tipo_sangre, fecha_registro
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt_familiar = $conn->prepare($sql_familiar);
                
                if (!$stmt_familiar) {
                    throw new Exception("Error preparando consulta de familiares: " . $conn->error);
                }
                
                foreach ($familiares as $familiar) {
                    // Validar parentesco
                    $parentescos_validos = ['ESPOSO/A', 'HIJO/A', 'PADRE', 'MADRE', 'OTRO'];
                    $parentesco = in_array($familiar['parentesco'], $parentescos_validos) 
                                ? $familiar['parentesco'] 
                                : 'OTRO';
                    
                    $stmt_familiar->bind_param(
                        "sssssiddddsss",
                        $ci_trabajador,
                        $familiar['cedula'],
                        $familiar['nombre_familiar'],
                        $familiar['apellido_familiar'],
                        $parentesco,
                        $familiar['edad'],
                        $familiar['peso'],
                        $familiar['altura'],
                        $familiar['talla_zapato'],
                        $familiar['talla_camisa'],
                        $familiar['talla_pantalon'],
                        $familiar['tipo_sangre'],
                        $familiar['fecha_registro']
                    );
                    
                    if (!$stmt_familiar->execute()) {
                        throw new Exception("Error al registrar familiar: " . $stmt_familiar->error);
                    }
                }
                
                $stmt_familiar->close();
            }
            
            $conn->commit();
            
            // ===== 7. PREPARAR RESPUESTA DE ÉXITO =====
            $_SESSION['form_success'] = true;
            $_SESSION['message'] = "✅ Trabajador registrado exitosamente" . 
                                  (count($familiares) > 0 ? " con " . count($familiares) . " familiar(es)." : ".");
            $_SESSION['empleado_id'] = $empleado_id;
            $_SESSION['ci_trabajador'] = $datos['ci'];
            
            // Guardar datos para estadísticas opcionales
            $_SESSION['last_registration'] = [
                'ci' => $datos['ci'],
                'nombre' => $datos['primer_nombre'] . ' ' . $datos['primer_apellido'],
                'fecha' => date('d/m/Y H:i'),
                'familiares' => count($familiares)
            ];
            
            // Redirigir DE VUELTA al formulario
            header("Location: ../trabajadores/formulario1.php");
            exit();
            
        } catch (Exception $e) {
            $conn->rollback();
            $errores[] = "Error en el registro: " . $e->getMessage();
            error_log("Error en registro: " . $e->getMessage());
        }
    }
    
    // Si hay errores, mostrar en la sesión
    if (!empty($errores)) {
        $_SESSION['error'] = implode("<br>", $errores);
        // Guardar datos del formulario para repoblar
        $_SESSION['form_data'] = $_POST;
        header("Location: ../trabajadores/formulario1.php");
        exit();
    }
} else {
    // Si no es POST, redirigir al formulario
    header("Location: ../trabajadores/formulario1.php");
    exit();
}

$conn->close();
?>