<?php
$datos_est = $datos['P1'] ?? [];
$datos_socio = $datos['P2'] ?? [];
$datos_revision = $datos['P4'] ?? [];
$datos_entrevista = $datos['P6'] ?? [];
$datos_nutricional = $datos['P7'] ?? [];
$datos_comite = $datos_proceso ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comité BAERA</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .card { background: white; border-radius: 8px; padding: 24px; max-width: 800px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2, h3 { color: #1a1a2e; margin-top: 0; }
        .info { background: #e8f4fd; padding: 10px; border-radius: 4px; margin-bottom: 16px; color: #2980b9; }
        .resumen { margin: 12px 0; }
        .resumen-item { padding: 12px; margin-bottom: 8px; background: #f8f9fa; border-radius: 4px; border-left: 4px solid #1a1a2e; }
        .resumen-item h4 { margin: 0 0 6px 0; color: #1a1a2e; }
        .resumen-item p { margin: 4px 0; color: #555; font-size: 14px; }
        .resumen-item .label { font-weight: bold; color: #333; }
        label { display: block; margin-bottom: 4px; color: #333; font-weight: bold; font-size: 13px; }
        textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 12px; resize: vertical; height: 80px; box-sizing: border-box; }
        .btn-group { display: flex; gap: 12px; }
        .btn-aprobar, .btn-rechazar { flex: 1; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; color: white; }
        .btn-aprobar { background: #27ae60; }
        .btn-aprobar:hover { background: #219a52; }
        .btn-rechazar { background: #e74c3c; }
        .btn-rechazar:hover { background: #c0392b; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Comité BAERA - Decisión final</h2>
        <div class="info">Ticket #<?= $ticket ?> - Evaluación del Comité BAERA</div>

        <h3>Resumen consolidado del caso</h3>

        <div class="resumen">
            <div class="resumen-item">
                <h4>Datos del estudiante</h4>
                <p><span class="label">CI:</span> <?= htmlspecialchars($datos_est['ci'] ?? '-') ?></p>
                <p><span class="label">Nombre:</span> <?= htmlspecialchars(($datos_est['nombres'] ?? '') . ' ' . ($datos_est['apellidos'] ?? '')) ?></p>
                <p><span class="label">Carrera:</span> <?= htmlspecialchars($datos_est['carrera'] ?? '-') ?></p>
                <p><span class="label">Facultad:</span> <?= htmlspecialchars($datos_est['facultad'] ?? '-') ?></p>
            </div>

            <div class="resumen-item">
                <h4>Resultado de revisión documental</h4>
                <p><?= htmlspecialchars($datos_revision['observaciones_revision'] ?? 'Sin observaciones registradas') ?></p>
            </div>

            <div class="resumen-item">
                <h4>Informe del Trabajador Social</h4>
                <p><span class="label">Situación familiar:</span> <?= htmlspecialchars(mb_substr($datos_entrevista['situacion_familiar'] ?? 'No registrado', 0, 200)) ?></p>
                <p><span class="label">Recomendación:</span> <?= htmlspecialchars($datos_entrevista['recomendacion_social'] ?? 'No registrada') ?></p>
            </div>

            <div class="resumen-item">
                <h4>Informe nutricional</h4>
                <p><span class="label">IMC:</span> <?= htmlspecialchars($datos_nutricional['indice_masa_corporal'] ?? 'No registrado') ?></p>
                <p><span class="label">Diagnóstico:</span> <?= htmlspecialchars($datos_nutricional['diagnostico_nutricional'] ?? 'No registrado') ?></p>
            </div>
        </div>

        <h3>Decisión del comité</h3>
        <form method="POST" action="controlador.php">
            <input type="hidden" name="flujo" value="<?= $flujo_id ?>">
            <input type="hidden" name="proceso" value="<?= $proceso_id ?>">
            <input type="hidden" name="ticket" value="<?= $ticket ?>">

            <label>Observaciones del comité</label>
            <textarea name="observaciones_comite"><?= htmlspecialchars($datos_comite['observaciones_comite'] ?? '') ?></textarea>

            <div class="btn-group">
                <button type="submit" name="siguiente" value="aprobar" class="btn-aprobar">APROBAR BECA</button>
                <button type="submit" name="siguiente" value="rechazar" class="btn-rechazar">RECHAZAR BECA</button>
            </div>
        </form>
    </div>
</body>
</html>
