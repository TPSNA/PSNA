<?php
ob_start();  

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include("conexion_bd.php");
// Crear una nueva hoja de cálculo
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
// encabezados en la fila 1
$sheet->setCellValue('A1', 'id');
$sheet->setCellValue('B1', 'nacionalidad');
$sheet->setCellValue('C1', 'ci');
$sheet->setCellValue('D1', 'primer_nombre');
$sheet->setCellValue('E1', 'segundo_nombre');
$sheet->setCellValue('F1', 'primer_apellido');
$sheet->setCellValue('G1', 'segundo_apellido');
$sheet->setCellValue('H1', 'fecha_nacimiento');
$sheet->setCellValue('I1', 'sexo');
$sheet->setCellValue('J1', 'estado_civil');
$sheet->setCellValue('K1', 'direccion_ubicacion');
$sheet->setCellValue('L1', 'telefono');
$sheet->setCellValue('M1', 'correo');
$sheet->setCellValue('N1', 'cuenta_bancaria');
$sheet->setCellValue('O1', 'tipo_trabajador');
$sheet->setCellValue('P1', 'grado_instruccion');
$sheet->setCellValue('Q1', 'cargo');
$sheet->setCellValue('R1', 'sede');
$sheet->setCellValue('S1', 'dependencia');
$sheet->setCellValue('T1', 'fecha_ingreso');
$sheet->setCellValue('U1', 'cod_siantel');
$sheet->setCellValue('V1', 'ubicacion_estante');
$sheet->setCellValue('W1', 'estatus');
$sheet->setCellValue('X1', 'fecha_egreso');
$sheet->setCellValue('Y1', 'motivo_retiro');
$sheet->setCellValue('Z1', 'ubicacion_estante_retiro');
$sheet->setCellValue('AA1', 'tipo_sangre');
$sheet->setCellValue('AB1', 'lateralidad');
$sheet->setCellValue('AC1', 'peso_trabajador');
$sheet->setCellValue('AD1', 'altura_trabajador');
$sheet->setCellValue('AE1', 'calzado_trabajador');
$sheet->setCellValue('AF1', 'camisa_trabajador');
$sheet->setCellValue('AG1', 'pantalon_trabajador');
$sheet->setCellValue('AH1', 'foto');
$sheet->setCellValue('AI1', 'fecha_registro');
// consulta SQL 
$sql = "SELECT * FROM empleados";
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
        $sheet->setCellValue('M' . $rowNumber, $fila[12]);  
        $sheet->setCellValue('N' . $rowNumber, $fila[13]); 
        $sheet->setCellValue('O' . $rowNumber, $fila[14]); 
        $sheet->setCellValue('P' . $rowNumber, $fila[15]); 
        $sheet->setCellValue('Q' . $rowNumber, $fila[16]);  
        $sheet->setCellValue('R' . $rowNumber, $fila[17]);  
        $sheet->setCellValue('S' . $rowNumber, $fila[18]);  
        $sheet->setCellValue('T' . $rowNumber, $fila[19]);  
        $sheet->setCellValue('U' . $rowNumber, $fila[20]);  
        $sheet->setCellValue('V' . $rowNumber, $fila[21]); 
        $sheet->setCellValue('W' . $rowNumber, $fila[22]); 
        $sheet->setCellValue('X' . $rowNumber, $fila[23]);
        $sheet->setCellValue('Y' . $rowNumber, $fila[24]);  
        $sheet->setCellValue('Z' . $rowNumber, $fila[25]); 
        $sheet->setCellValue('AA' . $rowNumber, $fila[26]); 
        $sheet->setCellValue('AB' . $rowNumber, $fila[27]); 
        $sheet->setCellValue('AC' . $rowNumber, $fila[28]);  
        $sheet->setCellValue('AD' . $rowNumber, $fila[29]);  
        $sheet->setCellValue('AE' . $rowNumber, $fila[30]);  
        $sheet->setCellValue('AF' . $rowNumber, $fila[31]);  
        $sheet->setCellValue('AG' . $rowNumber, $fila[32]);  
        $sheet->setCellValue('AH' . $rowNumber, $fila[33]); 
        $sheet->setCellValue('AI' . $rowNumber, $fila[34]);   
        $rowNumber++;
    }
} else {
    // Si no hay datos, mostrar mensaje en la hoja
    $sheet->setCellValue('A2', 'No se encontraron datos en la tabla empleado.');
}

mysqli_close($conexion);
// Limpiar el buffer
ob_end_clean();  
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="archivo.xlsx"');
header('Cache-Control: max-age=0');
// Crear el escritor y enviar el archivo al navegador
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;  
?>
