<?php
session_start();
include "json_helper.php";

if (!isset($_SESSION['usuario'])) {
    header("location: login.php");
    exit();
}

$flujo_id = $_GET['flujo'] ?? $_POST['flujo'] ?? null;
$proceso_id = $_GET['proceso'] ?? $_POST['proceso'] ?? null;
$ticket = $_GET['ticket'] ?? $_POST['ticket'] ?? null;

if (!$flujo_id || !$proceso_id || !$ticket) {
    header("location: bandeja.php");
    exit();
}

$flujo_data = leer_json('flujo_baera.json');
$tickets = leer_json('tickets.json');
$formularios = leer_json('formularios_baera.json');

$proceso = buscar_proceso($flujo_data, $proceso_id);
if (!$proceso) {
    echo "<h2>Error: Proceso no encontrado</h2>";
    exit();
}

$ticket_actual = null;
foreach ($tickets as $t) {
    if ($t['ticket'] == $ticket && $t['proceso'] == $proceso_id && $t['fechafinal'] == null) {
        $ticket_actual = $t;
        break;
    }
}
if (!$ticket_actual) {
    echo "<h2>Error: Ticket no encontrado o ya completado</h2>";
    exit();
}

$datos = $formularios[$ticket] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['siguiente'])) {
    $campos = $_POST;
    unset($campos['flujo'], $campos['proceso'], $campos['ticket'], $campos['siguiente'], $campos['decision']);

    if (!empty($campos)) {
        $existentes = $formularios[$ticket][$proceso_id] ?? [];
        $formularios[$ticket][$proceso_id] = array_merge($existentes, $campos);
        guardar_json('formularios_baera.json', $formularios);
    }

    foreach ($tickets as &$t) {
        if ($t['ticket'] == $ticket && $t['proceso'] == $proceso_id && $t['fechafinal'] == null) {
            $t['fechafinal'] = date('Y-m-d H:i:s');
            break;
        }
    }
    unset($t);

    $decision = $_POST['decision'] ?? $_POST['siguiente'];
    if ($decision === '1') {
        $decision = null;
    }

    $siguiente_id = null;
    if ($decision) {
        $condicion = buscar_condicion($flujo_data, $proceso_id, $decision);
        if ($condicion) {
            $siguiente_id = $condicion['proceso_destino'];
        }
    }
    if (!$siguiente_id) {
        $siguiente_id = $proceso['siguiente'] ?? null;
    }

    $accion = "Completado: {$proceso['nombre']}";
    $observaciones = $decision ? "Decisión: $decision" : "Avance automático";
    $estado = $siguiente_id ? "EN_PROCESO" : "FINALIZADO";

    registrar_historial($ticket, $_SESSION['usuario'], $_SESSION['rol'], $accion, $observaciones, $estado);

    if ($siguiente_id) {
        $proc_sig = buscar_proceso($flujo_data, $siguiente_id);
        if (!$proc_sig) {
            guardar_json('tickets.json', $tickets);
            echo "<h2>Error: Proceso siguiente no encontrado</h2>";
            exit();
        }

        $usuario_asignado = $ticket_actual['usuario'];
        if ($proc_sig['rol'] === 'Estudiante') {
            foreach ($tickets as $t) {
                if ($t['ticket'] == $ticket && $t['proceso'] === 'P1') {
                    $usuario_asignado = $t['usuario'];
                    break;
                }
            }
        } else {
            $usuarios = leer_json('usuarios.json');
            foreach ($usuarios as $u) {
                if ($u['rol'] === $proc_sig['rol']) {
                    $usuario_asignado = $u['usuario'];
                    break;
                }
            }
        }

        $tickets[] = [
            'ticket' => $ticket,
            'flujo' => $flujo_id,
            'proceso' => $siguiente_id,
            'usuario' => $usuario_asignado,
            'fechainicial' => date('Y-m-d H:i:s'),
            'fechafinal' => null
        ];

        guardar_json('tickets.json', $tickets);

        if ($proc_sig['rol'] === 'Sistema' && !$proc_sig['pantalla']) {
            $formularios[$ticket][$siguiente_id] = [
                'fecha_programacion' => date('Y-m-d H:i:s'),
                'fecha_entrevista' => date('Y-m-d H:i:s', strtotime('+1 day 09:00:00'))
            ];
            guardar_json('formularios_baera.json', $formularios);

            foreach ($tickets as &$t) {
                if ($t['ticket'] == $ticket && $t['proceso'] == $siguiente_id && $t['fechafinal'] == null) {
                    $t['fechafinal'] = date('Y-m-d H:i:s');
                    break;
                }
            }
            unset($t);

            registrar_historial($ticket, 'Sistema', 'Sistema', "Auto: {$proc_sig['nombre']}", 'Procesado automáticamente', 'EN_PROCESO');

            if ($proc_sig['siguiente']) {
                $sig2 = buscar_proceso($flujo_data, $proc_sig['siguiente']);
                if ($sig2) {
                    $usuario_asignado2 = $ticket_actual['usuario'];
                    if ($sig2['rol'] === 'Estudiante') {
                        foreach ($tickets as $t) {
                            if ($t['ticket'] == $ticket && $t['proceso'] === 'P1') {
                                $usuario_asignado2 = $t['usuario'];
                                break;
                            }
                        }
                    } else {
                        $usuarios = leer_json('usuarios.json');
                        foreach ($usuarios as $u) {
                            if ($u['rol'] === $sig2['rol']) {
                                $usuario_asignado2 = $u['usuario'];
                                break;
                            }
                        }
                    }

                    $tickets[] = [
                        'ticket' => $ticket,
                        'flujo' => $flujo_id,
                        'proceso' => $proc_sig['siguiente'],
                        'usuario' => $usuario_asignado2,
                        'fechainicial' => date('Y-m-d H:i:s'),
                        'fechafinal' => null
                    ];
                    guardar_json('tickets.json', $tickets);
                }
            }
            header("location: bandeja.php");
            exit();
        }

        if ($proc_sig['rol'] === $_SESSION['rol']) {
            header("location: controlador.php?flujo={$flujo_id}&proceso={$siguiente_id}&ticket={$ticket}");
        } else {
            header("location: bandeja.php");
        }
        exit();
    }

    guardar_json('tickets.json', $tickets);
    header("location: bandeja.php");
    exit();
}

if ($proceso['pantalla'] && file_exists(__DIR__ . '/' . $proceso['pantalla'] . '.php')) {
    $datos_proceso = $datos[$proceso_id] ?? [];
    include __DIR__ . '/' . $proceso['pantalla'] . '.php';
} else {
    echo "<h2>Error: Pantalla no encontrada para {$proceso['nombre']}</h2>";
}
