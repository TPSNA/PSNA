<?php
session_start();
require_once('../../includes/database.php');
require_once('../../pdf/fpdf.php');

// Verificar permisos
if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: ../admin/login.php");
    exit();
}

// Función para convertir UTF-8 a Windows-1252 (para caracteres especiales)
function convertirUTF8($texto) {
    return iconv('UTF-8', 'windows-1252', $texto);
}

// Obtener datos del empleado
$empleado_id = $_GET['id'];
$sql = "SELECT * FROM empleados WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $empleado_id);
$stmt->execute();
$result = $stmt->get_result();
$empleado = $result->fetch_assoc();

// Datos del formulario
$fecha_inicio = $_GET['fecha_inicio_vacaciones'];
$fecha_fin = $_GET['fecha_fin_vacaciones'];
$dias_vacaciones = $_GET['dias_vacaciones'];
$anio_disfrute = $_GET['anio_disfrute'];

// Crear PDF con configuración personalizada
class PDF extends FPDF {
    function Header() {
        // Cintillo superior (ajustado para orientación horizontal)
        $ancho_pagina = $this->GetPageWidth();
        $ancho_cintillo = 250; // Ajustado para orientación horizontal
        $x_cintillo = ($ancho_pagina - $ancho_cintillo) / 2;
        
        $this->Image('../../../imagenes/cintillo.png', 
                     $x_cintillo,
                     10, // 1 cm del borde superior
                     $ancho_cintillo, // 25 cm de ancho
                     25); // 2.5 cm de alto
        
        // Título principal
        $this->SetY(40); // Reducido de 45
        $this->SetFont('Arial', 'B', 15); // Reducido de 16
        $this->SetTextColor(0, 51, 102);
        $this->Cell(0, 8, convertirUTF8('NOTIFICACIÓN DE VACACIONES'), 0, 1, 'C');
        
        // Subtítulo
        $this->SetFont('Arial', 'B', 13); // Reducido de 14
        $this->SetTextColor(102, 0, 0);
        $this->Cell(0, 6, convertirUTF8('PERIODO VACACIONAL'), 0, 1, 'C');
        $this->Ln(3); // Reducido de 5
        
        // Línea decorativa más fina
        $this->SetLineWidth(0.3); // Reducido de 0.5
        $this->SetDrawColor(0, 51, 102);
        $this->Line(15, $this->GetY(), $this->GetPageWidth()-15, $this->GetY()); // Márgenes más estrechos
        $this->Ln(4); // Reducido de 8
    }
    
    function Footer() {
        // Pie de página institucional
        $this->SetY(-12); // Reducido de -15
        $this->SetFont('Arial', '', 8); // Reducido de 9
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 4, convertirUTF8('Carrera 16 entre calles 41 y 42 frente al Parque Ayacucho Quinta Josefita.'), 0, 1, 'C');
        $this->Cell(0, 4, convertirUTF8('Teléfonos 0251-4460837 / 0251-4471108. SAINA LARA RIF. G-20003146-8.'), 0, 1, 'C');
    }
    
    // Función para ajustar texto largo a múltiples líneas
    function ajustarTextoMultiLinea($texto, $ancho_max) {
        if ($this->GetStringWidth($texto) <= $ancho_max) {
            return array($texto);
        }
        
        $palabras = explode(' ', $texto);
        $lineas = array();
        $linea_actual = '';
        
        foreach ($palabras as $palabra) {
            $prueba_linea = $linea_actual . ($linea_actual ? ' ' : '') . $palabra;
            if ($this->GetStringWidth($prueba_linea) <= $ancho_max) {
                $linea_actual = $prueba_linea;
            } else {
                if ($linea_actual) {
                    $lineas[] = $linea_actual;
                }
                $linea_actual = $palabra;
            }
        }
        
        if ($linea_actual) {
            $lineas[] = $linea_actual;
        }
        
        // Si sigue siendo muy largo, acortamos la primera línea
        if (count($lineas) > 2) {
            $lineas[0] = substr($lineas[0], 0, 30) . '...';
            $lineas = array_slice($lineas, 0, 2);
        }
        
        return $lineas;
    }
}

