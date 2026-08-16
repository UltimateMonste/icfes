<?php

require_once __DIR__ . "/../../includes/seguridad.php";

exigirAdmin();


/*
|--------------------------------------------------------------------------
| VARIABLES
|--------------------------------------------------------------------------
*/

$errores = [];
$mensajes = [];

$gradoSeleccionado = trim($_GET["grado"] ?? "");
$cursoSeleccionado = (int)($_GET["curso"] ?? 0);


/*
|--------------------------------------------------------------------------
| VALIDAR GRADO
|--------------------------------------------------------------------------
*/

$gradosPermitidos = ["9", "10", "11"];

if (
    $gradoSeleccionado !== "" &&
    !in_array($gradoSeleccionado, $gradosPermitidos, true)
) {

    $gradoSeleccionado = "";

}


/*
|--------------------------------------------------------------------------
| PROCESAR ACCIONES INDIVIDUALES
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $accionIndividual = trim($_POST["accion_individual"] ?? "");

    $idIndividual = filter_var(
        $_POST["id_usuario"] ?? null,
        FILTER_VALIDATE_INT
    );


    if (
        $accionIndividual !== "" &&
        $idIndividual
    ) {

        if (
            !in_array(
                $accionIndividual,
                ["activar", "inactivar"],
                true
            )
        ) {

            $errores[] =
                "La acción individual no es válida.";

        } else {

            try {

                $nuevoEstado =
                    $accionIndividual === "activar"
                    ? "Activo"
                    : "Inactivo";


                $sqlIndividual = "
                    UPDATE usuarios
                    SET estado = ?
                    WHERE id_usuario = ?
                    AND id_rol = 2
                ";


                $stmtIndividual =
                    $conexion->prepare(
                        $sqlIndividual
                    );


                $stmtIndividual->execute([
                    $nuevoEstado,
                    $idIndividual
                ]);


                if ($stmtIndividual->rowCount() > 0) {

                    if ($nuevoEstado === "Activo") {

                        $mensajes[] =
                            "Estudiante activado correctamente.";

                    } else {

                        $mensajes[] =
                            "Estudiante inactivado correctamente.";

                    }

                } else {

                    $errores[] =
                        "No fue posible actualizar el estado del estudiante.";

                }

            } catch (PDOException $e) {

                $errores[] =
                    "No fue posible actualizar el estado del estudiante.";

            }

        }

    }

}


/*
|--------------------------------------------------------------------------
| OBTENER CURSOS
|--------------------------------------------------------------------------
|
| IMPORTANTE:
| Siempre obtenemos los cursos activos, pero JavaScript
| será quien controle cuáles se muestran según el grado.
|
*/

$cursos = [];

try {

    $sqlCursos = "
        SELECT
            id_curso,
            grado,
            grupo
        FROM cursos
        WHERE estado = 'Activo'
        AND grado IN ('9', '10', '11')
        ORDER BY
            grado ASC,
            grupo ASC
    ";


    $stmtCursos =
        $conexion->query(
            $sqlCursos
        );


    $cursos =
        $stmtCursos->fetchAll(
            PDO::FETCH_ASSOC
        );

} catch (PDOException $e) {

    $errores[] =
        "No fue posible cargar los cursos.";

}


/*
|--------------------------------------------------------------------------
| VALIDAR CURSO SELECCIONADO
|--------------------------------------------------------------------------
|
| El curso solamente puede pertenecer al grado seleccionado.
|
*/

if (
    $cursoSeleccionado > 0 &&
    $gradoSeleccionado !== ""
) {

    $cursoValido = false;

    foreach ($cursos as $curso) {

        if (
            (int)$curso["id_curso"] === $cursoSeleccionado &&
            (string)$curso["grado"] === $gradoSeleccionado
        ) {

            $cursoValido = true;

            break;

        }

    }


    if (!$cursoValido) {

        $cursoSeleccionado = 0;

    }

} elseif ($gradoSeleccionado === "") {

    $cursoSeleccionado = 0;

}


