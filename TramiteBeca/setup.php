<?php
require_once __DIR__ . '/json_helper.php';

$usuarios = [
    [
        'usuario' => 'estudiante',
        'password' => password_hash('123456', PASSWORD_DEFAULT),
        'nombre' => 'Juan Pérez',
        'rol' => 'Estudiante',
        'ci' => '1234567',
        'ru' => '20240001',
        'carrera' => 'Informática',
        'facultad' => 'Facultad de Ciencias Puras'
    ],
    [
        'usuario' => 'bienestar',
        'password' => password_hash('123456', PASSWORD_DEFAULT),
        'nombre' => 'Lic. María García',
        'rol' => 'Bienestar Social'
    ],
    [
        'usuario' => 'trabajador',
        'password' => password_hash('123456', PASSWORD_DEFAULT),
        'nombre' => 'Lic. Carlos López',
        'rol' => 'Trabajador Social'
    ],
    [
        'usuario' => 'nutricionista',
        'password' => password_hash('123456', PASSWORD_DEFAULT),
        'nombre' => 'Dra. Ana Martínez',
        'rol' => 'Nutricionista'
    ],
    [
        'usuario' => 'comite',
        'password' => password_hash('123456', PASSWORD_DEFAULT),
        'nombre' => 'Dr. Roberto Vargas',
        'rol' => 'Comité BAERA'
    ]
];

guardar_json('usuarios.json', $usuarios);
guardar_json('tickets.json', []);
guardar_json('historial.json', []);
guardar_json('formularios_baera.json', []);

echo "Sistema inicializado correctamente.\n";
echo "Usuarios creados (contraseña: 123456):\n";
foreach ($usuarios as $u) {
    echo "  - {$u['usuario']} ({$u['rol']})\n";
}
