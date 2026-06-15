<?php
$datos_est = $datos['P1'] ?? [];
$datos_comite = $datos['P8'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Beca rechazada</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .card { background: white; border-radius: 8px; padding: 40px; max-width: 600px; text-align: center; box-shadow: 0 2px 20px rgba(0,0,0,0.1); }
        .icon { font-size: 64px; color: #e74c3c; margin-bottom: 16px; }
        h2 { color: #e74c3c; margin: 0 0 12px 0; font-size: 28px; }
        p { color: #555; line-height: 1.6; margin: 8px 0; }
        .data { background: #f8f9fa; padding: 16px; border-radius: 4px; margin: 16px 0; text-align: left; }
        .data dt { font-weight: bold; color: #555; font-size: 13px; margin-top: 8px; }
        .data dd { margin: 0 0 4px 0; color: #333; }
        .observaciones { background: #fdf0ef; padding: 16px; border-radius: 4px; margin: 16px 0; text-align: left; }
        .observaciones h4 { margin: 0 0 8px 0; color: #e74c3c; }
        .observaciones p { color: #333; }
        .btn { display: inline-block; padding: 12px 32px; background: #1a1a2e; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; font-size: 16px; margin-top: 16px; }
        .btn:hover { background: #16213e; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">&#10008;</div>
        <h2>Beca BAERA No otorgada</h2>
        <p>Estimado(a) <strong><?= htmlspecialchars(($datos_est['nombres'] ?? '') . ' ' . ($datos_est['apellidos'] ?? '')) ?></strong>,</p>
        <p>Lamentamos informarle que su solicitud de Beca BAERA no ha sido aprobada.</p>

        <dl class="data">
            <dt>CI</dt><dd><?= htmlspecialchars($datos_est['ci'] ?? '-') ?></dd>
            <dt>Carrera</dt><dd><?= htmlspecialchars($datos_est['carrera'] ?? '-') ?></dd>
            <dt>Facultad</dt><dd><?= htmlspecialchars($datos_est['facultad'] ?? '-') ?></dd>
        </dl>

        <?php if (!empty($datos_comite['observaciones_comite'])): ?>
        <div class="observaciones">
            <h4>Observaciones del Comité BAERA</h4>
            <p><?= nl2br(htmlspecialchars($datos_comite['observaciones_comite'])) ?></p>
        </div>
        <?php endif; ?>

        <form method="POST" action="controlador.php">
            <input type="hidden" name="flujo" value="<?= $flujo_id ?>">
            <input type="hidden" name="proceso" value="<?= $proceso_id ?>">
            <input type="hidden" name="ticket" value="<?= $ticket ?>">
            <button type="submit" name="siguiente" value="1" class="btn">Finalizar</button>
        </form>
    </div>
</body>
</html>
