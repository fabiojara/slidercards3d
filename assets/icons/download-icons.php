<?php
/**
 * Script para descargar iconos PNG de Lucide
 *
 * Ejecutar desde la línea de comandos:
 * php download-icons.php
 *
 * O visitar desde el navegador (solo para desarrollo)
 */

// Lista de iconos necesarios
$icons = array(
    'image' => 24,
    'file-text' => 24,
    'settings' => 24,
    'chevron-left' => 24,
    'chevron-right' => 24,
    'x' => 24,
    'check' => 24,
    'external-link' => 16
);

$base_url = 'https://api.iconify.design/lucide/';
$save_dir = __DIR__;

echo "Descargando iconos PNG de Lucide...\n\n";

foreach ($icons as $icon_name => $size) {
    // Usar un servicio que convierta SVG a PNG
    // Opción 1: Usar Iconify con conversión a PNG
    $svg_url = $base_url . $icon_name . '.svg?width=' . $size . '&height=' . $size;

    // Descargar SVG primero
    $svg_content = @file_get_contents($svg_url);

    if ($svg_content) {
        // Guardar SVG temporalmente
        $temp_svg = $save_dir . '/temp_' . $icon_name . '.svg';
        file_put_contents($temp_svg, $svg_content);

        // Convertir SVG a PNG usando ImageMagick si está disponible
        if (extension_loaded('imagick')) {
            $imagick = new Imagick();
            $imagick->setBackgroundColor(new ImagickPixel('transparent'));
            $imagick->readImage($temp_svg);
            $imagick->setImageFormat('png');
            $imagick->resizeImage($size, $size, Imagick::FILTER_LANCZOS, 1);
            $png_path = $save_dir . '/' . $icon_name . '.png';
            $imagick->writeImage($png_path);
            $imagick->clear();
            $imagick->destroy();
            unlink($temp_svg);
            echo "✓ Descargado: {$icon_name}.png ({$size}x{$size})\n";
        } else {
            // Si ImageMagick no está disponible, usar un servicio online
            echo "⚠ ImageMagick no disponible. Usa un convertidor online para {$icon_name}.svg\n";
            echo "   URL: {$svg_url}\n";
            echo "   Guarda como: {$icon_name}.png ({$size}x{$size})\n\n";
        }
    } else {
        echo "✗ Error al descargar: {$icon_name}\n";
    }
}

echo "\n¡Proceso completado!\n";
echo "Si algunos iconos no se descargaron, visita https://lucide.dev/icons/ y descárgalos manualmente.\n";

