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
        // Obtener configuración guardada
        $defaults = array(
            'separation_desktop' => 100,
            'separation_tablet' => 70,
            'separation_mobile' => 50
        );
        $settings = get_option('slidercards3d_settings', $defaults);
        $settings = wp_parse_args($settings, $defaults);
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
                <button class="slidercards3d-tab" data-tab="settings">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M12 1v6m0 6v6m9-9h-6m-6 0H3m15.364 6.364l-4.243-4.243m-4.242 0L5.636 18.364m12.728 0l-4.243-4.243m-4.242 0L5.636 5.636"></path>
                    </svg>
                    Configuración
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

                <!-- Pestaña Configuración -->
                <div class="slidercards3d-tab-content" id="tab-settings">
                    <div class="slidercards3d-settings">
                        <div class="slidercards3d-settings-header">
                            <h2 class="slidercards3d-settings-title">Configuración del Slider</h2>
                            <p class="slidercards3d-settings-description">Ajusta la separación horizontal de las tarjetas según el tamaño de pantalla</p>
                        </div>
                        
                        <form id="slidercards3d-settings-form" class="slidercards3d-settings-form">
                            <div class="slidercards3d-settings-group">
                                <label class="slidercards3d-settings-label">
                                    <span class="slidercards3d-settings-label-text">Separación Desktop (px)</span>
                                    <span class="slidercards3d-settings-label-desc">Separación horizontal para pantallas grandes (más de 768px)</span>
                                </label>
                                <div class="slidercards3d-settings-input-wrapper">
                                    <input 
                                        type="number" 
                                        id="separation-desktop" 
                                        name="separation_desktop" 
                                        class="slidercards3d-settings-input" 
                                        min="0" 
                                        max="500" 
                                        step="10"
                                        value="<?php echo esc_attr($settings['separation_desktop']); ?>"
                                    >
                                    <span class="slidercards3d-settings-unit">px</span>
                                </div>
                            </div>

                            <div class="slidercards3d-settings-group">
                                <label class="slidercards3d-settings-label">
                                    <span class="slidercards3d-settings-label-text">Separación Tablet (px)</span>
                                    <span class="slidercards3d-settings-label-desc">Separación horizontal para tablets (481px - 768px)</span>
                                </label>
                                <div class="slidercards3d-settings-input-wrapper">
                                    <input 
                                        type="number" 
                                        id="separation-tablet" 
                                        name="separation_tablet" 
                                        class="slidercards3d-settings-input" 
                                        min="0" 
                                        max="500" 
                                        step="10"
                                        value="<?php echo esc_attr($settings['separation_tablet']); ?>"
                                    >
                                    <span class="slidercards3d-settings-unit">px</span>
                                </div>
                            </div>

                            <div class="slidercards3d-settings-group">
                                <label class="slidercards3d-settings-label">
                                    <span class="slidercards3d-settings-label-text">Separación Móvil (px)</span>
                                    <span class="slidercards3d-settings-label-desc">Separación horizontal para móviles (hasta 480px)</span>
                                </label>
                                <div class="slidercards3d-settings-input-wrapper">
                                    <input 
                                        type="number" 
                                        id="separation-mobile" 
                                        name="separation_mobile" 
                                        class="slidercards3d-settings-input" 
                                        min="0" 
                                        max="500" 
                                        step="10"
                                        value="<?php echo esc_attr($settings['separation_mobile']); ?>"
                                    >
                                    <span class="slidercards3d-settings-unit">px</span>
                                </div>
                            </div>

                            <div class="slidercards3d-settings-actions">
                                <button type="submit" class="slidercards3d-btn slidercards3d-btn-primary" id="save-settings">
                                    Guardar configuración
                                </button>
                                <button type="button" class="slidercards3d-btn slidercards3d-btn-secondary" id="reset-settings">
                                    Restaurar valores por defecto
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

