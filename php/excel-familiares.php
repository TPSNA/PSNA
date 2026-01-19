<?php
// php/excel-familiares.php - EXPORTAR DATOS DE FAMILIARES A EXCEL
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
require_once('../../admin/includes/database.php');

// Consulta para obtener todos los familiares con datos del trabajador
$sql = "SELECT 
            f.id,
            f.ci_trabajador,
            e.primer_nombre as nombre_trabajador,
            e.primer_apellido as apellido_trabajador,
            CONCAT(e.primer_nombre, ' ', COALESCE(e.segundo_nombre, ''), ' ', 
                   e.primer_apellido, ' ', COALESCE(e.segundo_apellido, '')) as trabajador_completo,
            f.cedula_familiar,
            f.nombre_familiar,
            f.apellido_familiar,
            CONCAT(f.nombre_familiar, ' ', f.apellido_familiar) as familiar_completo,
            f.parentesco,
            f.edad,
            f.peso,
            f.altura,
            f.talla_zapato,
            f.talla_camisa,
            f.talla_pantalon,
            f.tipo_sangre,
            f.fecha_registro,
            e.cargo,
            e.dependencia,
            e.estatus as estatus_trabajador,
            e.telefono,
            e.correo
        FROM familiares f
        INNER JOIN empleados e ON f.ci_trabajador = e.ci
        ORDER BY e.ci, f.parentesco, f.fecha_registro DESC";

// Usar $conn
$result = $conn->query($sql);

// Verificar si la consulta fue exitosa
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Crear nuevo Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Título del reporte
$sheet->setTitle('Reporte Familiares');
$sheet->mergeCells('A1:R1');
$sheet->setCellValue('A1', 'REPORTE DE FAMILIARES DE EMPLEADOS - SAINA');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

// Información del reporte
$sheet->setCellValue('A2', 'Fecha de generación:');
$sheet->setCellValue('B2', date('d/m/Y H:i:s'));
$sheet->setCellValue('A3', 'Total de familiares:');
$sheet->setCellValue('B3', $result->num_rows);
$sheet->setCellValue('A4', 'Generado por:');
$sheet->setCellValue('B4', $_SESSION['username'] . ' (' . $_SESSION['rol'] . ')');

// Obtener estadísticas para mostrar
$sql_stats = "SELECT 
                COUNT(DISTINCT ci_trabajador) as trabajadores_con_familiares,
                COUNT(DISTINCT parentesco) as tipos_parentesco,
                parentesco,
                COUNT(*) as cantidad
              FROM familiares 
              GROUP BY parentesco
              ORDER BY cantidad DESC";
$stats_result = $conn->query($sql_stats);

$sheet->setCellValue('A5', 'Trabajadores con familiares:');
$sheet->setCellValue('B5', $stats_result->fetch_assoc()['trabajadores_con_familiares'] ?? 0);

// Encabezados de columnas
$headers = [
    'ID Familiar', 'Cédula Trabajador', 'Trabajador', 'Cargo', 'Dependencia',
    'Cédula Familiar', 'Nombre Familiar', 'Apellido Familiar', 'Parentesco',
    'Edad', 'Peso (kg)', 'Altura (m)', 'Talla Zapato', 'Talla Camisa',
    'Talla Pantalón', 'Tipo Sangre', 'Fecha Registro', 'Estatus Trabajador'
];

// Aplicar estilo a los encabezados
$row = 7;
$col = 1; // Comenzar en columna A (1)
foreach ($headers as $header) {
    $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;
    $sheet->setCellValue($cell, $header);
    
    $sheet->getStyle($cell)->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => [
            'fillType' => 'solid',
            'startColor' => ['rgb' => '3498db']  // Azul para diferenciar del reporte de empleados
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => 'thin',
                'color' => ['rgb' => '000000']
            ]
        ],
        'alignment' => [
            'horizontal' => 'center',
            'vertical' => 'center',
            'wrapText' => true
        ]
    ]);
    
    // Autoajustar ancho de columnas
    $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col))
          ->setAutoSize(true);
    
    $col++;
}

// Llenar datos
$row = 8;
$contador = 1;
$ci_trabajador_anterior = '';

