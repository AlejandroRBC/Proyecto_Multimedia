<?php
include "../helpers/auth.php";
verificarRol("ESTUDIANTE");
include "../helpers/json_helper.php";

$tramiteId = (int) ($_POST["id"] ?? 0);
$tramites = leerJson("../data/tramites.json");

$docKeys = [
    "diplomaAcademico",
    "certificadoNacimiento",
    "cedula",
    "fotografias",
    "comprobantePago",
    "informeDecanato",
    "servicioRural",
    "recordAcademico"
];

foreach ($tramites as &$t) {
    if ($t["id"] == $tramiteId) {
        $documentos = [];
        $links = [];
        foreach ($docKeys as $key) {
            $marcado = isset($_POST["doc_{$key}"]);
            $link = trim($_POST["link_{$key}"] ?? "");
            $documentos[$key] = $marcado;
            $links[$key] = ($marcado && !empty($link)) ? $link : null;
        }
        $t["documentos"] = $documentos;
        $t["links"] = $links;
        unset($t["archivos"]);
        $t["estado"] = "PENDIENTE_REVISION";
        break;
    }
}

guardarJson("../data/tramites.json", $tramites);
registrarHistorial("../data/historial.json", $tramiteId, "PENDIENTE_REVISION", "Documentos enviados por el estudiante (enlaces)");
header("Location: mis_tramites.php");
exit();