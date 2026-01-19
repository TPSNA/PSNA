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

// Función para obtener el mes en español
function obtenerMesEspanol($numero_mes) {
    $meses = array(
        '01' => 'enero', '02' => 'febrero', '03' => 'marzo', '04' => 'abril',
        '05' => 'mayo', '06' => 'junio', '07' => 'julio', '08' => 'agosto',
        '09' => 'septiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre'
    );
    return $meses[$numero_mes];
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

// Crear PDF con configuración personalizada
class PDF extends FPDF {
    function __construct($orientation='P', $unit='cm', $size='Letter') {
        parent::__construct($orientation, $unit, $size);
    }
    
    function Header() {
        // Configurar márgenes según especificaciones
        $this->SetMargins(3, 4, 2); // Izquierdo: 3cm, Superior: 4cm, Derecho: 2cm
        
        // Cintillo institucional - centrado horizontalmente
        $this->SetY(1.5); // 1.5 cm desde el borde superior para dar espacio
        $cintillo_ancho = 17.5; // Ancho del cintillo: 17.5 cm
        $cintillo_alto = 2.5; // Alto del cintillo: 2.5 cm
        $x_pos = ($this->GetPageWidth() - $cintillo_ancho) / 2; // Centrar horizontalmente
        
        $this->Image('../../../imagenes/cintillo.png',  
                     $x_pos,
                     1.5,
                     $cintillo_ancho,
                     $cintillo_alto);
        
        // Título - 3 espacios después del cintillo
        $this->SetY(4.5); // 4.5 cm desde el borde superior (1.5 + 2.5 + 0.5)
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 0.8, convertirUTF8('CONSTANCIA DE EGRESO'), 0, 1, 'C');
        $this->Ln(0.8); // 2 espacios después del título
    }
    
    function Footer() {
        // Posicionar en el margen inferior de 3.5 cm
        $this->SetY(-3.5);
        
        // Línea separadora fina (0.5 pt) antes del texto del pie de página
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.05); // 0.5 puntos
        $this->Line(3, $this->GetY(), $this->GetPageWidth() - 2, $this->GetY());
        $this->Ln(0.3);
        
        // Información de contacto en el pie de página
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 0.4, convertirUTF8('Carrera 16 entre calles 41 y 42 frente al Parque Ayacucho Quinta Josefita.'), 0, 1, 'C');
        $this->Cell(0, 0.4, convertirUTF8('Teléfonos 0251-4460837 / 0251-4471108. SAINA LARA RIF. G-20003146-8'), 0, 1, 'C');
    }
    
    // Función para escribir texto con formato mixto (normal/negrita) como un solo párrafo justificado
    function WriteMixedParagraph($text_parts, $line_height = 0.5) {
        $ancho_disponible = $this->GetPageWidth() - 3 - 2; // Ancho total - margen izquierdo - margen derecho
        $x = $this->GetX();
        $y = $this->GetY();
        $line_start_x = $x;
        $line_text = '';
        $line_words = array();
        
        // Procesar cada parte del texto
        foreach ($text_parts as $part) {
            $text = $part['text'];
            $bold = $part['bold'];
            
            // Dividir el texto en palabras
            $words = explode(' ', $text);
            
            foreach ($words as $word) {
                if ($word === '') continue;
                
                // Determinar el estilo para esta palabra
                $style = $bold ? 'B' : '';
                
                // Calcular el ancho de la palabra con el estilo correspondiente
                $this->SetFont('Arial', $style, 12);
                $word_width = $this->GetStringWidth(convertirUTF8($word . ' '));
                
                // Si la palabra no cabe en la línea actual
                if ($x + $word_width > $line_start_x + $ancho_disponible && $x > $line_start_x) {
                    // Escribir la línea actual justificada
                    $this->writeJustifiedLine($line_words, $line_start_x, $y, $ancho_disponible, $line_height);
                    
                    // Preparar nueva línea
                    $y += $line_height * 1.5; // Interlineado 1.5
                    $x = $line_start_x;
                    $line_words = array();
                }
                
                // Agregar palabra a la línea actual
                $line_words[] = array('text' => $word, 'bold' => $bold);
                $x += $word_width;
            }
        }
        
        // Escribir la última línea
        if (!empty($line_words)) {
            $this->writeJustifiedLine($line_words, $line_start_x, $y, $ancho_disponible, $line_height);
            $y += $line_height * 1.5;
        }
        
        // Actualizar posición
        $this->SetXY($line_start_x, $y);
    }
    
    // Función auxiliar para escribir una línea justificada
    function writeJustifiedLine($words, $start_x, $y, $max_width, $line_height) {
        if (empty($words)) return;
        
        // Calcular el ancho total de las palabras sin espacios
        $total_word_width = 0;
        foreach ($words as $word_data) {
            $this->SetFont('Arial', $word_data['bold'] ? 'B' : '', 12);
            $total_word_width += $this->GetStringWidth(convertirUTF8($word_data['text']));
        }
        
        // Calcular el espacio adicional necesario para justificar
        $num_spaces = count($words) - 1;
        $space_width = $num_spaces > 0 ? ($max_width - $total_word_width) / $num_spaces : 0;
        
        // Escribir las palabras con el espaciado calculado
        $x = $start_x;
        foreach ($words as $index => $word_data) {
            $this->SetFont('Arial', $word_data['bold'] ? 'B' : '', 12);
            $word = convertirUTF8($word_data['text']);
            $word_width = $this->GetStringWidth($word);
            
            $this->SetXY($x, $y);
            $this->Cell($word_width, $line_height, $word, 0, 0, 'L');
            
            $x += $word_width;
            
            // Agregar espacio entre palabras (justificado)
            if ($index < count($words) - 1) {
                $x += $space_width;
            }
        }
    }
    
    // Función para escribir texto justificado simple
    function WriteJustifiedSimple($w, $h, $txt, $border=0, $align='J', $fill=false) {
        $line_height = $h * 1.5; // Interlineado 1.5
        $this->MultiCell($w, $line_height, $txt, $border, $align, $fill);
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

// Posicionar después del título (2 espacios después)
$pdf->SetY(6.1); // 4.5 (inicio título) + 0.8 (altura título) + 0.8 (espacio) = 6.1

// Definir los textos con sus estilos para el párrafo justificado
$text_parts = array(
    array('text' => "Quien suscribe, Directora de Talento Humano del Servicio de Atención Integral al Niño, Niña y al Adolescente (", 'bold' => false),
    array('text' => "SAINA-LARA", 'bold' => true),
    array('text' => "), por medio de la presente hago constar que el (a) ciudadano (a): ", 'bold' => false),
    array('text' => mb_strtoupper($nombre_completo, 'UTF-8'), 'bold' => true),
    array('text' => ", titular de la Cédula de Identidad ", 'bold' => false),
    array('text' => $cedula, 'bold' => true),
    array('text' => ", prestó sus servicios en esta Institución, desde el ", 'bold' => false),
    array('text' => $fecha_ingreso, 'bold' => true),
    array('text' => ", hasta, cumpliendo funciones como ", 'bold' => false),
    array('text' => mb_strtoupper($empleado['cargo'], 'UTF-8'), 'bold' => true),
    array('text' => ", devengando un salario mensual de: ", 'bold' => false),
    array('text' => number_format($total_ingreso, 2, ',', '.') . " Bs.", 'bold' => true),
);

// Escribir el párrafo completo como un solo párrafo justificado con partes en negrita
$pdf->WriteMixedParagraph($text_parts, 0.5);

$pdf->Ln(0.8); // 2 espacios después del cuerpo

// Información de contacto para confirmación
$info_contacto = "Información que deberá ser confirmada al teléfono 0251/447.11.08";
$pdf->SetFont('Arial', '', 12);
$pdf->WriteJustifiedSimple(0, 0.5, convertirUTF8($info_contacto));

$pdf->Ln(0.8); // 2 espacios antes de la fecha

// Fecha en formato "a los 09 de enero del 2026"
$fecha_formateada = "En Barquisimeto a los " . date('d') . " de " . obtenerMesEspanol(date('m')) . " del " . date('Y');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 0.6, convertirUTF8($fecha_formateada), 0, 1, 'L');

$pdf->Ln(1.2); // 3 espacios para la firma

// Firma
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 0.6, convertirUTF8("Atentamente,"), 0, 1, 'C');
$pdf->Ln(1.0); // 4-5 espacios para la firma física/digital

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 0.6, convertirUTF8("LCDA MARIA E. LANDAETA P."), 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 0.6, convertirUTF8("DIRECTORA DE TALENTO HUMANO"), 0, 1, 'C');
$pdf->Cell(0, 0.6, convertirUTF8("Resolución Nº 076-2025 del 14 de Octubre del 2025"), 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 0.6, convertirUTF8("SAINA-LARA"), 0, 1, 'C');

$pdf->Ln(0.8); // 2 espacios antes de "PARA EFECTOS PERSONALES"

// "PARA EFECTOS PERSONALES" alineado a la izquierda
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 0.6, convertirUTF8("PARA EFECTOS PERSONALES"), 0, 1, 'L');

// Salida del PDF
$pdf->Output('Constancia_Egreso_' . $empleado['ci'] . '.pdf', 'D');
?>