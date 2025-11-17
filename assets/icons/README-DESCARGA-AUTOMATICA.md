# 📥 Descarga Automática de Iconos PNG

Este directorio contiene scripts y herramientas para descargar y convertir automáticamente los iconos SVG de Lucide a formato PNG.

## 🚀 Método Rápido (Recomendado)

### Opción 1: Herramienta HTML (Más Fácil)

1. **Abrir la herramienta HTML:**
   ```powershell
   .\convertir-svg-a-png.html
   ```
   O simplemente haz doble clic en `convertir-svg-a-png.html`

2. **En el navegador:**
   - Haz clic en "Descargar PNG" para cada icono
   - Guarda cada archivo en esta misma carpeta (`assets/icons/`)
   - Asegúrate de usar los nombres exactos (ej: `image.png`, `file-text.png`)

### Opción 2: Script Automático Completo

```powershell
.\descargar-todo-automatico.ps1
```

Este script:
- ✅ Descarga los SVG automáticamente
- ✅ Convierte a PNG si ImageMagick está instalado
- ✅ O abre la herramienta HTML si ImageMagick no está disponible

## 📋 Iconos Requeridos

Los siguientes iconos deben estar en formato PNG:

- `image.png` (24x24)
- `file-text.png` (24x24)
- `settings.png` (24x24)
- `chevron-left.png` (24x24)
- `chevron-right.png` (24x24)
- `x.png` (24x24)
- `check.png` (24x24)
- `external-link.png` (16x16)

## 🛠️ Métodos Alternativos

### Si tienes ImageMagick instalado:

```powershell
# Descargar SVG primero
.\descargar-iconos.ps1

# Convertir a PNG
.\convertir-svg-a-png.ps1
```

### Si prefieres usar un servicio online:

1. Visita: https://cloudconvert.com/svg-to-png
2. Sube cada archivo `.svg` de esta carpeta
3. Configura el tamaño según el icono (24x24 o 16x16)
4. Descarga y guarda como `.png` en esta carpeta

## ✅ Verificación

Una vez descargados todos los PNG, verifica que existan:

```powershell
Get-ChildItem *.png
```

Deberías ver 8 archivos PNG:
- check.png
- chevron-left.png
- chevron-right.png
- external-link.png
- file-text.png
- image.png
- settings.png
- x.png

## 📝 Notas

- Los SVG ya están descargados en esta carpeta
- La herramienta HTML funciona directamente en el navegador sin necesidad de instalaciones adicionales
- Si tienes problemas con CORS al usar la herramienta HTML, abre el archivo desde un servidor local (ej: `http://localhost/variospluginswp/wp-content/plugins/slidercards3d/assets/icons/convertir-svg-a-png.html`)

