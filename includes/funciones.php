<?php

function limpiarTexto($texto)
{
    return trim($texto);
}

function redireccionar($url)
{
    header("Location: " . $url);
    exit;
}

function usuarioEstaLogueado()
{
    return isset($_SESSION['id_usuario']);
}