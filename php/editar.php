<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Editar Empleado</title>
    <link rel="stylesheet" href="../css/editar.css"> <!-- Reutiliza estilos si es necesario -->
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
                <i class="fas fa-edit"></i> Editar Empleado 
            </div>
        </div>

        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- Campos anteriores sin cambios -->
            <div class="form-group">
                <label for="nacionalidad">Nacionalidad:</label>
                <input type="text" id="nacionalidad" name="nacionalidad" value="<?php echo htmlspecialchars($empleado['nacionalidad'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="ci">CI:</label>
                <input type="text" id="ci" name="ci" value="<?php echo htmlspecialchars($empleado['ci'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="primer_nombre">Primer Nombre:</label>
                <input type="text" id="primer_nombre" name="primer_nombre" value="<?php echo htmlspecialchars($empleado['primer_nombre'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="segundo_nombre">Segundo Nombre:</label>
                <input type="text" id="segundo_nombre" name="segundo_nombre" value="<?php echo htmlspecialchars($empleado['segundo_nombre'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="primer_apellido">Primer Apellido:</label>
                <input type="text" id="primer_apellido" name="primer_apellido" value="<?php echo htmlspecialchars($empleado['primer_apellido'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="segundo_apellido">Segundo Apellido:</label>
                <input type="text" id="segundo_apellido" name="segundo_apellido" value="<?php echo htmlspecialchars($empleado['segundo_apellido'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="fecha_nac">Fecha Nacimiento:</label>
                <input type="date" id="fecha_nac" name="fecha_nac" value="<?php echo htmlspecialchars($empleado['fecha_nacimiento'] ?? ''); ?>" required>
            </div>
            
            <!-- Corregido: Sexo con opciones que coinciden con la DB -->
            <div class="form-group">
                <label for="sexo">Sexo:</label>
                <select id="sexo" name="sexo" required>
                    <option value="">Selecciona una opción</option>
                    <option value="MASCULINO" <?php if (($empleado['sexo'] ?? '') == 'MASCULINO') echo 'selected'; ?>>MASCULINO</option>
                    <option value="FEMENINO" <?php if (($empleado['sexo'] ?? '') == 'FEMENINO') echo 'selected'; ?>>FEMENINO</option>
                </select>
            </div>
            
            <!-- Corregido: Estado Civil con opciones que coinciden con la DB -->
            <div class="form-group">
                <label for="estado_civil">Estado Civil:</label>
                <select id="estado_civil" name="estado_civil" required>
                    <option value="">Selecciona una opción</option>
                    <option value="SOLTERO(A)" <?php if (($empleado['estado_civil'] ?? '') == 'SOLTERO(A)') echo 'selected'; ?>>SOLTERO(A)</option>
                    <option value="CASADO(A)" <?php if (($empleado['estado_civil'] ?? '') == 'CASADO(A)') echo 'selected'; ?>>CASADO(A)</option>
                    <option value="DIVORCIADO(A)" <?php if (($empleado['estado_civil'] ?? '') == 'DIVORCIADO(A)') echo 'selected'; ?>>DIVORCIADO(A)</option>
                    <option value="VIUDO(A)" <?php if (($empleado['estado_civil'] ?? '') == 'VIUDO(A)') echo 'selected'; ?>>VIUDO(A)</option>
                </select>
            </div>
            
            <!-- Campos intermedios sin cambios -->
            <div class="form-group">
                <label for="direccion_ubicacion">Dirección Ubicación:</label>
                <input type="text" id="direccion_ubicacion" name="direccion_ubicacion" value="<?php echo htmlspecialchars($empleado['direccion_ubicacion'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($empleado['telefono'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($empleado['correo'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="cuenta_bancaria">Cuenta Bancaria:</label>
                <input type="text" id="cuenta_bancaria" name="cuenta_bancaria" value="<?php echo htmlspecialchars($empleado['cuenta_bancaria'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="tipo_trabajador">Tipo Trabajador:</label>
                <input type="text" id="tipo_trabajador" name="tipo_trabajador" value="<?php echo htmlspecialchars($empleado['tipo_trabajador'] ?? ''); ?>">
            </div>
            
            <!-- Corregido: Grado de Instrucción como select con opciones que coinciden con la DB -->
            <div class="form-group">
                <label for="grado_instruccion">Grado de Instrucción:</label>
                <select id="grado_instruccion" name="grado_instruccion" required>
                    <option value="">Selecciona una opción</option>
                    <option value="PRIMARIA" <?php if (($empleado['grado_instruccion'] ?? '') == 'PRIMARIA') echo 'selected'; ?>>PRIMARIA</option>
                    <option value="BACHILLER" <?php if (($empleado['grado_instruccion'] ?? '') == 'BACHILLER') echo 'selected'; ?>>BACHILLER</option>
                    <option value="TSU" <?php if (($empleado['grado_instruccion'] ?? '') == 'TSU') echo 'selected'; ?>>TSU</option>
                    <option value="LICENCIADO" <?php if (($empleado['grado_instruccion'] ?? '') == 'LICENCIADO') echo 'selected'; ?>>LICENCIADO</option>
                    <option value="INGENIERO" <?php if (($empleado['grado_instruccion'] ?? '') == 'INGENIERO') echo 'selected'; ?>>INGENIERO</option>
                    <option value="ESPECIALISTA" <?php if (($empleado['grado_instruccion'] ?? '') == 'ESPECIALISTA') echo 'selected'; ?>>ESPECIALISTA</option>
                    <option value="MAESTRIA" <?php if (($empleado['grado_instruccion'] ?? '') == 'MAESTRIA') echo 'selected'; ?>>MAESTRIA</option>
                    <option value="DOCTORADO" <?php if (($empleado['grado_instruccion'] ?? '') == 'DOCTORADO') echo 'selected'; ?>>DOCTORADO</option>
                    <option value="NINGUNO" <?php if (($empleado['grado_instruccion'] ?? '') == 'NINGUNO') echo 'selected'; ?>>NINGUNO</option>
                </select>
            </div>
            
            <!-- Resto de campos sin cambios -->
            <div class="form-group">
                <label for="cargo">Cargo:</label>
                <input type="text" id="cargo" name="cargo" value="<?php echo htmlspecialchars($empleado['cargo'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="sede">Sede:</label>
                <input type="text" id="sede" name="sede" value="<?php echo htmlspecialchars($empleado['sede'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="dependencia">Dependencia:</label>
                <input type="text" id="dependencia" name="dependencia" value="<?php echo htmlspecialchars($empleado['dependencia'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="fecha_ingreso">Fecha Ingreso:</label>
                <input type="date" id="fecha_ingreso" name="fecha_ingreso" value="<?php echo htmlspecialchars($empleado['fecha_ingreso'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="cod_siantel">Código SIANTEL:</label>
                <input type="text" id="cod_siantel" name="cod_siantel" value="<?php echo htmlspecialchars($empleado['cod_siantel'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="ubicacion_estante">Ubicación Estante:</label>
                <input type="text" id="ubicacion_estante" name="ubicacion_estante" value="<?php echo htmlspecialchars($empleado['ubicacion_estante'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="estatus">Estatus:</label>
                <input type="text" id="estatus" name="estatus" value="<?php echo htmlspecialchars($empleado['estatus'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="fecha_egreso">Fecha Egreso:</label>
                <input type="date" id="fecha_egreso" name="fecha_egreso" value="<?php echo htmlspecialchars($empleado['fecha_egreso'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="motivo_retiro">Motivo Retiro:</label>
                <input type="text" id="motivo_retiro" name="motivo_retiro" value="<?php echo htmlspecialchars($empleado['motivo_retiro'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="ubicacion_est_retiro">Ubicación Est. Retiro:</label>
                <input type="text" id="ubicacion_estante_retiro" name="ubicacion_estante_retiro" value="<?php echo htmlspecialchars($empleado['ubicacion_estante_retiro'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="tipo_sangre">Tipo Sangre:</label>
                <input type="text" id="tipo_sangre" name="tipo_sangre" value="<?php echo htmlspecialchars($empleado['tipo_sangre'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="lateralidad">Lateralidad:</label>
                <input type="text" id="lateralidad" name="lateralidad" value="<?php echo htmlspecialchars($empleado['lateralidad'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="peso">Peso:</label>
                <input type="number" step="0.01" id="peso_trabajador" name="peso_trabajador" value="<?php echo htmlspecialchars($empleado['peso_trabajador'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="altura">Altura:</label>
                <input type="number" step="0.01" id="altura_trabajador" name="altura_trabajador" value="<?php echo htmlspecialchars($empleado['altura_trabajador'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="talla_calzado">Talla Calzado:</label>
                <input type="text" id="calzado_trabajador" name="calzado_trabajador" value="<?php echo htmlspecialchars($empleado['calzado_trabajador'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="talla_camisa">Talla Camisa:</label>
                <input type="text" id="camisa_trabajador" name="camisa_trabajador" value="<?php echo htmlspecialchars($empleado['camisa_trabajador'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="talla_pantalon">Talla Pantalón:</label>
                <input type="text" id="pantalon_trabajador" name="pantalon_trabajador" value="<?php echo htmlspecialchars($empleado['pantalon_trabajador'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="foto">Foto (URL o ruta):</label>
                <input type="text" id="foto" name="foto" value="<?php echo htmlspecialchars($empleado['foto'] ?? ''); ?>">
            </div>
            <button type="submit">Actualizar Empleado</button>
        </form>
    </div>
</body>
</html>
