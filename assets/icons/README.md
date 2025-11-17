# Iconos SVG de Heroicons

Esta carpeta contiene los iconos SVG de Heroicons utilizados en el plugin.

## 📦 Librería: Heroicons

**Heroicons** es la librería oficial de Tailwind CSS, perfecta para diseños modernos estilo Vercel/Linear/Stripe/Apple.

- ✅ **Formato**: SVG (escalable y ligero)
- ✅ **Variante**: Outline (minimalista y moderna)
- ✅ **Fuente**: https://heroicons.com/

## 🎨 Iconos necesarios:

1. **photo.svg** - Icono de imágenes (24x24px)
2. **document-text.svg** - Icono de páginas/documentos (24x24px)
3. **cog-6-tooth.svg** - Icono de configuración (24x24px)
4. **chevron-left.svg** - Icono de anterior (24x24px)
5. **chevron-right.svg** - Icono de siguiente (24x24px)
6. **x-mark.svg** - Icono de cerrar (24x24px)
7. **check.svg** - Icono de checkmark (24x24px)
8. **arrow-top-right-on-square.svg** - Icono de enlace externo (16x16px)

## 📥 Descarga automática:

Los iconos se pueden descargar automáticamente usando:

```powershell
.\descargar-heroicons.ps1
```

Este script descarga todos los SVG necesarios desde la API de Iconify.

## 🔄 Fallback automático:

Si un icono SVG no existe localmente, el sistema automáticamente usará la API de Iconify como fallback, garantizando que los iconos siempre se muestren correctamente.

## ✨ Ventajas de usar SVG:

- **Escalabilidad**: Se ven perfectos en cualquier resolución (retina, 4K, etc.)
- **Tamaño**: Archivos más pequeños que PNG
- **Personalización**: Se pueden modificar con CSS (color, tamaño, etc.)
- **Calidad**: Siempre nítidos, sin pixelación
- **Moderno**: Estándar actual para iconos web

## 📝 Notas:

- Los SVG ya están descargados en esta carpeta
- Si necesitas actualizar los iconos, ejecuta el script de descarga nuevamente
- Los iconos usan la variante "outline" de Heroicons para un look más minimalista
