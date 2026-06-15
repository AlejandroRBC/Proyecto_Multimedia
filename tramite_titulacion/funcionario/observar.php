<?php

include "../helpers/auth.php";
verificarRol("FUNCIONARIO");
include "../helpers/json_helper.php";

$id = (int) ($_POST["id"] ?? 0);
$obs = htmlspecialchars($_POST["observacion"] ?? "");

if (!$id || empty($obs)) {
    header("Location: bandeja.php");
    exit();
}

$tramites = leerJson("../data/tramites.json");
$observaciones = leerJson("../data/observaciones.json");

foreach ($tramites as &$t) {
    if ($t["id"] == $id) {
        $t["estado"] = "OBSERVADO";
        break;
    }
}

$observaciones[] = [
    "tramite" => $id,
    "descripcion" => $obs,
    "fecha" => date("Y-m-d H:i:s")
];

guardarJson("../data/tramites.json", $tramites);
guardarJson("../data/observaciones.json", $observaciones);
registrarHistorial("../data/historial.json", $id, "OBSERVADO", "Funcionario observó: " . $obs);

header("Location: bandeja.php");
exit();