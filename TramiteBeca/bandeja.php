<?php
session_start();
include "json_helper.php";

if (!isset($_SESSION['usuario'])) {
    header("location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$rol = $_SESSION['rol'];
$tickets = leer_json('tickets.json');

$pendientes = array_filter($tickets, function ($t) use ($usuario, $rol) {
    return $t['usuario'] == $usuario && $t['fechafinal'] == null;
});
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bandeja - Beca BAERA</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f0f2f5; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        h2 { margin: 0; color: #1a1a2e; }
        .user-info { color: #666; }
        .user-info a { color: #e74c3c; text-decoration: none; margin-left: 10px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        th { background: #1a1a2e; color: white; padding: 12px 15px; text-align: left; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; }
        tr:hover { background: #f8f9fa; }
        .btn { display: inline-block; padding: 6px 16px; background: #27ae60; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .btn:hover { background: #219a52; }
        .btn-new { display: inline-block; padding: 10px 20px; background: #1a1a2e; color: white; text-decoration: none; border-radius: 4px; margin-top: 20px; }
        .btn-new:hover { background: #16213e; }
        .empty { text-align: center; padding: 40px; color: #999; }
        .rol-badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 12px; background: #e8f4fd; color: #2980b9; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h2>Bandeja de tareas</h2>
            <span class="user-info">Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?>
                <span class="rol-badge"><?= htmlspecialchars($_SESSION['rol']) ?></span>
                <a href="logout.php">Cerrar sesión</a>
            </span>
        </div>
    </div>

    <?php if (empty($pendientes)): ?>
        <div class="empty">No tienes tareas pendientes</div>
    <?php else: ?>
        <table>
            <tr>
                <th>Ticket</th>
                <th>Flujo</th>
                <th>Proceso</th>
                <th>Desde</th>
                <th>Acción</th>
            </tr>
            <?php foreach ($pendientes as $t): ?>
            <tr>
                <td><?= $t['ticket'] ?></td>
                <td><?= htmlspecialchars($t['flujo']) ?></td>
                <td><?= htmlspecialchars($t['proceso']) ?></td>
                <td><?= $t['fechainicial'] ?></td>
                <td>
                    <a class="btn" href="controlador.php?flujo=<?= $t['flujo'] ?>&proceso=<?= $t['proceso'] ?>&ticket=<?= $t['ticket'] ?>">Atender</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <div style="margin-top: 20px; display: flex; gap: 12px;">
    <?php if ($rol === 'Comité BAERA'): ?>
        <a class="btn-new" href="usuarios.php" style="background: #3498db;">
            Gestionar usuarios
        </a>

    <?php elseif ($rol === 'Estudiante'): ?>
        <a class="btn-new" href="nuevo_tramite.php">
            + Nuevo registro de solicitud
        </a>

    <?php endif; ?>
</div>
</body>
</html>