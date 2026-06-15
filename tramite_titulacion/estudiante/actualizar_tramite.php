<?php
include "../helpers/auth.php";
verificarRol("ESTUDIANTE");
include "../helpers/json_helper.php";

$id = (int) ($_POST["id"] ?? 0);
$usuario = $_SESSION["usuario"];
$tramites = leerJson("../data/tramites.json");

foreach ($tramites as &$t) {
    if ($t["id"] == $id && $t["estudiante"] == $usuario["id"]) {

        if (!in_array($t["estado"], ["BORRADOR", "OBSERVADO"])) {
            header("Location: mis_tramites.php");
            exit();
        }


        $carreraSeleccionada = htmlspecialchars($_POST["carrera"] ?? "");
        if ($carreraSeleccionada === "Otros") {
            $t["carrera"] = htmlspecialchars(trim($_POST["otraCarrera"] ?? "Otros"));
        } else {
            $t["carrera"] = $carreraSeleccionada;
        }

        $esExcelencia = isset($_POST["excelencia"]) && $_POST["excelencia"] == "1";
        $t["nivel"] = htmlspecialchars($_POST["nivel"] ?? "");
        $t["excelencia"] = $esExcelencia;
        $t["extranjero"] = isset($_POST["extranjero"]);
        $t["modalidadPago"] = $esExcelencia ? "EXCELENCIA" : htmlspecialchars($_POST["modalidadPago"] ?? "");
        break;
    }
}

guardarJson("../data/tramites.json", $tramites);
registrarHistorial("../data/historial.json", $id, "BORRADOR", "Trámite actualizado por el estudiante");

header("Location: mis_tramites.php");
exit();