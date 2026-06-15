<?php
include "../helpers/auth.php";
verificarRol("ESTUDIANTE");
include "../helpers/json_helper.php";

$id = (int) ($_GET["id"] ?? 0);
$tramites = leerJson("../data/tramites.json");
$historial = leerJson("../data/historial.json");
$observaciones = leerJson("../data/observaciones.json");
$tramite = null;
foreach ($tramites as $t) {
    if ($t["id"] == $id) {
        $tramite = $t;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seguimiento – Trámite #<?= $id ?></title>
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

        /* CONTENIDO */
        .contenido {
            max-width: 860px;
            margin: 0 auto;
            padding: 28px 24px;
        }

        .page-header {
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: #1e293b;
        }

        .page-sub {
            font-size: .85rem;
            color: #64748b;
            margin-top: 3px;
        }

        /* CARD ESTADO ACTUAL */
        .estado-card {
            background: #fff;
            border-radius: 16px;
            border: 1.5px solid #e2e8f0;
            padding: 22px 26px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .estado-icon {
            font-size: 2.4rem;
        }

        .estado-info {
            flex: 1;
        }

        .estado-label {
            font-size: .72rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .estado-valor {
            font-size: 1.25rem;
            font-weight: 800;
            color: #1e3a8a;
            margin-top: 2px;
        }

        .estado-meta {
            font-size: .82rem;
            color: #64748b;
            margin-top: 4px;
        }

        /* BADGE ESTADO */
        .estado-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: .78rem;
            font-weight: 700;
        }

        .e-BORRADOR {
            background: #fffbeb;
            color: #92400e;
            border: 1.5px solid #f6e05e;
        }

        .e-PENDIENTE_REVISION {
            background: #eff6ff;
            color: #1e40af;
            border: 1.5px solid #93c5fd;
        }

        .e-REVISION_DOCUMENTAL {
            background: #f5f3ff;
            color: #5b21b6;
            border: 1.5px solid #c4b5fd;
        }

        .e-OBSERVADO {
            background: #fef2f2;
            color: #b91c1c;
            border: 1.5px solid #fca5a5;
        }

        .e-APROBADO {
            background: #f0fdf4;
            color: #166534;
            border: 1.5px solid #86efac;
        }

        .e-EN_ELABORACION {
            background: #fff7ed;
            color: #c2410c;
            border: 1.5px solid #fed7aa;
        }

        .e-FIRMADO {
            background: #eff6ff;
            color: #1e3a8a;
            border: 1.5px solid #93c5fd;
        }

        .e-LISTO_PARA_ENTREGA {
            background: #f0fdf4;
            color: #065f46;
            border: 1.5px solid #6ee7b7;
        }

        .e-ENTREGADO {
            background: #1e3a8a;
            color: #fff;
            border: 1.5px solid #1e3a8a;
        }

        /* PANEL */
        .panel {
            background: #fff;
            border-radius: 16px;
            border: 1.5px solid #e2e8f0;
            padding: 20px 24px;
            margin-bottom: 20px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .04);
        }

        .panel-title {
            font-size: .92rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1.5px solid #f1f5f9;
            padding-bottom: 12px;
        }

        /* FLUJO BPM */
        .flujo-bpm {
            display: flex;
            flex-wrap: wrap;
            gap: 0;
            list-style: none;
            counter-reset: paso;
        }

        .flujo-bpm li {
            display: flex;
            align-items: center;
            gap: 0;
            font-size: .78rem;
            font-weight: 600;
            color: #94a3b8;
            position: relative;
        }

        .flujo-bpm li .paso-dot {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f1f5f9;
            border: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .75rem;
            font-weight: 800;
            color: #94a3b8;
            transition: all .25s;
            flex-shrink: 0;
            z-index: 1;
        }

        .flujo-bpm li .paso-label {
            display: none;
        }

        .flujo-bpm li::after {
            content: '';
            display: block;
            width: 28px;
            height: 2px;
            background: #e2e8f0;
            flex-shrink: 0;
        }

        .flujo-bpm li:last-child::after {
            display: none;
        }

        .flujo-bpm li.pasado .paso-dot {
            background: #dbeafe;
            border-color: #3b82f6;
            color: #1e40af;
        }

        .flujo-bpm li.pasado::after {
            background: #3b82f6;
        }

        .flujo-bpm li.activo .paso-dot {
            background: #1e3a8a;
            border-color: #1e3a8a;
            color: #fff;
            box-shadow: 0 0 0 4px rgba(30, 58, 138, .15);
            transform: scale(1.15);
        }

        .flujo-bpm li.observado .paso-dot {
            background: #fef2f2;
            border-color: #ef4444;
            color: #b91c1c;
        }

        /* Vista expandida del paso activo */
        .flujo-activo-label {
            margin-top: 14px;
            background: #eff6ff;
            border: 1.5px solid #bfdbfe;
            border-radius: 10px;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: .85rem;
            color: #1e40af;
            font-weight: 600;
        }

        .flujo-activo-label .dot-live {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #3b82f6;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: .5;
                transform: scale(1.4);
            }
        }

        /* HISTORIAL TABLE */
        .hist-table {
            width: 100%;
            border-collapse: collapse;
        }

        .hist-table thead th {
            background: #f8fafc;
            padding: 10px 14px;
            font-size: .7rem;
            font-weight: 700;
            color: #64748b;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: .05em;
            border-bottom: 1.5px solid #f1f5f9;
        }

        .hist-table tbody tr {
            border-bottom: 1px solid #f8fafc;
            transition: background .15s;
        }

        .hist-table tbody tr:last-child {
            border-bottom: none;
        }

        .hist-table tbody tr:hover {
            background: #f8fafc;
        }

        .hist-table td {
            padding: 10px 14px;
            font-size: .83rem;
            color: #374151;
            vertical-align: middle;
        }

        .hist-nota {
            color: #64748b;
        }

        .hist-fecha {
            color: #94a3b8;
            font-size: .75rem;
            white-space: nowrap;
        }

        /* OBSERVACIONES */
        .obs-card {
            background: #fef9ec;
            border: 1.5px solid #fde68a;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 10px;
        }

        .obs-fecha {
            font-size: .75rem;
            color: #92400e;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .obs-desc {
            font-size: .87rem;
            color: #78350f;
        }

        .btn-subsanar {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #d97706, #f59e0b);
            color: #fff;
            padding: 10px 22px;
            border-radius: 10px;
            font-size: .87rem;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 3px 10px rgba(217, 119, 6, .3);
            margin-top: 14px;
            transition: transform .15s;
        }

        .btn-subsanar:hover {
            transform: translateY(-1px);
        }

        .empty-text {
            color: #94a3b8;
            font-size: .87rem;
            padding: 8px 0;
        }

        .error-box {
            background: #fef2f2;
            border: 1.5px solid #fca5a5;
            border-radius: 12px;
            padding: 20px 24px;
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
            Sistema BPM – UMSA
        </div>
        <div class="topbar-right">
            <div class="topbar-user">
                <div class="topbar-avatar"><?= strtoupper(substr($_SESSION["usuario"]["nombre"], 0, 1)) ?></div>
                <?= htmlspecialchars($_SESSION["usuario"]["nombre"]) ?>
            </div>
            <a href="mis_tramites.php">← Mis trámites</a>
            <a href="../auth/logout.php">Cerrar sesión</a>
        </div>
    </div>

    <div class="contenido">

        <div class="page-header">
            <div class="page-title">🔍 Seguimiento de Trámite</div>
            <div class="page-sub">Trámite #<?= $id ?> — Estado actual y flujo de proceso</div>
        </div>

        <?php if (!$tramite): ?>
            <div class="error-box">⚠️ Trámite no encontrado. Verifique el número de trámite.</div>
        <?php else:
            $estadoActual = $tramite["estado"];
            $eIcons = [
                "BORRADOR" => "✏️",
                "PENDIENTE_REVISION" => "⏳",
                "REVISION_DOCUMENTAL" => "🔎",
                "OBSERVADO" => "⚠️",
                "APROBADO" => "✅",
                "EN_ELABORACION" => "⚙️",
                "FIRMADO" => "✍️",
                "LISTO_PARA_ENTREGA" => "📦",
                "ENTREGADO" => "🎓",
            ];
            $eLabels = [
                "BORRADOR" => "Borrador",
                "PENDIENTE_REVISION" => "Pendiente de Revisión",
                "REVISION_DOCUMENTAL" => "En Revisión Documental",
                "OBSERVADO" => "Observado",
                "APROBADO" => "Aprobado",
                "EN_ELABORACION" => "En Elaboración",
                "FIRMADO" => "Firmado",
                "LISTO_PARA_ENTREGA" => "Listo para Entrega",
                "ENTREGADO" => "Entregado",
            ];
            ?>

            <!-- ESTADO ACTUAL -->
            <div class="estado-card">
                <div class="estado-icon"><?= $eIcons[$estadoActual] ?? "📋" ?></div>
                <div class="estado-info">
                    <div class="estado-label">Estado actual</div>
                    <div class="estado-valor"><?= htmlspecialchars($eLabels[$estadoActual] ?? $estadoActual) ?></div>
                    <div class="estado-meta">
                        <?= htmlspecialchars($tramite["carrera"]) ?> &nbsp;·&nbsp;
                        <?= htmlspecialchars($tramite["nivel"]) ?>
                    </div>
                </div>
                <span class="estado-badge e-<?= htmlspecialchars($estadoActual) ?>">
                    <?= ($eIcons[$estadoActual] ?? "•") . " " . htmlspecialchars($estadoActual) ?>
                </span>
            </div>

            <!-- FLUJO BPM -->
            <?php
            $flujo_ordenado = [
                "BORRADOR",
                "PENDIENTE_REVISION",
                "REVISION_DOCUMENTAL",
                "APROBADO",
                "EN_ELABORACION",
                "FIRMADO",
                "LISTO_PARA_ENTREGA",
                "ENTREGADO"
            ];
            $flujoNums = [1, 2, 3, 4, 5, 6, 7, 8];
            $posActual = array_search($estadoActual, $flujo_ordenado);
            ?>
            <div class="panel">
                <div class="panel-title">🗺️ Flujo del Proceso</div>
                <ul class="flujo-bpm">
                    <?php foreach ($flujo_ordenado as $i => $e):
                        $clase = "";
                        if ($e === $estadoActual)
                            $clase = "activo";
                        elseif ($posActual !== false && $i < $posActual)
                            $clase = "pasado";
                        elseif ($estadoActual === "OBSERVADO" && $e === "REVISION_DOCUMENTAL")
                            $clase = "observado";
                        ?>
                        <li class="<?= $clase ?>">
                            <div class="paso-dot"><?= $i + 1 ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="flujo-activo-label">
                    <div class="dot-live"></div>
                    Paso <?= ($posActual !== false ? $posActual + 1 : "?") ?> de <?= count($flujo_ordenado) ?>
                    &nbsp;—&nbsp; <?= htmlspecialchars($eLabels[$estadoActual] ?? $estadoActual) ?>
                </div>
            </div>

            <!-- HISTORIAL -->
            <div class="panel">
                <div class="panel-title">📋 Historial de Actividad</div>
                <?php $hTramite = array_values(array_filter($historial, fn($h) => $h["tramite"] == $id)); ?>
                <?php if (empty($hTramite)): ?>
                    <p class="empty-text">Sin registros de actividad aún.</p>
                <?php else: ?>
                    <table class="hist-table">
                        <thead>
                            <tr>
                                <th>Estado</th>
                                <th>Nota</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_reverse($hTramite) as $h): ?>
                                <tr>
                                    <td>
                                        <span class="estado-badge e-<?= htmlspecialchars($h["estado"]) ?>">
                                            <?= ($eIcons[$h["estado"]] ?? "•") . " " . htmlspecialchars($h["estado"]) ?>
                                        </span>
                                    </td>
                                    <td class="hist-nota"><?= htmlspecialchars($h["nota"] ?? "—") ?></td>
                                    <td class="hist-fecha">🕐 <?= htmlspecialchars($h["fecha"]) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- OBSERVACIONES -->
            <?php $obsTramite = array_filter($observaciones, fn($o) => $o["tramite"] == $id); ?>
            <?php if (!empty($obsTramite)): ?>
                <div class="panel">
                    <div class="panel-title">⚠️ Observaciones del Funcionario</div>
                    <?php foreach ($obsTramite as $o): ?>
                        <div class="obs-card">
                            <div class="obs-fecha">📅 <?= htmlspecialchars($o["fecha"]) ?></div>
                            <div class="obs-desc"><?= htmlspecialchars($o["descripcion"]) ?></div>
                        </div>
                    <?php endforeach; ?>
                    <a href="corregir.php?id=<?= $id ?>" class="btn-subsanar">✏️ Subsanar observaciones</a>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</body>

</html>