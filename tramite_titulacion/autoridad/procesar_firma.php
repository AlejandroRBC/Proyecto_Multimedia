<?php
include "../helpers/auth.php";
verificarRol("AUTORIDAD");
include "../helpers/json_helper.php";

$id = (int) ($_GET["id"] ?? 0);
$tramites = leerJson("../data/tramites.json");

foreach ($tramites as &$t) {
    if ($t["id"] == $id) {
        $t["estado"] = "FIRMADO";
        $t["fechaFirma"] = date("Y-m-d H:i:s");
        break;
    }
}

guardarJson("../data/tramites.json", $tramites);
registrarHistorial("../data/historial.json", $id, "FIRMADO", "Autoridad firmó el Título Profesional");
header("Location: bandeja.php");
exit();