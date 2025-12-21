<?php
// admin/trabajadores/index.php - LISTADO COMPLETO DE TRABAJADORES
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
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
    $where[] = "(ci LIKE ? OR primer_nombre LIKE ? OR primer_apellido LIKE ? OR correo LIKE ?)";
    $search_term = "%$search%";
    array_push($params, $search_term, $search_term, $search_term, $search_term);
    $types .= 'ssss';
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

// Obtener sedes únicas para filtro
$sql_sedes = "SELECT DISTINCT sede FROM empleados WHERE sede IS NOT NULL AND sede != '' ORDER BY sede";
$result_sedes = $conn->query($sql_sedes);
$sedes = [];
while($row = $result_sedes->fetch_assoc()) {
    $sedes[] = $row['sede'];
}

// Obtener registros paginados
$sql = "SELECT id, ci, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, 
               correo, telefono, sede, estatus, fecha_ingreso, fecha_registro 
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
    <title>Gestión de Trabajadores - SAINA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --purple-start: #a8a0f9;
            --blue-end: #6162f4;
            --primary-color: #6a67f0;
            --text-color: #333;
            --light-text: #777;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --card-background: rgba(255, 255, 255, 0.95);
            --shadow-light: rgba(0, 0, 0, 0.1);
            --shadow-focus: rgba(106, 103, 240, 0.2);
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
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* HEADER */
        .header {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            animation: fadeIn 0.5s ease;
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
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }
        
        .header-content p {
            color: var(--light-text);
            font-size: 14px;
        }
        
        /* BARRA DE BÚSQUEDA Y FILTROS */
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
            transition: all 0.3s;
            background: white;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px var(--shadow-focus);
        }
        
        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
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
        }
        
        .btn-success {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ffd93d, #ff6b6b);
            color: white;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #c0392b);
            color: white;
        }
        
        /* TABLA */
        .table-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 30px;
            animation: fadeIn 0.7s ease;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
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
            transition: all 0.3s;
        }
        
        tbody tr:hover {
            background: rgba(106, 103, 240, 0.03);
        }
        
        td {
            padding: 18px 20px;
            font-size: 14px;
        }
        
        /* BADGES */
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
        
        .badge-primary {
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            color: white;
        }
        
        /* ACCIONES */
        .actions {
            display: flex;
            gap: 8px;
        }
        
        .btn-action {
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        /* PAGINACIÓN */
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
            transition: all 0.3s;
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
        
        /* ESTADO VACÍO */
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
        
        /* ANIMACIONES */
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
        
        /* RESPONSIVE */
        @media (max-width: 768px) {
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
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <div class="header-title">
                <div class="header-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="header-content">
                    <h1>Gestión de Trabajadores</h1>
                    <p>Administra todos los empleados del sistema SAINA</p>
                </div>
            </div>
            
            <div class="header-actions">
                <a href="registrar.php" class="btn btn-success">
                    <i class="fas fa-user-plus"></i> Nuevo Trabajador
                </a>
                <a href="../index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
        </div>
        
        <!-- BÚSQUEDA Y FILTROS -->
        <div class="search-filters">
            <form method="GET" class="search-form">
                <div class="form-group">
                    <label><i class="fas fa-search"></i> Buscar</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Buscar por CI, nombre, apellido o correo..."
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
                            <th>ID</th>
                            <th>Cédula</th>
                            <th>Nombre Completo</th>
                            <th>Contacto</th>
                            <th>Sede</th>
                            <th>Estado</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['ci']); ?></strong></td>
                            <td>
                                <div style="font-weight: 500;">
                                    <?php echo htmlspecialchars($row['primer_nombre'] . ' ' . $row['primer_apellido']); ?>
                                </div>
                                <div style="font-size: 12px; color: var(--light-text);">
                                    <?php echo htmlspecialchars($row['segundo_nombre'] . ' ' . $row['segundo_apellido']); ?>
                                </div>
                            </td>
                            <td>
                                <div><?php echo htmlspecialchars($row['correo']); ?></div>
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
                                <div style="font-size: 12px;">
                                    <?php echo date('d/m/Y', strtotime($row['fecha_ingreso'])); ?>
                                </div>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="ver.php?id=<?php echo $row['id']; ?>" class="btn-action btn-primary">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a href="editar.php?id=<?php echo $row['id']; ?>" class="btn-action btn-warning">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="eliminar.php?id=<?php echo $row['id']; ?>&ci=<?php echo urlencode($row['ci']); ?>" 
                                       class="btn-action btn-danger"
                                       onclick="return confirm('¿Eliminar trabajador <?php echo addslashes($row['primer_nombre'] . ' ' . $row['primer_apellido']); ?>?')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
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
                    <p><?php echo ($search || $filter_status || $filter_sede) ? 'Intenta con otros filtros' : 'Comienza registrando el primer trabajador'; ?></p>
                    <a href="registrar.php" class="btn btn-success" style="margin-top: 20px;">
                        <i class="fas fa-user-plus"></i> Registrar Primer Trabajador
                    </a>
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
        // Confirmación mejorada para eliminar
        document.querySelectorAll('.btn-danger').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!confirm('⚠️ ¿ESTÁS SEGURO DE ELIMINAR ESTE TRABAJADOR?\n\nEsta acción eliminará todos los datos del trabajador y NO se puede deshacer.')) {
                    e.preventDefault();
                }
            });
        });
        
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
            
            // Efecto hover en botones de acción
            const actionBtns = document.querySelectorAll('.btn-action');
            actionBtns.forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px) scale(1.05)';
                });
                
                btn.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
        
        // Auto-enfocar en búsqueda
        document.querySelector('input[name="search"]').focus();
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>