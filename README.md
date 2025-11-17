# Slider Cards 3D

Un plugin de WordPress moderno que crea un slider 3D interactivo con imágenes y páginas seleccionadas desde el panel de administración.

## Características

- 🎴 **Slider 3D Interactivo**: Efectos 3D suaves y modernos
- 🖼️ **Gestión de Imágenes**: Selecciona imágenes desde la biblioteca de medios
- 📄 **Gestión de Páginas**: Incluye páginas con sus imágenes destacadas
- 🎨 **UI Moderna**: Interfaz estilo Vercel/Linear/Stripe/Apple
- 📦 **Control de Versiones**: Sistema automático de versiones y backups
- 🔄 **API REST**: Endpoints para gestión de contenido
- 📱 **Responsive**: Diseño adaptativo para todos los dispositivos

## Instalación

1. Clona o descarga el plugin en tu directorio de plugins de WordPress:
   ```
   wp-content/plugins/slidercards3d/
   ```

2. Activa el plugin desde el panel de administración de WordPress

3. Ve a **Slider 3D** en el menú de administración

## Uso

### Panel de Administración

1. **Pestaña Imágenes**:
   - Visualiza todas las imágenes de tu biblioteca de medios
   - Selecciona las imágenes que quieres incluir en el slider
   - Haz clic en "Guardar selección"

2. **Pestaña Páginas**:
   - Visualiza todas tus páginas publicadas
   - Selecciona las páginas que quieres incluir en el slider
   - Las páginas mostrarán su imagen destacada
   - Haz clic en "Guardar selección"

### Frontend

Usa el shortcode en cualquier página o entrada:

```
[slidercards3d]
```

O especifica el tipo de contenido:

```
[slidercards3d type="images"]
[slidercards3d type="pages"]
[slidercards3d type="all"]
```

### Navegación

- **Teclado**: Usa las flechas izquierda/derecha para navegar
- **Mouse**: Haz clic en los botones de navegación
- **Touch**: Desliza en dispositivos móviles
- **Indicadores**: Haz clic en los puntos inferiores para saltar a una slide específica

## Estructura del Proyecto

```
slidercards3d/
├── slidercards3d.php          # Archivo principal del plugin
├── includes/
│   ├── class-admin.php        # Panel de administración
│   ├── class-frontend.php     # Frontend del slider
│   ├── class-api.php          # API REST
│   └── class-version-manager.php  # Gestor de versiones
├── assets/
│   ├── css/
│   │   ├── admin.css          # Estilos del admin
│   │   └── frontend.css        # Estilos del slider
│   └── js/
│       ├── admin.js           # JavaScript del admin
│       └── frontend.js        # JavaScript del slider
├── CHANGELOG.md               # Historial de cambios
└── README.md                  # Este archivo
```

## Control de Versiones

El plugin incluye un sistema automático de control de versiones:

- Cada cambio importante actualiza la versión
- Se crean backups automáticos en `wp-content/uploads/slidercards3d-backups/`
- El changelog se actualiza automáticamente en `CHANGELOG.md`

## Requisitos

- WordPress 5.0 o superior
- PHP 7.4 o superior
- MySQL 5.6 o superior

## Desarrollo

### Repositorio

- **GitHub**: https://github.com/fabiojara/slidercards3d.git
- **Local**: `C:\laragon\www\variospluginswp\wp-content\plugins\slidercards3d`

### Contribuir

1. Haz un fork del repositorio
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -am 'Agrega nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

## Licencia

GPL v2 or later

## Autor

**Fabio Jara**
- GitHub: [@fabiojara](https://github.com/fabiojara)

## Créditos

- Slider 3D inspirado en: [CodePen - Nidal95](https://codepen.io/Nidal95/pen/RNNgWNM)
- UI inspirada en: Vercel, Linear, Stripe, Apple

