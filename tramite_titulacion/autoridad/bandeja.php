<?php
include "../helpers/auth.php";
verificarRol("AUTORIDAD");
include "../helpers/json_helper.php";

$usuario = $_SESSION["usuario"];
$tramites = leerJson("../data/tramites.json");

$paraFirmar = array_values(array_filter($tramites, fn($t) => $t["estado"] === "REVISION_AUTORIDAD"));
$firmados = array_values(array_filter($tramites, fn($t) => $t["estado"] === "FIRMADO"));
$entregados = array_values(array_filter($tramites, fn($t) => $t["estado"] === "ENTREGADO"));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bandeja Autoridad – UMSA</title>
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
            max-width: 960px;
            margin: 0 auto;
            padding: 28px 24px;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .page-sub {
            font-size: .85rem;
            color: #64748b;
            margin-bottom: 22px;
        }

        /* Stats */
        .stats-row {
            display: flex;
            gap: 12px;
            margin-bottom: 26px;
            flex-wrap: wrap;
        }

        .stat-card {
            flex: 1;
            min-width: 130px;
            background: #fff;
            border-radius: 14px;
            border: 1.5px solid #e2e8f0;
            padding: 16px 20px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .04);
        }

        .stat-num {
            font-size: 1.8rem;
            font-weight: 800;
        }

        .stat-label {
            font-size: .75rem;
            color: #64748b;
            font-weight: 600;
            margin-top: 2px;
        }

        .stat-firmar .stat-num {
            color: #1e40af;
        }

        .stat-firmados .stat-num {
            color: #7c3aed;
        }

        .stat-entregados .stat-num {
            color: #166534;
        }

        /* Section */
        .section-title {
            font-size: .95rem;
            font-weight: 700;
            color: #1e293b;
            margin: 22px 0 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Cards trámite */
        .tramite-card {
            background: #fff;
            border-radius: 14px;
            border: 1.5px solid #e2e8f0;
            padding: 16px 22px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .04);
            transition: box-shadow .2s, border-color .2s;
        }

        .tramite-card:hover {
            box-shadow: 0 4px 16px rgba(30, 58, 138, .1);
            border-color: #bfdbfe;
        }

        .tramite-card.card-firma {
            border-color: #bfdbfe;
        }

        .tramite-card.card-firmado {
            border-color: #c4b5fd;
        }

        .tramite-card.card-entregado {
            border-color: #86efac;
            background: #f0fdf4;
        }

        .tramite-card-left {
            flex: 1;
            min-width: 180px;
        }

        .tramite-id {
            font-size: .72rem;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .tramite-nombre {
            font-size: .95rem;
            font-weight: 700;
            color: #1e3a8a;
        }

        .tramite-carrera {
            font-size: .82rem;
            color: #64748b;
            margin-top: 2px;
        }

        .tramite-fecha {
            font-size: .72rem;
            color: #94a3b8;
            margin-top: 3px;
        }

        /* Badges */
        .estado-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: .73rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-autoridad {
            background: #eff6ff;
            color: #1e40af;
            border: 1.5px solid #93c5fd;
        }

        .badge-firmado {
            background: #f5f3ff;
            color: #5b21b6;
            border: 1.5px solid #c4b5fd;
        }

        .badge-entregado {
            background: #f0fdf4;
            color: #166534;
            border: 1.5px solid #86efac;
        }

        /* Acciones */
        .acciones {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .btn-accion {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 9px;
            font-size: .82rem;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: transform .15s, box-shadow .15s;
        }

        .btn-accion:hover {
            transform: translateY(-1px);
        }

        .btn-ver-docs {
            background: #eff6ff;
            color: #1e40af;
            border: 1.5px solid #bfdbfe;
        }

        .btn-firmar {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: #fff;
            box-shadow: 0 3px 10px rgba(37, 99, 235, .25);
        }

        .btn-entregar {
            background: linear-gradient(135deg, #5b21b6, #7c3aed);
            color: #fff;
            box-shadow: 0 3px 10px rgba(124, 58, 237, .25);
        }

        /* Empty */
        .empty-box {
            background: #fff;
            border-radius: 14px;
            border: 2px dashed #e2e8f0;
            padding: 28px;
            text-align: center;
            color: #94a3b8;
            font-size: .88rem;
        }
    </style>
</head>

<body>
    <div class="topbar">
        <div class="topbar-brand">
            <div class="logo-icon">🎓</div>
            Sistema BPM – Autoridad
        </div>
        <div class="topbar-right">
            <div class="topbar-user">
                <div class="topbar-avatar"><?= strtoupper(substr($usuario["nombre"], 0, 1)) ?></div>
                <?= htmlspecialchars($usuario["nombre"]) ?>
            </div>
            <a href="dashboard.php">Inicio</a>
            <a href="../auth/logout.php">Cerrar sesión</a>
        </div>
    </div>

    <div class="contenido">
        <div class="page-title">📜 Trámites – Firma y Entrega</div>
        <div class="page-sub">Revise los documentos, firme y entregue los títulos profesionales.</div>

        <!-- ESTADÍSTICAS -->
        <div class="stats-row">
            <div class="stat-card stat-firmar">
                <div class="stat-num"><?= count($paraFirmar) ?></div>
                <div class="stat-label">✍️ Pendientes de firma</div>
            </div>
            <div class="stat-card stat-firmados">
                <div class="stat-num"><?= count($firmados) ?></div>
                <div class="stat-label">📜 Firmados / Pendiente entrega</div>
            </div>
            <div class="stat-card stat-entregados">
                <div class="stat-num"><?= count($entregados) ?></div>
                <div class="stat-label">🎓 Entregados</div>
            </div>
        </div>

        <!-- PARA FIRMAR -->
        <div class="section-title">✍️ Pendientes de Firma</div>
        <?php if (empty($paraFirmar)): ?>
            <div class="empty-box">No hay trámites pendientes de firma en este momento.</div>
        <?php else: ?>
            <?php foreach ($paraFirmar as $t): ?>
                <div class="tramite-card card-firma">
                    <div class="tramite-card-left">
                        <div class="tramite-id">Trámite #<?= $t["id"] ?></div>
                        <div class="tramite-nombre"><?= htmlspecialchars($t["nombreEstudiante"] ?? $t["estudiante"]) ?></div>
                        <div class="tramite-carrera"><?= htmlspecialchars($t["carrera"]) ?> —
                            <?= htmlspecialchars($t["nivel"]) ?></div>
                        <div class="tramite-fecha">📅 <?= htmlspecialchars($t["fechaRegistro"] ?? "") ?></div>
                    </div>
                    <span class="estado-badge badge-autoridad">📜 PARA FIRMA</span>
                    <div class="acciones">
                        <a href="firmar.php?id=<?= $t["id"] ?>" class="btn-accion btn-ver-docs">👁 Ver docs y firmar</a>
                        <a href="procesar_firma.php?id=<?= $t["id"] ?>" class="btn-accion btn-firmar"
                            onclick="return confirm('¿Confirma la firma del trámite #<?= $t["id"] ?>?')">
                            ✍️ Firmar
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- FIRMADOS / PENDIENTE ENTREGA -->
        <?php if (!empty($firmados)): ?>
            <div class="section-title">📦 Firmados – Pendiente de Entrega</div>
            <?php foreach ($firmados as $t): ?>
                <div class="tramite-card card-firmado">
                    <div class="tramite-card-left">
                        <div class="tramite-id">Trámite #<?= $t["id"] ?></div>
                        <div class="tramite-nombre"><?= htmlspecialchars($t["nombreEstudiante"] ?? $t["estudiante"]) ?></div>
                        <div class="tramite-carrera"><?= htmlspecialchars($t["carrera"]) ?> —
                            <?= htmlspecialchars($t["nivel"]) ?></div>
                        <div class="tramite-fecha">✍️ Firmado: <?= htmlspecialchars($t["fechaFirma"] ?? "—") ?></div>
                    </div>
                    <span class="estado-badge badge-firmado">✍️ FIRMADO</span>
                    <div class="acciones">
                        <a href="entregar.php?id=<?= $t["id"] ?>" class="btn-accion btn-entregar"
                            onclick="return confirm('¿Confirma la entrega del título al graduado #<?= $t["id"] ?>?')">
                            🎓 Entregar Título
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- ENTREGADOS (últimos 5) -->
        <?php if (!empty($entregados)): ?>
            <div class="section-title">🎓 Títulos Entregados Recientemente</div>
            <?php foreach (array_slice(array_reverse($entregados), 0, 5) as $t): ?>
                <div class="tramite-card card-entregado">
                    <div class="tramite-card-left">
                        <div class="tramite-id">Trámite #<?= $t["id"] ?></div>
                        <div class="tramite-nombre"><?= htmlspecialchars($t["nombreEstudiante"] ?? $t["estudiante"]) ?></div>
                        <div class="tramite-carrera"><?= htmlspecialchars($t["carrera"]) ?> —
                            <?= htmlspecialchars($t["nivel"]) ?></div>
                        <div class="tramite-fecha">🎓 Entregado: <?= htmlspecialchars($t["fechaEntrega"] ?? "—") ?></div>
                    </div>
                    <span class="estado-badge badge-entregado">🎓 ENTREGADO</span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>

</html>