/*
|--------------------------------------------------------------------------
| PROCESAR ACCIONES MASIVAS
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $accion =
        trim($_POST["accion"] ?? "");


    $estudiantesSeleccionados =
        $_POST["estudiantes"] ?? [];


    if (!is_array($estudiantesSeleccionados)) {

        $estudiantesSeleccionados = [];

    }


    $ids = [];


    foreach (
        $estudiantesSeleccionados as $id
    ) {

        if (
            filter_var(
                $id,
                FILTER_VALIDATE_INT
            )
        ) {

            $ids[] = (int)$id;

        }

    }


    $ids =
        array_values(
            array_unique($ids)
        );


    $accionesPermitidas = [
        "activar",
        "inactivar",
        "eliminar"
    ];


    if (
        !in_array(
            $accion,
            $accionesPermitidas,
            true
        )
    ) {

        $accion = "";

    }


    if (
        !empty($accion) &&
        empty($ids)
    ) {

        $errores[] =
            "Debes seleccionar al menos un estudiante.";

    }


    if (
        !empty($accion) &&
        !empty($ids) &&
        empty($errores)
    ) {

        try {

            $conexion->beginTransaction();


            $placeholders =
                implode(
                    ",",
                    array_fill(
                        0,
                        count($ids),
                        "?"
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | ACTIVAR
            |--------------------------------------------------------------------------
            */

            if ($accion === "activar") {

                $sql = "
                    UPDATE usuarios
                    SET estado = 'Activo'
                    WHERE id_rol = 2
                    AND id_usuario IN ($placeholders)
                ";


                $stmt =
                    $conexion->prepare(
                        $sql
                    );


                $stmt->execute($ids);


                $cantidad =
                    $stmt->rowCount();


                $mensajes[] =
                    $cantidad .
                    " estudiante(s) activado(s) correctamente.";

            }


            /*
            |--------------------------------------------------------------------------
            | INACTIVAR
            |--------------------------------------------------------------------------
            */

            elseif ($accion === "inactivar") {

                $sql = "
                    UPDATE usuarios
                    SET estado = 'Inactivo'
                    WHERE id_rol = 2
                    AND id_usuario IN ($placeholders)
                ";


                $stmt =
                    $conexion->prepare(
                        $sql
                    );


                $stmt->execute($ids);


                $cantidad =
                    $stmt->rowCount();


                $mensajes[] =
                    $cantidad .
                    " estudiante(s) inactivado(s) correctamente.";

            }


            /*
            |--------------------------------------------------------------------------
            | ELIMINAR
            |--------------------------------------------------------------------------
            */

            elseif ($accion === "eliminar") {

                $sqlVerificar = "
                    SELECT id_usuario
                    FROM usuarios
                    WHERE id_rol = 2
                    AND id_usuario IN ($placeholders)
                ";


                $stmtVerificar =
                    $conexion->prepare(
                        $sqlVerificar
                    );


                $stmtVerificar->execute($ids);


                $idsValidos =
                    $stmtVerificar->fetchAll(
                        PDO::FETCH_COLUMN
                    );


                if (empty($idsValidos)) {

                    throw new Exception(
                        "No se encontraron estudiantes válidos para eliminar."
                    );

                }


                $placeholdersEliminar =
                    implode(
                        ",",
                        array_fill(
                            0,
                            count($idsValidos),
                            "?"
                        )
                    );


                $sqlEliminar = "
                    DELETE FROM usuarios
                    WHERE id_rol = 2
                    AND id_usuario IN ($placeholdersEliminar)
                ";


                $stmtEliminar =
                    $conexion->prepare(
                        $sqlEliminar
                    );


                $stmtEliminar->execute(
                    $idsValidos
                );


                $cantidad =
                    $stmtEliminar->rowCount();


                $mensajes[] =
                    $cantidad .
                    " estudiante(s) eliminado(s) correctamente.";

            }


            $conexion->commit();


        } catch (Exception $e) {

            if (
                $conexion->inTransaction()
            ) {

                $conexion->rollBack();

            }


            $errores[] =
                "No fue posible completar la operación. " .
                $e->getMessage();

        }

    }

}


/*
|--------------------------------------------------------------------------
| OBTENER ESTUDIANTES
|--------------------------------------------------------------------------
*/

$estudiantes = [];

