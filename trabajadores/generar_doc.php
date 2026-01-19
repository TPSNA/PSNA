<?php
// usuario/trabajadores/generar_doc.php - INTERFAZ PARA GENERAR DOCUMENTOS LABORALES
session_start();

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: ../../admin/login.php"); // Cambiado a ../../admin/login.php
    exit();
}

require_once('../includes/database.php'); // Mantenido como está

// Obtener datos del empleado si se ha buscado
$empleado = null;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['buscar_empleado'])) {
        $busqueda = trim($_POST['busqueda']);
        
        if (!empty($busqueda)) {
            // Buscar por CI (cedula) o nombres
            $sql = "SELECT * FROM empleados WHERE 
                   ci LIKE ? OR 
                   primer_nombre LIKE ? OR 
                   primer_apellido LIKE ? OR 
                   CONCAT(primer_nombre, ' ', primer_apellido) LIKE ?";
            $stmt = $conn->prepare($sql);
            $search_term = "%$busqueda%";
            $stmt->bind_param("ssss", $search_term, $search_term, $search_term, $search_term);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $empleado = $result->fetch_assoc();
                
                // Formatear el nombre completo
                $empleado['nombre_completo'] = $empleado['primer_nombre'] . ' ' . 
                    ($empleado['segundo_nombre'] ? $empleado['segundo_nombre'] . ' ' : '') . 
                    $empleado['primer_apellido'] . ' ' . 
                    ($empleado['segundo_apellido'] ? $empleado['segundo_apellido'] : '');
            } else {
                $error = "No se encontró ningún empleado con esos datos.";
            }
        } else {
            $error = "Por favor, ingrese un nombre o cédula para buscar.";
        }
    }
    
    // Generar documento
    if (isset($_POST['generar_documento'])) {
        $tipo_documento = $_POST['tipo_documento'];
        $empleado_id = $_POST['empleado_id'];
        $sueldo_base = $_POST['sueldo_base'];
        $prima_profesionalizacion = $_POST['prima_profesionalizacion'] ?? 0;
        $prima_antiguedad = $_POST['prima_antiguedad'] ?? 0;
        $prima_hijos = $_POST['prima_hijos'] ?? 0;
        $total_ingreso = $_POST['total_ingreso'];
        
        // Obtener datos del empleado
        $sql = "SELECT * FROM empleados WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $empleado_id);
        $stmt->execute();
        $empleado = $stmt->get_result()->fetch_assoc();
        
        // Redirigir al generador de PDF correspondiente
        switch($tipo_documento) {
            case 'constancia_trabajo':
                header("Location: ../php/doc/generar_constancia_trabajo.php?" . http_build_query(array_merge(
                    ['id' => $empleado_id],
                    $_POST
                )));
                exit();
                
            case 'constancia_egreso':
                header("Location: ../php/doc/generar_constancia_egreso.php?" . http_build_query(array_merge(
                    ['id' => $empleado_id],
                    $_POST
                )));
                exit();
                
            case 'notificacion_vacaciones':
                header("Location: ../php/doc/generar_notificacion_vacaciones.php?" . http_build_query(array_merge(
                    ['id' => $empleado_id],
                    $_POST
                )));
                exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Documentos - SAINA</title>
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
            --info-color: #3498db;
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
            padding-top: 80px;
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
        
        /* ===== CONTENIDO PRINCIPAL ===== */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        /* ===== TARJETA PRINCIPAL ===== */
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
        
        /* ===== BUSCADOR ===== */
        .search-section {
            padding: 40px;
            background: #f8f9ff;
        }
        
        .search-container {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        
        .search-box {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .search-input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 16px;
            transition: var(--transition);
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(106, 103, 240, 0.1);
        }
        
        .search-btn {
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            color: white;
            border: none;
            padding: 0 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(106, 103, 240, 0.3);
        }
        
        /* ===== BOTÓN DE LIMPIAR FILTRO ===== */
        .clear-filter-btn {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 0 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        
        .clear-filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(52, 152, 219, 0.3);
            background: linear-gradient(135deg, #2980b9, #3498db);
        }
        
        .search-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
        }
        
        /* ===== INFORMACIÓN DEL EMPLEADO ===== */
        .employee-info {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
            border-left: 5px solid var(--primary-color);
        }
        
        .employee-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .employee-main {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .employee-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 40px;
        }
        
        .employee-details h3 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .employee-details p {
            color: var(--light-text);
            font-size: 15px;
        }
        
        .employee-status {
            background: linear-gradient(135deg, var(--info-color), #2980b9);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .employee-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-card {
            background: #f8f9ff;
            padding: 20px;
            border-radius: 15px;
        }
        
        .info-card h4 {
            font-size: 14px;
            color: var(--light-text);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .info-card p {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-color);
        }
        
        /* ===== SECCIÓN DE DOCUMENTOS ===== */
        .documents-section {
            padding: 40px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .section-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .section-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--info-color), #2980b9);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
        }
        
        .section-title h2 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-color);
        }
        
        .documents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .document-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.4s ease;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .document-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(106, 103, 240, 0.15);
            border-color: var(--primary-color);
        }
        
        .document-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--purple-start), var(--blue-end));
        }
        
        .document-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--info-color), #2980b9);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            color: white;
            font-size: 28px;
        }
        
        .document-content h3 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--text-color);
        }
        
        .document-content p {
            color: var(--light-text);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        /* ===== BOTONES ===== */
        .btn-primary {
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(106, 103, 240, 0.3);
        }
        
        .btn-info {
            background: linear-gradient(135deg, var(--info-color), #2980b9);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        
        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(52, 152, 219, 0.3);
            background: linear-gradient(135deg, #2980b9, var(--info-color));
        }
        
        .btn-secondary {
            background: #e9ecef;
            color: var(--text-color);
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .btn-secondary:hover {
            background: #dee2e6;
        }
        
        /* ===== FORMULARIO DE DOCUMENTOS ===== */
        .document-form {
            background: #f8f9ff;
            padding: 30px;
            border-radius: 20px;
            margin-top: 30px;
            display: none;
        }
        
        .document-form.active {
            display: block;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-color);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 15px;
            transition: var(--transition);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(106, 103, 240, 0.1);
        }
        
        .salary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin: 25px 0;
        }
        
        /* ===== ALINEACIÓN DE ETIQUETAS DE SALARIO ===== */
        .salary-grid .form-group {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .salary-grid .form-label {
            min-height: 38px; /* Altura fija para todas las etiquetas */
            display: flex;
            align-items: flex-end; /* Alinea el texto en la parte inferior */
            margin-bottom: 8px;
            line-height: 1.2;
        }
        
        .salary-grid .form-control {
            flex: 1;
        }
        
        .salary-total {
            background: linear-gradient(135deg, var(--info-color), #2980b9);
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            margin: 25px 0;
        }
        
        .salary-total h4 {
            font-size: 14px;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        
        .salary-total .total-amount {
            font-size: 32px;
            font-weight: 700;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        /* ===== ALERTAS ===== */
        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert-danger {
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #f44336;
        }
        
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #4caf50;
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
            
            .search-box {
                flex-direction: column;
            }
            
            .search-actions {
                flex-direction: column;
            }
            
            .documents-grid {
                grid-template-columns: 1fr;
            }
            
            .employee-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
        
        @media (max-width: 480px) {
            .form-header {
                padding: 30px 20px;
            }
            
            .search-section, .documents-section {
                padding: 20px;
            }
            
            .employee-main {
                flex-direction: column;
                text-align: center;
            }
            
            .search-box {
                gap: 10px;
            }
            
            .search-btn, .clear-filter-btn {
                padding: 12px 20px;
                font-size: 14px;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- ===== HEADER UNIFICADO ===== -->
    <header class="main-header">
        <div class="header-container">
            <div class="logo-container">
                <img src="../../imagenes/Logo SAINA Horizontal.png" alt="SAINA Logo"> 
                <span class="system-name"></span>
            </div>
            
            <button class="mobile-menu-btn" id="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
            
            <nav class="nav-menu" id="nav-menu">
                <a href="../index.php" class="nav-link"> 
                    <i class="fas fa-home"></i> Inicio
                </a>
                <a href="../trabajadores/index.php" class="nav-link"> 
                    <i class="fas fa-history"></i> Gestionar Trabajadores
                </a>
                <a href="../trabajadores/buscar.php" class="nav-link">
                    <i class="fas fa-users"></i> Buscar Trabajadores
                </a>
                <a href="../trabajadores/formulario1.php" class="nav-link">
                    <i class="fas fa-user-plus"></i> Nuevo Trabajador
                </a>
                <a href="../trabajadores/expedientes.php" class="nav-link">
                    <i class="fas fa-folder-open"></i> Expedientes
                </a>
                <a href="../trabajadores/reportes.php" class="nav-link">
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
    
    <!-- ===== CONTENIDO PRINCIPAL ===== -->
    <div class="main-container">
        <!-- ===== TARJETA PRINCIPAL ===== -->
        <div class="form-card">
            <!-- CABECERA -->
            <div class="form-header">
                <div class="form-icon">
                    <i class="fas fa-file-contract"></i>
                </div>
                <h1>Generar Documentos Laborales</h1>
                <p>Sistema de Administración Integral de Nómina y Archivo</p>
            </div>
            
            <!-- BUSCADOR -->
            <div class="search-section">
                <div class="search-container">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" id="search-form">
                        <div class="search-box">
                            <input type="text" 
                                   name="busqueda" 
                                   class="search-input" 
                                   placeholder="Ingrese cédula o nombre del empleado..."
                                   value="<?php echo isset($_POST['busqueda']) ? htmlspecialchars($_POST['busqueda']) : ''; ?>"
                                   id="search-input">
                            <button type="submit" name="buscar_empleado" class="search-btn">
                                <i class="fas fa-search"></i> Buscar Empleado
                            </button>
                        </div>
                        
                        <?php if ($empleado || isset($_POST['busqueda'])): ?>
                        <div class="search-actions">
                            <a href="generar_doc.php" class="clear-filter-btn" id="clear-filter-btn">
                                <i class="fas fa-times"></i> Limpiar Filtro
                            </a>
                        </div>
                        <?php endif; ?>
                    </form>
                    
                    <!-- INFORMACIÓN DEL EMPLEADO ENCONTRADO -->
                    <?php if ($empleado): ?>
                        <div class="employee-info">
                            <div class="employee-header">
                                <div class="employee-main">
                                    <div class="employee-avatar">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <div class="employee-details">
                                        <h3><?php echo $empleado['nombre_completo']; ?></h3>
                                        <p>Cédula: <?php echo $empleado['nacionalidad'] . '-' . $empleado['ci']; ?></p>
                                        <p>Cargo: <?php echo $empleado['cargo']; ?></p>
                                    </div>
                                </div>
                                <div class="employee-status">
                                    <?php echo $empleado['estatus']; ?>
                                </div>
                            </div>
                            
                            <div class="employee-grid">
                                <div class="info-card">
                                    <h4>Fecha de Ingreso</h4>
                                    <p><?php echo date('d/m/Y', strtotime($empleado['fecha_ingreso'])); ?></p>
                                </div>
                                <div class="info-card">
                                    <h4>Teléfono</h4>
                                    <p><?php echo $empleado['telefono']; ?></p>
                                </div>
                                <div class="info-card">
                                    <h4>Departamento</h4>
                                    <p><?php echo $empleado['dependencia']; ?></p>
                                </div>
                                <div class="info-card">
                                    <h4>Tipo de Trabajador</h4>
                                    <p><?php echo $empleado['tipo_trabajador']; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- DOCUMENTOS DISPONIBLES -->
            <div class="documents-section">
                <?php if ($empleado): ?>
                    <div class="section-header">
                        <div class="section-title">
                            <div class="section-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h2>Seleccione el Documento a Generar</h2>
                        </div>
                    </div>
                    
                    <div class="documents-grid">
                        <!-- CONSTANCIA DE TRABAJO -->
                        <div class="document-card">
                            <div class="document-icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div class="document-content">
                                <h3>Constancia de Trabajo</h3>
                                <p>Documento oficial que certifica la relación laboral, cargo, fecha de ingreso y salario del empleado.</p>
                                <button class="btn-info btn-generate" data-doc="constancia_trabajo">
                                    <i class="fas fa-file-download"></i> Generar Documento
                                </button>
                            </div>
                        </div>
                        
                        <!-- CONSTANCIA DE EGRESO -->
                        <div class="document-card">
                            <div class="document-icon">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                            <div class="document-content">
                                <h3>Constancia de Egreso</h3>
                                <p>Certifica el cese de labores del empleado, indicando fecha de ingreso, egreso y motivo de salida.</p>
                                <button class="btn-info btn-generate" data-doc="constancia_egreso">
                                    <i class="fas fa-file-download"></i> Generar Documento
                                </button>
                            </div>
                        </div>
                        
                        <!-- NOTIFICACIÓN DE VACACIONES -->
                        <div class="document-card">
                            <div class="document-icon">
                                <i class="fas fa-umbrella-beach"></i>
                            </div>
                            <div class="document-content">
                                <h3>Notificación de Vacaciones</h3>
                                <p>Formato oficial para notificar el período vacacional del empleado con fechas de disfrute.</p>
                                <button class="btn-info btn-generate" data-doc="notificacion_vacaciones">
                                    <i class="fas fa-file-download"></i> Generar Documento
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- FORMULARIO DINÁMICO -->
                    <form method="POST" action="" id="document-form" class="document-form">
                        <input type="hidden" name="empleado_id" value="<?php echo $empleado['id']; ?>">
                        <input type="hidden" name="tipo_documento" id="tipo_documento" value="">
                        
                        <!-- Sección de datos de salario (común para todos) -->
                        <div class="salary-section">
                            <h3 style="margin-bottom: 20px; color: var(--primary-color);">
                                <i class="fas fa-money-bill-wave"></i> Ingrese los Datos Salariales
                            </h3>
                            
                            <div class="salary-grid">
                                <div class="form-group">
                                    <label class="form-label">Sueldo Base (Bs)</label>
                                    <input type="number" name="sueldo_base" class="form-control" step="0.01" required value="348.00">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Prima por Profesionalización (Bs)</label>
                                    <input type="number" name="prima_profesionalizacion" class="form-control" step="0.01" value="0.00">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Prima por Antigüedad (Bs)</label>
                                    <input type="number" name="prima_antiguedad" class="form-control" step="0.01" value="0.00">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Prima por Hijos (Bs)</label>
                                    <input type="number" name="prima_hijos" class="form-control" step="0.01" value="12.50">
                                </div>
                            </div>
                            
                            <div class="salary-total">
                                <h4>TOTAL DE INGRESO MENSUAL</h4>
                                <div class="total-amount" id="total-ingreso">360,50 Bs</div>
                                <input type="hidden" name="total_ingreso" id="total-ingreso-input" value="360.50">
                            </div>
                        </div>
                        
                        <!-- Campos específicos para cada documento -->
                        <div id="constancia_trabajo_fields" style="display: none;">
                            <!-- Campos adicionales para constancia de trabajo -->
                            <div class="form-group">
                                <label class="form-label">Horario de Trabajo</label>
                                <input type="text" name="horario" class="form-control" value="08:00 AM - 12:00 PM / 01:00 PM - 04:00 PM">
                            </div>
                        </div>
                        
                        <div id="constancia_egreso_fields" style="display: none;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Fecha de Egreso</label>
                                    <input type="date" name="fecha_egreso" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Motivo de Egreso</label>
                                    <select name="motivo_egreso" class="form-control">
                                        <option value="RENUNCIA VOLUNTARIA">Renuncia Voluntaria</option>
                                        <option value="DESPIDO">Despido</option>
                                        <option value="JUBILACIÓN">Jubilación</option>
                                        <option value="FIN DE CONTRATO">Fin de Contrato</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div id="notificacion_vacaciones_fields" style="display: none;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Fecha Inicio Vacaciones</label>
                                    <input type="date" name="fecha_inicio_vacaciones" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Fecha Fin Vacaciones</label>
                                    <input type="date" name="fecha_fin_vacaciones" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Días de Vacaciones</label>
                                    <input type="number" name="dias_vacaciones" class="form-control" value="15">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Año de Disfrute</label>
                                <input type="number" name="anio_disfrute" class="form-control" value="<?php echo date('Y'); ?>">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="generar_documento" class="btn-primary">
                                <i class="fas fa-file-pdf"></i> Generar Documento PDF
                            </button>
                            <button type="button" class="btn-secondary" id="cancel-btn">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="no-employee" style="text-align: center; padding: 60px 20px;">
                        <div style="font-size: 80px; color: #e0e0e0; margin-bottom: 20px;">
                            <i class="fas fa-user-search"></i>
                        </div>
                        <h3 style="color: var(--light-text); margin-bottom: 15px;">
                            Busque un empleado para generar documentos
                        </h3>
                        <p style="color: var(--light-text); max-width: 500px; margin: 0 auto;">
                            Utilice el buscador superior para encontrar al empleado y seleccionar el documento que desea generar.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Menú móvil
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const navMenu = document.getElementById('nav-menu');
            
            mobileMenuBtn.addEventListener('click', function() {
                navMenu.classList.toggle('active');
            });
            
            // Calcular total de ingreso
            function calcularTotalIngreso() {
                const sueldoBase = parseFloat(document.querySelector('[name="sueldo_base"]').value) || 0;
                const primaProf = parseFloat(document.querySelector('[name="prima_profesionalizacion"]').value) || 0;
                const primaAnt = parseFloat(document.querySelector('[name="prima_antiguedad"]').value) || 0;
                const primaHijos = parseFloat(document.querySelector('[name="prima_hijos"]').value) || 0;
                
                const total = sueldoBase + primaProf + primaAnt + primaHijos;
                
                document.getElementById('total-ingreso').textContent = total.toLocaleString('es-VE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' Bs';
                
                document.getElementById('total-ingreso-input').value = total;
            }
            
            // Inicializar cálculo
            calcularTotalIngreso();
            
            // Actualizar cálculo cuando cambien los campos
            const camposSalario = document.querySelectorAll('[name="sueldo_base"], [name="prima_profesionalizacion"], [name="prima_antiguedad"], [name="prima_hijos"]');
            camposSalario.forEach(campo => {
                campo.addEventListener('input', calcularTotalIngreso);
            });
            
            // Manejar botones de generar documento
            const btnGenerates = document.querySelectorAll('.btn-generate');
            const documentForm = document.getElementById('document-form');
            const tipoDocumentoInput = document.getElementById('tipo_documento');
            const cancelBtn = document.getElementById('cancel-btn');
            
            // Ocultar todos los campos específicos al inicio
            document.querySelectorAll('[id$="_fields"]').forEach(field => {
                field.style.display = 'none';
            });
            
            btnGenerates.forEach(btn => {
                btn.addEventListener('click', function() {
                    const tipoDoc = this.getAttribute('data-doc');
                    
                    // Setear tipo de documento
                    tipoDocumentoInput.value = tipoDoc;
                    
                    // Ocultar todos los campos específicos primero
                    document.querySelectorAll('[id$="_fields"]').forEach(field => {
                        field.style.display = 'none';
                    });
                    
                    // Mostrar campos específicos del documento seleccionado
                    document.getElementById(tipoDoc + '_fields').style.display = 'block';
                    
                    // Mostrar formulario con animación
                    documentForm.classList.add('active');
                    
                    // Scroll al formulario
                    documentForm.scrollIntoView({ behavior: 'smooth' });
                });
            });
            
            // Botón cancelar
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    documentForm.classList.remove('active');
                    calcularTotalIngreso(); // Recalcular con valores por defecto
                });
            }
            
            // Efectos visuales para tarjetas
            const documentCards = document.querySelectorAll('.document-card');
            documentCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });
            
            // Pre-fill fecha de hoy para egreso
            const fechaEgreso = document.querySelector('[name="fecha_egreso"]');
            if (fechaEgreso) {
                const hoy = new Date();
                fechaEgreso.valueAsDate = hoy;
            }
            
            // Pre-fill fechas de vacaciones
            const fechaInicioVacaciones = document.querySelector('[name="fecha_inicio_vacaciones"]');
            const fechaFinVacaciones = document.querySelector('[name="fecha_fin_vacaciones"]');
            const diasVacaciones = document.querySelector('[name="dias_vacaciones"]');
            
            if (fechaInicioVacaciones && fechaFinVacaciones && diasVacaciones) {
                const hoy = new Date();
                fechaInicioVacaciones.valueAsDate = hoy;
                
                // Calcular fin de vacaciones automáticamente
                const finVacaciones = new Date(hoy);
                finVacaciones.setDate(hoy.getDate() + parseInt(diasVacaciones.value) - 1);
                fechaFinVacaciones.valueAsDate = finVacaciones;
            }
            
            // Limpiar filtro cuando se presiona la tecla Escape en el campo de búsqueda
            const searchInput = document.getElementById('search-input');
            const clearFilterBtn = document.getElementById('clear-filter-btn');
            
            if (searchInput) {
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        this.value = '';
                        this.focus();
                    }
                });
            }
            
            // Efecto visual al hacer clic en limpiar filtro
            if (clearFilterBtn) {
                clearFilterBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Animación de desvanecimiento
                    const container = document.querySelector('.search-container');
                    if (container) {
                        container.style.opacity = '0.7';
                        container.style.transition = 'opacity 0.3s ease';
                        
                        setTimeout(() => {
                            window.location.href = 'generar_doc.php';
                        }, 300);
                    } else {
                        window.location.href = 'generar_doc.php';
                    }
                });
            }
            
            // Función para limpiar el campo de búsqueda
            function clearSearchField() {
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.focus();
                }
            }
            
            // También podemos agregar un botón "X" dentro del campo de búsqueda
            if (searchInput) {
                // Crear botón clear dentro del input
                const clearSearchBtn = document.createElement('button');
                clearSearchBtn.innerHTML = '<i class="fas fa-times"></i>';
                clearSearchBtn.style.position = 'absolute';
                clearSearchBtn.style.right = '10px';
                clearSearchBtn.style.top = '50%';
                clearSearchBtn.style.transform = 'translateY(-50%)';
                clearSearchBtn.style.background = 'transparent';
                clearSearchBtn.style.border = 'none';
                clearSearchBtn.style.color = '#999';
                clearSearchBtn.style.cursor = 'pointer';
                clearSearchBtn.style.display = 'none';
                
                // Envolver el input en un contenedor relativo
                const searchContainer = searchInput.parentElement;
                searchContainer.style.position = 'relative';
                searchContainer.appendChild(clearSearchBtn);
                
                // Mostrar/ocultar botón clear
                searchInput.addEventListener('input', function() {
                    clearSearchBtn.style.display = this.value ? 'block' : 'none';
                });
                
                // Limpiar campo al hacer clic
                clearSearchBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    searchInput.focus();
                    clearSearchBtn.style.display = 'none';
                });
            }
        });
    </script>
    
    <?php if (isset($conn)) { $conn->close(); } ?>
</body>
</html>