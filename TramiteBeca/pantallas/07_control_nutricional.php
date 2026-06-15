<?php
$datos_est = $datos['P1'] ?? [];
$datos_nutricional = $datos_proceso ?? [];

$peso = $datos_nutricional['peso'] ?? '';
$talla = $datos_nutricional['talla'] ?? '';
$imc = '';
$diagnostico = $datos_nutricional['diagnostico_nutricional'] ?? '';

if ($peso && $talla && $talla > 0) {
    $imc = round($peso / ($talla * $talla), 2);
    if ($imc < 18.5) $diagnostico_sugerido = 'Bajo peso';
    elseif ($imc < 25) $diagnostico_sugerido = 'Normal';
    elseif ($imc < 30) $diagnostico_sugerido = 'Sobrepeso';
    else $diagnostico_sugerido = 'Obesidad';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control nutricional</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .card { background: white; border-radius: 8px; padding: 24px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2, h3 { color: #1a1a2e; margin-top: 0; }
        .data-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin: 12px 0; padding: 12px; background: #f8f9fa; border-radius: 4px; }
        .data-grid dt { font-weight: bold; color: #555; font-size: 13px; }
        .data-grid dd { margin: 0 0 8px 0; color: #333; }
        label { display: block; margin-bottom: 4px; color: #333; font-weight: bold; font-size: 13px; }
        input, textarea, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 12px; box-sizing: border-box; }
        textarea { height: 80px; resize: vertical; }
        .btn { width: 100%; padding: 10px; background: #1a1a2e; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #16213e; }
        .info { background: #e8f4fd; padding: 10px; border-radius: 4px; margin-bottom: 16px; color: #2980b9; }
        .imc-result { font-size: 24px; font-weight: bold; text-align: center; padding: 16px; background: #e8f8f5; border-radius: 4px; margin: 12px 0; }
    </style>
    <script>
        function calcularIMC() {
            var peso = document.getElementById('peso').value;
            var talla = document.getElementById('talla').value;
            if (peso && talla && talla > 0) {
                var imc = peso / (talla * talla);
                document.getElementById('imc').value = imc.toFixed(2);
                var diag = '';
                if (imc < 18.5) diag = 'Bajo peso';
                else if (imc < 25) diag = 'Normal';
                else if (imc < 30) diag = 'Sobrepeso';
                else diag = 'Obesidad';
                document.getElementById('diagnostico_sugerido').innerHTML = 'Diagnóstico sugerido: ' + diag;
            }
        }
    </script>
</head>
<body>
    <div class="card">
        <h2>Control nutricional</h2>
        <div class="info">Ticket #<?= $ticket ?> - Evaluación del Nutricionista</div>

        <h3>Datos del postulante</h3>
        <dl class="data-grid">
            <dt>CI</dt><dd><?= htmlspecialchars($datos_est['ci'] ?? '-') ?></dd>
            <dt>Nombre</dt><dd><?= htmlspecialchars(($datos_est['nombres'] ?? '') . ' ' . ($datos_est['apellidos'] ?? '')) ?></dd>
            <dt>Carrera</dt><dd><?= htmlspecialchars($datos_est['carrera'] ?? '-') ?></dd>
        </dl>

        <h3>Evaluación nutricional</h3>
        <form method="POST" action="controlador.php">
            <input type="hidden" name="flujo" value="<?= $flujo_id ?>">
            <input type="hidden" name="proceso" value="<?= $proceso_id ?>">
            <input type="hidden" name="ticket" value="<?= $ticket ?>">

            <label>Peso (kg)</label>
            <input type="number" step="0.01" id="peso" name="peso" value="<?= htmlspecialchars($peso) ?>" oninput="calcularIMC()" required>

            <label>Talla (m)</label>
            <input type="number" step="0.01" id="talla" name="talla" value="<?= htmlspecialchars($talla) ?>" oninput="calcularIMC()" required>

            <label>Índice de Masa Corporal (IMC)</label>
            <input type="text" id="imc" name="indice_masa_corporal" value="<?= htmlspecialchars($imc ?: ($datos_nutricional['indice_masa_corporal'] ?? '')) ?>" readonly>
            <div id="diagnostico_sugerido" class="info" style="text-align:center;"></div>

            <label>Diagnóstico nutricional</label>
            <select name="diagnostico_nutricional" id="diagnostico_nutricional">
                <option value="">Seleccionar...</option>
                <option value="Bajo peso" <?= $diagnostico === 'Bajo peso' ? 'selected' : '' ?>>Bajo peso</option>
                <option value="Normal" <?= $diagnostico === 'Normal' ? 'selected' : '' ?>>Normal</option>
                <option value="Sobrepeso" <?= $diagnostico === 'Sobrepeso' ? 'selected' : '' ?>>Sobrepeso</option>
                <option value="Obesidad" <?= $diagnostico === 'Obesidad' ? 'selected' : '' ?>>Obesidad</option>
            </select>

            <label>Observaciones nutricionales</label>
            <textarea name="observaciones_nutricionales"><?= htmlspecialchars($datos_nutricional['observaciones_nutricionales'] ?? '') ?></textarea>

            <button type="submit" name="siguiente" value="1" class="btn">Guardar y enviar al Comité BAERA</button>
        </form>
    </div>
</body>
</html>
