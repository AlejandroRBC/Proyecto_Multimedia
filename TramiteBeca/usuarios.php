<?php
session_start();
include "json_helper.php";

if (!isset($_SESSION['usuario'])) {
    header("location: login.php");
    exit();
}

$mensaje = '';
$error = '';
$usuarios = leer_json('usuarios.json');

if (isset($_GET['eliminar'])) {
    $eliminar = $_GET['eliminar'];
    $usuarios = array_filter($usuarios, function ($u) use ($eliminar) {
        return $u['usuario'] !== $eliminar;
    });
    $usuarios = array_values($usuarios);
    guardar_json('usuarios.json', $usuarios);
    $mensaje = "Usuario eliminado correctamente";
    header("location: usuarios.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $rol = $_POST['rol'] ?? '';

    if ($accion === 'crear') {
        $existe = false;
        foreach ($usuarios as $u) {
            if ($u['usuario'] === $usuario) { $existe = true; break; }
        }
        if ($existe) {
            $error = "El usuario '$usuario' ya existe";
        } else {
            $usuarios[] = [
                'usuario' => $usuario,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'nombre' => $nombre,
                'rol' => $rol
            ];
            guardar_json('usuarios.json', $usuarios);
            $mensaje = "Usuario '$usuario' creado correctamente";
        }
    } elseif ($accion === 'editar') {
        foreach ($usuarios as &$u) {
            if ($u['usuario'] === $usuario) {
                $u['nombre'] = $nombre;
                $u['rol'] = $rol;
                if (!empty($password)) {
                    $u['password'] = password_hash($password, PASSWORD_DEFAULT);
                }
                break;
            }
        }
        unset($u);
        guardar_json('usuarios.json', $usuarios);
        $mensaje = "Usuario '$usuario' actualizado correctamente";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de usuarios</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .card { background: white; border-radius: 8px; padding: 24px; max-width: 800px; margin: 0 auto 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #1a1a2e; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1a1a2e; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        tr:hover { background: #f8f9fa; }
        .btn-sm { padding: 4px 10px; border: none; border-radius: 3px; cursor: pointer; font-size: 12px; text-decoration: none; display: inline-block; }
        .btn-edit { background: #3498db; color: white; }
        .btn-delete { background: #e74c3c; color: white; }
        .btn-back { display: inline-block; padding: 8px 16px; background: #1a1a2e; color: white; text-decoration: none; border-radius: 4px; margin-bottom: 16px; }
        .btn-back:hover { background: #16213e; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px; }
        label { display: block; margin-bottom: 4px; color: #333; font-weight: bold; font-size: 13px; }
        input, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn-save { padding: 8px 20px; background: #27ae60; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn-save:hover { background: #219a52; }
        .mensaje { padding: 10px; background: #d4edda; color: #155724; border-radius: 4px; margin-bottom: 12px; }
        .error { padding: 10px; background: #fdf0ef; color: #e74c3c; border-radius: 4px; margin-bottom: 12px; }
        .rol-badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 12px; background: #e8f4fd; color: #2980b9; }
        .full { grid-column: 1 / -1; }
    </style>
</head>
<body>
    <a href="bandeja.php" class="btn-back">&larr; Volver a la bandeja</a>

    <div class="card">
        <h2>Gestión de usuarios</h2>
        <?php if ($mensaje): ?><div class="mensaje"><?= $mensaje ?></div><?php endif; ?>
        <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>

        <table>
            <tr><th>Usuario</th><th>Nombre</th><th>Rol</th><th>Acciones</th></tr>
            <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['usuario']) ?></td>
                <td><?= htmlspecialchars($u['nombre']) ?></td>
                <td><span class="rol-badge"><?= htmlspecialchars($u['rol']) ?></span></td>
                <td>
                    <a href="usuarios.php?editar=<?= urlencode($u['usuario']) ?>" class="btn-sm btn-edit">Editar</a>
                    <a href="usuarios.php?eliminar=<?= urlencode($u['usuario']) ?>" class="btn-sm btn-delete" onclick="return confirm('¿Eliminar usuario \'<?= htmlspecialchars(addslashes($u['usuario'])) ?>\'?')">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <?php
    $editando = $_GET['editar'] ?? null;
    $edit_data = null;
    if ($editando) {
        foreach ($usuarios as $u) {
            if ($u['usuario'] === $editando) { $edit_data = $u; break; }
        }
    }
    ?>

    <div class="card">
        <h2><?= $edit_data ? 'Editar usuario' : 'Nuevo usuario' ?></h2>
        <form method="POST">
            <input type="hidden" name="accion" value="<?= $edit_data ? 'editar' : 'crear' ?>">
            <div class="form-row">
                <div>
                    <label>Usuario</label>
                    <input type="text" name="usuario" value="<?= htmlspecialchars($edit_data['usuario'] ?? '') ?>" <?= $edit_data ? 'readonly' : '' ?> required>
                </div>
                <div>
                    <label>Contraseña <?= $edit_data ? '(dejar vacío para no cambiar)' : '' ?></label>
                    <input type="password" name="password" <?= $edit_data ? '' : 'required' ?>>
                </div>
                <div>
                    <label>Nombre completo</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($edit_data['nombre'] ?? '') ?>" required>
                </div>
                <div>
                    <label>Rol</label>
                    <select name="rol" required>
                        <option value="">Seleccionar...</option>
                        <option value="Estudiante" <?= ($edit_data['rol'] ?? '') === 'Estudiante' ? 'selected' : '' ?>>Estudiante</option>
                        <option value="Bienestar Social" <?= ($edit_data['rol'] ?? '') === 'Bienestar Social' ? 'selected' : '' ?>>Bienestar Social</option>
                        <option value="Trabajador Social" <?= ($edit_data['rol'] ?? '') === 'Trabajador Social' ? 'selected' : '' ?>>Trabajador Social</option>
                        <option value="Nutricionista" <?= ($edit_data['rol'] ?? '') === 'Nutricionista' ? 'selected' : '' ?>>Nutricionista</option>
                        <option value="Comité BAERA" <?= ($edit_data['rol'] ?? '') === 'Comité BAERA' ? 'selected' : '' ?>>Comité BAERA</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn-save"><?= $edit_data ? 'Guardar cambios' : 'Crear usuario' ?></button>
        </form>
    </div>
</body>
</html>
