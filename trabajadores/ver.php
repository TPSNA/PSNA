<?php
// usuario/trabajadores/ver.php - VER DETALLES COMPLETOS
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];

// Obtener datos del trabajador
$sql = "SELECT * FROM empleados WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php?error=notfound");
    exit();
}

$trabajador = $result->fetch_assoc();
$stmt->close();

// Obtener familiares si existen
$sql_familiares = "SELECT * FROM familiares WHERE ci_trabajador = ?";
$stmt_fam = $conn->prepare($sql_familiares);
$stmt_fam->bind_param("s", $trabajador['ci']);
$stmt_fam->execute();
$familiares = $stmt_fam->get_result();
$stmt_fam->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Trabajador - Usuario SAINA</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-title {
            display: flex;
            align-items: center;
            gap: 20px;
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
        }
        
        .header-content h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 5px;
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
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .info-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        
        .card-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(106, 103, 240, 0.1);
        }
        
        .card-icon {
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
        
        .card-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-color);
        }
        
        .info-item {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .info-label {
            font-size: 12px;
            color: var(--light-text);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 16px;
            font-weight: 500;
            color: var(--text-color);
        }
        
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .badge-active {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
        }
        
        .badge-inactive {
            background: linear-gradient(135deg, #ffd93d, #ff6b6b);
            color: white;
        }
        
        .photo-container {
            text-align: center;
            margin-top: 20px;
        }
        
        .photo-preview {
            width: 200px;
            height: 200px;
            border-radius: 15px;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
        }
        
        .familiares-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .familiar-card {
            background: rgba(106, 103, 240, 0.05);
            padding: 20px;
            border-radius: 15px;
            border-left: 4px solid var(--primary-color);
        }
        
        .no-data {
            text-align: center;
            color: var(--light-text);
            padding: 40px;
            font-style: italic;
        }
        
        .section-actions {
            text-align: center;
            margin-top: 40px;
            padding-top: 40px;
            border-top: 2px solid rgba(0,0,0,0.05);
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
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
                    <h1><?php echo htmlspecialchars($trabajador['primer_nombre'] . ' ' . $trabajador['primer_apellido']); ?></h1>
                    <p>CI: <?php echo htmlspecialchars($trabajador['ci']); ?> • ID: <?php echo $trabajador['id']; ?></p>
                </div>
            </div>
            
            <div class="header-actions">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="editar.php?id=<?php echo $trabajador['id']; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
            </div>
        </div>
        
        <!-- INFORMACIÓN GENERAL -->
        <div class="info-grid">
            <!-- DATOS PERSONALES -->
            <div class="info-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div class="card-title">Datos Personales</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Nacionalidad</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['nacionalidad']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Cédula de Identidad</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['ci']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Nombres Completos</div>
                    <div class="info-value">
                        <?php echo htmlspecialchars($trabajador['primer_nombre'] . ' ' . $trabajador['segundo_nombre']); ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Apellidos Completos</div>
                    <div class="info-value">
                        <?php echo htmlspecialchars($trabajador['primer_apellido'] . ' ' . $trabajador['segundo_apellido']); ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Fecha de Nacimiento</div>
                    <div class="info-value">
                        <?php echo date('d/m/Y', strtotime($trabajador['fecha_nacimiento'])); ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Sexo</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['sexo']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Estado Civil</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['estado_civil']); ?></div>
                </div>
            </div>
            
            <!-- INFORMACIÓN LABORAL -->
            <div class="info-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="card-title">Información Laboral</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Tipo de Trabajador</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['tipo_trabajador']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Grado de Instrucción</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['grado_instruccion']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Cargo</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['cargo']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Sede</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['sede']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Dependencia</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['dependencia']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Fecha de Ingreso</div>
                    <div class="info-value">
                        <?php echo date('d/m/Y', strtotime($trabajador['fecha_ingreso'])); ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Código SIANTEL</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['cod_siantel']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Estado</div>
                    <div class="info-value">
                        <span class="badge <?php echo $trabajador['estatus'] == 'ACTIVO' ? 'badge-active' : 'badge-inactive'; ?>">
                            <?php echo $trabajador['estatus']; ?>
                        </span>
                    </div>
                </div>
                
                <?php if ($trabajador['estatus'] == 'INACTIVO'): ?>
                <div class="info-item">
                    <div class="info-label">Fecha de Egreso</div>
                    <div class="info-value">
                        <?php echo $trabajador['fecha_egreso'] ? date('d/m/Y', strtotime($trabajador['fecha_egreso'])) : 'N/A'; ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Motivo de Retiro</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['motivo_retiro']); ?></div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- CONTACTO Y UBICACIÓN -->
            <div class="info-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="card-title">Contacto y Ubicación</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Dirección</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['direccion_ubicacion']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Teléfono</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['telefono']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Correo Electrónico</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['correo']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Cuenta Bancaria</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['cuenta_bancaria']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Ubicación Estante</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['ubicacion_estante']); ?></div>
                </div>
                
                <?php if ($trabajador['estatus'] == 'INACTIVO'): ?>
                <div class="info-item">
                    <div class="info-label">Ubicación Estante (Retiro)</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['ubicacion_estante_retiro']); ?></div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- INFORMACIÓN GENERAL -->
            <div class="info-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="card-title">Información General</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Tipo de Sangre</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['tipo_sangre']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Lateralidad</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['lateralidad']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Peso</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['peso_trabajador']); ?> kg</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Altura</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['altura_trabajador']); ?> cm</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Talla Calzado</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['calzado_trabajador']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Talla Camisa</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['camisa_trabajador']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Talla Pantalón</div>
                    <div class="info-value"><?php echo htmlspecialchars($trabajador['pantalon_trabajador']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Fecha de Registro</div>
                    <div class="info-value">
                        <?php echo date('d/m/Y H:i', strtotime($trabajador['fecha_registro'])); ?>
                    </div>
                </div>
                
                <?php if ($trabajador['foto']): ?>
                <div class="photo-container">
                    <img src="<?php echo htmlspecialchars($trabajador['foto']); ?>" 
                         alt="Foto del trabajador" 
                         class="photo-preview">
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- FAMILIARES -->
        <?php if ($familiares->num_rows > 0): ?>
        <div class="info-card" style="margin-bottom: 40px;">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-title">Familiares Registrados</div>
            </div>
            
            <div class="familiares-grid">
                <?php while($familiar = $familiares->fetch_assoc()): ?>
                <div class="familiar-card">
                    <div style="font-weight: 600; margin-bottom: 10px; color: var(--primary-color);">
                        <?php echo htmlspecialchars($familiar['cedula_familiar']); ?>
                    </div>
                    <div style="margin-bottom: 8px;">
                        <strong>Parentesco:</strong> <?php echo htmlspecialchars($familiar['parentesco']); ?>
                    </div>
                    <div style="margin-bottom: 8px;">
                        <strong>Edad:</strong> <?php echo htmlspecialchars($familiar['edad']); ?> años
                    </div>
                    <div style="margin-bottom: 8px;">
                        <strong>Tipo Sangre:</strong> <?php echo htmlspecialchars($familiar['tipo_sangre']); ?>
                    </div>
                    <div>
                        <strong>Registro:</strong> <?php echo date('d/m/Y', strtotime($familiar['fecha_registro'])); ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- ACCIONES -->
        <div class="section-actions">
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-list"></i> Volver al Listado
            </a>
            <a href="editar.php?id=<?php echo $trabajador['id']; ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar Trabajador
            </a>
            <!-- NOTA: NO HAY BOTÓN ELIMINAR PARA USUARIOS -->
        </div>
    </div>
    
    <script>
        // Efectos visuales
        document.addEventListener('DOMContentLoaded', function() {
            // Animación de entrada para las tarjetas
            const cards = document.querySelectorAll('.info-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });
            
            // Establecer estilos iniciales
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.6s ease';
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>