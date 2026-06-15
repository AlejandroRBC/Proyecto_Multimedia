<?php
include "../helpers/auth.php";
verificarRol("ESTUDIANTE");
include "../helpers/json_helper.php";

$usuario = $_SESSION["usuario"];
$tramites = leerJson("../data/tramites.json");
$observaciones = leerJson("../data/observaciones.json");


$tramitesObservados = array_values(array_filter(
    $tramites,
    fn($t) => $t["estudiante"] == $usuario["id"] && $t["estado"] === "OBSERVADO"
));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis Observaciones – UMSA</title>
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
            max-width: 820px;
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
            margin-bottom: 24px;
        }

        /* CARD OBSERVACIÓN */
        .obs-card {
            background: #fff;
            border-radius: 18px;
            border: 1.5px solid #fca5a5;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(185, 28, 28, .08);
        }

        .obs-card-header {
            background: linear-gradient(135deg, #b91c1c, #dc2626);
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .obs-card-header-left {
            color: #fff;
        }

        .obs-tramite-id {
            font-size: .75rem;
            opacity: .8;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .obs-tramite-title {
            font-size: 1.05rem;
            font-weight: 700;
        }

        .obs-badge-estado {
            background: rgba(255, 255, 255, .2);
            color: #fff;
            border-radius: 20px;
            padding: 4px 14px;
            font-size: .75rem;
            font-weight: 700;
            border: 1px solid rgba(255, 255, 255, .3);
        }

        .obs-body {
            padding: 22px 24px;
        }

        /* Línea de observación */
        .obs-item {
            background: #fef2f2;
            border: 1.5px solid #fecaca;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 12px;
        }

        .obs-item-fecha {
            font-size: .72rem;
            color: #b91c1c;
            font-weight: 700;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .obs-item-desc {
            font-size: .9rem;
            color: #7f1d1d;
            line-height: 1.5;
        }

        /* Acciones */
        .obs-acciones {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid #f1f5f9;
        }

        .btn-accion {
            padding: 9px 20px;
            border-radius: 10px;
            font-size: .85rem;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: opacity .15s, transform .1s;
        }

        .btn-accion:hover {
            opacity: .87;
            transform: translateY(-1px);
        }

        .btn-subsanar {
            background: linear-gradient(135deg, #166534, #16a34a);
            color: #fff;
            box-shadow: 0 3px 10px rgba(22, 163, 74, .3);
        }

        .btn-documentos {
            background: #eff6ff;
            color: #1e40af;
            border: 1.5px solid #bfdbfe;
        }

        .btn-editar {
            background: #fffbeb;
            color: #92400e;
            border: 1.5px solid #fcd34d;
        }

        .btn-seguimiento {
            background: #f8fafc;
            color: #475569;
            border: 1.5px solid #e2e8f0;
        }

        /* Empty state / Success */
        .estado-ok {
            background: #f0fdf4;
            border: 1.5px solid #86efac;
            border-radius: 16px;
            padding: 36px 28px;
            text-align: center;
        }

        .estado-ok .icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .estado-ok h3 {
            color: #166534;
            font-size: 1.1rem;
            margin-bottom: 6px;
        }

        .estado-ok p {
            color: #4ade80;
            font-size: .85rem;
        }

        .btn-volver {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #475569;
            text-decoration: none;
            font-size: .85rem;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            background: #fff;
            margin-top: 20px;
            transition: background .18s;
        }

        .btn-volver:hover {
            background: #f1f5f9;
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
            <a href="dashboard.php">Inicio</a>
            <a href="../auth/logout.php">Cerrar sesión</a>
        </div>
    </div>

    <div class="contenido">

        <div class="page-title">⚠️ Mis Observaciones</div>
        <div class="page-sub">Revise las observaciones del funcionario y tome acción sobre su documentación.</div>

        <?php if (empty($tramitesObservados)): ?>

            <div class="estado-ok">
                <div class="icon">✅</div>
                <h3>No tiene observaciones pendientes</h3>
                <p>Todos sus trámites están al día.</p>
            </div>

        <?php else: ?>

            <?php foreach ($tramitesObservados as $t):

                $obsDelTramite = array_values(array_filter(
                    $observaciones,
                    fn($o) => $o["tramite"] == $t["id"]
                ));
                ?>
                <div class="obs-card">
                   
                    <div class="obs-card-header">
                        <div class="obs-card-header-left">
                            <div class="obs-tramite-id">Trámite #<?= $t["id"] ?></div>
                            <div class="obs-tramite-title"><?= htmlspecialchars($t["carrera"]) ?> —
                                <?= htmlspecialchars($t["nivel"]) ?></div>
                        </div>
                        <div class="obs-badge-estado">⚠️ OBSERVADO</div>
                    </div>

                    <!-- Cuerpo -->
                    <div class="obs-body">

                        <?php if (empty($obsDelTramite)): ?>
                            <p style="color:#94a3b8;font-size:.85rem;">No se encontraron detalles de observaciones.</p>
                        <?php else: ?>
                            <?php foreach ($obsDelTramite as $o): ?>
                                <div class="obs-item">
                                    <div class="obs-item-fecha">🕐 <?= htmlspecialchars($o["fecha"]) ?></div>
                                    <div class="obs-item-desc">⚠️ <?= htmlspecialchars($o["descripcion"]) ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Acciones -->
                        <div class="obs-acciones">
                            <a href="documentos.php?id=<?= $t["id"] ?>" class="btn-accion btn-documentos">
                                📂 Actualizar Documentación
                            </a>
                            <a href="editar_tramite.php?id=<?= $t["id"] ?>" class="btn-accion btn-editar">
                                ✏️ Editar Trámite
                            </a>
                            <a href="corregir.php?id=<?= $t["id"] ?>" class="btn-accion btn-subsanar"
                                onclick="return confirm('¿Confirma que subsanó las observaciones y desea reenviar a revisión?')">
                                ✅ Subsanar y Reenviar
                            </a>
                            <a href="seguimiento.php?id=<?= $t["id"] ?>" class="btn-accion btn-seguimiento">
                                👁 Ver Seguimiento
                            </a>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>

        <a href="dashboard.php" class="btn-volver">← Volver al inicio</a>

    </div>
</body>

</html>