<?php
// admin/usuarios/listar.php - LISTAR TODOS LOS USUARIOS
require_once '../includes/database.php';
session_start();

// Verificar que sea administrador
if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Obtener todos los usuarios
$sql = "SELECT * FROM usuarios ORDER BY fecha_creacion DESC";
$resultado = $conn->query($sql);

// Contar usuarios
$sql_count = "SELECT COUNT(*) as total FROM usuarios";
$count_result = $conn->query($sql_count);
$total_usuarios = $count_result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios - SAINA Admin</title>
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
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
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
        
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
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
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
            padding: 6px 12px;
            font-size: 13px;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ffd93d, #ff6b6b);
            color: white;
            padding: 6px 12px;
            font-size: 13px;
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
        
        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: linear-gradient(90deg, #2c3e50, #4a6491);
            color: white;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #f1f1f1;
        }
        
        th {
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
        
        .badge-admin {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .badge-user {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
        }
        
        .badge-active {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
        }
        
        .badge-inactive {
            background: linear-gradient(135deg, #ffd93d, #ff6b6b);
            color: white;
        }
        
        .actions {
            display: flex;
            gap: 8px;
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
            .header-actions {
                flex-direction: column;
                gap: 15px;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-users"></i> Gestión de Usuarios</h1>
            <p>Administra los usuarios del sistema SAINA</p>
            
            <div class="stats-bar">
                <div class="stat-item">
                    <i class="fas fa-users"></i>
                    <span>Total usuarios: <span class="number"><?php echo $total_usuarios; ?></span></span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-user-shield"></i>
                    <a href="registrar.php" style="color: #4facfe; text-decoration: none;">
                        + Agregar nuevo usuario
                    </a>
                </div>
            </div>
            
            <div class="header-actions">
                <a href="registrar.php" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Nuevo Usuario
                </a>
                
                <a href="../index.php" class="btn" style="background: #95a5a6; color: white;">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
        </div>
        
        <div class="table-container">
            <?php if ($resultado->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($usuario = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $usuario['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($usuario['username']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td>
                                <span class="badge <?php echo $usuario['rol'] == 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                                    <?php echo $usuario['rol'] == 'admin' ? 'Administrador' : 'Usuario'; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php echo $usuario['activo'] ? 'badge-active' : 'badge-inactive'; ?>">
                                    <?php echo $usuario['activo'] ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($usuario['fecha_creacion'])); ?></td>
                            <td>
                                <div class="actions">
                                    <a href="registrar.php?editar=<?php echo $usuario['id']; ?>" 
                                       class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <?php if ($usuario['username'] != 'racheld'): ?>
                                    <a href="eliminar.php?id=<?php echo $usuario['id']; ?>" 
                                       class="btn btn-danger"
                                       onclick="return confirm('¿Eliminar usuario <?php echo htmlspecialchars($usuario['username']); ?>?')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-users-slash"></i>
                    <h3>No hay usuarios registrados</h3>
                    <p>Comienza creando el primer usuario del sistema.</p>
                    <a href="registrar.php" class="btn btn-primary" style="margin-top: 15px;">
                        <i class="fas fa-user-plus"></i> Crear Primer Usuario
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="../index.php" class="btn" style="background: #95a5a6; color: white;">
                <i class="fas fa-home"></i> Volver al Dashboard
            </a>
        </div>
    </div>
    
    <script>
        // Confirmación para eliminar
        document.querySelectorAll('.btn-danger').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const row = this.closest('tr');
                const username = row.querySelector('td:nth-child(2) strong').textContent;
                
                if (!confirm(`¿Estás seguro de eliminar al usuario "${username}"?\n\nEsta acción no se puede deshacer.`)) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>