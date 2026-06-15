<?php
session_start();
include "../helpers/json_helper.php";

$usuarios = leerJson("../data/usuarios.json");
$usuario = trim($_POST["usuario"] ?? "");
$password = trim($_POST["password"] ?? "");
$rolElegido = trim($_POST["rol"] ?? "");

foreach ($usuarios as $u) {
    if ($u["usuario"] === $usuario && $u["password"] === $password) {

        if (isset($u["estado"]) && $u["estado"] !== "ACTIVO") {
            header("Location: login.php?error=inactivo");
            exit();
        }

        if ($u["rol"] !== $rolElegido) {
            header("Location: login.php?error=rol");
            exit();
        }

        $_SESSION["usuario"] = $u;

        switch ($u["rol"]) {
            case "ESTUDIANTE":
                header("Location: ../estudiante/dashboard.php");
                break;
            case "FUNCIONARIO":
                header("Location: ../funcionario/dashboard.php");
                break;
            case "AUTORIDAD":
                header("Location: ../autoridad/dashboard.php");
                break;
            case "ADMIN":
                header("Location: ../admin/dashboard.php");
                break;
            default:
                header("Location: login.php?error=invalido");
                break;
        }
        exit();
    }
}

header("Location: login.php?error=invalido");
exit();