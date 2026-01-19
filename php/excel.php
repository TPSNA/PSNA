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

// Crear hoja adicional para estadísticas (reemplaza la sección actual)
$spreadsheet->createSheet();
$spreadsheet->setActiveSheetIndex(1);
$sheet2 = $spreadsheet->getActiveSheet();
$sheet2->setTitle('Estadísticas Empleados');

// Título estadísticas
$sheet2->setCellValue('A1', 'ESTADÍSTICAS DE EMPLEADOS');
$sheet2->mergeCells('A1:D1');
$sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet2->getStyle('A1')->getAlignment()->setHorizontal('center');

// Obtener estadísticas reales de la base de datos
$sql_estadisticas = "SELECT 
    COUNT(*) as total_empleados,
    SUM(CASE WHEN estatus = 'ACTIVO' THEN 1 ELSE 0 END) as activos,
    SUM(CASE WHEN estatus = 'INACTIVO' THEN 1 ELSE 0 END) as inactivos,
    SUM(CASE WHEN tipo_trabajador = 'CTD' THEN 1 ELSE 0 END) as ctd,
    SUM(CASE WHEN tipo_trabajador = 'CTI' THEN 1 ELSE 0 END) as cti,
    SUM(CASE WHEN tipo_trabajador = 'LNR' THEN 1 ELSE 0 END) as lnr,
    SUM(CASE WHEN sexo = 'MASCULINO' THEN 1 ELSE 0 END) as masculino,
    SUM(CASE WHEN sexo = 'FEMENINO' THEN 1 ELSE 0 END) as femenino,
    SUM(CASE WHEN sede = 'ADMIN' THEN 1 ELSE 0 END) as admin,
    SUM(CASE WHEN sede = 'CAFO' THEN 1 ELSE 0 END) as cafo,
    SUM(CASE WHEN sede = 'CATE' THEN 1 ELSE 0 END) as cate,
    SUM(CASE WHEN sede = 'CSAI' THEN 1 ELSE 0 END) as csai,
    SUM(CASE WHEN sede = 'CSB' THEN 1 ELSE 0 END) as csb
FROM empleados";

$stats = $conn->query($sql_estadisticas)->fetch_assoc();

// Estadísticas generales
$sheet2->setCellValue('A3', 'ESTADÍSTICAS GENERALES');
$sheet2->getStyle('A3')->getFont()->setBold(true)->setSize(12);
$sheet2->getStyle('A3')->getFill()->setFillType('solid')->getStartColor()->setARGB('E6F3FF');

$row = 4;
$sheet2->setCellValue('A' . $row, 'Total Empleados:');
$sheet2->setCellValue('B' . $row, $stats['total_empleados']);
$row++;

$sheet2->setCellValue('A' . $row, 'Empleados Activos:');
$sheet2->setCellValue('B' . $row, $stats['activos']);
$sheet2->setCellValue('C' . $row, number_format(($stats['activos'] / $stats['total_empleados']) * 100, 2) . '%');
$sheet2->getStyle('A' . $row . ':C' . $row)->getFill()
       ->setFillType('solid')->getStartColor()->setARGB('D5E8D4'); // Verde claro
$row++;

$sheet2->setCellValue('A' . $row, 'Empleados Inactivos:');
$sheet2->setCellValue('B' . $row, $stats['inactivos']);
$sheet2->setCellValue('C' . $row, number_format(($stats['inactivos'] / $stats['total_empleados']) * 100, 2) . '%');
$sheet2->getStyle('A' . $row . ':C' . $row)->getFill()
       ->setFillType('solid')->getStartColor()->setARGB('F8CECC'); // Rojo claro
$row++;

// Distribución por Tipo de Trabajador
$sheet2->setCellValue('A' . ($row + 1), 'DISTRIBUCIÓN POR TIPO DE TRABAJADOR');
$sheet2->getStyle('A' . ($row + 1))->getFont()->setBold(true)->setSize(12);
$sheet2->getStyle('A' . ($row + 1))->getFill()->setFillType('solid')->getStartColor()->setARGB('FFF2CC');

