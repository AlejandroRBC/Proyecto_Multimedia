<?php
include "../helpers/auth.php";
verificarRol("AUTORIDAD");
include "../helpers/json_helper.php";

$id = (int) ($_POST["id"] ?? 0);
$motivo = trim($_POST["motivo"] ?? "");

if (!$id || empty($motivo)) {
    header("Location: bandeja.php");
    exit();
}

$tramites = leerJson("../data/tramites.json");
foreach ($tramites as &$t) {
    if ($t["id"] == $id) {
        $t["estado"] = "OBSERVADO";
        break;
    }
}
guardarJson("../data/tramites.json", $tramites);

registrarHistorial(
    "../data/historial.json",
    $id,
    "OBSERVADO",
    "Autoridad rechazó el trámite: " . $motivo
);

$observaciones = leerJson("../data/observaciones.json");
$observaciones[] = [
    "tramite" => $id,
    "descripcion" => "[Rechazo de Autoridad] " . $motivo,
    "fecha" => date("Y-m-d H:i:s"),
];
guardarJson("../data/observaciones.json", $observaciones);

header("Location: bandeja.php?rechazo=ok");
exit();