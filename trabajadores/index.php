<?php
// usuario/trabajadores/index.php - LISTADO DE TRABAJADORES (USUARIO)
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: ../index.php");
    exit();
}

// Configuración de paginación
$registros_por_pagina = 10;
$pagina_actual = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Búsqueda y filtros
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_sede = isset($_GET['sede']) ? $_GET['sede'] : '';

// Construir consulta
$where = [];
$params = [];
$types = '';

if ($search) {
    $where[] = "(ci LIKE ? OR primer_nombre LIKE ? OR primer_apellido LIKE ? OR correo LIKE ? OR cargo LIKE ?)";
    $search_term = "%$search%";
    array_push($params, $search_term, $search_term, $search_term, $search_term, $search_term);
    $types .= 'sssss';
}

if ($filter_status && in_array($filter_status, ['ACTIVO', 'INACTIVO'])) {
    $where[] = "estatus = ?";
    $params[] = $filter_status;
    $types .= 's';
}

if ($filter_sede) {
    $where[] = "sede = ?";
    $params[] = $filter_sede;
    $types .= 's';
}

$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Contar total de registros
$sql_count = "SELECT COUNT(*) as total FROM empleados $where_clause";
$stmt_count = $conn->prepare($sql_count);
if ($where) {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_registros = $result_count->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);
$stmt_count->close();

// Obtener sedes únicas para filtro - FORZAR LAS 5 SEDES
$sedes = ['ADMIN', 'CAFO', 'CATE', 'CSAI', 'CSB'];

// Obtener registros paginados
$sql = "SELECT id, ci, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, 
               correo, telefono, sede, cargo, estatus, fecha_ingreso, fecha_registro 
        FROM empleados $where_clause 
        ORDER BY fecha_registro DESC 
        LIMIT ?, ?";

