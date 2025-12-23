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
            
            <!-- Icono para cerrar sesión -->

            <div class="logout-section">
            <a href="logout.php" class="logout-btn">
                <div class="nav-icon"><i class="fas fa-sign-out-alt"></i></div>
                <div class="nav-text">Cerrar Sesión</div>
            </a>
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
                            <select id="nacionalidad" name="nacionalidad" >
                                <option value="">Selecciona una opción</option>
                                <option value="EXTRANJERO(A)">EXTRANJERO(A)</option>
                                <option value="VENEZOLANO(A)">VENEZOLANO(A)</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="cedula">Cedula de Identidad</label>
                            <input type="text" id="ci" name="ci" placeholder="Ingresa CI">
                        </div>
                        <div class="input-group">
                            <label for="primer_nombre">Primer Nombre</label>
                            <input type="text" id="primer_nombre" name="primer_nombre" placeholder="Ingresa Primer Nombre" >
                        </div>
                        <div class="input-group">
                            <label for="segundo_nombre">Segundo Nombre</label>
                            <input type="text" id="segundo_nombre" name="segundo_nombre" placeholder="Ingresa Segundo Nombre" >
                        </div>
                        <div class="input-group">
                            <label for="primer_apellido">Primer Apellido</label>
                            <input type="text" id="primer_apellido" name="primer_apellido" placeholder="Ingresa Primer Apellido" >
                        </div>
                        <div class="input-group">
                            <label for="segundo_apellido">Segundo Apellido</label>
                            <input type="text" id="segundo_apellido" name="segundo_apellido" placeholder="Ingresa segundo Apellido" >
                        </div>
                        <div class="input-group">
                            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" >
                        </div>
                       <div class="input-group">
                            <label for="sexo">Sexo</label>
                            <select id="sexo" name="sexo" >
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="MASCULINO">MASCULINO</option>
                                <option value="FEMENINO">FEMENINO</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="estado_civil">Estado Civil</label>
                            <select id="estado_civil" name="estado_civil" >
                                <option value="">Selecciona una opción</option>
                                <option value="SOLTERO(A)">SOLTERO(A)</option>
                                <option value="CASADO(A)">CASADO(A)</option>
                                <option value="DIVORCIADO(A)">DIVORCIADO(A)</option>
                                <option value="VIUDO(A)">VIUDO(A)</option>
                            </select>
                        </div>
                        <div class="input-group">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado_id" disabled>
                            <option value="1" selected>Lara</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="municipio">Municipio</label>
                        <select id="municipio" name="municipio_id" >
                            <option value="">Selecciona un municipio</option>
                            <!-- Opciones se cargan dinámicamente -->
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="parroquia">Parroquia</label>
                        <select id="parroquia" name="parroquia_id" >
                            <option value="">Selecciona una Parroquia</option>
                            <!-- Opciones se cargan dinámicamente -->
                        </select>
                    </div>

                        <div class="input-group">
                            <label for="direccion_ubicacion">Direccion de Ubicacion</label>
                            <input type="text" id="direccion_ubicacion" name="direccion_ubicacion" placeholder="Ingresa Direccion" >
                        </div>
                        <div class="input-group">
                            <label for="telefono">Numero de Telefono</label>
                            <input type="tel" id="telefono" name="telefono" placeholder="Ingrese el numero telefonico" >
                        </div>
                        <div class="input-group">
                            <label for="correo">Correo</label>
                            <input type="email" id="correo" name="correo" placeholder="Ingrese Correo Electronico" >
                        </div>
                        <div class="input-group">
                            <label for="cuenta_bancaria">Cuenta Bancaria</label>
                            <input type="text" id="cuenta_bancaria" name="cuenta_bancaria" placeholder="Ingresa cuenta bancaria" >
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
                            <select id="tipo_trabajador" name="tipo_trabajador" >
                                <option value="">Selecciona una opción</option>
                                <option value="CTD">CTD</option>
                                <option value="CTI">CTI</option>
                                <option value="LNR">LNR</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="grado_instruccion">Grado de Instrucción</label>
                            <select id="grado_instruccion" name="grado_instruccion" >
                                <option value="">Selecciona una opción</option>
                                <option value="PRIMARIA">PRIMARIA</option>
                                <option value="BACHILLER">BACHILLER</option>
                                <option value="TSU">TSU</option>
                                <option value="LICENCIADO">LICENCIADO</option>
                                <option value="INGENIERO">INGENIERO</option>
                                <option value="ESPECIALISTA">ESPECIALISTA</option>
                                <option value="MAESTRIA">MAESTRIA</option>
                                <option value="DOCTORADO">DOCTORADO</option>
                                <option value="NINGUNO">NINGUNO</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="cargo">Cargo</label>
                            <select id="cargo" name="cargo" >
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
                            <select id="sede" name="sede" >
                                <option value="">Selecciona una opción</option>
                                <option value="ADMIN">ADMIN</option>
                                <option value="CAFO">CAFO</option>
                                <option value="CATE">CATE</option>
                                <option value="CSAI">CSAI</option>
                                <option value="CSB">CSB</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="dependencia">Dependencia</label>
                            <select id="dependencia" name="dependencia" >
                                <option value="">Selecciona una opción</option>
                                <option value="ADMIN">ADMIN</option>
                                <option value="CAFO">CAFO</option>
                                <option value="CATE">CATE</option>
                                <option value="CSAI">CSAI</option>
                                <option value="CSB">CSB</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="fecha_ingreso">Fecha de Ingreso</label>
                            <input type="date" id="fecha_ingreso" name="fecha_ingreso" >
                        </div>
                        <div class="input-group">
                            <label for="codigo_siantel">Codigo Carnet SIANTEL</label>
                            <input type="text" id="cod_siantel" name="cod_siantel" placeholder="Ingrese el codigo" >
                        </div>
                        <div class="input-group">
                            <label for="ubicacionEstante">Ubicación estante</label>
                            <input type="text" id="ubicacionEstante" name="ubicacion_estante" placeholder="Ej: Estante A-5">
                        </div>
                        <div class="input-group">
                            <label for="estatus">Estatus</label>
                            <select id="estatus" name="estatus" >
                                <option value="">Selecciona un estatus</option>
                                <option value="ACTIVO">ACTIVO</option>
                                <option value="INACTIVO">INACTIVO</option>
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
                            <select id="tipo_sangre" name="tipo_sangre" >
                                <option value="">Selecciona una opcion</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="lateralidad">Lateralidad</label>
                            <select id="lateralidad" name="lateralidad" >
                                <option value="">Selecciona una opcion</option>
                                <option value="DIESTRO(A)">DIESTRO(A)</option>
                                <option value="ZURDO(A)">ZURDO(A)</option>
                                
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
<!-- Paso 4: Familia -->
<div class="step-content" data-step="4">
    <p class="step-label">Paso 4</p>
    <h2>Familia</h2>
    <p class="step-description">Indica el número de carga familiar y completa los datos para cada miembro. Puedes agregar familiares uno por uno o eliminarlos individualmente.</p>
    <div class="form-grid">
        <div class="input-group">
            <label for="num_familiares">Número de Carga Familiar (Referencia)</label>
           
            <button type="button" id="agregar-familiar">Agregar Familiar</button>
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
                             <input type="file" id="foto" name="foto" accept="image/*"  style="display: none;">
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
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
