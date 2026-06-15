<?php
$guardado = $datos_proceso ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Datos socioeconómicos</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .card { background: white; border-radius: 8px; padding: 24px; max-width: 700px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #1a1a2e; margin-top: 0; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        label { display: block; margin-bottom: 4px; color: #333; font-weight: bold; font-size: 13px; }
        input, select, textarea { width: 100%; padding: 8px; margin-bottom: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 60px; resize: vertical; }
        .full { grid-column: 1 / -1; }
        .btn { width: 100%; padding: 10px; background: #1a1a2e; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #16213e; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Paso 2: Datos socioeconómicos</h2>
        <form method="POST">
            <input type="hidden" name="flujo" value="<?= $flujo_id ?>">
            <input type="hidden" name="proceso" value="<?= $proceso_id ?>">
            <input type="hidden" name="ticket" value="<?= $ticket ?>">
            <div class="form-row">
                <div>
                    <label>Dirección actual</label>
                    <input type="text" name="direccion" value="<?= htmlspecialchars($guardado['direccion'] ?? '') ?>" required>
                </div>
                <div>
                    <label>Zona</label>
                    <input type="text" name="zona" value="<?= htmlspecialchars($guardado['zona'] ?? '') ?>" required>
                </div>
                <div>
                    <label>Distrito</label>
                    <input type="text" name="distrito" value="<?= htmlspecialchars($guardado['distrito'] ?? '') ?>" required>
                </div>
                <div>
                    <label>Teléfono</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($guardado['telefono'] ?? '') ?>" required>
                </div>
                <div class="full">
                    <label>Correo electrónico</label>
                    <input type="email" name="correo" value="<?= htmlspecialchars($guardado['correo'] ?? '') ?>" required>
                </div>
                <div>
                    <label>N° integrantes del hogar</label>
                    <input type="number" name="integrantes_hogar" value="<?= htmlspecialchars($guardado['integrantes_hogar'] ?? '') ?>" required>
                </div>
                <div>
                    <label>N° dependientes</label>
                    <input type="number" name="dependientes" value="<?= htmlspecialchars($guardado['dependientes'] ?? '') ?>" required>
                </div>
                <div class="full">
                    <label>Ingreso económico familiar mensual (Bs)</label>
                    <input type="number" name="ingreso_familiar" value="<?= htmlspecialchars($guardado['ingreso_familiar'] ?? '') ?>" required>
                </div>
                <div>
                    <label>Tipo de vivienda</label>
                    <select name="tipo_vivienda">
                        <option value="propia" <?= ($guardado['tipo_vivienda'] ?? '') === 'propia' ? 'selected' : '' ?>>Propia</option>
                        <option value="alquilada" <?= ($guardado['tipo_vivienda'] ?? '') === 'alquilada' ? 'selected' : '' ?>>Alquilada</option>
                        <option value="anticretico" <?= ($guardado['tipo_vivienda'] ?? '') === 'anticretico' ? 'selected' : '' ?>>Anticrético</option>
                        <option value="prestada" <?= ($guardado['tipo_vivienda'] ?? '') === 'prestada' ? 'selected' : '' ?>>Prestada</option>
                        <option value="otro" <?= ($guardado['tipo_vivienda'] ?? '') === 'otro' ? 'selected' : '' ?>>Otro</option>
                    </select>
                </div>
                <div>
                    <label>Situación laboral del estudiante</label>
                    <select name="trabajo_estudiante">
                        <option value="trabaja" <?= ($guardado['trabajo_estudiante'] ?? '') === 'trabaja' ? 'selected' : '' ?>>Trabaja</option>
                        <option value="no_trabaja" <?= ($guardado['trabajo_estudiante'] ?? '') === 'no_trabaja' ? 'selected' : '' ?>>No trabaja</option>
                        <option value="trabajo_parcial" <?= ($guardado['trabajo_estudiante'] ?? '') === 'trabajo_parcial' ? 'selected' : '' ?>>Trabajo parcial</option>
                    </select>
                </div>
                <div class="full">
                    <label>Situación laboral de los padres o tutores</label>
                    <select name="trabajo_padres">
                        <option value="ambos_trabajan" <?= ($guardado['trabajo_padres'] ?? '') === 'ambos_trabajan' ? 'selected' : '' ?>>Ambos trabajan</option>
                        <option value="solo_uno_trabaja" <?= ($guardado['trabajo_padres'] ?? '') === 'solo_uno_trabaja' ? 'selected' : '' ?>>Solo uno trabaja</option>
                        <option value="ninguno_trabaja" <?= ($guardado['trabajo_padres'] ?? '') === 'ninguno_trabaja' ? 'selected' : '' ?>>Ninguno trabaja</option>
                        <option value="jubilados" <?= ($guardado['trabajo_padres'] ?? '') === 'jubilados' ? 'selected' : '' ?>>Jubilados</option>
                        <option value="otro" <?= ($guardado['trabajo_padres'] ?? '') === 'otro' ? 'selected' : '' ?>>Otro</option>
                    </select>
                </div>
                <div class="full">
                    <label>Observaciones adicionales</label>
                    <textarea name="observaciones_personales"><?= htmlspecialchars($guardado['observaciones_personales'] ?? '') ?></textarea>
                </div>
            </div>
            <button type="submit" name="siguiente" value="1" class="btn">Guardar y continuar</button>
        </form>
    </div>
</body>
</html>
