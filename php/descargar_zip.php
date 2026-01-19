<?php
// usuario/trabajadores/descargar_zip.php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: ../admin/login.php");
    exit();
}

require_once '../includes/database.php';

if (!isset($_GET['id'])) {
    header("Location: expedientes.php");
    exit();
}

$empleado_id = intval($_GET['id']);

// Obtener información del empleado
$stmt = $conn->prepare("SELECT CONCAT(primer_nombre, ' ', primer_apellido) as nombre_completo, ci, cargo, sede FROM empleados WHERE id = ?");
$stmt->bind_param("i", $empleado_id);
$stmt->execute();
$empleado = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$empleado) {
    header("Location: expedientes.php");
    exit();
}

// Verificar si ZipArchive está disponible
if (!class_exists('ZipArchive')) {
    header("Location: ver_expediente.php?id=$empleado_id&error=La extensión Zip no está habilitada en el servidor");
    exit();
}

// Obtener todos los documentos activos del empleado
$stmt = $conn->prepare("SELECT id, nombre_original, contenido, tipo_documento, descripcion, fecha_subida FROM expedientes WHERE empleado_id = ? AND estado = 'activo' ORDER BY tipo_documento, fecha_subida");
$stmt->bind_param("i", $empleado_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ver_expediente.php?id=$empleado_id&error=No hay documentos para exportar");
    exit();
}

// Crear nombre de carpeta principal
$nombre_carpeta = preg_replace('/[^a-z0-9\s]/i', '', $empleado['nombre_completo']);
$nombre_carpeta = str_replace(' ', '_', trim($nombre_carpeta));
$nombre_carpeta = $nombre_carpeta . '_' . $empleado['ci'];

// Nombre del archivo ZIP (sin extensión en el nombre de la carpeta interna)
$nombre_archivo = $nombre_carpeta . '_' . date('Y-m-d') . '.zip';

// Crear archivo ZIP temporal
$zip = new ZipArchive();
$temp_file = tempnam(sys_get_temp_dir(), 'zip');

if ($zip->open($temp_file, ZipArchive::CREATE) !== TRUE) {
    header("Location: ver_expediente.php?id=$empleado_id&error=Error al crear archivo ZIP");
    exit();
}

// Nombres amigables para tipos de documentos
$nombres_tipos = [
    'cedula_frontal' => 'Cedula_Frente',
    'cedula_reverso' => 'Cedula_Reverso',
    'curriculum' => 'Curriculum',
    'titulos' => 'Titulos_Academicos',
    'certificaciones' => 'Certificaciones',
    'contrato' => 'Contrato_Trabajo',
    'evaluaciones' => 'Evaluaciones',
    'foto_carnet' => 'Foto_Carnet',
    'formacion_academica' => 'Formacion_Academica',
    'experiencia_laboral' => 'Experiencia_Laboral',
    'carnet_salud' => 'Carnet_Salud',
    'otros' => 'Otros_Documentos'
];

// Crear archivo README.txt con información
$readme_content = "EXPEDIENTE DIGITAL - " . strtoupper($empleado['nombre_completo']) . "\n";
$readme_content .= "==================================================\n\n";
$readme_content .= "INFORMACIÓN DEL EMPLEADO:\n";
$readme_content .= "Nombre: " . $empleado['nombre_completo'] . "\n";
$readme_content .= "Cédula: " . $empleado['ci'] . "\n";
$readme_content .= "Cargo: " . $empleado['cargo'] . "\n";
$readme_content .= "Sede: " . $empleado['sede'] . "\n";
$readme_content .= "Fecha de exportación: " . date('d/m/Y H:i:s') . "\n";
$readme_content .= "Total de documentos: " . $result->num_rows . "\n\n";
$readme_content .= "DOCUMENTOS INCLUIDOS:\n";
$readme_content .= "=====================\n\n";

$documentos_agregados = 0;
$result->data_seek(0); // Reiniciar puntero del resultado

