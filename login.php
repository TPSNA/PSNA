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
    <!-- Favicon agregado  -->
    <link rel="icon" type="image/png" sizes="32x32" href="../imagenes/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="../imagenes/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="../imagenes/favicon.icoo">
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
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
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
            position: relative;
            overflow: hidden;
        }
        
        /* Efectos de fondo */
        .background-pattern {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0.1;
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(106, 103, 240, 0.2) 2px, transparent 2px),
                radial-gradient(circle at 75% 75%, rgba(168, 160, 249, 0.2) 2px, transparent 2px);
            background-size: 50px 50px;
        }
        
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }
        
        .floating-circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
            animation: float 15s infinite ease-in-out;
        }
        
        .circle-1 {
            width: 300px;
            height: 300px;
            top: -150px;
            right: -150px;
            animation-delay: 0s;
        }
        
        .circle-2 {
            width: 200px;
            height: 200px;
            bottom: -100px;
            left: -100px;
            animation-delay: 5s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .login-box {
            background: var(--card-background);
            padding: 50px 40px;
            border-radius: 25px;
            box-shadow: 
                0 20px 40px rgba(0,0,0,0.15),
                0 0 0 1px rgba(255,255,255,0.1);
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 10;
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo-container {
            margin-bottom: 25px;
            position: relative;
        }
        
        .logo-img {
            max-width: 280px;
            height: auto;
            margin: 0 auto;
            display: block;
            filter: drop-shadow(0 5px 15px rgba(106, 103, 240, 0.3));
            transition: all 0.3s ease;
        }
        
        .logo-img:hover {
            transform: scale(1.02);
            filter: drop-shadow(0 8px 20px rgba(106, 103, 240, 0.4));
        }
        
        .login-header h1 {
            color: var(--text-color);
            margin-bottom: 8px;
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--purple-start), var(--blue-end));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .login-header p {
            color: var(--light-text);
            font-size: 15px;
            letter-spacing: 0.5px;
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
        
        .form-group label i { 
            color: var(--primary-color); 
            font-size: 16px; 
        }
        
        .input-with-icon { 
            position: relative; 
        }
        
        .input-with-icon i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--placeholder-color);
            font-size: 18px;
            z-index: 2;
        }
        
        .form-control {
            width: 100%;
            padding: 16px 50px 16px 50px;
            border: 2px solid #e6e6e6;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
            background: white;
            color: var(--text-color);
            position: relative;
            z-index: 1;
        }
        
        /* Padding especial para el campo de contraseña para acomodar el botón */
        .password-input {
            padding-right: 50px !important;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px var(--shadow-focus);
        }
        
        .form-control::placeholder { 
            color: var(--placeholder-color); 
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: var(--placeholder-color);
            padding: 8px;
            z-index: 2;
            transition: all 0.2s;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }
        
        .password-toggle:hover { 
            color: var(--primary-color); 
            background-color: rgba(106, 103, 240, 0.1);
            transform: translateY(-50%) scale(1.05);
        }
        
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
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(106, 103, 240, 0.4);
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .error-message {
            background: linear-gradient(135deg, rgba(255, 154, 158, 0.1), rgba(250, 208, 196, 0.1));
            color: var(--error-color);
            padding: 15px 20px;
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
            backdrop-filter: blur(5px);
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
            letter-spacing: 0.5px;
        }
        
        .hint {
            font-size: 12px;
            color: var(--light-text);
            margin-top: 8px;
            text-align: center;
            opacity: 0.7;
        }
        
        /* Animación de entrada para el formulario */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-box {
            animation: fadeInUp 0.8s ease-out;
        }
        
        /* Efecto de brillo en los inputs */
        .form-group:hover .form-control {
            border-color: var(--purple-start);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .login-box {
                padding: 40px 25px;
                max-width: 90%;
            }
            
            .logo-img {
                max-width: 220px;
            }
            
            .login-header h1 {
                font-size: 24px;
            }
        }
        
        @media (max-width: 480px) {
            .login-box {
                padding: 30px 20px;
            }
            
            .logo-img {
                max-width: 180px;
            }
            
            .login-header h1 {
                font-size: 22px;
            }
            
            .form-control {
                padding: 14px 45px 14px 45px;
            }
            
            .password-input {
                padding-right: 45px !important;
            }
            
            .password-toggle {
                right: 12px;
                width: 36px;
                height: 36px;
            }
            
            .circle-1, .circle-2 {
                display: none;
            }
        }
        
        /* Efecto de carga */
        .loading {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.9);
            border-radius: 25px;
            z-index: 20;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 15px;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--glass-border);
            border-top-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .loading-text {
            color: var(--text-color);
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="background-pattern"></div>
    <div class="floating-elements">
        <div class="floating-circle circle-1"></div>
        <div class="floating-circle circle-2"></div>
    </div>
    
    <div class="login-box">
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <div class="loading-text">Verificando credenciales...</div>
        </div>
        
        <div class="login-header">
            <div class="logo-container">
                <img src="../imagenes/Logo SAINA Horizontal.png" 
                     alt="SAINA Logo" 
                     class="logo-img">
            </div>
            <h1>Acceso al Sistema</h1>
            <p>Sistema de Gestión Integral SAINA</p>
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
                           class="form-control password-input" 
                           placeholder="Ingrese su contraseña" 
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
            <div class="system-info">Sistema SAINA © <?php echo date('Y'); ?> - Todos los derechos reservados</div>
            <div class="hint">
                Sistema de Administración Integral de Nómina y Archivo
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
                this.setAttribute('title', 'Ocultar contraseña');
            } else {
                passwordInput.type = 'password';
                icon.className = 'fas fa-eye';
                this.setAttribute('title', 'Mostrar contraseña');
            }
        });
        
        // Auto-focus
        document.getElementById('username').focus();
        
        // Animación de entrada para el logo
        document.addEventListener('DOMContentLoaded', function() {
            const logo = document.querySelector('.logo-img');
            logo.style.opacity = '0';
            logo.style.transform = 'scale(0.8)';
            
            setTimeout(() => {
                logo.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
                logo.style.opacity = '1';
                logo.style.transform = 'scale(1)';
            }, 300);
        });
        
        // Mostrar loading al enviar formulario
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const loading = document.getElementById('loading');
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (username && password) {
                loading.style.display = 'flex';
                
                // Simular delay mínimo para UX
                setTimeout(() => {
                    // El formulario se enviará normalmente
                }, 800);
            }
        });
        
        // Efecto de pulsación en el botón al hacer hover
        const loginBtn = document.querySelector('.btn-login');
        loginBtn.addEventListener('mouseenter', function() {
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = 'pulse 1.5s infinite';
            }, 10);
        });
        
        loginBtn.addEventListener('mouseleave', function() {
            this.style.animation = 'none';
        });
        
        // Auto-completar para pruebas (solo en desarrollo)
        document.getElementById('username').addEventListener('focus', function() {
            if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                if (!this.value) {
                    this.value = 'admin';
                    document.getElementById('password').value = 'admin123';
                    
                    // Animación de autocompletado
                    this.style.transition = 'all 0.5s ease';
                    this.style.backgroundColor = 'rgba(106, 103, 240, 0.1)';
                    setTimeout(() => {
                        this.style.backgroundColor = '';
                    }, 1000);
                }
            }
        });
        
        // Añadir estilo de animación para pulsación
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0% { transform: translateY(-3px) scale(1); box-shadow: 0 8px 20px rgba(106, 103, 240, 0.4); }
                50% { transform: translateY(-3px) scale(1.02); box-shadow: 0 12px 25px rgba(106, 103, 240, 0.5); }
                100% { transform: translateY(-3px) scale(1); box-shadow: 0 8px 20px rgba(106, 103, 240, 0.4); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>