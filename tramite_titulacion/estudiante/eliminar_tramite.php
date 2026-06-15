<?php
include "../helpers/auth.php";
verificarRol("ESTUDIANTE");
include "../helpers/json_helper.php";

$id = (int) ($_GET["id"] ?? 0);
$usuario = $_SESSION["usuario"];
$tramites = leerJson("../data/tramites.json");

$nuevo = [];
foreach ($tramites as $t) {

    if (
        $t["id"] == $id &&
        $t["estudiante"] == $usuario["id"] &&
        in_array($t["estado"], ["BORRADOR", "OBSERVADO"])
    ) {
        continue;
    }
    $nuevo[] = $t;
}

guardarJson("../data/tramites.json", $nuevo);

header("Location: mis_tramites.php");
exit();