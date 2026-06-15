<?php
include "../helpers/auth.php";
verificarRol("ESTUDIANTE");
include "../helpers/json_helper.php";

$id = (int) ($_GET["id"] ?? 0);
$usuario = $_SESSION["usuario"];
$tramites = leerJson("../data/tramites.json");
$tramite = null;

foreach ($tramites as $t) {
    if ($t["id"] == $id) {
        $tramite = $t;
        break;
    }
}

if (
    !$tramite || $tramite["estudiante"] != $usuario["id"] ||
    !in_array($tramite["estado"], ["BORRADOR", "OBSERVADO"])
) {
    header("Location: mis_tramites.php");
    exit();
}

$carrerasConocidas = ["Informática", "Medicina", "Odontología", "Enfermería", "Informática"];
$esOtraCarrera = !in_array($tramite["carrera"], $carrerasConocidas);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Trámite #<?= $id ?> – UMSA</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-section {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(26, 82, 118, .08);
            padding: 28px 32px;
            margin-bottom: 24px;
        }

        .form-section h3 {
            color: #1A5276;
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            border-bottom: 2px solid #ebf5fb;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .form-group {
            flex: 1;
            min-width: 200px;
        }

        .form-group label {
            display: block;
            font-size: .85rem;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 6px;
        }

        .form-group select,
        .form-group input[type="text"] {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #d1d5db;
            border-radius: 10px;
            font-size: .95rem;
            color: #1a202c;
            background: #f9fafb;
            box-sizing: border-box;
            transition: border-color .2s;
        }

        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: #1A5276;
            background: #fff;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border: 1.5px solid #d1d5db;
            border-radius: 10px;
            background: #f9fafb;
            margin-bottom: 12px;
            cursor: pointer;
            transition: border-color .2s, background .2s;
        }

        .checkbox-group:hover {
            border-color: #1A5276;
            background: #ebf5fb;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #1A5276;
        }

        .checkbox-group span {
            font-size: .9rem;
            color: #2d3748;
        }

        #seccionOtraCarrera,
        #seccionModalidadPago {
            transition: all .25s ease;
        }

        #seccionOtraCarrera.oculto,
        #seccionModalidadPago.oculto {
            display: none;
        }

        .btn-guardar {
            background: linear-gradient(135deg, #1A5276, #2980b9);
            color: white;
            padding: 13px 32px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(26, 82, 118, .25);
        }

        .btn-guardar:hover {
            opacity: .92;
        }

        .badge-estado {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: .78rem;
            font-weight: 700;
            background:
                <?= $tramite["estado"] === "OBSERVADO" ? "#fff5f5" : "#fffbeb" ?>
            ;
            color:
                <?= $tramite["estado"] === "OBSERVADO" ? "#c53030" : "#b7791f" ?>
            ;
            border: 1.5px solid
                <?= $tramite["estado"] === "OBSERVADO" ? "#feb2b2" : "#f6e05e" ?>
            ;
        }
    </style>
</head>

<body>
    <div class="topbar">
        <div>🎓 Sistema BPM – UMSA</div>
        <div>
            <?= htmlspecialchars($usuario["nombre"]) ?>
            <a href="mis_tramites.php">← Mis Trámites</a>
            <a href="../auth/logout.php">Salir</a>
        </div>
    </div>

    <div class="contenido">
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;flex-wrap:wrap">
            <h2 style="color:#1A5276;margin:0">✏️ Editar Trámite #<?= $id ?></h2>
            <span class="badge-estado"><?= htmlspecialchars($tramite["estado"]) ?></span>
        </div>

        <?php if ($tramite["estado"] === "OBSERVADO"): ?>
            <div class="alerta alerta-warning" style="margin-bottom:20px">
                ⚠️ Este trámite tiene observaciones. Corrija la información y vuelva a enviarlo.
            </div>
        <?php endif; ?>

        <form action="actualizar_tramite.php" method="POST">
            <input type="hidden" name="id" value="<?= $tramite["id"] ?>">

            <!-- SECCIÓN 1: Información Académica -->
            <div class="form-section">
                <h3>📚 Información Académica</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Carrera *</label>
                        <select name="carrera" id="selectCarrera" required onchange="toggleOtraCarrera(this.value)">
                            <option value="">-- Seleccione --</option>
                            <option value="Informática" <?= $tramite["carrera"] === "Informática" && !$esOtraCarrera ? "selected" : "" ?>>Informática</option>
                            <option value="Medicina" <?= $tramite["carrera"] === "Medicina" ? "selected" : "" ?>>Medicina
                            </option>
                            <option value="Odontología" <?= $tramite["carrera"] === "Odontología" ? "selected" : "" ?>>
                                Odontología</option>
                            <option value="Enfermería" <?= $tramite["carrera"] === "Enfermería" ? "selected" : "" ?>>
                                Enfermería</option>
                            <option value="Informática" <?= $tramite["carrera"] === "Informática" && !$esOtraCarrera ? "selected" : "" ?>>Informática</option>
                            <option value="Otros" <?= $esOtraCarrera ? "selected" : "" ?>>Otros</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nivel Académico *</label>
                        <select name="nivel" required>
                            <option value="">-- Seleccione --</option>
                            <option value="Licenciatura" <?= $tramite["nivel"] === "Licenciatura" ? "selected" : "" ?>>
                                Licenciatura</option>
                            <option value="Técnico Superior" <?= $tramite["nivel"] === "Técnico Superior" ? "selected" : "" ?>>Técnico Universitario Superior</option>
                            <option value="Técnico Medio" <?= $tramite["nivel"] === "Técnico Medio" ? "selected" : "" ?>>
                                Técnico Universitario Medio</option>
                        </select>
                    </div>
                </div>

                <div class="form-group <?= $esOtraCarrera ? "" : "oculto" ?>" id="seccionOtraCarrera"
                    style="margin-top:14px">
                    <label>Especifique su carrera *</label>
                    <input type="text" name="otraCarrera" id="otraCarrera"
                        value="<?= $esOtraCarrera ? htmlspecialchars($tramite["carrera"]) : "" ?>"
                        placeholder="Ingrese el nombre de su carrera" maxlength="100" <?= $esOtraCarrera ? "required" : "" ?>>
                </div>
            </div>

            <!-- SECCIÓN 2: Condiciones Especiales -->
            <div class="form-section">
                <h3>⭐ Condiciones Especiales</h3>
                <label class="checkbox-group">
                    <input type="checkbox" name="excelencia" value="1" id="chkExcelencia"
                        <?= !empty($tramite["excelencia"]) ? "checked" : "" ?>
                        onchange="toggleModalidadPago(this.checked)">
                    <span>Graduado por Excelencia Académica (liberado de costo)</span>
                </label>
                <label class="checkbox-group">
                    <input type="checkbox" name="extranjero" value="1" <?= !empty($tramite["extranjero"]) ? "checked" : "" ?>>
                    <span>Estudiante Extranjero</span>
                </label>
            </div>

            <!-- SECCIÓN 3: Modalidad de Pago -->
            <div class="form-section <?= !empty($tramite["excelencia"]) ? "oculto" : "" ?>" id="seccionModalidadPago">
                <h3>💳 Modalidad de Pago</h3>
                <div class="form-group">
                    <label>Forma de pago</label>
                    <select name="modalidadPago" id="selectModalidad" <?= empty($tramite["excelencia"]) ? "required" : "" ?>>
                        <option value="">-- Seleccione --</option>
                        <option value="ONLINE" <?= ($tramite["modalidadPago"] ?? "") === "ONLINE" ? "selected" : "" ?>>
                            Online – tramitesenlinea.umsa.bo</option>
                        <option value="PRESENCIAL" <?= ($tramite["modalidadPago"] ?? "") === "PRESENCIAL" ? "selected" : "" ?>>Presencial – Caja Edificio Melissa</option>
                    </select>
                </div>
            </div>

            <div style="display:flex;gap:12px;align-items:center">
                <button type="submit" class="btn-guardar">💾 Guardar Cambios</button>
                <a href="mis_tramites.php"
                    style="background:#e2e8f0;color:#4a5568;padding:13px 24px;border-radius:10px;text-decoration:none;font-weight:600">Cancelar</a>
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