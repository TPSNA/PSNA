<?php
// admin/trabajadores/editar.php - EDITAR TRABAJADOR
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$error = '';
$success = '';
$trabajador = null;

// Si se pasa ID, obtener datos del trabajador
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $sql = "SELECT * FROM empleados WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $trabajador = $result->fetch_assoc();
    } else {
        $error = "Trabajador no encontrado";
    }
    $stmt->close();
} else {
    header("Location: index.php");
    exit();
}

// Procesar formulario de actualización
if ($_SERVER["REQUEST_METHOD"] == "POST" && $trabajador) {
    // Recoger datos del formulario
    $datos = [
        'nacionalidad' => $_POST['nacionalidad'] ?? '',
        'ci' => $_POST['ci'] ?? '',
        'primer_nombre' => $_POST['primer_nombre'] ?? '',
        'segundo_nombre' => $_POST['segundo_nombre'] ?? '',
        'primer_apellido' => $_POST['primer_apellido'] ?? '',
        'segundo_apellido' => $_POST['segundo_apellido'] ?? '',
        'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? '',
        'sexo' => $_POST['sexo'] ?? '',
        'estado_civil' => $_POST['estado_civil'] ?? '',
        'direccion_ubicacion' => $_POST['direccion_ubicacion'] ?? '',
        'telefono' => $_POST['telefono'] ?? '',
        'correo' => $_POST['correo'] ?? '',
        'cuenta_bancaria' => $_POST['cuenta_bancaria'] ?? '',
        'tipo_trabajador' => $_POST['tipo_trabajador'] ?? '',
        'grado_instruccion' => $_POST['grado_instruccion'] ?? '',
        'cargo' => $_POST['cargo'] ?? '',
        'sede' => $_POST['sede'] ?? '',
        'dependencia' => $_POST['dependencia'] ?? '',
        'fecha_ingreso' => $_POST['fecha_ingreso'] ?? '',
        'cod_siantel' => $_POST['cod_siantel'] ?? '',
        'ubicacion_estante' => $_POST['ubicacion_estante'] ?? '',
        'estatus' => $_POST['estatus'] ?? '',
        'tipo_sangre' => $_POST['tipo_sangre'] ?? '',
        'lateralidad' => $_POST['lateralidad'] ?? '',
        'peso_trabajador' => $_POST['peso_trabajador'] ?? '',
        'altura_trabajador' => $_POST['altura_trabajador'] ?? '',
        'calzado_trabajador' => $_POST['calzado_trabajador'] ?? '',
        'camisa_trabajador' => $_POST['camisa_trabajador'] ?? '',
        'pantalon_trabajador' => $_POST['pantalon_trabajador'] ?? ''
    ];
    
    // Si está inactivo, agregar campos de retiro
    if ($datos['estatus'] == 'INACTIVO') {
        $datos['fecha_egreso'] = $_POST['fecha_egreso'] ?? '';
        $datos['motivo_retiro'] = $_POST['motivo_retiro'] ?? '';
        $datos['ubicacion_estante_retiro'] = $_POST['ubicacion_estante_retiro'] ?? '';
    } else {
        $datos['fecha_egreso'] = NULL;
        $datos['motivo_retiro'] = NULL;
        $datos['ubicacion_estante_retiro'] = NULL;
    }
    
    // Validar datos mínimos
    if (empty($datos['ci']) || empty($datos['primer_nombre']) || empty($datos['primer_apellido'])) {
        $error = "CI, primer nombre y primer apellido son requeridos";
    } else {
        try {
            // Verificar si el CI ya existe (excluyendo el actual)
            $sql_check = "SELECT id FROM empleados WHERE ci = ? AND id != ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("si", $datos['ci'], $id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows > 0) {
                $error = "El CI ya está registrado para otro trabajador";
            } else {
                // Preparar consulta de actualización
                $sql_update = "UPDATE empleados SET 
                    nacionalidad = ?, ci = ?, primer_nombre = ?, segundo_nombre = ?,
                    primer_apellido = ?, segundo_apellido = ?, fecha_nacimiento = ?,
                    sexo = ?, estado_civil = ?, direccion_ubicacion = ?, telefono = ?,
                    correo = ?, cuenta_bancaria = ?, tipo_trabajador = ?,
                    grado_instruccion = ?, cargo = ?, sede = ?, dependencia = ?,
                    fecha_ingreso = ?, cod_siantel = ?, ubicacion_estante = ?,
                    estatus = ?, fecha_egreso = ?, motivo_retiro = ?,
                    ubicacion_estante_retiro = ?, tipo_sangre = ?, lateralidad = ?,
                    peso_trabajador = ?, altura_trabajador = ?, calzado_trabajador = ?,
                    camisa_trabajador = ?, pantalon_trabajador = ?
                    WHERE id = ?";
                
                $stmt_update = $conn->prepare($sql_update);
                
                // Bind parameters
                $stmt_update->bind_param("ssssssssssssssssssssssssssssssssi",
                    $datos['nacionalidad'], $datos['ci'], $datos['primer_nombre'], $datos['segundo_nombre'],
                    $datos['primer_apellido'], $datos['segundo_apellido'], $datos['fecha_nacimiento'],
                    $datos['sexo'], $datos['estado_civil'], $datos['direccion_ubicacion'], $datos['telefono'],
                    $datos['correo'], $datos['cuenta_bancaria'], $datos['tipo_trabajador'],
                    $datos['grado_instruccion'], $datos['cargo'], $datos['sede'], $datos['dependencia'],
                    $datos['fecha_ingreso'], $datos['cod_siantel'], $datos['ubicacion_estante'],
                    $datos['estatus'], $datos['fecha_egreso'], $datos['motivo_retiro'],
                    $datos['ubicacion_estante_retiro'], $datos['tipo_sangre'], $datos['lateralidad'],
                    $datos['peso_trabajador'], $datos['altura_trabajador'], $datos['calzado_trabajador'],
                    $datos['camisa_trabajador'], $datos['pantalon_trabajador'],
                    $id
                );
                
                if ($stmt_update->execute()) {
                    $success = "Trabajador actualizado correctamente";
                    // Actualizar datos locales
                    $trabajador = array_merge($trabajador, $datos);
                } else {
                    $error = "Error al actualizar: " . $conn->error;
                }
                $stmt_update->close();
            }
            $stmt_check->close();
            
        } catch (Exception $e) {
            $error = "Error en el sistema: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Trabajador - SAINA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --purple-start: #a8a0f9;
            --blue-end: #6162f4;
            --primary-color: #6a67f0;
            --text-color: #333;
            --light-text: #777;
            --card-background: rgba(255, 255, 255, 0.95);
            --shadow-light: rgba(0, 0, 0, 0.1);
            --shadow-focus: rgba(106, 103, 240, 0.2);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }
        
        body {
            background: #F0F4F3;
            color: var(--text-color);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        
        .header-title {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .header-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        
        .header-content h1 {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .alert {
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 500;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
        }
        
        .alert-error {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
        }
        
        .form-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .form-group label {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-group label i {
            color: var(--primary-color);
            font-size: 16px;
        }
        
        .required::after {
            content: " *";
            color: #ff6b6b;
        }
        
        .form-control {
            padding: 14px 18px;
            border: 2px solid #e6e6e6;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s;
            background: white;
            color: var(--text-color);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px var(--shadow-focus);
        }
        
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236a67f0' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 18px center;
            background-size: 16px;
            padding-right: 45px;
        }
        
        .form-section {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 2px solid rgba(0,0,0,0.05);
        }
        
        .section-title {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .section-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, rgba(106, 103, 240, 0.1), rgba(168, 160, 249, 0.1));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 20px;
        }
        
        .section-title h2 {
            font-size: 22px;
            font-weight: 600;
            color: var(--text-color);
        }
        
        .form-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
            padding-top: 40px;
            border-top: 2px solid rgba(0,0,0,0.05);
        }
        
        .btn {
            padding: 16px 32px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            color: white;
            box-shadow: 0 5px 15px rgba(106, 103, 240, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(106, 103, 240, 0.4);
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
        }
        
        .form-extra {
            display: none;
            margin-top: 30px;
            padding: 25px;
            background: rgba(106, 103, 240, 0.05);
            border-radius: 15px;
            border-left: 4px solid var(--primary-color);
        }
        
        .form-extra.show {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <div class="header-title">
                <div class="header-icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <div class="header-content">
                    <h1>Editar Trabajador</h1>
                    <p style="color: var(--light-text); font-size: 14px;">
                        ID: <?php echo $trabajador['id']; ?> • 
                        CI: <?php echo htmlspecialchars($trabajador['ci']); ?> • 
                        Nombre: <?php echo htmlspecialchars($trabajador['primer_nombre'] . ' ' . $trabajador['primer_apellido']); ?>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- MENSAJES -->
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <!-- FORMULARIO -->
        <div class="form-container">
            <form method="POST" action="" id="formEditar">
                <!-- DATOS PERSONALES -->
                <div class="form-section">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <h2>Datos Personales</h2>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nacionalidad" class="required"><i class="fas fa-globe"></i> Nacionalidad</label>
                            <select id="nacionalidad" name="nacionalidad" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="VENEZOLANO(A)" <?php echo $trabajador['nacionalidad'] == 'VENEZOLANO(A)' ? 'selected' : ''; ?>>VENEZOLANO(A)</option>
                                <option value="EXTRANJERO(A)" <?php echo $trabajador['nacionalidad'] == 'EXTRANJERO(A)' ? 'selected' : ''; ?>>EXTRANJERO(A)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="ci" class="required"><i class="fas fa-id-card"></i> Cédula de Identidad</label>
                            <input type="text" id="ci" name="ci" class="form-control" 
                                   value="<?php echo htmlspecialchars($trabajador['ci']); ?>" 
                                   placeholder="Ej: V-12345678" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="primer_nombre" class="required"><i class="fas fa-user"></i> Primer Nombre</label>
                            <input type="text" id="primer_nombre" name="primer_nombre" class="form-control"
                                   value="<?php echo htmlspecialchars($trabajador['primer_nombre']); ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="segundo_nombre"><i class="fas fa-user"></i> Segundo Nombre</label>
                            <input type="text" id="segundo_nombre" name="segundo_nombre" class="form-control"
                                   value="<?php echo htmlspecialchars($trabajador['segundo_nombre']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="primer_apellido" class="required"><i class="fas fa-user"></i> Primer Apellido</label>
                            <input type="text" id="primer_apellido" name="primer_apellido" class="form-control"
                                   value="<?php echo htmlspecialchars($trabajador['primer_apellido']); ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="segundo_apellido"><i class="fas fa-user"></i> Segundo Apellido</label>
                            <input type="text" id="segundo_apellido" name="segundo_apellido" class="form-control"
                                   value="<?php echo htmlspecialchars($trabajador['segundo_apellido']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="fecha_nacimiento" class="required"><i class="fas fa-birthday-cake"></i> Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control"
                                   value="<?php echo $trabajador['fecha_nacimiento']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="sexo" class="required"><i class="fas fa-venus-mars"></i> Sexo</label>
                            <select id="sexo" name="sexo" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="MASCULINO" <?php echo $trabajador['sexo'] == 'MASCULINO' ? 'selected' : ''; ?>>MASCULINO</option>
                                <option value="FEMENINO" <?php echo $trabajador['sexo'] == 'FEMENINO' ? 'selected' : ''; ?>>FEMENINO</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="estado_civil" class="required"><i class="fas fa-heart"></i> Estado Civil</label>
                            <select id="estado_civil" name="estado_civil" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="SOLTERO(A)" <?php echo $trabajador['estado_civil'] == 'SOLTERO(A)' ? 'selected' : ''; ?>>SOLTERO(A)</option>
                                <option value="CASADO(A)" <?php echo $trabajador['estado_civil'] == 'CASADO(A)' ? 'selected' : ''; ?>>CASADO(A)</option>
                                <option value="DIVORCIADO(A)" <?php echo $trabajador['estado_civil'] == 'DIVORCIADO(A)' ? 'selected' : ''; ?>>DIVORCIADO(A)</option>
                                <option value="VIUDO(A)" <?php echo $trabajador['estado_civil'] == 'VIUDO(A)' ? 'selected' : ''; ?>>VIUDO(A)</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- INFORMACIÓN LABORAL -->
                <div class="form-section">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h2>Información Laboral</h2>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="tipo_trabajador" class="required"><i class="fas fa-user-tie"></i> Tipo de Trabajador</label>
                            <select id="tipo_trabajador" name="tipo_trabajador" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="CTD" <?php echo $trabajador['tipo_trabajador'] == 'CTD' ? 'selected' : ''; ?>>CTD</option>
                                <option value="CTI" <?php echo $trabajador['tipo_trabajador'] == 'CTI' ? 'selected' : ''; ?>>CTI</option>
                                <option value="LNR" <?php echo $trabajador['tipo_trabajador'] == 'LNR' ? 'selected' : ''; ?>>LNR</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="grado_instruccion" class="required"><i class="fas fa-graduation-cap"></i> Grado de Instrucción</label>
                            <select id="grado_instruccion" name="grado_instruccion" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="PRIMARIA" <?php echo $trabajador['grado_instruccion'] == 'PRIMARIA' ? 'selected' : ''; ?>>PRIMARIA</option>
                                <option value="BACHILLER" <?php echo $trabajador['grado_instruccion'] == 'BACHILLER' ? 'selected' : ''; ?>>BACHILLER</option>
                                <option value="TSU" <?php echo $trabajador['grado_instruccion'] == 'TSU' ? 'selected' : ''; ?>>TSU</option>
                                <option value="LICENCIADO" <?php echo $trabajador['grado_instruccion'] == 'LICENCIADO' ? 'selected' : ''; ?>>LICENCIADO</option>
                                <option value="INGENIERO" <?php echo $trabajador['grado_instruccion'] == 'INGENIERO' ? 'selected' : ''; ?>>INGENIERO</option>
                                <option value="ESPECIALISTA" <?php echo $trabajador['grado_instruccion'] == 'ESPECIALISTA' ? 'selected' : ''; ?>>ESPECIALISTA</option>
                                <option value="MAESTRIA" <?php echo $trabajador['grado_instruccion'] == 'MAESTRIA' ? 'selected' : ''; ?>>MAESTRIA</option>
                                <option value="DOCTORADO" <?php echo $trabajador['grado_instruccion'] == 'DOCTORADO' ? 'selected' : ''; ?>>DOCTORADO</option>
                                <option value="NINGUNO" <?php echo $trabajador['grado_instruccion'] == 'NINGUNO' ? 'selected' : ''; ?>>NINGUNO</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="cargo" class="required"><i class="fas fa-briefcase"></i> Cargo</label>
                            <input type="text" id="cargo" name="cargo" class="form-control"
                                   value="<?php echo htmlspecialchars($trabajador['cargo']); ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="sede" class="required"><i class="fas fa-building"></i> Sede</label>
                            <select id="sede" name="sede" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="ADMIN" <?php echo $trabajador['sede'] == 'ADMIN' ? 'selected' : ''; ?>>ADMIN</option>
                                <option value="CAFO" <?php echo $trabajador['sede'] == 'CAFO' ? 'selected' : ''; ?>>CAFO</option>
                                <option value="CATE" <?php echo $trabajador['sede'] == 'CATE' ? 'selected' : ''; ?>>CATE</option>
                                <option value="CSAI" <?php echo $trabajador['sede'] == 'CSAI' ? 'selected' : ''; ?>>CSAI</option>
                                <option value="CSB" <?php echo $trabajador['sede'] == 'CSB' ? 'selected' : ''; ?>>CSB</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="dependencia" class="required"><i class="fas fa-sitemap"></i> Dependencia</label>
                            <select id="dependencia" name="dependencia" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="ADMIN" <?php echo $trabajador['dependencia'] == 'ADMIN' ? 'selected' : ''; ?>>ADMIN</option>
                                <option value="CAFO" <?php echo $trabajador['dependencia'] == 'CAFO' ? 'selected' : ''; ?>>CAFO</option>
                                <option value="CATE" <?php echo $trabajador['dependencia'] == 'CATE' ? 'selected' : ''; ?>>CATE</option>
                                <option value="CSAI" <?php echo $trabajador['dependencia'] == 'CSAI' ? 'selected' : ''; ?>>CSAI</option>
                                <option value="CSB" <?php echo $trabajador['dependencia'] == 'CSB' ? 'selected' : ''; ?>>CSB</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="fecha_ingreso" class="required"><i class="fas fa-calendar-alt"></i> Fecha de Ingreso</label>
                            <input type="date" id="fecha_ingreso" name="fecha_ingreso" class="form-control"
                                   value="<?php echo $trabajador['fecha_ingreso']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="cod_siantel"><i class="fas fa-id-badge"></i> Código SIANTEL</label>
                            <input type="text" id="cod_siantel" name="cod_siantel" class="form-control"
                                   value="<?php echo htmlspecialchars($trabajador['cod_siantel']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="ubicacion_estante"><i class="fas fa-archive"></i> Ubicación Estante</label>
                            <input type="text" id="ubicacion_estante" name="ubicacion_estante" class="form-control"
                                   value="<?php echo htmlspecialchars($trabajador['ubicacion_estante']); ?>"
                                   placeholder="Ej: Estante A-5">
                        </div>
                        
                        <div class="form-group">
                            <label for="estatus" class="required"><i class="fas fa-check-circle"></i> Estatus</label>
                            <select id="estatus" name="estatus" class="form-control" required>
                                <option value="ACTIVO" <?php echo $trabajador['estatus'] == 'ACTIVO' ? 'selected' : ''; ?>>ACTIVO</option>
                                <option value="INACTIVO" <?php echo $trabajador['estatus'] == 'INACTIVO' ? 'selected' : ''; ?>>INACTIVO</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- FORMULARIO EXTRA PARA INACTIVOS -->
                    <div id="formExtra" class="form-extra <?php echo $trabajador['estatus'] == 'INACTIVO' ? 'show' : ''; ?>">
                        <div class="section-title" style="margin-bottom: 20px;">
                            <div class="section-icon">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                            <h2 style="font-size: 18px;">Información de Retiro</h2>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="fecha_egreso"><i class="fas fa-calendar-times"></i> Fecha de Egreso</label>
                                <input type="date" id="fecha_egreso" name="fecha_egreso" class="form-control"
                                       value="<?php echo $trabajador['fecha_egreso']; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="motivo_retiro"><i class="fas fa-comment-alt"></i> Motivo del Retiro</label>
                                <textarea id="motivo_retiro" name="motivo_retiro" class="form-control" 
                                          rows="3" placeholder="Describe el motivo del retiro"><?php echo htmlspecialchars($trabajador['motivo_retiro']); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="ubicacion_estante_retiro"><i class="fas fa-archive"></i> Ubicación Estante (Retiro)</label>
                                <input type="text" id="ubicacion_estante_retiro" name="ubicacion_estante_retiro" class="form-control"
                                       value="<?php echo htmlspecialchars($trabajador['ubicacion_estante_retiro']); ?>"
                                       placeholder="Ej: Estante B-3">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- CONTACTO Y UBICACIÓN -->
                <div class="form-section">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h2>Contacto y Ubicación</h2>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="direccion_ubicacion" class="required"><i class="fas fa-home"></i> Dirección</label>
                            <input type="text" id="direccion_ubicacion" name="direccion_ubicacion" class="form-control"
                                   value="<?php echo htmlspecialchars($trabajador['direccion_ubicacion']); ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="telefono" class="required"><i class="fas fa-phone"></i> Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" class="form-control"
                                   value="<?php echo htmlspecialchars($trabajador['telefono']); ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="correo" class="required"><i class="fas fa-envelope"></i> Correo Electrónico</label>
                            <input type="email" id="correo" name="correo" class="form-control"
                                   value="<?php echo htmlspecialchars($trabajador['correo']); ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="cuenta_bancaria"><i class="fas fa-credit-card"></i> Cuenta Bancaria</label>
                            <input type="text" id="cuenta_bancaria" name="cuenta_bancaria" class="form-control"
                                   value="<?php echo htmlspecialchars($trabajador['cuenta_bancaria']); ?>">
                        </div>
                    </div>
                </div>
                
                <!-- INFORMACIÓN GENERAL -->
                <div class="form-section">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h2>Información General</h2>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="tipo_sangre"><i class="fas fa-tint"></i> Tipo de Sangre</label>
                            <select id="tipo_sangre" name="tipo_sangre" class="form-control">
                                <option value="">Seleccione...</option>
                                <option value="A+" <?php echo $trabajador['tipo_sangre'] == 'A+' ? 'selected' : ''; ?>>A+</option>
                                <option value="A-" <?php echo $trabajador['tipo_sangre'] == 'A-' ? 'selected' : ''; ?>>A-</option>
                                <option value="B+" <?php echo $trabajador['tipo_sangre'] == 'B+' ? 'selected' : ''; ?>>B+</option>
                                <option value="B-" <?php echo $trabajador['tipo_sangre'] == 'B-' ? 'selected' : ''; ?>>B-</option>
                                <option value="AB+" <?php echo $trabajador['tipo_sangre'] == 'AB+' ? 'selected' : ''; ?>>AB+</option>
                                <option value="AB-" <?php echo $trabajador['tipo_sangre'] == 'AB-' ? 'selected' : ''; ?>>AB-</option>
                                <option value="O+" <?php echo $trabajador['tipo_sangre'] == 'O+' ? 'selected' : ''; ?>>O+</option>
                                <option value="O-" <?php echo $trabajador['tipo_sangre'] == 'O-' ? 'selected' : ''; ?>>O-</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="lateralidad"><i class="fas fa-hand-point-up"></i> Lateralidad</label>
                            <select id="lateralidad" name="lateralidad" class="form-control">
                                <option value="">Seleccione...</option>
                                <option value="DIESTRO(A)" <?php echo $trabajador['lateralidad'] == 'DIESTRO(A)' ? 'selected' : ''; ?>>DIESTRO(A)</option>
                                <option value="ZURDO(A)" <?php echo $trabajador['lateralidad'] == 'ZURDO(A)' ? 'selected' : ''; ?>>ZURDO(A)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="peso_trabajador"><i class="fas fa-weight"></i> Peso (kg)</label>
                            <input type="number" id="peso_trabajador" name="peso_trabajador" class="form-control"
                                   value="<?php echo htmlspecialchars($trabajador['peso_trabajador']); ?>"
                                   step="0.1" placeholder="Ej: 70.5">
                        </div>
                        
                        <div class="form-group">
                            <label for="altura_trabajador"><i class="fas fa-ruler-vertical"></i> Altura (cm)</label>
                            <input type="number" id="altura_trabajador" name="altura_trabajador" class="form-control"
                                   value="<?php echo htmlspecialchars($trabajador['altura_trabajador']); ?>"
                                   placeholder="Ej: 175">
                        </div>
                        
                        <div class="form-group">
                            <label for="calzado_trabajador"><i class="fas fa-shoe-prints"></i> Talla Calzado</label>
                            <input type="text" id="calzado_trabajador" name="calzado_trabajador" class="form-control"
                                   value="<?php echo htmlspecialchars($trabajador['calzado_trabajador']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="camisa_trabajador"><i class="fas fa-tshirt"></i> Talla Camisa</label>
                            <input type="text" id="camisa_trabajador" name="camisa_trabajador" class="form-control"
                                   value="<?php echo htmlspecialchars($trabajador['camisa_trabajador']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="pantalon_trabajador"><i class="fas fa-tshirt"></i> Talla Pantalón</label>
                            <input type="text" id="pantalon_trabajador" name="pantalon_trabajador" class="form-control"
                                   value="<?php echo htmlspecialchars($trabajador['pantalon_trabajador']); ?>">
                        </div>
                    </div>
                </div>
                
                <!-- ACCIONES -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    
                    <a href="ver.php?id=<?php echo $trabajador['id']; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    
                    <a href="index.php" class="btn" style="background: #95a5a6; color: white;">
                        <i class="fas fa-list"></i> Volver al Listado
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Mostrar/ocultar formulario extra según estatus
        document.getElementById('estatus').addEventListener('change', function() {
            const formExtra = document.getElementById('formExtra');
            if (this.value === 'INACTIVO') {
                formExtra.classList.add('show');
            } else {
                formExtra.classList.remove('show');
            }
        });
        
        // Validación de formulario
        document.getElementById('formEditar').addEventListener('submit', function(e) {
            const ci = document.getElementById('ci').value.trim();
            const primerNombre = document.getElementById('primer_nombre').value.trim();
            const primerApellido = document.getElementById('primer_apellido').value.trim();
            const fechaNacimiento = document.getElementById('fecha_nacimiento').value;
            const fechaIngreso = document.getElementById('fecha_ingreso').value;
            const estatus = document.getElementById('estatus').value;
            const fechaEgreso = document.getElementById('fecha_egreso')?.value;
            
            // Validar fechas
            if (fechaNacimiento && fechaIngreso) {
                const nacimiento = new Date(fechaNacimiento);
                const ingreso = new Date(fechaIngreso);
                
                if (ingreso <= nacimiento) {
                    e.preventDefault();
                    alert('La fecha de ingreso debe ser posterior a la fecha de nacimiento');
                    return false;
                }
            }
            
            // Si está inactivo, validar fecha de egreso
            if (estatus === 'INACTIVO' && fechaEgreso && fechaIngreso) {
                const egreso = new Date(fechaEgreso);
                const ingreso = new Date(fechaIngreso);
                
                if (egreso <= ingreso) {
                    e.preventDefault();
                    alert('La fecha de egreso debe ser posterior a la fecha de ingreso');
                    return false;
                }
            }
            
            // Validar formato de CI
            if (ci && !/^[VEve]-\d{5,8}$/.test(ci) && !/^\d{5,8}$/.test(ci)) {
                e.preventDefault();
                alert('Formato de CI inválido. Use: V-12345678 o solo números');
                return false;
            }
            
            return true;
        });
        
        // Efectos visuales
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-enfocar en primer campo
            document.querySelector('.form-control').focus();
            
            // Efectos en campos al hacer focus
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });
            
            // Validación en tiempo real para CI
            const ciInput = document.getElementById('ci');
            if (ciInput) {
                ciInput.addEventListener('input', function() {
                    const ci = this.value.trim();
                    if (ci && !/^[VEve]-\d{5,8}$/.test(ci) && !/^\d{5,8}$/.test(ci)) {
                        this.style.borderColor = '#ff6b6b';
                        this.style.boxShadow = '0 0 0 4px rgba(255, 107, 107, 0.2)';
                    } else {
                        this.style.borderColor = '#43e97b';
                        this.style.boxShadow = '0 0 0 4px rgba(67, 233, 123, 0.2)';
                    }
                });
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>