<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/funciones.php";

function exigirLogin()
{
    if (!isset($_SESSION['id_usuario'])) {
        redireccionar("../login.php");
    }
}

function exigirAdmin()
{
    exigirLogin();

    if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) {
        http_response_code(403);
        die("Acceso no autorizado.");
    }
}

function exigirEstudiante()
{
    exigirLogin();

    if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 2) {
        http_response_code(403);
        die("Acceso no autorizado.");
    }
}