try {

    $sql = "
        SELECT
            u.id_usuario,
            u.nombres,
            u.apellidos,
            u.numero_documento,
            u.correo,
            u.grado,
            u.id_curso,
            c.grupo,
            u.puntos,
            u.nivel,
            u.primer_ingreso,
            u.estado

        FROM usuarios u

        LEFT JOIN cursos c
            ON u.id_curso = c.id_curso

        WHERE u.id_rol = 2
    ";


    $parametros = [];


    /*
    |--------------------------------------------------------------------------
    | FILTRO GRADO
    |--------------------------------------------------------------------------
    */

    if ($gradoSeleccionado !== "") {

        $sql .= "
            AND u.grado = ?
        ";


        $parametros[] =
            $gradoSeleccionado;

    }


    /*
    |--------------------------------------------------------------------------
    | FILTRO CURSO
    |--------------------------------------------------------------------------
    */

    if ($cursoSeleccionado > 0) {

        $sql .= "
            AND u.id_curso = ?
        ";


        $parametros[] =
            $cursoSeleccionado;

    }


    $sql .= "
        ORDER BY
            u.grado ASC,
            c.grupo ASC,
            u.apellidos ASC,
            u.nombres ASC
    ";


    $stmt =
        $conexion->prepare(
            $sql
        );


    $stmt->execute(
        $parametros
    );


    $estudiantes =
        $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );


} catch (PDOException $e) {

    $errores[] =
        "No fue posible cargar los estudiantes.";

}


/*
|--------------------------------------------------------------------------
| ESTADÍSTICAS
|--------------------------------------------------------------------------
*/

$totalEstudiantes =
    count($estudiantes);

$estudiantesActivos = 0;

$estudiantesInactivos = 0;

$estudiantesPendientes = 0;


foreach (
    $estudiantes as $estudiante
) {

    if (
        $estudiante["estado"] === "Activo"
    ) {

        $estudiantesActivos++;

    } else {

        $estudiantesInactivos++;

    }


    if (
        (int)$estudiante["primer_ingreso"] === 1
    ) {

        $estudiantesPendientes++;

    }

}

?>


<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Estudiantes | ICFES Platform
    </title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <style>

        body {
            font-size: 0.92rem;
        }


        .navbar-brand {
            font-weight: 600;
        }


        .panel-principal {
            border: 0;
            box-shadow: 0 0.125rem 0.35rem rgba(0, 0, 0, 0.08);
        }


        .barra-control {
            background: #ffffff;
            border-bottom: 1px solid #dee2e6;
        }


        .tabla-estudiantes th {
            white-space: nowrap;
            font-size: 0.82rem;
        }


        .tabla-estudiantes td {
            font-size: 0.86rem;
        }


        .acciones-masivas {
            display: none;
        }


        .acciones-masivas.visible {
            display: flex;
        }


        .contador-seleccionados {
            font-size: 0.85rem;
        }


        .estadistica {
            font-size: 0.8rem;
        }


        .estadistica strong {
            font-size: 1rem;
        }


        .btn-accion {
            min-width: 38px;
        }


        /*
        |--------------------------------------------------------------------------
        | CAMPO CURSO OCULTO
        |--------------------------------------------------------------------------
        */

        #contenedorCurso {
            display: none;
        }


        #contenedorCurso.visible {
            display: block;
        }


        @media (max-width: 768px) {

            .acciones-masivas {
                width: 100%;
            }

        }

    </style>

</head>


<body class="bg-light">


<!-- =========================================================
     NAVBAR
========================================================= -->

<nav class="navbar navbar-dark bg-dark">

    <div class="container-fluid">

        <a
            href="../dashboard.php"
            class="navbar-brand"
        >

            <i class="bi bi-mortarboard-fill"></i>

            ICFES Platform

        </a>


        <div class="d-flex align-items-center gap-2">

            <span
                class="text-white d-none d-md-inline"
            >

                <i class="bi bi-shield-check"></i>

                Administrador

            </span>


            <a
                href="../../cerrar_sesion.php"
                class="btn btn-outline-light btn-sm"
            >

                <i class="bi bi-box-arrow-right"></i>

                Cerrar sesión

            </a>

        </div>

    </div>

</nav>


<!-- =========================================================
     CONTENIDO
========================================================= -->

