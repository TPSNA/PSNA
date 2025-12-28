<?php
// php/excel.php - EXPORTAR DATOS A EXCEL
ob_start();  // Agregado para evitar problemas de salida

session_start();

// Verificar sesión (tanto admin como usuario pueden exportar)
if (!isset($_SESSION['username'])) {
    header("Location: ../admin/login.php");
    exit();
}

// Incluir PHPSpreadsheet (si usas Composer)
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

// Conexión a la base de datos (corregida la ruta)
require_once('../../admin/includes/database.php');  // Ruta correcta basada en tu confirmación

// Consulta para obtener todos los empleados con datos completos
$sql = "SELECT 
            e.id,
            e.nacionalidad,
            e.ci,
            CONCAT(e.primer_nombre, ' ', COALESCE(e.segundo_nombre, '')) as nombres,
            CONCAT(e.primer_apellido, ' ', COALESCE(e.segundo_apellido, '')) as apellidos,
            e.fecha_nacimiento,
            e.sexo,
            e.estado_civil,
            e.direccion_ubicacion,
            e.telefono,
            e.correo,
            e.cuenta_bancaria,
            e.tipo_trabajador,
            e.grado_instruccion,
            e.cargo,
            e.sede,
            e.dependencia,
            e.fecha_ingreso,
            e.cod_siantel,
            e.ubicacion_estante,
            e.estatus,
            e.fecha_egreso,
            e.motivo_retiro,
            e.tipo_sangre,
            e.lateralidad,
            e.peso_trabajador,
            e.altura_trabajador,
            e.calzado_trabajador,
            e.camisa_trabajador,
            e.pantalon_trabajador,
            e.fecha_registro,
            COUNT(f.id) as total_familiares
        FROM empleados e
        LEFT JOIN familiares f ON e.ci = f.ci_trabajador
        GROUP BY e.id
        ORDER BY e.fecha_registro DESC";

// Usar $conn (asumiendo que database.php define $conn como en versiones anteriores)
$result = $conn->query($sql);

// Verificar si la consulta fue exitosa
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Crear nuevo Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Título del reporte
$sheet->setTitle('Reporte Empleados');
$sheet->mergeCells('A1:AE1');
$sheet->setCellValue('A1', 'REPORTE COMPLETO DE EMPLEADOS - SAINA');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');  // Cambiado a string

// Información del reporte
$sheet->setCellValue('A2', 'Fecha de generación:');
$sheet->setCellValue('B2', date('d/m/Y H:i:s'));
$sheet->setCellValue('A3', 'Total de empleados:');
$sheet->setCellValue('B3', $result->num_rows);
$sheet->setCellValue('A4', 'Generado por:');
$sheet->setCellValue('B4', $_SESSION['username'] . ' (' . $_SESSION['rol'] . ')');

// Encabezados de columnas
$headers = [
    'ID', 'Nacionalidad', 'Cédula', 'Nombres', 'Apellidos', 
    'Fecha Nacimiento', 'Sexo', 'Estado Civil', 'Dirección', 
    'Teléfono', 'Correo', 'Cuenta Bancaria', 'Tipo Trabajador',
    'Grado Instrucción', 'Cargo', 'Sede', 'Dependencia',
    'Fecha Ingreso', 'Código SIANTEL', 'Ubicación Estante',
    'Estatus', 'Fecha Egreso', 'Motivo Retiro', 'Tipo Sangre',
    'Lateralidad', 'Peso (kg)', 'Altura (cm)', 'Talla Calzado',
    'Talla Camisa', 'Talla Pantalón', 'Fecha Registro', 'Familiares'
];

// Aplicar estilo a los encabezados
$row = 6;
$col = 1; // Comenzar en columna A (1)
foreach ($headers as $header) {
    $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;
    $sheet->setCellValue($cell, $header);
    
    $sheet->getStyle($cell)->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => [
            'fillType' => 'solid',  // Cambiado a string
            'startColor' => ['rgb' => '2c3e50']
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => 'thin',  // Cambiado a string
                'color' => ['rgb' => '000000']
            ]
        ],
        'alignment' => [
            'horizontal' => 'center',  // Cambiado a string
            'vertical' => 'center',  // Cambiado a string
            'wrapText' => true
        ]
    ]);
    
    // Autoajustar ancho de columnas
    $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col))
          ->setAutoSize(true);
    
    $col++;
}

