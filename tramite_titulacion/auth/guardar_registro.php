<?php

include "../helpers/json_helper.php";

$usuarios = leerJson("../data/usuarios.json");

$id = count($usuarios) + 1;

$usuarios[] = [
    "id" => $id,
    "usuario" => $_POST["usuario"],
    "password" => $_POST["password"],
    "nombre" => $_POST["nombre"],
    "rol" => "ESTUDIANTE",
    "estado" => "ACTIVO"
];

guardarJson("../data/usuarios.json", $usuarios);

header("Location: login.php?registro=ok");
exit();