while ($familiar = $result->fetch_assoc()) {
    $col = 1;
    
    // Cambiar color de fondo por cada trabajador diferente
    if ($ci_trabajador_anterior != $familiar['ci_trabajador']) {
        $ci_trabajador_anterior = $familiar['ci_trabajador'];
        $contador++;
    }
    
    $data = [
        $familiar['id'],
        $familiar['ci_trabajador'],
        $familiar['trabajador_completo'],
        $familiar['cargo'],
        $familiar['dependencia'],
        $familiar['cedula_familiar'],
        $familiar['nombre_familiar'],
        $familiar['apellido_familiar'],
        $familiar['parentesco'],
        $familiar['edad'],
        $familiar['peso'] ? number_format($familiar['peso'], 2) : '',
        $familiar['altura'] ? number_format($familiar['altura'], 2) : '',
        $familiar['talla_zapato'],
        $familiar['talla_camisa'],
        $familiar['talla_pantalon'],
        $familiar['tipo_sangre'],
        $familiar['fecha_registro'] ? date('d/m/Y', strtotime($familiar['fecha_registro'])) : '',
        $familiar['estatus_trabajador']
    ];
    
    foreach ($data as $value) {
        $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;
        $sheet->setCellValue($cell, $value);
        
        // Alternar colores de filas por trabajador para mejor legibilidad
        if ($contador % 2 == 0) {
            $sheet->getStyle($cell)->getFill()
                  ->setFillType('solid')
                  ->getStartColor()->setARGB('E8F4FD'); // Azul claro
        } else {
            $sheet->getStyle($cell)->getFill()
                  ->setFillType('solid')
                  ->getStartColor()->setARGB('F0F8FF'); // Azul muy claro
        }
        
        // Bordes para todas las celdas
        $sheet->getStyle($cell)->getBorders()
              ->getAllBorders()->setBorderStyle('thin');
        
        $col++;
    }
    $row++;
}

