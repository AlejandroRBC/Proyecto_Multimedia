<?php
session_start();
if (isset($_SESSION["usuario"])) {
    $rol = $_SESSION["usuario"]["rol"];
    if ($rol === "ESTUDIANTE")
        header("Location: ../estudiante/dashboard.php");
    elseif ($rol === "FUNCIONARIO")
        header("Location: ../funcionario/dashboard.php");
    elseif ($rol === "AUTORIDAD")
        header("Location: ../autoridad/dashboard.php");
    elseif ($rol === "ADMIN")
        header("Location: ../admin/dashboard.php");
    exit();
}
$error = $_GET["error"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión – UMSA</title>
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
            min-height: 100vh;
            display: flex;
            background: #f0f4f8;
        }

        /* ── PANEL IZQUIERDO (decorativo) ── */
        .left-panel {
            width: 55%;
            background: linear-gradient(145deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px;
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .04);
            top: -120px;
            left: -120px;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            width: 360px;
            height: 360px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .05);
            bottom: -80px;
            right: -80px;
        }

        .left-content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: #fff;
        }

        .left-logo {
            width: 72px;
            height: 72px;
            background: rgba(255, 255, 255, .15);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 24px;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, .2);
        }

        .left-title {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .left-subtitle {
            font-size: .95rem;
            color: rgba(255, 255, 255, .7);
            margin-bottom: 36px;
            line-height: 1.5;
        }

        .steps {
            display: flex;
            flex-direction: column;
            gap: 16px;
            text-align: left;
            width: 100%;
            max-width: 320px;
        }

        .step {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            background: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 14px;
            padding: 14px 16px;
            backdrop-filter: blur(6px);
        }

        .step-icon {
            width: 36px;
            height: 36px;
            flex-shrink: 0;
            background: rgba(255, 255, 255, .15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .step-text {}

        .step-label {
            font-size: .8rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 2px;
        }

        .step-desc {
            font-size: .73rem;
            color: rgba(255, 255, 255, .6);
        }

        /* ── PANEL DERECHO (formulario) ── */
        .right-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 32px;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            margin-bottom: 28px;
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .login-sub {
            font-size: .85rem;
            color: #64748b;
        }

        /* Alerta error */
        .alert-error {
            background: #fef2f2;
            border: 1.5px solid #fca5a5;
            border-radius: 12px;
            padding: 12px 16px;
            color: #b91c1c;
            font-size: .85rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Formulario */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: .83rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 7px;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: .95rem;
            pointer-events: none;
            color: #94a3b8;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1.5px solid #d1d5db;
            border-radius: 11px;
            font-size: .95rem;
            color: #1a202c;
            background: #f9fafb;
            transition: border-color .2s, background .2s;
            outline: none;
        }

        .form-input:focus {
            border-color: #1e3a8a;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, .08);
        }

        /* Select rol */
        .select-wrap {
            position: relative;
        }

        .select-wrap select {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1.5px solid #d1d5db;
            border-radius: 11px;
            font-size: .95rem;
            color: #1a202c;
            background: #f9fafb;
            appearance: none;
            cursor: pointer;
            outline: none;
            transition: border-color .2s;
        }

        .select-wrap select:focus {
            border-color: #1e3a8a;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, .08);
        }

        .select-arrow {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #94a3b8;
            font-size: .8rem;
        }

        /* Botón */
        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: #fff;
            border: none;
            border-radius: 11px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(37, 99, 235, .35);
            transition: transform .15s, box-shadow .15s;
            margin-top: 6px;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, .4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Divider / registro */
        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 22px 0;
            color: #cbd5e1;
            font-size: .78rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .btn-register {
            display: block;
            width: 100%;
            padding: 12px;
            background: #fff;
            color: #1e3a8a;
            border: 1.5px solid #bfdbfe;
            border-radius: 11px;
            font-size: .92rem;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: background .18s, border-color .18s;
        }

        .btn-register:hover {
            background: #eff6ff;
            border-color: #93c5fd;
        }

        .footer-note {
            text-align: center;
            margin-top: 24px;
            font-size: .75rem;
            color: #94a3b8;
        }

        /* Responsive */
        @media (max-width: 720px) {
            .left-panel {
                display: none;
            }

            .right-panel {
                padding: 32px 20px;
            }
        }
    </style>
</head>

<body>

    <!-- PANEL IZQUIERDO -->
    <div class="left-panel">
        <div class="left-content">
            <div class="left-logo">🎓</div>
            <div class="left-title">Sistema BPM<br>Titulación UMSA</div>
            <div class="left-subtitle">Gestión digital del proceso de titulación profesional de la Universidad Mayor de
                San Andrés</div>

            <div class="steps">
                <div class="step">
                    <div class="step-icon">📋</div>
                    <div class="step-text">
                        <div class="step-label">Registra tu trámite</div>
                        <div class="step-desc">Completa tu información y envía tus documentos</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-icon">🔎</div>
                    <div class="step-text">
                        <div class="step-label">Revisión documental</div>
                        <div class="step-desc">El funcionario verifica tu documentación</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-icon">✍️</div>
                    <div class="step-text">
                        <div class="step-label">Firma y entrega</div>
                        <div class="step-desc">La autoridad firma y entrega tu título</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PANEL DERECHO -->
    <div class="right-panel">
        <div class="login-card">
            <div class="login-header">
                <div class="login-title">Iniciar sesión</div>
                <div class="login-sub">Ingrese sus credenciales para acceder al sistema</div>
            </div>

            <?php if ($error === "invalido"): ?>
                <div class="alert-error">⚠️ Usuario o contraseña incorrectos. Intente nuevamente.</div>
            <?php elseif ($error === "inactivo"): ?>
                <div class="alert-error">⛔ Su cuenta está desactivada. Contacte al administrador.</div>
            <?php elseif ($error === "rol"): ?>
                <div class="alert-error">🔒 El rol seleccionado no corresponde a esta cuenta. Verifique e intente
                    nuevamente.</div>
            <?php endif; ?>

            <form action="validar.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Usuario</label>
                    <div class="input-wrap">
                        <span class="input-icon">👤</span>
                        <input type="text" name="usuario" class="form-input" placeholder="Ingrese su usuario" required
                            autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <div class="input-wrap">
                        <span class="input-icon">🔒</span>
                        <input type="password" name="password" class="form-input" placeholder="Ingrese su contraseña"
                            required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Rol</label>
                    <div class="select-wrap">
                        <span class="input-icon">🏷️</span>
                        <select name="rol" required>
                            <option value="">— Seleccione su rol —</option>
                            <option value="ESTUDIANTE">🎓 Estudiante</option>
                            <option value="FUNCIONARIO">🗂️ Funcionario</option>
                            <option value="AUTORIDAD">📜 Autoridad</option>
                            <option value="ADMIN">⚙️ Administrador</option>
                        </select>
                        <span class="select-arrow">▼</span>
                    </div>
                </div>

                <button type="submit" class="btn-login">Ingresar →</button>
            </form>

            <div class="divider">¿No tiene cuenta?</div>
            <a href="registro.php" class="btn-register">✏️ Registrarse</a>

            <div class="footer-note">UMSA · Sistema de Titulación Profesional BPM</div>
        </div>
    </div>

</body>

</html>