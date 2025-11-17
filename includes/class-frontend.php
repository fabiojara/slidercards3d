<?php
/**
 * Frontend del slider
 */

if (!defined('ABSPATH')) {
    exit;
}

class SliderCards3D_Frontend {

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_shortcode('slidercards3d', array($this, 'render_slider'));
    }

    /**
     * Cargar assets del frontend
     */
    public function enqueue_assets() {
        // Solo cargar si hay un shortcode en la página
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'slidercards3d')) {
            $this->load_assets();
        }
    }

    /**
     * Prevenir conflictos con otros plugins que carguen scripts externos
     */
    public function prevent_external_conflicts() {
        // Este método puede usarse en el futuro para prevenir conflictos
        // Por ahora, el plugin no carga scripts externos problemáticos
    }

    /**
     * Cargar assets
     */
    private function load_assets() {
        // CSS
        wp_enqueue_style(
            'slidercards3d-frontend',
            SLIDERCARDS3D_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            SLIDERCARDS3D_VERSION
        );

        // JavaScript
        wp_enqueue_script(
            'slidercards3d-frontend',
            SLIDERCARDS3D_PLUGIN_URL . 'assets/js/frontend.js',
            array(),
            SLIDERCARDS3D_VERSION,
            true
        );

        // Localizar script
        wp_localize_script('slidercards3d-frontend', 'slidercards3dData', array(
            'apiUrl' => rest_url('slidercards3d/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'pluginUrl' => SLIDERCARDS3D_PLUGIN_URL
        ));
    }

    /**
     * Renderizar slider mediante shortcode
     */
    public function render_slider($atts) {
        // Asegurar que los assets estén cargados
        $this->load_assets();

        $atts = shortcode_atts(array(
            'type' => 'all' // 'images', 'pages', 'products', 'all'
        ), $atts);

        // Generar ID único para esta instancia
        static $instance_count = 0;
        $instance_count++;
        $instance_id = 'slidercards3d-' . $instance_count;

        ob_start();
        ?>
        <div class="slidercards3d-container" data-type="<?php echo esc_attr($atts['type']); ?>" data-instance-id="<?php echo esc_attr($instance_id); ?>">
            <div class="slidercards3d-wrapper">
                <div class="slidercards3d-slider" id="<?php echo esc_attr($instance_id); ?>-slider">
                    <!-- Contenido cargado dinámicamente -->
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

