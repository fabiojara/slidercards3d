# Slider Cards 3D - Plugin para WordPress

Un slider 3D moderno para WordPress con gestión de imágenes y páginas desde el panel de administración.

## 📋 Características

- **Slider 3D Interactivo**: Efecto 3D con transformaciones CSS y navegación fluida
- **Gestión de Contenido**: Selección de imágenes desde la biblioteca de medios y páginas con imágenes destacadas
- **Panel de Administración Moderno**: Interfaz estilo Vercel/Linear/Stripe/Apple
- **Configuración Flexible**:
  - Separación horizontal configurable (Desktop, Tablet, Mobile)
  - Autoplay configurable con intervalo personalizable
  - Control de intensidad de oscurecimiento de imágenes laterales
- **Slider Infinito**: Navegación circular sin fin
- **Lightbox Moderno**: Visualización ampliada de imágenes con zoom
- **Múltiples Instancias**: Soporte para múltiples sliders en la misma página
- **Responsive**: Adaptación automática a diferentes tamaños de pantalla
- **Iconos SVG**: Sistema de iconos Heroicons con fallbacks automáticos

## 🚀 Instalación

1. Descarga o clona el repositorio en la carpeta de plugins de WordPress:
   ```
   wp-content/plugins/slidercards3d
   ```

2. Activa el plugin desde el panel de administración de WordPress

3. Ve a **Slider 3D** en el menú de WordPress para configurar el contenido

## 📖 Uso

### Shortcode Básico

```
[slidercards3d]
```

### Parámetros Disponibles

- `type`: Especifica qué tipo de contenido mostrar
  - `all` (por defecto) - Muestra imágenes y páginas seleccionadas
  - `images` - Solo muestra imágenes seleccionadas
  - `pages` - Solo muestra páginas seleccionadas

### Ejemplos

```
[slidercards3d]
[slidercards3d type="images"]
[slidercards3d type="pages"]
[slidercards3d type="all"]
```

### Uso en Templates PHP

```php
<?php echo do_shortcode('[slidercards3d]'); ?>
```

## ⚙️ Configuración

### Separación Horizontal

Configura la separación entre las tarjetas del slider para diferentes dispositivos:
- **Desktop**: Separación para pantallas grandes
- **Tablet**: Separación para tablets
- **Mobile**: Separación para móviles

### Autoplay

- Activa/desactiva la reproducción automática del slider
- Configura el intervalo entre transiciones (en milisegundos)

### Efectos Visuales

- **Intensidad de Oscurecimiento**: Controla qué tan oscuras se ven las imágenes detrás de la imagen principal (0-100%)

## 🎨 Características Técnicas

### Estructura del Plugin

```
slidercards3d/
├── slidercards3d.php          # Archivo principal del plugin
├── includes/
│   ├── class-admin.php       # Panel de administración
│   ├── class-frontend.php    # Renderizado frontend
│   ├── class-icons.php       # Gestión de iconos SVG
│   ├── class-rest-api.php    # API REST para gestión
│   └── class-database.php    # Gestión de base de datos
├── assets/
│   ├── css/
│   │   ├── admin.css         # Estilos del panel de administración
│   │   └── frontend.css      # Estilos del slider frontend
│   ├── js/
│   │   ├── admin.js          # JavaScript del panel de administración
│   │   └── frontend.js       # JavaScript del slider frontend
│   └── icons/                # Iconos SVG de Heroicons
├── CHANGELOG.md              # Historial de cambios
└── README.md                 # Este archivo
```

### Tecnologías Utilizadas

- **PHP**: WordPress Plugin API, REST API
- **JavaScript**: ES6 Classes, Fetch API, Promises
- **CSS**: CSS3 Transforms, 3D Transforms, Flexbox, Grid
- **SVG**: Heroicons Outline para iconos
- **WordPress**: Hooks, Shortcodes, Admin Menus, Media Library API

### Base de Datos

El plugin crea una tabla personalizada para almacenar las selecciones:
- `wp_slidercards3d_selections`: Almacena las selecciones de imágenes y páginas

### API REST

Endpoints disponibles:
- `GET /wp-json/slidercards3d/v1/selection?type={image|page}`: Obtener selecciones
- `POST /wp-json/slidercards3d/v1/selection`: Guardar selecciones
- `GET /wp-json/slidercards3d/v1/settings`: Obtener configuración
- `POST /wp-json/slidercards3d/v1/settings`: Guardar configuración

## 🔧 Desarrollo

### Requisitos

- WordPress 5.0+
- PHP 7.4+
- Navegadores modernos (Chrome, Firefox, Safari, Edge)

### Control de Versiones

El plugin utiliza Git para control de versiones. Cada versión incluye:
- Actualización de versión en `slidercards3d.php`
- Entrada en `CHANGELOG.md`
- Commit y push a GitHub

### Iconos

Los iconos utilizan Heroicons (Outline) en formato SVG:
- Se cargan desde archivos locales en `assets/icons/`
- Fallback automático a Iconify API si no se encuentran localmente
- Sistema de renderizado inline SVG con `currentColor` para herencia de color

### Múltiples Instancias

El plugin soporta múltiples instancias del shortcode en la misma página:
- Cada instancia tiene un ID único (`data-instance-id`)
- Estado independiente por instancia
- Eventos manejados de forma independiente

## 📝 Changelog

Ver [CHANGELOG.md](CHANGELOG.md) para el historial completo de cambios.

## 🤝 Contribuir

1. Fork el repositorio
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este plugin está licenciado bajo GPL v2 o posterior.

## 👤 Autor

**Fabio Jara**
- GitHub: [@fabiojara](https://github.com/fabiojara)
- Repositorio: [slidercards3d](https://github.com/fabiojara/slidercards3d)

## 🙏 Agradecimientos

- [Heroicons](https://heroicons.com/) por los iconos SVG
- [Iconify](https://iconify.design/) por la API de iconos
- Inspiración del slider 3D: [CodePen - Nidal95](https://codepen.io/Nidal95/pen/RNNgWNM)
