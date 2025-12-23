<?php
// admin/find_form.php - DIAGNÓSTICO DE RUTAS
session_start();
if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
    die('Acceso denegado');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Diagnóstico de Rutas</title>
    <style>
        body { font-family: monospace; padding: 20px; }
        .found { color: green; font-weight: bold; }
        .notfound { color: red; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <h2>Diagnóstico de Rutas - Sistema SAINA</h2>
    
    <?php
    $rutas = [
        'formulario1.php' => [
            '../../html/formulario1.php',
            '../html/formulario1.php',
            'html/formulario1.php',
            '../../formulario1.php',
            '../formulario1.php',
            'formulario1.php'
        ],
        'tabla_datos.php' => [
            '../../html/tabla_datos.php',
            '../html/tabla_datos.php',
            'html/tabla_datos.php'
        ],
        'excel.php' => [
            '../../php/excel.php',
            '../php/excel.php',
            'php/excel.php'
        ]
    ];
    
    foreach ($rutas as $archivo => $posibles_rutas) {
        echo "<h3>Buscando: $archivo</h3>";
        $encontrado = false;
        
        foreach ($posibles_rutas as $ruta) {
            if (file_exists($ruta)) {
                echo "<p class='found'>✓ ENCONTRADO: $ruta</p>";
                echo "<p>Tamaño: " . filesize($ruta) . " bytes</p>";
                $encontrado = true;
                break;
            } else {
                echo "<p class='notfound'>✗ No encontrado: $ruta</p>";
            }
        }
        
        if (!$encontrado) {
            echo "<p><strong>No se encontró $archivo en ninguna ruta</strong></p>";
        }
        echo "<hr>";
    }
    
    // Mostrar ruta actual
    echo "<h3>Información del servidor:</h3>";
    echo "<pre>";
    echo "Ruta actual: " . __DIR__ . "\n";
    echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
    echo "</pre>";
    ?>
    
    <script>
        // Copiar ruta correcta al portapapeles
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('found')) {
                const texto = e.target.textContent.replace('✓ ENCONTRADO: ', '');
                navigator.clipboard.writeText(texto).then(() => {
                    alert('Ruta copiada al portapapeles: ' + texto);
                });
            }
        });
    </script>
</body>
</html>