<div class="container-fluid py-3">


    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3"
    >

        <div>

            <h3 class="mb-1">

                <i class="bi bi-people-fill"></i>

                Estudiantes

            </h3>

            <div class="text-muted">

                Administración de estudiantes registrados.

            </div>

        </div>


        <div
            class="d-flex gap-2 mt-2 mt-md-0"
        >

            <a
                href="crear.php"
                class="btn btn-primary btn-sm"
            >

                <i class="bi bi-person-plus-fill"></i>

                Nuevo estudiante

            </a>


            <a
                href="importar.php"
                class="btn btn-success btn-sm"
            >

                <i class="bi bi-file-earmark-excel-fill"></i>

                Importar Excel

            </a>

        </div>

    </div>


    <!-- =====================================================
         MENSAJES
    ====================================================== -->

    <?php foreach ($mensajes as $mensaje): ?>

        <div
            class="alert alert-success alert-dismissible fade show py-2"
        >

            <i class="bi bi-check-circle-fill"></i>

            <?= htmlspecialchars($mensaje) ?>


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    <?php endforeach; ?>


    <?php foreach ($errores as $error): ?>

        <div
            class="alert alert-danger alert-dismissible fade show py-2"
        >

            <i class="bi bi-exclamation-triangle-fill"></i>

            <?= htmlspecialchars($error) ?>


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    <?php endforeach; ?>


    <!-- =====================================================
         PANEL PRINCIPAL
    ====================================================== -->

    <div class="card panel-principal">


        <!-- =================================================
             BARRA DE FILTROS
        ================================================== -->

        <div class="card-body barra-control py-2">


            <form
                method="GET"
                class="row g-2 align-items-end"
                id="formFiltros"
            >


                <!-- =================================================
                     GRADO
                ================================================== -->

                <div class="col-sm-4 col-md-3 col-lg-2">

                    <label
                        for="filtroGrado"
                        class="form-label mb-1 small fw-semibold"
                    >

                        Grado

                    </label>


                    <select
                        name="grado"
                        id="filtroGrado"
                        class="form-select form-select-sm"
                    >

                        <option value="">

                            Selecciona un grado

                        </option>


                        <option
                            value="9"
                            <?= $gradoSeleccionado === "9"
                                ? "selected"
                                : "" ?>
                        >

                            9°

                        </option>


                        <option
                            value="10"
                            <?= $gradoSeleccionado === "10"
                                ? "selected"
                                : "" ?>
                        >

                            10°

                        </option>


                        <option
                            value="11"
                            <?= $gradoSeleccionado === "11"
                                ? "selected"
                                : "" ?>
                        >

                            11°

                        </option>

                    </select>

                </div>


                <!-- =================================================
                     CURSO / GRUPO
                ================================================== -->

                <div
                    class="col-sm-5 col-md-4 col-lg-3"
                    id="contenedorCurso"
                >

                    <label
                        for="filtroCurso"
                        class="form-label mb-1 small fw-semibold"
                    >

                        Curso / Grupo

                    </label>


                    <select
                        name="curso"
                        id="filtroCurso"
                        class="form-select form-select-sm"
                    >

                        <option
                            value="0"
                            data-grado=""
                        >

                            Todos los grupos

                        </option>


                        <?php foreach ($cursos as $curso): ?>

                            <option
                                value="<?= (int)$curso["id_curso"] ?>"
                                data-grado="<?= htmlspecialchars(
                                    (string)$curso["grado"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>"
                                <?= $cursoSeleccionado ===
                                    (int)$curso["id_curso"]
                                    ? "selected"
                                    : "" ?>
                            >

                                <?= htmlspecialchars(
                                    (string)$curso["grupo"]
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- =================================================
                     BOTONES
                ================================================== -->

                <div
                    class="col-sm-3 col-md-auto d-flex gap-1"
                >

                    <button
                        type="submit"
                        class="btn btn-primary btn-sm"
                    >

                        <i class="bi bi-funnel-fill"></i>

                        Filtrar

                    </button>


                    <a
                        href="index.php"
                        class="btn btn-outline-secondary btn-sm"
                        title="Limpiar filtros"
                    >

                        <i class="bi bi-x-lg"></i>

                    </a>

                </div>


                <!-- =================================================
                     ESTADÍSTICAS
                ================================================== -->

                <div
                    class="col-12 col-md-auto ms-md-auto"
                >

                    <div
                        class="d-flex gap-3 align-items-center justify-content-md-end mt-2 mt-md-0"
                    >

                        <div class="estadistica">

                            <span class="text-muted">
                                Total:
                            </span>

                            <strong>
                                <?= $totalEstudiantes ?>
                            </strong>

                        </div>


                        <div class="estadistica">

                            <span class="text-muted">
                                Activos:
                            </span>

                            <strong class="text-success">
                                <?= $estudiantesActivos ?>
                            </strong>

                        </div>


                        <div class="estadistica">

                            <span class="text-muted">
                                Inactivos:
                            </span>

                            <strong class="text-danger">
                                <?= $estudiantesInactivos ?>
                            </strong>

                        </div>


                        <div
                            class="estadistica d-none d-lg-block"
                        >

                            <span class="text-muted">
                                Pendientes:
                            </span>

                            <strong class="text-warning">
                                <?= $estudiantesPendientes ?>
                            </strong>

                        </div>

                    </div>

                </div>

            </form>

        </div>


        <!-- =================================================
             ACCIONES MASIVAS
        ================================================== -->

        <form
            method="POST"
            id="formAcciones"
        >


            <div
                id="barraAcciones"
                class="acciones-masivas px-3 py-2 border-bottom bg-light align-items-center justify-content-between"
            >

                <div
                    class="contador-seleccionados"
                >

                    <i class="bi bi-check2-square"></i>

                    <strong id="cantidadSeleccionados">
                        0
                    </strong>

                    estudiante(s) seleccionado(s)

                </div>


                <div class="d-flex gap-1">

                    <button
                        type="submit"
                        name="accion"
                        value="activar"
                        class="btn btn-outline-success btn-sm"
                        onclick="return confirmarAccion('activar')"
                    >

                        <i class="bi bi-person-check-fill"></i>

                        <span class="d-none d-sm-inline">
                            Activar
                        </span>

                    </button>


                    <button
                        type="submit"
                        name="accion"
                        value="inactivar"
                        class="btn btn-outline-warning btn-sm"
                        onclick="return confirmarAccion('inactivar')"
                    >

                        <i class="bi bi-person-x-fill"></i>

                        <span class="d-none d-sm-inline">
                            Inactivar
                        </span>

                    </button>


                    <button
                        type="submit"
                        name="accion"
                        value="eliminar"
                        class="btn btn-outline-danger btn-sm"
                        onclick="return confirmarAccion('eliminar')"
                    >

                        <i class="bi bi-trash-fill"></i>

                        <span class="d-none d-sm-inline">
                            Eliminar
                        </span>

                    </button>

                </div>

            </div>


            <!-- =================================================
                 TABLA
            ================================================== -->

            <?php if ($totalEstudiantes > 0): ?>

                <div class="table-responsive">

                    <table
                        class="table table-hover table-sm align-middle mb-0 tabla-estudiantes"
                    >

                        <thead class="table-dark">

                            <tr>

                                <th
                                    class="text-center"
                                    style="width: 40px;"
                                >

                                    <input
                                        type="checkbox"
                                        id="seleccionarTodos"
                                        class="form-check-input"
                                        title="Seleccionar todos"
                                    >

                                </th>


                                <th>
                                    #
                                </th>


                                <th>
                                    Estudiante
                                </th>


                                <th>
                                    Documento
                                </th>


                                <th>
                                    Correo
                                </th>


                                <th>
                                    Grado
                                </th>


                                <th>
                                    Curso
                                </th>


                                <th>
                                    Puntos
                                </th>


                                <th>
                                    Nivel
                                </th>


                                <th>
                                    Estado
                                </th>


                                <th>
                                    Primer ingreso
                                </th>


                                <th class="text-center">
                                    Acciones
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php foreach (
                            $estudiantes
                            as $estudiante
                        ): ?>

                            <tr>

                                <td class="text-center">

                                    <input
                                        type="checkbox"
                                        name="estudiantes[]"
                                        value="<?= (int)$estudiante["id_usuario"] ?>"
                                        class="form-check-input checkbox-estudiante"
                                    >

                                </td>


                                <td>

                                    <span class="text-muted">

                                        <?= (int)$estudiante["id_usuario"] ?>

                                    </span>

                                </td>


                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $estudiante["nombres"] .
                                            " " .
                                            $estudiante["apellidos"]
                                        ) ?>

                                    </strong>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $estudiante["numero_documento"]
                                    ) ?>

                                </td>


                                <td>

                                    <?php if (
                                        !empty(
                                            $estudiante["correo"]
                                        )
                                    ): ?>

                                        <?= htmlspecialchars(
                                            $estudiante["correo"]
                                        ) ?>

                                    <?php else: ?>

                                        <span class="text-muted">

                                            Sin correo

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <span
                                        class="badge bg-info text-dark"
                                    >

                                        <?= htmlspecialchars(
                                            $estudiante["grado"]
                                        ) ?>°

                                    </span>

                                </td>


                                <td>

                                    <?php if (
                                        !empty(
                                            $estudiante["grupo"]
                                        )
                                    ): ?>

                                        <span
                                            class="badge bg-secondary"
                                        >

                                            <?= htmlspecialchars(
                                                $estudiante["grupo"]
                                            ) ?>

                                        </span>

                                    <?php else: ?>

                                        <span class="text-muted">

                                            Sin curso

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <span
                                        class="badge bg-warning text-dark"
                                    >

                                        <i
                                            class="bi bi-star-fill"
                                        ></i>

                                        <?= (int)$estudiante["puntos"] ?>

                                    </span>

                                </td>


                                <td>

                                    <span
                                        class="badge bg-primary"
                                    >

                                        <?= (int)$estudiante["nivel"] ?>

                                    </span>

                                </td>


                                <td>

                                    <?php if (
                                        $estudiante["estado"]
                                        === "Activo"
                                    ): ?>

                                        <span
                                            class="badge bg-success"
                                        >

                                            <i
                                                class="bi bi-check-circle-fill"
                                            ></i>

                                            Activo

                                        </span>

                                    <?php else: ?>

                                        <span
                                            class="badge bg-danger"
                                        >

                                            <i
                                                class="bi bi-x-circle-fill"
                                            ></i>

                                            Inactivo

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <?php if (
                                        (int)$estudiante[
                                            "primer_ingreso"
                                        ] === 1
                                    ): ?>

                                        <span
                                            class="badge bg-warning text-dark"
                                            title="Debe cambiar su contraseña"
                                        >

                                            Pendiente

                                        </span>

                                    <?php else: ?>

                                        <span
                                            class="badge bg-success"
                                        >

                                            Completado

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <div
                                        class="d-flex justify-content-center gap-1"
                                    >

                                        <?php if (
                                            $estudiante["estado"] === "Activo"
                                        ): ?>

                                            <button
                                                type="submit"
                                                name="accion_individual"
                                                value="inactivar"
                                                class="btn btn-sm btn-outline-warning btn-accion"
                                                title="Inactivar estudiante"
                                                formmethod="POST"
                                                formaction="index.php"
                                                onclick="prepararAccionIndividual(this, <?= (int)$estudiante["id_usuario"] ?>)"
                                            >

                                                <i
                                                    class="bi bi-person-x-fill"
                                                ></i>

                                            </button>

                                        <?php else: ?>

                                            <button
                                                type="submit"
                                                name="accion_individual"
                                                value="activar"
                                                class="btn btn-sm btn-outline-success btn-accion"
                                                title="Activar estudiante"
                                                formmethod="POST"
                                                formaction="index.php"
                                                onclick="prepararAccionIndividual(this, <?= (int)$estudiante["id_usuario"] ?>)"
                                            >

                                                <i
                                                    class="bi bi-person-check-fill"
                                                ></i>

                                            </button>

                                        <?php endif; ?>


                                        <a
                                            href="editar.php?id=<?= (int)$estudiante["id_usuario"] ?>"
                                            class="btn btn-sm btn-outline-primary btn-accion"
                                            title="Editar estudiante"
                                        >

                                            <i
                                                class="bi bi-pencil-fill"
                                            ></i>

                                        </a>


                                        <a
                                            href="restablecer_password.php?id=<?= (int)$estudiante["id_usuario"] ?>"
                                            class="btn btn-sm btn-outline-warning btn-accion"
                                            title="Restablecer contraseña"
                                        >

                                            <i
                                                class="bi bi-key-fill"
                                            ></i>

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div
                    class="text-center py-5"
                >

                    <div
                        class="fs-1 text-muted mb-2"
                    >

                        <i class="bi bi-search"></i>

                    </div>


                    <h5>

                        No se encontraron estudiantes

                    </h5>


                    <p class="text-muted mb-3">

                        No hay estudiantes que coincidan con los filtros seleccionados.

                    </p>


                    <a
                        href="index.php"
                        class="btn btn-outline-secondary btn-sm"
                    >

                        <i class="bi bi-x-circle"></i>

                        Limpiar filtros

                    </a>

                </div>

            <?php endif; ?>


        </form>

    </div>

