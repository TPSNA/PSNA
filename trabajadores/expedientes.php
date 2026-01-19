<?php
// usuario/trabajadores/expedientes.php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: ../admin/login.php");
    exit();
}

require_once '../includes/database.php';

// Configuración
$items_por_pagina = 15;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina - 1) * $items_por_pagina;

// Búsqueda
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$condicion_busqueda = '';
$parametros = [];
$tipos = '';

if ($busqueda !== '') {
    $condicion_busqueda = "WHERE (e.ci LIKE ? OR e.primer_nombre LIKE ? OR e.primer_apellido LIKE ?)";
    $parametros = ["%$busqueda%", "%$busqueda%", "%$busqueda%"];
    $tipos = 'sss';
}

// Obtener total de empleados
$sql_total = "SELECT COUNT(DISTINCT e.id) as total 
              FROM empleados e 
              $condicion_busqueda";
$stmt_total = $conn->prepare($sql_total);
if ($tipos) $stmt_total->bind_param($tipos, ...$parametros);
$stmt_total->execute();
$total_empleados = $stmt_total->get_result()->fetch_assoc()['total'];
$stmt_total->close();

// Calcular total de páginas
$total_paginas = ceil($total_empleados / $items_por_pagina);

// Obtener empleados con información de expediente
$sql = "SELECT 
            e.id,
            e.ci,
            e.primer_nombre,
            e.primer_apellido,
            e.segundo_nombre,
            e.segundo_apellido,
            e.cargo,
            e.estatus,
            COUNT(DISTINCT ex.id) as total_documentos,
            SUM(ex.tamaño) as tamaño_total,
            MAX(ex.fecha_subida) as ultima_actualizacion
        FROM empleados e
        LEFT JOIN expedientes ex ON e.id = ex.empleado_id AND ex.estado = 'activo'
        $condicion_busqueda
        GROUP BY e.id
        ORDER BY e.primer_nombre, e.primer_apellido
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
if ($tipos) {
    $parametros[] = $items_por_pagina;
    $parametros[] = $offset;
    $stmt->bind_param($tipos . 'ii', ...$parametros);
} else {
    $stmt->bind_param("ii", $items_por_pagina, $offset);
}
$stmt->execute();
$empleados = $stmt->get_result();

