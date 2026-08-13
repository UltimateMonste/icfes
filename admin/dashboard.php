<?php

require_once __DIR__ . "/../includes/seguridad.php";

exigirAdmin();

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Administración | ICFES Platform</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">

    <div class="container">

        <span class="navbar-brand">
            ICFES Platform - Administración
        </span>

        <a
            href="../cerrar_sesion.php"
            class="btn btn-light btn-sm"
        >
            Cerrar sesión
        </a>

    </div>

</nav>


<div class="container py-4">

    <div class="card shadow-sm">

        <div class="card-body">

            <h2>
                Panel de administración
            </h2>

            <p class="text-muted">
                Bienvenido,
                <?php
                echo htmlspecialchars($_SESSION["nombres"]);
                ?>.
            </p>

            <hr>

            <p>
                Desde este panel podremos administrar:
            </p>

            <ul>

                <li>Estudiantes</li>
                <li>Materias</li>
                <li>Temas</li>
                <li>Recursos</li>
                <li>Evaluaciones</li>
                <li>Preguntas</li>
                <li>Insignias</li>
                <li>Avatares</li>
                <li>Sugerencias</li>

            </ul>

        </div>

    </div>

</div>

</body>

</html>