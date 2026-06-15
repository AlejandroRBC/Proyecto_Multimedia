<?php
include "../helpers/auth.php";
$usuario = $_SESSION["usuario"];
include "../helpers/json_helper.php";

$tramites = leerJson("../data/tramites.json");
$pendientes = count(array_filter($tramites, fn($t) => $t["estado"] === "PENDIENTE_REVISION"));
$enRevision = count(array_filter($tramites, fn($t) => $t["estado"] === "REVISION_DOCUMENTAL"));
$observados = count(array_filter($tramites, fn($t) => $t["estado"] === "OBSERVADO"));
$aprobados = count(array_filter($tramites, fn($t) => $t["estado"] === "REVISION_AUTORIDAD"));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Funcionario – UMSA</title>
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
            padding: 32px 24px;
        }

        .welcome-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .welcome-sub {
            font-size: .88rem;
            color: #64748b;
            margin-bottom: 28px;
        }

        /* Stats */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: #fff;
            border-radius: 16px;
            border: 1.5px solid #e2e8f0;
            padding: 18px 20px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .05);
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .icon-pend {
            background: #eff6ff;
        }

        .icon-rev {
            background: #f5f3ff;
        }

        .icon-obs {
            background: #fef2f2;
        }

        .icon-aprov {
            background: #f0fdf4;
        }

        .stat-num {
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1;
        }

        .stat-label {
            font-size: .72rem;
            color: #64748b;
            font-weight: 600;
            margin-top: 3px;
        }

        .num-pend {
            color: #1e40af;
        }

        .num-rev {
            color: #7c3aed;
        }

        .num-obs {
            color: #b91c1c;
        }

        .num-aprov {
            color: #166534;
        }

        /* Accesos */
        .accesos-title {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 14px;
        }

        .accesos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
        }

        .acceso-card {
            background: #fff;
            border-radius: 16px;
            border: 1.5px solid #e2e8f0;
            padding: 22px 24px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .04);
            transition: box-shadow .2s, border-color .2s, transform .15s;
        }

        .acceso-card:hover {
            box-shadow: 0 6px 20px rgba(30, 58, 138, .12);
            border-color: #bfdbfe;
            transform: translateY(-2px);
        }

        .acceso-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
        }

        .acceso-title {
            font-size: .95rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 3px;
        }

        .acceso-desc {
            font-size: .78rem;
            color: #64748b;
        }

        .badge-alert {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ef4444;
            color: #fff;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: .72rem;
            font-weight: 800;
            margin-left: auto;
            flex-shrink: 0;
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
            <a href="../auth/logout.php">Cerrar sesión</a>
        </div>
    </div>

    <div class="contenido">
        <div class="welcome-title">Bienvenido/a, <?= htmlspecialchars(explode(" ", $usuario["nombre"])[0]) ?> 👋</div>
        <div class="welcome-sub">Panel de Funcionario · Sistema de Titulación Profesional UMSA</div>

        <!-- ESTADÍSTICAS -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon icon-pend">⏳</div>
                <div>
                    <div class="stat-num num-pend"><?= $pendientes ?></div>
                    <div class="stat-label">Pendientes de revisión</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-rev">🔎</div>
                <div>
                    <div class="stat-num num-rev"><?= $enRevision ?></div>
                    <div class="stat-label">En revisión documental</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-obs">⚠️</div>
                <div>
                    <div class="stat-num num-obs"><?= $observados ?></div>
                    <div class="stat-label">Observados</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-aprov">✅</div>
                <div>
                    <div class="stat-num num-aprov"><?= $aprobados ?></div>
                    <div class="stat-label">Aprobados / En autoridad</div>
                </div>
            </div>
        </div>

        <!-- ACCESOS RÁPIDOS -->
        <div class="accesos-title">Accesos rápidos</div>
        <div class="accesos-grid">
            <a href="bandeja.php" class="acceso-card">
                <div class="acceso-icon">📋</div>
                <div>
                    <div class="acceso-title">Bandeja de Revisión</div>
                    <div class="acceso-desc">Revise y gestione los trámites pendientes</div>
                </div>
                <?php if ($pendientes > 0): ?>
                    <span class="badge-alert"><?= $pendientes ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</body>

</html>