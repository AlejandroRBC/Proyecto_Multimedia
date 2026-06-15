<?php
include "../helpers/auth.php";
$usuario = $_SESSION["usuario"];
include "../helpers/json_helper.php";

$tramites = leerJson("../data/tramites.json");
$paraFirmar = count(array_filter($tramites, fn($t) => $t["estado"] === "REVISION_AUTORIDAD"));
$firmados = count(array_filter($tramites, fn($t) => $t["estado"] === "FIRMADO"));
$entregados = count(array_filter($tramites, fn($t) => $t["estado"] === "ENTREGADO"));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Autoridad – UMSA</title>
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

        .welcome-header {
            margin-bottom: 28px;
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
        }

        /* Stats */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 14px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: #fff;
            border-radius: 16px;
            border: 1.5px solid #e2e8f0;
            padding: 20px 22px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .05);
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .icon-firma {
            background: #eff6ff;
        }

        .icon-firmado {
            background: #f5f3ff;
        }

        .icon-entregado {
            background: #f0fdf4;
        }

        .stat-info {}

        .stat-num {
            font-size: 1.7rem;
            font-weight: 800;
            line-height: 1;
        }

        .stat-label {
            font-size: .75rem;
            color: #64748b;
            font-weight: 600;
            margin-top: 3px;
        }

        .num-firma {
            color: #1e40af;
        }

        .num-firmado {
            color: #7c3aed;
        }

        .num-entregado {
            color: #166534;
        }

        /* Accesos rápidos */
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
        }

        .acceso-firmar .acceso-icon {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
        }

        .acceso-info {}

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
        <div class="welcome-header">
            <div class="welcome-title">Bienvenido/a, <?= htmlspecialchars(explode(" ", $usuario["nombre"])[0]) ?> 👋
            </div>
            <div class="welcome-sub">Panel de Autoridad · Sistema de Titulación Profesional UMSA</div>
        </div>

        <!-- ESTADÍSTICAS -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon icon-firma">✍️</div>
                <div class="stat-info">
                    <div class="stat-num num-firma"><?= $paraFirmar ?></div>
                    <div class="stat-label">Pendientes de firma</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-firmado">📜</div>
                <div class="stat-info">
                    <div class="stat-num num-firmado"><?= $firmados ?></div>
                    <div class="stat-label">Firmados / Por entregar</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-entregado">🎓</div>
                <div class="stat-info">
                    <div class="stat-num num-entregado"><?= $entregados ?></div>
                    <div class="stat-label">Títulos entregados</div>
                </div>
            </div>
        </div>

        <!-- ACCESOS RÁPIDOS -->
        <div class="accesos-title">Accesos rápidos</div>
        <div class="accesos-grid">
            <a href="bandeja.php" class="acceso-card acceso-firmar">
                <div class="acceso-icon">✍️</div>
                <div class="acceso-info">
                    <div class="acceso-title">Bandeja de Firma y Entrega</div>
                    <div class="acceso-desc">Revise documentos, firme y entregue títulos</div>
                </div>
                <?php if ($paraFirmar > 0): ?>
                    <span class="badge-alert"><?= $paraFirmar ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</body>

</html>