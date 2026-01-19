<?php
// usuario/trabajadores/ver_expediente.php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: ../admin/login.php");
    exit();
}

require_once '../includes/database.php';

$empleado_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($empleado_id === 0) {
    header("Location: expedientes.php");
    exit();
}

// Obtener información del empleado
$sql_empleado = "SELECT *, CONCAT(primer_nombre, ' ', primer_apellido) as nombre_completo FROM empleados WHERE id = ?";
$stmt = $conn->prepare($sql_empleado);
$stmt->bind_param("i", $empleado_id);
$stmt->execute();
$empleado = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$empleado) {
    header("Location: expedientes.php");
    exit();
}


// Tipos de documentos organizados por categorías
$categorias_documentos = [
    'documentos_identidad' => [
        'label' => '📋 Documentos de Identidad',
        'tipos' => ['cedula_frontal', 'cedula_reverso', 'foto_carnet']
    ],
    'documentos_laborales' => [
        'label' => '💼 Documentos Laborales',
        'tipos' => ['contrato', 'curriculum', 'experiencia_laboral', 'evaluaciones']
    ],
    'documentos_academicos' => [
        'label' => '🎓 Documentos Académicos',
        'tipos' => ['titulos', 'certificaciones', 'formacion_academica']
    ],
    'documentos_salud' => [
        'label' => '🏥 Documentos de Salud',
        'tipos' => ['carnet_salud']
    ],
    'otros' => [
        'label' => '📁 Otros Documentos',
        'tipos' => ['otros']
    ]
];

$nombres_tipos = [
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

// Procesar eliminación de documento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_documento'])) {
    $documento_id = intval($_POST['documento_id']);
    
    $sql_eliminar = "UPDATE expedientes SET estado = 'eliminado' WHERE id = ? AND empleado_id = ?";
    $stmt = $conn->prepare($sql_eliminar);
    $stmt->bind_param("ii", $documento_id, $empleado_id);
    $stmt->execute();
    $stmt->close();
    
    // Redirigir para evitar reenvío del formulario
    header("Location: ver_expediente.php?id=$empleado_id&eliminado=1");
    exit();
}

// Obtener todos los documentos del empleado
$sql_documentos = "SELECT * FROM expedientes WHERE empleado_id = ? AND estado = 'activo' ORDER BY tipo_documento, fecha_subida DESC";
$stmt = $conn->prepare($sql_documentos);
$stmt->bind_param("i", $empleado_id);
$stmt->execute();
$result_documentos = $stmt->get_result();
$documentos = [];
while ($doc = $result_documentos->fetch_assoc()) {
    $documentos[$doc['tipo_documento']][] = $doc;
}
$stmt->close();

// Estadísticas
$sql_stats = "SELECT 
                COUNT(*) as total_documentos,
                SUM(tamaño) as tamaño_total,
                MIN(fecha_subida) as fecha_primer,
                MAX(fecha_subida) as fecha_ultimo
              FROM expedientes 
              WHERE empleado_id = ? AND estado = 'activo'";
