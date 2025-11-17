<?php
/**
 * Plugin Name: Slider Cards 3D
 * Plugin URI: https://github.com/fabiojara/slidercards3d
 * Description: Un slider 3D moderno para WordPress con gestión de imágenes y páginas desde el panel de administración.
 * Version: 1.8.0
 * Author: Fabio Jara
 * Author URI: https://github.com/fabiojara
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: slidercards3d
 * Domain Path: /languages
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('SLIDERCARDS3D_VERSION', '1.8.0');
define('SLIDERCARDS3D_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SLIDERCARDS3D_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SLIDERCARDS3D_PLUGIN_FILE', __FILE__);

/**
 * Clase principal del plugin
 */
class SliderCards3D {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        // Hooks de activación/desactivación
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // Cargar archivos necesarios
        add_action('plugins_loaded', array($this, 'load_dependencies'));

        // Inicializar plugin
        add_action('init', array($this, 'init'));
    }

    public function activate() {
        // Crear tablas necesarias
        $this->create_tables();

        // Crear directorio de backups
        $upload_dir = wp_upload_dir();
        $backup_dir = $upload_dir['basedir'] . '/slidercards3d-backups';
        if (!file_exists($backup_dir)) {
            wp_mkdir_p($backup_dir);
        }

        // Guardar versión inicial
        update_option('slidercards3d_version', SLIDERCARDS3D_VERSION);

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    public function deactivate() {
        flush_rewrite_rules();
    }

    private function create_tables() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'slidercards3d_selections';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            type varchar(20) NOT NULL,
            item_id bigint(20) NOT NULL,
            selected tinyint(1) DEFAULT 1,
            order_index int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY type_item (type, item_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function load_dependencies() {
        // Cargar clases necesarias
        require_once SLIDERCARDS3D_PLUGIN_DIR . 'includes/class-icons.php';
        require_once SLIDERCARDS3D_PLUGIN_DIR . 'includes/class-admin.php';
        require_once SLIDERCARDS3D_PLUGIN_DIR . 'includes/class-frontend.php';
        require_once SLIDERCARDS3D_PLUGIN_DIR . 'includes/class-version-manager.php';
        require_once SLIDERCARDS3D_PLUGIN_DIR . 'includes/class-api.php';
    }

    public function init() {
        // Cargar traducciones
        load_plugin_textdomain('slidercards3d', false, dirname(plugin_basename(__FILE__)) . '/languages');

        // Inicializar componentes
        if (is_admin()) {
            new SliderCards3D_Admin();
        } else {
            new SliderCards3D_Frontend();
        }

        // Inicializar API REST
        new SliderCards3D_API();

        // Inicializar gestor de versiones
        new SliderCards3D_Version_Manager();
    }
}

// Inicializar plugin
function slidercards3d_init() {
    return SliderCards3D::get_instance();
}

// Iniciar el plugin
slidercards3d_init();

