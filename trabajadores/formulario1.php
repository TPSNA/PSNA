<?php
// usuario/trabajadores/formulario1.php

// 1. Iniciar sesión
session_start();

// 2. Verificar autenticación
if (!isset($_SESSION['username'])) {
    // Redirigir al login
    header("Location: ../../admin/login.php");
    exit();
}

// Verificar si hay un éxito reciente
$success_message = '';
$empleado_registrado = false;
$ci_trabajador = '';
$nombre_trabajador = '';
$fecha_registro = '';
$num_familiares = 0;

if (isset($_SESSION['form_success']) && $_SESSION['form_success'] === true) {
    $success_message = $_SESSION['message'] ?? 'Trabajador registrado exitosamente';
    $empleado_registrado = true;
    $ci_trabajador = $_SESSION['ci_trabajador'] ?? '';
    
    // Obtener datos del último registro si existen
    if (isset($_SESSION['last_registration'])) {
        $ci_trabajador = $_SESSION['last_registration']['ci'] ?? $ci_trabajador;
        $nombre_trabajador = $_SESSION['last_registration']['nombre'] ?? '';
        $fecha_registro = $_SESSION['last_registration']['fecha'] ?? date('d/m/Y H:i');
        $num_familiares = $_SESSION['last_registration']['familiares'] ?? 0;
    }
    
    // Limpiar la bandera de éxito
    unset($_SESSION['form_success']);
    unset($_SESSION['message']);
    unset($_SESSION['empleado_id']);
    unset($_SESSION['ci_trabajador']);
    unset($_SESSION['last_registration']);
}

// Verificar si hay errores
$error_message = $_SESSION['error'] ?? '';
if (isset($_SESSION['error'])) {
    unset($_SESSION['error']);
}

// Cargar datos del formulario anterior si existen (para repoblar en caso de error)
$form_data = $_SESSION['form_data'] ?? [];
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}

// 3. Conectar a la base de datos (ruta relativa corregida)
$database_file = __DIR__ . '/../../admin/includes/database.php';

if (!file_exists($database_file)) {
    die("Error crítico: No se puede conectar a la base de datos. Contacte al administrador.");
}

require_once $database_file;

// 4. Verificar que la conexión se estableció
if (!isset($conn) || !$conn) {
    die("Error de conexión a la base de datos.");
}

// Función para repoblar campos del formulario si hay error
function repoblarCampo($campo, $default = '') {
    global $form_data;
    return isset($form_data[$campo]) ? htmlspecialchars($form_data[$campo]) : $default;
}

// Función para marcar como seleccionado en selects
function seleccionarOpcion($campo, $valor) {
    global $form_data;
    if (isset($form_data[$campo]) && $form_data[$campo] == $valor) {
        return 'selected';
    }
    return '';
}

// Función para marcar como checked en radios/checkboxes
function marcarCheckbox($campo, $valor) {
    global $form_data;
    if (isset($form_data[$campo]) && $form_data[$campo] == $valor) {
        return 'checked';
    }
    return '';
}

$error = '';
$success = '';

