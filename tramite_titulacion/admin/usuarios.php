<?php
include "../helpers/auth.php";
verificarRol("ADMIN");
include "../helpers/json_helper.php";

$usuario = $_SESSION["usuario"];
$usuarios = leerJson("../data/usuarios.json");

$conteoRol = [];
foreach ($usuarios as $u) {
    $conteoRol[$u["rol"]] = ($conteoRol[$u["rol"]] ?? 0) + 1;
}
$activos = count(array_filter($usuarios, fn($u) => ($u["estado"] ?? "") === "activo"));
$inactivos = count($usuarios) - $activos;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuarios – Admin UMSA</title>
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

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 22px;
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
        }

        /* Stats */
        .stats-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .stat-pill {
            background: #fff;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .04);
            font-size: .85rem;
        }

        .stat-pill strong {
            font-size: 1.15rem;
            font-weight: 800;
        }

        .sp-total strong {
            color: #1e40af;
        }

        .sp-activo strong {
            color: #166534;
        }

        .sp-inactivo strong {
            color: #b91c1c;
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
            padding: 12px 18px;
            font-size: .75rem;
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
            padding: 13px 18px;
            font-size: .87rem;
            color: #374151;
            vertical-align: middle;
        }

        /* Avatar pequeño */
        .user-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-mini-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .82rem;
            color: #fff;
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
        }

        .user-name {
            font-weight: 600;
            color: #1e293b;
        }

        .user-id {
            font-size: .72rem;
            color: #94a3b8;
        }

        /* Badges rol */
        .rol-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .72rem;
            font-weight: 700;
        }

        .rol-ESTUDIANTE {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .rol-FUNCIONARIO {
            background: #f5f3ff;
            color: #5b21b6;
            border: 1px solid #c4b5fd;
        }

        .rol-AUTORIDAD {
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #fed7aa;
        }

        .rol-ADMIN {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        /* Badges estado */
        .estado-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .72rem;
            font-weight: 700;
        }

        .estado-activo {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #86efac;
        }

        .estado-inactivo {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
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
        <div class="page-header">
            <div>
                <div class="page-title">👤 Gestión de Usuarios</div>
                <div class="page-sub">Todos los usuarios registrados en el sistema</div>
            </div>
        </div>

        <!-- STATS -->
        <div class="stats-row">
            <div class="stat-pill sp-total">
                <span>👥 Total</span><strong><?= count($usuarios) ?></strong>
            </div>
            <div class="stat-pill sp-activo">
                <span>✅ Activos</span><strong><?= $activos ?></strong>
            </div>
            <div class="stat-pill sp-inactivo">
                <span>⛔ Inactivos</span><strong><?= $inactivos ?></strong>
            </div>
            <?php foreach ($conteoRol as $rol => $cnt): ?>
                <div class="stat-pill">
                    <span><?= htmlspecialchars($rol) ?></span><strong><?= $cnt ?></strong>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- TABLA -->
        <div class="tabla-wrap">
            <div class="tabla-header">
                <div class="tabla-title">Lista de usuarios</div>
                <input type="text" class="search-input" placeholder="🔍 Buscar usuario..."
                    oninput="filtrarTabla(this.value)">
            </div>
            <?php if (empty($usuarios)): ?>
                <div class="empty-box">No hay usuarios registrados aún.</div>
            <?php else: ?>
                <table id="tablaUsuarios">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td style="color:#94a3b8;font-weight:600;">#<?= htmlspecialchars($u["id"]) ?></td>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-mini-avatar"><?= strtoupper(substr($u["nombre"], 0, 1)) ?></div>
                                        <div>
                                            <div class="user-name"><?= htmlspecialchars($u["nombre"]) ?></div>
                                            <div class="user-id"><?= htmlspecialchars($u["usuario"] ?? "—") ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php $rolIcons = ["ESTUDIANTE" => "🎓", "FUNCIONARIO" => "🗂️", "AUTORIDAD" => "📜", "ADMIN" => "⚙️"]; ?>
                                    <span class="rol-badge rol-<?= htmlspecialchars($u["rol"]) ?>">
                                        <?= $rolIcons[$u["rol"]] ?? "•" ?>         <?= htmlspecialchars($u["rol"]) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php $est = $u["estado"] ?? "activo"; ?>
                                    <span class="estado-badge estado-<?= $est ?>">
                                        <?= $est === "activo" ? "✅ Activo" : "⛔ Inactivo" ?>
                                    </span>
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
            const filas = document.querySelectorAll('#tablaUsuarios tbody tr');
            q = q.toLowerCase();
            filas.forEach(f => {
                f.style.display = f.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        }
    </script>
</body>

</html>