<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/funciones.php";


/*
|--------------------------------------------------------------------------
| Exigir inicio de sesión
|--------------------------------------------------------------------------
*/

function exigirLogin()
{
    if (!isset($_SESSION['id_usuario'])) {
        redireccionar("../login.php");
    }
}


/*
|--------------------------------------------------------------------------
| Obtener estado actual del usuario
|--------------------------------------------------------------------------
*/

function obtenerEstadoUsuario()
{
    global $conexion;

    if (!isset($_SESSION['id_usuario'])) {
        return false;
    }

    try {

        $sql = "SELECT
                    id_usuario,
                    id_rol,
                    estado,
                    primer_ingreso
                FROM usuarios
                WHERE id_usuario = :id_usuario
                LIMIT 1";

        $consulta = $conexion->prepare($sql);

        $consulta->execute([
            ":id_usuario" => $_SESSION['id_usuario']
        ]);

        return $consulta->fetch();

    } catch (PDOException $e) {

        return false;
    }
}


/*
|--------------------------------------------------------------------------
| Exigir administrador
|--------------------------------------------------------------------------
*/

function exigirAdmin()
{
    exigirLogin();

    $usuario = obtenerEstadoUsuario();

    /*
    | Usuario inexistente
    */

    if (!$usuario) {

        session_unset();
        session_destroy();

        header("Location: ../login.php?error=" . urlencode("La sesión no es válida."));
        exit;
    }


    /*
    | Cuenta inactiva
    */

    if ($usuario['estado'] !== 'Activo') {

        session_unset();
        session_destroy();

        header("Location: ../login.php?error=" . urlencode("Su cuenta se encuentra inactiva."));
        exit;
    }


    /*
    | Verificar rol
    */

    if ((int)$usuario['id_rol'] !== 1) {

        http_response_code(403);
        die("Acceso no autorizado.");
    }
}


/*
|--------------------------------------------------------------------------
| Exigir estudiante
|--------------------------------------------------------------------------
*/

function exigirEstudiante()
{
    exigirLogin();

    $usuario = obtenerEstadoUsuario();

    /*
    | Usuario inexistente
    */

    if (!$usuario) {

        session_unset();
        session_destroy();

        header("Location: ../login.php?error=" . urlencode("La sesión no es válida."));
        exit;
    }


    /*
    | Cuenta inactiva
    */

    if ($usuario['estado'] !== 'Activo') {

        session_unset();
        session_destroy();

        header("Location: ../login.php?error=" . urlencode("Su cuenta se encuentra inactiva."));
        exit;
    }


    /*
    | Verificar rol estudiante
    */

    if ((int)$usuario['id_rol'] !== 2) {

        http_response_code(403);
        die("Acceso no autorizado.");
    }


    /*
    | Verificar primer ingreso directamente
    | desde la base de datos
    */

    if ((int)$usuario['primer_ingreso'] === 1) {

        header("Location: ../cambiar_password.php");
        exit;
    }


    /*
    | Actualizar el valor de sesión
    */

    $_SESSION['primer_ingreso'] = $usuario['primer_ingreso'];
}