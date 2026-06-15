<?php
define('DATA_DIR', __DIR__ . '/data/');

function leer_json($archivo)
{
    $ruta = DATA_DIR . $archivo;
    if (!file_exists($ruta)) return [];
    $contenido = file_get_contents($ruta);
    return json_decode($contenido, true) ?? [];
}

function guardar_json($archivo, $datos)
{
    $ruta = DATA_DIR . $archivo;
    $json = json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($ruta, $json, LOCK_EX);
}

function buscar_proceso($flujo, $proceso_id)
{
    foreach ($flujo['procesos'] as $p) {
        if ($p['id'] == $proceso_id) return $p;
    }
    return null;
}

function buscar_condicion($flujo, $proceso_id, $decision)
{
    if (!isset($flujo['condiciones'])) return null;
    foreach ($flujo['condiciones'] as $c) {
        if ($c['proceso'] == $proceso_id && $c['decision'] == $decision) {
            return $c;
        }
    }
    return null;
}

function registrar_historial($ticket, $usuario, $rol, $accion, $observaciones, $estado)
{
    $historial = leer_json('historial.json');
    $historial[] = [
        'ticket' => $ticket,
        'fecha' => date('Y-m-d H:i:s'),
        'usuario' => $usuario,
        'rol' => $rol,
        'accion' => $accion,
        'observaciones' => $observaciones,
        'estado' => $estado
    ];
    guardar_json('historial.json', $historial);
}

function obtener_siguiente_ticket()
{
    $tickets = leer_json('tickets.json');
    if (empty($tickets)) return 1;
    $max = max(array_column($tickets, 'ticket'));
    return $max + 1;
}
