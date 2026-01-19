<?php
// admin/usuarios/registrar.php - CREAR Y EDITAR USUARIOS
require_once '../includes/database.php';
session_start();

// Verificar que sea administrador
if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$error = '';
$exito = '';
$usuario = [
    'id' => '',
    'username' => '',
    'email' => '',
    'rol' => 'usuario',
    'activo' => 1
];
$titulo = "Nuevo Usuario";
$es_edicion = false;

// ============================================
// 1. SI VIENE PARÁMETRO "editar" → ES EDICIÓN
// ============================================
if (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
    $id_usuario = intval($_GET['editar']);
    $es_edicion = true;
    $titulo = "Editar Usuario";
    
    // Obtener datos del usuario a editar
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows == 1) {
        $usuario = $resultado->fetch_assoc();
    } else {
        $error = "Usuario no encontrado";
        $es_edicion = false;
    }
    $stmt->close();
}

// ============================================
// 2. PROCESAR FORMULARIO (POST)
// ============================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $rol = $_POST['rol'];
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    // Validaciones básicas
    if (empty($username)) {
        $error = "El nombre de usuario es requerido";
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo electrónico no es válido";
    } else {
        if ($es_edicion) {
            // ============================================
            // 2A. MODIFICAR USUARIO EXISTENTE
            // ============================================
            $id = intval($_POST['id']);
            
            // Verificar si username ya existe (excluyendo el actual)
            $sql_check = "SELECT id FROM usuarios WHERE username = ? AND id != ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("si", $username, $id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows > 0) {
                $error = "El nombre de usuario ya está en uso";
            } else {
                // Actualizar usuario
                $sql = "UPDATE usuarios SET username = ?, email = ?, rol = ?, activo = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssii", $username, $email, $rol, $activo, $id);
                
                if ($stmt->execute()) {
                    $exito = "Usuario actualizado correctamente";
                    // Actualizar datos locales
                    $usuario['username'] = $username;
                    $usuario['email'] = $email;
                    $usuario['rol'] = $rol;
                    $usuario['activo'] = $activo;
                } else {
                    $error = "Error al actualizar usuario: " . $conn->error;
                }
                $stmt->close();
            }
            $stmt_check->close();
            
        } else {
            // ============================================
            // 2B. CREAR NUEVO USUARIO
            // ============================================
            $password = trim($_POST['password']);
            $confirm_password = trim($_POST['confirm_password']);
            
            if (empty($password)) {
                $error = "La contraseña es requerida";
            } elseif (strlen($password) < 4) {
                $error = "La contraseña debe tener al menos 4 caracteres";
            } elseif ($password !== $confirm_password) {
                $error = "Las contraseñas no coinciden";
            } else {
                // Verificar si el usuario ya existe
                $sql_check = "SELECT id FROM usuarios WHERE username = ?";
                $stmt_check = $conn->prepare($sql_check);
                $stmt_check->bind_param("s", $username);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();
                
                if ($result_check->num_rows > 0) {
                    $error = "El nombre de usuario ya está registrado";
                } else {
                    // Insertar nuevo usuario con contraseña encriptada
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "INSERT INTO usuarios (username, password, email, rol, activo) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssi", $username, $password_hash, $email, $rol, $activo);
                    
                    if ($stmt->execute()) {
                        $exito = "Usuario creado correctamente";
                        // Limpiar formulario
                        $usuario = [
                            'id' => '',
                            'username' => '',
                            'email' => '',
                            'rol' => 'usuario',
                            'activo' => 1
                        ];
                    } else {
                        $error = "Error al crear usuario: " . $conn->error;
                    }
                    $stmt->close();
                }
                $stmt_check->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $titulo; ?> - SAINA Admin</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 500px;
        }
        
        .form-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .form-header {
            background: linear-gradient(90deg, #2c3e50, #4a6491);
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .form-header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .form-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }
        
        .required::after {
            content: " *";
            color: #ff6b6b;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5eb;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        select:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 8px;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .radio-option input[type="radio"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .form-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ffd93d, #ff6b6b);
            color: white;
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
        }
        
        .alert-error {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
        }
        
        .password-container {
            position: relative;
        }
        
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #666;
        }
        
        .info-text {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        @media (max-width: 480px) {
            .form-body {
                padding: 20px;
            }
            
            .radio-group {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-card">
            <div class="form-header">
                <h1>
                    <i class="fas <?php echo $es_edicion ? 'fa-user-edit' : 'fa-user-plus'; ?>"></i>
                    <?php echo $titulo; ?>
                </h1>
                <p>Sistema de Administración SAINA</p>
            </div>
            
            <div class="form-body">
                <?php if ($exito): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $exito; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <?php if ($es_edicion): ?>
                        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="username" class="required">Nombre de Usuario</label>
                        <input type="text" id="username" name="username" 
                               value="<?php echo htmlspecialchars($usuario['username']); ?>"
                               placeholder="Ej: maria.garcia" required autofocus>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($usuario['email']); ?>"
                               placeholder="ejemplo@saina.com">
                    </div>
                    
                    <?php if (!$es_edicion): ?>
                        <div class="form-group">
                            <label for="password" class="required">Contraseña</label>
                            <div class="password-container">
                                <input type="password" id="password" name="password" 
                                       placeholder="Mínimo 4 caracteres" required>
                                <button type="button" class="toggle-password" data-target="password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="info-text">
                                <i class="fas fa-info-circle"></i> La contraseña será encriptada
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password" class="required">Confirmar Contraseña</label>
                            <div class="password-container">
                                <input type="password" id="confirm_password" name="confirm_password" 
                                       placeholder="Repite la contraseña" required>
                                <button type="button" class="toggle-password" data-target="confirm_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>Tipo de Usuario</label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="rol_usuario" name="rol" value="usuario" 
                                       <?php echo ($usuario['rol'] == 'usuario') ? 'checked' : ''; ?>>
                                <label for="rol_usuario">Usuario Normal</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="rol_admin" name="rol" value="admin"
                                       <?php echo ($usuario['rol'] == 'admin') ? 'checked' : ''; ?>>
                                <label for="rol_admin">Administrador</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="activo" name="activo" 
                                   <?php echo $usuario['activo'] ? 'checked' : ''; ?>>
                            <label for="activo">Usuario activo (puede iniciar sesión)</label>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn <?php echo $es_edicion ? 'btn-warning' : 'btn-primary'; ?>">
                            <i class="fas <?php echo $es_edicion ? 'fa-save' : 'fa-user-plus'; ?>"></i>
                            <?php echo $es_edicion ? 'Actualizar Usuario' : 'Crear Usuario'; ?>
                        </button>
                        
                        <a href="listar.php" class="btn btn-secondary">
                            <i class="fas fa-list"></i> Ver Todos los Usuarios
                        </a>
                        
                        <a href="../index.php" class="btn btn-secondary">
                            <i class="fas fa-home"></i> Volver al Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Mostrar/ocultar contraseña
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.className = 'fas fa-eye-slash';
                } else {
                    input.type = 'password';
                    icon.className = 'fas fa-eye';
                }
            });
        });
        
        // Validación de contraseñas en tiempo real (solo para creación)
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        
        if (password && confirmPassword) {
            function validarContraseñas() {
                if (password.value && confirmPassword.value) {
                    if (password.value !== confirmPassword.value) {
                        confirmPassword.style.borderColor = '#ff6b6b';
                    } else {
                        confirmPassword.style.borderColor = '#43e97b';
                    }
                }
            }
            
            password.addEventListener('input', validarContraseñas);
            confirmPassword.addEventListener('input', validarContraseñas);
            
            // Validar al enviar formulario
            document.querySelector('form').addEventListener('submit', function(e) {
                if (password.value.length < 4) {
                    e.preventDefault();
                    alert('La contraseña debe tener al menos 4 caracteres');
                    password.focus();
                    return false;
                }
                
                if (password.value !== confirmPassword.value) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden');
                    confirmPassword.focus();
                    return false;
                }
            });
        }
        
        // Auto-focus en username
        document.getElementById('username').focus();
        
        // Mostrar mensajes temporales
        const alertMessage = document.querySelector('.alert');
        if (alertMessage) {
            setTimeout(() => {
                alertMessage.style.opacity = '0';
                alertMessage.style.transition = 'opacity 0.5s';
                setTimeout(() => alertMessage.remove(), 500);
            }, 5000);
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>