</div>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>


/*
|--------------------------------------------------------------------------
| PREPARAR ACCIÓN INDIVIDUAL
|--------------------------------------------------------------------------
*/

function prepararAccionIndividual(
    boton,
    idUsuario
) {

    const formulario =
        boton.closest("form");


    if (!formulario) {

        return;

    }


    const anterior =
        formulario.querySelector(
            'input[name="id_usuario"]'
        );


    if (anterior) {

        anterior.remove();

    }


    const input =
        document.createElement("input");


    input.type = "hidden";

    input.name = "id_usuario";

    input.value = idUsuario;


    formulario.appendChild(input);

}


/*
|--------------------------------------------------------------------------
| ELEMENTOS
|--------------------------------------------------------------------------
*/

const seleccionarTodos =
    document.getElementById(
        "seleccionarTodos"
    );


const checkboxes =
    document.querySelectorAll(
        ".checkbox-estudiante"
    );


const barraAcciones =
    document.getElementById(
        "barraAcciones"
    );


const cantidadSeleccionados =
    document.getElementById(
        "cantidadSeleccionados"
    );


/*
|--------------------------------------------------------------------------
| ACTUALIZAR BARRA DE ACCIONES
|--------------------------------------------------------------------------
*/

function actualizarSeleccion() {

    const seleccionados =
        document.querySelectorAll(
            ".checkbox-estudiante:checked"
        );


    const cantidad =
        seleccionados.length;


    cantidadSeleccionados.textContent =
        cantidad;


    if (cantidad > 0) {

        barraAcciones.classList.add(
            "visible"
        );

    } else {

        barraAcciones.classList.remove(
            "visible"
        );

    }


    if (
        seleccionarTodos &&
        checkboxes.length > 0 &&
        cantidad === checkboxes.length
    ) {

        seleccionarTodos.checked =
            true;

    } else if (seleccionarTodos) {

        seleccionarTodos.checked =
            false;

    }

}


