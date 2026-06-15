<?php
include "../helpers/auth.php";
verificarRol("AUTORIDAD");
include "../helpers/json_helper.php";

$id = (int) ($_GET["id"] ?? 0);
$tramites = leerJson("../data/tramites.json");
$historial = leerJson("../data/historial.json");
$tramite = null;
foreach ($tramites as $t) {
    if ($t["id"] == $id) {
        $tramite = $t;
        break;
    }
}
$historialTramite = array_values(array_filter($historial, fn($h) => $h["tramite"] == $id));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Revisar – Trámite #<?= $id ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f4f8;
        }

        /* TOPBAR */
        .topbar {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            padding: 0 32px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .25);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
        }

        .topbar-brand .logo-icon {
            width: 34px;
            height: 34px;
            background: rgba(255, 255, 255, .15);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, .85);
            font-size: .85rem;
        }

        .topbar-avatar {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, .2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
            font-size: .85rem;
        }

        .topbar-right a {
            color: rgba(255, 255, 255, .7);
            text-decoration: none;
            font-size: .82rem;
            padding: 6px 14px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, .2);
            transition: background .2s;
        }

        .topbar-right a:hover {
            background: rgba(255, 255, 255, .1);
            color: #fff;
        }

        /* LAYOUT */
        .contenido {
            max-width: 860px;
            margin: 0 auto;
            padding: 28px 24px;
        }

        .page-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .page-sub {
            font-size: .85rem;
            color: #64748b;
            margin-bottom: 24px;
        }

        /* PANEL */
        .panel {
            background: #fff;
            border-radius: 16px;
            border: 1.5px solid #e2e8f0;
            padding: 22px 26px;
            margin-bottom: 18px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .04);
        }

        .panel-title {
            font-size: .92rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1.5px solid #f1f5f9;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        /* INFO GRID */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 24px;
        }

        .info-item {
            font-size: .88rem;
            color: #374151;
        }

        .info-item strong {
            color: #1e293b;
            display: block;
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #94a3b8;
            margin-bottom: 2px;
        }

        /* BADGE ESTADO */
        .estado-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: .75rem;
            font-weight: 700;
        }

        .e-APROBADO {
            background: #f0fdf4;
            color: #166534;
            border: 1.5px solid #86efac;
        }

        .e-REVISION_AUTORIDAD {
            background: #fff7ed;
            color: #c2410c;
            border: 1.5px solid #fed7aa;
        }

        /* DOCUMENTOS */
        .doc-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 8px;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            flex-wrap: wrap;
        }

        .doc-row.doc-ok {
            border-color: #86efac;
            background: #f0fdf4;
        }

        .doc-row.doc-falta {
            border-color: #fca5a5;
            background: #fff8f8;
        }

        .doc-icon {
            font-size: 1rem;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .doc-name {
            font-size: .88rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .doc-link-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 7px;
            font-size: .78rem;
            font-weight: 600;
            text-decoration: none;
            background: #eff6ff;
            color: #1e40af;
            border: 1.5px solid #bfdbfe;
            transition: background .15s;
        }

        .doc-link-btn:hover {
            background: #dbeafe;
        }

        .doc-sin-enlace {
            font-size: .75rem;
            color: #92400e;
        }

        .doc-no-pres {
            font-size: .75rem;
            color: #b91c1c;
        }

        /* HISTORIAL TIMELINE */
        .historial-timeline {
            display: flex;
            flex-direction: column;
        }

        .historial-item {
            display: flex;
            gap: 14px;
            padding: 10px 0;
            border-left: 2px solid #e2e8f0;
            margin-left: 8px;
            padding-left: 18px;
            position: relative;
        }

        .historial-item:last-child {
            border-left-color: transparent;
        }

        .historial-dot {
            position: absolute;
            left: -7px;
            top: 14px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #2563eb;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #bfdbfe;
        }

        .historial-item:first-child .historial-dot {
            background: #16a34a;
            box-shadow: 0 0 0 2px #bbf7d0;
        }

        .historial-estado {
            font-size: .82rem;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 2px;
        }

        .historial-nota {
            font-size: .8rem;
            color: #64748b;
        }

        .historial-fecha {
            font-size: .72rem;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* ACCIONES */
        .acciones-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: stretch;
        }

        /* BTN FIRMAR */
        .btn-firmar {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: #fff;
            padding: 12px 26px;
            border: none;
            border-radius: 10px;
            font-size: .95rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, .3);
            transition: transform .15s;
        }

        .btn-firmar:hover {
            transform: translateY(-1px);
        }

        /* BTN RECHAZAR (toggle) */
        .btn-rechazar-toggle {
            background: #fff;
            color: #b91c1c;
            padding: 12px 22px;
            border: 1.5px solid #fca5a5;
            border-radius: 10px;
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: background .2s, border-color .2s;
        }

        .btn-rechazar-toggle:hover {
            background: #fef2f2;
            border-color: #ef4444;
        }

        /* BTN VOLVER */
        .btn-volver {
            background: #f1f5f9;
            color: #475569;
            padding: 12px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: .9rem;
            border: 1.5px solid #e2e8f0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background .2s;
        }

        .btn-volver:hover {
            background: #e2e8f0;
        }

        /* PANEL RECHAZO */
        .rechazo-panel {
            display: none;
            background: #fef2f2;
            border: 1.5px solid #fca5a5;
            border-radius: 14px;
            padding: 22px 24px;
            margin-top: 16px;
        }

        .rechazo-panel.visible {
            display: block;
        }

        .rechazo-title {
            font-size: .9rem;
            font-weight: 700;
            color: #b91c1c;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .rechazo-label {
            font-size: .82rem;
            font-weight: 600;
            color: #7f1d1d;
            margin-bottom: 6px;
            display: block;
        }

        .rechazo-textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #fca5a5;
            border-radius: 10px;
            font-size: .9rem;
            color: #1a202c;
            background: #fff;
            resize: vertical;
            min-height: 100px;
            font-family: 'Segoe UI', sans-serif;
            transition: border-color .2s, box-shadow .2s;
        }

        .rechazo-textarea:focus {
            outline: none;
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, .1);
        }

        .rechazo-hint {
            font-size: .75rem;
            color: #b91c1c;
            margin-top: 6px;
        }

        /* Motivos rápidos */
        .motivos-rapidos {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }

        .motivo-chip {
            background: #fff;
            border: 1.5px solid #fca5a5;
            border-radius: 20px;
            padding: 5px 14px;
            font-size: .78rem;
            color: #b91c1c;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s, border-color .15s;
        }

        .motivo-chip:hover {
            background: #fef2f2;
            border-color: #ef4444;
        }

        .rechazo-actions {
            display: flex;
            gap: 10px;
            margin-top: 16px;
        }

        .btn-confirmar-rechazo {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: #fff;
            padding: 10px 22px;
            border: none;
            border-radius: 10px;
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            box-shadow: 0 3px 10px rgba(220, 38, 38, .3);
            transition: transform .15s;
        }

        .btn-confirmar-rechazo:hover {
            transform: translateY(-1px);
        }

        .btn-cancelar-rechazo {
            background: transparent;
            color: #64748b;
            padding: 10px 18px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: .88rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s;
        }

        .btn-cancelar-rechazo:hover {
            background: #f1f5f9;
        }

        .alerta-danger {
            background: #fef2f2;
            border: 1.5px solid #fca5a5;
            border-radius: 12px;
            padding: 16px;
            color: #b91c1c;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <!-- TOPBAR -->
    <div class="topbar">
        <div class="topbar-brand">
            <div class="logo-icon">🎓</div>
            Sistema BPM – Autoridad
        </div>
        <div class="topbar-right">
            <div class="topbar-user">
                <div class="topbar-avatar"><?= strtoupper(substr($_SESSION["usuario"]["nombre"], 0, 1)) ?></div>
                <?= htmlspecialchars($_SESSION["usuario"]["nombre"]) ?>
            </div>
            <a href="bandeja.php">← Bandeja</a>
            <a href="../auth/logout.php">Cerrar sesión</a>
        </div>
    </div>

    <div class="contenido">

        <div class="page-title">✍️ Revisión de Trámite #<?= $id ?></div>
        <div class="page-sub">Verifique los documentos y decida si firma o rechaza el trámite.</div>

        <?php if (!$tramite): ?>
            <div class="alerta-danger">⚠️ Trámite no encontrado.</div>
        <?php else: ?>

            <!-- DATOS DEL SOLICITANTE -->
            <div class="panel">
                <div class="panel-title">👤 Datos del Solicitante</div>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Estudiante</strong>
                        <?= htmlspecialchars($tramite["nombreEstudiante"] ?? $tramite["estudiante"]) ?>
                    </div>
                    <div class="info-item">
                        <strong>Carrera</strong>
                        <?= htmlspecialchars($tramite["carrera"]) ?>
                    </div>
                    <div class="info-item">
                        <strong>Nivel</strong>
                        <?= htmlspecialchars($tramite["nivel"]) ?>
                    </div>
                    <div class="info-item">
                        <strong>Estado</strong>
                        <span class="estado-badge e-<?= htmlspecialchars($tramite["estado"]) ?>">
                            <?= htmlspecialchars($tramite["estado"]) ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- DOCUMENTOS -->
            <div class="panel">
                <div class="panel-title">📂 Documentos del Estudiante</div>
                <p style="font-size:.83rem;color:#64748b;margin-bottom:14px;">Revise cada documento antes de tomar una
                    decisión.</p>
                <?php
                $nombresDoc = [
                    "diplomaAcademico" => "📜 Diploma Académico",
                    "certificadoNacimiento" => "🪪 Certificado de Nacimiento",
                    "cedula" => "🆔 Cédula de Identidad",
                    "fotografias" => "📷 Fotografías",
                    "comprobantePago" => "💳 Comprobante de Pago",
                    "informeDecanato" => "🏫 Informe de Decanato",
                    "servicioRural" => "🏥 Servicio Rural",
                    "recordAcademico" => "📋 Record Académico",
                ];
                $docs = $tramite["documentos"] ?? [];
                $links = $tramite["links"] ?? [];
                foreach ($nombresDoc as $key => $nombre):
                    $marcado = !empty($docs[$key]);
                    $link = $links[$key] ?? "";
                    $tieneLink = !empty($link);
                    $rowClass = $marcado ? "doc-ok" : "doc-falta";
                    ?>
                    <div class="doc-row <?= $rowClass ?>">
                        <div class="doc-icon"><?= $marcado ? "✅" : "❌" ?></div>
                        <div style="flex:1">
                            <div class="doc-name"><?= $nombre ?></div>
                            <?php if ($marcado && $tieneLink): ?>
                                <div style="margin-top:6px">
                                    <a href="<?= htmlspecialchars($link) ?>" target="_blank" class="doc-link-btn">
                                        🔗 Ver documento
                                    </a>
                                </div>
                            <?php elseif ($marcado): ?>
                                <div style="margin-top:4px"><span class="doc-sin-enlace">⚠️ Sin enlace adjunto</span></div>
                            <?php else: ?>
                                <div style="margin-top:4px"><span class="doc-no-pres">✘ No presentado</span></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- HISTORIAL -->
            <div class="panel">
                <div class="panel-title">📋 Historial del Proceso</div>
                <?php if (empty($historialTramite)): ?>
                    <p style="color:#94a3b8;font-size:.85rem;">Sin registros.</p>
                <?php else: ?>
                    <div class="historial-timeline">
                        <?php foreach (array_reverse($historialTramite) as $h): ?>
                            <div class="historial-item">
                                <div class="historial-dot"></div>
                                <div>
                                    <div class="historial-estado"><?= htmlspecialchars($h["estado"]) ?></div>
                                    <div class="historial-nota"><?= htmlspecialchars($h["nota"] ?? "") ?></div>
                                    <div class="historial-fecha">🕐 <?= htmlspecialchars($h["fecha"]) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- PANEL DE DECISIÓN -->
            <div class="panel">
                <div class="panel-title">⚖️ Decisión de la Autoridad</div>

                <div class="acciones-row">
                    <!-- FIRMAR -->
                    <a href="procesar_firma.php?id=<?= $id ?>" class="btn-firmar"
                        onclick="return confirm('¿Confirma la firma del trámite #<?= $id ?>?')">
                        ✍️ Firmar y Aprobar
                    </a>

                    <!-- RECHAZAR (abre panel) -->
                    <button type="button" class="btn-rechazar-toggle" onclick="mostrarRechazo()">
                        ✕ Rechazar Trámite
                    </button>

                    <a href="bandeja.php" class="btn-volver">← Volver</a>
                </div>

                <!-- PANEL DE RECHAZO -->
                <div class="rechazo-panel" id="rechazoPanel">
                    <div class="rechazo-title">⚠️ Motivo de Rechazo</div>

                    <label class="rechazo-label">Motivos frecuentes (clic para seleccionar):</label>
                    <div class="motivos-rapidos">
                        <span class="motivo-chip"
                            onclick="agregarMotivo('Documentación incompleta o ilegible')">Documentación incompleta</span>
                        <span class="motivo-chip" onclick="agregarMotivo('Firma o sello no válido en diploma')">Firma/sello
                            inválido</span>
                        <span class="motivo-chip" onclick="agregarMotivo('Comprobante de pago vencido o incorrecto')">Pago
                            incorrecto</span>
                        <span class="motivo-chip"
                            onclick="agregarMotivo('Record académico no coincide con el nivel solicitado')">Record no
                            coincide</span>
                        <span class="motivo-chip"
                            onclick="agregarMotivo('Fotografías no cumplen los requisitos establecidos')">Fotografías
                            incorrectas</span>
                    </div>

                    <form action="procesar_rechazo.php" method="POST">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <label class="rechazo-label">Detalle el motivo del rechazo <span
                                style="color:#ef4444">*</span></label>
                        <textarea name="motivo" id="motivoTextarea" class="rechazo-textarea"
                            placeholder="Describa el motivo por el cual se rechaza este trámite..." required
                            maxlength="500"></textarea>
                        <div class="rechazo-hint">El estudiante recibirá este mensaje y deberá corregir su trámite.</div>

                        <div class="rechazo-actions">
                            <button type="submit" class="btn-confirmar-rechazo"
                                onclick="return confirm('¿Confirma el rechazo del trámite #<?= $id ?>?')">
                                ⚠️ Confirmar Rechazo
                            </button>
                            <button type="button" class="btn-cancelar-rechazo" onclick="ocultarRechazo()">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        <?php endif; ?>
    </div>

    <script>
        function mostrarRechazo() {
            document.getElementById('rechazoPanel').classList.add('visible');
            document.getElementById('motivoTextarea').focus();
        }
        function ocultarRechazo() {
            document.getElementById('rechazoPanel').classList.remove('visible');
            document.getElementById('motivoTextarea').value = '';
        }
        function agregarMotivo(texto) {
            const ta = document.getElementById('motivoTextarea');
            ta.value = ta.value ? ta.value + '. ' + texto : texto;
            ta.focus();
        }
    </script>
</body>

</html>