$stmt = $conn->prepare($sql_stats);
$stmt->bind_param("i", $empleado_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente de <?php echo htmlspecialchars($empleado['nombre_completo']); ?> - SAINA</title>
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
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 30px;
        }
        
        .avatar-empleado {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 36px;
            font-weight: bold;
            box-shadow: 0 10px 20px rgba(106, 103, 240, 0.3);
        }
        
        .info-empleado h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 10px;
        }
        
        .cedula-badge {
            background: var(--primary-color);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
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
        
        .btn-danger {
            background: #dc3545;
        }
        
        .btn-success {
            background: #28a745;
        }
        
        .estadisticas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .estadistica-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
            border-top: 4px solid var(--primary-color);
        }
        
        .estadistica-valor {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .estadistica-label {
            font-size: 14px;
            color: var(--light-text);
        }
        
        .categoria {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .categoria-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .categoria-titulo {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .documentos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
        }
        
        .documento-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .documento-card:hover {
            border-color: var(--primary-color);
            background: white;
            transform: translateY(-2px);
        }
        
        .documento-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
        }
        
        .documento-tipo {
            font-weight: 600;
            color: var(--text-color);
        }
        
        .documento-info {
            color: var(--light-text);
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .documento-acciones {
            display: flex;
            gap: 10px;
        }
        
        .accion-btn {
            flex: 1;
            padding: 8px 12px;
            border-radius: 6px;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-descargar {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .btn-descargar:hover {
            background: rgba(40, 167, 69, 0.2);
        }
        
        .btn-eliminar {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        
        .btn-eliminar:hover {
            background: rgba(220, 53, 69, 0.2);
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 500px;
            width: 90%;
            text-align: center;
        }
        
        .modal-acciones {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
        }
        
        .sin-documentos {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            color: var(--light-text);
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
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }
            
            .documentos-grid {
                grid-template-columns: 1fr;
            }
            
            .estadisticas {
                grid-template-columns: 1fr;
            }
            .btn-success {
                background: #28a745;
            }

            .btn-success:hover {
                background: #218838;
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(40, 167, 69, 0.3);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_GET['eliminado']) && $_GET['eliminado'] == '1'): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Documento eliminado exitosamente
        </div>
        <?php endif; ?>
        
        <div class="header">
            <div class="avatar-empleado">
                <?php echo strtoupper(substr($empleado['primer_nombre'], 0, 1)); ?>
            </div>
            <div style="flex: 1;">
                <h1><?php echo htmlspecialchars($empleado['primer_nombre'] . ' ' . $empleado['primer_apellido']); ?></h1>
                <div class="cedula-badge">
                    <i class="fas fa-id-card"></i> <?php echo htmlspecialchars($empleado['ci']); ?>
                </div>
                <p style="color: var(--light-text); margin-bottom: 15px;">
                    <i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($empleado['cargo']); ?> |
                    <i class="fas fa-building"></i> <?php echo htmlspecialchars($empleado['sede']); ?>
                </p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="../trabajadores/expedientes.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver a Expedientes
                    </a>
                    <!-- Agrega este botón NUEVO -->
                        <a href="../php/descargar_zip.php?id=<?php echo $empleado['id']; ?>" class="btn btn-success">
                            <i class="fas fa-file-archive"></i> Descargar ZIP
                        </a>
                    
                    <a href="../index.php" class="btn" style="background: #6c757d;">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas -->
        <div class="estadisticas">
            <div class="estadistica-card">
                <div class="estadistica-valor"><?php echo $stats['total_documentos'] ?? 0; ?></div>
                <div class="estadistica-label">Documentos Totales</div>
            </div>
            <div class="estadistica-card">
                <div class="estadistica-valor">
                    <?php echo $stats['tamaño_total'] ? round($stats['tamaño_total'] / 1024 / 1024, 2) . ' MB' : '0 MB'; ?>
                </div>
                <div class="estadistica-label">Tamaño Total</div>
            </div>
            <div class="estadistica-card">
                <div class="estadistica-valor">
                    <?php echo $stats['fecha_primer'] ? date('d/m/Y', strtotime($stats['fecha_primer'])) : 'N/A'; ?>
                </div>
                <div class="estadistica-label">Primer Documento</div>
            </div>
            <div class="estadistica-card">
                <div class="estadistica-valor">
                    <?php echo $stats['fecha_ultimo'] ? date('d/m/Y', strtotime($stats['fecha_ultimo'])) : 'N/A'; ?>
                </div>
                <div class="estadistica-label">Última Actualización</div>
            </div>
        </div>
        
        <!-- Documentos por categorías -->
        <?php foreach ($categorias_documentos as $categoria_key => $categoria): ?>
        <div class="categoria">
            <div class="categoria-header">
                <div class="categoria-titulo">
                    <?php echo $categoria['label']; ?>
                </div>
                <span style="color: var(--light-text); font-size: 14px;">
                    <?php 
                        $count = 0;
                        foreach ($categoria['tipos'] as $tipo) {
                            if (isset($documentos[$tipo])) {
                                $count += count($documentos[$tipo]);
                            }
                        }
                        echo $count . ' documento(s)';
                    ?>
                </span>
            </div>
            
            <div class="documentos-grid">
                <?php 
                $tiene_documentos = false;
                foreach ($categoria['tipos'] as $tipo):
                    if (isset($documentos[$tipo])):
                        $tiene_documentos = true;
                        foreach ($documentos[$tipo] as $doc): 
                ?>
                <div class="documento-card">
                    <div class="documento-header">
                        <div class="documento-tipo"><?php echo $nombres_tipos[$tipo]; ?></div>
                        <span style="color: var(--light-text); font-size: 12px;">
                            <?php echo round($doc['tamaño'] / 1024, 1); ?> KB
                        </span>
                    </div>
                    <div class="documento-info">
                        <div><strong>Archivo:</strong> <?php echo htmlspecialchars($doc['nombre_original']); ?></div>
                        <?php if ($doc['descripcion']): ?>
                            <div><strong>Descripción:</strong> <?php echo htmlspecialchars($doc['descripcion']); ?></div>
                        <?php endif; ?>
                        <div><strong>Subido:</strong> <?php echo date('d/m/Y H:i', strtotime($doc['fecha_subida'])); ?></div>
                        <div><strong>Por:</strong> <?php echo htmlspecialchars($doc['subido_por']); ?></div>
                    </div>
                    <div class="documento-acciones">
                        <a href="descargar_documento.php?id=<?php echo $doc['id']; ?>" class="accion-btn btn-descargar">
                            <i class="fas fa-download"></i> Descargar
                        </a>
                        <button type="button" class="accion-btn btn-eliminar" 
                                onclick="confirmarEliminacion(<?php echo $doc['id']; ?>, '<?php echo htmlspecialchars(addslashes($doc['nombre_original'])); ?>')">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
                <?php endforeach; endif; endforeach; ?>
                
                <?php if (!$tiene_documentos): ?>
                <div class="sin-documentos">
                    <i class="fas fa-file-alt" style="font-size: 48px; margin-bottom: 15px; color: #ddd;"></i>
                    <p>No hay documentos en esta categoría</p>
                    <a href="nuevo_expediente.php?id=<?php echo $empleado['id']; ?>&tipo=<?php echo $categoria['tipos'][0] ?? 'otros'; ?>" 
                       class="btn" style="margin-top: 15px; padding: 8px 16px; font-size: 14px;">
                        <i class="fas fa-plus"></i> Agregar documento
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Modal de confirmación para eliminar -->
    <div id="modalEliminar" class="modal">
        <div class="modal-content">
            <h3 style="margin-bottom: 15px; color: var(--text-color);">
                <i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i>
                Confirmar eliminación
            </h3>
            <p id="textoEliminar" style="color: var(--light-text); margin-bottom: 20px;">
                ¿Está seguro que desea eliminar este documento?
            </p>
            <form id="formEliminar" method="POST">
                <input type="hidden" name="eliminar_documento" value="1">
                <input type="hidden" name="documento_id" id="documentoId">
                <div class="modal-acciones">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModal()">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function confirmarEliminacion(documentoId, nombreArchivo) {
            document.getElementById('documentoId').value = documentoId;
            document.getElementById('textoEliminar').innerHTML = 
                `¿Está seguro que desea eliminar el documento <strong>"${nombreArchivo}"</strong>?<br>
                 Esta acción no se puede deshacer.`;
            document.getElementById('modalEliminar').style.display = 'flex';
        }
        
        function cerrarModal() {
            document.getElementById('modalEliminar').style.display = 'none';
        }
        
        // Cerrar modal al hacer clic fuera
        document.getElementById('modalEliminar').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModal();
            }
        });
        
        // Cerrar con Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarModal();
            }
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>