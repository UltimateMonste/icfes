<?php

session_start();

require_once __DIR__ . "/config/conexion.php";

/*
|--------------------------------------------------------------------------
| Verificar que la solicitud sea POST
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Obtener datos del formulario
|--------------------------------------------------------------------------
*/

$documento = trim($_POST["documento"] ?? "");
$password = $_POST["password"] ?? "";


/*
|--------------------------------------------------------------------------
| Validar campos
|--------------------------------------------------------------------------
*/

if ($documento === "" || $password === "") {
    header("Location: login.php?error=" . urlencode("Debe completar todos los campos."));
    exit;
}


/*
|--------------------------------------------------------------------------
| Buscar usuario por número de documento
|--------------------------------------------------------------------------
*/

try {

    $sql = "SELECT
                id_usuario,
                nombres,
                apellidos,
                correo,
                password,
                grado,
                id_curso,
                avatar,
                id_avatar,
                puntos,
                nivel,
                numero_documento,
                id_rol,
                id_institucion,
                primer_ingreso,
                estado
            FROM usuarios
            WHERE numero_documento = :documento
            LIMIT 1";

    $consulta = $conexion->prepare($sql);

    $consulta->execute([
        ":documento" => $documento
    ]);

    $usuario = $consulta->fetch();


} catch (PDOException $e) {

    /*
     * No mostramos el error real de MySQL al usuario.
     * Esto evita revelar información interna de la aplicación.
     */

    header("Location: login.php?error=" . urlencode("No fue posible procesar el inicio de sesión."));
    exit;
}


/*
|--------------------------------------------------------------------------
| Verificar que el usuario exista
|--------------------------------------------------------------------------
*/

if (!$usuario) {

    header("Location: login.php?error=" . urlencode("Documento o contraseña incorrectos."));
    exit;
}


/*
|--------------------------------------------------------------------------
| Verificar estado de la cuenta
|--------------------------------------------------------------------------
*/

if ($usuario["estado"] !== "Activo") {

    header("Location: login.php?error=" . urlencode("La cuenta se encuentra inactiva. Comuníquese con el administrador."));
    exit;
}


/*
|--------------------------------------------------------------------------
| Verificar contraseña
|--------------------------------------------------------------------------
*/

if (!password_verify($password, $usuario["password"])) {

    header("Location: login.php?error=" . urlencode("Documento o contraseña incorrectos."));
    exit;
}


/*
|--------------------------------------------------------------------------
| Regenerar ID de sesión
|--------------------------------------------------------------------------
|
| Ayuda a prevenir ataques de fijación de sesión.
|
*/

session_regenerate_id(true);


/*
|--------------------------------------------------------------------------
| Crear variables de sesión
|--------------------------------------------------------------------------
*/

$_SESSION["id_usuario"] = $usuario["id_usuario"];
$_SESSION["nombres"] = $usuario["nombres"];
$_SESSION["apellidos"] = $usuario["apellidos"];
$_SESSION["correo"] = $usuario["correo"];
$_SESSION["grado"] = $usuario["grado"];
$_SESSION["id_curso"] = $usuario["id_curso"];
$_SESSION["avatar"] = $usuario["avatar"];
$_SESSION["id_avatar"] = $usuario["id_avatar"];
$_SESSION["puntos"] = $usuario["puntos"];
$_SESSION["nivel"] = $usuario["nivel"];
$_SESSION["numero_documento"] = $usuario["numero_documento"];
$_SESSION["id_rol"] = $usuario["id_rol"];
$_SESSION["id_institucion"] = $usuario["id_institucion"];
$_SESSION["primer_ingreso"] = $usuario["primer_ingreso"];


/*
|--------------------------------------------------------------------------
| Actualizar último acceso
|--------------------------------------------------------------------------
*/

try {

    $sql_actualizar = "UPDATE usuarios
                       SET ultimo_acceso = NOW()
                       WHERE id_usuario = :id_usuario";

    $actualizar = $conexion->prepare($sql_actualizar);

    $actualizar->execute([
        ":id_usuario" => $usuario["id_usuario"]
    ]);

} catch (PDOException $e) {

    /*
     * Si falla esta actualización no impedimos el acceso,
     * porque la autenticación ya fue correcta.
     */

}


/*
|--------------------------------------------------------------------------
| Redirección según el rol
|--------------------------------------------------------------------------
*/

if ((int)$usuario["id_rol"] === 1) {

    /*
     * Administrador
     */

    header("Location: admin/dashboard.php");
    exit;
}


if ((int)$usuario["id_rol"] === 2) {

    /*
     * Estudiante
     *
     * Si es su primer ingreso debe cambiar
     * obligatoriamente la contraseña.
     */

    if ((int)$usuario["primer_ingreso"] === 1) {

        header("Location: cambiar_password.php");
        exit;
    }

    /*
     * Estudiante que ya cambió su contraseña.
     */

    header("Location: estudiante/dashboard.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Rol no reconocido
|--------------------------------------------------------------------------
*/

session_unset();
session_destroy();

header("Location: login.php?error=" . urlencode("El usuario no tiene un rol válido."));
exit;