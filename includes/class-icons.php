<?php
/**
 * Gestor de iconos Lucide en formato PNG
 */

if (!defined('ABSPATH')) {
    exit;
}

class SliderCards3D_Icons {

    private static $iconify_api = 'https://api.iconify.design/lucide/';

    /**
     * Obtener URL del icono
     */
    public static function get_icon_url($icon_name, $size = 24, $color = 'currentColor') {
        $local_path = SLIDERCARDS3D_PLUGIN_DIR . 'assets/icons/' . $icon_name . '.png';
        $local_url = SLIDERCARDS3D_PLUGIN_URL . 'assets/icons/' . $icon_name . '.png';

        // Si existe el archivo local, usarlo
        if (file_exists($local_path)) {
            return $local_url;
        }

        // Si no existe, usar API de Iconify para generar PNG
        return self::get_iconify_url($icon_name, $size, $color);
    }

    /**
     * Obtener URL desde Iconify API (servirá como fallback temporal)
     * Nota: Iconify sirve SVG, pero podemos usar un servicio de conversión
     */
    private static function get_iconify_url($icon_name, $size = 24, $color = 'currentColor') {
        // Usar SimpleIcons o similar que puede servir PNG
        // Por ahora, retornar SVG como fallback hasta que se descarguen los PNG
        $color_hex = self::color_to_hex($color);
        // Usar un servicio que convierta SVG a PNG en tiempo real
        // Por ahora, retornamos el SVG como fallback temporal
        return self::$iconify_api . $icon_name . '.svg?color=' . urlencode($color_hex) . '&width=' . $size . '&height=' . $size;
    }

    /**
     * Convertir color a hex
     */
    private static function color_to_hex($color) {
        // Si ya es hex, retornarlo
        if (strpos($color, '#') === 0) {
            return $color;
        }

        // Colores comunes
        $colors = array(
            'currentColor' => '#000000',
            'white' => '#ffffff',
            'black' => '#000000',
        );

        return isset($colors[$color]) ? $colors[$color] : '#000000';
    }

    /**
     * Generar tag img para icono
     */
    public static function render_icon($icon_name, $size = 24, $alt = '', $class = '') {
        $url = self::get_icon_url($icon_name, $size);
        $alt_attr = $alt ? ' alt="' . esc_attr($alt) . '"' : '';
        $class_attr = $class ? ' class="' . esc_attr($class) . '"' : '';

        return '<img src="' . esc_url($url) . '" width="' . esc_attr($size) . '" height="' . esc_attr($size) . '"' . $alt_attr . $class_attr . '>';
    }
}

