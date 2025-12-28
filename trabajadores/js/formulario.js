document.addEventListener('DOMContentLoaded', () => {
    // ============================================
    // VARIABLES GLOBALES Y CONFIGURACIÓN
    // ============================================
    const steps = document.querySelectorAll('.step');
    const stepContents = document.querySelectorAll('.step-content');
    const nextBtn = document.getElementById('next-btn');
    const prevBtn = document.getElementById('prev-btn');
    const submitBtn = document.getElementById('submit-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const form = document.getElementById('registration-form');
    let currentStep = 1;

    // ============================================
    // MENÚ MÓVIL
    // ============================================
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const navMenu = document.getElementById('nav-menu');
    
    if (mobileMenuBtn && navMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            mobileMenuBtn.innerHTML = navMenu.classList.contains('active') 
                ? '<i class="fas fa-times"></i>' 
                : '<i class="fas fa-bars"></i>';
        });
        
        // Cerrar menú al hacer clic en un enlace
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
            });
        });
    }

    // ============================================
    // FORMULARIO EXTRA PARA ESTATUS INACTIVO
    // ============================================
    const estatusSelect = document.getElementById('estatus');
    const formularioExtra = document.getElementById('formularioExtra');

    // Función para mostrar/ocultar formulario extra según estatus
    function toggleFormularioExtra() {
        if (estatusSelect && formularioExtra) {
            if (estatusSelect.value === 'INACTIVO') {
                formularioExtra.classList.remove('hidden');
                formularioExtra.classList.add('show');
            } else {
                formularioExtra.classList.add('hidden');
                formularioExtra.classList.remove('show');
            }
        }
    }

    if (estatusSelect && formularioExtra) {
        // Event listener para cambios en el select
        estatusSelect.addEventListener('change', toggleFormularioExtra);
        
        // Ejecutar al cargar la página
        toggleFormularioExtra();
    }

    // ============================================
    // SISTEMA DE PASOS DEL FORMULARIO
    // ============================================
    
    // Actualizar visualización de pasos
    function updateSteps() {
        // Actualizar indicadores de pasos
        steps.forEach(step => {
            step.classList.remove('active', 'completed');
            const stepNum = parseInt(step.dataset.step);
            
            if (stepNum === currentStep) {
                step.classList.add('active');
            } else if (stepNum < currentStep) {
                step.classList.add('completed');
            }
        });

        // Actualizar contenido de pasos
        stepContents.forEach(content => {
            content.classList.remove('active');
            if (parseInt(content.dataset.step) === currentStep) {
                content.classList.add('active');
                // Scroll suave al paso activo
                setTimeout(() => {
                    content.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            }
        });

        // Actualizar botones
        prevBtn.disabled = currentStep === 1;
        
        // Mostrar/ocultar botones según el paso
        nextBtn.style.display = currentStep === 5 ? 'none' : 'flex';
        submitBtn.style.display = currentStep === 5 ? 'flex' : 'none';

        // Actualizar textos
        if (currentStep < 5) {
            nextBtn.innerHTML = 'Siguiente Paso <i class="fas fa-arrow-right"></i>';
        } else {
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Registrar Trabajador';
        }

        // Actualizar botón de scroll
        updateScrollBtnVisibility();
    }

// Validar paso actual
function validateStep(step = 'all') {
    let inputs;
    if (step === 'all') {
        // Validar todos los campos requeridos del formulario
        inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    } else {
        // Validar solo el paso especificado
        const stepContent = document.querySelector(`.step-content[data-step="${step}"]`);
        if (!stepContent) return true;
        inputs = stepContent.querySelectorAll('input[required], select[required], textarea[required]');
    }
    
    let isValid = true;
    inputs.forEach(input => {
        // EXCLUIR LOS CAMPOS QUE NO SON OBLIGATORIOS
        const excludedFields = [
            'direccion_ubicacion',
            'telefono',
            'correo',
            'cuenta_bancaria',
            'tipo_sangre'
        ];
        
        const fieldName = input.name || input.id || '';
        
        // Verificar si este campo debe ser excluido
        let shouldExclude = false;
        for (const field of excludedFields) {
            if (fieldName.toLowerCase().includes(field.toLowerCase()) || 
                input.id.toLowerCase().includes(field.toLowerCase())) {
                shouldExclude = true;
                break;
            }
        }
        
        if (shouldExclude) {
            // Si está en la lista de excluidos, NO validar
            input.classList.remove('error');
            const errorMsg = input.parentElement.querySelector('.error-message');
            if (errorMsg) errorMsg.remove();
            return; // Saltar a la siguiente iteración
        }
        
        // Validar solo si no está excluido
        if (!input.value.trim()) {
            input.classList.add('error');
            isValid = false;
            
            // Agregar mensaje de error visual
            let errorMsg = input.parentElement.querySelector('.error-message');
            if (!errorMsg) {
                errorMsg = document.createElement('div');
                errorMsg.className = 'error-message';
                errorMsg.style.color = '#ff6b6b';
                errorMsg.style.fontSize = '12px';
                errorMsg.style.marginTop = '5px';
                input.parentElement.appendChild(errorMsg);
            }
            errorMsg.textContent = 'Este campo es requerido';
        } else {
            input.classList.remove('error');
            const errorMsg = input.parentElement.querySelector('.error-message');
            if (errorMsg) errorMsg.remove();
        }
    });
    return isValid;
}

    // Navegación entre pasos
    nextBtn.addEventListener('click', () => {
        if (validateStep(currentStep)) {
            if (currentStep < 5) {
                currentStep++;
                updateSteps();
            }
        } else {
            alert('Por favor complete todos los campos requeridos antes de continuar.');
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            updateSteps();
        }
    });

    // Navegación directa a cualquier paso
    steps.forEach(step => {
        step.addEventListener('click', () => {
            const stepNum = parseInt(step.dataset.step);
            if (stepNum < currentStep || confirm('¿Está seguro? Perderá los cambios no guardados del paso actual.')) {
                currentStep = stepNum;
                updateSteps();
            }
        });
    });

    // ============================================
    // BOTÓN CANCELAR
    // ============================================
    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            if (confirm('¿Está seguro de cancelar el registro? Se perderán todos los datos ingresados.')) {
                window.location.href = '../index.php';
            }
        });
    }

    // ============================================
    // PREVIEW DE FOTO
    // ============================================
    const preview = document.getElementById('preview');
    const fotoInput = document.getElementById('foto');
    const btnSeleccionarFoto = document.getElementById('btn-seleccionar-foto');

    if (preview && fotoInput) {
        // Abrir selector de archivos
        const openFileSelector = () => fotoInput.click();
        
        if (btnSeleccionarFoto) {
            btnSeleccionarFoto.addEventListener('click', openFileSelector);
        }
        preview.addEventListener('click', openFileSelector);

        // Manejar selección de archivo
        fotoInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                // Validar tamaño (5MB máximo)
                if (file.size > 5 * 1024 * 1024) {
                    alert('El archivo es demasiado grande. Máximo 5MB.');
                    this.value = '';
                    return;
                }

                // Validar tipo de archivo
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    alert('Formato de archivo no válido. Use JPG, PNG o GIF.');
                    this.value = '';
                    return;
                }

                // Mostrar preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                    preview.style.border = 'none';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // ============================================
    // GESTIÓN DE FAMILIARES
    // ============================================
    let familiarCounter = 0;

    // Agregar familiar