$row += 2;
$sheet2->setCellValue('A' . $row, 'Tipo');
$sheet2->setCellValue('B' . $row, 'Cantidad');
$sheet2->setCellValue('C' . $row, 'Porcentaje');
$sheet2->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);

$tipos_trabajador = [
    ['CTD', $stats['ctd']],
    ['CTI', $stats['cti']],
    ['LNR', $stats['lnr']],
    ['OTROS', $stats['total_empleados'] - ($stats['ctd'] + $stats['cti'] + $stats['lnr'])]
];

$row++;
foreach ($tipos_trabajador as $tipo) {
    $sheet2->setCellValue('A' . $row, $tipo[0]);
    $sheet2->setCellValue('B' . $row, $tipo[1]);
    $sheet2->setCellValue('C' . $row, number_format(($tipo[1] / $stats['total_empleados']) * 100, 2) . '%');
    $row++;
}

// Distribución por Sexo
$sheet2->setCellValue('A' . ($row + 1), 'DISTRIBUCIÓN POR SEXO');
$sheet2->getStyle('A' . ($row + 1))->getFont()->setBold(true)->setSize(12);
$sheet2->getStyle('A' . ($row + 1))->getFill()->setFillType('solid')->getStartColor()->setARGB('E1D5E7');

$row += 2;
$sheet2->setCellValue('A' . $row, 'Sexo');
$sheet2->setCellValue('B' . $row, 'Cantidad');
$sheet2->setCellValue('C' . $row, 'Porcentaje');
$sheet2->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);

$distribucion_sexo = [
    ['MASCULINO', $stats['masculino']],
    ['FEMENINO', $stats['femenino']]
];

$row++;
foreach ($distribucion_sexo as $sexo) {
    $sheet2->setCellValue('A' . $row, $sexo[0]);
    $sheet2->setCellValue('B' . $row, $sexo[1]);
    $sheet2->setCellValue('C' . $row, number_format(($sexo[1] / $stats['total_empleados']) * 100, 2) . '%');
    $row++;
}

// Distribución por Sede
$sheet2->setCellValue('A' . ($row + 1), 'DISTRIBUCIÓN POR SEDE');
$sheet2->getStyle('A' . ($row + 1))->getFont()->setBold(true)->setSize(12);
$sheet2->getStyle('A' . ($row + 1))->getFill()->setFillType('solid')->getStartColor()->setARGB('DAE8FC');

$row += 2;
$sheet2->setCellValue('A' . $row, 'Sede');
$sheet2->setCellValue('B' . $row, 'Cantidad');
$sheet2->setCellValue('C' . $row, 'Porcentaje');
$sheet2->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);

$distribucion_sede = [
    ['ADMIN', $stats['admin']],
    ['CAFO', $stats['cafo']],
    ['CATE', $stats['cate']],
    ['CSAI', $stats['csai']],
    ['CSB', $stats['csb']]
];

$row++;
foreach ($distribucion_sede as $sede) {
    $sheet2->setCellValue('A' . $row, $sede[0]);
    $sheet2->setCellValue('B' . $row, $sede[1]);
    $sheet2->setCellValue('C' . $row, number_format(($sede[1] / $stats['total_empleados']) * 100, 2) . '%');
    $row++;
}

// TOP 10 Cargos más comunes
$sql_cargos = "SELECT cargo, COUNT(*) as cantidad 
               FROM empleados 
               WHERE cargo IS NOT NULL AND cargo != ''
               GROUP BY cargo 
               ORDER BY cantidad DESC 
               LIMIT 10";
$cargos_result = $conn->query($sql_cargos);

$sheet2->setCellValue('A' . ($row + 2), 'TOP 10 CARGOS MÁS COMUNES');
$sheet2->getStyle('A' . ($row + 2))->getFont()->setBold(true)->setSize(12);
$sheet2->getStyle('A' . ($row + 2))->getFill()->setFillType('solid')->getStartColor()->setARGB('F5F5F5');

$row += 3;
$sheet2->setCellValue('A' . $row, 'Cargo');
$sheet2->setCellValue('B' . $row, 'Cantidad');
$sheet2->setCellValue('C' . $row, 'Porcentaje');
$sheet2->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);

