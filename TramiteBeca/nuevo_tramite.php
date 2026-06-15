<?php
session_start();
include "json_helper.php";

if (!isset($_SESSION['usuario'])) {
    header("location: login.php");
    exit();
}

$flujo_data = leer_json('flujo_baera.json');
$tickets = leer_json('tickets.json');
$formularios = leer_json('formularios_baera.json');

$ticket_id = obtener_siguiente_ticket();

$tickets[] = [
    'ticket' => $ticket_id,
    'flujo' => $flujo_data['flujo'],
    'proceso' => 'P1',
    'usuario' => $_SESSION['usuario'],
    'fechainicial' => date('Y-m-d H:i:s'),
    'fechafinal' => null
];

$formularios[$ticket_id] = [];

guardar_json('tickets.json', $tickets);
guardar_json('formularios_baera.json', $formularios);

registrar_historial(
    $ticket_id,
    $_SESSION['usuario'],
    $_SESSION['rol'],
    'Inicio de trámite',
    'Nuevo trámite de beca BAERA creado',
    'EN_PROCESO'
);

header("location: controlador.php?flujo={$flujo_data['flujo']}&proceso=P1&ticket={$ticket_id}");
exit();