$params[] = $offset;
$params[] = $registros_por_pagina;
$types .= 'ii';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Trabajadores - Usuario SAINA</title>
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
            --success-color: #27ae60;
            --warning-color: #f39c12;
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
            max-width: 1400px;
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
        
        /* ===== BARRA DE BÚSQUEDA Y FILTROS ===== */
        .search-filters {
            background: rgba(106, 103, 240, 0.05);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .search-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
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
        }
        
        .form-control {
            padding: 14px 18px;
            border: 2px solid #e6e6e6;
            border-radius: 12px;
            font-size: 15px;
            transition: var(--transition);
            background: white;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(106, 103, 240, 0.2);
        }
        
        /* ===== BOTONES ===== */
        .btn {
            padding: 14px 28px;
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
        
        .btn-warning {
            background: linear-gradient(135deg, #ffd93d, #ff6b6b);
            color: white;
        }
        
        /* ===== TABLA ===== */
        .table-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 30px;
            overflow-x: auto; /* Esto permite scroll horizontal si la tabla es muy ancha */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px; /* Establece un ancho mínimo para la tabla */
        }

        
        thead {
            background: linear-gradient(90deg, var(--purple-start), var(--blue-end));
        }
        
        th {
            padding: 20px;
            text-align: left;
            color: white;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        tbody tr {
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: var(--transition);
        }
        
        tbody tr:hover {
            background: rgba(106, 103, 240, 0.03);
        }
        
        td {
            padding: 18px 20px;
            font-size: 14px;
        }
        
        /* ===== BADGES ===== */
        .badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-success {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
        }
        
        .badge-warning {
            background: linear-gradient(135deg, #ffd93d, #ff6b6b);
            color: white;
        }
        
       /* ===== ACCIONES ===== */
.actions {
    display: flex;
    gap: 8px;
    flex-wrap: nowrap;
    white-space: nowrap;
    min-width: 180px; /* Aumenté este valor */
}

.btn-action {
    padding: 10px 14px; /* Aumenté el padding */
    border-radius: 8px;
    font-size: 13px; /* Aumenté ligeramente la fuente */
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: var(--transition);
    white-space: nowrap;
    min-width: 75px; /* Ancho mínimo para cada botón */
    justify-content: center;
}

/* Ajustes responsive para la columna de acciones */
@media (max-width: 768px) {
    table {
        min-width: 1000px;
    }
    
    .actions {
        min-width: 160px;
    }
    
    .btn-action {
        padding: 8px 10px;
        font-size: 12px;
        min-width: 70px;
    }
}
        
        /* ===== PAGINACIÓN ===== */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            padding: 20px;
        }
        
        .page-btn {
            padding: 10px 16px;
            border: 2px solid #e6e6e6;
            border-radius: 10px;
            background: white;
            color: var(--text-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .page-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .page-btn.active {
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            color: white;
            border-color: transparent;
        }
        
        /* ===== ESTADO VACÍO ===== */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--light-text);
        }
        
        .empty-state i {
            font-size: 60px;
            margin-bottom: 20px;
            color: #bdc3c7;
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
            
            .search-form {
                grid-template-columns: 1fr;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
            
            .actions {
                flex-direction: column;
            }
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 20px 15px;
            }
            
            .header {
                padding: 20px;
            }
            
            .header-title {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .pagination {
                flex-wrap: wrap;
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
                <a href="index.php" class="nav-link active">
                    <i class="fas fa-history"></i> Gestionar Trabajadores
                </a>
                <a href="buscar.php" class="nav-link">
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
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="header-content">
                    <h1>Gestión de Trabajadores</h1>
                    <p>Consulta todos los empleados del sistema SAINA</p>
                </div>
            </div>
            
            <div class="header-actions">
                <a href="../index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Inicio
                </a>
            </div>
        </div>
        
        <!-- BÚSQUEDA Y FILTROS -->
        <div class="search-filters">
            <form method="GET" class="search-form">
                <div class="form-group">
                    <label><i class="fas fa-search"></i> Buscar</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Buscar por CI, nombre, apellido, correo o cargo..."
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-filter"></i> Estado</label>
                    <select name="status" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="ACTIVO" <?php echo $filter_status == 'ACTIVO' ? 'selected' : ''; ?>>Activos</option>
                        <option value="INACTIVO" <?php echo $filter_status == 'INACTIVO' ? 'selected' : ''; ?>>Inactivos</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-building"></i> Sede</label>
                    <select name="sede" class="form-control">
                        <option value="">Todas las sedes</option>
                        <?php foreach($sedes as $sede): ?>
                            <option value="<?php echo $sede; ?>" <?php echo $filter_sede == $sede ? 'selected' : ''; ?>>
                                <?php echo $sede; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group" style="align-self: end;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
            
            <?php if ($search || $filter_status || $filter_sede): ?>
                <div style="text-align: center;">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Limpiar Filtros
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- TABLA DE TRABAJADORES -->
        <div class="table-container">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Cédula</th>
                            <th>Nombre Completo</th>
                            <th>Cargo</th>
                            <th>Contacto</th>
                            <th>Sede</th>
                            <th>Estado</th>
                            <th>Fecha Ingreso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['ci']); ?></strong></td>
                            <td>
                                <div style="font-weight: 500;">
                                    <?php echo htmlspecialchars($row['primer_nombre'] . ' ' . $row['primer_apellido']); ?>
                                </div>
                                <div style="font-size: 12px; color: var(--light-text);">
                                    <?php echo htmlspecialchars($row['segundo_nombre'] . ' ' . $row['segundo_apellido']); ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($row['cargo']); ?></td>
                            <td>
                                
                                <div style="font-size: 12px; color: var(--light-text);">
                                    <?php echo htmlspecialchars($row['telefono']); ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($row['sede']); ?></td>
                            <td>
                                <span class="badge <?php echo $row['estatus'] == 'ACTIVO' ? 'badge-success' : 'badge-warning'; ?>">
                                    <?php echo $row['estatus']; ?>
                                </span>
                            </td>
                            <td>
                                <?php echo date('d/m/Y', strtotime($row['fecha_ingreso'])); ?>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="ver.php?id=<?php echo $row['id']; ?>" class="btn-action btn-primary">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a href="editar.php?id=<?php echo $row['id']; ?>" class="btn-action btn-warning">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <!-- NOTA: NO HAY BOTÓN ELIMINAR PARA USUARIOS -->
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-users-slash"></i>
                    <h3>No se encontraron trabajadores</h3>
                    <p><?php echo ($search || $filter_status || $filter_sede) ? 'Intenta con otros filtros' : 'No hay trabajadores registrados'; ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- PAGINACIÓN -->
        <?php if ($total_paginas > 1): ?>
            <div class="pagination">
                <?php if ($pagina_actual > 1): ?>
                    <a href="?page=<?php echo $pagina_actual - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $filter_status; ?>&sede=<?php echo $filter_sede; ?>" 
                       class="page-btn">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </a>
                <?php endif; ?>
                
                <?php for($i = 1; $i <= $total_paginas; $i++): ?>
                    <?php if($i == 1 || $i == $total_paginas || ($i >= $pagina_actual - 2 && $i <= $pagina_actual + 2)): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $filter_status; ?>&sede=<?php echo $filter_sede; ?>" 
                           class="page-btn <?php echo $i == $pagina_actual ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php elseif($i == $pagina_actual - 3 || $i == $pagina_actual + 3): ?>
                        <span class="page-btn">...</span>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($pagina_actual < $total_paginas): ?>
                    <a href="?page=<?php echo $pagina_actual + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $filter_status; ?>&sede=<?php echo $filter_sede; ?>" 
                       class="page-btn">
                        Siguiente <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- ESTADÍSTICAS -->
        <div style="text-align: center; color: var(--light-text); font-size: 14px; margin-top: 20px;">
            <i class="fas fa-info-circle"></i> 
            Mostrando <?php echo min($registros_por_pagina, $result->num_rows); ?> de <?php echo $total_registros; ?> trabajadores
            <?php if ($search || $filter_status || $filter_sede): ?>
                (filtrados)
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Auto-enfocar en búsqueda
        document.querySelector('input[name="search"]').focus();
        
        // Efectos visuales
        document.addEventListener('DOMContentLoaded', function() {
            // Animación de entrada para las filas
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                setTimeout(() => {
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 100);
            });
            
            // Establecer estilos iniciales para animación
            rows.forEach(row => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                row.style.transition = 'all 0.5s ease';
            });
            
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

<?php
$stmt->close();
$conn->close();
?>