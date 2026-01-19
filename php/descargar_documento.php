<?php
// usuario/trabajadores/descargar_documento.php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: ../admin/login.php");
    exit();
}

require_once '../includes/database.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT nombre_original, contenido, mime_type FROM expedientes WHERE id = ? AND estado = 'activo'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($nombre, $contenido, $mime_type);
    
    if ($stmt->fetch()) {
        // Configurar headers para descarga
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: attachment; filename="' . $nombre . '"');
        header('Content-Length: ' . strlen($contenido));
        
        // Liberar resultado
        $stmt->free_result();
        $stmt->close();
        
        // Enviar contenido
        echo $contenido;
        exit();
    }
    $stmt->close();
}

// Si falla, redirigir
header("Location: expedientes.php");
exit();
?>