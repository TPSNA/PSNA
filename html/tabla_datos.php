<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Historico-registros</title>
    <link rel="stylesheet" href="../css/tabla_datos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="../javascrip/formulario1.js"></script>
</head>
<body>
    <!-- juangelyn sanchez -->

    <header class="fixed-header">
        <div class="header-content">
            <div class="left-group">
                <nav class="nav-menu">
                    <input type="checkbox" id="menu-toggle" class="menu-toggle">
                        <label for="menu-toggle" class="menu-icon">
                            <span></span>
                            <span></span>
                            <span></span>
                        </label>
                        <ul class="menu-list">
                            <li><a href="../html/bienvenida.php">Inicio</a></li>
                            <li><a href="../html/formulario1.php">Registro Trabajador</a></li>
                            <li><a href="../html/tabla_datos.php">Historico Registros</a></li>
                            <li><a href="carnet-siantel.html">Carnet SIANTEL</a></li>
                        </ul>
                    </nav>
                    
                    <div class="logo-container">
                        <img src="../imagen/2 - Logo SAINA Horizontal.png" alt="Logo Principal" class="logo">
                    </div>
                </div>
                
                <!-- cerrar sesión -->
                <div class="logout-section">
            <a href="logout.php" class="logout-btn">
                <div class="nav-icon"><i class="fas fa-sign-out-alt"></i></div>
                <div class="nav-text">Cerrar Sesión</div>
            </a>
        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="main-container">
        
            <div class="header-section">
                <div class="logo-section">
                    <i class="fas fa-building"></i> Historico Registros 
                </div>
                <div class="search-filter">
                    <form method="GET" action="">
                        <input type="text" name="search" placeholder="Buscar empleado..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        <select name="filter">
                            <option value="">Filtrar por...</option>
                            <option value="ci" <?php if (($_GET['filter'] ?? '') == 'ci') echo 'selected'; ?>>CI</option>
                            <option value="primer_nombre" <?php if (($_GET['filter'] ?? '') == 'primer_nombre') echo 'selected'; ?>>Nombre</option>
                            <option value="correo" <?php if (($_GET['filter'] ?? '') == 'correo') echo 'selected'; ?>>Correo</option>
                        </select>
                        <button type="submit" class="btn">Buscar</button>
                    </form>
                </div>
            </div>

           <!-- Tabla de empleados -->
    <div class="table-container">  <!-- Corregido: era "table-containear" -->
        <table id="employee-table">
            <tr>
                <th>ID</th>
                <th>CI</th>
                <th>Primer Nombre</th>
                <th>Primer Apellido</th>
                <th>Status</th>
                <th>Acciones</th>
            </tr>

                    <?php
                    include("../php/conexion_bd.php");

                    // Configuración de paginación
                    $registros_por_pagina = 10;
                    $pagina_actual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($pagina_actual - 1) * $registros_por_pagina;

                    // Obtener búsqueda y filtro
                    $search = $_GET['search'] ?? '';
                    $filter = $_GET['filter'] ?? '';

                    $where_clause = '';
                    $params = [];
                    $types = '';
                    if ($search && $filter) {
                        $where_clause = "WHERE $filter LIKE ?";
                        $params[] = "%$search%";
                        $types .= 's';
                    }

                    // Contar total de registros con prepared statement
                    $sql_total = "SELECT COUNT(*) AS total FROM empleados $where_clause";
                    $stmt_total = $conexion->prepare($sql_total);
                    if ($params) {
                        $stmt_total->bind_param($types, ...$params);
                    }
                    $stmt_total->execute();
                    $resultado_total = $stmt_total->get_result();
                    $total_registros = $resultado_total->fetch_assoc()['total'];
                    $total_paginas = ceil($total_registros / $registros_por_pagina);
                    $stmt_total->close();

                    // Consulta con paginación 
                    $sql = "SELECT * FROM empleados $where_clause ORDER BY id LIMIT ?, ?";
                    $stmt = $conexion->prepare($sql);
                    $types_pagination = $types . 'ii';  
                    $params_pagination = array_merge($params, [$offset, $registros_por_pagina]);
                    $stmt->bind_param($types_pagination, ...$params_pagination);
                    $stmt->execute();
                    $ejecutar = $stmt->get_result();

                    // Juangelyn_sanchez
                    while ($fila = mysqli_fetch_array($ejecutar)) {
                        ?>
                         <tr>
                                <td><?php echo htmlspecialchars($fila[0]); ?></td>  <!-- ID -->
                                <td><?php echo htmlspecialchars($fila[2]); ?></td>  <!-- CI -->
                                <td><?php echo htmlspecialchars($fila[3]); ?></td>  <!-- PRIMER NOMBRE -->
                                <td><?php echo htmlspecialchars($fila[5]); ?></td>  <!-- PRIMER APELLIDO -->
                                <td><?php echo htmlspecialchars($fila[22]); ?></td> <!-- ESTATUS (Status) -->
                                <td>
                                    <a href="../php/editar.php?id=<?php echo htmlspecialchars($fila[0]); ?>" class="btn-edit">Editar</a>
                                    <button class="btn-details" data-id="<?php echo htmlspecialchars($fila[0]); ?>">Ver Detalles</button>
                                </td>
                
                            </tr>
                        <?php
                    }
                    $stmt->close();
                    mysqli_close($conexion);
                    ?>
                </table>
            </div>

            <!-- Controles de paginación -->
            <?php if ($total_paginas > 1): ?>
                <div class="pagination">
                    <?php if ($pagina_actual > 1): ?>
                        <a href="?page=<?php echo $pagina_actual - 1; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo urlencode($filter); ?>" class="btn-pagination">
                            <i class="fas fa-chevron-left"></i> Anterior
                        </a>
                    <?php endif; ?>

                    <span>Página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?></span>

                    <?php if ($pagina_actual < $total_paginas): ?>
                        <a href="?page=<?php echo $pagina_actual + 1; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo urlencode($filter); ?>" class="btn-pagination">
                            Siguiente <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="export-buttons">
                <button onclick="exportExcel()" class="btn-small blue">Descargar Excel</button>
            </div>

            
        </div>
        <!-- Modal para mostrar detalles -->
            <div id="modal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Detalles del Empleado</h2>
                    <div id="modal-body"></div>
                </div>
            </div>

        <script src="../javascrip/tabla_datos.js"> </script>
    </body>
</html>