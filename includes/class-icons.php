<?php
/**
 * Gestor de iconos Heroicons en formato SVG
 * Heroicons es la librería oficial de Tailwind CSS, perfecta para diseños estilo Vercel/Linear/Stripe/Apple
 * Usamos SVG directamente para mejor calidad y escalabilidad
 */

if (!defined('ABSPATH')) {
    exit;
}

class SliderCards3D_Icons {

    private static $iconify_api = 'https://api.iconify.design/heroicons-outline/';

    /**
     * Obtener URL del icono SVG
     */
    public static function get_icon_url($icon_name, $size = 24, $color = 'currentColor') {
        $local_path = SLIDERCARDS3D_PLUGIN_DIR . 'assets/icons/' . $icon_name . '.svg';
        $local_url = SLIDERCARDS3D_PLUGIN_URL . 'assets/icons/' . $icon_name . '.svg';

        // Si existe el archivo local, usarlo
        if (file_exists($local_path)) {
            return $local_url;
        }

        // Si no existe, usar API de Iconify como fallback
        return self::get_iconify_url($icon_name, $size, $color);
    }

    /**
     * Obtener URL desde Iconify API (fallback si no existe localmente)
     */
    private static function get_iconify_url($icon_name, $size = 24, $color = 'currentColor') {
        $color_hex = self::color_to_hex($color);
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

