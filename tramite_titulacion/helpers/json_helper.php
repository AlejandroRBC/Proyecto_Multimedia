<?php

function leerJson($archivo)
{
    if (!file_exists($archivo)) {

        $dir = dirname($archivo);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($archivo, "[]");
    }
    $contenido = file_get_contents($archivo);
    return json_decode($contenido, true) ?? [];
}

function guardarJson($archivo, $datos)
{

    $dir = dirname($archivo);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $json = json_encode(
        $datos,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );
    return file_put_contents($archivo, $json) !== false;
}


function registrarHistorial($archivo, $tramiteId, $estado, $nota = "")
{
    $historial = leerJson($archivo);
    $historial[] = [
        "tramite" => (int) $tramiteId,
        "estado" => $estado,
        "nota" => $nota,
        "fecha" => date("Y-m-d H:i:s")
    ];
    guardarJson($archivo, $historial);
}