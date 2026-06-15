<?php
include "../helpers/auth.php";
verificarRol("ESTUDIANTE");
include "../helpers/json_helper.php";

$usuario = $_SESSION["usuario"];
$tramites = leerJson("../data/tramites.json");
$misTramites = array_values(array_filter($tramites, fn($t) => $t["estudiante"] == $usuario["id"]));

$conteos = ["TODOS" => count($misTramites)];
foreach ($misTramites as $t) {
    $conteos[$t["estado"]] = ($conteos[$t["estado"]] ?? 0) + 1;
}

$filtroActivo = $_GET["filtro"] ?? "TODOS";
$tramitesFiltrados = $filtroActivo === "TODOS"
    ? $misTramites
    : array_values(array_filter($misTramites, fn($t) => $t["estado"] === $filtroActivo));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis Trámites – UMSA</title>
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

        /* ── CONTENIDO ── */
        .contenido {
            max-width: 960px;
            margin: 0 auto;
            padding: 28px 24px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 22px;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
        }

        .page-sub {
            font-size: .85rem;
            color: #64748b;
            margin-top: 4px;
        }

        .btn-nuevo {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: white;
            padding: 10px 22px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: .88rem;
            box-shadow: 0 4px 12px rgba(37, 99, 235, .3);
            transition: transform .15s, box-shadow .15s;
        }

        .btn-nuevo:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, .4);
        }

        /* ── FILTROS ── */
        .filtros-bar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .filtro-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 22px;
            font-size: .8rem;
            font-weight: 700;
            text-decoration: none;
            border: 2px solid #e2e8f0;
            background: #fff;
            color: #475569;
            cursor: pointer;
            transition: all .18s;
            user-select: none;
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

        .filtro-chip .chip-count {
            background: rgba(0, 0, 0, .12);
            border-radius: 10px;
            padding: 1px 7px;
            font-size: .72rem;
        }

        .filtro-chip.activo .chip-count {
            background: rgba(255, 255, 255, .25);
        }

        /* Colores específicos por estado (no activo) */
        .chip-BORRADOR:not(.activo) {
            border-color: #f6e05e;
            color: #92400e;
            background: #fffbeb;
        }

        .chip-PENDIENTE_REVISION:not(.activo) {
            border-color: #93c5fd;
            color: #1e40af;
            background: #eff6ff;
        }

        .chip-REVISION_DOCUMENTAL:not(.activo) {
            border-color: #c4b5fd;
            color: #5b21b6;
            background: #f5f3ff;
        }

        .chip-OBSERVADO:not(.activo) {
            border-color: #fca5a5;
            color: #b91c1c;
            background: #fef2f2;
        }

        .chip-APROBADO:not(.activo) {
            border-color: #86efac;
            color: #166534;
            background: #f0fdf4;
        }

        .chip-ENTREGADO:not(.activo) {
            border-color: #1e3a8a;
            color: #1e3a8a;
            background: #eff6ff;
        }

        /* ── CARDS DE TRÁMITE ── */
        .tramite-card {
            background: #fff;
            border-radius: 16px;
            border: 1.5px solid #e2e8f0;
            padding: 20px 24px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .05);
            transition: box-shadow .2s, border-color .2s;
        }

        .tramite-card:hover {
            box-shadow: 0 4px 18px rgba(30, 58, 138, .1);
            border-color: #bfdbfe;
        }

        .tramite-card.card-observado {
            border-color: #fca5a5;
            background: #fff8f8;
        }

        .tramite-card-left {
            flex: 1;
            min-width: 200px;
        }

        .tramite-id {
            font-size: .75rem;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .tramite-carrera {
            font-size: 1rem;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 2px;
        }

        .tramite-nivel {
            font-size: .8rem;
            color: #64748b;
        }

        .tramite-fecha {
            font-size: .75rem;
            color: #94a3b8;
            margin-top: 4px;
        }

        /* Badges estado */
        .estado-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: .75rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .estado-BORRADOR {
            background: #fffbeb;
            color: #92400e;
            border: 1.5px solid #f6e05e;
        }

        .estado-PENDIENTE_REVISION {
            background: #eff6ff;
            color: #1e40af;
            border: 1.5px solid #93c5fd;
        }

        .estado-REVISION_DOCUMENTAL {
            background: #f5f3ff;
            color: #5b21b6;
            border: 1.5px solid #c4b5fd;
        }

        .estado-OBSERVADO {
            background: #fef2f2;
            color: #b91c1c;
            border: 1.5px solid #fca5a5;
        }

        .estado-APROBADO {
            background: #f0fdf4;
            color: #166534;
            border: 1.5px solid #86efac;
        }

        .estado-REVISION_AUTORIDAD {
            background: #f0fdf4;
            color: #166534;
            border: 1.5px solid #86efac;
        }

        .estado-EN_ELABORACION {
            background: #ecfdf5;
            color: #065f46;
            border: 1.5px solid #6ee7b7;
        }

        .estado-FIRMADO {
            background: #eff6ff;
            color: #1e3a8a;
            border: 1.5px solid #93c5fd;
        }

        .estado-ENTREGADO {
            background: #1e3a8a;
            color: #fff;
            border: 1.5px solid #1e3a8a;
        }

        /* Botones acción */
        .acciones {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .btn-accion {
            padding: 7px 14px;
            border-radius: 8px;
            font-size: .78rem;
            font-weight: 600;
            text-decoration: none;
            border: 1.5px solid transparent;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: opacity .15s, transform .1s;
        }

        .btn-accion:hover {
            opacity: .85;
            transform: translateY(-1px);
        }

        .btn-ver {
            background: #eff6ff;
            color: #1e40af;
            border-color: #bfdbfe;
        }

        .btn-docs {
            background: #f0fdf4;
            color: #166534;
            border-color: #86efac;
        }

        .btn-editar {
            background: #fffbeb;
            color: #92400e;
            border-color: #fcd34d;
        }

        .btn-obs {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fca5a5;
        }

        .btn-eliminar {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fca5a5;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: #fff;
            border-radius: 16px;
            border: 2px dashed #e2e8f0;
        }

        .empty-state h3 {
            color: #475569;
            margin-bottom: 8px;
            font-size: 1.1rem;
        }

        .empty-state p {
            color: #94a3b8;
            margin-bottom: 20px;
            font-size: .9rem;
        }

        /* Info filtro */
        .filtro-info {
            font-size: .8rem;
            color: #64748b;
            margin-bottom: 14px;
            padding: 8px 16px;
            background: #f8fafc;
            border-radius: 8px;
            border-left: 3px solid #2563eb;
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

        <!-- ENCABEZADO -->
        <div class="page-header">
            <div>
                <div class="page-title">📄 Mis Trámites</div>
                <div class="page-sub">Gestione y haga seguimiento de sus solicitudes de titulación.</div>
            </div>
            <a href="nuevo_tramite.php" class="btn-nuevo">＋ Nuevo Trámite</a>
        </div>

        <?php if (!empty($misTramites)): ?>

            <!-- FILTROS CLICABLES -->
            <div class="filtros-bar">
                <?php
                $iconosFiltro = [
                    "TODOS" => "📋",
                    "BORRADOR" => "✏️",
                    "PENDIENTE_REVISION" => "⏳",
                    "REVISION_DOCUMENTAL" => "🔎",
                    "OBSERVADO" => "⚠️",
                    "APROBADO" => "✅",
                    "REVISION_AUTORIDAD" => "📜",
                    "FIRMADO" => "✍️",
                    "ENTREGADO" => "🎓",
                ];
                $etiquetasFiltro = [
                    "TODOS" => "Todos",
                    "BORRADOR" => "Borrador",
                    "PENDIENTE_REVISION" => "En revisión",
                    "REVISION_DOCUMENTAL" => "Rev. documental",
                    "OBSERVADO" => "Observado",
                    "APROBADO" => "Aprobado",
                    "REVISION_AUTORIDAD" => "Rev. autoridad",
                    "FIRMADO" => "Firmado",
                    "ENTREGADO" => "Entregado",
                ];

                $estadosPresentes = array_unique(array_column($misTramites, "estado"));
                $filtrosMostrar = array_merge(["TODOS"], $estadosPresentes);

                foreach ($filtrosMostrar as $f):
                    $cnt = $conteos[$f] ?? 0;
                    $activo = ($filtroActivo === $f) ? " activo" : "";
                    $icon = $iconosFiltro[$f] ?? "•";
                    $label = $etiquetasFiltro[$f] ?? $f;
                    ?>
                    <a href="?filtro=<?= urlencode($f) ?>" class="filtro-chip chip-<?= htmlspecialchars($f) ?><?= $activo ?>">
                        <?= $icon ?>         <?= $label ?>
                        <span class="chip-count"><?= $cnt ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- INFO FILTRO ACTIVO -->
            <?php if ($filtroActivo !== "TODOS"): ?>
                <div class="filtro-info">
                    Mostrando: <strong><?= htmlspecialchars($etiquetasFiltro[$filtroActivo] ?? $filtroActivo) ?></strong>
                    — <?= count($tramitesFiltrados) ?> trámite(s) ·
                    <a href="mis_tramites.php" style="color:#2563eb;text-decoration:none;">Ver todos</a>
                </div>
            <?php endif; ?>

        <?php endif; ?>

        <!-- LISTA DE TRÁMITES -->
        <?php if (empty($misTramites)): ?>
            <div class="empty-state">
                <div style="font-size:3rem;margin-bottom:12px">📋</div>
                <h3>No tiene trámites registrados</h3>
                <p>Inicie su solicitud de título profesional haciendo clic en "Nuevo Trámite".</p>
                <a href="nuevo_tramite.php" class="btn-nuevo" style="display:inline-flex;margin-top:4px">＋ Iniciar
                    Trámite</a>
            </div>

        <?php elseif (empty($tramitesFiltrados)): ?>
            <div class="empty-state">
                <div style="font-size:2.5rem;margin-bottom:10px">🔍</div>
                <h3>Sin resultados para este filtro</h3>
                <p>No tiene trámites con el estado seleccionado.</p>
                <a href="mis_tramites.php" style="color:#2563eb;font-weight:600;text-decoration:none;">← Ver todos</a>
            </div>

        <?php else: ?>

            <?php foreach ($tramitesFiltrados as $t):
                $estado = $t["estado"];
                $esObservado = $estado === "OBSERVADO";
                ?>
                <div class="tramite-card <?= $esObservado ? 'card-observado' : '' ?>">
                    <div class="tramite-card-left">
                        <div class="tramite-id">Trámite #<?= $t["id"] ?></div>
                        <div class="tramite-carrera"><?= htmlspecialchars($t["carrera"]) ?></div>
                        <div class="tramite-nivel"><?= htmlspecialchars($t["nivel"]) ?></div>
                        <div class="tramite-fecha">📅 <?= htmlspecialchars($t["fechaRegistro"]) ?></div>
                    </div>

                    <div>
                        <?php
                        $iconos = [
                            "BORRADOR" => "✏️",
                            "PENDIENTE_REVISION" => "⏳",
                            "REVISION_DOCUMENTAL" => "🔎",
                            "OBSERVADO" => "⚠️",
                            "APROBADO" => "✅",
                            "REVISION_AUTORIDAD" => "📜",
                            "EN_ELABORACION" => "⚙️",
                            "FIRMADO" => "✍️",
                            "ENTREGADO" => "🎓",
                        ];
                        ?>
                        <span class="estado-badge estado-<?= $estado ?>">
                            <?= ($iconos[$estado] ?? "•") . " " . htmlspecialchars($estado) ?>
                        </span>
                    </div>

                    <div class="acciones">
                        <!-- Ver seguimiento -->
                        <a href="seguimiento.php?id=<?= $t["id"] ?>" class="btn-accion btn-ver">👁 Ver</a>

                        <?php if ($estado === "BORRADOR"): ?>
                            <a href="documentos.php?id=<?= $t["id"] ?>" class="btn-accion btn-docs">📂 Documentos</a>
                            <a href="editar_tramite.php?id=<?= $t["id"] ?>" class="btn-accion btn-editar">✏️ Editar</a>
                            <a href="eliminar_tramite.php?id=<?= $t["id"] ?>" class="btn-accion btn-eliminar"
                                onclick="return confirm('¿Eliminar el trámite #<?= $t["id"] ?>? Esta acción no se puede deshacer.')">
                                🗑 Eliminar
                            </a>
                        <?php endif; ?>

                        <?php if ($estado === "OBSERVADO"): ?>
                            <a href="observaciones.php?id=<?= $t["id"] ?>" class="btn-accion btn-obs">⚠️ Observaciones</a>
                            <a href="documentos.php?id=<?= $t["id"] ?>" class="btn-accion btn-docs">📂 Documentos</a>
                            <a href="editar_tramite.php?id=<?= $t["id"] ?>" class="btn-accion btn-editar">✏️ Editar</a>
                            <a href="eliminar_tramite.php?id=<?= $t["id"] ?>" class="btn-accion btn-eliminar"
                                onclick="return confirm('¿Eliminar el trámite observado #<?= $t["id"] ?>?')">
                                🗑 Eliminar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>

    </div>
</body>

</html>