// Procesar formulario (mantener tu lógica de procesamiento aquí)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Trabajador - SAINA</title>
    <!-- Favicon agregado  -->
    <link rel="icon" type="image/png" sizes="32x32" href="../../imagenes/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="../../imagenes/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="../../imagenes/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ===== VARIABLES GLOBALES ===== */
        :root {
            --purple-start: #a8a0f9;
            --blue-end: #6162f4;
            --primary-color: #6a67f0;
            --danger-color: #ff6b6b;
            --success-color: #43e97b;
            --warning-color: #ffd166;
            --text-color: #333;
            --light-text: #777;
            --light-bg: rgba(255, 255, 255, 0.95);
            --shadow-light: rgba(0, 0, 0, 0.1);
            --shadow-medium: rgba(0, 0, 0, 0.15);
            --border-radius: 15px;
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            min-height: 100vh;
            padding-top: 80px; /* Para el header fijo */
        }
        
        /* ===== HEADER UNIFICADO ===== */
        .main-header {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(106, 103, 240, 0.1);
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 80px;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-container img {
            height: 100px;
            transition: var(--transition);
        }
        
        .logo-container img:hover {
            transform: scale(1.05);
        }
        
        .system-name {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 20px;
            letter-spacing: -0.5px;
        }
        
        .nav-menu {
            display: flex;
            align-items: center;
            gap: 30px;
        }
        
        .nav-link {
            color: var(--text-color);
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            padding: 8px 16px;
            border-radius: 10px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .nav-link:hover {
            background: rgba(106, 103, 240, 0.1);
            color: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .nav-link.active {
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            color: white;
            box-shadow: 0 5px 15px rgba(106, 103, 240, 0.3);
        }
        
        .user-menu {
            position: relative;
        }
        
        .user-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            border: none;
            color: white;
            cursor: pointer;
            font-size: 18px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .user-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(106, 103, 240, 0.4);
        }
        
        .user-dropdown {
            position: absolute;
            top: 55px;
            right: 0;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            min-width: 200px;
            padding: 10px 0;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: var(--transition);
        }
        
        .user-menu:hover .user-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .user-dropdown a {
            display: block;
            padding: 12px 20px;
            color: var(--text-color);
            text-decoration: none;
            transition: var(--transition);
            font-size: 14px;
            font-weight: 500;
        }
        
        .user-dropdown a:hover {
            background: rgba(106, 103, 240, 0.1);
            color: var(--primary-color);
            padding-left: 25px;
        }
        
        .user-dropdown a i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
        
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--primary-color);
            cursor: pointer;
        }
        
        /* ===== CONTENEDOR PRINCIPAL ===== */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        /* ===== TARJETA DEL FORMULARIO ===== */
        .form-card {
            background: white;
            border-radius: 25px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        
        .form-header {
            background: linear-gradient(90deg, var(--purple-start), var(--blue-end));
            padding: 40px;
            text-align: center;
            color: white;
        }
        
        .form-icon {
            font-size: 60px;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .form-header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .form-header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        /* ===== INDICADOR DE PASOS ===== */
        .steps-container {
            padding: 30px 40px 0;
        }
        
        .steps-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }
        
        .steps-indicator::before {
            content: '';
            position: absolute;
            top: 25px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e6e6e6;
            z-index: 1;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
            cursor: pointer;
            transition: var(--transition);
            flex: 1;
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: white;
            border: 3px solid #e6e6e6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            color: #777;
            margin-bottom: 10px;
            transition: var(--transition);
        }
        
        .step.active .step-number {
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            border-color: var(--primary-color);
            color: white;
            box-shadow: 0 5px 15px rgba(106, 103, 240, 0.3);
            transform: scale(1.1);
        }
        
        .step.completed .step-number {
            background: linear-gradient(135deg, var(--success-color), #38f9d7);
            border-color: var(--success-color);
            color: white;
        }
        
        .step-text {
            font-size: 13px;
            font-weight: 600;
            color: #777;
            text-align: center;
            max-width: 100px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .step.active .step-text {
            color: var(--primary-color);
        }
        
        .step.completed .step-text {
            color: var(--success-color);
        }
        
        /* ===== CONTENIDO DE LOS PASOS ===== */
        .form-body {
            padding: 0 40px 40px;
        }
        
        .step-content {
            display: none;
            animation: fadeIn 0.5s ease-in;
        }
        
        .step-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .step-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
            color: var(--primary-color);
        }
        
        .step-title h2 {
            font-size: 24px;
            font-weight: 600;
        }
        
        .step-description {
            color: var(--light-text);
            margin-bottom: 30px;
            font-size: 15px;
            line-height: 1.6;
            padding-left: 35px;
        }
        
        /* ===== FORMULARIO ===== */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .input-group {
            display: flex;
            flex-direction: column;
        }
        
        .input-group label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .input-group label i {
            color: var(--primary-color);
            width: 20px;
        }
        
        .input-group input,
        .input-group select,
        .input-group textarea {
            padding: 14px 16px;
            border: 2px solid #e6e6e6;
            border-radius: 10px;
            font-size: 15px;
            transition: var(--transition);
            background: white;
        }
        
        .input-group input:focus,
        .input-group select:focus,
        .input-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(106, 103, 240, 0.2);
        }
        
        .input-group input.error,
        .input-group select.error {
            border-color: var(--danger-color);
            box-shadow: 0 0 0 4px rgba(255, 107, 107, 0.2);
        }
        
        /* ===== BOTONES ===== */
        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid rgba(0, 0, 0, 0.05);
        }
        
        .btn {
            padding: 14px 32px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        
        .btn.primary {
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            color: white;
            box-shadow: 0 5px 15px rgba(106, 103, 240, 0.3);
        }
        
        .btn.primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(106, 103, 240, 0.4);
        }
        
        .btn.secondary {
            background: rgba(149, 165, 166, 0.1);
            color: #95a5a6;
            border: 2px solid #e6e6e6;
        }
        
        .btn.secondary:hover {
            background: rgba(149, 165, 166, 0.2);
            border-color: #95a5a6;
        }
        
        .btn.danger {
            background: linear-gradient(135deg, var(--danger-color), #ee5a52);
            color: white;
        }
        
        .btn.danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
        }
        
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }
        
        /* ===== FAMILIARES ===== */
        .familiar-item {
            background: rgba(106, 103, 240, 0.05);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary-color);
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .familiar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(106, 103, 240, 0.1);
        }
        
        .familiar-header h4 {
            color: var(--primary-color);
            font-size: 16px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        /* ===== FOTO ===== */
        .photo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }
        
        .photo-preview {
            width: 200px;
            height: 200px;
            border: 3px dashed var(--primary-color);
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            overflow: hidden;
            transition: var(--transition);
            background: rgba(106, 103, 240, 0.05);
        }
        
        .photo-preview:hover {
            border-color: var(--blue-end);
            background: rgba(106, 103, 240, 0.1);
            transform: translateY(-5px);
        }
        
        .photo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .photo-preview i {
            font-size: 48px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .photo-preview p {
            color: var(--light-text);
            text-align: center;
            font-size: 14px;
        }
        
        /* ===== MENSAJES ===== */
        .alert {
            padding: 20px 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 500;
            animation: fadeIn 0.3s ease;
            border-left: 4px solid;
        }
        
        .alert i {
            font-size: 22px;
        }
        
        .alert-success {
            background: linear-gradient(135deg, rgba(67, 233, 123, 0.1), rgba(56, 249, 215, 0.1));
            color: #2ecc71;
            border-left-color: #2ecc71;
        }
        
        .alert-error {
            background: linear-gradient(135deg, rgba(255, 107, 107, 0.1), rgba(238, 90, 82, 0.1));
            color: var(--danger-color);
            border-left-color: var(--danger-color);
        }
        
        /* ===== FORMULARIO EXTRA PARA INACTIVOS ===== */
        #formularioExtra {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background: rgba(255, 107, 107, 0.05);
            border-radius: 10px;
            border-left: 4px solid var(--danger-color);
        }
        
        #formularioExtra h3 {
            color: var(--danger-color);
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        #formularioExtra.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        /* ===== MODAL DE ÉXITO - ESTILOS MODIFICADOS ===== */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(5px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: fadeIn 0.3s ease;
            padding: 20px;
        }
        
        .success-modal {
            background: white;
            border-radius: 20px;
            width: 100%;
            max-width: 420px; /* Reducido un poco más */
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(106, 103, 240, 0.1);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #6a67f0, #6162f4);
            padding: 25px; /* Reducido */
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .modal-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1%, transparent 1%);
            background-size: 20px 20px;
            animation: float 20s linear infinite;
        }
        
        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-10px, -10px) rotate(360deg); }
        }
        
        .success-icon {
            font-size: 50px; /* Reducido */
            margin-bottom: 10px;
            animation: bounce 1s ease;
        }
        
        .modal-header h2 {
            margin: 0 0 5px 0;
            font-size: 24px; /* Reducido */
            font-weight: 700;
        }
        
        .modal-subtitle {
            margin: 0;
            opacity: 0.9;
            font-size: 14px; /* Reducido */
        }
        
        .modal-body {
            padding: 20px; /* Reducido */
        }
        
        .success-summary {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px; /* Reducido */
            margin-bottom: 20px; /* Reducido */
        }
        
        .summary-item {
            display: flex;
            align-items: center;
            gap: 10px; /* Reducido */
            padding: 10px; /* Reducido */
            background: rgba(106, 103, 240, 0.05);
            border-radius: 10px;
            border-left: 4px solid var(--primary-color);
            transition: all 0.3s ease;
        }
        
        .summary-item:hover {
            background: rgba(106, 103, 240, 0.1);
            transform: translateX(5px);
        }
        
        .summary-item i {
            font-size: 18px; /* Reducido */
            width: 30px; /* Reducido */
            text-align: center;
        }
        
        .summary-content {
            flex: 1;
        }
        
        .summary-label {
            display: block;
            font-size: 10px; /* Reducido */
            color: #777;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        
        .summary-value {
            display: block;
            font-size: 13px; /* Reducido */
            font-weight: 600;
            color: #333;
        }
        
        .success-message {
            display: flex;
            align-items: flex-start;
            gap: 8px; /* Reducido */
            padding: 12px; /* Reducido */
            background: rgba(67, 233, 123, 0.1);
            border-radius: 10px;
            border: 1px solid rgba(67, 233, 123, 0.2);
        }
        
        .success-message i {
            color: #43e97b;
            font-size: 18px; /* Reducido */
            margin-top: 2px;
        }
        
        .success-message p {
            margin: 0;
            color: #2e7d32;
            font-size: 13px; /* Reducido */
            line-height: 1.5;
        }
        
        .modal-footer {
            padding: 15px 20px; /* Reducido */
            background: #f8f9fa;
            border-top: 1px solid #e6e6e6;
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px; /* Reducido */
            margin-bottom: 12px; /* Reducido */
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            color: white;
            border: none;
            padding: 12px; /* Reducido */
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(106, 103, 240, 0.3);
        }
        
        .btn-secondary {
            background: rgba(106, 103, 240, 0.1);
            color: var(--primary-color);
            border: 2px solid rgba(106, 103, 240, 0.2);
            padding: 12px; /* Reducido */
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: rgba(106, 103, 240, 0.2);
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background: transparent;
            color: #666;
            border: 2px solid #ddd;
            padding: 12px; /* Reducido */
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-outline:hover {
            border-color: #999;
            color: #333;
            transform: translateY(-2px);
        }
        
        .auto-close-notice {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #777;
            font-size: 12px; /* Reducido */
            padding: 8px; /* Reducido */
        }
        
        .auto-close-notice i {
            color: var(--primary-color);
        }
        
        #countdown {
            font-weight: 700;
            color: var(--primary-color);
            min-width: 18px;
            text-align: center;
        }
        
        /* Animaciones del modal */
        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        
        /* Confetti animation */
        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            opacity: 0.7;
            animation: confettiFall linear forwards;
        }
        
        @keyframes confettiFall {
            0% { 
                transform: translateY(-100px) rotate(0deg) scale(1);
                opacity: 1;
            }
            100% { 
                transform: translateY(100vh) rotate(720deg) scale(0.5);
                opacity: 0;
            }
        }
        
        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .nav-menu {
                gap: 15px;
            }
            
            .nav-link {
                padding: 6px 12px;
                font-size: 14px;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }
            
            .header-container {
                height: 70px;
                padding: 0 15px;
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .nav-menu {
                position: fixed;
                top: 70px;
                left: 0;
                right: 0;
                background: white;
                flex-direction: column;
                padding: 20px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                border-top: 1px solid rgba(0, 0, 0, 0.1);
                transform: translateY(-100%);
                opacity: 0;
                transition: var(--transition);
                z-index: 999;
            }
            
            .nav-menu.active {
                transform: translateY(0);
                opacity: 1;
            }
            
            .nav-link {
                width: 100%;
                justify-content: center;
            }
            
            .form-header {
                padding: 30px 20px;
            }
            
            .form-header h1 {
                font-size: 24px;
            }
            
            .steps-container,
            .form-body {
                padding: 20px;
            }
            
            .steps-indicator {
                gap: 10px;
            }
            
            .step-number {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
            
            .step-text {
                font-size: 11px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-header {
                padding: 20px;
            }
            
            .modal-header h2 {
                font-size: 22px;
            }
            
            .action-buttons {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 480px) {
            .form-actions {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .familiar-item {
                padding: 15px;
            }
            
            .summary-item {
                padding: 10px;
            }
            
            .summary-item i {
                font-size: 16px;
                width: 25px;
            }
            
            .success-modal {
                max-width: 95%;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER UNIFICADO -->
    <header class="main-header">
        <div class="header-container">
            <div class="logo-container">
                <img src="../../imagenes/Logo SAINA Horizontal.png" alt="SAINA Logo">
                <span class="system-name"></span>
            </div>
            
            <button class="mobile-menu-btn" id="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- En la sección del nav-menu (línea ~215) -->
                <nav class="nav-menu" id="nav-menu">
                    <a href="../index.php" class="nav-link">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                    <a href="index.php" class="nav-link">
                        <i class="fas fa-history"></i> Gestionar Trabajadores
                    </a>
                    <a href="buscar.php" class="nav-link">
                        <i class="fas fa-users"></i> Buscar Trabajadores
                    </a>
                    <a href="formulario1.php" class="nav-link active">  <!-- active en Nuevo -->
                        <i class="fas fa-user-plus"></i> Nuevo Trabajador
                    </a>
                    <a href="expedientes.php" class="nav-link">  <!-- Expedientes agregado -->
                        <i class="fas fa-folder-open"></i> Expedientes
                    </a>
                    <a href="reportes.php" class="nav-link">
                        <i class="fas fa-download"></i> Reportes
                    </a>
                </nav>
            
            <div class="user-menu">
                <button class="user-btn" id="user-btn">
                    <i class="fas fa-user"></i>
                </button>
                <div class="user-dropdown">
                    
                    <a href="../../admin/logout.php">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </header>
    
    <!-- CONTENIDO PRINCIPAL -->
    <div class="main-container">
        <!-- MENSAJES DE ERROR -->
        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i> 
                <div>
                    <strong>¡Error!</strong><br>
                    <?php echo $error_message; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- TARJETA DEL FORMULARIO -->
        <div class="form-card">
            <!-- CABECERA -->
            <div class="form-header">
                <div class="form-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1>Registro de Trabajador</h1>
                <p>Sistema de Administración Integral de Nómina y Archivo</p>
            </div>
            
            <!-- INDICADOR DE PASOS -->
            <div class="steps-container">
                <nav class="steps-indicator">
                    <div class="step active" data-step="1">
                        <span class="step-number">1</span>
                        <span class="step-text">Datos Personales</span>
                    </div>
                    <div class="step" data-step="2">
                        <span class="step-number">2</span>
                        <span class="step-text">Información Laboral</span>
                    </div>
                    <div class="step" data-step="3">
                        <span class="step-number">3</span>
                        <span class="step-text">Información General</span>
                    </div>
                    <div class="step" data-step="4">
                        <span class="step-number">4</span>
                        <span class="step-text">Familia</span>
                    </div>
                    <div class="step" data-step="5">
                        <span class="step-number">5</span>
                        <span class="step-text">Foto</span>
                    </div>
                </nav>
            </div>
            
            <!-- FORMULARIO -->
            <div class="form-body">
                <form action="../php/procesar_trabajador.php" method="POST" enctype="multipart/form-data" id="registration-form">
                    <!-- PASO 1: DATOS PERSONALES -->
                    <div class="step-content active" data-step="1">
                        <p class="step-label">Paso 1</p>
                        <h2>Datos Personales</h2>
                        <p class="step-description">A continuación, complete la información personal del empleado. <strong>Nota:</strong> Todos los campos son opcionales.</p>
                        <div class="form-grid">
                            <div class="input-group">
                                <label for="nacionalidad">Nacionalidad</label>
                                <select id="nacionalidad" name="nacionalidad">
                                    <option value="">Selecciona una opción</option>
                                    <option value="EXTRANJERO(A)" <?php echo seleccionarOpcion('nacionalidad', 'EXTRANJERO(A)'); ?>>EXTRANJERO(A)</option>
                                    <option value="VENEZOLANO(A)" <?php echo seleccionarOpcion('nacionalidad', 'VENEZOLANO(A)'); ?>>VENEZOLANO(A)</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="cedula">Cedula de Identidad</label>
                                <input type="text" id="ci" name="ci" placeholder="Ingresa CI " value="<?php echo repoblarCampo('ci'); ?>">
                            </div>
                            <div class="input-group">
                                <label for="primer_nombre">Primer Nombre</label>
                                <input type="text" id="primer_nombre" name="primer_nombre" placeholder="Ingresa Primer Nombre " value="<?php echo repoblarCampo('primer_nombre'); ?>">
                            </div>
                            <div class="input-group">
                                <label for="segundo_nombre">Segundo Nombre</label>
                                <input type="text" id="segundo_nombre" name="segundo_nombre" placeholder="Ingresa Segundo Nombre " value="<?php echo repoblarCampo('segundo_nombre'); ?>">
                            </div>
                            <div class="input-group">
                                <label for="primer_apellido">Primer Apellido</label>
                                <input type="text" id="primer_apellido" name="primer_apellido" placeholder="Ingresa Primer Apellido " value="<?php echo repoblarCampo('primer_apellido'); ?>">
                            </div>
                            <div class="input-group">
                                <label for="segundo_apellido">Segundo Apellido</label>
                                <input type="text" id="segundo_apellido" name="segundo_apellido" placeholder="Ingresa segundo Apellido " value="<?php echo repoblarCampo('segundo_apellido'); ?>">
                            </div>
                            <div class="input-group">
                                <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo repoblarCampo('fecha_nacimiento'); ?>">
                            </div>
                            <div class="input-group">
                                <label for="sexo">Sexo</label>
                                <select id="sexo" name="sexo">
                                    <option value="">Selecciona una opción</option>
                                    <option value="MASCULINO" <?php echo seleccionarOpcion('sexo', 'MASCULINO'); ?>>MASCULINO</option>
                                    <option value="FEMENINO" <?php echo seleccionarOpcion('sexo', 'FEMENINO'); ?>>FEMENINO</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="estado_civil">Estado Civil</label>
                                <select id="estado_civil" name="estado_civil">
                                    <option value="">Selecciona una opción</option>
                                    <option value="SOLTERO(A)" <?php echo seleccionarOpcion('estado_civil', 'SOLTERO(A)'); ?>>SOLTERO(A)</option>
                                    <option value="CASADO(A)" <?php echo seleccionarOpcion('estado_civil', 'CASADO(A)'); ?>>CASADO(A)</option>
                                    <option value="DIVORCIADO(A)" <?php echo seleccionarOpcion('estado_civil', 'DIVORCIADO(A)'); ?>>DIVORCIADO(A)</option>
                                    <option value="VIUDO(A)" <?php echo seleccionarOpcion('estado_civil', 'VIUDO(A)'); ?>>VIUDO(A)</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="estado">Estado</label>
                                <select id="estado" name="estado_id" disabled>
                                    <option value="1" selected>Lara</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="municipio">Municipio</label>
                                <select id="municipio" name="municipio_id">
                                    <option value="">Selecciona un municipio</option>
                                    <!-- Opciones se cargan dinámicamente -->
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="parroquia">Parroquia</label>
                                <select id="parroquia" name="parroquia_id">
                                    <option value="">Selecciona una Parroquia </option>
                                    <!-- Opciones se cargan dinámicamente -->
                                </select>
                            </div>

                            <div class="input-group">
                                <label for="direccion_ubicacion">Direccion de Domicilio</label>
                                <input type="text" id="direccion_ubicacion" name="direccion_ubicacion" placeholder="Ingresa Direccion " value="<?php echo repoblarCampo('direccion_ubicacion'); ?>">
                            </div>
                            <div class="input-group">
                                <label for="telefono">Numero de Telefono</label>
                                <input type="tel" id="telefono" name="telefono" placeholder="Ingrese el numero telefonico " value="<?php echo repoblarCampo('telefono'); ?>">
                            </div>
                            <div class="input-group">
                                <label for="correo">Correo</label>
                                <input type="email" id="correo" name="correo" placeholder="Ingrese Correo Electronico " value="<?php echo repoblarCampo('correo'); ?>">
                            </div>
                            <div class="input-group">
                                <label for="cuenta_bancaria">Cuenta Bancaria</label>
                                <input type="text" id="cuenta_bancaria" name="cuenta_bancaria" placeholder="Ingresa cuenta bancaria " value="<?php echo repoblarCampo('cuenta_bancaria'); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- PASO 2: INFORMACIÓN LABORAL -->
                    <div class="step-content" data-step="2">
                        <div class="step-title">
                            <div class="step-number" style="width: 40px; height: 40px; font-size: 16px;">2</div>
                            <h2>Información Laboral</h2>
                        </div>
                        <p class="step-description">
                            Proporcione los detalles laborales y administrativos del trabajador. <strong>Todos los campos son opcionales.</strong>
                        </p>
                        
                        <div class="form-grid">
                            <!-- Campos del paso 2 -->
                            <div class="input-group">
                                <label for="tipo_trabajador">Tipo de Trabajador</label>
                                <select id="tipo_trabajador" name="tipo_trabajador">
                                    <option value="">Selecciona una opción </option>
                                    <option value="CTD" <?php echo seleccionarOpcion('tipo_trabajador', 'CTD'); ?>>CTD</option>
                                    <option value="CTI" <?php echo seleccionarOpcion('tipo_trabajador', 'CTI'); ?>>CTI</option>
                                    <option value="LNR" <?php echo seleccionarOpcion('tipo_trabajador', 'LNR'); ?>>LNR</option>
                                </select>
                            </div>
                            
                            <div class="input-group">
                                <label for="grado_instruccion">Grado de Instrucción</label>
                                <select id="grado_instruccion" name="grado_instruccion">
                                    <option value="">Selecciona una opción </option>
                                    <option value="PRIMARIA" <?php echo seleccionarOpcion('grado_instruccion', 'PRIMARIA'); ?>>PRIMARIA</option>
                                    <option value="BACHILLER" <?php echo seleccionarOpcion('grado_instruccion', 'BACHILLER'); ?>>BACHILLER</option>
                                    <option value="TSU" <?php echo seleccionarOpcion('grado_instruccion', 'TSU'); ?>>TSU</option>
                                    <option value="LICENCIADO" <?php echo seleccionarOpcion('grado_instruccion', 'LICENCIADO'); ?>>LICENCIADO</option>
                                    <option value="INGENIERO" <?php echo seleccionarOpcion('grado_instruccion', 'INGENIERO'); ?>>INGENIERO</option>
                                    <option value="ESPECIALISTA" <?php echo seleccionarOpcion('grado_instruccion', 'ESPECIALISTA'); ?>>ESPECIALISTA</option>
                                    <option value="MAESTRIA" <?php echo seleccionarOpcion('grado_instruccion', 'MAESTRIA'); ?>>MAESTRIA</option>
                                    <option value="DOCTORADO" <?php echo seleccionarOpcion('grado_instruccion', 'DOCTORADO'); ?>>DOCTORADO</option>
                                    <option value="NINGUNO" <?php echo seleccionarOpcion('grado_instruccion', 'NINGUNO'); ?>>NINGUNO</option>
                                </select>
                            </div>
                            
                            <div class="input-group">
                                <label for="cargo">Cargo</label>
                                <select id="cargo" name="cargo">
                                    <option value="">Selecciona una opción </option>
                                    <option value="DIRECTOR GENERAL" <?php echo seleccionarOpcion('cargo', 'DIRECTOR GENERAL'); ?>>DIRECTOR GENERAL</option>
                                    <option value="COORDINADOR DE PRESUPUESTO" <?php echo seleccionarOpcion('cargo', 'COORDINADOR DE PRESUPUESTO'); ?>>COORD. DE PRESUPUESTO</option>
                                    <option value="COORDINADOR DE DESARROLLO ORGANIZACIONAL" <?php echo seleccionarOpcion('cargo', 'COORDINADOR DE DESARROLLO ORGANIZACIONAL'); ?>>COORD. DE DESARROLLO ORG.</option>
                                    <option value="COORDINADOR DE ARCHIVOS" <?php echo seleccionarOpcion('cargo', 'COORDINADOR DE ARCHIVOS'); ?>>COORD. DE ARCHIVOS</option>
                                    <option value="COORDINADOR DE SEGURIDAD" <?php echo seleccionarOpcion('cargo', 'COORDINADOR DE SEGURIDAD'); ?>>COORDINADOR DE SEGURIDAD</option>
                                    <option value="DIRECTOR DE ASUNTOS JURIDICOS" <?php echo seleccionarOpcion('cargo', 'DIRECTOR DE ASUNTOS JURIDICOS'); ?>>DIR. DE ASUNTOS JURIDICOS</option>
                                    <option value="COORDINADOR DE CONTROL Y GESTION" <?php echo seleccionarOpcion('cargo', 'COORDINADOR DE CONTROL Y GESTION'); ?>>COORD. DE CONTROL Y GESTION</option>
                                    <option value="COORDINADOR DE TRANSPORTE" <?php echo seleccionarOpcion('cargo', 'COORDINADOR DE TRANSPORTE'); ?>>COORD. DE TRANSPORTE</option>
                                    <option value="DIRECTOR TALENTO HUMANO" <?php echo seleccionarOpcion('cargo', 'DIRECTOR TALENTO HUMANO'); ?>>DIR. TALENTO HUMANO</option>
                                    <option value="DIRECTOR DESARROLLO HUMANO INTEGRAL" <?php echo seleccionarOpcion('cargo', 'DIRECTOR DESARROLLO HUMANO INTEGRAL'); ?>>DIR. DESARROLLO HUMANO INTEGRAL</option>
                                    <option value="COORDINADOR DE BIENES" <?php echo seleccionarOpcion('cargo', 'COORDINADOR DE BIENES'); ?>>COORD. DE BIENES</option>
                                    <option value="DIRECTOR DE ADMINISTRACION Y FINANZAS" <?php echo seleccionarOpcion('cargo', 'DIRECTOR DE ADMINISTRACION Y FINANZAS'); ?>>DIR. DE ADMINISTRACION Y FINANZAS</option>
                                    <option value="DIRECTOR DE REINSERCION SOCIAL" <?php echo seleccionarOpcion('cargo', 'DIRECTOR DE REINSERCION SOCIAL'); ?>>DIR. DE REINSERCION SOCIAL</option>
                                    <option value="DIRECTOR DE PLANIFICACION PRESUPUESTO Y DESARROLLO ORGANIZACIONAL" <?php echo seleccionarOpcion('cargo', 'DIRECTOR DE PLANIFICACION PRESUPUESTO Y DESARROLLO ORGANIZACIONAL'); ?>>DIR. DE PLAN. PRESUP. Y DESARROLLO ORG.</option>
                                    <option value="DIRECTOR DE OPERACIONES" <?php echo seleccionarOpcion('cargo', 'DIRECTOR DE OPERACIONES'); ?>>DIR. DE OPERACIONES</option>
                                    <option value="DIRECTOR DE BIENESTAR SOCIAL" <?php echo seleccionarOpcion('cargo', 'DIRECTOR DE BIENESTAR SOCIAL'); ?>>DIR. DE BIENESTAR SOCIAL</option>
                                    <option value="COORDINADOR DE NUTRICION" <?php echo seleccionarOpcion('cargo', 'COORDINADOR DE NUTRICION'); ?>>COORD. DE NUTRICION</option>
                                    <option value="DIRECTOR COMUNICACIÓN Y REDES SOCIALES" <?php echo seleccionarOpcion('cargo', 'DIRECTOR COMUNICACIÓN Y REDES SOCIALES'); ?>>DIR. COMUNICACIÓN Y RRSS</option>
                                    <option value="COORDINADOR DE TALENTO HUMANO" <?php echo seleccionarOpcion('cargo', 'COORDINADOR DE TALENTO HUMANO'); ?>>COORD. DE TALENTO HUMANO</option>
                                    <option value="DIRECTOR DE TECNOLOGIA INFORMATICA Y TELECOMUNICACIONES" <?php echo seleccionarOpcion('cargo', 'DIRECTOR DE TECNOLOGIA INFORMATICA Y TELECOMUNICACIONES'); ?>>DIR. DE TIT</option>
                                    <option value="DIRECTOR DE ATENCION AL CIUDADANO" <?php echo seleccionarOpcion('cargo', 'DIRECTOR DE ATENCION AL CIUDADANO'); ?>>DIR. DE ATENCION AL CIUDADANO</option>
                                    <option value="COORDINADOR DE AREA" <?php echo seleccionarOpcion('cargo', 'COORDINADOR DE AREA'); ?>>COORD. DE AREA</option>
                                    <option value="COORDINADOR DE SERVICIOS GENERALES " <?php echo seleccionarOpcion('cargo', 'COORDINADOR DE SERVICIOS GENERALES '); ?>>COORD. DE SERVICIOS GENERALES</option>
                                    <option value="DIRECTOR DE CENTRO" <?php echo seleccionarOpcion('cargo', 'DIRECTOR DE CENTRO'); ?>>DIR. DE CENTRO</option>
                                    <option value="COORDINADOR DE CENTRO " <?php echo seleccionarOpcion('cargo', 'COORDINADOR DE CENTRO '); ?>>COORD. DE CENTRO</option>
                                    <option value="GUIA FACILITADOR" <?php echo seleccionarOpcion('cargo', 'GUIA FACILITADOR'); ?>>GUIA FACILITADOR</option>
                                    <option value="ASISTENTE DE OFICINA" <?php echo seleccionarOpcion('cargo', 'ASISTENTE DE OFICINA'); ?>>ASISTENTE DE OFICINA</option>
                                    <option value="CONTADOR" <?php echo seleccionarOpcion('cargo', 'CONTADOR'); ?>>CONTADOR</option>
                                    <option value="ABOGADO" <?php echo seleccionarOpcion('cargo', 'ABOGADO'); ?>>ABOGADO</option>
                                    <option value="INSTRUCTOR DE FORMACION PROFESIONAL" <?php echo seleccionarOpcion('cargo', 'INSTRUCTOR DE FORMACION PROFESIONAL'); ?>>INSTRUCTOR DE FORM. PROF.</option>
                                    <option value="CAPELLAN" <?php echo seleccionarOpcion('cargo', 'CAPELLAN'); ?>>CAPELLAN</option>
                                    <option value="MEDICO" <?php echo seleccionarOpcion('cargo', 'MEDICO'); ?>>MEDICO</option>
                                    <option value="ENFERMERA" <?php echo seleccionarOpcion('cargo', 'ENFERMERA'); ?>>ENFERMERA</option>
                                    <option value="PSICOLOGO" <?php echo seleccionarOpcion('cargo', 'PSICOLOGO'); ?>>PSICOLOGO</option>
                                    <option value="TRABAJADOR SOCIAL" <?php echo seleccionarOpcion('cargo', 'TRABAJADOR SOCIAL'); ?>>TRABAJADOR SOCIAL</option>
                                    <option value="TERAPEUTA" <?php echo seleccionarOpcion('cargo', 'TERAPEUTA'); ?>>TERAPEUTA</option>
                                    <option value="MANTENIMIENTO" <?php echo seleccionarOpcion('cargo', 'MANTENIMIENTO'); ?>>MANTENIMIENTO</option>
                                    <option value="COCINERO" <?php echo seleccionarOpcion('cargo', 'COCINERO'); ?>>COCINERO</option>
                                    <option value="VIGILANTE" <?php echo seleccionarOpcion('cargo', 'VIGILANTE'); ?>>VIGILANTE</option>
                                </select>
                            </div>

                             <div class="input-group">
                                    <label for="dpt_desempeñar"> Departamento Asignado </label>
                                    <select id="dpt_desempeñar" name="dpt_desempeñar">
                                        <option value="">Selecciona una opción </option>
                                        <option value="DIR. GENERAL" <?php echo seleccionarOpcion('dpt_desempeñar', 'DIR. GENERAL'); ?>>DIRECCIÓN GENERAL</option>
                                        <option value="COM. CONTRAT." <?php echo seleccionarOpcion('dpt_desempeñar', 'COM. CONTRAT.'); ?>>COMISIÓN DE CONTRATACIONES</option>
                                        <option value="DIR. ADM. Y FIN." <?php echo seleccionarOpcion('dpt_desempeñar', 'DIR. ADM. Y FIN.'); ?>>DIRECCIÓN DE ADMINISTRACIÓN Y FINANZAS</option>
                                        <option value="DIR. COM. Y R.R.S.S." <?php echo seleccionarOpcion('dpt_desempeñar', 'DIR. COM. Y R.R.S.S.'); ?>>DIRECCIÓN DE COMUNICACIÓN Y REDES SOCIALES</option>
                                        <option value="UNID. CONS. JUR." <?php echo seleccionarOpcion('dpt_desempeñar', 'UNID. CONS. JUR.'); ?>>UNIDAD DE CONSULTORÍA JURÍDICA</option>
                                        <option value="DIR. ATEN. CIUD." <?php echo seleccionarOpcion('dpt_desempeñar', 'DIR. ATEN. CIUD.'); ?>>DIRECCIÓN DE ATENCIÓN AL CIUDADANO</option>
                                        <option value="DIR. PLAN., PRESUP. Y DES." <?php echo seleccionarOpcion('dpt_desempeñar', 'DIR. PLAN., PRESUP. Y DES.'); ?>>DIRECCIÓN DE PLANIFICACIÓN, PRESUPUESTO Y DESARROLLO</option>
                                        <option value="DIR. TAL. HUM." <?php echo seleccionarOpcion('dpt_desempeñar', 'DIR. TAL. HUM.'); ?>>DIRECCIÓN DE TALENTO HUMANO</option>
                                        <option value="DIR. TEC., INF. Y TELECOM." <?php echo seleccionarOpcion('dpt_desempeñar', 'DIR. TEC., INF. Y TELECOM.'); ?>>DIRECCIÓN DE TECNOLOGÍA, INFORMÁTICA Y TELECOMUNICACIONES</option>
                                        <option value="DIR. OPER." <?php echo seleccionarOpcion('dpt_desempeñar', 'DIR. OPER.'); ?>>DIRECCIÓN DE OPERACIONES</option>
                                        <option value="DIR. DES. INTEG." <?php echo seleccionarOpcion('dpt_desempeñar', 'DIR. DES. INTEG.'); ?>>DIRECCIÓN DE DESARROLLO INTEGRAL</option>
                                        <option value="DIR. PREV. Y REHAB." <?php echo seleccionarOpcion('dpt_desempeñar', 'DIR. PREV. Y REHAB.'); ?>>DIRECCIÓN DE PREVENCIÓN Y REHABILITACIÓN</option>
                                        <option value="DIR. REINS. SOC-FAM. Y SOC-LAB." <?php echo seleccionarOpcion('dpt_desempeñar', 'DIR. REINS. SOC-FAM. Y SOC-LAB.'); ?>>DIRECCIÓN DE REINSERCIÓN SOCIO-FAMILIAR Y SOCIO-LABORAL</option>
                                        <option value="DIR. BIEN. INTEG." <?php echo seleccionarOpcion('dpt_desempeñar', 'DIR. BIEN. INTEG.'); ?>>DIRECCIÓN DE BIENESTAR INTEGRAL</option>
                                        <option value="DIR. CENT. SOCIOEDUC." <?php echo seleccionarOpcion('dpt_desempeñar', 'DIR. CENT. SOCIOEDUC.'); ?>>DIRECCIÓN DE CENTROS SOCIOEDUCATIVOS</option>
                                        <option value="DIR. CAS. ABRIGO" <?php echo seleccionarOpcion('dpt_desempeñar', 'DIR. CAS. ABRIGO'); ?>>DIRECCIÓN DE CASAS DE ABRIGO</option>
                                        <option value="COORD. SEG." <?php echo seleccionarOpcion('dpt_desempeñar', 'COORD. SEG.'); ?>>COORDINACIÓN DE SEGURIDAD</option>
                                        <option value="COORD. NUTRIC." <?php echo seleccionarOpcion('dpt_desempeñar', 'COORD. NUTRIC.'); ?>>COORDINACIÓN DE NUTRICIÓN</option>
                                        <option value="COORD. SERV. GEN." <?php echo seleccionarOpcion('dpt_desempeñar', 'COORD. SERV. GEN.'); ?>>COORDINACIÓN DE SERVICIOS GENERALES</option>
                                        <option value="COORD. TRANS." <?php echo seleccionarOpcion('dpt_desempeñar', 'COORD. TRANS.'); ?>>COORDINACIÓN DE TRANSPORTE</option>
                                        <option value="COORD. BIENES" <?php echo seleccionarOpcion('dpt_desempeñar', 'COORD. BIENES'); ?>>COORDINACIÓN DE BIENES</option>
                                        <option value="COORD. PSIC." <?php echo seleccionarOpcion('dpt_desempeñar', 'COORD. PSIC.'); ?>>COORDINACIÓN DE PSICOLOGÍA</option>
                                        <option value="NUC. TERR." <?php echo seleccionarOpcion('dpt_desempeñar', 'NUC. TERR.'); ?>>NÚCLEOS TERRITORIALES</option>
                                    </select>
                                </div>

                            <div class="input-group">
                                <label for="sede">Sede</label>
                                <select id="sede" name="sede">
                                    <option value="">Selecciona una opción</option>
                                    <option value="ADMIN" <?php echo seleccionarOpcion('sede', 'ADMIN'); ?>>ADMIN</option>
                                    <option value="CAFO" <?php echo seleccionarOpcion('sede', 'CAFO'); ?>>CAFO</option>
                                    <option value="CATE" <?php echo seleccionarOpcion('sede', 'CATE'); ?>>CATE</option>
                                    <option value="CSAI" <?php echo seleccionarOpcion('sede', 'CSAI'); ?>>CSAI</option>
                                    <option value="CSB" <?php echo seleccionarOpcion('sede', 'CSB'); ?>>CSB</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="dependencia">Dependencia</label>
                                <select id="dependencia" name="dependencia">
                                    <option value="">Selecciona una opción </option>
                                    <option value="ADMIN" <?php echo seleccionarOpcion('dependencia', 'ADMIN'); ?>>ADMIN</option>
                                    <option value="CAFO" <?php echo seleccionarOpcion('dependencia', 'CAFO'); ?>>CAFO</option>
                                    <option value="CATE" <?php echo seleccionarOpcion('dependencia', 'CATE'); ?>>CATE</option>
                                    <option value="CSAI" <?php echo seleccionarOpcion('dependencia', 'CSAI'); ?>>CSAI</option>
                                    <option value="CSB" <?php echo seleccionarOpcion('dependencia', 'CSB'); ?>>CSB</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="fecha_ingreso">Fecha de Ingreso</label>
                                <input type="date" id="fecha_ingreso" name="fecha_ingreso" value="<?php echo repoblarCampo('fecha_ingreso'); ?>">
                            </div>
                            <div class="input-group">
                                <label for="codigo_siantel">Codigo Carnet SIAMTEL</label>
                                <input type="text" id="cod_siantel" name="cod_siantel" placeholder="Ingrese el codigo " value="<?php echo repoblarCampo('cod_siantel'); ?>">
                            </div>
                            <div class="input-group">
                                <label for="ubicacionEstante">Ubicación estante</label>
                                <input type="text" id="ubicacionEstante" name="ubicacion_estante" placeholder="Ej: Estante A-5 " value="<?php echo repoblarCampo('ubicacion_estante'); ?>">
                            </div>
                            <div class="input-group">
                                <label for="estatus">Estatus</label>
                                <select id="estatus" name="estatus">
                                    <option value="">Selecciona un estatus </option>
                                    <option value="ACTIVO" <?php echo seleccionarOpcion('estatus', 'ACTIVO'); ?>>ACTIVO</option>
                                    <option value="INACTIVO" <?php echo seleccionarOpcion('estatus', 'INACTIVO'); ?>>INACTIVO</option>
                                </select>
                            </div>
                            <!-- Formulario adicional (oculto por defecto) -->
                            <div id="formularioExtra">
                                <h3>Detalles de Retiro</h3>
                                <div class="form-grid">
                                    <div class="input-group">
                                        <label for="fechaEgreso">Fecha de egreso</label>
                                        <input type="date" id="fechaEgreso" name="fecha_egreso" value="<?php echo repoblarCampo('fecha_egreso'); ?>">
                                    </div>
                                    <div class="input-group">
                                        <label for="motivoRetiro">Motivo del retiro</label>
                                        <textarea id="motivoRetiro" name="motivo_retiro" rows="3" placeholder="Describe el motivo "><?php echo repoblarCampo('motivo_retiro'); ?></textarea>
                                    </div>
                                    <div class="input-group">
                                        <label for="ubicacionEstanteRetiro">Ubicación estante (Retiro)</label>
                                        <input type="text" id="ubicacionEstanteRetiro" name="ubicacion_estante_retiro" placeholder="Ej: Estante A-5 " value="<?php echo repoblarCampo('ubicacion_estante_retiro'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- PASO 3: INFORMACIÓN GENERAL -->
                    <div class="step-content" data-step="3">
                        <div class="step-title">
                            <div class="step-number" style="width: 40px; height: 40px; font-size: 16px;">3</div>
                            <h2>Información General</h2>
                        </div>
                        <p class="step-description">
                            Complete la información adicional y características personales del trabajador. <strong>Todos los campos son opcionales.</strong>
                        </p>
                        
                        <div class="form-grid">
                            <div class="input-group">
                                <label for="tipo_sangre">Tipo de Sangre</label>
                                <select id="tipo_sangre" name="tipo_sangre">
                                    <option value="">Selecciona una opcion </option>
                                    <option value="A+" <?php echo seleccionarOpcion('tipo_sangre', 'A+'); ?>>A+</option>
                                    <option value="A-" <?php echo seleccionarOpcion('tipo_sangre', 'A-'); ?>>A-</option>
                                    <option value="B+" <?php echo seleccionarOpcion('tipo_sangre', 'B+'); ?>>B+</option>
                                    <option value="B-" <?php echo seleccionarOpcion('tipo_sangre', 'B-'); ?>>B-</option>
                                    <option value="AB+" <?php echo seleccionarOpcion('tipo_sangre', 'AB+'); ?>>AB+</option>
                                    <option value="AB-" <?php echo seleccionarOpcion('tipo_sangre', 'AB-'); ?>>AB-</option>
                                    <option value="O+" <?php echo seleccionarOpcion('tipo_sangre', 'O+'); ?>>O+</option>
                                    <option value="O-" <?php echo seleccionarOpcion('tipo_sangre', 'O-'); ?>>O-</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="lateralidad">Lateralidad</label>
                                <select id="lateralidad" name="lateralidad">
                                    <option value="">Selecciona una opcion </option>
                                    <option value="DIESTRO(A)" <?php echo seleccionarOpcion('lateralidad', 'DIESTRO(A)'); ?>>DIESTRO(A)</option>
                                    <option value="ZURDO(A)" <?php echo seleccionarOpcion('lateralidad', 'ZURDO(A)'); ?>>ZURDO(A)</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="peso_trabajador">Peso del Trabajador</label>
                                <input type="text" id="peso_trabajador" name="peso_trabajador" placeholder="Ingrese el peso del trabajador " value="<?php echo repoblarCampo('peso_trabajador'); ?>">
                            </div>
                            <div class="input-group">
                                <label for="altura_trabajador">Ingrese Altura en Metros</label>
                                <input type="text" id="altura_trabajador" name="altura_trabajador" placeholder="Ingrese la altura del trabajador " value="<?php echo repoblarCampo('altura_trabajador'); ?>">
                            </div>
                            <div class="input-group">
                                <label for="talla_calzado">Talla calzado del trabajador</label>
                                <input type="text" id="calzado_trabajador" name="calzado_trabajador" placeholder="Ingrese la talla calzado del trabajador " value="<?php echo repoblarCampo('calzado_trabajador'); ?>">
                            </div>
                            <div class="input-group">
                                <label for="talla_camisa">Talla camisa del Trabajador</label>
                                <input type="text" id="camisa_trabajador" name="camisa_trabajador" placeholder="Ingrese la talla camisa del trabajador " value="<?php echo repoblarCampo('camisa_trabajador'); ?>">
                            </div>
                            <div class="input-group">
                                <label for="talla_pantalon">Talla pantalon del Trabajador</label>
                                <input type="text" id="pantalon_trabajador" name="pantalon_trabajador" placeholder="Ingrese la talla pantalon del trabajador " value="<?php echo repoblarCampo('pantalon_trabajador'); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- PASO 4: FAMILIA -->
                    <div class="step-content" data-step="4">
                        <div class="step-title">
                            <div class="step-number" style="width: 40px; height: 40px; font-size: 16px;">4</div>
                            <h2>Familia</h2>
                        </div>
                        <p class="step-description">
                            Agregue los datos de los familiares del trabajador. Puede agregar hasta 10 familiares. 
                        </p>
                        
                        <!-- Contenedor de familiares -->
                        <div id="familiares-container">
                            <!-- Los familiares se agregan dinámicamente aquí -->
                        </div>
                        
                        <div style="text-align: center; margin: 30px 0;">
                            <button type="button" id="agregar-familiar" class="btn primary">
                                <i class="fas fa-plus"></i> Agregar Familiar
                            </button>
                            <button type="button" id="reiniciar-familiares" class="btn secondary">
                                <i class="fas fa-redo"></i> Reiniciar Todo
                            </button>
                        </div>
                    </div>
                    
                    <!-- PASO 5: FOTO -->
                    <div class="step-content" data-step="5">
                        <div class="step-title">
                            <div class="step-number" style="width: 40px; height: 40px; font-size: 16px;">5</div>
                            <h2>Fotografía</h2>
                        </div>
                        <p class="step-description">
                            Suba una fotografía del trabajador. Formatos aceptados: JPG, PNG. Tamaño máximo: 5MB. <strong>Este campo es opcional.</strong>
                        </p>
                        
                        <div class="photo-container">
                            <input type="file" id="foto" name="foto" accept="image/*" style="display: none;">
                            <div id="preview" class="photo-preview">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Haz clic para seleccionar una foto </p>
                                <small style="color: var(--light-text); margin-top: 10px;">Recomendado: 300x300 px</small>
                            </div>
                            <button type="button" id="btn-seleccionar-foto" class="btn secondary">
                                <i class="fas fa-folder-open"></i> Seleccionar Foto
                            </button>
                        </div>
                    </div>
                    
                    <!-- BOTONES DE NAVEGACIÓN -->
                    <div class="form-actions">
                        <button type="button" id="prev-btn" class="btn secondary" disabled>
                            <i class="fas fa-arrow-left"></i> Paso Anterior
                        </button>
                        
                        <div style="display: flex; gap: 15px;">
                            <button type="button" id="cancel-btn" class="btn secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button type="button" id="next-btn" class="btn primary">
                                Siguiente Paso <i class="fas fa-arrow-right"></i>
                            </button>
                            <button type="submit" id="submit-btn" class="btn primary" style="display: none;">
                                <i class="fas fa-save"></i> Registrar Trabajador
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- MODAL DE ÉXITO -->
    <?php if ($empleado_registrado): ?>
    <div id="successModal" class="modal-overlay" style="display: flex;">
        <div class="success-modal">
            <div class="modal-header">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2>¡Registro Exitoso!</h2>
                <p class="modal-subtitle">Trabajador registrado correctamente</p>
            </div>
            
            <div class="modal-body">
                <div class="success-summary">
                    <?php if ($nombre_trabajador): ?>
                    <div class="summary-item">
                        <i class="fas fa-user"></i>
                        <div class="summary-content">
                            <span class="summary-label">Trabajador</span>
                            <span class="summary-value"><?php echo htmlspecialchars($nombre_trabajador); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($ci_trabajador): ?>
                    <div class="summary-item">
                        <i class="fas fa-id-card"></i>
                        <div class="summary-content">
                            <span class="summary-label">Cédula</span>
                            <span class="summary-value"><?php echo htmlspecialchars($ci_trabajador); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="summary-item">
                        <i class="fas fa-users"></i>
                        <div class="summary-content">
                            <span class="summary-label">Familiares Registrados</span>
                            <span class="summary-value"><?php echo $num_familiares; ?></span>
                        </div>
                    </div>
                    
                    <?php if ($fecha_registro): ?>
                    <div class="summary-item">
                        <i class="fas fa-calendar-alt"></i>
                        <div class="summary-content">
                            <span class="summary-label">Fecha y Hora</span>
                            <span class="summary-value"><?php echo $fecha_registro; ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="success-message">
                    <i class="fas fa-info-circle"></i>
                    <p><?php echo $success_message; ?></p>
                </div>
            </div>
            
            <div class="modal-footer">
                <div class="action-buttons">
                    <button type="button" id="btnContinuarRegistro" class="btn-primary">
                        <i class="fas fa-plus-circle"></i> Registrar Otro
                    </button>
                    
                    <a href="index.php" class="btn-outline">
                        <i class="fas fa-list"></i> Ir al Listado
                    </a>
                </div>
                
                <div class="auto-close-notice">
                    <i class="fas fa-clock"></i>
                    <span>Este mensaje se cerrará automáticamente en <span id="countdown">8</span> segundos</span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/formulario.js"></script>
    
    
</body>
</html>

<?php
$conn->close();
?>