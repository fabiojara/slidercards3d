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
     * Obtener contenido SVG del icono
     */
    private static function get_icon_svg_content($icon_name) {
        $local_path = SLIDERCARDS3D_PLUGIN_DIR . 'assets/icons/' . $icon_name . '.svg';

        // Si existe el archivo local, leerlo
        if (file_exists($local_path)) {
            $svg_content = file_get_contents($local_path);
            if ($svg_content !== false) {
                return $svg_content;
            }
        }

        return false;
    }

    /**
     * Generar tag SVG inline para icono (método preferido)
     */
    public static function render_icon($icon_name, $size = 24, $alt = '', $class = '') {
        $svg_content = self::get_icon_svg_content($icon_name);

        // Si tenemos el SVG local, usar inline (mejor rendimiento)
        if ($svg_content !== false) {
            // Limpiar y preparar SVG
            $svg_content = trim($svg_content);

            // Remover etiquetas XML si existen
            $svg_content = preg_replace('/<\?xml[^>]*\?>/i', '', $svg_content);

            // Asegurar que tenga viewBox si no lo tiene
            if (strpos($svg_content, 'viewBox') === false && strpos($svg_content, '<svg') !== false) {
                $svg_content = preg_replace('/<svg([^>]*)>/i', '<svg$1 viewBox="0 0 24 24">', $svg_content);
            }

            // Agregar atributos de tamaño y clase
            // Primero verificar si ya tiene width/height para no duplicar
            $has_width = preg_match('/width\s*=/i', $svg_content);
            $has_height = preg_match('/height\s*=/i', $svg_content);

            if (!$has_width && !$has_height) {
                $svg_content = preg_replace('/<svg([^>]*)>/i', '<svg$1 width="' . esc_attr($size) . '" height="' . esc_attr($size) . '">', $svg_content);
            } else if (!$has_width) {
                $svg_content = preg_replace('/<svg([^>]*)>/i', '<svg$1 width="' . esc_attr($size) . '">', $svg_content);
            } else if (!$has_height) {
                $svg_content = preg_replace('/<svg([^>]*)>/i', '<svg$1 height="' . esc_attr($size) . '">', $svg_content);
            }

            // Agregar clase y aria-label
            if ($class || $alt) {
                $class_attr = $class ? ' class="' . esc_attr($class) . '"' : '';
                $aria_attr = $alt ? ' aria-label="' . esc_attr($alt) . '"' : '';
                $svg_content = preg_replace('/<svg([^>]*)>/i', '<svg$1' . $class_attr . $aria_attr . '>', $svg_content);
            }

            // Asegurar que el stroke sea currentColor para que herede el color del texto
            if (strpos($svg_content, 'stroke=') !== false && strpos($svg_content, 'stroke="currentColor"') === false) {
                $svg_content = preg_replace('/stroke="[^"]*"/i', 'stroke="currentColor"', $svg_content);
            }

            return $svg_content;
        }

        // Fallback a imagen con URL (con fallback a API)
        $url = self::get_icon_url($icon_name, $size);
        $alt_attr = $alt ? ' alt="' . esc_attr($alt) . '"' : '';
        $class_attr = $class ? ' class="' . esc_attr($class) . '"' : '';
        $fallback_url = self::get_iconify_url($icon_name, $size);

        return '<img src="' . esc_url($url) . '" width="' . esc_attr($size) . '" height="' . esc_attr($size) . '"' . $alt_attr . $class_attr . ' onerror="this.onerror=null; this.src=\'' . esc_url($fallback_url) . '\'">';
    }

    /**
     * Generar tag img para icono (método alternativo)
     */
    public static function render_icon_img($icon_name, $size = 24, $alt = '', $class = '') {
        $url = self::get_icon_url($icon_name, $size);
        $alt_attr = $alt ? ' alt="' . esc_attr($alt) . '"' : '';
        $class_attr = $class ? ' class="' . esc_attr($class) . '"' : '';
        $fallback_url = self::get_iconify_url($icon_name, $size);

        return '<img src="' . esc_url($url) . '" width="' . esc_attr($size) . '" height="' . esc_attr($size) . '"' . $alt_attr . $class_attr . ' onerror="this.onerror=null; this.src=\'' . esc_url($fallback_url) . '\'">';
    }
}

