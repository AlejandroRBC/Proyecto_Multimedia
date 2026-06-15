<?php
$datos_est = $datos['P1'] ?? [];
$datos_comite = $datos['P8'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Beca aprobada</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .card { background: white; border-radius: 8px; padding: 40px; max-width: 600px; text-align: center; box-shadow: 0 2px 20px rgba(0,0,0,0.1); }
        .icon { font-size: 64px; color: #27ae60; margin-bottom: 16px; }
        h2 { color: #27ae60; margin: 0 0 12px 0; font-size: 28px; }
        p { color: #555; line-height: 1.6; margin: 8px 0; }
        .data { background: #f8f9fa; padding: 16px; border-radius: 4px; margin: 16px 0; text-align: left; }
        .data dt { font-weight: bold; color: #555; font-size: 13px; margin-top: 8px; }
        .data dd { margin: 0 0 4px 0; color: #333; }
        .btn { display: inline-block; padding: 12px 32px; background: #27ae60; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; font-size: 16px; margin-top: 16px; }
        .btn:hover { background: #219a52; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">&#10004;</div>
        <h2>¡Beca BAERA APROBADA!</h2>
        <p>Felicitaciones, <strong><?= htmlspecialchars(($datos_est['nombres'] ?? '') . ' ' . ($datos_est['apellidos'] ?? '')) ?></strong>.</p>
        <p>Ha sido seleccionado como beneficiario de la Beca BAERA.</p>

        <dl class="data">
            <dt>CI</dt><dd><?= htmlspecialchars($datos_est['ci'] ?? '-') ?></dd>
            <dt>Carrera</dt><dd><?= htmlspecialchars($datos_est['carrera'] ?? '-') ?></dd>
            <dt>Facultad</dt><dd><?= htmlspecialchars($datos_est['facultad'] ?? '-') ?></dd>
        </dl>

        <p>Por favor, acérquese a la oficina de Bienestar Social para continuar con el proceso administrativo correspondiente.</p>

        <form method="POST" action="controlador.php">
            <input type="hidden" name="flujo" value="<?= $flujo_id ?>">
            <input type="hidden" name="proceso" value="<?= $proceso_id ?>">
            <input type="hidden" name="ticket" value="<?= $ticket ?>">
            <button type="submit" name="siguiente" value="1" class="btn">Finalizar</button>
        </form>
    </div>
</body>
</html>
