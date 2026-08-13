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

header("Location: login.php");
exit;