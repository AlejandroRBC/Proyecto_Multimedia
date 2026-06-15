<?php
include "../helpers/auth.php";
verificarRol("ESTUDIANTE");
include "../helpers/json_helper.php";

$id       = (int)($_GET["id"] ?? 0);
$tramites = leerJson("../data/tramites.json");
$tramite  = null;
foreach ($tramites as $t) {
    if ($t["id"] == $id) { $tramite = $t; break; }
}
if (!$tramite) { header("Location: mis_tramites.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Documentos – Trámite #<?= $id ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; }
        .topbar {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            padding: 0 32px; height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 2px 12px rgba(0,0,0,.25);
            position: sticky; top: 0; z-index: 100;
        }
        .topbar-brand { display: flex; align-items: center; gap: 10px; color: #fff; font-size: 1rem; font-weight: 700; }
        .topbar-brand .logo-icon {
            width: 34px; height: 34px; background: rgba(255,255,255,.15);
            border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
        }
        .topbar-right { display: flex; align-items: center; gap: 16px; }
        .topbar-right a {
            color: rgba(255,255,255,.7); text-decoration: none; font-size: .82rem;
            padding: 6px 14px; border-radius: 6px; border: 1px solid rgba(255,255,255,.2); transition: background .2s;
        }
        .topbar-right a:hover { background: rgba(255,255,255,.1); color: #fff; }
        .contenido { max-width: 760px; margin: 0 auto; padding: 28px 24px; }
        .page-title { font-size: 1.4rem; font-weight: 800; color: #1e293b; margin-bottom: 4px; }
        .page-sub { font-size: .85rem; color: #64748b; margin-bottom: 24px; }
        .info-box {
            background: #eff6ff; border: 1.5px solid #bfdbfe;
            border-radius: 12px; padding: 14px 18px; margin-bottom: 22px;
            font-size: .85rem; color: #1e40af; display: flex; gap: 10px; align-items: flex-start;
        }
        .doc-section {
            background: #fff; border-radius: 16px;
            border: 1.5px solid #e2e8f0; margin-bottom: 12px;
            padding: 18px 22px; box-shadow: 0 1px 4px rgba(0,0,0,.04);
        }
        .doc-label {
            font-size: .88rem; font-weight: 700; color: #1e293b; margin-bottom: 10px;
            display: flex; align-items: center; gap: 8px;
        }
        .doc-label .req { font-size: .72rem; color: #b91c1c; font-weight: 600; }
        .doc-label .opt { font-size: .72rem; color: #64748b; font-weight: 600; }
        .doc-check-row {
            display: flex; align-items: center; gap: 10px; margin-bottom: 10px;
        }
        .doc-check-row input[type="checkbox"] { width: 18px; height: 18px; accent-color: #1e3a8a; flex-shrink: 0; }
        .doc-check-row label { font-size: .85rem; color: #374151; cursor: pointer; }
        .url-input-wrap { position: relative; }
        .url-input-wrap input[type="url"],
        .url-input-wrap input[type="text"] {
            width: 100%; padding: 10px 14px 10px 38px;
            border: 1.5px solid #d1d5db; border-radius: 10px;
            font-size: .88rem; color: #1a202c; background: #f9fafb;
            transition: border-color .2s;
        }
        .url-input-wrap input:focus { outline: none; border-color: #1e3a8a; background: #fff; }
        .url-input-wrap .url-icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            font-size: .95rem; pointer-events: none;
        }
        .url-hint { font-size: .75rem; color: #94a3b8; margin-top: 5px; }
        .doc-section.has-link { border-color: #86efac; background: #f0fdf4; }
        .doc-section.has-link .doc-label { color: #166534; }
        .btn-row { display: flex; gap: 12px; margin-top: 24px; align-items: center; flex-wrap: wrap; }
        .btn-primary {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: white; padding: 12px 28px; border: none;
            border-radius: 10px; font-size: .95rem; font-weight: 700; cursor: pointer;
            box-shadow: 0 4px 12px rgba(37,99,235,.3); transition: transform .15s;
        }
        .btn-primary:hover { transform: translateY(-1px); }
        .btn-cancel {
            background: #f1f5f9; color: #475569; padding: 12px 22px;
            border: 1.5px solid #e2e8f0; border-radius: 10px;
            font-size: .9rem; font-weight: 600; text-decoration: none;
        }
        .docs-grid { display: flex; flex-direction: column; gap: 10px; }
    </style>
</head>
<body>
<div class="topbar">
    <div class="topbar-brand">
        <div class="logo-icon">🎓</div>
        Sistema BPM – UMSA
    </div>
    <div class="topbar-right">
        <a href="mis_tramites.php">← Mis trámites</a>
        <a href="../auth/logout.php">Cerrar sesión</a>
    </div>
</div>

<div class="contenido">
    <div class="page-title">📂 Documentos – Trámite #<?= $id ?></div>
    <div class="page-sub">Ingrese el enlace (URL) de cada documento requerido. Puede usar Google Drive, Dropbox u otro servicio de almacenamiento en la nube.</div>

    <div class="info-box">
        ℹ️ <span>Comparta el enlace de cada documento. Asegúrese de que el enlace sea <strong>público</strong> o accesible para revisión. Marque el checkbox para confirmar que el documento está disponible.</span>
    </div>

    <form action="guardar_documentos.php" method="POST">
        <input type="hidden" name="id" value="<?= $id ?>">

        <div class="docs-grid">
            <?php
            $docs = [
                "diplomaAcademico"      => ["📜", "Diploma Académico", true],
                "certificadoNacimiento" => ["🪪", "Certificado de Nacimiento", true],
                "cedula"                => ["🆔", "Cédula de Identidad", true],
                "fotografias"           => ["📷", "Fotografías", true],
                "comprobantePago"       => ["💳", "Comprobante de Pago", false],
                "informeDecanato"       => ["🏫", "Informe de Decanato", false],
                "servicioRural"         => ["🏥", "Servicio Rural (si aplica)", false],
                "recordAcademico"       => ["📋", "Record Académico", true],
            ];
            foreach ($docs as $key => [$icon, $label, $requerido]):
                $linkActual = $tramite["links"][$key] ?? $tramite["archivos"][$key] ?? "";
                $tieneLink  = !empty($linkActual);
            ?>
            <div class="doc-section <?= $tieneLink ? 'has-link' : '' ?>" id="section-<?= $key ?>">
                <div class="doc-label">
                    <?= $icon ?> <?= htmlspecialchars($label) ?>
                    <?php if ($requerido): ?>
                        <span class="req">● Requerido</span>
                    <?php else: ?>
                        <span class="opt">○ Opcional</span>
                    <?php endif; ?>
                </div>
                <div class="doc-check-row">
                    <input type="checkbox" name="doc_<?= $key ?>" id="chk-<?= $key ?>"
                           value="1" <?= $tieneLink ? 'checked' : '' ?>
                           onchange="toggleLink('<?= $key ?>', this.checked)">
                    <label for="chk-<?= $key ?>">Documento disponible / adjuntado</label>
                </div>
                <div class="url-input-wrap" id="url-wrap-<?= $key ?>" style="<?= !$tieneLink ? 'display:none' : '' ?>">
                    <span class="url-icon">🔗</span>
                    <input type="url" name="link_<?= $key ?>" id="url-<?= $key ?>"
                           placeholder="https://drive.google.com/..."
                           value="<?= htmlspecialchars($linkActual) ?>"
                           <?= ($requerido) ? '' : '' ?>>
                    <div class="url-hint">Pegue el enlace del documento (Google Drive, Dropbox, OneDrive, etc.)</div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="btn-row">
            <button type="submit" class="btn-primary">✅ Guardar y Enviar a Revisión</button>
            <a href="mis_tramites.php" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>

<script>
function toggleLink(key, checked) {
    const wrap = document.getElementById('url-wrap-' + key);
    const section = document.getElementById('section-' + key);
    if (checked) {
        wrap.style.display = 'block';
        section.classList.add('has-link');
    } else {
        wrap.style.display = 'none';
        section.classList.remove('has-link');
        document.getElementById('url-' + key).value = '';
    }
}
</script>
</body>
</html>