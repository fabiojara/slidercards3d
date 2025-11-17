<?php
/**
 * Panel de administración del plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class SliderCards3D_Admin {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    /**
     * Agregar menú de administración
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Slider Cards 3D', 'slidercards3d'),
            __('Slider 3D', 'slidercards3d'),
            'manage_options',
            'slidercards3d',
            array($this, 'render_admin_page'),
            'dashicons-images-alt2',
            30
        );
    }

    /**
     * Cargar assets del admin
     */
    public function enqueue_admin_assets($hook) {
        if ($hook !== 'toplevel_page_slidercards3d') {
            return;
        }

        // CSS
        wp_enqueue_style(
            'slidercards3d-admin',
            SLIDERCARDS3D_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            SLIDERCARDS3D_VERSION
        );

        // JavaScript
        wp_enqueue_script(
            'slidercards3d-admin',
            SLIDERCARDS3D_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-api'),
            SLIDERCARDS3D_VERSION,
            true
        );

        // Localizar script
        wp_localize_script('slidercards3d-admin', 'slidercards3dAdmin', array(
            'apiUrl' => rest_url('slidercards3d/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'strings' => array(
                'selectImage' => __('Seleccionar imagen', 'slidercards3d'),
                'deselectImage' => __('Deseleccionar imagen', 'slidercards3d'),
                'selectPage' => __('Seleccionar página', 'slidercards3d'),
                'deselectPage' => __('Deseleccionar página', 'slidercards3d'),
                'saving' => __('Guardando...', 'slidercards3d'),
                'saved' => __('Guardado', 'slidercards3d'),
                'error' => __('Error al guardar', 'slidercards3d')
            )
        ));
    }

    /**
     * Renderizar página de administración
     */
    public function render_admin_page() {
        ?>
        <div class="slidercards3d-admin-wrap">
            <div class="slidercards3d-header">
                <div class="slidercards3d-header-content">
                    <h1 class="slidercards3d-title">
                        <span class="slidercards3d-icon">🎴</span>
                        Slider Cards 3D
                    </h1>
                    <p class="slidercards3d-subtitle">Gestiona el contenido de tu slider 3D</p>
                </div>
                <div class="slidercards3d-header-actions">
                    <span class="slidercards3d-version">v<?php echo SLIDERCARDS3D_VERSION; ?></span>
                </div>
            </div>

            <div class="slidercards3d-tabs">
                <button class="slidercards3d-tab active" data-tab="images">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                    Imágenes
                </button>
                <button class="slidercards3d-tab" data-tab="pages">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    Páginas
                </button>
            </div>

            <div class="slidercards3d-content">
                <!-- Pestaña Imágenes -->
                <div class="slidercards3d-tab-content active" id="tab-images">
                    <div class="slidercards3d-toolbar">
                        <div class="slidercards3d-search">
                            <input type="text" id="image-search" placeholder="Buscar imágenes..." class="slidercards3d-search-input">
                        </div>
                        <div class="slidercards3d-actions">
                            <button class="slidercards3d-btn slidercards3d-btn-primary" id="save-images">
                                Guardar selección
                            </button>
                        </div>
                    </div>
                    <div class="slidercards3d-grid" id="images-grid">
                        <div class="slidercards3d-loading">
                            <div class="slidercards3d-spinner"></div>
                            <p>Cargando imágenes...</p>
                        </div>
                    </div>
                </div>

                <!-- Pestaña Páginas -->
                <div class="slidercards3d-tab-content" id="tab-pages">
                    <div class="slidercards3d-toolbar">
                        <div class="slidercards3d-search">
                            <input type="text" id="page-search" placeholder="Buscar páginas..." class="slidercards3d-search-input">
                        </div>
                        <div class="slidercards3d-actions">
                            <button class="slidercards3d-btn slidercards3d-btn-primary" id="save-pages">
                                Guardar selección
                            </button>
                        </div>
                    </div>
                    <div class="slidercards3d-grid slidercards3d-grid-cards" id="pages-grid">
                        <div class="slidercards3d-loading">
                            <div class="slidercards3d-spinner"></div>
                            <p>Cargando páginas...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

