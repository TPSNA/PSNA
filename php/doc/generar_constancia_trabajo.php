<?php
session_start();
require_once('../../includes/database.php');
require_once('../../pdf/fpdf.php');

// Verificar permisos
if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: ../../admin/login.php");
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
$sueldo_base = $_GET['sueldo_base'];
$prima_profesionalizacion = $_GET['prima_profesionalizacion'];
$prima_antiguedad = $_GET['prima_antiguedad'];
$prima_hijos = $_GET['prima_hijos'];
$total_ingreso = $_GET['total_ingreso'];
$horario = $_GET['horario'] ?? '08:00 AM - 12:00 PM / 01:00 PM - 04:00 PM';

// Crear PDF con configuración personalizada
class PDF extends FPDF {
    function __construct($orientation='P', $unit='cm', $size='Letter') {
        parent::__construct($orientation, $unit, $size);
    }
    
    function Header() {
        // Configurar márgenes
        $this->SetMargins(3, 4, 2); // Izquierdo: 3cm, Superior: 4cm, Derecho: 2cm
        
        // Cintillo institucional
        $this->SetY(1.5);
        $this->Image('../../../imagenes/cintillo.png', 
                     ($this->GetPageWidth() - 18)/2, // Corregido: Usar GetPageWidth() en lugar de $this->w (protected)
                     1.5,
                     18,
                     2.5);
        
        // Título
        $this->SetY(4.5);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 1, convertirUTF8('CONSTANCIA'), 0, 1, 'C');
        $this->Ln(0.5);
    }
    
    function Footer() {
        // Posicionar a 3 cm del borde inferior
        $this->SetY(-3);
        
        // Línea separadora fina
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.05);
        $this->Line(3, $this->GetY(), $this->GetPageWidth() - 2, $this->GetY()); // Corregido: Usar GetPageWidth() en lugar de $this->w (protected)
        $this->Ln(0.3);
        
        // Información de contacto
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 0.4, convertirUTF8('Carrera 16 entre calles 41 y 42 frente al Parque Ayacucho Quinta Josefita.'), 0, 1, 'C');
        $this->Cell(0, 0.4, convertirUTF8('Teléfonos 0251-4460837 / 0251-4471108. SAINA LARA RIF. G-20003146-8'), 0, 1, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// Datos del empleado
$fecha_actual = date('d/m/Y');
$nombre_completo = $empleado['primer_nombre'] . ' ' . 
                  ($empleado['segundo_nombre'] ? $empleado['segundo_nombre'] . ' ' : '') . 
                  $empleado['primer_apellido'] . ' ' . 
                  ($empleado['segundo_apellido'] ? $empleado['segundo_apellido'] : '');
$cedula = $empleado['nacionalidad'] . '-' . $empleado['ci'];
$fecha_ingreso = date('d/m/Y', strtotime($empleado['fecha_ingreso']));

// Configurar fuente para el cuerpo
$pdf->SetFont('Arial', '', 11);

// Posicionar después del título
$pdf->SetY(5.5);

// Texto completo del párrafo justificado
$texto_completo = "Quien suscribe, Directora de Talento Humano del Servicio de Atención Integral al Niño, Niña y al Adolescente (SAINA-LARA), por medio de la presente hago constar que el (a) ciudadano (a): " . strtoupper($nombre_completo) . ", titular de la Cédula de Identidad " . $cedula . ", presta sus servicios en esta Institución, desde " . $fecha_ingreso . ", cumpliendo funciones como " . strtoupper($empleado['cargo']) . ", con un Grado BI y un Paso I en un horario administrativo comprendido en la mañana desde las 08:00am-12:00pm y en la tarde desde la 01:00pm a 04:00pm, devengando un salario mensual de:";

$pdf->MultiCell(0, 0.6, convertirUTF8($texto_completo), 0, 'J');

$pdf->Ln(0.8); // Espacio antes de la tabla

// Calcular ancho disponible para la tabla
$ancho_disponible = $pdf->GetPageWidth() - 3 - 2; // Corregido: Usar GetPageWidth() en lugar de $pdf->w (protected)
$ancho_col1 = $ancho_disponible * 0.7;
$ancho_col2 = $ancho_disponible * 0.3;

// Tabla de sueldos
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell($ancho_col1, 0.6, convertirUTF8('Concepto'), 0, 0, 'L');
$pdf->Cell($ancho_col2, 0.6, convertirUTF8('Monto (Bs)'), 0, 1, 'R');

// Línea separadora
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.02);
$pdf->Line(3, $pdf->GetY(), $pdf->GetPageWidth() - 2, $pdf->GetY()); // Corregido: Usar GetPageWidth() en lugar de $pdf->w (protected)
$pdf->Ln(0.1);

