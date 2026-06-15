<?php
$datos_estudiante = $datos['P1'] ?? [];
$datos_docs = $datos['P3']['documentos'] ?? [];
$revision_guardada = $datos_proceso ?? [];

$documentos_lista = [
    'croquis' => 'Croquis del domicilio',
    'qr_ubicacion' => 'Código QR de ubicación',
    'foto_frontis' => 'Fotografía del frontis del domicilio',
    'foto_carnet' => 'Fotografía tamaño carnet',
    'plan_estudios' => 'Plan de estudios vigente',
    'boletas_inscripcion' => 'Boletas de inscripción gestión correspondiente',
    'historial_academico' => 'Historial académico',
    'matricula' => 'Matrícula universitaria',
    'cedula_identidad' => 'Fotocopia de Cédula de Identidad',
    'declaracion_social' => 'Declaración de situación social',
    'certificado_gestora' => 'Certificado de no aportación a la Gestora'
];

$tipos_archivo = [
    'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
    'gif' => 'image/gif', 'pdf' => 'application/pdf'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Revisión documental</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .card { background: white; border-radius: 8px; padding: 24px; max-width: 900px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2, h3 { color: #1a1a2e; margin-top: 0; }
        .info { background: #e8f4fd; padding: 10px; border-radius: 4px; margin-bottom: 16px; color: #2980b9; }
        .data-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin: 12px 0; padding: 12px; background: #f8f9fa; border-radius: 4px; }
        .data-grid dt { font-weight: bold; color: #555; font-size: 13px; }
        .data-grid dd { margin: 0 0 8px 0; color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 12px 0; }
        th { background: #1a1a2e; color: white; padding: 10px 8px; text-align: left; font-size: 13px; }
        td { padding: 8px; border-bottom: 1px solid #eee; vertical-align: middle; }
        .doc-preview { max-width: 80px; max-height: 60px; display: block; border-radius: 4px; }
        .doc-link { color: #3498db; text-decoration: none; font-size: 12px; }
        .doc-link:hover { text-decoration: underline; }
        .radio-group { display: flex; gap: 12px; }
        .radio-group label { font-size: 13px; cursor: pointer; }
        .radio-aprobar { color: #27ae60; font-weight: bold; }
        .radio-observar { color: #e74c3c; font-weight: bold; }
        textarea.doc-obs { width: 100%; height: 40px; font-size: 12px; padding: 4px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box; resize: vertical; }
        .btn-enviar { width: 100%; padding: 12px; background: #1a1a2e; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn-enviar:hover { background: #16213e; }
        .badge-ok { display: inline-block; background: #27ae60; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; }
        .badge-pending { display: inline-block; background: #e67e22; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Revisión documental</h2>
        <div class="info">Ticket #<?= $ticket ?> - Revisa cada documento y marca si está correcto u observado</div>

        <h3>Datos del estudiante</h3>
        <dl class="data-grid">
            <dt>CI</dt><dd><?= htmlspecialchars($datos_estudiante['ci'] ?? '-') ?></dd>
            <dt>Nombre</dt><dd><?= htmlspecialchars(($datos_estudiante['nombres'] ?? '') . ' ' . ($datos_estudiante['apellidos'] ?? '')) ?></dd>
            <dt>Carrera</dt><dd><?= htmlspecialchars($datos_estudiante['carrera'] ?? '-') ?></dd>
            <dt>Facultad</dt><dd><?= htmlspecialchars($datos_estudiante['facultad'] ?? '-') ?></dd>
        </dl>

        <h3>Revisión por documento</h3>
        <form method="POST" action="controlador.php" onsubmit="return validarRevision()">
            <input type="hidden" name="flujo" value="<?= $flujo_id ?>">
            <input type="hidden" name="proceso" value="<?= $proceso_id ?>">
            <input type="hidden" name="ticket" value="<?= $ticket ?>">
            <input type="hidden" name="decision" id="decision" value="">

            <table>
                <tr>
                    <th style="width:25%">Documento</th>
                    <th style="width:10%">Archivo</th>
                    <th style="width:30%">Estado</th>
                    <th style="width:35%">Observación</th>
                </tr>
                <?php foreach ($documentos_lista as $key => $label):
                    $tiene_doc = isset($datos_docs[$key]);
                    $archivo = $datos_docs[$key] ?? '';
                    $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                    $es_imagen = in_array($ext, ['jpg','jpeg','png','gif']);
                    $ruta_archivo = 'uploads/' . $archivo;
                    $estado_previo = $revision_guardada['doc_estado'][$key] ?? 'aprobado';
                    $obs_previa = $revision_guardada['doc_obs'][$key] ?? '';
                ?>
                <tr>
                    <td><?= htmlspecialchars($label) ?></td>
                    <td>
                        <?php if ($tiene_doc): ?>
                            <?php if ($es_imagen): ?>
                                <img src="<?= $ruta_archivo ?>" alt="preview" class="doc-preview">
                            <?php endif; ?>
                            <a href="<?= $ruta_archivo ?>" target="_blank" class="doc-link">Ver</a>
                        <?php else: ?>
                            <span class="badge-pending">No adjuntado</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="radio-group">
                            <label class="radio-aprobar">
                                <input type="radio" name="doc_estado[<?= $key ?>]" value="aprobado" <?= $estado_previo === 'aprobado' ? 'checked' : '' ?>>
                                Correcto
                            </label>
                            <label class="radio-observar">
                                <input type="radio" name="doc_estado[<?= $key ?>]" value="observado" <?= $estado_previo === 'observado' ? 'checked' : '' ?>>
                                Observado
                            </label>
                        </div>
                    </td>
                    <td>
                        <textarea name="doc_obs[<?= $key ?>]" class="doc-obs" placeholder="¿Por qué está observado?"><?= htmlspecialchars($obs_previa) ?></textarea>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>

            <button type="submit" name="siguiente" value="1" class="btn-enviar">Enviar revisión</button>
        </form>
    </div>

    <script>
    function validarRevision() {
        var docs = <?= json_encode(array_keys($documentos_lista)) ?>;
        var todosAprobados = true;
        var hayObservados = false;
        var errores = [];

        docs.forEach(function(key) {
            var radios = document.getElementsByName('doc_estado[' + key + ']');
            var seleccionado = null;
            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) {
                    seleccionado = radios[i].value;
                    break;
                }
            }
            if (seleccionado === 'observado') {
                todosAprobados = false;
                hayObservados = true;
                var obs = document.getElementsByName('doc_obs[' + key + ']')[0];
                if (obs && obs.value.trim() === '') {
                    errores.push('Debes escribir una observación para el documento observado');
                }
            } else if (!seleccionado) {
                todosAprobados = false;
                errores.push('Debes seleccionar un estado para cada documento');
            }
        });

        if (errores.length > 0) {
            alert(errores.join('\n'));
            return false;
        }

        document.getElementById('decision').value = todosAprobados ? 'aprobar' : 'observar';
        return true;
    }
    </script>
</body>
</html>
