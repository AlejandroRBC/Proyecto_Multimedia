<?php
session_start();
include "json_helper.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';

    $usuarios = leer_json('usuarios.json');
    foreach ($usuarios as $u) {
        if ($u['usuario'] === $usuario && password_verify($password, $u['password'])) {
            $_SESSION['usuario'] = $u['usuario'];
            $_SESSION['nombre'] = $u['nombre'];
            $_SESSION['rol'] = $u['rol'];
            header("location: bandeja.php");
            exit();
        }
    }
    $error = "Usuario o contraseña incorrectos";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Beca BAERA</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #f0f2f5; }
        .login { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 320px; }
        h1 { text-align: center; color: #1a1a2e; font-size: 20px; margin-bottom: 24px; }
        label { display: block; margin-bottom: 6px; color: #333; font-weight: bold; }
        input { width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #1a1a2e; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; }
        button:hover { background: #16213e; }
        .error { color: #e74c3c; text-align: center; margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="login">
        <h1>Sistema BPM - Beca BAERA</h1>
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <label>Usuario</label>
            <input type="text" name="usuario" required>
            <label>Contraseña</label>
            <input type="password" name="password" required>
            <button type="submit">Ingresar</button>
        </form>
    </div>
</body>
</html>