/*
|--------------------------------------------------------------------------
| SELECCIONAR TODOS
|--------------------------------------------------------------------------
*/

if (seleccionarTodos) {

    seleccionarTodos.addEventListener(
        "change",
        function() {

            checkboxes.forEach(
                function(checkbox) {

                    checkbox.checked =
                        seleccionarTodos.checked;

                }
            );


            actualizarSeleccion();

        }
    );

}


/*
|--------------------------------------------------------------------------
| CHECKBOX INDIVIDUAL
|--------------------------------------------------------------------------
*/

checkboxes.forEach(
    function(checkbox) {

        checkbox.addEventListener(
            "change",
            actualizarSeleccion
        );

    }
);


/*
|--------------------------------------------------------------------------
| FILTRO GRADO / CURSO
|--------------------------------------------------------------------------
|
| COMPORTAMIENTO:
|
| 1. Sin grado:
|    - Curso / Grupo permanece completamente oculto.
|
| 2. Se selecciona 9°:
|    - Aparece Curso / Grupo.
|    - Solo aparecen grupos de 9°.
|
| 3. Se selecciona 10°:
|    - Aparece Curso / Grupo.
|    - Solo aparecen grupos de 10°.
|
| 4. Se selecciona 11°:
|    - Aparece Curso / Grupo.
|    - Solo aparecen grupos de 11°.
|
| El cambio ocurre inmediatamente, sin pulsar "Filtrar".
|
*/