function agregarFamiliar() {
    if (familiarCounter >= 10) {
        alert('Máximo 10 familiares permitidos.');
        return;
    }

    familiarCounter++;
    const container = document.getElementById('familiares-container');
    
    const familiarDiv = document.createElement('div');
    familiarDiv.className = 'familiar-item';
    familiarDiv.setAttribute('data-familiar-id', familiarCounter);
    
    const today = new Date().toISOString().split('T')[0];
    
    familiarDiv.innerHTML = `
        <div class="familiar-header">
            <h4><i class="fas fa-user"></i> Familiar ${familiarCounter}</h4>
            <button type="button" class="eliminar-familiar btn danger" data-id="${familiarCounter}">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        </div>
        <div class="form-grid">
            <div class="input-group">
                <label for="cedula_familiar_${familiarCounter}"><i class="fas fa-id-card"></i> Cédula Familiar</label>
                <input type="text" id="cedula_familiar_${familiarCounter}" name="cedula_familiar[]" placeholder="Ej: V-12345678">
            </div>
            <div class="input-group">
                <label for="nombre_familiar_${familiarCounter}"><i class="fas fa-user"></i> Nombre</label>
                <input type="text" id="nombre_familiar_${familiarCounter}" name="nombre_familiar[]" placeholder="Nombre del familiar">
            </div>
            <div class="input-group">
                <label for="apellido_familiar_${familiarCounter}"><i class="fas fa-user"></i> Apellido</label>
                <input type="text" id="apellido_familiar_${familiarCounter}" name="apellido_familiar[]" placeholder="Apellido del familiar">
            </div>
            <div class="input-group">
                <label for="parentesco_${familiarCounter}"><i class="fas fa-heart"></i> Parentesco</label>
                <select id="parentesco_${familiarCounter}" name="parentesco[]">
                    <option value="">Selecciona</option>
                    <option value="ESPOSO/A">ESPOSO/A</option>
                    <option value="HIJO/A">HIJO/A</option>
                    <option value="PADRE">PADRE</option>
                    <option value="MADRE">MADRE</option>
                    <option value="OTRO">OTRO</option>
                </select>
            </div>
            <div class="input-group">
                <label for="edad_${familiarCounter}"><i class="fas fa-birthday-cake"></i> Edad</label>
                <input type="number" id="edad_${familiarCounter}" name="edad[]" min="0" max="120" placeholder="Edad">
            </div>
            <div class="input-group">
                <label for="peso_${familiarCounter}"><i class="fas fa-weight"></i> Peso (kg)</label>
                <input type="number" id="peso_${familiarCounter}" name="peso[]" step="0.1" min="0" max="200" placeholder="Peso en kg">
            </div>
            <div class="input-group">
                <label for="altura_${familiarCounter}"><i class="fas fa-ruler-vertical"></i> Altura (cm)</label>
                <input type="number" id="altura_${familiarCounter}" name="altura[]" step="0.1" min="0" max="250" placeholder="Altura en cm">
            </div>
            <div class="input-group">
                <label for="talla_zapato_${familiarCounter}"><i class="fas fa-shoe-prints"></i> Talla Zapato</label>
                <input type="number" id="talla_zapato_${familiarCounter}" name="talla_zapato[]" min="20" max="50" placeholder="Talla">
            </div>
            <div class="input-group">
                <label for="talla_camisa_${familiarCounter}"><i class="fas fa-tshirt"></i> Talla Camisa</label>
                <input type="text" id="talla_camisa_${familiarCounter}" name="talla_camisa[]" placeholder="Ej: S, M, L, XL">
            </div>
            <div class="input-group">
                <label for="talla_pantalon_${familiarCounter}"><i class="fas fa-tshirt"></i> Talla Pantalón</label>
                <input type="text" id="talla_pantalon_${familiarCounter}" name="talla_pantalon[]" placeholder="Ej: 30, 32, 34">
            </div>
            <div class="input-group">
                <label for="tipo_sangre_${familiarCounter}"><i class="fas fa-tint"></i> Tipo Sangre</label>
                <select id="tipo_sangre_${familiarCounter}" name="tipo_sangre[]">
                    <option value="">Selecciona</option>
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
                <label for="fecha_registro_${familiarCounter}"><i class="fas fa-calendar"></i> Fecha Registro</label>
                <input type="date" id="fecha_registro_${familiarCounter}" name="fecha_registro[]" value="${today}">
            </div>
        </div>
    `;
    
    container.appendChild(familiarDiv);
    
    // Agregar event listener al botón eliminar
    familiarDiv.querySelector('.eliminar-familiar').addEventListener('click', function() {
        eliminarFamiliar(this.getAttribute('data-id'));
    });
}

    // Eliminar familiar
    function eliminarFamiliar(id) {
        if (confirm('¿Está seguro de eliminar este familiar?')) {
            const familiarDiv = document.querySelector(`[data-familiar-id="${id}"]`);
            if (familiarDiv) {
                familiarDiv.remove();
                reenumerarFamiliares();
            }
        }
    }

    // Reenumerar familiares
    function reenumerarFamiliares() {
        const familiares = document.querySelectorAll('.familiar-item');
        familiares.forEach((familiar, index) => {
            const newId = index + 1;
            familiar.setAttribute('data-familiar-id', newId);
            familiar.querySelector('h4').innerHTML = `<i class="fas fa-user"></i> Familiar ${newId}`;
            familiar.querySelector('.eliminar-familiar').setAttribute('data-id', newId);
        });
        familiarCounter = familiares.length;
    }

    // Event listeners para botones de familiares
    const agregarFamiliarBtn = document.getElementById('agregar-familiar');
    const reiniciarFamiliaresBtn = document.getElementById('reiniciar-familiares');

    if (agregarFamiliarBtn) {
        agregarFamiliarBtn.addEventListener('click', agregarFamiliar);
    }

    if (reiniciarFamiliaresBtn) {
        reiniciarFamiliaresBtn.addEventListener('click', () => {
            if (confirm('¿Está seguro de reiniciar todos los campos de familiares? Se perderán todos los datos.')) {
                const container = document.getElementById('familiares-container');
                container.innerHTML = '';
                familiarCounter = 0;
            }
        });
    }

    // ============================================
    // VALIDACIÓN DE FECHAS
    // ============================================
    function validarFechas() {
        const fechaNacimiento = document.getElementById('fecha_nacimiento');
        const fechaIngreso = document.getElementById('fecha_ingreso');
        
        if (fechaNacimiento && fechaIngreso && fechaNacimiento.value && fechaIngreso.value) {
            const nacimiento = new Date(fechaNacimiento.value);
            const ingreso = new Date(fechaIngreso.value);
            
            // Validar que fecha de ingreso sea posterior a nacimiento
            if (ingreso <= nacimiento) {
                alert('La fecha de ingreso debe ser posterior a la fecha de nacimiento');
                return false;
            }
            
            // Validar edad mínima (18 años)
            const hoy = new Date();
            let edad = hoy.getFullYear() - nacimiento.getFullYear();
            const mes = hoy.getMonth() - nacimiento.getMonth();
            
            if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
                edad--;
            }
            
            if (edad < 18) {
                alert('El trabajador debe ser mayor de 18 años');
                return false;
            }
        }
        return true;
    }

    // ============================================
    // ENVÍO DEL FORMULARIO
    // ============================================
    form.addEventListener('submit', function(e) {
        // Validar todos los campos requeridos
        if (!validateStep('all')) {
            e.preventDefault();
            alert('Por favor complete todos los campos requeridos marcados con *.');
            // Ir al primer paso con error
            const firstError = document.querySelector('.error');
            if (firstError) {
                const stepWithError = firstError.closest('.step-content');
                if (stepWithError) {
                    currentStep = parseInt(stepWithError.dataset.step);
                    updateSteps();
                }
            }
            return false;
        }
        
        // Validar fechas
        if (!validarFechas()) {
            e.preventDefault();
            return false;
        }
        
        // Mostrar confirmación
        const numFamiliares = document.querySelectorAll('.familiar-item').length;
        const confirmMessage = numFamiliares > 0 
            ? `¿Está seguro de registrar este trabajador con ${numFamiliares} familiar(es)?`
            : '¿Está seguro de registrar este trabajador?';
        
        if (!confirm(confirmMessage)) {
            e.preventDefault();
            return false;
        }
        
        // Mostrar mensaje de procesamiento
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        submitBtn.disabled = true;
        
        return true;
    });

    // ============================================
    // BOTÓN DE SCROLL
    // ============================================
    const scrollBtn = document.getElementById('scroll-btn');
    
    if (scrollBtn) {
        scrollBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    function updateScrollBtnVisibility() {
        if (scrollBtn) {
            if (window.scrollY > 300) {
                scrollBtn.style.display = 'flex';
            } else {
                scrollBtn.style.display = 'none';
            }
        }
    }

    // ============================================
    // CARGAR MUNICIPIOS Y PARROQUIAS (AJAX) - RUTAS CORREGIDAS
    // ============================================
    $(document).ready(function() {
        console.log("Inicializando AJAX para municipios...");
        
        // OBTENER LA RUTA BASE CORRECTAMENTE
        const baseUrl = window.location.pathname.includes('/usuario/trabajadores/') 
            ? '../../php/' 
            : '../php/';
        
        console.log("Base URL detectada:", baseUrl);
        
        // Cargar municipios
        $.ajax({
            url: baseUrl + 'municipios.php',
            type: 'GET',
            success: function(data) {
                console.log("Municipios recibidos:", data);
                $('#municipio').html('<option value="">Selecciona un municipio</option>' + data);
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar municipios:", error);
                console.log("URL intentada:", baseUrl + 'municipios.php');
                
                // Cargar municipios estáticos de emergencia
                cargarMunicipiosEstaticos();
            }
        });

        // Al cambiar municipio, cargar parroquias
        $('#municipio').change(function() {
            const municipioId = $(this).val();
            console.log("Municipio seleccionado ID:", municipioId);
            
            if (municipioId) {
                $.ajax({
                    url: baseUrl + 'parroquias.php',
                    type: 'GET',
                    data: { 
                        municipio_id: municipioId,
                        _: new Date().getTime() // Evitar cache
                    },
                    success: function(data) {
                        console.log("Parroquias recibidas:", data);
                        $('#parroquia').html('<option value="">Selecciona una parroquia</option>' + data);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error al cargar parroquias:", error);
                        $('#parroquia').html('<option value="">Error al carparroquias</option>');
                        
                        // Cargar parroquias estáticas según municipio
                        cargarParroquiasEstaticas(municipioId);
                    }
                });
            } else {
                $('#parroquia').html('<option value="">Selecciona una parroquia</option>');
            }
        });
        
        // FUNCIÓN PARA CARGAR MUNICIPIOS ESTÁTICOS
        function cargarMunicipiosEstaticos() {
            const municipios = [
                {id: 1, nombre: "Iribarren"},
                {id: 2, nombre: "Palavecino"},
                {id: 3, nombre: "Torres"},
                {id: 4, nombre: "Morán"},
                {id: 5, nombre: "Crespo"},
                {id: 6, nombre: "Jiménez"},
                {id: 7, nombre: "Andrés Eloy Blanco"},
                {id: 8, nombre: "Simón Planas"},
                {id: 9, nombre: "Urdaneta"}
            ];
            
            let options = '<option value="">Selecciona un municipio</option>';
            municipios.forEach(m => {
                options += `<option value="${m.id}">${m.nombre}</option>`;
            });
            
            $('#municipio').html(options);
        }
        
        // FUNCIÓN PARA CARGAR PARROQUIAS ESTÁTICAS
        function cargarParroquiasEstaticas(municipioId) {
            const parroquiasPorMunicipio = {
                '1': [ // Iribarren
                    {id: 1, nombre: "Concepción"},
                    {id: 2, nombre: "Santa Rosa"},
                    {id: 3, nombre: "Unión"},
                    {id: 4, nombre: "Juan de Villegas"},
                    {id: 5, nombre: "Tamaca"},
                    {id: 6, nombre: "Aguedo Felipe Alvarado"},
                    {id: 7, nombre: "Buena Vista"}
                ],
                '2': [ // Palavecino
                    {id: 8, nombre: "Cabudare"},
                    {id: 9, nombre: "José Gregorio Bastidas"}
                ],
                '3': [ // Torres
                    {id: 10, nombre: "Carora"},
                    {id: 11, nombre: "Antonio Díaz"},
                    {id: 12, nombre: "Camacaro"},
                    {id: 13, nombre: "Castañeda"},
                    {id: 14, nombre: "Cecilio Zubillaga"},
                    {id: 15, nombre: "Chiquinquirá"},
                    {id: 16, nombre: "Espinoza de los Monteros"},
                    {id: 17, nombre: "Heriberto Arrollo"},
                    {id: 18, nombre: "Lara"},
                    {id: 19, nombre: "Las Mercedes"},
                    {id: 20, nombre: "Manuel Morillo"},
                    {id: 21, nombre: "Montaña Verde"},
                    {id: 22, nombre: "Montes de Oca"},
                    {id: 23, nombre: "Torres"},
                    {id: 24, nombre: "Trinidad Samuel"}
                ],
                '4': [ // Morán
                    {id: 25, nombre: "El Blanco"},
                    {id: 26, nombre: "El Tocuyo"},
                    {id: 27, nombre: "Guarico"},
                    {id: 28, nombre: "Humocaro Alto"},
                    {id: 29, nombre: "Humocaro Bajo"},
                    {id: 30, nombre: "La Candelaria"},
                    {id: 31, nombre: "Moroturo"},
                    {id: 32, nombre: "San Miguel"},
                    {id: 33, nombre: "Santa Rosa"}
                ],
                '5': [ // Crespo
                    {id: 34, nombre: "Duaca"},
                    {id: 35, nombre: "Agua Negra"},
                    {id: 36, nombre: "Aregue"},
                    {id: 37, nombre: "Río Claro"}
                ],
                '6': [ // Jiménez
                    {id: 38, nombre: "Quíbor"},
                    {id: 39, nombre: "Cubiro"},
                    {id: 40, nombre: "Cují"},
                    {id: 41, nombre: "San Miguel"},
                    {id: 42, nombre: "Tintorero"}
                ],
                '7': [ // Andrés Eloy Blanco
                    {id: 43, nombre: "Sanare"},
                    {id: 44, nombre: "Boca de Monte"},
                    {id: 45, nombre: "Buría"},
                    {id: 46, nombre: "Gustavo Vega"},
                    {id: 47, nombre: "Parapara"},
                    {id: 48, nombre: "Pío Tamayo"}
                ],
                '8': [ // Simón Planas
                    {id: 49, nombre: "Sarare"},
                    {id: 50, nombre: "Gustavo Machado"},
                    {id: 51, nombre: "Urdaneta"}
                ],
                '9': [ // Urdaneta
                    {id: 52, nombre: "Siquisique"},
                    {id: 53, nombre: "Moroturo"},
                    {id: 54, nombre: "San Miguel"},
                    {id: 55, nombre: "Xaguas"}
                ]
            };
            
            let options = '<option value="">Selecciona una parroquia</option>';
            if (parroquiasPorMunicipio[municipioId]) {
                parroquiasPorMunicipio[municipioId].forEach(p => {
                    options += `<option value="${p.id}">${p.nombre}</option>`;
                });
            } else {
                options = '<option value="">No hay parroquias disponibles</option>';
            }
            
            $('#parroquia').html(options);
        }
    });

    // ============================================
    // MANEJO DEL MODAL DE ÉXITO
    // ============================================
    const successModal = document.getElementById('successModal');
    
    if (successModal) {
        let countdown = 8;
        let countdownInterval;
        let confettiCreated = false;
        
        // Iniciar cuenta regresiva
        function startCountdown() {
            const countdownElement = document.getElementById('countdown');
            if (!countdownElement) return;
            
            countdownInterval = setInterval(() => {
                countdown--;
                countdownElement.textContent = countdown;
                
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    closeModal();
                }
            }, 1000);
        }
        
        // Crear efecto confetti
        function createConfetti() {
            if (confettiCreated) return;
            
            const colors = ['#6a67f0', '#43e97b', '#ffd166', '#ff6b6b', '#ff9a3c', '#a855f7'];
            
            for (let i = 0; i < 100; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.position = 'fixed';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.width = Math.random() * 10 + 5 + 'px';
                confetti.style.height = confetti.style.width;
                confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
                confetti.style.zIndex = '9998';
                confetti.style.animation = `confettiFall ${Math.random() * 2 + 1}s linear forwards`;
                confetti.style.animationDelay = Math.random() * 0.5 + 's';
                
                document.body.appendChild(confetti);
                
                // Remover después de animación
                setTimeout(() => {
                    if (confetti.parentNode) {
                        confetti.remove();
                    }
                }, 2000);
            }
            
            confettiCreated = true;
        }
        
        // Función para cerrar modal
        function closeModal() {
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            
            successModal.style.animation = 'fadeOut 0.3s ease';
            
            setTimeout(() => {
                successModal.style.display = 'none';
                
                // Remover todos los confettis restantes
                document.querySelectorAll('.confetti').forEach(confetti => {
                    confetti.remove();
                });
            }, 300);
        }
        
        // Cerrar modal al hacer clic fuera
        successModal.addEventListener('click', function(e) {
            if (e.target === successModal) {
                closeModal();
            }
        });
        
        // Botón "Registrar Otro"
        const btnContinuar = document.getElementById('btnContinuarRegistro');
        if (btnContinuar) {
            btnContinuar.addEventListener('click', function() {
                closeModal();
                
                // Limpiar formulario después de cerrar el modal
                setTimeout(() => {
                    const form = document.getElementById('registration-form');
                    if (form) form.reset();
                    
                    // Restablecer valores por defecto
                    const hoy = new Date();
                    const fechaHoy = hoy.toISOString().split('T')[0];
                    const hace25Anos = new Date();
                    hace25Anos.setFullYear(hoy.getFullYear() - 25);
                    
                    const fechaIngreso = document.getElementById('fecha_ingreso');
                    if (fechaIngreso) fechaIngreso.value = fechaHoy;
                    
                    const fechaNacimiento = document.getElementById('fecha_nacimiento');
                    if (fechaNacimiento) fechaNacimiento.value = hace25Anos.toISOString().split('T')[0];
                    
                    // Restablecer formulario extra para INACTIVO
                    toggleFormularioExtra();
                    
                    // Volver al paso 1
                    currentStep = 1;
                    updateSteps();
                    
                    // Enfocar primer campo
                    setTimeout(() => {
                        const primerCampo = document.getElementById('nacionalidad');
                        if (primerCampo) primerCampo.focus();
                    }, 500);
                }, 300);
            });
        }
        
        // Permitir cerrar con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && successModal.style.display === 'flex') {
                closeModal();
            }
        });
    }

    // ============================================
    // INICIALIZACIÓN
    // ============================================
    
    // Establecer fechas por defecto
    const hoy = new Date();
    const fechaHoy = hoy.toISOString().split('T')[0];
    
    // Fecha de ingreso = hoy por defecto
    const fechaIngreso = document.getElementById('fecha_ingreso');
    if (fechaIngreso && !fechaIngreso.value) {
        fechaIngreso.value = fechaHoy;
    }
    
    // Fecha de nacimiento = hace 25 años por defecto
    const fechaNacimiento = document.getElementById('fecha_nacimiento');
    if (fechaNacimiento && !fechaNacimiento.value) {
        const hace25Anos = new Date();
        hace25Anos.setFullYear(hoy.getFullYear() - 25);
        fechaNacimiento.value = hace25Anos.toISOString().split('T')[0];
    }
    
    // Event listeners para scroll
    window.addEventListener('scroll', updateScrollBtnVisibility);
    
    // Inicializar sistema de pasos
    updateSteps();
    
    // Inicializar botón de scroll
    updateScrollBtnVisibility();
    
    console.log('Sistema de formulario inicializado correctamente');
});

// ============================================
// FUNCIONES AUXILIARES GLOBALES
// ============================================

// Formatear número de teléfono
function formatPhoneNumber(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length > 10) value = value.substring(0, 10);
    
    if (value.length > 6) {
        value = value.replace(/(\d{4})(\d{3})(\d{3})/, '$1-$2-$3');
    } else if (value.length > 3) {
        value = value.replace(/(\d{4})(\d{0,3})/, '$1-$2');
    }
    
    input.value = value;
}

// Formatear cédula
function formatCedula(input) {
    let value = input.value.toUpperCase();
    if (value.match(/^[VE]\d+$/)) {
        value = value.replace(/([VE])(\d+)/, '$1-$2');
    }
    input.value = value;
}

// Validar email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Mostrar notificación
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Estilos básicos para notificación
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#43e97b' : '#ff6b6b'};
        color: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 9999;
        animation: slideIn 0.3s ease;
    `;
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Agregar estilos de animación para notificaciones
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);