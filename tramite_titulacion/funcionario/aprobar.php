<?php


include "../helpers/auth.php";
verificarRol("FUNCIONARIO");
include "../helpers/json_helper.php";

$id = (int) ($_GET["id"] ?? 0);
$tramites = leerJson("../data/tramites.json");

foreach ($tramites as &$t) {
    if ($t["id"] == $id) {
        $t["estado"] = "REVISION_AUTORIDAD";
        break;
    }
}

guardarJson("../data/tramites.json", $tramites);
registrarHistorial("../data/historial.json", $id, "APROBADO", "Funcionario aprobó documentación");

header("Location: bandeja.php");
exit();