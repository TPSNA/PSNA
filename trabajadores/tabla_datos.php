<?php
// html/tabla_datos.php - TABLA CON TODOS LOS TRABAJADORES
session_start();

// Verificar sesión
if (!isset($_SESSION['username'])) {
    header("Location: ../admin/login.php");
    exit();
}

require_once '../admin/includes/database.php';

// Configuración de paginación
$registros_por_pagina = 20;
$pagina_actual = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Búsqueda
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'todos';

// Construir consulta
$where = '';
$params = [];
$types = '';

if ($search) {
    switch ($filter) {
        case 'ci':
            $where = "WHERE ci LIKE ?";
            $params[] = "%$search%";
            $types = 's';
            break;
        case 'nombre':
            $where = "WHERE primer_nombre LIKE ? OR segundo_nombre LIKE ?";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $types = 'ss';
            break;
        case 'apellido':
            $where = "WHERE primer_apellido LIKE ? OR segundo_apellido LIKE ?";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $types = 'ss';
            break;
        case 'cargo':
            $where = "WHERE cargo LIKE ?";
            $params[] = "%$search%";
            $types = 's';
            break;
        case 'sede':
            $where = "WHERE sede LIKE ?";
            $params[] = "%$search%";
            $types = 's';
            break;
        case 'todos':
        default:
            $where = "WHERE ci LIKE ? OR primer_nombre LIKE ? OR primer_apellido LIKE ? OR cargo LIKE ? OR sede LIKE ?";
            $search_term = "%$search%";
            $params = [$search_term, $search_term, $search_term, $search_term, $search_term];
            $types = 'sssss';
            break;
    }
}

// Contar total de registros
$sql_count = "SELECT COUNT(*) as total FROM empleados $where";
$stmt_count = $conn->prepare($sql_count);
if ($where) {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_registros = $result_count->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);
$stmt_count->close();

// Obtener registros paginados
$sql = "SELECT id, ci, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, 
               fecha_nacimiento, sexo, estado_civil, telefono, correo, cargo, sede, 
               estatus, fecha_ingreso, fecha_registro 
        FROM empleados $where 
        ORDER BY fecha_registro DESC 
        LIMIT ?, ?";

// Agregar parámetros de paginación
$params[] = $offset;
$params[] = $registros_por_pagina;
$types .= 'ii';