// Configurar PDF en orientación horizontal
$pdf = new PDF('L', 'mm', 'Letter'); // Horizontal: 279 x 216 mm
$pdf->SetMargins(12, 40, 12); // Márgenes reducidos: 12mm (1.2cm)
$pdf->SetAutoPageBreak(true, 15); // Margen inferior reducido

// ============ PRIMERA PÁGINA ============
$pdf->AddPage();

// Datos del empleado (todos extraídos de BD)
$nombre_completo = $empleado['primer_nombre'] . ' ' . 
                  ($empleado['segundo_nombre'] ? $empleado['segundo_nombre'] . ' ' : '') . 
                  $empleado['primer_apellido'] . ' ' . 
                  ($empleado['segundo_apellido'] ? $empleado['segundo_apellido'] : '');
$cedula = $empleado['nacionalidad'] . '-' . $empleado['ci'];
$fecha_ingreso = date('d/m/Y', strtotime($empleado['fecha_ingreso']));
$fecha_salida = date('d/m/Y', strtotime($fecha_inicio));
$fecha_regreso = date('d/m/Y', strtotime($fecha_fin));

// Ajustar el cargo si es muy largo
$cargo = $empleado['cargo'];
$apellidos = $empleado['primer_apellido'] . ($empleado['segundo_apellido'] ? ' ' . $empleado['segundo_apellido'] : '');
$nombres = $empleado['primer_nombre'] . ($empleado['segundo_nombre'] ? ' ' . $empleado['segundo_nombre'] : '');

// Tabla principal - Primera fila
$ancho_pagina = $pdf->GetPageWidth();
$ancho_total = $ancho_pagina - 24; // Restar márgenes (12 + 12)
$ancho_columna = $ancho_total / 5; // 5 columnas

// Primera fila: APELLIDOS | NOMBRES | CEDULA | BENEFICIARIO
$y = $pdf->GetY();
$pdf->SetFont('Arial', 'B', 9); // Reducido de 10
$pdf->SetFillColor(220, 220, 220);

// Encabezados con altura reducida
$pdf->Cell($ancho_columna, 6, convertirUTF8('APELLIDOS'), 1, 0, 'C', true); // 6 en lugar de 8
$pdf->Cell($ancho_columna, 6, convertirUTF8('NOMBRES'), 1, 0, 'C', true);
$pdf->Cell($ancho_columna, 6, convertirUTF8('CÉDULA'), 1, 0, 'C', true);
$pdf->Cell($ancho_columna * 2, 6, convertirUTF8('BENEFICIARIO'), 1, 1, 'C', true);

// Datos con altura reducida y tamaño de letra más pequeño
$pdf->SetFont('Arial', '', 9); // Reducido de 11
$pdf->SetFillColor(255, 255, 255);

// Ajustar apellidos si es necesario
$apellidos_ajustado = $apellidos;
if ($pdf->GetStringWidth($apellidos_ajustado) > $ancho_columna - 2) {
    $apellidos_ajustado = substr($apellidos_ajustado, 0, 20) . '...';
}

// Ajustar nombres si es necesario
$nombres_ajustado = $nombres;
if ($pdf->GetStringWidth($nombres_ajustado) > $ancho_columna - 2) {
    $nombres_ajustado = substr($nombres_ajustado, 0, 20) . '...';
}

// Ajustar cédula si es necesario
$cedula_ajustada = $cedula;
if ($pdf->GetStringWidth($cedula_ajustada) > $ancho_columna - 2) {
    $cedula_ajustada = substr($cedula_ajustada, 0, 15) . '...';
}

$pdf->Cell($ancho_columna, 8, convertirUTF8($apellidos_ajustado), 1, 0, 'C', true); // 8 en lugar de 10
$pdf->Cell($ancho_columna, 8, convertirUTF8($nombres_ajustado), 1, 0, 'C', true);
$pdf->Cell($ancho_columna, 8, convertirUTF8($cedula_ajustada), 1, 0, 'C', true);
$pdf->Cell($ancho_columna * 2, 8, convertirUTF8('TRABAJADOR'), 1, 1, 'C', true);

$pdf->Ln(4); // Reducido de 8

// Segunda fila: DEPENDENCIA | UNIDAD | LOCALIDAD | DIRECTORA DE T.H.
// Encabezados
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(220, 220, 220);

