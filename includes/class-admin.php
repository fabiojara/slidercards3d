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
            'pluginUrl' => SLIDERCARDS3D_PLUGIN_URL,
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
            'separation_mobile' => 50,
            'autoplay' => false,
            'autoplay_interval' => 3000,
            'darkness_intensity' => 25, // Intensidad de oscurecimiento en porcentaje (0-100)
            'filter_intensity' => 30, // Intensidad del filtro en porcentaje (0-100)
            'brightness_intensity' => 50 // Intensidad de brillo en porcentaje (0-100)
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
                    <p class="slidercards3d-subtitle">Gestiona el contenido de tu slider 3D - Versión del plugin <?php echo SLIDERCARDS3D_VERSION; ?></p>
                </div>
                <div class="slidercards3d-header-actions">
                    <span class="slidercards3d-version">v<?php echo SLIDERCARDS3D_VERSION; ?></span>
                </div>
            </div>

            <div class="slidercards3d-tabs">
                <button class="slidercards3d-tab active" data-tab="images">
                    <?php echo SliderCards3D_Icons::render_icon('photo', 20, 'Imágenes', 'slidercards3d-tab-icon'); ?>
                    Imágenes
                </button>
                <button class="slidercards3d-tab" data-tab="pages">
                    <?php echo SliderCards3D_Icons::render_icon('document-text', 20, 'Páginas', 'slidercards3d-tab-icon'); ?>
                    Páginas
                </button>
                <button class="slidercards3d-tab" data-tab="settings">
                    <?php echo SliderCards3D_Icons::render_icon('cog-6-tooth', 20, 'Configuración', 'slidercards3d-tab-icon'); ?>
                    Configuración
                </button>
                <button class="slidercards3d-tab" data-tab="usage">
                    <?php echo SliderCards3D_Icons::render_icon('document-text', 20, 'Modo de Uso', 'slidercards3d-tab-icon'); ?>
                    Modo de Uso
                </button>
                <button class="slidercards3d-tab" data-tab="info">
                    <?php echo SliderCards3D_Icons::render_icon('information-circle', 20, 'Información', 'slidercards3d-tab-icon'); ?>
                    Información
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

                            <div class="slidercards3d-settings-divider"></div>

                            <div class="slidercards3d-settings-group">
                                <div class="slidercards3d-settings-switch-wrapper">
                                    <label class="slidercards3d-settings-label">
                                        <span class="slidercards3d-settings-label-text">Reproducción automática</span>
                                        <span class="slidercards3d-settings-label-desc">Activa el desplazamiento automático del slider</span>
                                    </label>
                                    <label class="slidercards3d-switch">
                                        <input
                                            type="checkbox"
                                            id="autoplay"
                                            name="autoplay"
                                            value="1"
                                            <?php checked($settings['autoplay'], true); ?>
                                        >
                                        <span class="slidercards3d-switch-slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="slidercards3d-settings-group" id="autoplay-interval-group" style="<?php echo $settings['autoplay'] ? '' : 'display: none;'; ?>">
                                <label class="slidercards3d-settings-label">
                                    <span class="slidercards3d-settings-label-text">Intervalo de reproducción (ms)</span>
                                    <span class="slidercards3d-settings-label-desc">Tiempo entre cada transición automática (en milisegundos)</span>
                                </label>
                                <div class="slidercards3d-settings-input-wrapper">
                                    <input
                                        type="number"
                                        id="autoplay-interval"
                                        name="autoplay_interval"
                                        class="slidercards3d-settings-input"
                                        min="1000"
                                        max="10000"
                                        step="500"
                                        value="<?php echo esc_attr($settings['autoplay_interval']); ?>"
                                    >
                                    <span class="slidercards3d-settings-unit">ms</span>
                                </div>
                            </div>

                            <div class="slidercards3d-settings-divider"></div>

                            <div class="slidercards3d-settings-group">
                                <label class="slidercards3d-settings-label">
                                    <span class="slidercards3d-settings-label-text">Intensidad de oscurecimiento</span>
                                    <span class="slidercards3d-settings-label-desc">Controla qué tan oscuras se ven las imágenes detrás de la principal (0% = sin oscurecimiento, 100% = máximo oscurecimiento)</span>
                                </label>
                                <div class="slidercards3d-range-wrapper">
                                    <input
                                        type="range"
                                        id="darkness-intensity"
                                        name="darkness_intensity"
                                        class="slidercards3d-range-input"
                                        min="0"
                                        max="100"
                                        step="5"
                                        value="<?php echo esc_attr($settings['darkness_intensity']); ?>"
                                    >
                                    <div class="slidercards3d-range-value">
                                        <span id="darkness-intensity-value"><?php echo esc_attr($settings['darkness_intensity']); ?></span>
                                        <span class="slidercards3d-settings-unit">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="slidercards3d-settings-group">
                                <label class="slidercards3d-settings-label">
                                    <span class="slidercards3d-settings-label-text">Intensidad de filtro</span>
                                    <span class="slidercards3d-settings-label-desc">Controla la intensidad del filtro aplicado a las imágenes no activas (0% = sin filtro, 100% = máximo filtro). Afecta a imágenes y páginas.</span>
                                </label>
                                <div class="slidercards3d-range-wrapper">
                                    <input
                                        type="range"
                                        id="filter-intensity"
                                        name="filter_intensity"
                                        class="slidercards3d-range-input"
                                        min="0"
                                        max="100"
                                        step="5"
                                        value="<?php echo esc_attr($settings['filter_intensity']); ?>"
                                    >
                                    <div class="slidercards3d-range-value">
                                        <span id="filter-intensity-value"><?php echo esc_attr($settings['filter_intensity']); ?></span>
                                        <span class="slidercards3d-settings-unit">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="slidercards3d-settings-group">
                                <label class="slidercards3d-settings-label">
                                    <span class="slidercards3d-settings-label-text">Intensidad de brillo</span>
                                    <span class="slidercards3d-settings-label-desc">Controla el brillo de las imágenes no activas (0% = sin brillo/muy oscuro, 100% = brillo máximo). Afecta a imágenes y páginas.</span>
                                </label>
                                <div class="slidercards3d-range-wrapper">
                                    <input
                                        type="range"
                                        id="brightness-intensity"
                                        name="brightness_intensity"
                                        class="slidercards3d-range-input"
                                        min="0"
                                        max="100"
                                        step="5"
                                        value="<?php echo esc_attr($settings['brightness_intensity']); ?>"
                                    >
                                    <div class="slidercards3d-range-value">
                                        <span id="brightness-intensity-value"><?php echo esc_attr($settings['brightness_intensity']); ?></span>
                                        <span class="slidercards3d-settings-unit">%</span>
                                    </div>
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

                <!-- Pestaña Modo de Uso -->
                <div class="slidercards3d-tab-content" id="tab-usage">
                    <div class="slidercards3d-usage">
                        <div class="slidercards3d-usage-header">
                            <h2 class="slidercards3d-usage-title">📖 Modo de Uso del Shortcode</h2>
                            <p class="slidercards3d-usage-description">Aprende cómo implementar el slider 3D en tu sitio WordPress</p>
                        </div>

                        <div class="slidercards3d-usage-content">
                            <!-- Shortcode Básico -->
                            <div class="slidercards3d-usage-section">
                                <h3 class="slidercards3d-usage-section-title">Shortcode Básico</h3>
                                <p class="slidercards3d-usage-text">El shortcode principal es <code>[slidercards3d]</code> y puede usarse en cualquier página, entrada o widget de WordPress.</p>

                                <div class="slidercards3d-usage-code-block">
                                    <div class="slidercards3d-usage-code-header">
                                        <span>Uso Simple</span>
                                        <button class="slidercards3d-copy-btn" data-copy="[slidercards3d]">Copiar</button>
                                    </div>
                                    <pre><code>[slidercards3d]</code></pre>
                                </div>

                                <p class="slidercards3d-usage-note">Este shortcode mostrará todas las imágenes y páginas que hayas seleccionado en el panel de administración.</p>
                            </div>

                            <!-- Parámetros -->
                            <div class="slidercards3d-usage-section">
                                <h3 class="slidercards3d-usage-section-title">Parámetros Disponibles</h3>

                                <div class="slidercards3d-usage-param">
                                    <h4 class="slidercards3d-usage-param-name">type</h4>
                                    <p class="slidercards3d-usage-text">Especifica qué tipo de contenido mostrar en el slider.</p>
                                    <ul class="slidercards3d-usage-list">
                                        <li><code>all</code> (por defecto) - Muestra imágenes y páginas seleccionadas</li>
                                        <li><code>images</code> - Solo muestra imágenes seleccionadas</li>
                                        <li><code>pages</code> - Solo muestra páginas seleccionadas</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Ejemplos -->
                            <div class="slidercards3d-usage-section">
                                <h3 class="slidercards3d-usage-section-title">Ejemplos de Uso</h3>

                                <div class="slidercards3d-usage-example">
                                    <h4 class="slidercards3d-usage-example-title">1. Mostrar Todo (Imágenes + Páginas)</h4>
                                    <div class="slidercards3d-usage-code-block">
                                        <div class="slidercards3d-usage-code-header">
                                            <span>Shortcode</span>
                                            <button class="slidercards3d-copy-btn" data-copy="[slidercards3d]">Copiar</button>
                                        </div>
                                        <pre><code>[slidercards3d]</code></pre>
                                    </div>
                                    <p class="slidercards3d-usage-text">O también puedes especificar explícitamente:</p>
                                    <div class="slidercards3d-usage-code-block">
                                        <div class="slidercards3d-usage-code-header">
                                            <span>Shortcode</span>
                                            <button class="slidercards3d-copy-btn" data-copy='[slidercards3d type="all"]'>Copiar</button>
                                        </div>
                                        <pre><code>[slidercards3d type="all"]</code></pre>
                                    </div>
                                </div>

                                <div class="slidercards3d-usage-example">
                                    <h4 class="slidercards3d-usage-example-title">2. Solo Imágenes</h4>
                                    <div class="slidercards3d-usage-code-block">
                                        <div class="slidercards3d-usage-code-header">
                                            <span>Shortcode</span>
                                            <button class="slidercards3d-copy-btn" data-copy='[slidercards3d type="images"]'>Copiar</button>
                                        </div>
                                        <pre><code>[slidercards3d type="images"]</code></pre>
                                    </div>
                                </div>

                                <div class="slidercards3d-usage-example">
                                    <h4 class="slidercards3d-usage-example-title">3. Solo Páginas</h4>
                                    <div class="slidercards3d-usage-code-block">
                                        <div class="slidercards3d-usage-code-header">
                                            <span>Shortcode</span>
                                            <button class="slidercards3d-copy-btn" data-copy='[slidercards3d type="pages"]'>Copiar</button>
                                        </div>
                                        <pre><code>[slidercards3d type="pages"]</code></pre>
                                    </div>
                                </div>
                            </div>

                            <!-- Dónde Usar -->
                            <div class="slidercards3d-usage-section">
                                <h3 class="slidercards3d-usage-section-title">Dónde Usar el Shortcode</h3>

                                <div class="slidercards3d-usage-example">
                                    <h4 class="slidercards3d-usage-example-title">En el Editor de WordPress (Gutenberg)</h4>
                                    <ol class="slidercards3d-usage-list">
                                        <li>Agrega un bloque <strong>"Shortcode"</strong> o <strong>"Código corto"</strong></li>
                                        <li>Escribe: <code>[slidercards3d]</code></li>
                                        <li>Guarda y visualiza</li>
                                    </ol>
                                </div>

                                <div class="slidercards3d-usage-example">
                                    <h4 class="slidercards3d-usage-example-title">En el Editor Clásico</h4>
                                    <p class="slidercards3d-usage-text">Simplemente pega el shortcode en el contenido:</p>
                                    <div class="slidercards3d-usage-code-block">
                                        <div class="slidercards3d-usage-code-header">
                                            <span>Shortcode</span>
                                            <button class="slidercards3d-copy-btn" data-copy="[slidercards3d]">Copiar</button>
                                        </div>
                                        <pre><code>[slidercards3d]</code></pre>
                                    </div>
                                </div>

                                <div class="slidercards3d-usage-example">
                                    <h4 class="slidercards3d-usage-example-title">En Widgets</h4>
                                    <ol class="slidercards3d-usage-list">
                                        <li>Ve a <strong>Apariencia → Widgets</strong></li>
                                        <li>Agrega un widget de <strong>"Texto"</strong> o <strong>"HTML"</strong></li>
                                        <li>Inserta el shortcode: <code>[slidercards3d]</code></li>
                                    </ol>
                                </div>

                                <div class="slidercards3d-usage-example">
                                    <h4 class="slidercards3d-usage-example-title">En Templates PHP</h4>
                                    <p class="slidercards3d-usage-text">Si necesitas insertarlo directamente en un template PHP:</p>
                                    <div class="slidercards3d-usage-code-block">
                                        <div class="slidercards3d-usage-code-header">
                                            <span>PHP</span>
                                            <button class="slidercards3d-copy-btn" data-copy="<?php echo esc_attr('<?php echo do_shortcode(\'[slidercards3d]\'); ?>'); ?>">Copiar</button>
                                        </div>
                                        <pre><code><?php echo esc_html('<?php echo do_shortcode(\'[slidercards3d]\'); ?>'); ?></code></pre>
                                    </div>
                                    <p class="slidercards3d-usage-text">O con parámetros:</p>
                                    <div class="slidercards3d-usage-code-block">
                                        <div class="slidercards3d-usage-code-header">
                                            <span>PHP</span>
                                            <button class="slidercards3d-copy-btn" data-copy="<?php echo esc_attr('<?php echo do_shortcode(\'[slidercards3d type="images"]\'); ?>'); ?>">Copiar</button>
                                        </div>
                                        <pre><code><?php echo esc_html('<?php echo do_shortcode(\'[slidercards3d type="images"]\'); ?>'); ?></code></pre>
                                    </div>
                                </div>
                            </div>

                            <!-- Notas Importantes -->
                            <div class="slidercards3d-usage-section">
                                <h3 class="slidercards3d-usage-section-title">Notas Importantes</h3>
                                <ul class="slidercards3d-usage-list">
                                    <li>El slider solo mostrará el contenido que hayas seleccionado y guardado en el panel de administración</li>
                                    <li>Si no hay contenido seleccionado, se mostrará un mensaje indicando que no hay contenido</li>
                                    <li>El slider es responsive y se adapta automáticamente a diferentes tamaños de pantalla</li>
                                    <li>La navegación funciona con teclado (flechas), mouse (botones) y touch (deslizar) en móviles</li>
                                    <li>Puedes configurar la separación horizontal, autoplay y oscurecimiento desde la pestaña <strong>Configuración</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pestaña Información -->
                <div class="slidercards3d-tab-content" id="tab-info">
                    <div class="slidercards3d-info">
                        <div class="slidercards3d-info-header">
                            <h2 class="slidercards3d-info-title">ℹ️ Información del Plugin</h2>
                            <p class="slidercards3d-info-description">Detalles de configuración y versión del plugin</p>
                        </div>

                        <div class="slidercards3d-info-content">
                            <!-- Versión -->
                            <div class="slidercards3d-info-section">
                                <h3 class="slidercards3d-info-section-title">Versión del Plugin</h3>
                                <div class="slidercards3d-info-item">
                                    <span class="slidercards3d-info-label">Versión actual:</span>
                                    <span class="slidercards3d-info-value"><?php echo esc_html(SLIDERCARDS3D_VERSION); ?></span>
                                </div>
                            </div>

                            <!-- Configuración Actual -->
                            <div class="slidercards3d-info-section">
                                <h3 class="slidercards3d-info-section-title">Configuración Actual</h3>

                                <div class="slidercards3d-info-group">
                                    <h4 class="slidercards3d-info-group-title">Separación Horizontal</h4>
                                    <div class="slidercards3d-info-item">
                                        <span class="slidercards3d-info-label">Desktop:</span>
                                        <span class="slidercards3d-info-value"><?php echo esc_html($settings['separation_desktop']); ?> px</span>
                                    </div>
                                    <div class="slidercards3d-info-item">
                                        <span class="slidercards3d-info-label">Tablet:</span>
                                        <span class="slidercards3d-info-value"><?php echo esc_html($settings['separation_tablet']); ?> px</span>
                                    </div>
                                    <div class="slidercards3d-info-item">
                                        <span class="slidercards3d-info-label">Móvil:</span>
                                        <span class="slidercards3d-info-value"><?php echo esc_html($settings['separation_mobile']); ?> px</span>
                                    </div>
                                </div>

                                <div class="slidercards3d-info-group">
                                    <h4 class="slidercards3d-info-group-title">Reproducción Automática</h4>
                                    <div class="slidercards3d-info-item">
                                        <span class="slidercards3d-info-label">Estado:</span>
                                        <span class="slidercards3d-info-value">
                                            <?php echo $settings['autoplay'] ? '<span class="slidercards3d-info-badge slidercards3d-info-badge-success">Activo</span>' : '<span class="slidercards3d-info-badge slidercards3d-info-badge-inactive">Inactivo</span>'; ?>
                                        </span>
                                    </div>
                                    <?php if ($settings['autoplay']) : ?>
                                    <div class="slidercards3d-info-item">
                                        <span class="slidercards3d-info-label">Intervalo:</span>
                                        <span class="slidercards3d-info-value"><?php echo esc_html($settings['autoplay_interval']); ?> ms</span>
                                    </div>
                                    <?php endif; ?>
                                </div>

                    <div class="slidercards3d-info-group">
                        <h4 class="slidercards3d-info-group-title">Efectos Visuales</h4>
                        <div class="slidercards3d-info-item">
                            <span class="slidercards3d-info-label">Intensidad de oscurecimiento:</span>
                            <span class="slidercards3d-info-value"><?php echo esc_html($settings['darkness_intensity']); ?>%</span>
                        </div>
                        <div class="slidercards3d-info-item">
                            <span class="slidercards3d-info-label">Intensidad de filtro:</span>
                            <span class="slidercards3d-info-value"><?php echo esc_html($settings['filter_intensity']); ?>%</span>
                        </div>
                        <div class="slidercards3d-info-item">
                            <span class="slidercards3d-info-label">Intensidad de brillo:</span>
                            <span class="slidercards3d-info-value"><?php echo esc_html($settings['brightness_intensity']); ?>%</span>
                        </div>
                    </div>
                            </div>

                            <!-- Información del Sistema -->
                            <div class="slidercards3d-info-section">
                                <h3 class="slidercards3d-info-section-title">Información del Sistema</h3>

                                <div class="slidercards3d-info-item">
                                    <span class="slidercards3d-info-label">Versión de WordPress:</span>
                                    <span class="slidercards3d-info-value"><?php echo esc_html(get_bloginfo('version')); ?></span>
                                </div>
                                <div class="slidercards3d-info-item">
                                    <span class="slidercards3d-info-label">Versión de PHP:</span>
                                    <span class="slidercards3d-info-value"><?php echo esc_html(PHP_VERSION); ?></span>
                                </div>
                                <div class="slidercards3d-info-item">
                                    <span class="slidercards3d-info-label">Ruta del plugin:</span>
                                    <span class="slidercards3d-info-value slidercards3d-info-path"><?php echo esc_html(SLIDERCARDS3D_PLUGIN_DIR); ?></span>
                                </div>
                                <div class="slidercards3d-info-item">
                                    <span class="slidercards3d-info-label">URL del plugin:</span>
                                    <span class="slidercards3d-info-value slidercards3d-info-path"><?php echo esc_url(SLIDERCARDS3D_PLUGIN_URL); ?></span>
                                </div>
                            </div>

                            <!-- Estadísticas -->
                            <div class="slidercards3d-info-section">
                                <h3 class="slidercards3d-info-section-title">Estadísticas</h3>

                                <?php
                                // Obtener selecciones guardadas
                                global $wpdb;
                                $table_name = $wpdb->prefix . 'slidercards3d_selections';

                                $images_count = $wpdb->get_var($wpdb->prepare(
                                    "SELECT COUNT(*) FROM $table_name WHERE type = %s AND selected = 1",
                                    'image'
                                ));

                                $pages_count = $wpdb->get_var($wpdb->prepare(
                                    "SELECT COUNT(*) FROM $table_name WHERE type = %s AND selected = 1",
                                    'page'
                                ));
                                ?>

                                <div class="slidercards3d-info-item">
                                    <span class="slidercards3d-info-label">Imágenes seleccionadas:</span>
                                    <span class="slidercards3d-info-value"><?php echo esc_html($images_count ? $images_count : 0); ?></span>
                                </div>
                                <div class="slidercards3d-info-item">
                                    <span class="slidercards3d-info-label">Páginas seleccionadas:</span>
                                    <span class="slidercards3d-info-value"><?php echo esc_html($pages_count ? $pages_count : 0); ?></span>
                                </div>
                                <div class="slidercards3d-info-item">
                                    <span class="slidercards3d-info-label">Total de elementos:</span>
                                    <span class="slidercards3d-info-value"><?php echo esc_html(($images_count ? $images_count : 0) + ($pages_count ? $pages_count : 0)); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

