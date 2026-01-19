<?php
// usuario/trabajadores/nuevo_expediente.php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: ../admin/login.php");
    exit();
}

require_once '../includes/database.php';

// Validar que el empleado viene por parámetro (OBLIGATORIO)
$empleado_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($empleado_id === 0) {
    // Redirigir a la lista de empleados si no viene ID
    header("Location: expedientes.php?error=Seleccione un empleado primero");
    exit();
}

// Obtener información del empleado
$empleado = null;
$stmt = $conn->prepare("SELECT id, ci, CONCAT(primer_nombre, ' ', primer_apellido) as nombre_completo FROM empleados WHERE id = ?");
$stmt->bind_param("i", $empleado_id);
$stmt->execute();
$empleado = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$empleado) {
    header("Location: expedientes.php?error=Empleado no encontrado");
    exit();
}

// Tipos de documentos
$tipos_documentos = [
    'cedula_frontal' => 'Cédula (Frente)',
    'cedula_reverso' => 'Cédula (Reverso)',
    'curriculum' => 'Currículum Vitae',
    'titulos' => 'Títulos Académicos',
    'certificaciones' => 'Certificaciones',
    'contrato' => 'Contrato de Trabajo',
    'evaluaciones' => 'Evaluaciones',
    'foto_carnet' => 'Foto Carnet',
    'formacion_academica' => 'Formación Académica',
    'experiencia_laboral' => 'Experiencia Laboral',
    'carnet_salud' => 'Carnet de Salud',
    'otros' => 'Otros Documentos'
];

