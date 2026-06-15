<?php
$datos_est = $datos['P1'] ?? [];
$datos_socio = $datos['P2'] ?? [];
$datos_entrevista = $datos_proceso ?? [];
$datos_entrevista_prog = $datos['P5'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Entrevista trabajo social</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .card { background: white; border-radius: 8px; padding: 24px; max-width: 800px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2, h3 { color: #1a1a2e; margin-top: 0; }
        .data-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin: 12px 0; padding: 12px; background: #f8f9fa; border-radius: 4px; }
        .data-grid dt { font-weight: bold; color: #555; font-size: 13px; }
        .data-grid dd { margin: 0 0 8px 0; color: #333; }
        label { display: block; margin-bottom: 4px; color: #333; font-weight: bold; font-size: 13px; }
        textarea, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 12px; box-sizing: border-box; }
        textarea { height: 80px; resize: vertical; }
        .btn { width: 100%; padding: 10px; background: #1a1a2e; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #16213e; }
        .info { background: #e8f4fd; padding: 10px; border-radius: 4px; margin-bottom: 16px; color: #2980b9; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Entrevista socioeconómica</h2>
        <div class="info">Ticket #<?= $ticket ?> - Evaluación del Trabajador Social</div>

        <h3>Datos del postulante</h3>
        <dl class="data-grid">
            <dt>CI</dt><dd><?= htmlspecialchars($datos_est['ci'] ?? '-') ?></dd>
            <dt>Nombre</dt><dd><?= htmlspecialchars(($datos_est['nombres'] ?? '') . ' ' . ($datos_est['apellidos'] ?? '')) ?></dd>
            <dt>Carrera</dt><dd><?= htmlspecialchars($datos_est['carrera'] ?? '-') ?></dd>
            <dt>Dirección</dt><dd><?= htmlspecialchars($datos_socio['direccion'] ?? '-') ?></dd>
            <dt>Ingreso familiar</dt><dd><?= htmlspecialchars($datos_socio['ingreso_familiar'] ?? '-') ?> Bs</dd>
            <dt>Integrantes</dt><dd><?= htmlspecialchars($datos_socio['integrantes_hogar'] ?? '-') ?></dd>
            <dt>Vivienda</dt><dd><?= htmlspecialchars($datos_socio['tipo_vivienda'] ?? '-') ?></dd>
        </dl>

        <?php if (!empty($datos_entrevista_prog['fecha_entrevista'])): ?>
            <div class="info">Fecha de entrevista programada: <?= $datos_entrevista_prog['fecha_entrevista'] ?></div>
        <?php endif; ?>

        <h3>Evaluación social</h3>
        <form method="POST" action="controlador.php">
            <input type="hidden" name="flujo" value="<?= $flujo_id ?>">
            <input type="hidden" name="proceso" value="<?= $proceso_id ?>">
            <input type="hidden" name="ticket" value="<?= $ticket ?>">

            <label>Descripción de la situación familiar</label>
            <textarea name="situacion_familiar"><?= htmlspecialchars($datos_entrevista['situacion_familiar'] ?? '') ?></textarea>

            <label>Composición del núcleo familiar</label>
            <textarea name="composicion_familiar"><?= htmlspecialchars($datos_entrevista['composicion_familiar'] ?? '') ?></textarea>

            <label>Principales ingresos y gastos</label>
            <textarea name="ingresos_gastos"><?= htmlspecialchars($datos_entrevista['ingresos_gastos'] ?? '') ?></textarea>

            <label>Condiciones de vivienda</label>
            <textarea name="condiciones_vivienda"><?= htmlspecialchars($datos_entrevista['condiciones_vivienda'] ?? '') ?></textarea>

            <label>Situación de salud relevante</label>
            <textarea name="situacion_salud"><?= htmlspecialchars($datos_entrevista['situacion_salud'] ?? '') ?></textarea>

            <label>Observaciones del entrevistador</label>
            <textarea name="observaciones_trabajador_social"><?= htmlspecialchars($datos_entrevista['observaciones_trabajador_social'] ?? '') ?></textarea>

            <label>Recomendación social</label>
            <select name="recomendacion_social">
                <option value="favorable" <?= ($datos_entrevista['recomendacion_social'] ?? '') === 'favorable' ? 'selected' : '' ?>>Favorable</option>
                <option value="desfavorable" <?= ($datos_entrevista['recomendacion_social'] ?? '') === 'desfavorable' ? 'selected' : '' ?>>Desfavorable</option>
                <option value="con_observaciones" <?= ($datos_entrevista['recomendacion_social'] ?? '') === 'con_observaciones' ? 'selected' : '' ?>>Con observaciones</option>
            </select>

            <button type="submit" name="siguiente" value="1" class="btn">Guardar y enviar a control nutricional</button>
        </form>
    </div>
</body>
</html>
