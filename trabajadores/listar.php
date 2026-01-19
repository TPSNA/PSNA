<?php
// admin/trabajadores/listar.php
require_once '../includes/database.php';
session_start();

// Verificar que sea administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Manejar mensajes
$mensaje = '';
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje'];
}

// Configuración de paginación
$registros_por_pagina = 15;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Parámetros de búsqueda
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'ci';
$estatus = isset($_GET['estatus']) ? $_GET['estatus'] : '';

// Construir WHERE dinámico
$where_conditions = [];
$params = [];
$types = '';

if (!empty($busqueda)) {
    $where_conditions[] = "$filtro LIKE ?";
    $params[] = "%$busqueda%";
    $types .= 's';
}

if (!empty($estatus)) {
    $where_conditions[] = "estatus = ?";
    $params[] = $estatus;
    $types .= 's';
}

$where_sql = '';
if (!empty($where_conditions)) {
    $where_sql = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Contar total de registros
$sql_total = "SELECT COUNT(*) as total FROM empleados $where_sql";
$stmt_total = $conn->prepare($sql_total);
if (!empty($params)) {
    $stmt_total->bind_param($types, ...$params);
}
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_registros = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);
$stmt_total->close();

// Obtener empleados con paginación
$sql = "SELECT * FROM empleados $where_sql ORDER BY fecha_registro DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$types_pag = $types . 'ii';
$params_pag = array_merge($params, [$offset, $registros_por_pagina]);

