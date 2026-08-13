<?php

session_start();

require_once __DIR__ . "/config/conexion.php";

/*
|--------------------------------------------------------------------------
| Verificar que exista una sesión
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Verificar que realmente sea primer ingreso
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["primer_ingreso"]) || (int)$_SESSION["primer_ingreso"] !== 1) {

    if ((int)$_SESSION["id_rol"] === 1) {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: estudiante/dashboard.php");
    }

    exit;
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

    <title>Cambiar contraseña | ICFES Platform</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body class="bg-light">

<div class="container">

    <div class="row justify-content-center align-items-center min-vh-100">

        <div class="col-12 col-sm-10 col-md-7 col-lg-5">

            <div class="card shadow">

                <div class="card-body p-4">

                    <h2 class="text-center mb-3">
                        Cambiar contraseña
                    </h2>

                    <p class="text-muted text-center">
                        Es tu primer ingreso a la plataforma.
                    </p>

                    <div class="alert alert-info">

                        Por seguridad, debes cambiar tu contraseña
                        antes de continuar.

                    </div>


                    <?php if (isset($_GET["error"])): ?>

                        <div class="alert alert-danger">

                            <?php
                            echo htmlspecialchars($_GET["error"]);
                            ?>

                        </div>

                    <?php endif; ?>


                    <form
                        action="guardar_password.php"
                        method="POST"
                    >

                        <div class="mb-3">

                            <label
                                for="nueva_password"
                                class="form-label"
                            >
                                Nueva contraseña
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="nueva_password"
                                name="nueva_password"
                                minlength="8"
                                required
                            >

                            <div class="form-text">

                                Debe tener mínimo 8 caracteres.

                            </div>

                        </div>


                        <div class="mb-3">

                            <label
                                for="confirmar_password"
                                class="form-label"
                            >
                                Confirmar contraseña
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="confirmar_password"
                                name="confirmar_password"
                                minlength="8"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Guardar nueva contraseña
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>