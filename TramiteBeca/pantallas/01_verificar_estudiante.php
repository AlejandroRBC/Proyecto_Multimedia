<?php
$guardado = $datos_proceso ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campos = [
        'ci' => $_POST['ci'] ?? '',
        'ru' => $_POST['ru'] ?? '',
        'nombres' => $_POST['nombres'] ?? '',
        'apellidos' => $_POST['apellidos'] ?? '',
        'carrera' => $_POST['carrera'] ?? '',
        'facultad' => $_POST['facultad'] ?? '',
        'fecha_registro' => date('Y-m-d H:i:s')
    ];
    $formularios[$ticket]['P1'] = $campos;
    guardar_json('formularios_baera.json', $formularios);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head><meta charset="UTF-8"><title>Datos registrados</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .card { background: white; border-radius: 8px; padding: 24px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #1a1a2e; margin-top: 0; }
        .data { margin: 16px 0; }
        .data dt { font-weight: bold; color: #555; margin-top: 8px; }
        .data dd { margin-left: 0; color: #333; }
        .btn { display: inline-block; padding: 10px 20px; background: #27ae60; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; font-size: 14px; }
        .btn:hover { background: #219a52; }
        .success { color: #27ae60; font-weight: bold; }
    </style>
    </head>
    <body>
    <div class="card">
        <h2>Registro exitoso</h2>
        <p class="success">Tus datos han sido registrados correctamente</p>
        <dl class="data">
            <dt>CI</dt><dd><?= htmlspecialchars($campos['ci']) ?></dd>
            <dt>RU</dt><dd><?= htmlspecialchars($campos['ru']) ?></dd>
            <dt>Nombres</dt><dd><?= htmlspecialchars($campos['nombres']) ?></dd>
            <dt>Apellidos</dt><dd><?= htmlspecialchars($campos['apellidos']) ?></dd>
            <dt>Carrera</dt><dd><?= htmlspecialchars($campos['carrera']) ?></dd>
            <dt>Facultad</dt><dd><?= htmlspecialchars($campos['facultad']) ?></dd>
        </dl>
        <form method="POST" action="controlador.php">
            <input type="hidden" name="flujo" value="<?= $flujo_id ?>">
            <input type="hidden" name="proceso" value="<?= $proceso_id ?>">
            <input type="hidden" name="ticket" value="<?= $ticket ?>">
            <?php foreach ($campos as $k => $v): ?>
                <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
            <?php endforeach; ?>
            <button type="submit" name="siguiente" value="1" class="btn">Continuar con datos socioeconómicos</button>
        </form>
    </div>
    </body>
    </html>
    <?php
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de postulante</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .card { background: white; border-radius: 8px; padding: 24px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #1a1a2e; margin-top: 0; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        label { display: block; margin-bottom: 4px; color: #333; font-weight: bold; font-size: 13px; }
        input { width: 100%; padding: 8px; margin-bottom: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .full { grid-column: 1 / -1; }
        .btn { width: 100%; padding: 10px; background: #1a1a2e; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #16213e; }
        .info { color: #666; font-size: 14px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Paso 1: Registro del postulante</h2>
        <p class="info">Ingresa tus datos personales para iniciar la postulación a la Beca BAERA.</p>
        <form method="POST">
            <div class="form-row">
                <div>
                    <label>Número de CI</label>
                    <input type="text" name="ci" value="<?= htmlspecialchars($guardado['ci'] ?? '') ?>" required>
                </div>
                <div>
                    <label>RU</label>
                    <input type="text" name="ru" value="<?= htmlspecialchars($guardado['ru'] ?? '') ?>" required>
                </div>
                <div>
                    <label>Nombres</label>
                    <input type="text" name="nombres" value="<?= htmlspecialchars($guardado['nombres'] ?? '') ?>" required>
                </div>
                <div>
                    <label>Apellidos</label>
                    <input type="text" name="apellidos" value="<?= htmlspecialchars($guardado['apellidos'] ?? '') ?>" required>
                </div>
                <div class="full">
                    <label>Carrera</label>
                    <input type="text" name="carrera" value="<?= htmlspecialchars($guardado['carrera'] ?? '') ?>" required>
                </div>
                <div class="full">
                    <label>Facultad</label>
                    <input type="text" name="facultad" value="<?= htmlspecialchars($guardado['facultad'] ?? '') ?>" required>
                </div>
            </div>
            <button type="submit" class="btn">Registrar y continuar</button>
        </form>
    </div>
</body>
</html>
