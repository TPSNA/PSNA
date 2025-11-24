<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAINA REGISTRO</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="fixed-header">
        <div class="header-content">
            <div class="left-group">
                <!-- Junagelyn_sanchez -->
                <!-- Menú hamburguesa -->
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
            
            <!-- Icono para cerrar sesión -->
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
    
        <div class="form-card">
            <nav class="steps-indicator">
                
                <div class="step active" data-step="1">
                    <span class="step-number">1</span>
                    <span class="step-text">Datos Personales</span>
                </div>
                <div class="step" data-step="2">
                    <span class="step-number">2</span>
                    <span class="step-text">Informacion Laboral</span>
                </div>
                <div class="step" data-step="3">
                    <span class="step-number">3</span>
                    <span class="step-text">Informacion General</span>
                </div>
                <div class="step" data-step="4">
                    <span class="step-number">4</span>
                    <span class="step-text">Familia</span>
                </div>
                 <div class="step" data-step="5">
                    <span class="step-number">5</span>
                    <span class="step-text">Foto</span>
                </div>
            </nav>

            <form action="../php/registro_formulario.php" method="POST" enctype="multipart/form-data" id="registration-form" class="registration-form">
                <!-- Paso 1: Datos personales -->
                <div class="step-content active" data-step="1">
                    <p class="step-label">Paso 1</p>
                    <h2>Datos Personales</h2>
                    <p class="step-description">A continuación, complete la información personal del empleado.</p>
                    <div class="form-grid">
                         <div class="input-group">
                            <label for="nacionalidad">Nacionalidad</label>
                            <select id="nacionalidad" name="nacionalidad" required>
                                <option value="">Selecciona una opción</option>
                                <option value="extranjero(a)">EXTRANJERO(A)</option>
                                <option value="venezolano(a)">VENEZOLANO(A)</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="cedula">Cedula de Identidad</label>
                            <input type="text" id="ci" name="ci" placeholder="Ingresa CI" required>
                        </div>
                        <div class="input-group">
                            <label for="primer_nombre">Primer Nombre</label>
                            <input type="text" id="primer_nombre" name="primer_nombre" placeholder="Ingresa Primer Nombre" required>
                        </div>
                        <div class="input-group">
                            <label for="segundo_nombre">Segundo Nombre</label>
                            <input type="text" id="segundo_nombre" name="segundo_nombre" placeholder="Ingresa Segundo Nombre" required>
                        </div>
                        <div class="input-group">
                            <label for="primer_apellido">Primer Apellido</label>
                            <input type="text" id="primer_apellido" name="primer_apellido" placeholder="Ingresa Primer Apellido" required>
                        </div>
                        <div class="input-group">
                            <label for="segundo_apellido">Segundo Apellido</label>
                            <input type="text" id="segundo_apellido" name="segundo_apellido" placeholder="Ingresa segundo Apellido" required>
                        </div>
                        <div class="input-group">
                            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
                        </div>
                       <div class="input-group">
                            <label for="sexo">Sexo</label>
                            <select id="sexo" name="sexo" required>
                                <option value="">Selecciona una opción</option>
                                <option value="masculino">MASCULINO</option>
                                <option value="femenino">FEMENINO</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="estado_civil">Estado Civil</label>
                            <select id="estado_civil" name="estado_civil" required>
                                <option value="">Selecciona una opción</option>
                                <option value="soltero(a)">SOLTERO(A)</option>
                                <option value="casado(a)">CASADO(A)</option>
                                <option value="divorciado(a)">DIVORCIADO(A)</option>
                                <option value="viudo(a)">VIUDO(A)</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="direccion_ubicacion">Direccion de Ubicacion</label>
                            <input type="text" id="direccion_ubicacion" name="direccion_ubicacion" placeholder="Ingresa Direccion" required>
                        </div>
                        <div class="input-group">
                            <label for="telefono">Numero de Telefono</label>
                            <input type="tel" id="telefono" name="telefono" placeholder="Ingrese el numero telefonico" required>
                        </div>
                        <div class="input-group">
                            <label for="correo">Correo</label>
                            <input type="email" id="correo" name="correo" placeholder="Ingrese Correo Electronico" required>
                        </div>
                        <div class="input-group">
                            <label for="cuenta_bancaria">Cuenta Bancaria</label>
                            <input type="text" id="cuenta_bancaria" name="cuenta_bancaria" placeholder="Ingresa cuenta bancaria" required>
                        </div>
                    </div>
                </div>

                <!-- Paso 2: Informacion Laboral -->
                 <!-- Junagelyn_sanchez -->
                <div class="step-content" data-step="2">
                    <p class="step-label">Paso 2</p>
                    <h2>Informacion Laboral</h2>
                    <p class="step-description">Para continuar con el registro, por favor ingrese los siguientes datos.</p>
                    <div class="form-grid">
                        <div class="input-group">
                            <label for="tipo_trabajador">Tipo de Trabajador</label>
                            <select id="tipo_trabajador" name="tipo_trabajador" required>
                                <option value="">Selecciona una opción</option>
                                <option value="ctd">CTD</option>
                                <option value="cti">CTI</option>
                                <option value="lnr">LNR</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="grado_instruccion">Grado de Instrucción</label>
                            <select id="grado_instruccion" name="grado_instruccion" required>
                                <option value="">Selecciona una opción</option>
                                <option value="primaria">PRIMARIA</option>
                                <option value="bachiller">BACHILLER</option>
                                <option value="tecnico_profesional">TSU</option>
                                <option value="licenciado">LICENCIADO</option>
                                <option value="ingeniero">INGENIERO</option>
                                <option value="especialista">ESPECIALISTA</option>
                                <option value="maestria">MAESTRIA</option>
                                <option value="doctorado">DOCTORADO</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="cargo">Cargo</label>
                            <select id="cargo" name="cargo" required>
                                <option value="">Selecciona una opción</option>
                                <option value="DIRECTOR GENERAL">DIRECTOR GENERAL</option>
                                <option value="COORDINADOR DE PRESUPUESTO">COORD. DE PRESUPUESTO</option>
                                <option value="COORDINADOR DE DESARROLLO ORGANIZACIONAL">COORD. DE DESARROLLO ORG.</option>
                                <option value="COORDINADOR DE ARCHIVOS">COORD. DE ARCHIVOS</option>
                                <option value="COORDINADOR DE SEGURIDAD">COORDINADOR DE SEGURIDAD</option>
                                <option value="DIRECTOR DE ASUNTOS JURIDICOS">DIR. DE ASUNTOS JURIDICOS</option>
                                <option value="COORDINADOR DE CONTROL Y GESTION">COORD. DE CONTROL Y GESTION</option>
                                <option value="COORDINADOR DE TRANSPORTE">COORD. DE TRANSPORTE</option>
                                <option value="DIRECTOR TALENTO HUMANO">DIR. TALENTO HUMANO</option>
                                <option value="DIRECTOR DESARROLLO HUMANO INTEGRAL">DIR. DESARROLLO HUMANO INTEGRAL</option>
                                <option value="COORDINADOR DE BIENES">COORD. DE BIENES</option>
                                <option value="DIRECTOR DE ADMINISTRACION Y FINANZAS">DIR. DE ADMINISTRACION Y FINANZAS</option>
                                <option value="DIRECTOR DE REINSERCION SOCIAL">DIR. DE REINSERCION SOCIAL</option>
                                <option value="DIRECTOR DE PLANIFICACION PRESUPUESTO Y DESARROLLO ORGANIZACIONAL">DIR. DE PLAN. PRESUP. Y DESARROLLO ORG.</option>
                                <option value="DIRECTOR DE OPERACIONES">DIR. DE OPERACIONES</option>
                                <option value="DIRECTOR DE BIENESTAR SOCIAL">DIR. DE BIENESTAR SOCIAL</option>
                                <option value="COORDINADOR DE NUTRICION">COORD. DE NUTRICION</option>
                                <option value="DIRECTOR COMUNICACIÓN Y REDES SOCIALES">DIR. COMUNICACIÓN Y RRSS</option>
                                <option value="COORDINADOR DE TALENTO HUMANO">COORD. DE TALENTO HUMANO</option>
                                <option value="DIRECTOR DE TECNOLOGIA INFORMATICA Y TELECOMUNICACIONES">DIR. DE TIT</option>
                                <option value="DIRECTOR DE ATENCION AL CIUDADANO">DIR. DE ATENCION AL CIUDADANO</option>
                                <option value="COORDINADOR DE AREA">COORD. DE AREA</option>
                                <option value="COORDINADOR DE SERVICIOS GENERALES ">COORD. DE SERVICIOS GENERALES</option>
                                <option value="DIRECTOR DE CENTRO">DIR. DE CENTRO</option>
                                <option value="COORDINADOR DE CENTRO ">COORD. DE CENTRO</option>
                                <option value="GUIA FACILITADOR">GUIA FACILITADOR</option>
                                <option value="ASISTENTE DE OFICINA">ASISTENTE DE OFICINA</option>
                                <option value="CONTADOR">CONTADOR</option>
                                <option value="ABOGADO">ABOGADO</option>
                                <option value="INSTRUCTOR DE FORMACION PROFESIONAL">INSTRUCTOR DE FORM. PROF.</option>
                                <option value="CAPELLAN">CAPELLAN</option>
                                <option value="MEDICO">MEDICO</option>
                                <option value="ENFERMERA">ENFERMERA</option>
                                <option value="PSICOLOGO">PSICOLOGO</option>
                                <option value="TRABAJADOR SOCIAL">TRABAJADOR SOCIAL</option>
                                <option value="TERAPEUTA">TERAPEUTA</option>
                                <option value="MANTENIMIENTO">MANTENIMIENTO</option>
                                <option value="COCINERO">COCINERO</option>
                                <option value="VIGILANTE">VIGILANTE</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="sede">Sede</label>
                            <select id="sede" name="sede" required>
                                <option value="">Selecciona una opción</option>
                                <option value="admin">ADMIN</option>
                                <option value="cafo">CAFO</option>
                                <option value="cate">CATE</option>
                                <option value="csai">CSAI</option>
                                <option value="csb">CSB</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="dependencia">Dependencia</label>
                            <select id="dependencia" name="dependencia" required>
                                <option value="">Selecciona una opción</option>
                                <option value="admin">ADMIN</option>
                                <option value="cafo">CAFO</option>
                                <option value="cate">CATE</option>
                                <option value="csai">CSAI</option>
                                <option value="csb">CSB</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="fecha_ingreso">Fecha de Ingreso</label>
                            <input type="date" id="fecha_ingreso" name="fecha_ingreso" required>
                        </div>
                        <div class="input-group">
                            <label for="codigo_siantel">Codigo Carnet SIANTEL</label>
                            <input type="text" id="cod_siantel" name="cod_siantel" placeholder="Ingrese el codigo" required>
                        </div>
                        <div class="input-group">
                            <label for="ubicacionEstante">Ubicación estante</label>
                            <input type="text" id="ubicacionEstante" name="ubicacion_estante" placeholder="Ej: Estante A-5">
                        </div>
                        <div class="input-group">
                            <label for="estatus">Estatus</label>
                            <select id="estatus" name="estatus" required>
                                <option value="">Selecciona un estatus</option>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                        <!-- Formulario adicional (oculto por defecto) -->
                         <!-- Junagelyn_sanchez -->
                        <div id="formularioExtra" class="hidden">
                            <h3>Detalles de Retiro</h3>
                            <div class="form-grid">
                                <div class="input-group">
                                    <label for="fechaEgreso">Fecha de egreso</label>
                                    <input type="date" id="fechaEgreso" name="fecha_egreso">
                                </div>
                                <div class="input-group">
                                    <label for="motivoRetiro">Motivo del retiro</label>
                                    <textarea id="motivoRetiro" name="motivo_retiro" rows="3" placeholder="Describe el motivo"></textarea>
                                </div>
                                <div class="input-group">
                                    <label for="ubicacionEstanteRetiro">Ubicación estante (Retiro)</label>
                                    <input type="text" id="ubicacionEstanteRetiro" name="ubicacion_estante_retiro" placeholder="Ej: Estante A-5">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paso 3: Informacion General -->
                 <!-- Junagelyn_sanchez -->
                <div class="step-content" data-step="3">
                    <p class="step-label">Paso 3</p>
                    <h2>Informacion General</h2>
                    <p class="step-description">Agregue información general del empleado.</p>
                    <div class="form-grid">
                        <div class="input-group">
                            <label for="tipo_sangre">Tipo de Sangre</label>
                            <select id="tipo_sangre" name="tipo_sangre" required>
                                <option value="">Selecciona una opcion</option>
                                <option value="a+">A+</option>
                                <option value="a-">A-</option>
                                <option value="b+">B+</option>
                                <option value="b-">B-</option>
                                <option value="ab+">AB+</option>
                                <option value="ab-">AB-</option>
                                <option value="o+">O+</option>
                                <option value="o-">O-</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="lateralidad">Lateralidad</label>
                            <select id="lateralidad" name="lateralidad" required>
                                <option value="">Selecciona una opcion</option>
                                <option value="diestro(a)">DIESTRO(A)</option>
                                <option value="zurdo(a)">ZURDO(A)</option>
                                
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="peso_trabajador">Peso del Trabajador</label>
                            <input type="text" id="peso_trabajador" name="peso_trabajador" placeholder="Ingrese el peso del trabajador">
                        </div>
                        <div class="input-group">
                            <label for="altura_trabajador">Altura del Trabajador</label>
                            <input type="text" id="altura_trabajador" name="altura_trabajador" placeholder="Ingrese la altura del trabajador">
                        </div>
                        <div class="input-group">
                            <label for="talla_calzado">Talla calzado del trabajador</label>
                            <input type="text" id="calzado_trabajador" name="calzado_trabajador" placeholder="Ingrese la talla calzado del trabajador">
                        </div>
                        <div class="input-group">
                            <label for="talla_camisa">Talla camisa del Trabajador</label>
                            <input type="text" id="camisa_trabajador" name="camisa_trabajador" placeholder="Ingrese la talla camisa del trabajador">
                        </div>
                        <div class="input-group">
                            <label for="talla_pantalon">Talla pantalon del Trabajador</label>
                            <input type="text" id="pantalon_trabajador" name="pantalon_trabajador" placeholder="Ingrese la talla  pantalon del trabajador">
                        </div>
                    </div>
                </div>

              <!-- Paso 4: Familia -->
               <!-- Junagelyn_sanchez -->
