# Guía de Uso del Shortcode - Slider Cards 3D

## Shortcode Básico

El shortcode principal es `[slidercards3d]` y puede usarse en cualquier página, entrada o widget de WordPress.

### Uso Simple

```
[slidercards3d]
```

Este shortcode mostrará todas las imágenes y páginas que hayas seleccionado en el panel de administración.

## Parámetros Disponibles

### type

Especifica qué tipo de contenido mostrar en el slider.

**Valores posibles:**
- `all` (por defecto) - Muestra imágenes y páginas seleccionadas
- `images` - Solo muestra imágenes seleccionadas
- `pages` - Solo muestra páginas seleccionadas

### Ejemplos de Uso

#### 1. Mostrar Todo (Imágenes + Páginas)
```
[slidercards3d]
```
o
```
[slidercards3d type="all"]
```

#### 2. Solo Imágenes
```
[slidercards3d type="images"]
```

#### 3. Solo Páginas
```
[slidercards3d type="pages"]
```

## Dónde Usar el Shortcode

### En el Editor de WordPress (Gutenberg)

1. Agrega un bloque **"Shortcode"** o **"Código corto"**
2. Escribe: `[slidercards3d]`
3. Guarda y visualiza

### En el Editor Clásico

1. Simplemente pega el shortcode en el contenido:
   ```
   [slidercards3d]
   ```

### En Widgets

1. Ve a **Apariencia → Widgets**
2. Agrega un widget de **"Texto"** o **"HTML"**
3. Inserta el shortcode: `[slidercards3d]`

### En Templates PHP

Si necesitas insertarlo directamente en un template PHP:

```php
<?php echo do_shortcode('[slidercards3d]'); ?>
```

O con parámetros:

```php
<?php echo do_shortcode('[slidercards3d type="images"]'); ?>
```

### En Funciones PHP (programáticamente)

```php
// En tu functions.php o plugin personalizado
function mostrar_mi_slider() {
    return do_shortcode('[slidercards3d type="images"]');
}
```

## Ejemplos Prácticos

### Ejemplo 1: Slider en la Página de Inicio

Crea una página llamada "Inicio" y agrega:

```
Bienvenido a nuestro sitio

[slidercards3d type="all"]

Descubre más sobre nosotros...
```

### Ejemplo 2: Slider Solo de Imágenes en una Galería

Crea una página "Galería" y agrega:

```
# Nuestra Galería

[slidercards3d type="images"]
```

### Ejemplo 3: Slider de Páginas Destacadas

En una página "Destacados":

```
# Páginas Destacadas

[slidercards3d type="pages"]
```

### Ejemplo 4: Múltiples Sliders en la Misma Página

```
# Galería de Imágenes
[slidercards3d type="images"]

# Páginas Destacadas
[slidercards3d type="pages"]
```

## Personalización con CSS

Si necesitas personalizar el estilo del slider, puedes agregar CSS personalizado:

```css
/* En Apariencia → Personalizar → CSS Adicional */

/* Cambiar altura del slider */
.slidercards3d-wrapper {
    height: 500px !important;
}

/* Personalizar botones de navegación */
.slidercards3d-btn-prev,
.slidercards3d-btn-next {
    background: #ff0000 !important;
}

/* Personalizar tamaño de las tarjetas */
.slidercards3d-card {
    width: 350px !important;
    height: 450px !important;
}
```

## Solución de Problemas

### El slider no aparece

1. Verifica que hayas seleccionado contenido en el panel de administración
2. Asegúrate de haber guardado la selección haciendo clic en "Guardar selección"
3. Verifica que el shortcode esté escrito correctamente: `[slidercards3d]`

### El slider aparece vacío

1. Ve al panel de administración: **Slider 3D**
2. Selecciona imágenes o páginas en las pestañas correspondientes
3. Haz clic en "Guardar selección"
4. Recarga la página donde está el shortcode

### Los estilos no se cargan

1. Verifica que el plugin esté activado
2. Limpia la caché de WordPress si usas un plugin de caché
3. Verifica la consola del navegador para errores de JavaScript

## Notas Importantes

- El slider solo mostrará el contenido que hayas seleccionado y guardado en el panel de administración
- Si no hay contenido seleccionado, se mostrará un mensaje indicando que no hay contenido
- El slider es responsive y se adapta automáticamente a diferentes tamaños de pantalla
- La navegación funciona con teclado (flechas), mouse (botones) y touch (deslizar) en móviles

