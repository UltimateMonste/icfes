<?php

require_once __DIR__ . "/../includes/seguridad.php";

exigirEstudiante();

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard | ICFES Platform</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">

    <div class="container">

        <span class="navbar-brand">
            ICFES Platform
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

    <div class="row">

        <div class="col-12">

            <div class="card shadow-sm mb-4">

                <div class="card-body">

                    <h2>
                        ¡Hola,
                        <?php
                        echo htmlspecialchars($_SESSION["nombres"]);
                        ?>!
                    </h2>

                    <p class="text-muted">
                        Bienvenido a tu plataforma de preparación para Saber 11°.
                    </p>

                </div>

            </div>

        </div>

    </div>


    <div class="row g-4">

        <div class="col-md-4">

            <div class="card text-center shadow-sm">

                <div class="card-body">

                    <h5>⭐ Puntos</h5>

                    <h2>
                        <?php
                        echo (int)$_SESSION["puntos"];
                        ?>
                    </h2>

                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card text-center shadow-sm">

                <div class="card-body">

                    <h5>🏆 Nivel</h5>

                    <h2>
                        <?php
                        echo (int)$_SESSION["nivel"];
                        ?>
                    </h2>

                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card text-center shadow-sm">

                <div class="card-body">

                    <h5>📚 Grado</h5>

                    <h2>
                        <?php
                        echo htmlspecialchars($_SESSION["grado"]);
                        ?>
                    </h2>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>