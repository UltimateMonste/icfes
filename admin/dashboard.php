<?php

require_once __DIR__ . "/../includes/seguridad.php";

exigirAdmin();


// =====================================================
// ESTADÍSTICAS DEL SISTEMA
// =====================================================

$total_estudiantes = 0;
$total_materias = 0;
$total_temas = 0;
$total_recursos = 0;
$total_evaluaciones = 0;
$total_sugerencias = 0;


try {

    // Total de estudiantes
    $consulta = $conexion->query(
        "SELECT COUNT(*) 
         FROM usuarios 
         WHERE id_rol = 2"
    );

    $total_estudiantes = (int) $consulta->fetchColumn();


    // Total de materias
    $consulta = $conexion->query(
        "SELECT COUNT(*) 
         FROM materias"
    );

    $total_materias = (int) $consulta->fetchColumn();


    // Total de temas
    $consulta = $conexion->query(
        "SELECT COUNT(*) 
         FROM temas"
    );

    $total_temas = (int) $consulta->fetchColumn();


    // Total de recursos
    $consulta = $conexion->query(
        "SELECT COUNT(*) 
         FROM recursos"
    );

    $total_recursos = (int) $consulta->fetchColumn();


    // Total de evaluaciones
    $consulta = $conexion->query(
        "SELECT COUNT(*) 
         FROM evaluaciones"
    );

    $total_evaluaciones = (int) $consulta->fetchColumn();


    // Total de sugerencias
    $consulta = $conexion->query(
        "SELECT COUNT(*) 
         FROM sugerencias"
    );

    $total_sugerencias = (int) $consulta->fetchColumn();


} catch (PDOException $e) {

    $error_estadisticas = true;
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
        Administración | ICFES Platform
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

</head>


<body class="bg-light">


<!-- =====================================================
     BARRA DE NAVEGACIÓN
====================================================== -->

<nav class="navbar navbar-dark bg-dark">

    <div class="container-fluid">

        <span class="navbar-brand mb-0 h1">

            <i class="bi bi-mortarboard-fill"></i>

            ICFES Platform

        </span>


        <div class="d-flex align-items-center">

            <span class="text-white me-3">

                <i class="bi bi-person-circle"></i>

                <?php

                echo htmlspecialchars(
                    $_SESSION["nombres"] . " " .
                    $_SESSION["apellidos"]
                );

                ?>

            </span>


            <a
                href="../cerrar_sesion.php"
                class="btn btn-outline-light btn-sm"
            >

                <i class="bi bi-box-arrow-right"></i>

                Cerrar sesión

            </a>

        </div>

    </div>

</nav>



<!-- =====================================================
     CONTENIDO PRINCIPAL
====================================================== -->

<div class="container-fluid py-4">


    <!-- TÍTULO -->

    <div class="mb-4">

        <h2>

            <i class="bi bi-speedometer2"></i>

            Panel de administración

        </h2>

        <p class="text-muted mb-0">

            Administración general de la plataforma.

        </p>

    </div>



    <?php if (isset($error_estadisticas)): ?>

        <div class="alert alert-danger">

            <i class="bi bi-exclamation-triangle"></i>

            No fue posible cargar las estadísticas
            del sistema.

        </div>

    <?php endif; ?>



    <!-- =================================================
         TARJETAS DE ESTADÍSTICAS
    ================================================== -->

    <div class="row g-4 mb-4">


        <!-- ESTUDIANTES -->

        <div class="col-12 col-sm-6 col-xl-3">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <p class="text-muted mb-1">
                                Estudiantes
                            </p>

                            <h2 class="mb-0">

                                <?php
                                echo $total_estudiantes;
                                ?>

                            </h2>

                        </div>

                        <div class="fs-1 text-primary">

                            <i class="bi bi-people-fill"></i>

                        </div>

                    </div>

                </div>

                <div class="card-footer bg-white border-0">

                    <a
                        href="estudiantes/index.php"
                        class="text-decoration-none"
                    >
                        Gestionar estudiantes →
                    </a>

                </div>

            </div>

        </div>



        <!-- MATERIAS -->

        <div class="col-12 col-sm-6 col-xl-3">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <p class="text-muted mb-1">
                                Materias
                            </p>

                            <h2 class="mb-0">

                                <?php
                                echo $total_materias;
                                ?>

                            </h2>

                        </div>

                        <div class="fs-1 text-success">

                            <i class="bi bi-book-fill"></i>

                        </div>

                    </div>

                </div>

                <div class="card-footer bg-white border-0">

                    <span class="text-muted">
                        Próximamente
                    </span>

                </div>

            </div>

        </div>



        <!-- TEMAS -->

        <div class="col-12 col-sm-6 col-xl-3">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <p class="text-muted mb-1">
                                Temas
                            </p>

                            <h2 class="mb-0">

                                <?php
                                echo $total_temas;
                                ?>

                            </h2>

                        </div>

                        <div class="fs-1 text-warning">

                            <i class="bi bi-journal-text"></i>

                        </div>

                    </div>

                </div>

                <div class="card-footer bg-white border-0">

                    <span class="text-muted">
                        Próximamente
                    </span>

                </div>

            </div>

        </div>



        <!-- RECURSOS -->

        <div class="col-12 col-sm-6 col-xl-3">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <p class="text-muted mb-1">
                                Recursos
                            </p>

                            <h2 class="mb-0">

                                <?php
                                echo $total_recursos;
                                ?>

                            </h2>

                        </div>

                        <div class="fs-1 text-info">

                            <i class="bi bi-collection-play-fill"></i>

                        </div>

                    </div>

                </div>

                <div class="card-footer bg-white border-0">

                    <span class="text-muted">
                        Próximamente
                    </span>

                </div>

            </div>

        </div>

    </div>



    <!-- =================================================
         SEGUNDA FILA
    ================================================== -->

    <div class="row g-4 mb-4">


        <!-- EVALUACIONES -->

        <div class="col-12 col-md-6">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div class="fs-1 text-danger me-3">

                            <i class="bi bi-clipboard-check-fill"></i>

                        </div>

                        <div>

                            <h5 class="mb-1">
                                Evaluaciones
                            </h5>

                            <p class="mb-0 text-muted">

                                <?php
                                echo $total_evaluaciones;
                                ?>

                                evaluaciones registradas.

                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        <!-- SUGERENCIAS -->

        <div class="col-12 col-md-6">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div class="fs-1 text-secondary me-3">

                            <i class="bi bi-chat-left-text-fill"></i>

                        </div>

                        <div>

                            <h5 class="mb-1">
                                Sugerencias
                            </h5>

                            <p class="mb-0 text-muted">

                                <?php
                                echo $total_sugerencias;
                                ?>

                                sugerencias recibidas.

                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



    <!-- =================================================
         ACCESOS ADMINISTRATIVOS
    ================================================== -->

    <div class="card shadow-sm">

        <div class="card-header">

            <h5 class="mb-0">

                <i class="bi bi-gear-fill"></i>

                Administración

            </h5>

        </div>


        <div class="card-body">

            <div class="row g-3">


                <!-- ESTUDIANTES -->

                <div class="col-12 col-sm-6 col-lg-4">

                    <a
                        href="estudiantes/index.php"
                        class="btn btn-outline-primary w-100 p-3"
                    >

                        <i class="bi bi-people-fill fs-4"></i>

                        <br>

                        Gestionar estudiantes

                    </a>

                </div>


                <!-- MATERIAS -->

                <div class="col-12 col-sm-6 col-lg-4">

                    <button
                        type="button"
                        class="btn btn-outline-success w-100 p-3"
                        disabled
                    >

                        <i class="bi bi-book-fill fs-4"></i>

                        <br>

                        Gestionar materias

                    </button>

                </div>


                <!-- TEMAS -->

                <div class="col-12 col-sm-6 col-lg-4">

                    <button
                        type="button"
                        class="btn btn-outline-warning w-100 p-3"
                        disabled
                    >

                        <i class="bi bi-journal-text fs-4"></i>

                        <br>

                        Gestionar temas

                    </button>

                </div>


                <!-- RECURSOS -->

                <div class="col-12 col-sm-6 col-lg-4">

                    <button
                        type="button"
                        class="btn btn-outline-info w-100 p-3"
                        disabled
                    >

                        <i class="bi bi-collection-play fs-4"></i>

                        <br>

                        Gestionar recursos

                    </button>

                </div>


                <!-- EVALUACIONES -->

                <div class="col-12 col-sm-6 col-lg-4">

                    <button
                        type="button"
                        class="btn btn-outline-danger w-100 p-3"
                        disabled
                    >

                        <i class="bi bi-clipboard-check fs-4"></i>

                        <br>

                        Gestionar evaluaciones

                    </button>

                </div>


                <!-- SUGERENCIAS -->

                <div class="col-12 col-sm-6 col-lg-4">

                    <button
                        type="button"
                        class="btn btn-outline-secondary w-100 p-3"
                        disabled
                    >

                        <i class="bi bi-chat-left-text fs-4"></i>

                        <br>

                        Ver sugerencias

                    </button>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>