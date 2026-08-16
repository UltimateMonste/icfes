<?php

require_once __DIR__ . "/../../includes/seguridad.php";

exigirAdmin();


/*
|--------------------------------------------------------------------------
| Cargar PhpSpreadsheet
|--------------------------------------------------------------------------
*/

require_once __DIR__ . "/../../vendor/autoload.php";


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;


/*
|--------------------------------------------------------------------------
| Crear Excel
|--------------------------------------------------------------------------
*/

$spreadsheet = new Spreadsheet();

$hoja = $spreadsheet->getActiveSheet();

$hoja->setTitle("Estudiantes");


/*
|--------------------------------------------------------------------------
| Encabezados
|--------------------------------------------------------------------------
*/

$encabezados = [
    "nombres",
    "apellidos",
    "numero_documento",
    "correo",
    "grado",
    "grupo"
];


foreach ($encabezados as $columna => $nombre) {

    $letra =
        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
            $columna + 1
        );

    $hoja->setCellValue(
        $letra . "1",
        $nombre
    );

}


/*
|--------------------------------------------------------------------------
| Ejemplo
|--------------------------------------------------------------------------
*/

$hoja->setCellValue("A2", "Juan");
$hoja->setCellValue("B2", "Pérez");
$hoja->setCellValue("C2", "123456789");
$hoja->setCellValue("D2", "juan@correo.com");
$hoja->setCellValue("E2", "11");
$hoja->setCellValue("F2", "11-01");


/*
|--------------------------------------------------------------------------
| Hoja de instrucciones
|--------------------------------------------------------------------------
*/

$instrucciones =
    $spreadsheet->createSheet();

$instrucciones->setTitle("Instrucciones");


$instrucciones->setCellValue(
    "A1",
    "PLANTILLA DE IMPORTACIÓN DE ESTUDIANTES"
);

$instrucciones->setCellValue(
    "A3",
    "Columna"
);

$instrucciones->setCellValue(
    "B3",
    "Descripción"
);


$datos = [

    [
        "nombres",
        "Nombres completos del estudiante."
    ],

    [
        "apellidos",
        "Apellidos completos del estudiante."
    ],

    [
        "numero_documento",
        "Número de documento. Se utilizará como contraseña inicial."
    ],

    [
        "correo",
        "Correo electrónico del estudiante. Puede dejarse vacío."
    ],

    [
        "grado",
        "Debe ser 9, 10 u 11."
    ],

    [
        "grupo",
        "Grupo existente en la tabla cursos. Ejemplo: 11-01."
    ]

];


$fila = 4;


foreach ($datos as $dato) {

    $instrucciones->setCellValue(
        "A" . $fila,
        $dato[0]
    );

    $instrucciones->setCellValue(
        "B" . $fila,
        $dato[1]
    );

    $fila++;

}


$instrucciones->setCellValue(
    "A12",
    "IMPORTANTE"
);

$instrucciones->setCellValue(
    "A13",
    "No elimines ni cambies los nombres de las columnas."
);

$instrucciones->setCellValue(
    "A14",
    "Cada estudiante debe ocupar una fila."
);

$instrucciones->setCellValue(
    "A15",
    "El número de documento debe ser único."
);

$instrucciones->setCellValue(
    "A16",
    "El grado debe corresponder con el grupo."
);


/*
|--------------------------------------------------------------------------
| Estilos
|--------------------------------------------------------------------------
*/

foreach ([$hoja, $instrucciones] as $pagina) {

    foreach (
        $pagina->getColumnIterator()
        as $column
    ) {

        $dimension =
            $pagina->getColumnDimension(
                $column->getColumnIndex()
            );

        $dimension->setAutoSize(true);

    }

}


$hoja->freezePane("A2");

$instrucciones->freezePane("A4");


/*
|--------------------------------------------------------------------------
| Descargar
|--------------------------------------------------------------------------
*/

$nombreArchivo =
    "plantilla_estudiantes.xlsx";


header(
    "Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
);

header(
    "Content-Disposition: attachment; filename=\"" .
    $nombreArchivo .
    "\""
);

header(
    "Cache-Control: max-age=0"
);


$writer =
    new Xlsx($spreadsheet);

$writer->save("php://output");

exit;