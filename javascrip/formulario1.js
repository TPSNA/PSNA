document.addEventListener('DOMContentLoaded', () => {
    // Juangelyn_Sanchez
    const steps = document.querySelectorAll('.step');
    const stepContents = document.querySelectorAll('.step-content');
    const nextBtn = document.getElementById('next-btn');
    const prevBtn = document.getElementById('prev-btn');
    const submitBtn = document.getElementById('submit-btn');
    const form = document.getElementById('registration-form');
    let currentStep = 1;

    // Función para mostrar/ocultar el formulario extra basado en el estatus
    const estatusSelect = document.getElementById('estatus');
    const formularioExtra = document.getElementById('formularioExtra');

    if (estatusSelect) {
        estatusSelect.addEventListener('change', function() {
            if (this.value === 'INACTIVO') {  // Cambié a 'INACTIVO' para coincidir con las opciones del HTML
                formularioExtra.classList.remove('hidden');
            } else {
                formularioExtra.classList.add('hidden');
            }
        });
    }

    // manejar 5 pasos 
    function updateSteps() {
        steps.forEach(step => {
            step.classList.remove('active');
            if (parseInt(step.dataset.step) === currentStep) {
                step.classList.add('active');
            }
        });
        stepContents.forEach(content => {
            content.classList.remove('active');
            if (parseInt(content.dataset.step) === currentStep) {
                content.classList.add('active');
            }
        });

        prevBtn.disabled = currentStep === 1;
        // "Registrar" solo en el último paso
        nextBtn.style.display = currentStep === 5 ? 'none' : 'inline-block';
        submitBtn.style.display = currentStep === 5 ? 'inline-block' : 'none';
        
       
        if (currentStep < 5) {
            nextBtn.textContent = 'Continuar';
        } else {
            submitBtn.textContent = 'Registrar';
        }
    }

    
    function validateStep(step = 'all') {
        let inputs;
        if (step === 'all') {
            // Validacion todos los campos required del formulario
            inputs = form.querySelectorAll('input[required], select[required]');
        } else {
            // Validacion solo el paso especificado
            inputs = stepContents[step - 1].querySelectorAll('input[required], select[required]');
        }
        let isValid = true;
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('error');
                isValid = false;
            } else {
                input.classList.remove('error');
            }
        });
        return isValid;
    }

    // Juangelyn_Sanchez
    nextBtn.addEventListener('click', () => {
        if (currentStep < 5) {  
            currentStep++;
            updateSteps();
        }
    });

   
    prevBtn.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            updateSteps();
        }
    });

    // permite ir a cualquier paso sin validación
    steps.forEach(step => {
        step.addEventListener('click', () => {
            const stepNum = parseInt(step.dataset.step);
            currentStep = stepNum;
            updateSteps();
        });
    });

    // Envío del formulario SIN validación obligatoria
    form.addEventListener('submit', (e) => {
       
    });

    updateSteps();

    // Scroll 
    const scrollBtn = document.getElementById('scroll-btn'); 

    
    scrollBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    function updateScrollBtnVisibility() {
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;

        if (documentHeight <= windowHeight + 100) {
            scrollBtn.style.display = 'none';
        } else {
            scrollBtn.style.display = 'block';
        }
    }

   
    updateScrollBtnVisibility();

    window.addEventListener('resize', updateScrollBtnVisibility);

    // Función para el preview de la foto
    const preview = document.getElementById('preview');
    const fotoInput = document.getElementById('foto');

    // CORRECCIÓN: Eliminé el listener duplicado. Mantengo solo este, que abre el selector y maneja el preview completo.
    preview.addEventListener('click', () => {
        fotoInput.click();
    });

    // Evento para actualizar el preview cuando se selecciona un archivo
    fotoInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" style="max-width: 150px; max-height: 150px; border-radius: 5px;">';
            };
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '<p>Haz clic para seleccionar una foto</p>';
        }
    });

    // Variables para controlar familiares
    let familiarCounter = 0;  // Contador de familiares agregados

    // Función para agregar un familiar
    function agregarFamiliar() {
        familiarCounter++;
        const container = document.getElementById('familiares-container');
        
        const familiarDiv = document.createElement('div');
        familiarDiv.className = 'familiar-item';
        familiarDiv.setAttribute('data-familiar-id', familiarCounter);  // Atributo para identificar
        
        familiarDiv.innerHTML = `
            <h4>Familiar ${familiarCounter}</h4>
            <button type="button" class="eliminar-familiar" data-id="${familiarCounter}">Eliminar este Familiar</button>
            <div class="form-grid">
                <div class="input-group">
                    <label for="cedula_familiar_${familiarCounter}">Cédula Familiar</label>
                    <input type="text" id="cedula_familiar_${familiarCounter}" name="cedula_familiar[]" placeholder="Cédula del familiar">
                </div>
                <div class="input-group">
                    <label for="parentesco_${familiarCounter}">Parentesco</label>
                    <select id="parentesco_${familiarCounter}" name="parentesco[]">
                        <option value="">Selecciona</option>
                        <option value="ESPOSO/A">ESPOSO/A</option>
                        <option value="HIJO/A">HIJO/A</option>
                        <option value="PADRE">PADRE</option>
                        <option value="MADRE">MADRE</option>
                        <option value="OTROS">OTROS</option>
                    </select>
                </div>
                <div class="input-group">
                    <label for="edad_${familiarCounter}">Edad</label>
                    <input type="number" id="edad_${familiarCounter}" name="edad[]" min="0" placeholder="Edad">
                </div>
                <div class="input-group">
                    <label for="peso_${familiarCounter}">Peso</label>
                    <input type="number" id="peso_${familiarCounter}" name="peso[]" step="0.01" placeholder="Peso (kg)">
                </div>
                <div class="input-group">
                    <label for="altura_${familiarCounter}">Altura</label>
                    <input type="number" id="altura_${familiarCounter}" name="altura[]" step="0.01" placeholder="Altura (cm)">
                </div>
                <div class="input-group">
                    <label for="talla_zapato_${familiarCounter}">Talla Zapato</label>
                    <input type="number" id="talla_zapato_${familiarCounter}" name="talla_zapato[]" placeholder="Talla zapato">
                </div>
                <div class="input-group">
                    <label for="talla_camisa_${familiarCounter}">Talla Camisa</label>
                    <input type="text" id="talla_camisa_${familiarCounter}" name="talla_camisa[]" placeholder="Talla camisa">
                </div>
                <div class="input-group">
                    <label for="talla_pantalon_${familiarCounter}">Talla Pantalón</label>
                    <input type="text" id="talla_pantalon_${familiarCounter}" name="talla_pantalon[]" placeholder="Talla pantalón">
                </div>
                <div class="input-group">
                    <label for="tipo_sangre_${familiarCounter}">Tipo Sangre</label>
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
                    <label for="fecha_registro_${familiarCounter}">Fecha Registro</label>
                    <input type="date" id="fecha_registro_${familiarCounter}" name="fecha_registro[]">
                </div>
            </div>
        `;
        
        container.appendChild(familiarDiv);
        
        // Agregar event listener al botón "Eliminar" recién creado
        familiarDiv.querySelector('.eliminar-familiar').addEventListener('click', function() {
            eliminarFamiliar(this.getAttribute('data-id'));
        });
    }

    // Función para eliminar un familiar específico
    function eliminarFamiliar(id) {
        const familiarDiv = document.querySelector(`[data-familiar-id="${id}"]`);
        if (familiarDiv) {
            familiarDiv.remove();
            // Opcional: Reenumerar los restantes (si quieres mantener el orden)
            reenumerarFamiliares();
        }
    }

    // Función opcional para reenumerar familiares después de eliminar
    function reenumerarFamiliares() {
        const familiares = document.querySelectorAll('.familiar-item');
        familiares.forEach((familiar, index) => {
            const newId = index + 1;
            familiar.setAttribute('data-familiar-id', newId);
            familiar.querySelector('h4').textContent = `Familiar ${newId}`;
            familiar.querySelector('.eliminar-familiar').setAttribute('data-id', newId);
            // Actualizar IDs de inputs si es necesario (para consistencia, aunque no es crítico)
        });
        familiarCounter = familiares.length;
    }

    // Event listener para "Agregar Familiar"
    document.getElementById('agregar-familiar').addEventListener('click', function() {
        if (familiarCounter < 10) {  // Límite máximo
            agregarFamiliar();
        } else {
            alert('Máximo 10 familiares permitidos.');
        }
    });

    // Event listener para "Reiniciar Campos" (actualizado)
    document.getElementById('reiniciar-familiares').addEventListener('click', function() {
        const container = document.getElementById('familiares-container');
        const numFamiliaresInput = document.getElementById('num_familiares');
        
        container.innerHTML = '';
        numFamiliaresInput.value = '';
        familiarCounter = 0;
        
        alert('Campos reiniciados.');
    });

    // Código jQuery actualizado con logs de depuración
    $(document).ready(function() {
        console.log("Cargando municipios...");  // Agrega esto para depurar
        $.ajax({
            url: '../php/municipios.php',
            type: 'GET',
            success: function(data) {
                console.log("Municipios cargados:", data);  // Ver qué devuelve
                $('#municipio').html('<option value="">Selecciona un municipio</option>' + data);
            },
            error: function(xhr, status, error) {
                console.error("Error en municipios:", error);  // Muestra errores
                alert('Error al cargar municipios. Verifica la conexión a la base de datos.');
            }
        });

        // Al cambiar municipio, cargar parroquias filtradas
        $('#municipio').change(function() {
            var municipioId = $(this).val();
            console.log("Cambiando a municipio ID:", municipioId);  // Depurar
            if (municipioId) {
                $.ajax({
                    url: '../php/parroquias.php',
                    type: 'GET',
                    data: { municipio_id: municipioId },
                    success: function(data) {
                        console.log("Parroquias cargadas:", data);
                        $('#parroquia').html('<option value="">Selecciona una parroquia</option>' + data);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error en parroquias:", error);
                        alert('Error al cargar parroquias. Verifica la conexión a la base de datos.');
                    }
                });
            } else {
                $('#parroquia').html('<option value="">Selecciona una parroquia</option>');
            }
        });
    });
});
