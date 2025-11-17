# Changelog


## [1.3.0] - 2025-11-17

### Cambios
- Actualización de versión de 1.2.0 a 1.3.0
- Mejoras y correcciones generales

# Changelog


## [1.2.0] - 2025-11-17

### Cambios
- Actualización de versión de 1.0.0 a 1.2.0
- Mejoras y correcciones generales

# Changelog

## [1.3.0] - 2024-12-XX

### Mejoras
- Sistema mejorado de carga y renderizado de iconos SVG en backend y frontend
- Iconos SVG ahora usan `currentColor` para heredar el color del texto automáticamente
- Reemplazo automático de colores hardcodeados en SVG por `currentColor`
- Iconos de check mejorados con `stroke: white !important` para mejor visibilidad
- Manejo mejorado de atributos `fill` y `stroke` en iconos SVG
- Estilos CSS mejorados para renderizado correcto de todos los iconos
- Fallbacks robustos para carga de iconos (local → Iconify API)
- Función `loadIconSVG()` en frontend con carga asíncrona y múltiples fallbacks
- Documentación completa del estado actual del plugin

### Correcciones
- Corregido renderizado de iconos SVG en todas las pestañas del administrador
- Corregido color de iconos de check para que se vean blancos cuando están seleccionados
- Corregido manejo de colores hardcodeados en archivos SVG
- Mejorado sistema de fallbacks para iconos que no se encuentran localmente

## [1.2.0] - 2024-12-XX

### Nuevas Características
- Agregada pestaña "Modo de Uso" en el panel de administración con documentación completa del shortcode
- Botones de copiar en los ejemplos de código para facilitar el uso
- Versión del plugin visible en el subtítulo del header

### Mejoras
- Actualización de versión del plugin a 1.2.0
- Mejora en la documentación del shortcode con ejemplos prácticos
- Interfaz mejorada para la pestaña de documentación

## [1.0.0] - 2024-01-01

### Inicial
- Versión inicial del plugin Slider Cards 3D
- Panel de administración con pestañas para Imágenes y Páginas
- Sistema de selección de contenido desde la biblioteca de medios y páginas
- Slider 3D interactivo en el frontend
- Sistema de versiones y backups automáticos
- API REST para gestión de selecciones
- Interfaz moderna estilo Vercel/Linear/Stripe/Apple
- Configuración de separación horizontal (Desktop, Tablet, Mobile)
- Sistema de autoplay configurable
- Control de intensidad de oscurecimiento
- Lightbox moderno para imágenes activas
- Slider infinito con navegación circular
- Iconos Heroicons (SVG) para diseño moderno

