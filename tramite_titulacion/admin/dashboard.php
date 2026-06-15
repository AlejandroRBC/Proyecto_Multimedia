<?php
include "../helpers/auth.php";
verificarRol("ADMIN");
$usuario = $_SESSION["usuario"];
include "../helpers/json_helper.php";

$tramites = leerJson("../data/tramites.json");
$usuarios = leerJson("../data/usuarios.json");
$historial = leerJson("../data/historial.json");

$totalTramites = count($tramites);
$totalUsuarios = count($usuarios);
$totalHistorial = count($historial);
$pendientes = count(array_filter($tramites, fn($t) => $t["estado"] === "PENDIENTE_REVISION"));
$entregados = count(array_filter($tramites, fn($t) => $t["estado"] === "ENTREGADO"));
$observados = count(array_filter($tramites, fn($t) => $t["estado"] === "OBSERVADO"));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin – UMSA</title>
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

        /* Stats grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 14px;
            margin-bottom: 30px;
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
            transition: box-shadow .2s, transform .15s;
        }

        .stat-card:hover {
            box-shadow: 0 4px 16px rgba(30, 58, 138, .1);
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .si-blue {
            background: #eff6ff;
        }

        .si-purple {
            background: #f5f3ff;
        }

        .si-green {
            background: #f0fdf4;
        }

        .si-orange {
            background: #fff7ed;
        }

        .si-red {
            background: #fef2f2;
        }

        .si-slate {
            background: #f8fafc;
        }

        .stat-num {
            font-size: 1.7rem;
            font-weight: 800;
            line-height: 1;
        }

        .stat-label {
            font-size: .73rem;
            color: #64748b;
            font-weight: 600;
            margin-top: 3px;
        }

        .sn-blue {
            color: #1e40af;
        }

        .sn-purple {
            color: #7c3aed;
        }

        .sn-green {
            color: #166534;
        }

        .sn-orange {
            color: #c2410c;
        }

        .sn-red {
            color: #b91c1c;
        }

        .sn-slate {
            color: #475569;
        }

        /* Accesos */
        .section-title {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 14px;
        }

        .accesos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
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
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .ai-blue {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
        }

        .ai-green {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        }

        .ai-purple {
            background: linear-gradient(135deg, #f5f3ff, #ede9fe);
        }

        .acceso-text {}

        .acceso-title {
            font-size: .97rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 3px;
        }

        .acceso-desc {
            font-size: .78rem;
            color: #64748b;
        }

        .acceso-arrow {
            margin-left: auto;
            color: #94a3b8;
            font-size: 1rem;
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
        <div class="welcome-title">Panel Administrativo 👋</div>
        <div class="welcome-sub">Vista general del sistema de titulación · UMSA</div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon si-blue">📄</div>
                <div>
                    <div class="stat-num sn-blue"><?= $totalTramites ?></div>
                    <div class="stat-label">Total de trámites</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon si-orange">⏳</div>
                <div>
                    <div class="stat-num sn-orange"><?= $pendientes ?></div>
                    <div class="stat-label">Pendientes revisión</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon si-red">⚠️</div>
                <div>
                    <div class="stat-num sn-red"><?= $observados ?></div>
                    <div class="stat-label">Observados</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon si-green">🎓</div>
                <div>
                    <div class="stat-num sn-green"><?= $entregados ?></div>
                    <div class="stat-label">Títulos entregados</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon si-purple">👤</div>
                <div>
                    <div class="stat-num sn-purple"><?= $totalUsuarios ?></div>
                    <div class="stat-label">Usuarios registrados</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon si-slate">📋</div>
                <div>
                    <div class="stat-num sn-slate"><?= $totalHistorial ?></div>
                    <div class="stat-label">Eventos de auditoría</div>
                </div>
            </div>
        </div>

        <div class="section-title">Accesos rápidos</div>
        <div class="accesos-grid">
            <a href="usuarios.php" class="acceso-card">
                <div class="acceso-icon ai-blue">👤</div>
                <div class="acceso-text">
                    <div class="acceso-title">Gestión de Usuarios</div>
                    <div class="acceso-desc">Ver, activar y administrar cuentas del sistema</div>
                </div>
                <span class="acceso-arrow">→</span>
            </a>
            <a href="tramites.php" class="acceso-card">
                <div class="acceso-icon ai-green">📄</div>
                <div class="acceso-text">
                    <div class="acceso-title">Control de Trámites</div>
                    <div class="acceso-desc">Supervisar el estado de todos los trámites</div>
                </div>
                <span class="acceso-arrow">→</span>
            </a>
            <a href="auditoria.php" class="acceso-card">
                <div class="acceso-icon ai-purple">📊</div>
                <div class="acceso-text">
                    <div class="acceso-title">Auditoría del Sistema</div>
                    <div class="acceso-desc">Historial completo de eventos y cambios de estado</div>
                </div>
                <span class="acceso-arrow">→</span>
            </a>
        </div>
    </div>
</body>

</html>