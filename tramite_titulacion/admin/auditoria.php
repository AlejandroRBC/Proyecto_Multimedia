<?php
include "../helpers/auth.php";
verificarRol("ADMIN");
include "../helpers/json_helper.php";

$usuario = $_SESSION["usuario"];
$historial = leerJson("../data/historial.json");
$historial = array_reverse($historial);

$conteoEstado = [];
foreach ($historial as $h) {
    $conteoEstado[$h["estado"]] = ($conteoEstado[$h["estado"]] ?? 0) + 1;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Auditoría – Admin UMSA</title>
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

        .contenido {
            max-width: 1000px;
            margin: 0 auto;
            padding: 28px 24px;
        }

        .page-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 3px;
        }

        .page-sub {
            font-size: .85rem;
            color: #64748b;
            margin-bottom: 20px;
        }

        .stats-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .stat-pill {
            background: #fff;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .04);
            font-size: .82rem;
            white-space: nowrap;
        }

        .stat-pill strong {
            font-size: 1.1rem;
            font-weight: 800;
            color: #1e40af;
        }

        .tabla-wrap {
            background: #fff;
            border-radius: 16px;
            border: 1.5px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .05);
        }

        .tabla-header {
            padding: 16px 22px;
            border-bottom: 1.5px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .tabla-title {
            font-size: .95rem;
            font-weight: 700;
            color: #1e293b;
        }

        .search-input {
            padding: 8px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 9px;
            font-size: .83rem;
            outline: none;
            width: 220px;
            transition: border-color .2s;
        }

        .search-input:focus {
            border-color: #1e3a8a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #f8fafc;
            padding: 12px 16px;
            font-size: .72rem;
            font-weight: 700;
            color: #64748b;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: .05em;
            border-bottom: 1.5px solid #f1f5f9;
        }

        tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background .15s;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        td {
            padding: 11px 16px;
            font-size: .85rem;
            color: #374151;
            vertical-align: middle;
        }

        .tramite-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 24px;
            border-radius: 8px;
            background: #eff6ff;
            color: #1e40af;
            font-size: .75rem;
            font-weight: 700;
            border: 1px solid #bfdbfe;
        }

        .estado-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .7rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .e-BORRADOR {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #f6e05e;
        }

        .e-PENDIENTE_REVISION {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .e-REVISION_DOCUMENTAL {
            background: #f5f3ff;
            color: #5b21b6;
            border: 1px solid #c4b5fd;
        }

        .e-OBSERVADO {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        .e-REVISION_AUTORIDAD {
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #fed7aa;
        }

        .e-APROBADO {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #86efac;
        }

        .e-FIRMADO {
            background: #eff6ff;
            color: #1e3a8a;
            border: 1px solid #93c5fd;
        }

        .e-ENTREGADO {
            background: #1e3a8a;
            color: #fff;
            border: 1px solid #1e3a8a;
        }

        .nota-text {
            color: #64748b;
            font-size: .82rem;
        }

        .fecha-text {
            color: #94a3b8;
            font-size: .78rem;
            white-space: nowrap;
        }

        .empty-box {
            padding: 48px;
            text-align: center;
            color: #94a3b8;
            font-size: .9rem;
        }
    </style>
</head>

<body>
    <div class="topbar">
        <div class="topbar-brand">
            <div class="logo-icon">🎓</div>
            Sistema BPM – UMSA
        </div>
        <div class="topbar-right">
            <div class="topbar-user">
                <div class="topbar-avatar"><?= strtoupper(substr($usuario["nombre"], 0, 1)) ?></div>
                <?= htmlspecialchars($usuario["nombre"]) ?>
            </div>
            <a href="dashboard.php">← Dashboard</a>
            <a href="../auth/logout.php">Cerrar sesión</a>
        </div>
    </div>

    <div class="contenido">
        <div class="page-title">📊 Auditoría del Sistema</div>
        <div class="page-sub">Historial completo de eventos y cambios de estado del sistema BPM</div>

        <!-- STATS -->
        <div class="stats-row">
            <div class="stat-pill">
                📋 Total eventos <strong><?= count($historial) ?></strong>
            </div>
            <?php foreach ($conteoEstado as $est => $cnt): ?>
                <div class="stat-pill">
                    <?= htmlspecialchars($est) ?> <strong><?= $cnt ?></strong>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- TABLA -->
        <div class="tabla-wrap">
            <div class="tabla-header">
                <div class="tabla-title">Registro de eventos</div>
                <input type="text" class="search-input" placeholder="🔍 Buscar evento..."
                    oninput="filtrarTabla(this.value)">
            </div>
            <?php if (empty($historial)): ?>
                <div class="empty-box">No hay registros de auditoría aún.</div>
            <?php else: ?>
                <table id="tablaAuditoria">
                    <thead>
                        <tr>
                            <th>Trámite</th>
                            <th>Estado</th>
                            <th>Nota</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $eIcons = [
                            "BORRADOR" => "✏️",
                            "PENDIENTE_REVISION" => "⏳",
                            "REVISION_DOCUMENTAL" => "🔎",
                            "OBSERVADO" => "⚠️",
                            "REVISION_AUTORIDAD" => "📜",
                            "APROBADO" => "✅",
                            "FIRMADO" => "✍️",
                            "ENTREGADO" => "🎓"
                        ];
                        foreach ($historial as $h):
                            $est = $h["estado"];
                            ?>
                            <tr>
                                <td><span class="tramite-pill">#<?= $h["tramite"] ?></span></td>
                                <td>
                                    <span class="estado-badge e-<?= htmlspecialchars($est) ?>">
                                        <?= ($eIcons[$est] ?? "•") . " " . htmlspecialchars($est) ?>
                                    </span>
                                </td>
                                <td class="nota-text"><?= htmlspecialchars($h["nota"] ?? "—") ?></td>
                                <td class="fecha-text">🕐 <?= htmlspecialchars($h["fecha"]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function filtrarTabla(q) {
            const filas = document.querySelectorAll('#tablaAuditoria tbody tr');
            q = q.toLowerCase();
            filas.forEach(f => {
                f.style.display = f.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        }
    </script>
</body>

</html>