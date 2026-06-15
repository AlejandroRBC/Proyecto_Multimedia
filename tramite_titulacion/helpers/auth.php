<?php

session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../auth/login.php");
    exit();
}

function verificarRol($rolesPermitidos)
{
    $rol = $_SESSION["usuario"]["rol"] ?? "";
    if (!in_array($rol, (array) $rolesPermitidos)) {
        header("Location: ../auth/login.php");
        exit();
    }
}