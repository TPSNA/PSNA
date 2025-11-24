document.addEventListener('DOMContentLoaded', () => {// Juangelyn_Sanchez
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
            if (this.value === 'inactivo') {
                formularioExtra.classList.remove('hidden');
            } else {
                formularioExtra.classList.add('hidden');
            }
        });
    }

    //  manejar 5 pasos 
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
        //  "Registrar" solo en el último paso
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

    // Envío del formulario 
    form.addEventListener('submit', (e) => {
        if (!validateStep('all')) {  
            e.preventDefault();
            alert('Por favor, completa todos los campos obligatorios antes de enviar.');
        }
    
    });

    updateSteps();

    // Scroll (siempre lleva al principio)
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

    
    preview.addEventListener('click', () => {
        fotoInput.click();
    });

    // Maneja la selección de archivo y muestra preview
    fotoInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                let img = preview.querySelector('img');
                if (!img) {
                    img = document.createElement('img');
                    preview.innerHTML = ''; 
                    preview.appendChild(img);
                }
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            
            preview.innerHTML = '<p>Por favor, selecciona una imagen válida.</p>';
        }
    });

    // Función para generar campos de familiares dinámicamente
    document.getElementById('generar-familiares').addEventListener('click', function() {
        const numFamiliares = parseInt(document.getElementById('num_familiares').value);
        const container = document.getElementById('familiares-container');
        
        // Limpiar contenedor anterior
        container.innerHTML = '';
        
        if (numFamiliares > 0 && numFamiliares <= 10) {
            for (let i = 1; i <= numFamiliares; i++) {
                const familiarDiv = document.createElement('div');
                familiarDiv.className = 'familiar-item';// Juangelyn_Sanchez
                familiarDiv.innerHTML = `
                    <h4>Familiar ${i}</h4>
                    <div class="form-grid">
                        <div class="input-group">
                            <label for="cedula_trabajador_${i}">Cédula Trabajador</label>
                            <input type="text" id="cedula_trabajador_${i}" name="cedula_trabajador[]" placeholder="Cédula del trabajador" required>
                        </div>
                        <div class="input-group">
                            <label for="cedula_familiar_${i}">Cédula Familiar</label>
                            <input type="text" id="cedula_familiar_${i}" name="cedula_familiar[]" placeholder="Cédula del familiar" required>
                        </div>
                        <div class="input-group">
                            <label for="parentesco_${i}">Parentesco</label>
                            <select id="parentesco_${i}" name="parentesco[]" required>
                                <option value="">Selecciona</option>
                                <option value="Esposo/a">Esposo/a</option>
                                <option value="Hijo/a">Hijo/a</option>
                                <option value="Padre">Padre</option>
                                <option value="Madre">Madre</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="edad_${i}">Edad</label>
                            <input type="number" id="edad_${i}" name="edad[]" min="0" placeholder="Edad" required>
                        </div>
                        <div class="input-group">
                            <label for="peso_${i}">Peso</label>
                            <input type="number" id="peso_${i}" name="peso[]" step="0.01" placeholder="Peso (kg)">
                        </div>
                        <div class="input-group">
                            <label for="altura_${i}">Altura</label>
                            <input type="number" id="altura_${i}" name="altura[]" step="0.01" placeholder="Altura (cm)">
                        </div>
                        <div class="input-group">
                            <label for="talla_zapato_${i}">Talla Zapato</label>
                            <input type="number" id="talla_zapato_${i}" name="talla_zapato[]" placeholder="Talla zapato">
                        </div>
                        <div class="input-group">
                            <label for="talla_camisa_${i}">Talla Camisa</label>
                            <input type="text" id="talla_camisa_${i}" name="talla_camisa[]" placeholder="Talla camisa">
                        </div>
                        <div class="input-group">
                            <label for="talla_pantalon_${i}">Talla Pantalón</label>
                            <input type="text" id="talla_pantalon_${i}" name="talla_pantalon[]" placeholder="Talla pantalón">
                        </div>
                        <div class="input-group">
                            <label for="tipo_sangre_${i}">Tipo Sangre</label>
                            <select id="tipo_sangre_${i}" name="tipo_sangre[]">
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
                            <label for="fecha_registro_${i}">Fecha Registro</label>
                            <input type="date" id="fecha_registro_${i}" name="fecha_registro[]" required>
                        </div>
                    </div>
                `;
                container.appendChild(familiarDiv);
            }
        } else {
            alert('Ingresa un número válido de familiares (1-10).');
        }
    });
// Juangelyn_Sanchez
    // Función para reiniciar campos de familiares
    document.getElementById('reiniciar-familiares').addEventListener('click', function() {
        const container = document.getElementById('familiares-container');
        const numFamiliaresInput = document.getElementById('num_familiares');
        
        // Limpiar contenedor
        container.innerHTML = '';
        
        // Resetear el input del número de familiares
        numFamiliaresInput.value = '';
        
        
        alert('Campos reiniciados. Puedes ingresar un nuevo número y generar de nuevo.');
    });
});
