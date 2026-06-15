<?php

include "../helpers/auth.php";
verificarRol("AUTORIDAD");
include "../helpers/json_helper.php";

$id = (int) ($_GET["id"] ?? 0);
$tramites = leerJson("../data/tramites.json");

foreach ($tramites as &$t) {
    if ($t["id"] == $id) {
        $t["estado"] = "ENTREGADO";
        $t["fechaEntrega"] = date("Y-m-d H:i:s");
        break;
    }
}

guardarJson("../data/tramites.json", $tramites);
registrarHistorial("../data/historial.json", $id, "ENTREGADO", "Título Profesional entregado al graduado");

header("Location: bandeja.php");
exit();