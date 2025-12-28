<?php
// admin/login.php 
session_start();

// Si ya está logueado, redirigir
if (isset($_SESSION['username'])) {
    if ($_SESSION['rol'] == 'admin') {
        header("Location: index.php");
    } else {
        header("Location: ../usuario/index.php");
    }
    exit();
}

require_once 'includes/database.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        $error = "Usuario y contraseña requeridos";
    } else {
        $sql = "SELECT id, username, password, rol, activo FROM usuarios WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            
            if (!$usuario['activo']) {
                $error = "Usuario inactivo";
            } elseif (password_verify($password, $usuario['password'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['username'] = $usuario['username'];
                $_SESSION['rol'] = $usuario['rol'];
                
                if ($usuario['rol'] == 'admin') {
                    header("Location: index.php");
                } else {
                    header("Location: ../usuario/index.php");
                }
                exit();
            } else {
                $error = "Contraseña incorrecta";
            }
        } else {
            $error = "Usuario no encontrado";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso al Sistema - SAINA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --purple-start: #a8a0f9;
            --blue-end: #6162f4;
            --primary-color: #6a67f0;
            --text-color: #333;
            --light-text: #777;
            --placeholder-color: #aaa;
            --card-background: rgba(255, 255, 255, 0.95);
            --error-color: #e74c3c;
            --success-color: #27ae60;
            --shadow-light: rgba(0, 0, 0, 0.1);
            --shadow-focus: rgba(106, 103, 240, 0.2);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-box {
            background: var(--card-background);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 450px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .logo {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 28px;
            box-shadow: 0 5px 15px rgba(106, 103, 240, 0.3);
        }
        
        .login-header h1 {
            color: var(--text-color);
            margin-bottom: 8px;
            font-size: 26px;
            font-weight: 700;
        }
        
        .login-header p {
            color: var(--light-text);
            font-size: 15px;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: var(--text-color);
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-group label i { color: var(--primary-color); font-size: 16px; }
        
        .input-with-icon { position: relative; }
        
        .input-with-icon i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--placeholder-color);
            font-size: 18px;
        }
        
        .form-control {
            width: 100%;
            padding: 16px 20px 16px 50px;
            border: 2px solid #e6e6e6;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
            background: white;
            color: var(--text-color);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px var(--shadow-focus);
        }
        
        .form-control::placeholder { color: var(--placeholder-color); }
        
        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: var(--placeholder-color);
            padding: 5px;
        }
        
        .password-toggle:hover { color: var(--primary-color); }
        
        .btn-login {
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            color: white;
            border: none;
            padding: 18px;
            width: 100%;
            border-radius: 12px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 5px 15px rgba(106, 103, 240, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(106, 103, 240, 0.4);
        }
        
        .error-message {
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
            color: var(--error-color);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: 1px solid rgba(231, 76, 60, 0.2);
            animation: shake 0.5s;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .login-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid rgba(0,0,0,0.1);
        }
        
        .system-info {
            color: var(--light-text);
            font-size: 13px;
        }
        
        .hint {
            font-size: 12px;
            color: var(--light-text);
            margin-top: 8px;
            text-align: center;
        }
        
        @media (max-width: 480px) {
            .login-box { padding: 30px 20px; }
            .login-header h1 { font-size: 22px; }
            .form-control { padding: 14px 18px 14px 45px; }
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-header">
            <div class="logo"><i class="fas fa-shield-alt"></i></div>
            <h1>Acceso al Sistema</h1>
            <p>Sistema de Gestión SAINA</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="loginForm">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Usuario</label>
                <div class="input-with-icon">
                    <i class="fas fa-user-circle"></i>
                    <input type="text" id="username" name="username" 
                           class="form-control" 
                           placeholder="admin o usuario1" 
                           required autofocus>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Contraseña</label>
                <div class="input-with-icon">
                    <i class="fas fa-key"></i>
                    <input type="password" id="password" name="password" 
                           class="form-control" 
                           placeholder="admin123 o usuario123" 
                           required>
                    <button type="button" class="password-toggle" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
            </button>
        </form>
        
        <div class="login-footer">
            <div class="system-info">Sistema SAINA © <?php echo date('Y'); ?></div>
            <div class="hint">
                <strong>Usuarios de prueba:</strong><br>
                • Admin: admin / admin123<br>
                • Usuario: juangel / juangel123
            </div>
        </div>
    </div>
    
    <script>
        // Mostrar/ocultar contraseña
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                icon.className = 'fas fa-eye';
            }
        });
        
        // Auto-focus
        document.getElementById('username').focus();
        
        // Auto-completar para pruebas
        document.getElementById('username').addEventListener('focus', function() {
            if (!this.value) {
                this.value = 'admin';
                document.getElementById('password').value = 'admin123';
            }
        });
    </script>
</body>
</html>