while ($doc = $result->fetch_assoc()) {
    // Obtener nombre amigable del tipo
    $tipo_nombre = isset($nombres_tipos[$doc['tipo_documento']]) ? 
                   $nombres_tipos[$doc['tipo_documento']] : 'Otros';
    
    // Limpiar nombre de archivo
    $extension = pathinfo($doc['nombre_original'], PATHINFO_EXTENSION);
    $nombre_base = pathinfo($doc['nombre_original'], PATHINFO_FILENAME);
    $nombre_limpio = preg_replace('/[^\w\s.-]/', '_', $nombre_base);
    
    // Si el nombre quedó vacío, usar un nombre basado en el tipo
    if (empty($nombre_limpio)) {
        $nombre_limpio = $tipo_nombre . '_' . $doc['id'];
    }
    
    // Agregar extensión si existe
    if (!empty($extension)) {
        $nombre_limpio .= '.' . $extension;
    } else {
        // Si no hay extensión, determinar según tipo
        $mime_map = [
            'cedula_frontal' => 'jpg',
            'cedula_reverso' => 'jpg', 
            'foto_carnet' => 'jpg',
            'curriculum' => 'pdf',
            'contrato' => 'pdf'
        ];
        $nombre_limpio .= '.' . ($mime_map[$doc['tipo_documento']] ?? 'pdf');
    }
    
    // Ruta completa en el ZIP - directamente en la carpeta principal
    $ruta_zip = $nombre_carpeta . '/' . $nombre_limpio;
    
    // Evitar nombres duplicados agregando número si es necesario
    $contador = 1;
    $nombre_original = $nombre_limpio;
    while ($zip->locateName($ruta_zip) !== false) {
        $nombre_sin_ext = pathinfo($nombre_original, PATHINFO_FILENAME);
        $ext = pathinfo($nombre_original, PATHINFO_EXTENSION);
        $nombre_limpio = $nombre_sin_ext . '_' . $contador;
        if (!empty($ext)) {
            $nombre_limpio .= '.' . $ext;
        }
        $ruta_zip = $nombre_carpeta . '/' . $nombre_limpio;
        $contador++;
    }
    
    // Agregar al ZIP
    if ($zip->addFromString($ruta_zip, $doc['contenido'])) {
        $documentos_agregados++;
        
        // Agregar información al README
        $readme_content .= ($documentos_agregados) . ". " . $nombre_limpio . "\n";
        $readme_content .= "   Tipo: " . $tipo_nombre . "\n";
        if (!empty($doc['descripcion'])) {
            $readme_content .= "   Descripción: " . $doc['descripcion'] . "\n";
        }
        $readme_content .= "   Fecha subida: " . date('d/m/Y H:i', strtotime($doc['fecha_subida'])) . "\n";
        $readme_content .= "   Tamaño: " . round(strlen($doc['contenido']) / 1024, 1) . " KB\n\n";
    }
}

// Agregar archivo README.txt
$readme_content .= "\n==================================================\n";
$readme_content .= "Sistema de Administración Integral de Nómina y Asistencia (SAINA)\n";
$readme_content .= "Exportado el: " . date('d/m/Y H:i:s') . "\n";
$zip->addFromString($nombre_carpeta . '/00_README.txt', $readme_content);

// También crear un archivo de índice HTML
$html_content = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente - ' . htmlspecialchars($empleado['nombre_completo']) . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .documento { border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; border-radius: 5px; }
        .numero { background: #007bff; color: white; padding: 2px 8px; border-radius: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>📁 Expediente Digital</h1>
    
    <div class="info">
        <h2>' . htmlspecialchars($empleado['nombre_completo']) . '</h2>
        <p><strong>Cédula:</strong> ' . $empleado['ci'] . '</p>
        <p><strong>Cargo:</strong> ' . htmlspecialchars($empleado['cargo']) . '</p>
        <p><strong>Sede:</strong> ' . htmlspecialchars($empleado['sede']) . '</p>
        <p><strong>Fecha exportación:</strong> ' . date('d/m/Y H:i:s') . '</p>
        <p><strong>Total documentos:</strong> ' . $documentos_agregados . '</p>
    </div>
    
    <h2>📋 Lista de Documentos</h2>';

$result->data_seek(0); // Reiniciar puntero nuevamente
$contador_html = 1;
while ($doc = $result->fetch_assoc()) {
    $tipo_nombre = isset($nombres_tipos[$doc['tipo_documento']]) ? 
                   $nombres_tipos[$doc['tipo_documento']] : 'Otros';
    $extension = pathinfo($doc['nombre_original'], PATHINFO_EXTENSION);
    $nombre_base = pathinfo($doc['nombre_original'], PATHINFO_FILENAME);
    $nombre_limpio = preg_replace('/[^\w\s.-]/', '_', $nombre_base);
    
    if (empty($nombre_limpio)) {
        $nombre_limpio = $tipo_nombre . '_' . $doc['id'];
    }
    
    if (!empty($extension)) {
        $nombre_limpio .= '.' . $extension;
    }
    
    $html_content .= '
    <div class="documento">
        <span class="numero">' . $contador_html . '</span>
        <strong>' . htmlspecialchars($nombre_limpio) . '</strong><br>
        <small>Tipo: ' . $tipo_nombre . '</small><br>';
    
    if (!empty($doc['descripcion'])) {
        $html_content .= '<small>Descripción: ' . htmlspecialchars($doc['descripcion']) . '</small><br>';
    }
    
    $html_content .= '
        <small>Subido: ' . date('d/m/Y H:i', strtotime($doc['fecha_subida'])) . '</small>
    </div>';
    $contador_html++;
}

$html_content .= '
    <hr>
    <footer>
        <p>Sistema SAINA - Exportado el ' . date('d/m/Y H:i:s') . '</p>
    </footer>
</body>
</html>';

$zip->addFromString($nombre_carpeta . '/index.html', $html_content);

$zip->close();

if ($documentos_agregados === 0) {
    unlink($temp_file);
    header("Location: ver_expediente.php?id=$empleado_id&error=No se pudieron agregar documentos al ZIP");
    exit();
}

// Enviar el archivo ZIP
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
header('Content-Length: ' . filesize($temp_file));
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Enviar contenido
readfile($temp_file);

// Eliminar archivo temporal
unlink($temp_file);
exit();
?>