$stmt = $conn->prepare($sql);
if ($where || $search) {
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param('ii', $offset, $registros_por_pagina);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Trabajadores - SAINA</title>
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
            --card-background: rgba(255, 255, 255, 0.95);
            --shadow-light: rgba(0, 0, 0, 0.1);
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
            max-width: 1600px;
            margin: 0 auto;
        }
        
        /* HEADER */
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
        -webkit-background-clip: text;  /* For older WebKit-based browsers */
        background-clip: text;          /* Standard property for modern browsers */
        -webkit-text-fill-color: transparent;
        margin-bottom: 8px;
}
        
        .header-content p {
            color: var(--light-text);
            font-size: 14px;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            color: white;
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        /* BARRA DE BÚSQUEDA */
        .search-container {
            background: rgba(106, 103, 240, 0.05);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .search-form {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .search-input {
            flex: 1;
            min-width: 300px;
            padding: 14px 18px;
            border: 2px solid #e6e6e6;
            border-radius: 12px;
            font-size: 15px;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(106, 103, 240, 0.2);
        }
        
        .filter-select {
            padding: 14px 18px;
            border: 2px solid #e6e6e6;
            border-radius: 12px;
            font-size: 15px;
            background: white;
            min-width: 150px;
        }
        
        /* TABLA */
        .table-wrapper {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: auto;
            margin-bottom: 30px;
            max-height: 600px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
        }
        
        thead {
            background: linear-gradient(90deg, var(--purple-start), var(--blue-end));
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        th {
            padding: 20px;
            text-align: left;
            color: white;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-right: 1px solid rgba(255,255,255,0.1);
        }
        
        th:last-child {
            border-right: none;
        }
        
        tbody tr {
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        tbody tr:hover {
            background: rgba(106, 103, 240, 0.03);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        td {
            padding: 18px 20px;
            font-size: 14px;
            vertical-align: middle;
        }
        
        /* BADGES */
        .badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }
        
        .badge-success {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
        }
        
        .badge-warning {
            background: linear-gradient(135deg, #ffd93d, #ff6b6b);
            color: white;
        }
        
        .badge-info {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
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
            flex-wrap: wrap;
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
        
        /* ESTADÍSTICAS */
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
        
        /* RESPONSIVE */
        @media (max-width: 768px) {
            .search-form {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-input, .filter-select {
                width: 100%;
            }
            
            .table-wrapper {
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
                    <i class="fas fa-table"></i>
                </div>
                <div class="header-content">
                    <h1>Tabla de Trabajadores</h1>
                    <p>Vista completa de todos los empleados registrados en el sistema</p>
                </div>
            </div>
            
            <div class="stats-bar">
                <div class="stat-item">
                    <i class="fas fa-users"></i>
                    <span>Total: <span class="number"><?php echo $total_registros; ?></span></span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-eye"></i>
                    <span>Mostrando: <span class="number"><?php echo min($registros_por_pagina, $result->num_rows); ?></span></span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-file-export"></i>
                    <a href="../php/excel.php" style="color: #4facfe; text-decoration: none;">
                        Exportar a Excel
                    </a>
                </div>
            </div>
            
            <div style="margin-top: 20px; display: flex; gap: 15px;">
                <?php if ($_SESSION['rol'] == 'admin'): ?>
                    <a href="../admin/trabajadores/index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Panel de Administración
                    </a>
                    <a href="../admin/index.php" class="btn" style="background: #95a5a6; color: white;">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                <?php else: ?>
                    <a href="../usuario/trabajadores/index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Panel de Usuario
                    </a>
                    <a href="../usuario/index.php" class="btn" style="background: #95a5a6; color: white;">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                <?php endif; ?>
                
                <a href="formulario1.php" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Nuevo Trabajador
                </a>
            </div>
        </div>
        
        <!-- BARRA DE BÚSQUEDA -->
        <div class="search-container">
            <form method="GET" class="search-form">
                <input type="text" name="search" class="search-input" 
                       placeholder="Buscar trabajadores..."
                       value="<?php echo htmlspecialchars($search); ?>">
                
                <select name="filter" class="filter-select">
                    <option value="todos" <?php echo $filter == 'todos' ? 'selected' : ''; ?>>Buscar en todos los campos</option>
                    <option value="ci" <?php echo $filter == 'ci' ? 'selected' : ''; ?>>Cédula</option>
                    <option value="nombre" <?php echo $filter == 'nombre' ? 'selected' : ''; ?>>Nombre</option>
                    <option value="apellido" <?php echo $filter == 'apellido' ? 'selected' : ''; ?>>Apellido</option>
                    <option value="cargo" <?php echo $filter == 'cargo' ? 'selected' : ''; ?>>Cargo</option>
                    <option value="sede" <?php echo $filter == 'sede' ? 'selected' : ''; ?>>Sede</option>
                </select>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
                
                <?php if ($search): ?>
                    <a href="tabla_datos.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- TABLA -->
        <div class="table-wrapper">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cédula</th>
                            <th>Nombre Completo</th>
                            <th>Fecha Nac.</th>
                            <th>Sexo</th>
                            <th>Estado Civil</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Cargo</th>
                            <th>Sede</th>
                            <th>Estado</th>
                            <th>Fecha Ingreso</th>
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
                                <?php echo date('d/m/Y', strtotime($row['fecha_nacimiento'])); ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['sexo']); ?></td>
                            <td><?php echo htmlspecialchars($row['estado_civil']); ?></td>
                            <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($row['correo']); ?></td>
                            <td><?php echo htmlspecialchars($row['cargo']); ?></td>
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
                                <div style="font-size: 12px;">
                                    <?php echo date('d/m/Y', strtotime($row['fecha_registro'])); ?>
                                </div>
                            </td>
                            <td>
                                <div class="actions">
                                    <?php if ($_SESSION['rol'] == 'admin'): ?>
                                        <a href="../admin/trabajadores/ver.php?id=<?php echo $row['id']; ?>" 
                                           class="btn-action btn-primary">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        <a href="../admin/trabajadores/editar.php?id=<?php echo $row['id']; ?>" 
                                           class="btn-action" style="background: #ffd93d; color: white;">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                    <?php else: ?>
                                        <a href="../usuario/trabajadores/ver.php?id=<?php echo $row['id']; ?>" 
                                           class="btn-action btn-primary">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        <a href="../usuario/trabajadores/editar.php?id=<?php echo $row['id']; ?>" 
                                           class="btn-action" style="background: #ffd93d; color: white;">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 60px 20px; color: var(--light-text);">
                    <i class="fas fa-users-slash" style="font-size: 60px; margin-bottom: 20px;"></i>
                    <h3>No se encontraron trabajadores</h3>
                    <p><?php echo $search ? 'Intenta con otros términos de búsqueda' : 'No hay trabajadores registrados'; ?></p>
                    <a href="formulario1.php" class="btn btn-primary" style="margin-top: 20px;">
                        <i class="fas fa-user-plus"></i> Registrar Primer Trabajador
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- PAGINACIÓN -->
        <?php if ($total_paginas > 1): ?>
            <div class="pagination">
                <?php if ($pagina_actual > 1): ?>
                    <a href="?page=<?php echo $pagina_actual - 1; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo $filter; ?>" 
                       class="page-btn">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </a>
                <?php endif; ?>
                
                <?php for($i = 1; $i <= $total_paginas; $i++): ?>
                    <?php if($i == 1 || $i == $total_paginas || ($i >= $pagina_actual - 2 && $i <= $pagina_actual + 2)): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo $filter; ?>" 
                           class="page-btn <?php echo $i == $pagina_actual ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php elseif($i == $pagina_actual - 3 || $i == $pagina_actual + 3): ?>
                        <span class="page-btn">...</span>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($pagina_actual < $total_paginas): ?>
                    <a href="?page=<?php echo $pagina_actual + 1; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo $filter; ?>" 
                       class="page-btn">
                        Siguiente <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- INFORMACIÓN -->
        <div style="text-align: center; color: var(--light-text); font-size: 14px; margin-top: 20px;">
            <i class="fas fa-info-circle"></i> 
            Página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?> 
            • Total registros: <?php echo $total_registros; ?>
            <?php if ($search): ?>
                • Búsqueda: "<?php echo htmlspecialchars($search); ?>"
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Efectos visuales
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-enfocar en búsqueda
            document.querySelector('.search-input').focus();
            
            // Animación de entrada para las filas
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                setTimeout(() => {
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 50);
            });
            
            // Establecer estilos iniciales para animación
            rows.forEach(row => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                row.style.transition = 'all 0.5s ease';
            });
            
            // Alternar colores de filas
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach((row, index) => {
                if (index % 2 === 0) {
                    row.style.backgroundColor = 'rgba(106, 103, 240, 0.02)';
                }
            });
        });
        
        // Confirmación para exportar
        document.querySelector('a[href*="excel.php"]').addEventListener('click', function(e) {
            if (!confirm('¿Generar reporte en Excel?\n\nSe descargará un archivo con todos los datos de los trabajadores.')) {
                e.preventDefault();
            }
        });
        
        // Ordenar tabla por columnas (opcional)
        function sortTable(columnIndex) {
            const table = document.querySelector('table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            rows.sort((a, b) => {
                const aText = a.cells[columnIndex].textContent.trim();
                const bText = b.cells[columnIndex].textContent.trim();
                
                // Intentar convertir a números si es posible
                const aNum = parseFloat(aText.replace(/[^\d.-]/g, ''));
                const bNum = parseFloat(bText.replace(/[^\d.-]/g, ''));
                
                if (!isNaN(aNum) && !isNaN(bNum)) {
                    return aNum - bNum;
                }
                
                // Comparación de texto
                return aText.localeCompare(bText);
            });
            
            // Reordenar filas
            rows.forEach(row => tbody.appendChild(row));
        }
        
        // Hacer clickeables los encabezados para ordenar
        document.querySelectorAll('th').forEach((th, index) => {
            th.style.cursor = 'pointer';
            th.title = 'Click para ordenar';
            th.addEventListener('click', () => sortTable(index));
        });
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>