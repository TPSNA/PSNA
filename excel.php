<?php
ob_start();  // Bufferizar output para evitar corrupción del archivo

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// Incluir tu conexión a la BD (ajusta la ruta si 'conexion_bd.php' no está en la misma carpeta php/)
include("conexion_bd.php");
// Crear una nueva hoja de cálculo
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
// Agregar encabezados en la fila 1
$sheet->setCellValue('A1', 'id_empleado');
$sheet->setCellValue('B1', 'ci');
$sheet->setCellValue('C1', 'primer_nombre');
$sheet->setCellValue('D1', 'segundo_nombre');
$sheet->setCellValue('E1', 'primer_apellido');
$sheet->setCellValue('F1', 'segundo_apellido');
$sheet->setCellValue('G1', 'fecha_nacimiento');
$sheet->setCellValue('H1', 'edad');
$sheet->setCellValue('I1', 'correo');
$sheet->setCellValue('J1', 'direccion');
$sheet->setCellValue('K1', 'telefono');
$sheet->setCellValue('L1', 'fotos');
// consulta SQL 
$sql = "SELECT * FROM empleado";
$ejecutar = mysqli_query($conexion, $sql);
// Verificar si hay resultados y agregarlos a la hoja
if ($ejecutar && mysqli_num_rows($ejecutar) > 0) {
    $rowNumber = 2;  // Empezar desde la fila 2
    while ($fila = mysqli_fetch_array($ejecutar)) {
        $sheet->setCellValue('A' . $rowNumber, $fila[0]);  
        $sheet->setCellValue('B' . $rowNumber, $fila[1]); 
        $sheet->setCellValue('C' . $rowNumber, $fila[2]); 
        $sheet->setCellValue('D' . $rowNumber, $fila[3]); 
        $sheet->setCellValue('E' . $rowNumber, $fila[4]);  
        $sheet->setCellValue('F' . $rowNumber, $fila[5]);  
        $sheet->setCellValue('G' . $rowNumber, $fila[6]);  
        $sheet->setCellValue('H' . $rowNumber, $fila[7]);  
        $sheet->setCellValue('I' . $rowNumber, $fila[8]);  
        $sheet->setCellValue('J' . $rowNumber, $fila[9]); 
        $sheet->setCellValue('K' . $rowNumber, $fila[10]); 
        $sheet->setCellValue('L' . $rowNumber, $fila[11]); 
        $rowNumber++;
    }
} else {
    // Si no hay datos, mostrar mensaje en la hoja
    $sheet->setCellValue('A2', 'No se encontraron datos en la tabla empleado.');
}
// Cerrar la conexión a la BD
mysqli_close($conexion);
// Limpiar el buffer y configurar headers para forzar la descarga del archivo Excel
ob_end_clean();  
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="archivo.xlsx"');
header('Cache-Control: max-age=0');
// Crear el escritor y enviar el archivo al navegador
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;  
?>
