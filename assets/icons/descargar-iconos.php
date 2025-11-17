<?php
/**
 * Script para descargar y convertir iconos PNG de Lucide automáticamente
 * 
 * Ejecutar desde navegador: http://localhost/variospluginswp/wp-content/plugins/slidercards3d/assets/icons/descargar-iconos.php
 * O desde línea de comandos: php descargar-iconos.php
 */

// Permitir ejecución desde navegador (solo para desarrollo)
if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Descargar Iconos</title></head><body><pre>';
}

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
$success_count = 0;
$error_count = 0;

echo "=== Descargando iconos PNG de Lucide ===\n\n";

foreach ($icons as $icon_name => $size) {
    echo "Procesando: {$icon_name}.png ({$size}x{$size})... ";
    
    // URL del SVG desde Iconify
    $svg_url = $base_url . $icon_name . '.svg?width=' . $size . '&height=' . $size . '&color=%23000000';
    
    // Descargar SVG
    $svg_content = @file_get_contents($svg_url);
    
    if ($svg_content === false) {
        echo "ERROR: No se pudo descargar el SVG\n";
        $error_count++;
        continue;
    }
    
    // Guardar SVG temporalmente
    $temp_svg = $save_dir . '/temp_' . $icon_name . '.svg';
    file_put_contents($temp_svg, $svg_content);
    
    // Intentar convertir usando ImageMagick
    $png_path = $save_dir . '/' . $icon_name . '.png';
    $converted = false;
    
    // Método 1: ImageMagick (si está disponible)
    if (extension_loaded('imagick')) {
        try {
            $imagick = new Imagick();
            $imagick->setBackgroundColor(new ImagickPixel('transparent'));
            $imagick->readImage($temp_svg);
            $imagick->setImageFormat('png');
            $imagick->resizeImage($size, $size, Imagick::FILTER_LANCZOS, 1);
            $imagick->writeImage($png_path);
            $imagick->clear();
            $imagick->destroy();
            $converted = true;
            echo "✓ Convertido con ImageMagick\n";
            $success_count++;
        } catch (Exception $e) {
            echo "⚠ ImageMagick falló: " . $e->getMessage() . "\n";
        }
    }
    
    // Método 2: Usar servicio online de conversión
    if (!$converted) {
        // Usar un servicio que convierta SVG a PNG
        // Por ahora, guardamos el SVG y proporcionamos instrucciones
        echo "⚠ ImageMagick no disponible. Usando método alternativo...\n";
        
        // Intentar usar un servicio de conversión online
        $conversion_url = 'https://api.iconify.design/lucide/' . $icon_name . '.svg?download=true&width=' . $size . '&height=' . $size;
        
        // Como fallback, guardamos el SVG y el usuario puede convertirlo manualmente
        // O podemos usar un servicio como cloudconvert API (requiere API key)
        
        // Por ahora, vamos a crear un PNG básico desde el SVG usando GD si está disponible
        if (extension_loaded('gd')) {
            // GD no puede leer SVG directamente, así que esto no funcionará
            // Necesitamos ImageMagick o un servicio externo
        }
        
        // Guardar el SVG como referencia
        $svg_save_path = $save_dir . '/' . $icon_name . '.svg';
        file_put_contents($svg_save_path, $svg_content);
        echo "  SVG guardado en: {$icon_name}.svg\n";
        echo "  Convierte manualmente a PNG usando: https://cloudconvert.com/svg-to-png\n";
        $error_count++;
    }
    
    // Limpiar archivo temporal
    if (file_exists($temp_svg)) {
        unlink($temp_svg);
    }
}

echo "\n=== Resumen ===\n";
echo "✓ Iconos descargados exitosamente: {$success_count}\n";
echo "⚠ Iconos que requieren conversión manual: {$error_count}\n\n";

if ($error_count > 0) {
    echo "Para convertir los SVG restantes a PNG:\n";
    echo "1. Visita: https://cloudconvert.com/svg-to-png\n";
    echo "2. Sube cada archivo .svg de esta carpeta\n";
    echo "3. Configura el tamaño según el icono\n";
    echo "4. Descarga y guarda como .png en esta misma carpeta\n\n";
}

echo "¡Proceso completado!\n";

if (php_sapi_name() !== 'cli') {
    echo '</pre></body></html>';
}

