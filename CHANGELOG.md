# Changelog

## [1.7.0] - 2024-12-XX

### Nuevas Características
- **Navegación con Teclado**: Soporte completo para navegación con flechas izquierda/derecha del teclado
- **Navegación Táctil Mejorada**: Swipe mejorado para dispositivos touch (móviles, tablets, escritorio táctil)
- **Mouse Drag**: Soporte para arrastrar con el mouse en pantallas táctiles de escritorio

### Mejoras
- Navegación con teclado funciona cuando el slider está visible en pantalla
- Detección inteligente de swipe horizontal vs vertical
- Prevención de scroll durante swipe horizontal
- Mejor respuesta táctil en botones de navegación
- Cursor grab/grabbing para indicar que el slider es arrastrable
- Evita conflictos con inputs activos al usar teclado
- Mejor accesibilidad con contenedor focusable

### Detalles Técnicos
- Implementada detección de visibilidad del slider antes de procesar teclas
- Swipe mejorado con umbrales configurables (distancia mínima: 50px, tiempo máximo: 500ms)
- `touch-action: pan-x pan-y` para mejor control de gestos
- `user-select: none` para prevenir selección de texto durante swipe
- Soporte para múltiples métodos de navegación simultáneos

## [1.6.0] - 2024-12-XX

### Mejoras
- Mejorado sistema automático de actualización del CHANGELOG.md
- El sistema ahora detecta si ya existe una entrada para una versión antes de agregar una nueva
- Limpieza automática de múltiples encabezados "# Changelog" duplicados
- El sistema respeta las ediciones manuales del CHANGELOG y no las sobrescribe
- CHANGELOG.md reorganizado y limpiado de duplicados

### Detalles Técnicos
- Implementada verificación de versiones existentes en `update_changelog()`
- Agregada limpieza automática de encabezados duplicados
- Mejorado el manejo de archivos CHANGELOG existentes

## [1.5.0] - 2024-12-XX

### Nuevas Características
- Agregado sistema de **Skeleton Screens** con efecto Shimmer para indicador de carga moderno
- Skeleton cards 3D que imitan la estructura del slider mientras carga
- Efecto shimmer animado (método moderno usado por Facebook, Instagram, LinkedIn)
- Persistencia de pestaña activa en el panel de administración usando localStorage

### Mejoras
- Indicador de carga mejorado: skeleton screens en lugar de spinner tradicional
- Mejor experiencia de usuario durante la carga del contenido
- Los controles del slider se ocultan automáticamente durante la carga
- Transición suave al ocultar el skeleton cuando el contenido está listo
- La pestaña activa se mantiene al recargar la página del administrador
- Skeleton cards responsive adaptados a diferentes tamaños de pantalla

### Detalles Técnicos
- Implementación de `showLoading()` y `hideLoading()` en la clase `SliderCards3DInstance`
- Animaciones CSS puras sin dependencias externas
- Uso de localStorage para persistencia de estado en el admin
- Skeleton cards con transformaciones 3D que imitan el slider real

## [1.4.0] - 2024-12-XX

### Nuevas Características
- Agregados controles de **Intensidad de Filtro** (0-100%) para imágenes no activas
- Agregados controles de **Intensidad de Brillo** (0-100%) para imágenes no activas
- Los nuevos controles funcionan tanto para sliders de imágenes como de páginas
- Sistema de filtros combinados: brightness, blur y contrast aplicados dinámicamente

### Mejoras
- Mejor control visual sobre las imágenes no activas en el slider
- Filtros CSS combinados para efectos más sofisticados:
  - **Brightness**: Controlado por `brightness_intensity` (0% = muy oscuro, 100% = brillo normal)
  - **Blur**: Controlado por `filter_intensity` (hasta 2px de desenfoque)
  - **Contrast**: Controlado por `filter_intensity` (reducción hasta 70%)
- Los controles se muestran en la pestaña "Información" del panel de administración
- Valores por defecto optimizados para mejor experiencia visual

### Detalles Técnicos
- Nuevos parámetros de configuración: `filter_intensity` y `brightness_intensity`
- Validación de rangos (0-100) para todos los controles de intensidad
- Aplicación de filtros CSS dinámicos basados en la distancia de la imagen activa
- Compatibilidad total con sliders de imágenes y páginas

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