$row++;
$contador_cargo = 1;
while ($cargo = $cargos_result->fetch_assoc()) {
    $sheet2->setCellValue('A' . $row, $cargo['cargo']);
    $sheet2->setCellValue('B' . $row, $cargo['cantidad']);
    $sheet2->setCellValue('C' . $row, number_format(($cargo['cantidad'] / $stats['total_empleados']) * 100, 2) . '%');
    
    // Color para los primeros 3 puestos
    if ($contador_cargo == 1) {
        $sheet2->getStyle('A' . $row . ':C' . $row)->getFill()
               ->setFillType('solid')->getStartColor()->setARGB('FFF2CC'); // Amarillo oro
    } elseif ($contador_cargo == 2) {
        $sheet2->getStyle('A' . $row . ':C' . $row)->getFill()
               ->setFillType('solid')->getStartColor()->setARGB('E6E6E6'); // Gris plata
    } elseif ($contador_cargo == 3) {
        $sheet2->getStyle('A' . $row . ':C' . $row)->getFill()
               ->setFillType('solid')->getStartColor()->setARGB('F4CCCC'); // Bronce claro
    }
    
    $row++;
    $contador_cargo++;
}

// Distribución por Estado Civil
$sql_estado_civil = "SELECT estado_civil, COUNT(*) as cantidad 
                     FROM empleados 
                     WHERE estado_civil IS NOT NULL AND estado_civil != ''
                     GROUP BY estado_civil 
                     ORDER BY cantidad DESC";
$estado_civil_result = $conn->query($sql_estado_civil);

$sheet2->setCellValue('A' . ($row + 2), 'DISTRIBUCIÓN POR ESTADO CIVIL');
$sheet2->getStyle('A' . ($row + 2))->getFont()->setBold(true)->setSize(12);
$sheet2->getStyle('A' . ($row + 2))->getFill()->setFillType('solid')->getStartColor()->setARGB('FFE6CC');

$row += 3;
$sheet2->setCellValue('A' . $row, 'Estado Civil');
$sheet2->setCellValue('B' . $row, 'Cantidad');
$sheet2->setCellValue('C' . $row, 'Porcentaje');
$sheet2->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);

$row++;
while ($estado = $estado_civil_result->fetch_assoc()) {
    $sheet2->setCellValue('A' . $row, $estado['estado_civil']);
    $sheet2->setCellValue('B' . $row, $estado['cantidad']);
    $sheet2->setCellValue('C' . $row, number_format(($estado['cantidad'] / $stats['total_empleados']) * 100, 2) . '%');
    $row++;
}

// Información del reporte
$sheet2->setCellValue('A' . ($row + 2), 'INFORMACIÓN DEL REPORTE');
$sheet2->getStyle('A' . ($row + 2))->getFont()->setBold(true)->setSize(12);
$sheet2->getStyle('A' . ($row + 2))->getFill()->setFillType('solid')->getStartColor()->setARGB('E6F3FF');

$row += 3;
$sheet2->setCellValue('A' . $row, 'Fecha de generación:');
$sheet2->setCellValue('B' . $row, date('d/m/Y H:i:s'));

$row++;
$sheet2->setCellValue('A' . $row, 'Usuario generador:');
$sheet2->setCellValue('B' . $row, $_SESSION['username'] . ' (' . $_SESSION['rol'] . ')');

$row++;
$sheet2->setCellValue('A' . $row, 'Total de registros:');
$sheet2->setCellValue('B' . $row, $stats['total_empleados']);

$row++;
$sheet2->setCellValue('A' . $row, 'Sistema:');
$sheet2->setCellValue('B' . $row, 'SAINA - Sistema de Administración Integral');

// Autoajustar columnas en la hoja de estadísticas
foreach (range('A', 'D') as $columnID) {
    $sheet2->getColumnDimension($columnID)->setAutoSize(true);
}

// Aplicar bordes a todas las celdas con datos
$sheet2->getStyle('A1:D' . $row)->getBorders()
    ->getAllBorders()->setBorderStyle('thin');

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