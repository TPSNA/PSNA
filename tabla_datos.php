<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Historico-registros</title>
    <link rel="stylesheet" href="../css/tabla_datos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="./jquery-3.5.1.min.js" charset="UTF-8"></script>
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
                        <li><a href="../html/bienvenida.html">Inicio</a></li>
                        <li><a href="../html/formulario1.html">Registro Trabajador</a></li>
                        <li><a href="../html/tabla_datos.php">Historico Registros</a></li>
                        <li><a href="carnet-siantel.html">Carnet SIANTEL</a></li>
                    </ul>
                </nav>
                
                <!-- Logo -->
                <div class="logo-container">
                    <img src="../imagen/2 - Logo SAINA Horizontal.png" alt="Logo Principal" class="logo">
                </div>
            </div>
            
            <!-- Icono de usuario para cerrar sesión -->
            <div class="user-menu">
                <button class="user-icon" id="user-btn">
                    <i class="fas fa-user"></i>
                </button>
                <div class="user-dropdown" id="user-dropdown">
                    <a href="#" id="logout-link">Cerrar Sesión</a>
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
        <div class="table-container">
            <table id="employee-table">
                <tr>
                    <th>ID</th>
                    <th>CI</th>
                    <th>PRIMER NOMBRE</th>
                    <th>SEGUNDO NOMBRE</th>
                    <th>PRIMER APELLIDO</th>
                    <th>SEGUNDO APELLIDO</th>
                    <th>FECHA NAC.</th>
                    <th>EDAD</th>
                    <th>CORREO</th>
                    <th>DIRECCION</th>
                    <th>TELEFONO</th>
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
                if ($search && $filter) {
                    $where_clause = "WHERE $filter LIKE '%" . mysqli_real_escape_string($conexion, $search) . "%'";
                }

                // Contar total de registros
                $sql_total = "SELECT COUNT(*) AS total FROM empleado $where_clause";
                $resultado_total = mysqli_query($conexion, $sql_total);
                $total_registros = mysqli_fetch_assoc($resultado_total)['total'];
                $total_paginas = ceil($total_registros / $registros_por_pagina);

                // Consulta con paginación
                $sql = "SELECT * FROM empleado $where_clause LIMIT $offset, $registros_por_pagina";
                $ejecutar = mysqli_query($conexion, $sql);

                while ($fila = mysqli_fetch_array($ejecutar)) {
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila[0]); ?></td>
                        <td><?php echo htmlspecialchars($fila[1]); ?></td>
                        <td><?php echo htmlspecialchars($fila[2]); ?></td>
                        <td><?php echo htmlspecialchars($fila[3]); ?></td>
                        <td><?php echo htmlspecialchars($fila[4]); ?></td>
                        <td><?php echo htmlspecialchars($fila[5]); ?></td>
                        <td><?php echo htmlspecialchars($fila[6]); ?></td>
                        <td><?php echo htmlspecialchars($fila[7]); ?></td>
                        <td><?php echo htmlspecialchars($fila[8]); ?></td>
                        <td><?php echo htmlspecialchars($fila[9]); ?></td>
                        <td><?php echo htmlspecialchars($fila[10]); ?></td>
                    </tr>
                    <?php
                }
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

    <script>
        function exportExcel() {
            const search = '<?php echo urlencode($search); ?>';
            const filter = '<?php echo urlencode($filter); ?>';
            const url = `../php/excel.php?search=${search}&filter=${filter}`;

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.blob();
                })
                .then(blob => {
                    const downloadUrl = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = downloadUrl;
                    a.download = 'registros.xlsx';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(downloadUrl);
                    document.body.removeChild(a);
                })
                .catch(error => {
                    console.error('Error al descargar el Excel:', error);
                    alert('Hubo un error al descargar el archivo Excel. Verifica la configuración de excel.php.');
                });
        }
    </script>
</body>
</html>
