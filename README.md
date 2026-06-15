# Proyecto Multimedia

Tres tareas unificadas en una sola página web:

- **Baile Sincronizado** — Juego interactivo en Unity WebGL
- **Fotogrametria 3D** — Visualizador de modelo 3D (GLB) con model-viewer
- **TramiteBeca** — Sistema de flujo de solicitud de becas (PHP + diagrama Mermaid)

---

## Descargar el proyecto

```bash
git clone https://github.com/AlejandroRBC/Proyecto_Multimedia.git
cd Proyecto_Multimedia
```

---

## Ver la pagina principal (Unity, Fotogrametria, diagrama)

Las partes estaticas (pagina principal, Unity WebGL, visor 3D y diagrama de flujo) se pueden servir con cualquier servidor estatico.

### Python

```bash
python -m http.server 8000
```

Luego abre http://localhost:8000 en tu navegador.

### Node.js

```bash
npx serve .
```

### VS Code (Live Server)

Instala la extension **Live Server** y haz clic derecho en `index.html` > "Open with Live Server".

---

## TramiteBeca — Version funcional con PHP

El módulo **TramiteBeca** requiere PHP y un servidor Apache (XAMPP). Los archivos PHP interactúan con archivos JSON locales para el flujo de solicitud de becas.

### Con XAMPP

1. Descarga e instala [XAMPP](https://www.apachefriends.org/)
2. Copia la carpeta `TramiteBeca` dentro de `C:\xampp\htdocs\`:
   ```
   C:\xampp\htdocs\TramiteBeca\
   ```
3. Abre el Panel de Control de XAMPP y activa **Apache**
4. Accede en tu navegador:
   ```
   http://localhost/TramiteBeca/login.php
   ```

### Usuarios de prueba (segun `data/usuarios.json`)

| Usuario | Contraseña | Rol |
|---------|-----------|-----|
| admin | 1234 | Administrador |
| bienestar | 1234 | Bienestar Social |
| trabajador | 1234 | Trabajador Social |
| nutricionista | 1234 | Nutricionista |
| comite | 1234 | Comité BAERA |

---

## GitHub Pages

La página principal (con Unity, Fotogrametría y diagrama de flujo) está publicada en:

```
https://alejandrorbc.github.io/Proyecto_Multimedia/
```

---

## Estructura del proyecto

```
Proyecto_Multimedia/
├── index.html                     # Página principal con navbar
├── .gitignore
├── README.md
│
├── Baile Sincronizado/            # Unity WebGL
│   ├── index.html
│   ├── Build/
│   └── TemplateData/
│
├── Fotogrametria/                 # Fotogrametría 3D
│   ├── paginaweb.html
│   └── mi_modelo.glb
│
└── TramiteBeca/                   # Sistema de becas (PHP)
    ├── flujo.html                 # Diagrama de flujo (estático)
    ├── login.php
    ├── setup.php
    ├── bandeja.php
    ├── nuevo_tramite.php
    ├── controlador.php
    ├── json_helper.php
    ├── logout.php
    ├── usuarios.php
    ├── data/                      # Archivos JSON
    ├── pantallas/                 # Pantallas del flujo
    └── uploads/                   # Archivos subidos
```
