<?php
include "../helpers/auth.php";
verificarRol("ESTUDIANTE");
include "../helpers/json_helper.php";

$tramites = leerJson("../data/tramites.json");
$id = count($tramites) + 1;

$carreraSeleccionada = htmlspecialchars($_POST["carrera"] ?? "");
if ($carreraSeleccionada === "Otros") {
    $carreraFinal = htmlspecialchars(trim($_POST["otraCarrera"] ?? "Otros"));
} else {
    $carreraFinal = $carreraSeleccionada;
}

$esExcelencia = isset($_POST["excelencia"]) && $_POST["excelencia"] == "1";

$modalidadPago = $esExcelencia ? "EXCELENCIA" : htmlspecialchars($_POST["modalidadPago"] ?? "");


$comprobanteUrl = null;
if (!$esExcelencia) {
    $link = trim($_POST["comprobantePago"] ?? "");
    if (!empty($link)) {
        $comprobanteUrl = $link;
    }
}

$tramites[] = [
    "id" => $id,
    "estudiante" => $_SESSION["usuario"]["id"],
    "nombreEstudiante" => $_SESSION["usuario"]["nombre"],
    "fechaRegistro" => date("Y-m-d"),
    "carrera" => $carreraFinal,
    "nivel" => htmlspecialchars($_POST["nivel"] ?? ""),
    "excelencia" => $esExcelencia,
    "extranjero" => isset($_POST["extranjero"]),
    "modalidadPago" => $modalidadPago,
    "comprobantePago" => $comprobanteUrl,
    "estado" => "BORRADOR",
    "documentos" => [
        "diplomaAcademico" => false,
        "certificadoNacimiento" => false,
        "cedula" => false,
        "fotografias" => false,
        "comprobantePago" => false,
        "informeDecanato" => false,
        "servicioRural" => false,
        "recordAcademico" => false
    ]
];

guardarJson("../data/tramites.json", $tramites);
registrarHistorial("../data/historial.json", $id, "BORRADOR", "Trámite creado por el estudiante");

header("Location: documentos.php?id=" . $id);
exit();