const filtroGrado =
    document.getElementById(
        "filtroGrado"
    );


const contenedorCurso =
    document.getElementById(
        "contenedorCurso"
    );


const filtroCurso =
    document.getElementById(
        "filtroCurso"
    );


function actualizarCursosPorGrado() {

    const grado =
        filtroGrado.value;


    /*
    |--------------------------------------------------------------------------
    | SIN GRADO
    |--------------------------------------------------------------------------
    */

    if (grado === "") {

        contenedorCurso.classList.remove(
            "visible"
        );


        filtroCurso.value =
            "0";


        return;

    }


    /*
    |--------------------------------------------------------------------------
    | MOSTRAR CURSO
    |--------------------------------------------------------------------------
    */

    contenedorCurso.classList.add(
        "visible"
    );


    /*
    |--------------------------------------------------------------------------
    | RECORRER OPCIONES
    |--------------------------------------------------------------------------
    */

    Array.from(
        filtroCurso.options
    ).forEach(
        function(opcion) {

            /*
            | "Todos los grupos" siempre pertenece
            | al grado seleccionado.
            */

            if (
                opcion.value === "0"
            ) {

                opcion.hidden = false;

                return;

            }


            const gradoCurso =
                opcion.getAttribute(
                    "data-grado"
                );


            /*
            | SOLO mostrar el grupo cuyo grado
            | coincida exactamente.
            */

            if (
                gradoCurso === grado
            ) {

                opcion.hidden = false;

            } else {

                opcion.hidden = true;

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | VERIFICAR SELECCIÓN ACTUAL
    |--------------------------------------------------------------------------
    */

    const opcionSeleccionada =
        filtroCurso.options[
            filtroCurso.selectedIndex
        ];


    if (
        !opcionSeleccionada ||
        (
            opcionSeleccionada.value !== "0" &&
            opcionSeleccionada.getAttribute(
                "data-grado"
            ) !== grado
        )
    ) {

        filtroCurso.value =
            "0";

    }

}


/*
|--------------------------------------------------------------------------
| CAMBIO AUTOMÁTICO AL SELECCIONAR GRADO
|--------------------------------------------------------------------------
*/

filtroGrado.addEventListener(
    "change",
    function() {

        /*
        | Actualizamos inmediatamente
        | el listado de grupos.
        */

        actualizarCursosPorGrado();

    }
);


/*
|--------------------------------------------------------------------------
| INICIALIZAR FILTRO
|--------------------------------------------------------------------------
|
| Esto también funciona cuando la página se carga
| con ?grado=9, ?grado=10 o ?grado=11.
|
*/

actualizarCursosPorGrado();


/*
|--------------------------------------------------------------------------
| CONFIRMAR ACCIONES MASIVAS
|--------------------------------------------------------------------------
*/

function confirmarAccion(
    accion
) {

    const seleccionados =
        document.querySelectorAll(
            ".checkbox-estudiante:checked"
        );


    const cantidad =
        seleccionados.length;


    if (cantidad === 0) {

        alert(
            "Debes seleccionar al menos un estudiante."
        );


        return false;

    }


    if (
        accion === "activar"
    ) {

        return confirm(
            "¿Deseas activar " +
            cantidad +
            " estudiante(s)?"
        );

    }


    if (
        accion === "inactivar"
    ) {

        return confirm(
            "¿Deseas inactivar " +
            cantidad +
            " estudiante(s)?"
        );

    }


    if (
        accion === "eliminar"
    ) {

        return confirm(
            "⚠️ ADVERTENCIA\n\n" +

            "Estás a punto de ELIMINAR " +

            cantidad +

            " estudiante(s).\n\n" +

            "Esta acción puede eliminar " +
            "definitivamente sus registros " +
            "y afectar su progreso, puntos, " +
            "intentos, respuestas e insignias.\n\n" +

            "Si solamente quieres retirar " +
            "a los estudiantes de la plataforma, " +

            "es mucho más recomendable INACTIVARLOS.\n\n" +

            "¿Deseas continuar?"
        );

    }


    return false;

}


/*
|--------------------------------------------------------------------------
| INICIALIZAR SELECCIÓN
|--------------------------------------------------------------------------
*/

actualizarSeleccion();

</script>


<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>