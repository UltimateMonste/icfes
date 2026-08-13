<?php
session_start();

if (isset($_SESSION['id_usuario'])) {

    if ($_SESSION['id_rol'] == 1) {
        header("Location: admin/dashboard.php");
        exit;
    }

    header("Location: estudiante/dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar sesión | ICFES Platform</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">

        <div class="col-12 col-sm-10 col-md-6 col-lg-4">

            <div class="card shadow">

                <div class="card-body p-4">

                    <h2 class="text-center mb-2">
                        ICFES Platform
                    </h2>

                    <p class="text-center text-muted mb-4">
                        Plataforma de preparación para Saber 11°
                    </p>

                    <?php if (isset($_GET['error'])): ?>

                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($_GET['error']); ?>
                        </div>

                    <?php endif; ?>

                    <form action="autenticar.php" method="POST">

                        <div class="mb-3">

                            <label for="documento" class="form-label">
                                Número de documento
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="documento"
                                name="documento"
                                required
                                autocomplete="username"
                            >

                        </div>

                        <div class="mb-3">

                            <label for="password" class="form-label">
                                Contraseña
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                required
                                autocomplete="current-password"
                            >

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Iniciar sesión
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>
</div>

</body>
</html>