// Filas de la tabla
$pdf->SetFont('Arial', '', 11);
$pdf->Cell($ancho_col1, 0.6, convertirUTF8('Sueldo Base'), 0, 0, 'L');
$pdf->Cell($ancho_col2, 0.6, number_format($sueldo_base, 2, ',', '.'), 0, 1, 'R');

$pdf->Cell($ancho_col1, 0.6, convertirUTF8('Prima por profesionalización'), 0, 0, 'L');
$pdf->Cell($ancho_col2, 0.6, number_format($prima_profesionalizacion, 2, ',', '.'), 0, 1, 'R');

$pdf->Cell($ancho_col1, 0.6, convertirUTF8('Prima por antigüedad'), 0, 0, 'L');
$pdf->Cell($ancho_col2, 0.6, number_format($prima_antiguedad, 2, ',', '.'), 0, 1, 'R');

$pdf->Cell($ancho_col1, 0.6, convertirUTF8('Prima por hijo'), 0, 0, 'L');
$pdf->Cell($ancho_col2, 0.6, number_format($prima_hijos, 2, ',', '.'), 0, 1, 'R');

// Línea para total
$pdf->SetLineWidth(0.05);
$pdf->Line(3, $pdf->GetY(), $pdf->GetPageWidth() - 2, $pdf->GetY()); // Corregido: Usar GetPageWidth() en lugar de $pdf->w (protected)
$pdf->Ln(0.1);

// Total en negrita
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell($ancho_col1, 0.6, convertirUTF8('Total de ingreso'), 0, 0, 'L');
$pdf->Cell($ancho_col2, 0.6, number_format($total_ingreso, 2, ',', '.'), 0, 1, 'R');

$pdf->Ln(0.8);

// Nota legal
$pdf->SetFont('Arial', 'I', 10);
$nota = "Nota: Percibe el beneficio de alimentación en ticket mensual establecido en la LOTTIT y no se considera parte del salario de conformidad con lo establecido en la \"Ley del Cesta Ticket Socialista para los Trabajadores y Trabajadores\".";
$pdf->MultiCell(0, 0.5, convertirUTF8($nota), 0, 'J');

$pdf->Ln(0.8);

// Fecha
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 0.6, convertirUTF8("En Barquisimeto, al día " . $fecha_actual), 0, 1, 'L');
$pdf->Ln(1.5);

// Firma
$pdf->Cell(0, 0.6, convertirUTF8("Atentamente,"), 0, 1, 'C');
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 0.6, convertirUTF8("LCDA MARIA E. LANDAETA P."), 0, 1, 'C');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 0.6, convertirUTF8("DIRECTORA DE TALENTO HUMANO"), 0, 1, 'C');
$pdf->Cell(0, 0.6, convertirUTF8("Resolución N° 076-2025 del 14 de Octubre del 2025"), 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 0.6, convertirUTF8("SAINA-LARA"), 0, 1, 'C');

// Salida del PDF
$pdf->Output('Constancia_Trabajo_' . $empleado['ci'] . '.pdf', 'D');
?>