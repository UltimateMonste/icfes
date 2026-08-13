<?php

require_once __DIR__ . "/../../includes/seguridad.php";

exigirAdmin();


/*
|--------------------------------------------------------------------------
| Obtener estudiantes
|--------------------------------------------------------------------------
*/

$estudiantes = [];

try {

    $sql = "SELECT
                u.id_usuario,
                u.nombres,
                u.apellidos,
                u.numero_documento,
                u.correo,
                u.grado,
                u.puntos,
                u.nivel,
                u.primer_ingreso,
                u.estado
            FROM usuarios u
            WHERE u.id_rol = 2
            ORDER BY u.apellidos ASC, u.nombres ASC";

    $consulta = $conexion->query($sql);

    $estudiantes = $consulta->fetchAll();

} catch (PDOException $e) {

    $error = true;
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


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

</head>


<body class="bg-light">


<!-- NAVBAR -->

<nav class="navbar navbar-dark bg-dark">

    <div class="container-fluid">

        <a
            href="../dashboard.php"
            class="navbar-brand"
        >

            <i class="bi bi-mortarboard-fill"></i>

            ICFES Platform

        </a>


        <a
            href="../../cerrar_sesion.php"
            class="btn btn-outline-light btn-sm"
        >

            <i class="bi bi-box-arrow-right"></i>

            Cerrar sesión

        </a>

    </div>

</nav>


<div class="container-fluid py-4">


    <!-- ENCABEZADO -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2>

                <i class="bi bi-people-fill"></i>

                Estudiantes

            </h2>

            <p class="text-muted mb-0">

                Administración de estudiantes registrados.

            </p>

        </div>


        <div>

            <button
                class="btn btn-primary"
                disabled
            >

                <i class="bi bi-person-plus-fill"></i>

                Nuevo estudiante

            </button>

        </div>

    </div>


    <?php if (isset($error)): ?>

        <div class="alert alert-danger">

            No fue posible cargar los estudiantes.

        </div>

    <?php endif; ?>


    <!-- TABLA -->

    <div class="card shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-dark">

                        <tr>

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

                            <th>
                                Acciones
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if (count($estudiantes) > 0): ?>

                        <?php foreach ($estudiantes as $estudiante): ?>

                            <tr>

                                <td>

                                    <?php
                                    echo (int)$estudiante["id_usuario"];
                                    ?>

                                </td>


                                <td>

                                    <strong>

                                        <?php
                                        echo htmlspecialchars(
                                            $estudiante["nombres"] .
                                            " " .
                                            $estudiante["apellidos"]
                                        );
                                        ?>

                                    </strong>

                                </td>


                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $estudiante["numero_documento"]
                                    );
                                    ?>

                                </td>


                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $estudiante["correo"] ?? ""
                                    );
                                    ?>

                                </td>


                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $estudiante["grado"] ?? ""
                                    );
                                    ?>

                                </td>


                                <td>

                                    <span class="badge bg-warning text-dark">

                                        <i class="bi bi-star-fill"></i>

                                        <?php
                                        echo (int)$estudiante["puntos"];
                                        ?>

                                    </span>

                                </td>


                                <td>

                                    <span class="badge bg-primary">

                                        Nivel
                                        <?php
                                        echo (int)$estudiante["nivel"];
                                        ?>

                                    </span>

                                </td>


                                <td>

                                    <?php if ($estudiante["estado"] === "Activo"): ?>

                                        <span class="badge bg-success">
                                            Activo
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-danger">
                                            Inactivo
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <?php if ((int)$estudiante["primer_ingreso"] === 1): ?>

                                        <span class="badge bg-warning text-dark">
                                            Pendiente
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-success">
                                            Completado
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

    <a
        href="restablecer_password.php?id=<?= (int)$estudiante["id_usuario"] ?>"
        class="btn btn-sm btn-outline-warning"
        title="Restablecer contraseña"
    >

        <i class="bi bi-key-fill"></i>

        Restablecer

    </a>

</td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="10"
                                class="text-center text-muted py-4"
                            >

                                <i class="bi bi-info-circle"></i>

                                No hay estudiantes registrados.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>