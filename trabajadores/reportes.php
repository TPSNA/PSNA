<?php
// usuario/reportes.php - INTERFAZ DE REPORTES
session_start();

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: ../admin/login.php");
    exit();
}

require_once('../includes/database.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - SAINA</title>
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
        
        /* ===== HEADER UNIFICADO (Igual a formulario1.php) ===== */
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
        
        /* ===== TARJETA DE REPORTES ===== */
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
        
        /* ===== CONTENIDO DE LOS REPORTES ===== */
        .form-body {
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
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
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
        
        /* ===== GRID DE REPORTES ===== */
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .report-card {
            background: white;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.4s ease;
            border: 2px solid transparent;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .report-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(106, 103, 240, 0.2);
            border-color: var(--primary-color);
        }
        
        .report-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--purple-start), var(--blue-end));
        }
        
        .report-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: white;
            font-size: 32px;
            transition: all 0.3s ease;
        }
        
        .report-card:hover .report-icon {
            transform: scale(1.1) rotate(5deg);
        }
        
        .report-content h3 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--text-color);
        }
        
        .report-content p {
            color: var(--light-text);
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        
        .report-stats {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
            padding: 15px;
            background: rgba(106, 103, 240, 0.05);
            border-radius: 15px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            display: block;
        }
        
        .stat-label {
            font-size: 12px;
            color: var(--light-text);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .export-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            color: white;
            padding: 15px 30px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 5px 20px rgba(106, 103, 240, 0.3);
        }
        
        .export-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(106, 103, 240, 0.4);
        }
        
        .export-btn:active {
            transform: translateY(-1px);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            box-shadow: 0 5px 20px rgba(46, 204, 113, 0.3);
        }
        
        .btn-success:hover {
            box-shadow: 0 8px 25px rgba(46, 204, 113, 0.4);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            box-shadow: 0 5px 20px rgba(243, 156, 18, 0.3);
        }
        
        .btn-warning:hover {
            box-shadow: 0 8px 25px rgba(243, 156, 18, 0.4);
        }
        
        /* ===== INFORMACIÓN ADICIONAL ===== */
        .info-section {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-top: 30px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: rgba(106, 103, 240, 0.05);
            border-radius: 12px;
        }
        
        .info-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }
        
        .info-content h4 {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .info-content p {
            font-size: 13px;
            color: var(--light-text);
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
            
            .form-body {
                padding: 20px;
            }
            
            .reports-grid {
                grid-template-columns: 1fr;
            }
            
            .section-header {
                flex-direction: column;
                gap: 15px;
            }
            
            .report-stats {
                flex-direction: column;
                gap: 10px;
            }
        }
        
        @media (max-width: 480px) {
            .report-card {
                padding: 30px 20px;
            }
            
            .export-btn {
                width: 100%;
                justify-content: center;
            }
            
            .info-item {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- ===== HEADER UNIFICADO  ===== -->
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
                <a href="formulario1.php" class="nav-link">
                    <i class="fas fa-user-plus"></i> Nuevo Trabajador
                </a>
                <a href="expedientes.php" class="nav-link">  <!-- Ya existe -->
                    <i class="fas fa-folder-open"></i> Expedientes
                </a>
                <a href="reportes.php" class="nav-link active">  <!-- active en Reportes -->
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
        <!-- ===== TARJETA DE REPORTES ===== -->
        <div class="form-card">
            <!-- CABECERA -->
            <div class="form-header">
                <div class="form-icon">
                    <i class="fas fa-file-excel"></i>
                </div>
                <h1>Exportación de Reportes</h1>
                <p>Sistema de Administración Integral de Nómina y Archivo</p>
            </div>
            
            <!-- CONTENIDO -->
            <div class="form-body">
                <!-- SECCIÓN DE REPORTES -->
                <section class="reports-section">
                    <div class="section-header">
                        <div class="section-title">
                            <div class="section-icon">
                                <i class="fas fa-database"></i>
                            </div>
                            <h2>Reportes Disponibles</h2>
                        </div>
                    </div>
                    
                    <div class="reports-grid">
                        <!-- REPORTE DE EMPLEADOS -->
                        <div class="report-card">
                            <div class="report-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="report-content">
                                <h3>Reporte de Empleados</h3>
                                <p>Exporte un listado completo de todos los empleados registrados en el sistema con toda su información personal y laboral.</p>
                                
                                <div class="report-stats">
                                    <?php
                                    $sql_total = "SELECT COUNT(*) as total FROM empleados";
                                    $result_total = $conn->query($sql_total);
                                    $total_empleados = $result_total->fetch_assoc()['total'];
                                    
                                    $sql_activos = "SELECT COUNT(*) as activos FROM empleados WHERE estatus = 'ACTIVO'";
                                    $result_activos = $conn->query($sql_activos);
                                    $activos = $result_activos->fetch_assoc()['activos'];
                                    ?>
                                    <div class="stat-item">
                                        <span class="stat-value"><?php echo $total_empleados; ?></span>
                                        <span class="stat-label">Total</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-value"><?php echo $activos; ?></span>
                                        <span class="stat-label">Activos</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-value">.XLSX</span>
                                        <span class="stat-label">Formato</span>
                                    </div>
                                </div>
                                
                                <a href="../php/excel.php?tipo=empleados" class="export-btn btn-success" target="_blank">
                                    <i class="fas fa-download"></i>
                                    Exportar Empleados
                                </a>
                            </div>
                        </div>
                        
                        <!-- REPORTE DE FAMILIARES -->
                        <div class="report-card">
                            <div class="report-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="report-content">
                                <h3>Reporte de Familiares</h3>
                                <p>Genere un archivo Excel con información de familiares de empleados, incluyendo parentesco y datos de contacto.</p>
                                
                                <div class="report-stats">
                                    <?php
                                    $sql_familiares = "SELECT COUNT(*) as total FROM familiares";
                                    $result_familiares = $conn->query($sql_familiares);
                                    $total_familiares = $result_familiares->fetch_assoc()['total'];
                                    
                                    // Obtener tipos de familiares únicos
                                    $sql_tipos = "SELECT COUNT(DISTINCT parentesco) as tipos FROM familiares";
                                    $result_tipos = $conn->query($sql_tipos);
                                    $tipos_familiares = $result_tipos->fetch_assoc()['tipos'];
                                    ?>
                                    <div class="stat-item">
                                        <span class="stat-value"><?php echo $total_familiares; ?></span>
                                        <span class="stat-label">Registros</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-value"><?php echo $tipos_familiares; ?></span>
                                        <span class="stat-label">Parentescos</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-value">.XLSX</span>
                                        <span class="stat-label">Formato</span>
                                    </div>
                                </div>
                                
                                <a href="../php/excel-familiares.php?tipo=empleados" class="export-btn btn-warning" target="_blank">
                                    <i class="fas fa-download"></i>
                                    Exportar Familiares
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- ===== INFORMACIÓN ADICIONAL ===== -->
                <section class="info-section">
                    <div class="section-header">
                        <div class="section-title">
                            <div class="section-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <h2>Información de Exportación</h2>
                        </div>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-file-excel"></i>
                            </div>
                            <div class="info-content">
                                <h4>Formato Excel</h4>
                                <p>Todos los reportes se generan en formato .XLSX compatible con Microsoft Excel y Google Sheets</p>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="info-content">
                                <h4>Generación Rápida</h4>
                                <p>Los reportes se generan automáticamente en segundos con los datos más actualizados</p>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="info-content">
                                <h4>Seguridad de Datos</h4>
                                <p>Toda la información se exporta de manera segura manteniendo la confidencialidad</p>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-sync-alt"></i>
                            </div>
                            <div class="info-content">
                                <h4>Actualización Automática</h4>
                                <p>Los reportes siempre incluyen la información más reciente del sistema</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    
    <script>
        // Menú móvil
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const navMenu = document.getElementById('nav-menu');
            
            mobileMenuBtn.addEventListener('click', function() {
                navMenu.classList.toggle('active');
            });
            
            // Cerrar menú al hacer clic en un enlace
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    navMenu.classList.remove('active');
                });
            });
            
            // Efecto hover para tarjetas de reporte
            const reportCards = document.querySelectorAll('.report-card');
            reportCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
                
                // Efecto hover para iconos
                const icon = card.querySelector('.report-icon');
                card.addEventListener('mouseenter', function() {
                    icon.style.transform = 'scale(1.1) rotate(5deg)';
                });
                
                card.addEventListener('mouseleave', function() {
                    icon.style.transform = 'scale(1) rotate(0)';
                });
            });
            
            // Efecto para botones de exportación
            const exportBtns = document.querySelectorAll('.export-btn');
            exportBtns.forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px)';
                });
                
                btn.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
                
                btn.addEventListener('click', function(e) {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-check"></i> ¡Reporte Listo!';
                        setTimeout(() => {
                            this.innerHTML = '<i class="fas fa-download"></i> ' + this.textContent.replace('¡Reporte Listo!', 'Exportar');
                        }, 1500);
                    }, 1000);
                });
            });
            
            // Actualizar hora en tiempo real
            function updateTime() {
                const now = new Date();
                const options = { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                };
                // Podrías agregar un elemento para mostrar la hora si lo deseas
                console.log(now.toLocaleDateString('es-ES', options));
            }
            
            updateTime();
            setInterval(updateTime, 60000);
        });
    </script>
    
    <?php if (isset($conn)) { $conn->close(); } ?>
</body>
</html>