<?php
// usuario/trabajadores/buscar.php - BÚSQUEDA AVANZADA
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Trabajadores - Usuario SAINA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ===== VARIABLES GLOBALES ===== */
        :root {
            --purple-start: #a8a0f9;
            --blue-end: #6162f4;
            --primary-color: #6a67f0;
            --text-color: #333;
            --light-text: #777;
            --card-background: rgba(255, 255, 255, 0.95);
            --shadow-light: rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
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
            border-radius: 15px;
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
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        /* ===== CABECERA DEL CONTENIDO ===== */
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
            margin-bottom: 25px;
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
            box-shadow: 0 5px 15px rgba(106, 103, 240, 0.3);
        }
        
        .header-content h1 {
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }
        
        .header-content p {
            color: var(--light-text);
            font-size: 14px;
        }
        
        /* ===== BOTONES ===== */
        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            font-size: 15px;
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
            transform: translateY(-2px);
        }
        
        /* ===== FORMULARIO DE BÚSQUEDA ===== */
        .search-form-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 40px;
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
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
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
        
        .form-control {
            padding: 14px 18px;
            border: 2px solid #e6e6e6;
            border-radius: 12px;
            font-size: 15px;
            transition: var(--transition);
            background: white;
            color: var(--text-color);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(106, 103, 240, 0.2);
        }
        
        .form-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
        }
        
        .search-tips {
            background: rgba(106, 103, 240, 0.05);
            padding: 25px;
            border-radius: 15px;
            margin-top: 30px;
        }
        
        .tips-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .tips-list {
            list-style-type: none;
            padding-left: 0;
        }
        
        .tips-list li {
            padding: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--light-text);
        }
        
        .tips-list li i {
            color: var(--primary-color);
            font-size: 14px;
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
        
        @media (max-width: 480px) {
            .container {
                padding: 20px 15px;
            }
            
            .header {
                padding: 20px;
            }
            
            .search-form-container {
                padding: 25px;
            }
            
            .header-title {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .form-actions .btn {
                padding: 16px;
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
            
            <nav class="nav-menu" id="nav-menu">
                <a href="../index.php" class="nav-link">
                    <i class="fas fa-home"></i> Inicio
                </a>
                <a href="index.php" class="nav-link">
                    <i class="fas fa-history"></i> Gestionar Trabajadores
                </a>
                <a href="buscar.php" class="nav-link active">
                    <i class="fas fa-users"></i> Buscar Trabajadores
                </a>
                <a href="formulario1.php" class="nav-link">
                    <i class="fas fa-user-plus"></i> Nuevo Trabajador
                </a>
                <a href="../php/excel.php" class="nav-link">
                    <i class="fas fa-download"></i> Exportar Excel
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
    <div class="container">
        <!-- CABECERA DEL CONTENIDO -->
        <div class="header">
            <div class="header-title">
                <div class="header-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="header-content">
                    <h1>Búsqueda Avanzada</h1>
                    <p>Encuentra trabajadores usando diferentes criterios de búsqueda</p>
                </div>
            </div>
            
            <div class="header-actions">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Listado
                </a>
                <a href="../index.php" class="btn" style="background: #95a5a6; color: white;">
                    <i class="fas fa-home"></i> Inicio
                </a>
            </div>
        </div>
        
        <!-- FORMULARIO DE BÚSQUEDA -->
        <div class="search-form-container">
            <form method="GET" action="index.php">
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
                            <label for="search"><i class="fas fa-search"></i> Término de Búsqueda</label>
                            <input type="text" id="search" name="search" class="form-control" 
                                   placeholder="Buscar por nombre, apellido o cédula...">
                        </div>
                        
                        <div class="form-group">
                            <label for="nacionalidad"><i class="fas fa-globe"></i> Nacionalidad</label>
                            <select id="nacionalidad" name="nacionalidad" class="form-control">
                                <option value="">Todas las nacionalidades</option>
                                <option value="VENEZOLANO(A)">VENEZOLANO(A)</option>
                                <option value="EXTRANJERO(A)">EXTRANJERO(A)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="sexo"><i class="fas fa-venus-mars"></i> Sexo</label>
                            <select id="sexo" name="sexo" class="form-control">
                                <option value="">Todos</option>
                                <option value="MASCULINO">MASCULINO</option>
                                <option value="FEMENINO">FEMENINO</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="estado_civil"><i class="fas fa-heart"></i> Estado Civil</label>
                            <select id="estado_civil" name="estado_civil" class="form-control">
                                <option value="">Todos</option>
                                <option value="SOLTERO(A)">SOLTERO(A)</option>
                                <option value="CASADO(A)">CASADO(A)</option>
                                <option value="DIVORCIADO(A)">DIVORCIADO(A)</option>
                                <option value="VIUDO(A)">VIUDO(A)</option>
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
                            <label for="tipo_trabajador"><i class="fas fa-user-tie"></i> Tipo de Trabajador</label>
                            <select id="tipo_trabajador" name="tipo_trabajador" class="form-control">
                                <option value="">Todos</option>
                                <option value="CTD">CTD</option>
                                <option value="CTI">CTI</option>
                                <option value="LNR">LNR</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="cargo"><i class="fas fa-briefcase"></i> Cargo</label>
                            <input type="text" id="cargo" name="cargo" class="form-control" 
                                   placeholder="Buscar por cargo...">
                        </div>
                        
                        <div class="form-group">
                            <label for="sede"><i class="fas fa-building"></i> Sede</label>
                            <select id="sede" name="sede" class="form-control">
                                <option value="">Todas las sedes</option>
                                <option value="ADMIN">ADMIN</option>
                                <option value="CAFO">CAFO</option>
                                <option value="CATE">CATE</option>
                                <option value="CSAI">CSAI</option>
                                <option value="CSB">CSB</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="estatus"><i class="fas fa-check-circle"></i> Estatus</label>
                            <select id="estatus" name="status" class="form-control">
                                <option value="">Todos</option>
                                <option value="ACTIVO">ACTIVO</option>
                                <option value="INACTIVO">INACTIVO</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- RANGO DE FECHAS -->
                <div class="form-section">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h2>Rango de Fechas</h2>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="fecha_desde"><i class="fas fa-calendar-plus"></i> Fecha desde</label>
                            <input type="date" id="fecha_desde" name="fecha_desde" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="fecha_hasta"><i class="fas fa-calendar-minus"></i> Fecha hasta</label>
                            <input type="date" id="fecha_hasta" name="fecha_hasta" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="tipo_fecha"><i class="fas fa-filter"></i> Tipo de fecha</label>
                            <select id="tipo_fecha" name="tipo_fecha" class="form-control">
                                <option value="fecha_ingreso">Fecha de ingreso</option>
                                <option value="fecha_nacimiento">Fecha de nacimiento</option>
                                <option value="fecha_registro">Fecha de registro</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- ACCIONES -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" style="padding: 16px 32px;">
                        <i class="fas fa-search"></i> Buscar Trabajadores
                    </button>
                    
                    <button type="reset" class="btn btn-secondary" style="padding: 16px 32px;">
                        <i class="fas fa-redo"></i> Limpiar Formulario
                    </button>
                </div>
            </form>
            
            <!-- CONSEJOS DE BÚSQUEDA -->
            <div class="search-tips">
                <div class="tips-title">
                    <i class="fas fa-lightbulb"></i>
                    <h3>Consejos para una búsqueda efectiva</h3>
                </div>
                <ul class="tips-list">
                    <li><i class="fas fa-check-circle"></i> Usa términos parciales: "mar" encontrará "María" y "Mario"</li>
                    <li><i class="fas fa-check-circle"></i> Para cédulas, usa solo números: "12345678"</li>
                    <li><i class="fas fa-check-circle"></i> Puedes combinar múltiples filtros</li>
                    <li><i class="fas fa-check-circle"></i> Deja campos en blanco para ignorar ese filtro</li>
                    <li><i class="fas fa-check-circle"></i> Usa el botón "Limpiar" para reiniciar la búsqueda</li>
                </ul>
            </div>
        </div>
    </div>
    
    <script>
        // Configurar fechas por defecto
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const oneYearAgo = new Date();
            oneYearAgo.setFullYear(today.getFullYear() - 1);
            
            // Formatear fechas para input type="date"
            function formatDate(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }
            
            // Establecer fecha desde = hace 1 año
            document.getElementById('fecha_desde').value = formatDate(oneYearAgo);
            // Establecer fecha hasta = hoy
            document.getElementById('fecha_hasta').value = formatDate(today);
            
            // Validación de fechas
            const fechaDesde = document.getElementById('fecha_desde');
            const fechaHasta = document.getElementById('fecha_hasta');
            
            fechaDesde.addEventListener('change', function() {
                if (fechaHasta.value && fechaHasta.value < this.value) {
                    fechaHasta.value = this.value;
                }
            });
            
            fechaHasta.addEventListener('change', function() {
                if (fechaDesde.value && fechaDesde.value > this.value) {
                    this.value = fechaDesde.value;
                }
            });
            
            // Auto-enfocar en primer campo
            document.getElementById('search').focus();
            
            // Menú móvil
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const navMenu = document.getElementById('nav-menu');
            
            if (mobileMenuBtn && navMenu) {
                mobileMenuBtn.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                });
                
                // Cerrar menú al hacer clic en un enlace
                const navLinks = navMenu.querySelectorAll('.nav-link');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        navMenu.classList.remove('active');
                    });
                });
            }
        });
    </script>
</body>
</html>