// Procesar subida
$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['documento'])) {
    $tipo_documento = $_POST['tipo_documento'];
    $descripcion = trim($_POST['descripcion']);
    
    if ($_FILES['documento']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['documento'];
        
        // Validar tipo MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $mimes_permitidos = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx'
        ];
        
        if (!array_key_exists($mime_type, $mimes_permitidos)) {
            $error = "Tipo de archivo no permitido. Solo se aceptan PDF, imágenes y documentos Word";
        } elseif ($file['size'] > 10485760) { // 10MB máximo
            $error = "El archivo es muy grande (máximo 10MB)";
        } else {
            // Leer archivo
            $contenido = file_get_contents($file['tmp_name']);
            
            // Insertar en BD
            $stmt = $conn->prepare("INSERT INTO expedientes (empleado_id, tipo_documento, nombre_original, descripcion, contenido, mime_type, tamaño, subido_por) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $null = NULL;
            $stmt->bind_param("isssbsis", 
                $empleado_id, 
                $tipo_documento,
                $file['name'],
                $descripcion,
                $null,
                $mime_type,
                $file['size'],
                $_SESSION['username']
            );
            
            $stmt->send_long_data(4, $contenido);
            
            if ($stmt->execute()) {
                $mensaje = "✅ Documento subido exitosamente";
                // Redirigir al expediente con mensaje de éxito
                header("Location: ver_expediente.php?id=$empleado_id&subido=1");
                exit();
            } else {
                $error = "❌ Error al guardar: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $error = "Error en la subida del archivo: " . $_FILES['documento']['error'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Expediente - SAINA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6a67f0;
            --purple-start: #a8a0f9;
            --blue-end: #6162f4;
            --text-color: #333;
            --light-text: #777;
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
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 25px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-content h1 {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }
        
        .header-breadcrumb {
            font-size: 14px;
            color: var(--light-text);
        }
        
        .header-breadcrumb a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .header-breadcrumb a:hover {
            text-decoration: underline;
        }
        
        .card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(106, 103, 240, 0.1);
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 25px;
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 16px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(106, 103, 240, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: rgba(67, 233, 123, 0.1);
            color: #27ae60;
            border: 1px solid #43e97b;
        }
        
        .alert-error {
            background: rgba(255, 107, 107, 0.1);
            color: #e74c3c;
            border: 1px solid #ff6b6b;
        }
        
        .empleado-info {
            background: linear-gradient(135deg, rgba(168, 160, 249, 0.1), rgba(97, 98, 244, 0.1));
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            border-left: 4px solid var(--primary-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .empleado-datos h3 {
            color: var(--primary-color);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .empleado-datos p {
            margin-bottom: 5px;
            color: var(--text-color);
        }
        
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            margin-top: 5px;
        }
        
        .file-input-wrapper input[type=file] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-input-label {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            background: #f8f9fa;
            border: 2px dashed #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            justify-content: center;
        }
        
        .file-input-label:hover {
            border-color: var(--primary-color);
            background: rgba(106, 103, 240, 0.05);
        }
        
        .file-info {
            margin-top: 10px;
            font-size: 14px;
            color: var(--light-text);
        }
        
        .file-info span {
            display: block;
            margin-top: 5px;
        }
        
        .info-text {
            color: var(--light-text);
            font-size: 14px;
            margin-top: 5px;
        }
        
        .acciones {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .empleado-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .acciones {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <h1><i class="fas fa-file-upload"></i> Agregar Documento</h1>
                <div class="header-breadcrumb">
                    <a href="expedientes.php">Expedientes</a> → 
                    <a href="ver_expediente.php?id=<?php echo $empleado['id']; ?>">
                        <?php echo htmlspecialchars($empleado['nombre_completo']); ?>
                    </a> → Nuevo Documento
                </div>
            </div>
            <a href="ver_expediente.php?id=<?php echo $empleado['id']; ?>" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Volver al Expediente
            </a>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="empleado-info">
            <div class="empleado-datos">
                <h3><i class="fas fa-user-check"></i> Empleado Seleccionado</h3>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($empleado['nombre_completo']); ?></p>
                <p><strong>Cédula:</strong> <?php echo htmlspecialchars($empleado['ci']); ?></p>
            </div>
            <a href="ver_expediente.php?id=<?php echo $empleado['id']; ?>" class="btn">
                <i class="fas fa-folder-open"></i> Ver Expediente
            </a>
        </div>
        
        <div class="card">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="empleado_id" value="<?php echo $empleado['id']; ?>">
                
                <div class="form-group">
                    <label for="tipo_documento"><i class="fas fa-file-alt"></i> Tipo de Documento</label>
                    <select id="tipo_documento" name="tipo_documento" class="form-control" required>
                        <option value="">-- Seleccione el tipo de documento --</option>
                        <?php foreach($tipos_documentos as $key => $value): ?>
                            <option value="<?php echo $key; ?>" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] == $key) ? 'selected' : ''; ?>>
                                <?php echo $value; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="info-text">
                        Seleccione la categoría del documento que está subiendo
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="descripcion"><i class="fas fa-align-left"></i> Descripción (opcional)</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" rows="3" 
                              placeholder="Ej: Contrato firmado el 15/01/2024, Cédula actualizada, Título universitario, etc."></textarea>
                    <div class="info-text">
                        Agregue una descripción que ayude a identificar el documento
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-file-upload"></i> Documento a Subir</label>
                    <div class="file-input-wrapper">
                        <div class="file-input-label" id="fileLabel">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 24px; color: var(--primary-color);"></i>
                            <div>
                                <div style="font-weight: 600; color: var(--primary-color);">Haga clic para seleccionar archivo</div>
                                <div style="color: var(--light-text); font-size: 14px; margin-top: 5px;">
                                    Formatos permitidos: PDF, JPG, PNG, DOC, DOCX<br>
                                    Tamaño máximo: 10MB
                                </div>
                            </div>
                        </div>
                        <input type="file" name="documento" id="documento" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required onchange="showFileInfo(this)">
                    </div>
                    <div class="file-info" id="fileInfo">
                        <!-- Aquí se mostrará la información del archivo -->
                    </div>
                </div>
                
                <div class="acciones">
                    <button type="submit" class="btn">
                        <i class="fas fa-upload"></i> Subir Documento
                    </button>
                    <a href="ver_expediente.php?id=<?php echo $empleado['id']; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function showFileInfo(input) {
            const label = document.getElementById('fileLabel');
            const info = document.getElementById('fileInfo');
            
            if (input.files.length > 0) {
                const file = input.files[0];
                const sizeMB = (file.size / 1024 / 1024).toFixed(2);
                const extension = file.name.split('.').pop().toUpperCase();
                
                // Actualizar label
                label.innerHTML = `
                    <i class="fas fa-file" style="font-size: 24px; color: var(--primary-color);"></i>
                    <div>
                        <div style="font-weight: 600; color: var(--primary-color);">Archivo seleccionado</div>
                        <div style="color: var(--light-text); font-size: 14px; margin-top: 5px;">
                            ${file.name}
                        </div>
                    </div>
                `;
                
                // Mostrar información
                let fileType = '';
                if (file.type.includes('pdf')) fileType = '📄 Documento PDF';
                else if (file.type.includes('image')) fileType = '🖼️ Imagen';
                else if (file.type.includes('word')) fileType = '📝 Documento Word';
                else fileType = '📁 Archivo';
                
                info.innerHTML = `
                    <span><strong>Tipo:</strong> ${fileType} (${extension})</span>
                    <span><strong>Tamaño:</strong> ${sizeMB} MB</span>
                `;
                
                // Validar tamaño
                if (file.size > 10485760) { // 10MB
                    info.innerHTML += '<span style="color: #e74c3c;"><strong>⚠️ Error:</strong> El archivo es muy grande (máx. 10MB)</span>';
                    input.value = '';
                    label.innerHTML = `
                        <i class="fas fa-cloud-upload-alt" style="font-size: 24px; color: var(--primary-color);"></i>
                        <div>
                            <div style="font-weight: 600; color: var(--primary-color);">Haga clic para seleccionar archivo</div>
                            <div style="color: var(--light-text); font-size: 14px; margin-top: 5px;">
                                Formatos permitidos: PDF, JPG, PNG, DOC, DOCX<br>
                                Tamaño máximo: 10MB
                            </div>
                        </div>
                    `;
                    info.innerHTML = '';
                }
            } else {
                // Restaurar label original
                label.innerHTML = `
                    <i class="fas fa-cloud-upload-alt" style="font-size: 24px; color: var(--primary-color);"></i>
                    <div>
                        <div style="font-weight: 600; color: var(--primary-color);">Haga clic para seleccionar archivo</div>
                        <div style="color: var(--light-text); font-size: 14px; margin-top: 5px;">
                            Formatos permitidos: PDF, JPG, PNG, DOC, DOCX<br>
                            Tamaño máximo: 10MB
                        </div>
                    </div>
                `;
                info.innerHTML = '';
            }
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>