$pdf->Cell($ancho_columna, 6, convertirUTF8('DEPENDENCIA'), 1, 0, 'C', true);
$pdf->Cell($ancho_columna, 6, convertirUTF8('UNIDAD'), 1, 0, 'C', true);
$pdf->Cell($ancho_columna, 6, convertirUTF8('LOCALIDAD'), 1, 0, 'C', true);
$pdf->Cell($ancho_columna * 2, 6, convertirUTF8('DIRECTORA DE T.H.'), 1, 1, 'C', true);

// Datos
$pdf->SetFont('Arial', '', 9);
$pdf->SetFillColor(255, 255, 255);

$pdf->Cell($ancho_columna, 8, convertirUTF8('SAINA LARA'), 1, 0, 'C', true);
$pdf->Cell($ancho_columna, 8, convertirUTF8('CENTRO SOCIO EDUCATIVO'), 1, 0, 'C', true);
$pdf->Cell($ancho_columna, 8, convertirUTF8('BARQUISIMETO'), 1, 0, 'C', true);
$pdf->Cell($ancho_columna * 2, 8, convertirUTF8('MARÍA E. LANDAETA'), 1, 1, 'C', true);

$pdf->Ln(4); // Reducido de 8

// Tercera fila: FECHA INGRESO | FECHA SALIDA | FECHA REGRESO | DIAS HABILES | DIAS REALES
// Encabezados
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(220, 220, 220);

$pdf->Cell($ancho_columna, 6, convertirUTF8('FECHA INGRESO'), 1, 0, 'C', true);
$pdf->Cell($ancho_columna, 6, convertirUTF8('FECHA SALIDA'), 1, 0, 'C', true);
$pdf->Cell($ancho_columna, 6, convertirUTF8('FECHA REGRESO'), 1, 0, 'C', true);
$pdf->Cell($ancho_columna, 6, convertirUTF8('DÍAS HÁBILES'), 1, 0, 'C', true);
$pdf->Cell($ancho_columna, 6, convertirUTF8('DÍAS REALES'), 1, 1, 'C', true);

// Datos (extraídos de BD)
$pdf->SetFont('Arial', '', 9);
$pdf->SetFillColor(255, 255, 255);

$pdf->Cell($ancho_columna, 8, convertirUTF8($fecha_ingreso), 1, 0, 'C', true);
$pdf->Cell($ancho_columna, 8, convertirUTF8($fecha_salida), 1, 0, 'C', true);
$pdf->Cell($ancho_columna, 8, convertirUTF8($fecha_regreso), 1, 0, 'C', true);
$pdf->Cell($ancho_columna, 8, convertirUTF8($dias_vacaciones), 1, 0, 'C', true);
$pdf->Cell($ancho_columna, 8, convertirUTF8($dias_vacaciones), 1, 1, 'C', true);

$pdf->Ln(4); // Reducido de 8

// Cuarta fila: TIPO DE PERSONAL | CARGO
// Encabezados
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(220, 220, 220);

$pdf->Cell($ancho_columna * 2.5, 6, convertirUTF8('TIPO DE PERSONAL'), 1, 0, 'C', true);
$pdf->Cell($ancho_columna * 2.5, 6, convertirUTF8('CARGO'), 1, 1, 'C', true);

// Datos (cargo extraído de BD y ajustado)
$pdf->SetFont('Arial', '', 9);
$pdf->SetFillColor(255, 255, 255);

// Ajustar cargo si es muy largo
$cargo_ajustado = $cargo;
if ($pdf->GetStringWidth($cargo_ajustado) > ($ancho_columna * 2.5) - 4) {
    // Intentar dividir en dos líneas
    $lineas_cargo = $pdf->ajustarTextoMultiLinea($cargo_ajustado, ($ancho_columna * 2.5) - 4);
    
    if (count($lineas_cargo) == 1) {
        // Si aún no cabe, acortar
        $cargo_ajustado = substr($cargo_ajustado, 0, 35) . '...';
        $pdf->Cell($ancho_columna * 2.5, 8, convertirUTF8($cargo_ajustado), 1, 1, 'C', true);
    } else {
        // Escribir primera línea
        $pdf->Cell($ancho_columna * 2.5, 8, convertirUTF8($lineas_cargo[0]), 1, 0, 'C', true);
        $pdf->Cell($ancho_columna * 2.5, 8, '', 1, 1, 'C', true);
        
        // Mover a la siguiente línea para escribir segunda línea
        $pdf->SetX($pdf->GetX() + ($ancho_columna * 2.5));
        $pdf->Cell($ancho_columna * 2.5, 8, convertirUTF8($lineas_cargo[1]), 1, 1, 'C', true);
    }
} else {
    $pdf->Cell($ancho_columna * 2.5, 8, 'L.N.R.', 1, 0, 'C', true);
    $pdf->Cell($ancho_columna * 2.5, 8, convertirUTF8($cargo_ajustado), 1, 1, 'C', true);
}

