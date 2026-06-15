<?php
include "../helpers/auth.php";
verificarRol("ESTUDIANTE");
include "../helpers/json_helper.php";

$usuario = $_SESSION["usuario"];
$tramites = leerJson("../data/tramites.json");
$misTramites = array_values(array_filter($tramites, fn($t) => $t["estudiante"] == $usuario["id"]));

$total = count($misTramites);
$borradores = count(array_filter($misTramites, fn($t) => $t["estado"] === "BORRADOR"));
$revision = count(array_filter($misTramites, fn($t) => in_array($t["estado"], ["PENDIENTE_REVISION", "REVISION_DOCUMENTAL"])));
$observados = count(array_filter($misTramites, fn($t) => $t["estado"] === "OBSERVADO"));
$entregados = count(array_filter($misTramites, fn($t) => $t["estado"] === "ENTREGADO"));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inicio – UMSA Titulación</title>
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
            min-height: 100vh;
        }

        /* ── TOPBAR ── */
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
            letter-spacing: .01em;
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
            gap: 20px;
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

        /* ── HERO BIENVENIDA ── */
        .hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 60%, #2563eb 100%);
            padding: 40px 32px 48px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .hero::after {
            content: '🎓';
            position: absolute;
            right: 40px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 6rem;
            opacity: .12;
            pointer-events: none;
        }

        .hero-greeting {
            font-size: .85rem;
            opacity: .75;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .hero-name {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .hero-sub {
            font-size: .9rem;
            opacity: .7;
        }

        /* ── CONTENIDO ── */
        .contenido {
            max-width: 960px;
            margin: 0 auto;
            padding: 28px 24px;
        }

        /* ── STATS ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 14px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .06);
            border: 1.5px solid #e2e8f0;
            transition: box-shadow .2s, transform .2s;
            text-decoration: none;
        }

        .stat-card:hover {
            box-shadow: 0 4px 18px rgba(30, 58, 138, .12);
            transform: translateY(-2px);
            border-color: #bfdbfe;
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .stat-info {
            flex: 1;
            min-width: 0;
        }

        .stat-number {
            font-size: 1.6rem;
            font-weight: 800;
            color: #1e293b;
            line-height: 1;
        }

        .stat-label {
            font-size: .75rem;
            color: #64748b;
            margin-top: 3px;
        }

        .si-blue {
            background: #dbeafe;
        }

        .si-amber {
            background: #fef3c7;
        }

        .si-red {
            background: #fee2e2;
        }

        .si-green {
            background: #dcfce7;
        }

        .si-total {
            background: #f1f5f9;
        }

        /* ── ACCIONES ── */
        .section-title {
            font-size: .8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
            margin-bottom: 14px;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .action-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px 22px;
            text-decoration: none;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .05);
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: all .2s;
        }

        .action-card:hover {
            box-shadow: 0 6px 22px rgba(30, 58, 138, .13);
            border-color: #93c5fd;
            transform: translateY(-2px);
        }

        .action-card.primary {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            border-color: transparent;
            color: #fff;
        }

        .action-card.primary:hover {
            box-shadow: 0 6px 22px rgba(37, 99, 235, .35);
            border-color: transparent;
        }

        .action-icon {
            font-size: 1.8rem;
        }

        .action-title {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
        }

        .action-card.primary .action-title {
            color: #fff;
        }

        .action-desc {
            font-size: .8rem;
            color: #64748b;
            line-height: 1.4;
        }

        .action-card.primary .action-desc {
            color: rgba(255, 255, 255, .7);
        }

        .action-arrow {
            font-size: .8rem;
            color: #94a3b8;
            margin-top: 4px;
        }

        .action-card.primary .action-arrow {
            color: rgba(255, 255, 255, .6);
        }

        /* ── BADGE ALERTA ── */
        .badge-alert {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #fee2e2;
            color: #b91c1c;
            border-radius: 20px;
            padding: 2px 10px;
            font-size: .72rem;
            font-weight: 700;
            margin-left: 6px;
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
                <div class="topbar-avatar"><?= strtoupper(substr($usuario["nombre"], 0, 1)) ?></div>
                <?= htmlspecialchars($usuario["nombre"]) ?>
            </div>
            <a href="../auth/logout.php">Cerrar sesión</a>
        </div>
    </div>

    <!-- HERO -->
    <div class="hero">
        <div style="max-width:960px;margin:0 auto;">
            <div class="hero-greeting">Bienvenido/a de vuelta</div>
            <div class="hero-name"><?= htmlspecialchars($usuario["nombre"]) ?></div>
            <div class="hero-sub">Portal de Titulación Profesional · UMSA 2026</div>
        </div>
    </div>

    <div class="contenido">

        <!-- ESTADÍSTICAS -->
        <?php if ($total > 0): ?>
            <div class="stats-grid" style="margin-top:-24px;">
                <div class="stat-card">
                    <div class="stat-icon si-total">📋</div>
                    <div class="stat-info">
                        <div class="stat-number"><?= $total ?></div>
                        <div class="stat-label">Total trámites</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-amber">✏️</div>
                    <div class="stat-info">
                        <div class="stat-number"><?= $borradores ?></div>
                        <div class="stat-label">Borradores</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-blue">⏳</div>
                    <div class="stat-info">
                        <div class="stat-number"><?= $revision ?></div>
                        <div class="stat-label">En revisión</div>
                    </div>
                </div>
                <?php if ($observados > 0): ?>
                    <div class="stat-card">
                        <div class="stat-icon si-red">⚠️</div>
                        <div class="stat-info">
                            <div class="stat-number"><?= $observados ?></div>
                            <div class="stat-label">Observados</div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($entregados > 0): ?>
                    <div class="stat-card">
                        <div class="stat-icon si-green">🎓</div>
                        <div class="stat-info">
                            <div class="stat-number"><?= $entregados ?></div>
                            <div class="stat-label">Entregados</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- ACCIONES PRINCIPALES -->
        <div class="section-title">Acciones rápidas</div>
        <div class="actions-grid">
            <a class="action-card primary" href="nuevo_tramite.php">
                <div class="action-icon">＋</div>
                <div class="action-title">Nuevo Trámite</div>
                <div class="action-desc">Inicie una nueva solicitud de título profesional.</div>
                <div class="action-arrow">Comenzar →</div>
            </a>
            <a class="action-card" href="mis_tramites.php">
                <div class="action-icon">📄</div>
                <div class="action-title">Mis Trámites</div>
                <div class="action-desc">Gestione y haga seguimiento de todas sus solicitudes.</div>
                <div class="action-arrow">Ver todos →</div>
            </a>
            <a class="action-card" href="observaciones.php">
                <div class="action-icon">⚠️</div>
                <div class="action-title">
                    Mis Observaciones
                    <?php if ($observados > 0): ?>
                        <span class="badge-alert"><?= $observados ?></span>
                    <?php endif; ?>
                </div>
                <div class="action-desc">Revise y subsane observaciones del funcionario.</div>
                <div class="action-arrow">Revisar →</div>
            </a>
        </div>

    </div>
</body>

</html>