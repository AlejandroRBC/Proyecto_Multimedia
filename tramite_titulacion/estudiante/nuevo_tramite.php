<?php
include "../helpers/auth.php";
verificarRol("ESTUDIANTE");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuevo Trámite – UMSA</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
            max-width: 760px;
            margin: 0 auto;
            padding: 28px 24px;
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
            margin-bottom: 26px;
        }

        /* PROGRESS STEPS */
        .steps-bar {
            display: flex;
            align-items: center;
            gap: 0;
            margin-bottom: 28px;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }

        .step-dot {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e2e8f0;
            border: 2px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .78rem;
            font-weight: 800;
            color: #94a3b8;
            z-index: 1;
        }

        .step.active .step-dot {
            background: #1e3a8a;
            border-color: #1e3a8a;
            color: #fff;
        }

        .step.done .step-dot {
            background: #dbeafe;
            border-color: #3b82f6;
            color: #1e40af;
        }

        .step-label {
            font-size: .68rem;
            color: #94a3b8;
            margin-top: 5px;
            font-weight: 600;
            text-align: center;
        }

        .step.active .step-label {
            color: #1e3a8a;
        }

        .step::before {
            content: '';
            position: absolute;
            top: 15px;
            left: calc(-50% + 15px);
            right: calc(50% + 15px);
            height: 2px;
            background: #e2e8f0;
            z-index: 0;
        }

        .step:first-child::before {
            display: none;
        }

        /* FORM SECTION */
        .form-section {
            background: #fff;
            border-radius: 16px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
            padding: 24px 28px;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: .82rem;
            font-weight: 800;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: .07em;
            border-bottom: 2px solid #eff6ff;
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* FORM FIELDS */
        .form-row {
            display: flex;
            gap: 18px;
            flex-wrap: wrap;
        }

        .form-group {
            flex: 1;
            min-width: 200px;
        }

        .form-group label {
            display: block;
            font-size: .82rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-group label .req {
            color: #ef4444;
            margin-left: 2px;
        }

        .form-group select,
        .form-group input[type="text"],
        .form-group input[type="url"] {
            width: 100%;
            padding: 10px 13px;
            border: 1.5px solid #d1d5db;
            border-radius: 10px;
            font-size: .9rem;
            color: #1a202c;
            background: #f9fafb;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: #1e3a8a;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, .08);
        }

        .field-hint {
            font-size: .74rem;
            color: #94a3b8;
            margin-top: 5px;
        }

        /* CHECKBOX */
        .checkbox-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 13px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            background: #f9fafb;
            margin-bottom: 10px;
            cursor: pointer;
            transition: border-color .2s, background .2s;
        }

        .checkbox-item:hover {
            border-color: #1e3a8a;
            background: #eff6ff;
        }

        .checkbox-item input[type="checkbox"] {
            width: 17px;
            height: 17px;
            accent-color: #1e3a8a;
            margin-top: 1px;
            flex-shrink: 0;
        }

        .checkbox-item-text .cb-title {
            font-size: .88rem;
            font-weight: 600;
            color: #1e293b;
        }

        .checkbox-item-text .cb-desc {
            font-size: .77rem;
            color: #64748b;
            margin-top: 2px;
        }

        .badge-free {
            display: inline-block;
            background: #f0fdf4;
            color: #166534;
            border: 1.5px solid #86efac;
            border-radius: 20px;
            padding: 2px 10px;
            font-size: .72rem;
            font-weight: 700;
            margin-left: 8px;
        }

        /* HIDDEN */
        .oculto {
            display: none !important;
        }

        /* BUTTONS */
        .form-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 8px;
        }

        .btn-cancel {
            background: #f1f5f9;
            color: #475569;
            padding: 11px 24px;
            border: none;
            border-radius: 10px;
            font-size: .9rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background .2s;
        }

        .btn-cancel:hover {
            background: #e2e8f0;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: #fff;
            padding: 11px 32px;
            border: none;
            border-radius: 10px;
            font-size: .95rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(30, 58, 138, .3);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: transform .15s, box-shadow .15s;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(30, 58, 138, .4);
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
                <div class="topbar-avatar"><?= strtoupper(substr($_SESSION["usuario"]["nombre"], 0, 1)) ?></div>
                <?= htmlspecialchars($_SESSION["usuario"]["nombre"]) ?>
            </div>
            <a href="dashboard.php">← Dashboard</a>
            <a href="../auth/logout.php">Cerrar sesión</a>
        </div>
    </div>

    <div class="contenido">

        <div class="page-title">📋 Nuevo Trámite</div>
        <div class="page-sub">Complete los datos para iniciar su solicitud de Título Profesional.</div>

        <!-- PROGRESS BAR -->
        <div class="steps-bar">
            <div class="step active">
                <div class="step-dot">1</div>
                <div class="step-label">Información</div>
            </div>
            <div class="step">
                <div class="step-dot">2</div>
                <div class="step-label">Documentos</div>
            </div>
            <div class="step">
                <div class="step-dot">3</div>
                <div class="step-label">Confirmación</div>
            </div>
        </div>

        <form action="guardar_tramite.php" method="POST">

            <!-- SECCIÓN 1: Información Académica -->
            <div class="form-section">
                <div class="section-title">📚 Información Académica</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Carrera <span class="req">*</span></label>
                        <select name="carrera" id="selectCarrera" required onchange="toggleOtraCarrera(this.value)">
                            <option value="">— Seleccione su carrera —</option>
                            <option value="Informática">Informática</option>
                            <option value="Medicina">Medicina</option>
                            <option value="Odontología">Odontología</option>
                            <option value="Enfermería">Enfermería</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nivel Académico <span class="req">*</span></label>
                        <select name="nivel" required>
                            <option value="">— Seleccione —</option>
                            <option value="Licenciatura">Licenciatura</option>
                            <option value="Técnico Superior">Técnico Universitario Superior</option>
                            <option value="Técnico Medio">Técnico Universitario Medio</option>
                        </select>
                    </div>
                </div>

                <div class="form-group oculto" id="seccionOtraCarrera" style="margin-top:16px">
                    <label>Especifique su carrera <span class="req">*</span></label>
                    <input type="text" name="otraCarrera" id="otraCarrera" placeholder="Ingrese el nombre de su carrera"
                        maxlength="100">
                </div>
            </div>

            <!-- SECCIÓN 2: Condiciones Especiales -->
            <div class="form-section">
                <div class="section-title">⭐ Condiciones Especiales</div>

                <label class="checkbox-item">
                    <input type="checkbox" name="excelencia" value="1" id="chkExcelencia"
                        onchange="toggleModalidadPago(this.checked)">
                    <div class="checkbox-item-text">
                        <div class="cb-title">Graduado por Excelencia Académica
                            <span class="badge-free">★ Gratuito</span>
                        </div>
                        <div class="cb-desc">Exonerado del costo de titulación por mérito académico.</div>
                    </div>
                </label>

                <label class="checkbox-item">
                    <input type="checkbox" name="extranjero" value="1">
                    <div class="checkbox-item-text">
                        <div class="cb-title">Estudiante Extranjero</div>
                        <div class="cb-desc">Marque si usted es ciudadano de otro país.</div>
                    </div>
                </label>
            </div>

            <!-- SECCIÓN 3: Modalidad de Pago -->
            <div class="form-section" id="seccionModalidadPago">
                <div class="section-title">💳 Modalidad de Pago</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Forma de pago <span class="req">*</span></label>
                        <select name="modalidadPago" id="selectModalidad">
                            <option value="">— Seleccione —</option>
                            <option value="ONLINE">Online – tramitesenlinea.umsa.bo</option>
                            <option value="PRESENCIAL">Presencial – Caja Edificio Melissa</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Enlace del comprobante de pago</label>
                        <input type="url" name="comprobantePago" id="comprobantePago"
                            placeholder="https://drive.google.com/...">
                        <div class="field-hint">🔗 Pegue el enlace de su comprobante (Google Drive, Dropbox, etc.)</div>
                    </div>
                </div>
            </div>

            <!-- ACCIONES -->
            <div class="form-actions">
                <a href="dashboard.php" class="btn-cancel">Cancelar</a>
                <button type="submit" class="btn-primary">Continuar →</button>
            </div>

        </form>
    </div>

    <script>
        function toggleOtraCarrera(valor) {
            const seccion = document.getElementById('seccionOtraCarrera');
            const input = document.getElementById('otraCarrera');
            if (valor === 'Otros') {
                seccion.classList.remove('oculto');
                input.required = true;
            } else {
                seccion.classList.add('oculto');
                input.required = false;
                input.value = '';
            }
        }

        function toggleModalidadPago(esExcelencia) {
            const seccion = document.getElementById('seccionModalidadPago');
            const selectM = document.getElementById('selectModalidad');
            if (esExcelencia) {
                seccion.classList.add('oculto');
                selectM.required = false;
            } else {
                seccion.classList.remove('oculto');
                selectM.required = true;
            }
        }
    </script>
</body>

</html>