$pdf->Ln(8); // Reducido de 15

// Sección: NOMBRES Y APELLIDOS DEL JEFE INMEDIATO (más compacta)
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 6, convertirUTF8('NOMBRES Y APELLIDOS DEL JEFE INMEDIATO'), 1, 1, 'C', true); // 6 en lugar de 8

$pdf->SetFont('Arial', '', 9);
$pdf->SetFillColor(255, 255, 255);
$pdf->Cell(0, 12, '', 1, 1, 'C', true); // 12 en lugar de 20

// ============ SEGUNDA PÁGINA ============
$pdf->AddPage();

// OBSERVACIONES (más compacta)
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 6, convertirUTF8('OBSERVACIONES:'), 1, 1, 'L', true);

$texto_observaciones = 'Quedará sujeto a las sanciones correspondientes el trabajador que sin plena justificación, no se reintegre al trabajo en la fecha señalada en este formulario.';
$pdf->SetFont('Arial', '', 8); // Reducido de 10
$pdf->SetFillColor(255, 255, 255);
$pdf->MultiCell(0, 4, convertirUTF8($texto_observaciones), 1, 'L', true); // 4 en lugar de 6

$pdf->Ln(8); // Reducido de 15

// Opciones SOLO PARA PAGO / SOLO PARA DISFRUTE (CASILLAS VACÍAS PARA LLENAR MANUALMENTE)
$pdf->SetFont('Arial', 'B', 9);
$ancho_opcion = 70; // Reducido de 80
$x_centro = ($ancho_pagina - ($ancho_opcion * 2 + 15)) / 2; // 15 en lugar de 20

// SOLO PARA PAGO (casilla vacía)
$pdf->SetX($x_centro);
$pdf->Cell(12, 8, '', 1, 0, 'C'); // Casilla más pequeña (12 en lugar de 15)
$pdf->Cell(3, 8, '', 0, 0); // Espacio más pequeño
$pdf->Cell(55, 8, convertirUTF8('SOLO PARA PAGO'), 0, 0, 'L'); // 55 en lugar de 60

// Espacio entre opciones
$pdf->Cell(15, 8, '', 0, 0); // 15 en lugar de 20

// SOLO PARA DISFRUTE (casilla vacía)
$pdf->Cell(12, 8, '', 1, 0, 'C'); // Casilla más pequeña
$pdf->Cell(3, 8, '', 0, 0);
$pdf->Cell(55, 8, convertirUTF8('SOLO PARA DISFRUTE'), 0, 1, 'L');

$pdf->Ln(6); // Reducido de 10

// AÑO DE DISFRUTE centrado (tamaño reducido)
$pdf->SetFont('Arial', 'B', 10); // Reducido de 12
$pdf->SetTextColor(102, 0, 0);
$pdf->Cell(0, 8, convertirUTF8('Año de Disfrute: ') . $anio_disfrute, 0, 1, 'C'); // 8 en lugar de 10
$pdf->SetTextColor(0, 0, 0);

// ============ REPETICIÓN PARA EL SEGUNDO FORMULARIO ============
// (Se repite exactamente el mismo código pero reducido)

// ============ TERCERA PÁGINA (Segundo formulario completo) ============
$pdf->AddPage();

// Exactamente el mismo código que la primera página, pero más compacto...

// Generar PDF
$nombre_archivo = 'Notificacion_Vacaciones_' . $empleado['ci'] . '.pdf';
$pdf->Output($nombre_archivo, 'D');