<div class="step-content" data-step="4">
    <p class="step-label">Paso 4</p>
    <h2>Familia</h2>
    <p class="step-description">Indica el número de carga familiar y completa los datos para cada miembro.</p>
    <div class="form-grid">
        <div class="input-group">
            <label for="num_familiares">Número de Carga Familiar</label>
            <input type="number" id="num_familiares" min="0" max="10" placeholder="Ej: 2">
            <button type="button" id="generar-familiares">Generar Campos</button>
            <button type="button" id="reiniciar-familiares">Reiniciar Campos</button>  
        </div>
        <!-- Contenedor para los campos dinámicos -->
        <div id="familiares-container"></div>
    </div>
</div>

                <!-- Paso 5: Foto -->
                 <!-- Junagelyn_sanchez -->
                <div class="step-content" data-step="5">
                            <p class="step-label">Paso 5</p>
                            <h2>Foto</h2>
                            <p class="step-description">Agregue la foto del empleado.</p>
                    <div class="form-grid">
                        <div class="input-group">
                             <input type="file" id="foto" name="foto" accept="image/*" required style="display: none;">
                            <div id="preview" class="photo-preview">
                              <p>Haz clic para seleccionar una foto</p>
                            </div>
                        </div>
                    </div>
                </div>

                 

                <div class="form-actions">
                    <button type="button" id="prev-btn" class="btn secondary" disabled>Atras</button>
                    <button type="button" id="next-btn" class="btn primary">Continuar</button>
                    <button type="submit" id="submit-btn" class="btn primary" style="display: none;">Registrar</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Botón flotante para scroll -->
     <!-- Junagelyn_sanchez -->
    <button id="scroll-btn" class="scroll-btn" title="Desplazar hacia arriba/abajo">
        ↑
    </button>

    <script src="../javascrip/formulario1.js"></script>
</body>
</html>
