<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Registro - Sistema BPM UMSA</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {

            font-family: "Segoe UI", sans-serif;

            min-height: 100vh;

            display: flex;

            background: #f0f4f8;

        }

        /* PANEL IZQUIERDO */

        .left-panel {

            width: 55%;

            background: linear-gradient(145deg,
                    #0f172a 0%,
                    #1e3a8a 50%,
                    #2563eb 100%);

            display: flex;

            justify-content: center;

            align-items: center;

            padding: 50px;

            position: relative;

            overflow: hidden;

        }

        .left-panel::before {

            content: "";

            position: absolute;

            width: 450px;

            height: 450px;

            background: rgba(255, 255, 255, .05);

            border-radius: 50%;

            top: -120px;

            left: -120px;

        }

        .left-panel::after {

            content: "";

            position: absolute;

            width: 300px;

            height: 300px;

            background: rgba(255, 255, 255, .04);

            border-radius: 50%;

            bottom: -80px;

            right: -80px;

        }

        .left-content {

            position: relative;

            z-index: 10;

            color: white;

            max-width: 360px;

        }

        .logo {

            width: 80px;

            height: 80px;

            background: rgba(255, 255, 255, .15);

            border-radius: 20px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 40px;

            margin-bottom: 30px;

        }

        .left-content h1 {

            font-size: 34px;

            margin-bottom: 15px;

        }

        .left-content p {

            color: rgba(255, 255, 255, .75);

            line-height: 28px;

        }

        .info {

            margin-top: 40px;

        }

        .item {

            background: rgba(255, 255, 255, .08);

            padding: 15px;

            border-radius: 14px;

            margin-bottom: 15px;

        }

        .item strong {

            display: block;

            margin-bottom: 5px;

        }

        /* PANEL DERECHO */

        .right-panel {

            flex: 1;

            display: flex;

            justify-content: center;

            align-items: center;

            padding: 40px;

        }

        .card {

            width: 420px;

        }

        h2 {

            color: #1e293b;

            font-size: 32px;

            margin-bottom: 8px;

        }

        .subtitle {

            color: #64748b;

            margin-bottom: 30px;

            font-size: 14px;

        }

        .group {

            margin-bottom: 18px;

        }

        label {

            display: block;

            margin-bottom: 8px;

            color: #374151;

            font-weight: 600;

            font-size: 14px;

        }

        .input {

            position: relative;

        }

        .input span {

            position: absolute;

            left: 15px;

            top: 50%;

            transform: translateY(-50%);

        }

        input {

            width: 100%;

            padding: 13px 15px 13px 45px;

            border: 1px solid #d1d5db;

            border-radius: 12px;

            background: #f9fafb;

            font-size: 15px;

            outline: none;

            transition: .2s;

        }

        input:focus {

            border-color: #2563eb;

            background: white;

            box-shadow: 0 0 0 4px rgba(37, 99, 235, .1);

        }

        button {

            width: 100%;

            padding: 14px;

            margin-top: 10px;

            border: none;

            border-radius: 12px;

            background: linear-gradient(135deg,
                    #1e3a8a,
                    #2563eb);

            color: white;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;

            transition: .2s;

        }

        button:hover {

            transform: translateY(-2px);

        }

        .divider {

            text-align: center;

            margin: 25px 0;

            color: #94a3b8;

            font-size: 13px;

        }

        .back {

            display: block;

            text-align: center;

            text-decoration: none;

            color: #1e3a8a;

            border: 1px solid #bfdbfe;

            padding: 12px;

            border-radius: 12px;

            font-weight: bold;

            transition: .2s;

        }

        .back:hover {

            background: #eff6ff;

        }

        @media(max-width:800px) {

            .left-panel {

                display: none;

            }

            .right-panel {

                padding: 25px;

            }

        }
    </style>

</head>

<body>

    <div class="left-panel">

        <div class="left-content">

            <div class="logo">

                🎓

            </div>

            <h1>Sistema BPM<br>UMSA</h1>

            <p>

                Plataforma para la gestión digital del trámite de
                Titulación Profesional de la Universidad Mayor de San Andrés.

            </p>

            <div class="info">

                <div class="item">

                    <strong>📋 Registro</strong>

                    Cree una cuenta para iniciar sus trámites.

                </div>

                <div class="item">

                    <strong>🔎 Seguimiento</strong>

                    Consulte el estado de su trámite en cualquier momento.

                </div>

                <div class="item">

                    <strong>🎓 Titulación</strong>

                    Gestión completamente digital mediante BPM.

                </div>

            </div>

        </div>

    </div>

    <div class="right-panel">

        <div class="card">

            <h2>Crear cuenta</h2>

            <div class="subtitle">

                Complete la siguiente información para registrarse.

            </div>

            <form action="guardar_registro.php" method="POST" onsubmit="return validarPasswords()">

                <div class="group">

                    <label>Nombre completo</label>

                    <div class="input">

                        <span>👤</span>

                        <input type="text" name="nombre" required>

                    </div>

                </div>

                <div class="group">

                    <label>Usuario</label>

                    <div class="input">

                        <span>🏷️</span>

                        <input type="text" name="usuario" required>

                    </div>

                </div>

                <div class="group">

                    <label>Contraseña</label>

                    <div class="input">

                        <span>🔒</span>

                        <input type="password" id="password" name="password" required>

                    </div>

                </div>

                <div class="group">

                    <label>Confirmar contraseña</label>

                    <div class="input">

                        <span>✅</span>

                        <input type="password" id="confirmPassword" required>

                    </div>

                </div>

                <button type="submit">

                    Crear Cuenta

                </button>

            </form>

            <div class="divider">

                ¿Ya tiene una cuenta?

            </div>

            <a href="login.php" class="back">

                ← Volver al inicio de sesión

            </a>

        </div>

    </div>

    <script>

        function validarPasswords() {

            let p1 = document.getElementById("password").value;

            let p2 = document.getElementById("confirmPassword").value;

            if (p1 !== p2) {

                alert("Las contraseñas no coinciden.");

                return false;

            }

            return true;

        }

    </script>

</body>

</html>