
<?php
// admin/index.php - DASHBOARD HERMOSO Y MODERNO
session_start();

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'includes/database.php';
// Obtener estadísticas
$sql_empleados = "SELECT COUNT(*) as total FROM empleados";
$result_empleados = $conn->query($sql_empleados);
$total_empleados = $result_empleados->fetch_assoc()['total'];

$sql_activos = "SELECT COUNT(*) as activos FROM empleados WHERE estatus = 'ACTIVO'";
$result_activos = $conn->query($sql_activos);
$empleados_activos = $result_activos->fetch_assoc()['activos'];

$empleados_inactivos = $total_empleados - $empleados_activos;

// Obtener usuarios
$sql_usuarios = "SELECT COUNT(*) as total FROM usuarios";
$result_usuarios = $conn->query($sql_usuarios);
$total_usuarios = $result_usuarios->fetch_assoc()['total'];

// Actividad reciente
$sql_recientes = "SELECT primer_nombre, primer_apellido, fecha_registro, estatus 
                 FROM empleados ORDER BY fecha_registro DESC LIMIT 5";
$result_recientes = $conn->query($sql_recientes);

// Definir rutas
$ruta_formulario = '../../html/formulario1.php';
$ruta_tabla = '../../html/tabla_datos.php';
$ruta_excel = '../../php/excel.php';
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SAINA</title>
    <!-- Favicon agregado  -->
    <link rel="icon" type="image/png" sizes="32x32" href="../imagenes/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="../imagenes/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="../imagenes/favicon.icoo">
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
            --sidebar-width: 280px;
            --header-height: 70px;
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
            overflow-x: hidden;
        }
        
        .app-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* SIDEBAR */
.sidebar {
    width: var(--sidebar-width);
    background: white;
    box-shadow: 5px 0 20px rgba(0, 0, 0, 0.05);
    position: fixed;
    height: 100vh;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    transition: var(--transition);
}

