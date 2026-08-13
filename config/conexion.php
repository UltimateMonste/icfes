<?php

$host = "localhost";
$nombre_bd = "icfes_platform";
$usuario_bd = "root";
$password_bd = "";

try {
    $conexion = new PDO(
        "mysql:host=$host;dbname=$nombre_bd;charset=utf8mb4",
        $usuario_bd,
        $password_bd
    );

    $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $conexion->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $e) {
    die("Error de conexión con la base de datos.");
}