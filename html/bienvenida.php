<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAINA BIENVENIDA</title>
    <!-- Agregar Font Awesome para que se vea el icono -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/bienvenida.css">
</head>
<body>
    <header class="fixed-header">
        <div class="header-content">
            <div class="left-group">
                <!-- Menú hamburguesa -->
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
                
                <!-- Logo -->
                <div class="logo-container">
                    <img src="../imagen/2 - Logo SAINA Horizontal.png" alt="Logo Principal" class="logo">
                </div>
            </div>
            
                <!-- CERRAR SESIÓN -->
        <div class="logout-section">
            <a href="logout.php" class="logout-btn">
                <div class="nav-icon"><i class="fas fa-sign-out-alt"></i></div>
                <div class="nav-text">Cerrar Sesión</div>
            </a>
        </div>

    </header>
   
    <main class="main-content">
        
        <section class="welcome-card">
            <div class="welcome-text">
                <h1>BIENVENIDO AL SISTEMA DE REGISTRO DE TRABAJADORES</h1>
                <p>DEPARTAMENTO DE ARCHIVOS</p>
                
            </div>
            <img src="../imagen/archivo (2).png" alt=""> 
        </section>

        <nav class="options-menu">
            
            <a href="../html/formulario1.php" class="option-item">
              <img src="../imagen/formulario.png" alt="">
                <span class="option-text">FORMULARIO DE REGISTRO</span>
            </a>
            
            <a href="../html/tabla_datos.php" class="option-item">
                <img src="../imagen/historico_registros.png" alt="">
                <span class="option-text">HISTÓRICO DE REGISTROS</span>
            </a>
          
            
            <a href="../html/" class="option-item">
                <img src="../imagen/icono_siantel.png" alt="">
                <span class="option-text">GENERAR CARNET SIANTEL</span>
            </a>
            
        </nav>


    </aside>

    
    </main>
</body>
</html>