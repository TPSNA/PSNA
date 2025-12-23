// Función para exportar Excel (sin cambios)
function exportExcel() {
    const search = '<?php echo urlencode($search); ?>';
    const filter = '<?php echo urlencode($filter); ?>';
    const url = `../php/excel.php?search=${search}&filter=${filter}`;

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.blob();
        })
        .then(blob => {
            const downloadUrl = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = downloadUrl;
            a.download = 'registros.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(downloadUrl);
            document.body.removeChild(a);
        })
        .catch(error => {
            console.error('Error al descargar el Excel:', error);
            alert('Hubo un error al descargar el archivo Excel. Verifica la configuración de excel.php.');
        });
}

// Código integrado para el modal de detalles (combinando ambos códigos originales)
$(document).ready(function() {
    // Evento para el botón "Ver Detalles" (integrado del primer código, pero con lógica JSON del segundo)
    $(document).on('click', '.btn-details', function() {
        var id = $(this).data('id');
        // Aquí cargas los detalles via AJAX (usando la URL del primer código, pero ajustada a tu detalles.php)
        $.ajax({
            url: '../php/detalles.php', // Cambiado a tu archivo real que devuelve JSON
            type: 'GET',
            data: { id: id },
            success: function(data) {
                try {
                    var empleado = JSON.parse(data);
                    if (empleado.error) {
                        alert(empleado.error);
                        return;
                    }
                    // Construir el HTML con todos los campos (del segundo código original)
                    var html = '<ul>';
                    html += '<li><strong>ID:</strong> ' + (empleado.id || 'N/A') + '</li>';
                    html += '<li><strong>Nacionalidad:</strong> ' + (empleado.nacionalidad || 'N/A') + '</li>';
                    html += '<li><strong>CI:</strong> ' + (empleado.ci || 'N/A') + '</li>';
                    html += '<li><strong>Primer Nombre:</strong> ' + (empleado.primer_nombre || 'N/A') + '</li>';
                    html += '<li><strong>Segundo Nombre:</strong> ' + (empleado.segundo_nombre || 'N/A') + '</li>';
                    html += '<li><strong>Primer Apellido:</strong> ' + (empleado.primer_apellido || 'N/A') + '</li>';
                    html += '<li><strong>Segundo Apellido:</strong> ' + (empleado.segundo_apellido || 'N/A') + '</li>';
                    html += '<li><strong>Fecha Nac.:</strong> ' + (empleado.fecha_nacimiento || 'N/A') + '</li>';
                    html += '<li><strong>Sexo:</strong> ' + (empleado.sexo || 'N/A') + '</li>';
                    html += '<li><strong>Estado Civil:</strong> ' + (empleado.estado_civil || 'N/A') + '</li>';
                    html += '<li><strong>Dirección Ubicación:</strong> ' + (empleado.direccion_ubicacion || 'N/A') + '</li>';
                    html += '<li><strong>Teléfono:</strong> ' + (empleado.telefono || 'N/A') + '</li>';
                    html += '<li><strong>Correo:</strong> ' + (empleado.correo || 'N/A') + '</li>';
                    html += '<li><strong>Cuenta Bancaria:</strong> ' + (empleado.cuenta_bancaria || 'N/A') + '</li>';
                    html += '<li><strong>Tipo Trabajador:</strong> ' + (empleado.tipo_trabajador || 'N/A') + '</li>';
                    html += '<li><strong>Grado Instrucción:</strong> ' + (empleado.grado_instruccion || 'N/A') + '</li>';
                    html += '<li><strong>Cargo:</strong> ' + (empleado.cargo || 'N/A') + '</li>';
                    html += '<li><strong>Sede:</strong> ' + (empleado.sede || 'N/A') + '</li>';
                    html += '<li><strong>Dependencia:</strong> ' + (empleado.dependencia || 'N/A') + '</li>';
                    html += '<li><strong>Fecha Ingreso:</strong> ' + (empleado.fecha_ingreso || 'N/A') + '</li>';
                    html += '<li><strong>Cod SIANTEL:</strong> ' + (empleado.cod_siantel || 'N/A') + '</li>';
                    html += '<li><strong>Ubicación Estante:</strong> ' + (empleado.ubicacion_estante || 'N/A') + '</li>';
                    html += '<li><strong>Estatus:</strong> ' + (empleado.estatus || 'N/A') + '</li>';
                    html += '<li><strong>Fecha Egreso:</strong> ' + (empleado.fecha_egreso || 'N/A') + '</li>';
                    html += '<li><strong>Motivo Retiro:</strong> ' + (empleado.motivo_retiro || 'N/A') + '</li>';
                    html += '<li><strong>Ubicación Est. Retiro:</strong> ' + (empleado.ubicacion_estante_retiro || 'N/A') + '</li>';
                    html += '<li><strong>Tipo Sangre:</strong> ' + (empleado.tipo_sangre || 'N/A') + '</li>';
                    html += '<li><strong>Lateralidad:</strong> ' + (empleado.lateralidad || 'N/A') + '</li>';
                    html += '<li><strong>Peso:</strong> ' + (empleado.peso_trabajador || 'N/A') + '</li>';
                    html += '<li><strong>Altura:</strong> ' + (empleado.altura_trabajador || 'N/A') + '</li>';
                    html += '<li><strong>Talla Calzado:</strong> ' + (empleado.calzado_trabajador || 'N/A') + '</li>';
                    html += '<li><strong>Talla Camisa:</strong> ' + (empleado.camisa_trabajador || 'N/A') + '</li>';
                    html += '<li><strong>Talla Pantalón:</strong> ' + (empleado.pantalon_trabajador || 'N/A') + '</li>';
                    html += '<li><strong>Foto:</strong> ' + (empleado.foto ? '<img src="' + empleado.foto + '" alt="Foto del empleado" style="max-width: 150px; max-height: 150px; border-radius: 5px;">' : 'N/A') + '</li>';
                    html += '<li><strong>Fecha Registro:</strong> ' + (empleado.fecha_registro || 'N/A') + '</li>';
                    // Agrega estos si quieres mostrarlos (no estaban en tu lista original)
                    html += '<li><strong>Estado ID:</strong> ' + (empleado.estado_id || 'N/A') + '</li>';
                    html += '<li><strong>Municipio ID:</strong> ' + (empleado.municipio_id || 'N/A') + '</li>';
                    html += '<li><strong>Parroquia ID:</strong> ' + (empleado.parroquia_id || 'N/A') + '</li>';
                    html += '</ul>';
                    $('#modal-body').html(html);
                    $('#modal').css('display', 'block'); // Muestra el modal (usando css como en el primer código)
                } catch (e) {
                    alert('Error al procesar los datos del empleado.');
                    console.error('Error parsing JSON:', e);
                }
            },
            error: function() {
                alert('Error al cargar los detalles. Verifica la conexión.');
            }
        });
    });

    // Evento para cerrar el modal (botón X) (del primer código)
    $(document).on('click', '.close', function() {
        $('#modal').css('display', 'none');
    });

    // Cerrar el modal al hacer clic fuera de él (del primer código)
    $(window).on('click', function(event) {
        if (event.target == document.getElementById('modal')) {
            $('#modal').css('display', 'none');
        }
    });
});