// Llenar datos
$row = 7;
while ($empleado = $result->fetch_assoc()) {
    $col = 1;
    
    $data = [
        $empleado['id'],
        $empleado['nacionalidad'],
        $empleado['ci'],
        $empleado['nombres'],
        $empleado['apellidos'],
        $empleado['fecha_nacimiento'] ? date('d/m/Y', strtotime($empleado['fecha_nacimiento'])) : '',
        $empleado['sexo'],
        $empleado['estado_civil'],
        $empleado['direccion_ubicacion'],
        $empleado['telefono'],
        $empleado['correo'],
        $empleado['cuenta_bancaria'],
        $empleado['tipo_trabajador'],
        $empleado['grado_instruccion'],
        $empleado['cargo'],
        $empleado['sede'],
        $empleado['dependencia'],
        $empleado['fecha_ingreso'] ? date('d/m/Y', strtotime($empleado['fecha_ingreso'])) : '',
        $empleado['cod_siantel'],
        $empleado['ubicacion_estante'],
        $empleado['estatus'],
        $empleado['fecha_egreso'] ? date('d/m/Y', strtotime($empleado['fecha_egreso'])) : '',
        $empleado['motivo_retiro'],
        $empleado['tipo_sangre'],
        $empleado['lateralidad'],
        $empleado['peso_trabajador'],
        $empleado['altura_trabajador'],
        $empleado['calzado_trabajador'],
        $empleado['camisa_trabajador'],
        $empleado['pantalon_trabajador'],
        $empleado['fecha_registro'] ? date('d/m/Y H:i', strtotime($empleado['fecha_registro'])) : '',
        $empleado['total_familiares']
    ];
    
    foreach ($data as $value) {
        $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;
        $sheet->setCellValue($cell, $value);
        
        // Alternar colores de filas para mejor legibilidad
        if ($row % 2 == 0) {
            $sheet->getStyle($cell)->getFill()
                  ->setFillType('solid')  // Cambiado a string
                  ->getStartColor()->setARGB('F2F2F2');
        }
        
        // Bordes para todas las celdas
        $sheet->getStyle($cell)->getBorders()
              ->getAllBorders()->setBorderStyle('thin');  // Cambiado a string
        
        $col++;
    }
    $row++;
}

// Autoajustar todas las columnas
foreach (range('A', 'AE') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Crear hoja adicional para estadísticas
$spreadsheet->createSheet();
$spreadsheet->setActiveSheetIndex(1);
$sheet2 = $spreadsheet->getActiveSheet();
$sheet2->setTitle('Estadísticas');

// Estadísticas generales
$sheet2->setCellValue('A1', 'ESTADÍSTICAS GENERALES');
$sheet2->mergeCells('A1:C1');
$sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(14);

$stats = [
    ['A3', 'Total Empleados', $result->num_rows],
    ['A4', 'Empleados Activos', 0], // Se calculará después
    ['A5', 'Empleados Inactivos', 0],
    ['A6', 'Por Tipo de Trabajador:', ''],
    ['A7', '- CTD', 0],
    ['A8', '- CTI', 0],
    ['A9', '- LNR', 0],
    ['A10', 'Por Sexo:', ''],
    ['A11', '- Masculino', 0],
    ['A12', '- Femenino', 0],
    ['A13', 'Por Sede:', ''],
    ['A14', '- ADMIN', 0],
    ['A15', '- CAFO', 0],
    ['A16', '- CATE', 0],
    ['A17', '- CSAI', 0],
    ['A18', '- CSB', 0],
    ['A19', 'Con Familiares Registrados', 0],
    ['A20', 'Fecha de generación', date('d/m/Y H:i:s')],
    ['A21', 'Usuario generador', $_SESSION['username']]
];

foreach ($stats as $stat) {
    $sheet2->setCellValue($stat[0], $stat[1]);
    $sheet2->setCellValue('B' . substr($stat[0], 1), $stat[2]);
}

// Volver a la primera hoja
$spreadsheet->setActiveSheetIndex(0);

// Cerrar conexión
$conn->close();

// Limpiar el buffer
ob_end_clean();  

// Configurar encabezados HTTP para descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte_empleados_saina_' . date('Y-m-d_His') . '.xlsx"');
header('Cache-Control: max-age=0');
header('Pragma: public');

// Crear escritor y enviar al navegador
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit();
?>