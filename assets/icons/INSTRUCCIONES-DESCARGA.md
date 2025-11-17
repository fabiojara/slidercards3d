# Instrucciones para Descargar Iconos PNG de Lucide

## Método 1: Descarga Manual (Recomendado)

1. Visita: https://lucide.dev/icons/
2. Busca cada icono por nombre y descárgalo en formato PNG:
   - **image** - Icono de imagen
   - **file-text** - Icono de documento/página
   - **settings** - Icono de configuración
   - **chevron-left** - Flecha izquierda
   - **chevron-right** - Flecha derecha
   - **x** - Icono de cerrar
   - **check** - Icono de checkmark
   - **external-link** - Icono de enlace externo

3. Guarda cada icono en esta carpeta (`assets/icons/`) con el nombre exacto:
   - `image.png` (24x24px)
   - `file-text.png` (24x24px)
   - `settings.png` (24x24px)
   - `chevron-left.png` (24x24px)
   - `chevron-right.png` (24x24px)
   - `x.png` (24x24px)
   - `check.png` (24x24px)
   - `external-link.png` (16x16px)

## Método 2: Usar un Convertidor Online

1. Visita: https://lucide.dev/icons/
2. Descarga cada icono en formato SVG
3. Convierte SVG a PNG usando:
   - https://cloudconvert.com/svg-to-png
   - https://convertio.co/svg-png/
   - https://svgtopng.com/

4. Configura el tamaño:
   - 24x24px para la mayoría de iconos
   - 16x16px para external-link

5. Guarda los PNG en esta carpeta con los nombres indicados arriba

## Método 3: Usar npm (si tienes Node.js)

```bash
npm install -g lucide
npx lucide export --format png --size 24 --out ./assets/icons
```

Luego renombra los archivos según los nombres requeridos.

## Verificación

Una vez descargados, verifica que todos los archivos existan:
- ✅ image.png
- ✅ file-text.png
- ✅ settings.png
- ✅ chevron-left.png
- ✅ chevron-right.png
- ✅ x.png
- ✅ check.png
- ✅ external-link.png

## Nota

Si los archivos PNG no están disponibles localmente, el sistema usará temporalmente los SVG desde la API de Iconify hasta que los descargues.

