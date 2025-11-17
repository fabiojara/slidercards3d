# Solución de Problemas - Slider Cards 3D

## Problema: "No hay contenido" en el frontend

Si ves el mensaje "No hay contenido" después de seleccionar imágenes, sigue estos pasos:

### Paso 1: Verificar que guardaste la selección

1. Ve al panel de administración: **Slider 3D**
2. Selecciona las imágenes que quieres mostrar
3. **IMPORTANTE**: Haz clic en el botón **"Guardar selección"**
4. Espera a que aparezca el mensaje "Guardado"

### Paso 2: Limpiar caché

Si usas un plugin de caché (WP Super Cache, W3 Total Cache, etc.):

1. Ve a la configuración del plugin de caché
2. Limpia toda la caché
3. Recarga la página donde está el shortcode

### Paso 3: Verificar en la consola del navegador

1. Abre la página donde está el shortcode
2. Presiona **F12** para abrir las herramientas de desarrollador
3. Ve a la pestaña **"Console"**
4. Busca mensajes que empiecen con:
   - `Iniciando carga de items`
   - `Datos de selección de imágenes`
   - `Imágenes cargadas`
   - Cualquier error en rojo

### Paso 4: Verificar la API directamente

Abre esta URL en tu navegador (reemplaza `tudominio.com` con tu dominio):

```
http://tudominio.com/wp-json/slidercards3d/v1/selection?type=image
```

Deberías ver algo como:

```json
{
  "type": "image",
  "ids": [123, 456, 789],
  "count": 3
}
```

Si ves `"ids": []` o `"count": 0`, significa que no hay imágenes guardadas.

### Paso 5: Verificar la base de datos

Si tienes acceso a phpMyAdmin o similar:

1. Abre la base de datos de WordPress
2. Busca la tabla `wp_slidercards3d_selections` (el prefijo puede ser diferente)
3. Verifica que haya registros con `type = 'image'` y `selected = 1`

### Paso 6: Verificar permisos de la API REST

Abre esta URL:

```
http://tudominio.com/wp-json/
```

Deberías ver una lista de endpoints disponibles. Busca `slidercards3d/v1`.

## Problemas comunes y soluciones

### El slider no aparece

**Solución**: Verifica que el shortcode esté escrito correctamente: `[slidercards3d]`

### Las imágenes no se cargan

**Solución**:
1. Verifica que las imágenes existan en la biblioteca de medios
2. Verifica los permisos de archivos en `wp-content/uploads/`
3. Revisa la consola del navegador para errores 404

### Error 403 o 401 en la API

**Solución**:
1. Verifica que el plugin esté activado
2. Verifica los permisos de archivos del plugin
3. Desactiva y reactiva el plugin

### El slider aparece pero está vacío

**Solución**:
1. Asegúrate de haber guardado la selección
2. Limpia la caché del navegador (Ctrl+F5)
3. Verifica la consola del navegador

## Debugging avanzado

### Habilitar modo debug de WordPress

En `wp-config.php`, asegúrate de tener:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Luego revisa `wp-content/debug.log` para ver errores.

### Verificar que los assets se carguen

En la consola del navegador, verifica que estos archivos se carguen:

- `slidercards3d/assets/css/frontend.css`
- `slidercards3d/assets/js/frontend.js`

Si no se cargan, verifica los permisos de archivos.

## Contacto y soporte

Si el problema persiste:

1. Revisa los logs de WordPress (`wp-content/debug.log`)
2. Revisa la consola del navegador (F12)
3. Verifica que todas las dependencias estén instaladas
4. Verifica la versión de WordPress (requiere 5.0+)