// Tipos de documentos para referencia
$tipos_documentos = [
    'cedula_frontal' => 'Cédula Frontal',
    'cedula_reverso' => 'Cédula Reverso',
    'curriculum' => 'Currículum',
    'titulos' => 'Títulos',
    'certificaciones' => 'Certificaciones',
    'contrato' => 'Contrato',
    'evaluaciones' => 'Evaluaciones',
    'foto_carnet' => 'Foto Carnet',
    'formacion_academica' => 'Formación Académica',
    'experiencia_laboral' => 'Experiencia Laboral',
    'carnet_salud' => 'Carnet de Salud',
    'otros' => 'Otros'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expedientes - SAINA</title>
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
            background: #F0F4F3;
            color: var(--text-color);
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
        
        /* Estilos existentes de expedientes.php */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: white;
            padding: 25px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }
        
        .search-section {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .search-box {
            flex: 1;
            position: relative;
        }
        
        .search-box input {
            width: 100%;
            padding: 12px 45px 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(106, 103, 240, 0.1);
        }
        
        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--light-text);
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
        
        .btn-success {
            background: #28a745;
        }
        
        .empleados-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .empleado-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .empleado-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(106, 103, 240, 0.15);
            border-color: var(--primary-color);
        }
        
        .empleado-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .empleado-avatar {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 20px;
            box-shadow: 0 5px 15px rgba(106, 103, 240, 0.3);
        }
        
        .empleado-info h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--text-color);
        }
        
        .empleado-info p {
            color: var(--light-text);
            font-size: 14px;
        }
        
        .expediente-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
        }
        
        .info-label {
            font-size: 12px;
            color: var(--light-text);
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-color);
        }
        
        .acciones {
            display: flex;
            gap: 10px;
        }
        
        .accion-btn {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-ver {
            background: rgba(106, 103, 240, 0.1);
            color: var(--primary-color);
        }
        
        .btn-ver:hover {
            background: rgba(106, 103, 240, 0.2);
        }
        
        .btn-agregar {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .btn-agregar:hover {
            background: rgba(40, 167, 69, 0.2);
        }
        
        .estatus-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .estatus-activo {
            background: rgba(67, 233, 123, 0.1);
            color: #27ae60;
        }
        
        .estatus-inactivo {
            background: rgba(255, 107, 107, 0.1);
            color: #e74c3c;
        }
        
        .paginacion {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 30px;
        }
        
        .pagina-link {
            padding: 8px 15px;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-color);
            transition: all 0.3s ease;
        }
        
        .pagina-link:hover {
            background: #f8f9fa;
        }
        
        .pagina-activa {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .sin-resultados {
            grid-column: 1 / -1;
            text-align: center;
            padding: 50px;
            color: var(--light-text);
        }
        
        .sin-expediente {
            background: #fff9e6;
            border-left: 4px solid #ffc107;
        }
        
        @media (max-width: 768px) {
            .empleados-grid {
                grid-template-columns: 1fr;
            }
            
            .search-section {
                flex-direction: column;
            }
            
            .expediente-info {
                grid-template-columns: 1fr;
            }
            
            /* Responsive para header */
            .mobile-menu-btn {
                display: block;
            }
            
            .nav-menu {
                position: fixed;
                top: 80px;
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
                <a href="buscar.php" class="nav-link">
                    <i class="fas fa-users"></i> Buscar Trabajadores
                </a>
                <a href="formulario1.php" class="nav-link">
                    <i class="fas fa-user-plus"></i> Nuevo Trabajador
                </a>
                <a href="expedientes.php" class="nav-link active">
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
    
    <!-- CONTENIDO EXISTENTE DE EXPEDIENTES -->
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-folder-open"></i> Expedientes Digitales</h1>
            <p>Gestiona los expedientes completos de todos los trabajadores</p>
        </div>
        
        <div class="search-section">
            <div class="search-box">
                <input type="text" id="busqueda" 
                       placeholder="🔍 Buscar por cédula, nombre o apellido..." 
                       value="<?php echo htmlspecialchars($busqueda); ?>"
                       onkeyup="if(event.key === 'Enter') buscar()">
                <span class="search-icon"><i class="fas fa-search"></i></span>
            </div>
            <button onclick="buscar()" class="btn">
                <i class="fas fa-search"></i> Buscar
            </button>
        </div>
        
        <div class="empleados-grid">
            <?php if ($empleados->num_rows > 0): ?>
                <?php while($empleado = $empleados->fetch_assoc()): 
                    $nombre_completo = htmlspecialchars($empleado['primer_nombre'] . ' ' . $empleado['primer_apellido']);
                    if ($empleado['segundo_nombre']) $nombre_completo .= ' ' . htmlspecialchars($empleado['segundo_nombre']);
                    if ($empleado['segundo_apellido']) $nombre_completo .= ' ' . htmlspecialchars($empleado['segundo_apellido']);
                    
                    $inicial = strtoupper(substr($empleado['primer_nombre'], 0, 1));
                    $tiene_expediente = $empleado['total_documentos'] > 0;
                    $card_class = $tiene_expediente ? 'empleado-card' : 'empleado-card sin-expediente';
                ?>
                <div class="<?php echo $card_class; ?>">
                    <div class="empleado-header">
                        <div class="empleado-avatar">
                            <?php echo $inicial; ?>
                        </div>
                        <div class="empleado-info">
                            <h3><?php echo $nombre_completo; ?></h3>
                            <p>
                                <strong>Cédula:</strong> <?php echo htmlspecialchars($empleado['ci']); ?>
                                <span class="estatus-badge estatus-<?php echo strtolower($empleado['estatus']); ?>">
                                    <?php echo $empleado['estatus']; ?>
                                </span>
                            </p>
                            <p><strong>Cargo:</strong> <?php echo htmlspecialchars($empleado['cargo']); ?></p>
                        </div>
                    </div>
                    
                    <div class="expediente-info">
                        <div class="info-item">
                            <div class="info-label">Documentos</div>
                            <div class="info-value"><?php echo $empleado['total_documentos']; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tamaño total</div>
                            <div class="info-value">
                                <?php echo $empleado['tamaño_total'] ? round($empleado['tamaño_total'] / 1024, 1) . ' KB' : '0 KB'; ?>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Última actualización</div>
                            <div class="info-value">
                                <?php echo $empleado['ultima_actualizacion'] ? date('d/m/Y', strtotime($empleado['ultima_actualizacion'])) : 'Nunca'; ?>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Estado</div>
                            <div class="info-value">
                                <?php echo $tiene_expediente ? '<span style="color:#27ae60">✓ Completo</span>' : '<span style="color:#e74c3c">✗ Pendiente</span>'; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="acciones">
                        <a href="../php/ver_expediente.php?id=<?php echo $empleado['id']; ?>" class="accion-btn btn-ver">
                            <i class="fas fa-eye"></i> Ver Expediente
                        </a>
                        <a href="../php/nuevo_expediente.php?id=<?php echo $empleado['id']; ?>" class="accion-btn btn-agregar">
                            <i class="fas fa-plus"></i> Agregar Doc
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="sin-resultados">
                    <i class="fas fa-folder-open" style="font-size: 48px; margin-bottom: 20px; color: #ddd;"></i>
                    <h3>No se encontraron empleados</h3>
                    <p><?php echo $busqueda ? 'Intenta con otros términos de búsqueda' : 'No hay empleados registrados'; ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if ($total_paginas > 1): ?>
        <div class="paginacion">
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?pagina=<?php echo $i; ?><?php echo $busqueda ? '&busqueda=' . urlencode($busqueda) : ''; ?>" 
                   class="pagina-link <?php echo $i == $pagina ? 'pagina-activa' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
        function buscar() {
            const busqueda = document.getElementById('busqueda').value;
            window.location.href = 'expedientes.php?busqueda=' + encodeURIComponent(busqueda);
        }
        
        // Buscar al presionar Enter
        document.getElementById('busqueda').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                buscar();
            }
        });
        
        // Menú móvil
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const navMenu = document.getElementById('nav-menu');
            navMenu.classList.toggle('active');
        });
        
        // Cerrar menú al hacer clic fuera
        document.addEventListener('click', function(event) {
            const navMenu = document.getElementById('nav-menu');
            const mobileBtn = document.getElementById('mobile-menu-btn');
            
            if (!navMenu.contains(event.target) && !mobileBtn.contains(event.target) && navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
            }
        });
    </script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>