<?php
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

$doc_guardados = $datos_proceso['documentos'] ?? [];
$doc_estados = $datos['P4']['doc_estado'] ?? [];
$doc_obs = $datos['P4']['doc_obs'] ?? [];
$viene_observado = !empty($datos['P4']);
$tiene_observados = false;
foreach ($doc_estados as $k => $v) {
    if ($v === 'observado') $tiene_observados = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['documento'])) {
    $doc_key = $_POST['doc_key'] ?? '';
    $archivo = $_FILES['documento'];
    if ($doc_key && $archivo['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $ext = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombre = "{$ticket}_{$doc_key}." . $ext;
        move_uploaded_file($archivo['tmp_name'], $upload_dir . $nombre);
        $doc_guardados[$doc_key] = $nombre;
        $formularios[$ticket]['P3']['documentos'] = $doc_guardados;
        guardar_json('formularios_baera.json', $formularios);
        $mensaje = "Documento adjuntado correctamente";
    }
}

$todos_completos = count($doc_guardados) >= count($documentos_lista);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Adjuntar documentos</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .card { background: white; border-radius: 8px; padding: 24px; max-width: 800px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #1a1a2e; margin-top: 0; }
        .observacion-box { background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; padding: 16px; margin-bottom: 16px; }
        .observacion-box h3 { color: #856404; margin: 0 0 8px 0; font-size: 16px; }
        .obs-item { padding: 8px 0; border-bottom: 1px solid #ffeeba; }
        .obs-item:last-child { border: none; }
        .obs-item .doc-name { font-weight: bold; color: #856404; }
        .obs-item .doc-reason { color: #666; font-size: 14px; margin: 4px 0 0 0; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th { background: #1a1a2e; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #eee; vertical-align: middle; }
        .estado-pendiente { color: #e67e22; font-weight: bold; }
        .estado-adjuntado { color: #27ae60; font-weight: bold; }
        .estado-observado { color: #e74c3c; font-weight: bold; }
        input[type="file"] { max-width: 200px; }
        .btn-upload { padding: 4px 12px; background: #3498db; color: white; border: none; border-radius: 3px; cursor: pointer; }
        .btn-upload:hover { background: #2980b9; }
        .btn-submit { width: 100%; padding: 10px; background: <?= $todos_completos ? '#27ae60' : '#95a5a6' ?>; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn-submit:hover { background: <?= $todos_completos ? '#219a52' : '#7f8c8d' ?>; }
        .mensaje { padding: 10px; background: #d4edda; color: #155724; border-radius: 4px; margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Paso 3: Adjuntar documentos</h2>

        <?php if ($tiene_observados): ?>
        <div class="observacion-box">
            <h3>Documentos observados en la revisión anterior</h3>
            <?php foreach ($doc_estados as $key => $estado): ?>
                <?php if ($estado === 'observado'): ?>
                <div class="obs-item">
                    <div class="doc-name"><?= htmlspecialchars($documentos_lista[$key]) ?></div>
                    <div class="doc-reason"><?= htmlspecialchars($doc_obs[$key] ?? 'Sin observación específica') ?></div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <p style="margin: 8px 0 0 0; font-size: 13px; color: #856404;">Vuelve a adjuntar los documentos observados para continuar.</p>
        </div>
        <?php endif; ?>

        <?php if (isset($mensaje)): ?><div class="mensaje"><?= $mensaje ?></div><?php endif; ?>
        <table>
            <tr><th>Documento</th><th>Estado</th><th>Adjuntar</th></tr>
            <?php foreach ($documentos_lista as $key => $label):
                $estado_doc = isset($doc_guardados[$key]) ? 'adjuntado' : 'pendiente';
                if (isset($doc_estados[$key]) && $doc_estados[$key] === 'observado') {
                    $estado_doc = 'observado';
                }
            ?>
            <tr>
                <td><?= htmlspecialchars($label) ?></td>
                <td>
                    <?php if ($estado_doc === 'adjuntado'): ?>
                        <span class="estado-adjuntado">Adjuntado</span>
                    <?php elseif ($estado_doc === 'observado'): ?>
                        <span class="estado-observado">Observado</span>
                    <?php else: ?>
                        <span class="estado-pendiente">Pendiente</span>
                    <?php endif; ?>
                </td>
                <td>
                    <form method="POST" enctype="multipart/form-data" style="display:flex; gap:4px;">
                        <input type="hidden" name="flujo" value="<?= $flujo_id ?>">
                        <input type="hidden" name="proceso" value="<?= $proceso_id ?>">
                        <input type="hidden" name="ticket" value="<?= $ticket ?>">
                        <input type="hidden" name="doc_key" value="<?= $key ?>">
                        <input type="file" name="documento" required style="font-size:12px;">
                        <button type="submit" class="btn-upload">Subir</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <form method="POST">
            <input type="hidden" name="flujo" value="<?= $flujo_id ?>">
            <input type="hidden" name="proceso" value="<?= $proceso_id ?>">
            <input type="hidden" name="ticket" value="<?= $ticket ?>">
            <button type="submit" name="siguiente" value="1" class="btn-submit" <?= !$todos_completos ? 'disabled' : '' ?>>
                <?= $todos_completos ? 'Todos adjuntados - Enviar a revisión' : 'Faltan documentos por adjuntar' ?>
            </button>
        </form>
    </div>
</body>
</html>
