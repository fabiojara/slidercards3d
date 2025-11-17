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
            'nonce' => wp_create_nonce('wp_rest')
        ));
    }
    
    /**
     * Renderizar slider mediante shortcode
     */
    public function render_slider($atts) {
        $atts = shortcode_atts(array(
            'type' => 'all' // 'images', 'pages', 'all'
        ), $atts);
        
        ob_start();
        ?>
        <div class="slidercards3d-container" data-type="<?php echo esc_attr($atts['type']); ?>">
            <div class="slidercards3d-wrapper">
                <div class="slidercards3d-slider" id="slidercards3d-slider">
                    <!-- Contenido cargado dinámicamente -->
                </div>
                <div class="slidercards3d-controls">
                    <button class="slidercards3d-btn-prev" aria-label="Anterior">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </button>
                    <button class="slidercards3d-btn-next" aria-label="Siguiente">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

