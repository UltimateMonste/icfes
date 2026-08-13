<?php

session_start();

require_once __DIR__ . "/config/conexion.php";


/*
|--------------------------------------------------------------------------
| Verificar sesión
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["id_usuario"])) {

    header("Location: login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Verificar primer ingreso
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION["primer_ingreso"]) ||
    (int)$_SESSION["primer_ingreso"] !== 1
) {

    header("Location: estudiante/dashboard.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Verificar método POST
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: cambiar_password.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Obtener contraseñas
|--------------------------------------------------------------------------
*/

$nueva_password = $_POST["nueva_password"] ?? "";
$confirmar_password = $_POST["confirmar_password"] ?? "";


/*
|--------------------------------------------------------------------------
| Validar campos
|--------------------------------------------------------------------------
*/

if ($nueva_password === "" || $confirmar_password === "") {

    header(
        "Location: cambiar_password.php?error=" .
        urlencode("Debe completar todos los campos.")
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Validar longitud
|--------------------------------------------------------------------------
*/

if (strlen($nueva_password) < 8) {

    header(
        "Location: cambiar_password.php?error=" .
        urlencode("La contraseña debe tener mínimo 8 caracteres.")
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Verificar coincidencia
|--------------------------------------------------------------------------
*/

if ($nueva_password !== $confirmar_password) {

    header(
        "Location: cambiar_password.php?error=" .
        urlencode("Las contraseñas no coinciden.")
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Obtener documento del usuario
|--------------------------------------------------------------------------
*/

$consulta_usuario = $conexion->prepare(
    "SELECT numero_documento
     FROM usuarios
     WHERE id_usuario = :id_usuario
     LIMIT 1"
);

$consulta_usuario->execute([
    ":id_usuario" => $_SESSION["id_usuario"]
]);

$usuario = $consulta_usuario->fetch();


if (!$usuario) {

    session_unset();
    session_destroy();

    header(
        "Location: login.php?error=" .
        urlencode("No fue posible encontrar el usuario.")
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| No permitir utilizar nuevamente el documento
|--------------------------------------------------------------------------
*/

if ($nueva_password === $usuario["numero_documento"]) {

    header(
        "Location: cambiar_password.php?error=" .
        urlencode("La nueva contraseña no puede ser igual al número de documento.")
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Generar hash seguro
|--------------------------------------------------------------------------
*/

$password_hash = password_hash(
    $nueva_password,
    PASSWORD_DEFAULT
);


/*
|--------------------------------------------------------------------------
| Actualizar contraseña
|--------------------------------------------------------------------------
*/

try {

    $sql = "UPDATE usuarios
            SET
                password = :password,
                primer_ingreso = 0,
                fecha_cambio_password = NOW()
            WHERE id_usuario = :id_usuario";

    $actualizar = $conexion->prepare($sql);

    $actualizar->execute([
        ":password" => $password_hash,
        ":id_usuario" => $_SESSION["id_usuario"]
    ]);


    /*
    |--------------------------------------------------------------------------
    | Actualizar sesión
    |--------------------------------------------------------------------------
    */

    $_SESSION["primer_ingreso"] = 0;


    /*
    |--------------------------------------------------------------------------
    | Redireccionar al dashboard
    |--------------------------------------------------------------------------
    */

    if ((int)$_SESSION["id_rol"] === 1) {

        header("Location: admin/dashboard.php");
        exit;
    }

    header("Location: estudiante/dashboard.php");
    exit;


} catch (PDOException $e) {

    header(
        "Location: cambiar_password.php?error=" .
        urlencode("No fue posible actualizar la contraseña.")
    );

    exit;
}