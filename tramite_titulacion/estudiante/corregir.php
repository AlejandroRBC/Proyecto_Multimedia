<?php

include "../helpers/auth.php";
verificarRol("ESTUDIANTE");
include "../helpers/json_helper.php";

$id = (int) ($_GET["id"] ?? 0);
$tramites = leerJson("../data/tramites.json");

foreach ($tramites as &$t) {
    if ($t["id"] == $id) {
        $t["estado"] = "PENDIENTE_REVISION";
        break;
    }
}

guardarJson("../data/tramites.json", $tramites);

registrarHistorial(
    "../data/historial.json",
    $id,
    "PENDIENTE_REVISION",
    "Estudiante subsanó observaciones y reenvió a revisión"
);

header("Location: mis_tramites.php");
exit();