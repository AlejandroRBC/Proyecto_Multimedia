<?php
include "../helpers/auth.php";
verificarRol("ADMIN");
include "../helpers/json_helper.php";

$usuario = $_SESSION["usuario"];
$tramites = leerJson("../data/tramites.json");

$conteoEstado = [];
foreach ($tramites as $t) {
    $conteoEstado[$t["estado"]] = ($conteoEstado[$t["estado"]] ?? 0) + 1;
}

$filtro = $_GET["estado"] ?? "TODOS";
$tramitesFiltrados = $filtro === "TODOS"
    ? $tramites
    : array_values(array_filter($tramites, fn($t) => $t["estado"] === $filtro));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trámites – Admin UMSA</title>
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
            max-width: 1060px;
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

        /* Filtros chips */
        .filtros-bar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .filtro-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 7px 14px;
            border-radius: 20px;
            font-size: .78rem;
            font-weight: 700;
            text-decoration: none;
            border: 1.5px solid #e2e8f0;
            background: #fff;
            color: #475569;
            transition: all .18s;
            white-space: nowrap;
        }

        .filtro-chip:hover {
            border-color: #93c5fd;
            background: #eff6ff;
            color: #1d4ed8;
        }

        .filtro-chip.activo {
            background: #1e3a8a;
            border-color: #1e3a8a;
            color: #fff;
        }

        .chip-count {
            background: rgba(0, 0, 0, .12);
            border-radius: 10px;
            padding: 1px 6px;
            font-size: .7rem;
        }

        .filtro-chip.activo .chip-count {
            background: rgba(255, 255, 255, .25);
        }

        /* Tabla */
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

        .tabla-count {
            font-size: .8rem;
            color: #64748b;
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
            white-space: nowrap;
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
            padding: 12px 16px;
            font-size: .85rem;
            color: #374151;
            vertical-align: middle;
        }

        /* Estado badges */
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

        /* Modalidad */
        .modalidad-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 9px;
            border-radius: 8px;
            font-size: .7rem;
            font-weight: 600;
            background: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
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
        <div class="page-title">📄 Control de Trámites</div>
        <div class="page-sub">Supervisión general de todos los trámites de titulación</div>

        <!-- FILTROS POR ESTADO -->
        <div class="filtros-bar">
            <?php
            $iconos = [
                "TODOS" => "📋",
                "BORRADOR" => "✏️",
                "PENDIENTE_REVISION" => "⏳",
                "REVISION_DOCUMENTAL" => "🔎",
                "OBSERVADO" => "⚠️",
                "REVISION_AUTORIDAD" => "📜",
                "APROBADO" => "✅",
                "FIRMADO" => "✍️",
                "ENTREGADO" => "🎓"
            ];
            $labels = [
                "TODOS" => "Todos",
                "BORRADOR" => "Borrador",
                "PENDIENTE_REVISION" => "Pendiente",
                "REVISION_DOCUMENTAL" => "Rev. Documental",
                "OBSERVADO" => "Observado",
                "REVISION_AUTORIDAD" => "Rev. Autoridad",
                "APROBADO" => "Aprobado",
                "FIRMADO" => "Firmado",
                "ENTREGADO" => "Entregado"
            ];
            $estadosPresentes = array_unique(array_column($tramites, "estado"));
            $mostrar = array_merge(["TODOS"], $estadosPresentes);
            foreach ($mostrar as $e):
                $cnt = $e === "TODOS" ? count($tramites) : ($conteoEstado[$e] ?? 0);
                $activo = $filtro === $e ? " activo" : "";
                $icon = $iconos[$e] ?? "•";
                $label = $labels[$e] ?? $e;
                ?>
                <a href="?estado=<?= urlencode($e) ?>" class="filtro-chip<?= $activo ?>">
                    <?= $icon ?>     <?= $label ?> <span class="chip-count"><?= $cnt ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- TABLA -->
        <div class="tabla-wrap">
            <div class="tabla-header">
                <div>
                    <div class="tabla-title">Trámites registrados</div>
                    <div class="tabla-count"><?= count($tramitesFiltrados) ?> resultado(s)</div>
                </div>
                <input type="text" class="search-input" placeholder="🔍 Buscar..." oninput="filtrarTabla(this.value)">
            </div>
            <?php if (empty($tramitesFiltrados)): ?>
                <div class="empty-box">No hay trámites para este filtro.</div>
            <?php else: ?>
                <table id="tablaTramites">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Estudiante</th>
                            <th>Carrera</th>
                            <th>Nivel</th>
                            <th>Modalidad</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tramitesFiltrados as $t): ?>
                            <tr>
                                <td style="color:#94a3b8;font-weight:600;">#<?= $t["id"] ?></td>
                                <td style="font-weight:600;color:#1e293b;">
                                    <?= htmlspecialchars($t["nombreEstudiante"] ?? $t["estudiante"]) ?></td>
                                <td><?= htmlspecialchars($t["carrera"]) ?></td>
                                <td style="color:#64748b;"><?= htmlspecialchars($t["nivel"]) ?></td>
                                <td>
                                    <?php if (!empty($t["excelencia"])): ?>
                                        <span class="modalidad-badge">★ Excelencia</span>
                                    <?php else: ?>
                                        <span class="modalidad-badge"><?= htmlspecialchars($t["modalidadPago"] ?? "—") ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $est = $t["estado"];
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
                                    ?>
                                    <span class="estado-badge e-<?= $est ?>">
                                        <?= ($eIcons[$est] ?? "•") . " " . htmlspecialchars($est) ?>
                                    </span>
                                </td>
                                <td style="color:#94a3b8;font-size:.78rem;"><?= htmlspecialchars($t["fechaRegistro"] ?? "—") ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function filtrarTabla(q) {
            const filas = document.querySelectorAll('#tablaTramites tbody tr');
            q = q.toLowerCase();
            filas.forEach(f => {
                f.style.display = f.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        }
    </script>
</body>

</html>