if (!empty($params_pag)) {
    $stmt->bind_param($types_pag, ...$params_pag);
}
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Trabajadores - SAINA Admin</title>
    <!-- Favicon agregado  -->
    <link rel="icon" type="image/png" sizes="32x32" href="../../imagenes/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="../../imagenes/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="../../imagenes/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: #f5f7fa;
            color: #333;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #2c3e50;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .filters {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .filter-group {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        
        .filter-item {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-item label {
            display: block;
            margin-bottom: 5px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 14px;
        }
        
        .filter-item input,
        .filter-item select {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            background: #f8f9fa;
        }
        
        .filter-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            font-size: 14px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
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
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
        }
        
        .message {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            overflow: auto;
            margin-bottom: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }
        
        thead {
            background: linear-gradient(90deg, #2c3e50, #4a6491);
            color: white;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #f1f1f1;
            font-size: 14px;
        }
        
        th {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 13px;
        }
        
        tbody tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-active {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
        }
        
        .badge-inactive {
            background: linear-gradient(135deg, #ffd93d, #ff6b6b);
            color: white;
        }
        
        .badge-ctd {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            color: white;
        }
        
        .badge-cti {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .badge-lnr {
            background: linear-gradient(135deg, #fa709a, #fee140);
            color: white;
        }
        
        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .actions .btn {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .photo-thumbnail {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e0e0e0;
        }
        
        .photo-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #f1f1f1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #95a5a6;
            font-size: 20px;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
        }
        
        .pagination a, .pagination span {
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
            color: #333;
            background: white;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
        }
        
        .pagination a:hover {
            background: #4facfe;
            color: white;
            border-color: #4facfe;
        }
        
        .pagination .current {
            background: #4facfe;
            color: white;
            border-color: #4facfe;
        }
        
        .stats-bar {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .stat-item {
            background: white;
            padding: 10px 15px;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }
        
        .stat-item i {
            color: #4facfe;
        }
        
        .stat-item .number {
            font-weight: 700;
            color: #2c3e50;
        }
        
        .empty-state {
            text-align: center;
            padding: 50px;
            color: #7f8c8d;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #bdc3c7;
        }
        
        @media (max-width: 768px) {
            .filter-group {
                flex-direction: column;
            }
            
            .filter-item {
                min-width: 100%;
            }
            
            .filter-buttons {
                flex-direction: column;
            }
            
            .table-container {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-tie"></i> Gestión de Trabajadores</h1>
            <p>Administra los registros de empleados del sistema</p>
            
            <div class="stats-bar">
                <div class="stat-item">
                    <i class="fas fa-users"></i>
                    <span>Total: <span class="number"><?php echo $total_registros; ?></span></span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-user-check"></i>
                    <span>Mostrando: <span class="number"><?php echo min($registros_por_pagina, $resultado->num_rows); ?></span></span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-file-export"></i>
                    <a href="../php/excel.php" style="color: #4facfe; text-decoration: none;">
                        Exportar a Excel
                    </a>
                </div>
            </div>
        </div>
        
        <?php if (!empty($mensaje)): ?>
            <div class="message">
                <i class="fas fa-info-circle"></i> <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <div class="filters">
            <form method="GET" action="">
                <div class="filter-group">
                    <div class="filter-item">
                        <label for="busqueda">Buscar</label>
                        <input type="text" id="busqueda" name="busqueda" 
                               placeholder="Ingrese término de búsqueda"
                               value="<?php echo htmlspecialchars($busqueda); ?>">
                    </div>
                    
                    <div class="filter-item">
                        <label for="filtro">Buscar por</label>
                        <select id="filtro" name="filtro">
                            <option value="ci" <?php echo $filtro == 'ci' ? 'selected' : ''; ?>>Cédula</option>
                            <option value="primer_nombre" <?php echo $filtro == 'primer_nombre' ? 'selected' : ''; ?>>Nombre</option>
                            <option value="primer_apellido" <?php echo $filtro == 'primer_apellido' ? 'selected' : ''; ?>>Apellido</option>
                            <option value="correo" <?php echo $filtro == 'correo' ? 'selected' : ''; ?>>Correo</option>
                            <option value="cargo" <?php echo $filtro == 'cargo' ? 'selected' : ''; ?>>Cargo</option>
                        </select>
                    </div>
                    
                    <div class="filter-item">
                        <label for="estatus">Estatus</label>
                        <select id="estatus" name="estatus">
                            <option value="">Todos los estatus</option>
                            <option value="activo" <?php echo $estatus == 'activo' ? 'selected' : ''; ?>>Activo</option>
                            <option value="inactivo" <?php echo $estatus == 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>
                </div>
                
                <div class="filter-buttons">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <a href="listar.php" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Limpiar
                    </a>
                    <a href="../html/formulario1.php" class="btn btn-success">
                        <i class="fas fa-user-plus"></i> Nuevo Trabajador
                    </a>
                </div>
            </form>
        </div>
        
        <div class="table-container">
            <?php if ($resultado->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>CI</th>
                            <th>Nombre Completo</th>
                            <th>Cargo</th>
                            <th>Tipo</th>
                            <th>Estatus</th>
                            <th>Fecha Ingreso</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($empleado = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if (!empty($empleado['foto'])): ?>
                                    <img src="<?php echo htmlspecialchars($empleado['foto']); ?>" 
                                         class="photo-thumbnail" 
                                         alt="Foto" 
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="photo-placeholder" style="display: none;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="photo-placeholder">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($empleado['ci']); ?></strong>
                            </td>
                            <td>
                                <div style="font-weight: 600;">
                                    <?php echo htmlspecialchars($empleado['primer_nombre'] . ' ' . $empleado['primer_apellido']); ?>
                                </div>
                                <div style="font-size: 12px; color: #7f8c8d;">
                                    <?php echo htmlspecialchars($empleado['segundo_nombre'] . ' ' . $empleado['segundo_apellido']); ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($empleado['cargo']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($empleado['tipo_trabajador']); ?>">
                                    <?php echo strtoupper($empleado['tipo_trabajador']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php echo $empleado['estatus'] == 'activo' ? 'badge-active' : 'badge-inactive'; ?>">
                                    <?php echo ucfirst($empleado['estatus']); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo date('d/m/Y', strtotime($empleado['fecha_ingreso'])); ?>
                            </td>
                            <td>
                                <span style="font-size: 12px; color: #7f8c8d;">
                                    <?php echo date('d/m/Y', strtotime($empleado['fecha_registro'])); ?>
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="../php/editar.php?id=<?php echo $empleado['id']; ?>" 
                                       class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <a href="eliminar.php?id=<?php echo $empleado['id']; ?>&ci=<?php echo urlencode($empleado['ci']); ?>" 
                                       class="btn btn-danger" title="Eliminar"
                                       onclick="return confirm('¿Eliminar a <?php echo htmlspecialchars($empleado['primer_nombre'] . ' ' . $empleado['primer_apellido']); ?>?\n\n⚠️ También se eliminarán sus familiares registrados.')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    
                                    <a href="../html/tabla_datos.php?search=<?php echo urlencode($empleado['ci']); ?>&filter=ci" 
                                       class="btn btn-primary" title="Ver Detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-user-slash"></i>
                    <h3>No se encontraron trabajadores</h3>
                    <p><?php echo !empty($busqueda) ? 'Intenta con otros términos de búsqueda.' : 'Comienza registrando un nuevo trabajador.'; ?></p>
                    <a href="../html/formulario1.php" class="btn btn-success" style="margin-top: 15px;">
                        <i class="fas fa-user-plus"></i> Registrar Primer Trabajador
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if ($total_paginas > 1): ?>
            <div class="pagination">
                <?php if ($pagina_actual > 1): ?>
                    <a href="?pagina=<?php echo $pagina_actual - 1; ?>&busqueda=<?php echo urlencode($busqueda); ?>&filtro=<?php echo $filtro; ?>&estatus=<?php echo $estatus; ?>">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </a>
                <?php endif; ?>
                
                <span>Página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?></span>
                
                <?php if ($pagina_actual < $total_paginas): ?>
                    <a href="?pagina=<?php echo $pagina_actual + 1; ?>&busqueda=<?php echo urlencode($busqueda); ?>&filtro=<?php echo $filtro; ?>&estatus=<?php echo $estatus; ?>">
                        Siguiente <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Confirmación mejorada para eliminar
        document.querySelectorAll('.btn-danger').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const row = this.closest('tr');
                const nombre = row.querySelector('td:nth-child(3) div:first-child').textContent;
                const ci = row.querySelector('td:nth-child(2) strong').textContent;
                
                if (!confirm(`⚠️ ELIMINACIÓN PERMANENTE\n\n¿Estás seguro de eliminar a:\n${nombre}\nCI: ${ci}\n\n❌ Esta acción también eliminará TODOS sus familiares registrados.\n❌ No se puede deshacer.`)) {
                    e.preventDefault();
                }
            });
        });
        
        // Auto-focus en búsqueda
        document.querySelector('input[name="busqueda"]')?.focus();
        
        // Mostrar tooltips
        document.querySelectorAll('[title]').forEach(el => {
            el.addEventListener('mouseenter', function() {
                const tooltip = document.createElement('div');
                tooltip.textContent = this.getAttribute('title');
                tooltip.style.cssText = `
                    position: absolute;
                    background: #333;
                    color: white;
                    padding: 5px 10px;
                    border-radius: 4px;
                    font-size: 12px;
                    z-index: 1000;
                    white-space: nowrap;
                    pointer-events: none;
                `;
                document.body.appendChild(tooltip);
                
                const rect = this.getBoundingClientRect();
                tooltip.style.top = (rect.top - 30) + 'px';
                tooltip.style.left = (rect.left + rect.width/2 - tooltip.offsetWidth/2) + 'px';
                
                this.dataset.tooltip = tooltip;
            });
            
            el.addEventListener('mouseleave', function() {
                if (this.dataset.tooltip) {
                    document.body.removeChild(this.dataset.tooltip);
                    delete this.dataset.tooltip;
                }
            });
        });
        
        // Eliminar mensaje después de 5 segundos
        const message = document.querySelector('.message');
        if (message) {
            setTimeout(() => {
                message.style.opacity = '0';
                message.style.transition = 'opacity 0.5s';
                setTimeout(() => message.remove(), 500);
            }, 5000);
        }
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>