<?php
include "../helpers/auth.php";
verificarRol("FUNCIONARIO");
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

if ($tramite && $tramite["estado"] === "PENDIENTE_REVISION") {
    foreach ($tramites as &$t) {
        if ($t["id"] == $id) {
            $t["estado"] = "REVISION_DOCUMENTAL";
            $tramite = $t;
            break;
        }
    }
    guardarJson("../data/tramites.json", $tramites);
    registrarHistorial("../data/historial.json", $id, "REVISION_DOCUMENTAL", "Funcionario inició revisión documental");
}

$historialTramite = array_values(array_filter($historial, fn($h) => $h["tramite"] == $id));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Revisión – Trámite #<?= $id ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f4f8;
        }

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

        .contenido {
            max-width: 860px;
            margin: 0 auto;
            padding: 28px 24px;
        }

        .page-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 20px;
        }

        .panel {
            background: #fff;
            border-radius: 16px;
            border: 1.5px solid #e2e8f0;
            padding: 22px 26px;
            margin-bottom: 16px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .04);
        }

        .panel h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 1.5px solid #f1f5f9;
        }

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
        }

        .badge-excelencia {
            display: inline-block;
            background: #f0fff4;
            color: #166534;
            border: 1.5px solid #86efac;
            border-radius: 20px;
            padding: 3px 12px;
            font-size: .75rem;
            font-weight: 700;
        }

        /* Documentos con links */
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

        .doc-status {
            font-size: 1rem;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .doc-info {
            flex: 1;
            min-width: 180px;
        }

        .doc-name {
            font-size: .88rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .doc-link-area {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 6px;
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

        .link-status {
            font-size: .75rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .link-ok {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #86efac;
        }

        .link-error {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        .link-pending {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .link-preview-container {
            width: 100%;
            margin-top: 10px;
            display: none;
        }

        .link-preview-container iframe {
            width: 100%;
            height: 320px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            background: #fff;
        }

        /* Historial */
        .historial-timeline {
            display: flex;
            flex-direction: column;
            gap: 0;
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

        .historial-content {
            flex: 1;
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

        /* Acciones */
        .acciones-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-aprobar {
            background: linear-gradient(135deg, #166534, #16a34a);
            color: #fff;
            padding: 11px 24px;
            border: none;
            border-radius: 10px;
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            box-shadow: 0 4px 12px rgba(22, 163, 74, .3);
            transition: transform .15s;
        }

        .btn-aprobar:hover {
            transform: translateY(-1px);
        }

        textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #d1d5db;
            border-radius: 10px;
            font-size: .9rem;
            resize: vertical;
            font-family: inherit;
            margin-top: 8px;
            background: #f9fafb;
        }

        textarea:focus {
            outline: none;
            border-color: #1e3a8a;
            background: #fff;
        }

        .btn-observar {
            background: linear-gradient(135deg, #b91c1c, #dc2626);
            color: #fff;
            padding: 11px 24px;
            border: none;
            border-radius: 10px;
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            box-shadow: 0 4px 12px rgba(185, 28, 28, .3);
            transition: transform .15s;
        }

        .btn-observar:hover {
            transform: translateY(-1px);
        }

        .label-form {
            font-size: .85rem;
            font-weight: 600;
            color: #374151;
            margin-top: 14px;
            display: block;
        }
    </style>
</head>

<body>
    <div class="topbar">
        <div class="topbar-brand">
            <div class="logo-icon">🎓</div>
            Sistema BPM – Funcionario
        </div>
        <div class="topbar-right">
            <a href="bandeja.php">← Bandeja</a>
            <a href="../auth/logout.php">Cerrar sesión</a>
        </div>
    </div>

    <div class="contenido">
        <div class="page-title">🔎 Revisión Documental – Trámite #<?= $id ?></div>

        <?php if (!$tramite): ?>
            <div style="background:#fef2f2;border:1.5px solid #fca5a5;border-radius:12px;padding:16px;color:#b91c1c;">
                ⚠️ Trámite no encontrado.
            </div>
        <?php else: ?>

            <!-- DATOS DEL SOLICITANTE -->
            <div class="panel">
                <h3>👤 Datos del Solicitante</h3>
                <div class="info-grid">
                    <div class="info-item"><strong>Estudiante:</strong>
                        <?= htmlspecialchars($tramite["nombreEstudiante"] ?? $tramite["estudiante"]) ?></div>
                    <div class="info-item"><strong>Carrera:</strong> <?= htmlspecialchars($tramite["carrera"]) ?></div>
                    <div class="info-item"><strong>Nivel:</strong> <?= htmlspecialchars($tramite["nivel"]) ?></div>
                    <div class="info-item"><strong>Modalidad:</strong>
                        <?= htmlspecialchars($tramite["modalidadPago"] ?? "—") ?></div>
                    <div class="info-item"><strong>Estado:</strong> <?= htmlspecialchars($tramite["estado"]) ?></div>
                    <div class="info-item"><strong>Fecha registro:</strong>
                        <?= htmlspecialchars($tramite["fechaRegistro"] ?? "—") ?></div>
                </div>
                <?php if (!empty($tramite["excelencia"])): ?>
                    <div style="margin-top:12px"><span class="badge-excelencia">★ Excelencia Académica</span></div>
                <?php endif; ?>
            </div>

            <!-- DOCUMENTOS CON LINKS -->
            <div class="panel">
                <h3>📂 Documentos Presentados</h3>
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
                    <div class="doc-row <?= $rowClass ?>" id="doc-row-<?= $key ?>">
                        <div class="doc-status"><?= $marcado ? "✅" : "❌" ?></div>
                        <div class="doc-info">
                            <div class="doc-name"><?= $nombre ?></div>
                            <?php if ($marcado && $tieneLink): ?>
                                <div class="doc-link-area">
                                    <a href="<?= htmlspecialchars($link) ?>" target="_blank" class="doc-link-btn">
                                        🔗 Abrir enlace
                                    </a>
                                    <button type="button" class="doc-link-btn"
                                        style="cursor:pointer;background:#f0fdf4;color:#166534;border-color:#86efac;"
                                        onclick="verificarLink('<?= $key ?>', '<?= htmlspecialchars($link, ENT_QUOTES) ?>')">
                                        🔍 Verificar
                                    </button>
                                    <span class="link-status link-pending" id="status-<?= $key ?>">⏳ No verificado</span>
                                </div>
                                <div class="link-preview-container" id="preview-<?= $key ?>">
                                    <iframe id="iframe-<?= $key ?>" src=""
                                        sandbox="allow-same-origin allow-scripts allow-popups"></iframe>
                                </div>
                            <?php elseif ($marcado && !$tieneLink): ?>
                                <div class="doc-link-area">
                                    <span class="link-status link-error">⚠️ Sin enlace proporcionado</span>
                                </div>
                            <?php else: ?>
                                <div class="doc-link-area">
                                    <span class="link-status link-error">✘ No presentado</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- HISTORIAL DEL PROCESO -->
            <div class="panel">
                <h3>📋 Historial del Proceso</h3>
                <?php if (empty($historialTramite)): ?>
                    <p style="color:#94a3b8;font-size:.85rem;">Sin registros aún.</p>
                <?php else: ?>
                    <div class="historial-timeline">
                        <?php foreach (array_reverse($historialTramite) as $h): ?>
                            <div class="historial-item">
                                <div class="historial-dot"></div>
                                <div class="historial-content">
                                    <div class="historial-estado"><?= htmlspecialchars($h["estado"]) ?></div>
                                    <div class="historial-nota"><?= htmlspecialchars($h["nota"] ?? "") ?></div>
                                    <div class="historial-fecha">🕐 <?= htmlspecialchars($h["fecha"]) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ACCIONES BPM -->
            <div class="panel">
                <h3>✅ Aprobar Trámite</h3>
                <p style="font-size:.85rem;color:#64748b;margin-bottom:14px;">
                    Si todos los documentos están correctos, apruebe el trámite para enviarlo a la autoridad.
                </p>
                <a href="aprobar.php?id=<?= $id ?>" class="btn-aprobar"
                    onclick="return confirm('¿Confirma que aprueba el trámite #<?= $id ?>?')">
                    ✔ Aprobar y Enviar a Autoridad
                </a>
            </div>

            <div class="panel">
                <h3>⚠️ Enviar Observación</h3>
                <form action="observar.php" method="POST">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <span class="label-form">Descripción de la observación</span>
                    <textarea name="observacion" rows="3"
                        placeholder="Ej: Falta legalización del Diploma Académico. El link del certificado de nacimiento no es accesible..."
                        required></textarea>
                    <div style="margin-top:12px;">
                        <button type="submit" class="btn-observar"
                            onclick="return confirm('¿Enviar observación al estudiante?')">
                            ✘ Enviar Observación
                        </button>
                    </div>
                </form>
            </div>

        <?php endif; ?>
    </div>

    <script>

        function verificarLink(key, url) {
            const statusEl = document.getElementById('status-' + key);
            const previewEl = document.getElementById('preview-' + key);
            const iframeEl = document.getElementById('iframe-' + key);

            statusEl.className = 'link-status link-pending';
            statusEl.textContent = '⏳ Verificando...';


            iframeEl.onload = function () {
                try {

                    statusEl.className = 'link-status link-ok';
                    statusEl.textContent = '✅ Enlace válido';
                } catch (e) {
                    statusEl.className = 'link-status link-ok';
                    statusEl.textContent = '✅ Enlace activo';
                }
            };
            iframeEl.onerror = function () {
                statusEl.className = 'link-status link-error';
                statusEl.textContent = '❌ Link corrupto o inexistente';
            };


            fetch(url, { method: 'HEAD', mode: 'no-cors' })
                .then(() => {
                    statusEl.className = 'link-status link-ok';
                    statusEl.textContent = '✅ Enlace válido';

                    previewEl.style.display = 'block';
                    iframeEl.src = url;
                })
                .catch(() => {
                    statusEl.className = 'link-status link-error';
                    statusEl.textContent = '❌ Link corrupto o inexistente';
                    previewEl.style.display = 'none';
                });
        }
    </script>
</body>

</html>