.sidebar-header {
    padding: 0px 20px;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

/* ESTILOS DEL LOGO CON IMAGEN */
.logo {
    display: flex;
    align-items: center;
    gap: 15px;
}

.logo-container {
    display: flex;
    align-items: center;
    gap: 15px;
    width: 100%;
}

.logo-container img {
    height: 150px; 
    width: auto;
    max-width: 100%;
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
        .user-profile {
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 18px;
            box-shadow: 0 5px 15px rgba(106, 103, 240, 0.3);
        }
        
        .user-info h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .user-role {
            font-size: 12px;
            color: var(--light-text);
            background: rgba(106, 103, 240, 0.1);
            padding: 3px 10px;
            border-radius: 15px;
            font-weight: 500;
        }
        
        .nav-menu {
            padding: 20px 0;
            flex: 1;
            overflow-y: auto;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 25px;
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            margin: 5px 0;
        }
        
        .nav-item:hover {
            background: rgba(106, 103, 240, 0.05);
            color: var(--primary-color);
            border-left: 3px solid var(--primary-color);
        }
        
        .nav-item.active {
            background: linear-gradient(90deg, rgba(106, 103, 240, 0.1), transparent);
            color: var(--primary-color);
            border-left: 3px solid var(--primary-color);
        }
        
        .nav-icon {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }
        
        .nav-text {
            font-size: 14px;
            font-weight: 500;
            flex: 1;
        }
        
        .nav-badge {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 600;
        }
        
        .logout-section {
            padding: 20px 25px;
            border-top: 1px solid rgba(0,0,0,0.05);
        }
        
        .logout-btn {
            display: flex;
            align-items: center;
            gap: 15px;
            color: #ff6b6b;
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background: rgba(255, 107, 107, 0.1);
        }
        
        /* ===== CONTENIDO PRINCIPAL ===== */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 30px;
            min-height: 100vh;
        }
        
        .top-header {
            background: white;
            padding: 25px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: fadeIn 0.5s ease;
        }
        
        .header-title h1 {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }
        
        .header-subtitle {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: var(--light-text);
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            background: #43e97b;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .date-display {
            background: rgba(106, 103, 240, 0.1);
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        /* ===== ESTADÍSTICAS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
            animation: fadeIn 0.7s ease;
        }
        
        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(106, 103, 240, 0.15);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--purple-start), var(--blue-end));
        }
        
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, rgba(106, 103, 240, 0.1), rgba(168, 160, 249, 0.1));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: var(--primary-color);
        }
        
        .stat-trend {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            color: var(--light-text);
        }
        
        .stat-content h3 {
            font-size: 36px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }
        
        .stat-content p {
            color: var(--light-text);
            font-size: 14px;
        }
        
        /* ===== ACCIONES RÁPIDAS ===== */
        .quick-actions-section {
            animation: fadeIn 0.9s ease;
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
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
        }
        
        .action-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            text-decoration: none;
            color: var(--text-color);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.4s ease;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .action-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(106, 103, 240, 0.2);
            border-color: var(--primary-color);
        }
        
        .action-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, transparent, rgba(106, 103, 240, 0.03));
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .action-card:hover::after {
            opacity: 1;
        }
        
        .action-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            color: white;
            font-size: 24px;
            transition: all 0.3s ease;
        }
        
        .action-card:hover .action-icon {
            transform: scale(1.1) rotate(5deg);
        }
        
        .action-content h3 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--text-color);
        }
        
        .action-content p {
            color: var(--light-text);
            font-size: 14px;
            line-height: 1.6;
        }
        
        /* ===== ACTIVIDAD RECIENTE ===== */
        .recent-activity-section {
            margin-top: 50px;
            animation: fadeIn 1.1s ease;
        }
        
        .activity-list {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .activity-item {
            padding: 25px 30px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
        }
        
        .activity-item:hover {
            background: rgba(106, 103, 240, 0.03);
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, rgba(106, 103, 240, 0.1), rgba(168, 160, 249, 0.1));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 18px;
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-title {
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 16px;
        }
        
        .activity-time {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--light-text);
            font-size: 13px;
        }
        
        .activity-status {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .status-active {
            background: rgba(67, 233, 123, 0.1);
            color: #27ae60;
        }
        
        .status-inactive {
            background: rgba(255, 107, 107, 0.1);
            color: #e74c3c;
        }
        
        /* ===== ANIMACIONES ===== */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }
        
        /* ===== EFECTOS DE FONDO ===== */
        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }
        
        .shape {
            position: absolute;
            background: linear-gradient(135deg, var(--purple-start), transparent);
            border-radius: 50%;
            opacity: 0.1;
        }
        
        .shape-1 {
            width: 300px;
            height: 300px;
            top: -150px;
            right: -150px;
        }
        
        .shape-2 {
            width: 200px;
            height: 200px;
            bottom: -100px;
            left: -100px;
        }
        
        /* ===== RESPONSIVE ===== */
        @media (max-width: 1200px) {
            .sidebar {
                width: 80px;
                overflow: visible;
            }
            
            .logo-text, .user-info, .nav-text, .logout-btn .nav-text {
                display: none;
            }
            
            .logo {
                justify-content: center;
                padding: 0;
            }
            
            .user-profile {
                justify-content: center;
                padding: 20px 0;
            }
            
            .user-avatar {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
            
            .nav-item {
                justify-content: center;
                padding: 20px 0;
            }
            
            .nav-badge {
                position: absolute;
                top: 10px;
                right: 10px;
                font-size: 8px;
            }
            
            .main-content {
                margin-left: 80px;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                padding: 20px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .actions-grid {
                grid-template-columns: 1fr;
            }
            
            .top-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .section-header {
                flex-direction: column;
                gap: 15px;
            }
        }
        
        @media (max-width: 480px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .menu-toggle {
                display: block;
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1001;
                background: var(--primary-color);
                color: white;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                border: none;
                font-size: 20px;
                cursor: pointer;
                box-shadow: 0 5px 15px rgba(106, 103, 240, 0.3);
            }
        }
    </style>
</head>
<body>
    <!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <!-- Logo con imagen usando la clase correcta -->
            <div class="logo-container">
                <img src="../imagenes/Logo SAINA Horizontal.png" alt="Logo SAINA">
            </div>
        </div>
    </div>
        
        <!-- PERFIL DE USUARIO -->
    <div class="user-profile">
        <div class="user-avatar">
            <?php 
                $inicial = strtoupper(substr($_SESSION['username'], 0, 1));
                echo $inicial;
            ?>
        </div>
        <div class="user-info">
            <h3><?php echo htmlspecialchars($_SESSION['username']); ?></h3>
            <span class="user-role">Administrador</span>
        </div>
    </div>
        
        <!-- MENÚ DE NAVEGACIÓN -->
        <nav class="nav-menu">
            <a href="index.php" class="nav-item active">
                <div class="nav-icon"><i class="fas fa-home"></i></div>
                <div class="nav-text">Inicio</div>
            </a>
            
            <a href="usuarios/listar.php" class="nav-item">
                <div class="nav-icon"><i class="fas fa-users-cog"></i></div>
                <div class="nav-text">Gestión de Usuarios</div>
                <span class="nav-badge">Admin</span>
            </a>
            
            <a href="trabajadores/index.php" class="nav-item">
                 <div class="nav-icon"><i class="fas fa-user-tie"></i></div>
                  <div class="nav-text">Gestionar Trabajadores</div>
            </a>
            
            
            
        </nav>
        

        <!-- CERRAR SESIÓN -->
        <div class="logout-section">
            <a href="logout.php" class="logout-btn">
                <div class="nav-icon"><i class="fas fa-sign-out-alt"></i></div>
                <div class="nav-text">Cerrar Sesión</div>
            </a>
        </div>
    </aside>
    
    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">
        <!-- HEADER SUPERIOR -->
        <header class="top-header fade-in">
            <div class="header-title">
                <h1>
                    <i class="fas fa-tachometer-alt"></i>
                    Panel de Control
                </h1>
                <div class="header-subtitle">
                    <span class="status-dot status-online"></span>
                    <span>Sistema en línea</span>
                    <span>•</span>
                    <span>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
            </div>
            
            <div class="header-actions">
                <div class="date-display">
                    <i class="far fa-calendar"></i>
                    <?php echo date('d/m/Y'); ?>
                </div>
            </div>
        </header>
        
        <!-- ESTADÍSTICAS -->
        <section class="stats-grid fade-in">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="stat-trend">
                        <i class="fas fa-chart-line"></i>
                        <span>Total</span>
                    </div>
                </div>
                <div class="stat-content">
                    <h3><?php echo $total_empleados; ?></h3>
                    <p>Empleados Registrados</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-up"></i>
                        <span>Activos</span>
                    </div>
                </div>
                <div class="stat-content">
                    <h3><?php echo $empleados_activos; ?></h3>
                    <p>Empleados Activos</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="stat-trend">
                        <i class="fas fa-chart-bar"></i>
                        <span>Inactivos</span>
                    </div>
                </div>
                <div class="stat-content">
                    <h3><?php echo $empleados_inactivos; ?></h3>
                    <p>Empleados Inactivos</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-trend">
                        <i class="fas fa-user-shield"></i>
                        <span>Usuarios</span>
                    </div>
                </div>
                <div class="stat-content">
                    <h3><?php echo $total_usuarios; ?></h3>
                    <p>Usuarios del Sistema</p>
                </div>
            </div>
        </section>
        
        <!-- ACCIONES RÁPIDAS -->
        <section class="quick-actions-section fade-in">
            <div class="section-header">
                <div class="section-title">
                    <div class="section-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h2>Acciones Rápidas</h2>
                </div>
            </div>
            
            <div class="actions-grid">
                <!-- NUEVO USUARIO -->
                <a href="usuarios/registrar.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="action-content">
                        <h3>Nuevo Usuario</h3>
                        <p>Crear una nueva cuenta de usuario en el sistema</p>
                    </div>
                </a>
                
                <!-- GESTIONAR USUARIOS -->
                <a href="usuarios/listar.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div class="action-content">
                        <h3>Gestionar Usuarios</h3>
                        <p>Ver, editar o eliminar usuarios del sistema</p>
                    </div>
                </a>
                
                
                <!-- EXPORTAR EXCEL -->
                <a href="<?php echo $ruta_excel; ?>" class="action-card" target="_blank">
                    <div class="action-icon">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="action-content">
                        <h3>Exportar Excel</h3>
                        <p>Descargar reporte completo en formato Excel</p>
                    </div>
                </a>
                
                <a href="trabajadores/index.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-users-cog"></i>
                     </div>
                     <div class="action-content">
                        <h3>Gestionar Trabajadores</h3>
                        <p>Ver, editar o eliminar empleados registrados</p>
                    </div>
                </a>
            </div>
        </section>
        
        <!-- ACTIVIDAD RECIENTE -->
        <section class="recent-activity-section fade-in">
            <div class="section-header">
                <div class="section-title">
                    <div class="section-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h2>Actividad Reciente</h2>
                </div>
            </div>
            
            <ul class="activity-list">
                <?php if ($result_recientes && $result_recientes->num_rows > 0): ?>
                    <?php while($row = $result_recientes->fetch_assoc()): ?>
                    <li class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">
                                <?php echo htmlspecialchars($row['primer_nombre'] . ' ' . $row['primer_apellido']); ?>
                            </div>
                            <div class="activity-time">
                                <i class="far fa-clock"></i>
                                Registrado el <?php echo date('d/m/Y H:i', strtotime($row['fecha_registro'])); ?>
                                <span class="activity-status <?php echo $row['estatus'] == 'ACTIVO' ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $row['estatus']; ?>
                                </span>
                            </div>
                        </div>
                    </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">No hay actividad reciente</div>
                            <div class="activity-time">Comienza registrando trabajadores para ver actividad aquí</div>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </section>
    </main>
    
    <script>
        // Menú móvil
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
        
        // Efectos al cargar
        document.addEventListener('DOMContentLoaded', function() {
            // Animación para las tarjetas de estadísticas
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
            
            // Efecto hover mejorado para action cards
            const actionCards = document.querySelectorAll('.action-card');
            actionCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    const icon = this.querySelector('.action-icon');
                    icon.style.transform = 'scale(1.1) rotate(5deg)';
                });
                
                card.addEventListener('mouseleave', function() {
                    const icon = this.querySelector('.action-icon');
                    icon.style.transform = 'scale(1) rotate(0)';
                });
            });
            
            // Actualizar hora en tiempo real
            function updateTime() {
                const now = new Date();
                const dateElement = document.querySelector('.date-display');
                if (dateElement) {
                    const options = { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    };
                    dateElement.innerHTML = `<i class="far fa-calendar"></i> ${now.toLocaleDateString('es-ES', options)}`;
                }
            }
            
            // Actualizar cada minuto
            updateTime();
            setInterval(updateTime, 60000);
            
            // Verificar rutas
            const links = document.querySelectorAll('a[href*="formulario1.php"], a[href*="tabla_datos.php"]');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    console.log(`Navegando a: ${this.href}`);
                    // Aquí podrías agregar verificación de existencia del archivo
                });
            });
        });
        
        // Efecto de partículas (opcional)
        function createParticles() {
            const shapes = document.querySelector('.floating-shapes');
            if (shapes) {
                for (let i = 0; i < 5; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'shape';
                    particle.style.width = Math.random() * 50 + 20 + 'px';
                    particle.style.height = particle.style.width;
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.top = Math.random() * 100 + '%';
                    particle.style.opacity = Math.random() * 0.05 + 0.02;
                    particle.style.background = `linear-gradient(135deg, 
                        rgba(${Math.random() * 100 + 155}, ${Math.random() * 100 + 155}, ${Math.random() * 100 + 255}, 0.5),
                        transparent)`;
                    particle.style.animation = `float ${Math.random() * 20 + 10}s infinite ease-in-out`;
                    shapes.appendChild(particle);
                }
                
                // Agregar animación de flotación
                const style = document.createElement('style');
                style.textContent = `
                    @keyframes float {
                        0%, 100% { transform: translate(0, 0) rotate(0deg); }
                        25% { transform: translate(${Math.random() * 20 - 10}px, ${Math.random() * 20 - 10}px) rotate(${Math.random() * 5}deg); }
                        50% { transform: translate(${Math.random() * 20 - 10}px, ${Math.random() * 20 - 10}px) rotate(${Math.random() * 5}deg); }
                        75% { transform: translate(${Math.random() * 20 - 10}px, ${Math.random() * 20 - 10}px) rotate(${Math.random() * 5}deg); }
                    }
                `;
                document.head.appendChild(style);
            }
        }
        
        // Iniciar partículas después de cargar
        setTimeout(createParticles, 1000);
    </script>
</body>
</html>

<?php
if (isset($conn)) {
    $conn->close();
}
?>