// Autoajustar todas las columnas
foreach (range('A', 'R') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Crear hoja adicional para estadísticas
$spreadsheet->createSheet();
$spreadsheet->setActiveSheetIndex(1);
$sheet2 = $spreadsheet->getActiveSheet();
$sheet2->setTitle('Estadísticas Familiares');

// Título estadísticas
$sheet2->setCellValue('A1', 'ESTADÍSTICAS DE FAMILIARES');
$sheet2->mergeCells('A1:C1');
$sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet2->getStyle('A1')->getAlignment()->setHorizontal('center');

// Estadísticas generales
$sheet2->setCellValue('A3', 'ESTADÍSTICAS GENERALES');
$sheet2->getStyle('A3')->getFont()->setBold(true)->setSize(12);

$sheet2->setCellValue('A4', 'Total de familiares registrados:');
$sheet2->setCellValue('B4', $result->num_rows);

$sheet2->setCellValue('A5', 'Trabajadores con familiares registrados:');
$sheet2->setCellValue('B5', $stats_result->fetch_assoc()['trabajadores_con_familiares'] ?? 0);

// Obtener estadísticas de parentesco
$sql_parentesco = "SELECT parentesco, COUNT(*) as cantidad 
                   FROM familiares 
                   GROUP BY parentesco 
                   ORDER BY cantidad DESC";
$parentesco_result = $conn->query($sql_parentesco);

$sheet2->setCellValue('A7', 'DISTRIBUCIÓN POR PARENTESCO');
$sheet2->getStyle('A7')->getFont()->setBold(true)->setSize(12);

$row_stats = 8;
$sheet2->setCellValue('A' . $row_stats, 'Parentesco');
$sheet2->setCellValue('B' . $row_stats, 'Cantidad');
$sheet2->setCellValue('C' . $row_stats, 'Porcentaje');
$sheet2->getStyle('A' . $row_stats . ':C' . $row_stats)->getFont()->setBold(true);

$row_stats++;
$total_familiares = $result->num_rows;
while ($parentesco = $parentesco_result->fetch_assoc()) {
    $sheet2->setCellValue('A' . $row_stats, $parentesco['parentesco']);
    $sheet2->setCellValue('B' . $row_stats, $parentesco['cantidad']);
    $sheet2->setCellValue('C' . $row_stats, number_format(($parentesco['cantidad'] / $total_familiares) * 100, 2) . '%');
    $row_stats++;
}

// Obtener trabajadores con más familiares
$sql_top_trabajadores = "SELECT 
                            f.ci_trabajador,
                            CONCAT(e.primer_nombre, ' ', e.primer_apellido) as trabajador,
                            COUNT(*) as total_familiares
                         FROM familiares f
                         INNER JOIN empleados e ON f.ci_trabajador = e.ci
                         GROUP BY f.ci_trabajador
                         ORDER BY total_familiares DESC
                         LIMIT 10";
$top_result = $conn->query($sql_top_trabajadores);

$sheet2->setCellValue('A' . ($row_stats + 2), 'TOP 10 TRABAJADORES CON MÁS FAMILIARES');
$sheet2->getStyle('A' . ($row_stats + 2))->getFont()->setBold(true)->setSize(12);

$row_top = $row_stats + 3;
$sheet2->setCellValue('A' . $row_top, 'Trabajador');
$sheet2->setCellValue('B' . $row_top, 'Cédula');
$sheet2->setCellValue('C' . $row_top, 'Total Familiares');
$sheet2->getStyle('A' . $row_top . ':C' . $row_top)->getFont()->setBold(true);

$row_top++;
$contador_top = 1;
while ($top = $top_result->fetch_assoc()) {
    $sheet2->setCellValue('A' . $row_top, $top['trabajador']);
    $sheet2->setCellValue('B' . $row_top, $top['ci_trabajador']);
    $sheet2->setCellValue('C' . $row_top, $top['total_familiares']);
    
    // Color para los primeros 3 puestos
    if ($contador_top == 1) {
        $sheet2->getStyle('A' . $row_top . ':C' . $row_top)->getFill()
               ->setFillType('solid')->getStartColor()->setARGB('FFF2CC'); // Amarillo oro
    } elseif ($contador_top == 2) {
        $sheet2->getStyle('A' . $row_top . ':C' . $row_top)->getFill()
               ->setFillType('solid')->getStartColor()->setARGB('E6E6E6'); // Gris plata
    } elseif ($contador_top == 3) {
        $sheet2->getStyle('A' . $row_top . ':C' . $row_top)->getFill()
               ->setFillType('solid')->getStartColor()->setARGB('F4CCCC'); // Bronce claro
    }
    
    $row_top++;
    $contador_top++;
}

// Distribución por estatus del trabajador
$sql_estatus = "SELECT 
                    e.estatus,
                    COUNT(*) as total_familiares
                FROM familiares f
                INNER JOIN empleados e ON f.ci_trabajador = e.ci
                GROUP BY e.estatus";
$estatus_result = $conn->query($sql_estatus);

$sheet2->setCellValue('A' . ($row_top + 2), 'DISTRIBUCIÓN POR ESTATUS DEL TRABAJADOR');
$sheet2->getStyle('A' . ($row_top + 2))->getFont()->setBold(true)->setSize(12);

$row_estatus = $row_top + 3;
$sheet2->setCellValue('A' . $row_estatus, 'Estatus');
$sheet2->setCellValue('B' . $row_estatus, 'Total Familiares');
$sheet2->getStyle('A' . $row_estatus . ':B' . $row_estatus)->getFont()->setBold(true);

$row_estatus++;
while ($estatus = $estatus_result->fetch_assoc()) {
    $sheet2->setCellValue('A' . $row_estatus, $estatus['estatus']);
    $sheet2->setCellValue('B' . $row_estatus, $estatus['total_familiares']);
    
    // Color para activos vs inactivos
    if ($estatus['estatus'] == 'ACTIVO') {
        $sheet2->getStyle('A' . $row_estatus . ':B' . $row_estatus)->getFill()
               ->setFillType('solid')->getStartColor()->setARGB('D5E8D4'); // Verde claro
    } else {
        $sheet2->getStyle('A' . $row_estatus . ':B' . $row_estatus)->getFill()
               ->setFillType('solid')->getStartColor()->setARGB('F8CECC'); // Rojo claro
    }
    
    $row_estatus++;
}

// Información del reporte
$sheet2->setCellValue('A' . ($row_estatus + 2), 'INFORMACIÓN DEL REPORTE');
$sheet2->getStyle('A' . ($row_estatus + 2))->getFont()->setBold(true)->setSize(12);

$sheet2->setCellValue('A' . ($row_estatus + 3), 'Fecha de generación:');
$sheet2->setCellValue('B' . ($row_estatus + 3), date('d/m/Y H:i:s'));

$sheet2->setCellValue('A' . ($row_estatus + 4), 'Usuario generador:');
$sheet2->setCellValue('B' . ($row_estatus + 4), $_SESSION['username'] . ' (' . $_SESSION['rol'] . ')');

$sheet2->setCellValue('A' . ($row_estatus + 5), 'Sistema:');
$sheet2->setCellValue('B' . ($row_estatus + 5), 'SAINA - Sistema de Administración Integral');

// Autoajustar columnas en la hoja de estadísticas
foreach (range('A', 'C') as $columnID) {
    $sheet2->getColumnDimension($columnID)->setAutoSize(true);
}

// Volver a la primera hoja
$spreadsheet->setActiveSheetIndex(0);

// Cerrar conexión
$conn->close();

// Limpiar el buffer
ob_end_clean();  

// Configurar encabezados HTTP para descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte_familiares_saina_' . date('Y-m-d_His') . '.xlsx"');
header('Cache-Control: max-age=0');
header('Pragma: public');

// Crear escritor y enviar al navegador
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit();
?>