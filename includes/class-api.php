<?php
/**
 * API REST para el plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class SliderCards3D_API {

    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    /**
     * Registrar rutas de la API
     */
    public function register_routes() {
        register_rest_route('slidercards3d/v1', '/images', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_images'),
            'permission_callback' => array($this, 'check_permissions')
        ));

        register_rest_route('slidercards3d/v1', '/pages', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_pages'),
            'permission_callback' => array($this, 'check_permissions')
        ));

        // Endpoint para productos WooCommerce (solo si WooCommerce está activo)
        if (class_exists('WooCommerce')) {
            register_rest_route('slidercards3d/v1', '/products', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_products'),
                'permission_callback' => array($this, 'check_permissions')
            ));
        }

        register_rest_route('slidercards3d/v1', '/selection', array(
            'methods' => 'POST',
            'callback' => array($this, 'save_selection'),
            'permission_callback' => array($this, 'check_permissions')
        ));

        register_rest_route('slidercards3d/v1', '/selection', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_selection'),
            'permission_callback' => '__return_true', // Público para el frontend
            'args' => array(
                'type' => array(
                    'default' => null,
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            ),
        ));

        // Endpoint para obtener datos completos de productos seleccionados (público)
        if (class_exists('WooCommerce')) {
            register_rest_route('slidercards3d/v1', '/products-data', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_products_data'),
                'permission_callback' => '__return_true', // Público para el frontend
                'args' => array(
                    'ids' => array(
                        'default' => array(),
                        'sanitize_callback' => function($ids) {
                            if (is_string($ids)) {
                                $ids = explode(',', $ids);
                            }
                            return array_map('intval', $ids);
                        },
                    ),
                ),
            ));
        }

        register_rest_route('slidercards3d/v1', '/settings', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_settings'),
            'permission_callback' => '__return_true', // Público para el frontend
        ));

        register_rest_route('slidercards3d/v1', '/settings', array(
            'methods' => 'POST',
            'callback' => array($this, 'save_settings'),
            'permission_callback' => array($this, 'check_permissions')
        ));
    }

    /**
     * Verificar permisos
     */
    public function check_permissions() {
        return current_user_can('manage_options');
    }

    /**
     * Obtener todas las imágenes
     */
    public function get_images($request) {
        $args = array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'posts_per_page' => -1,
            'post_status' => 'inherit',
            'orderby' => 'date',
            'order' => 'DESC'
        );

        $search = $request->get_param('search');
        if ($search) {
            $args['s'] = sanitize_text_field($search);
        }

        $images = get_posts($args);
        $selected_ids = $this->get_selected_ids('image');

        $result = array();
        foreach ($images as $image) {
            $image_url = wp_get_attachment_image_url($image->ID, 'medium');
            $full_url = wp_get_attachment_image_url($image->ID, 'full');

            $result[] = array(
                'id' => $image->ID,
                'title' => $image->post_title,
                'url' => $image_url,
                'full_url' => $full_url,
                'selected' => in_array($image->ID, $selected_ids),
                'date' => $image->post_date
            );
        }

        return rest_ensure_response($result);
    }

    /**
     * Obtener todas las páginas
     */
    public function get_pages($request) {
        $args = array(
            'post_type' => 'page',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC'
        );

        $search = $request->get_param('search');
        if ($search) {
            $args['s'] = sanitize_text_field($search);
        }

        $pages = get_posts($args);
        $selected_ids = $this->get_selected_ids('page');

        $result = array();
        foreach ($pages as $page) {
            $thumbnail_id = get_post_thumbnail_id($page->ID);
            $thumbnail_url = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'medium') : '';

            $result[] = array(
                'id' => $page->ID,
                'title' => $page->post_title,
                'thumbnail' => $thumbnail_url,
                'selected' => in_array($page->ID, $selected_ids),
                'url' => get_permalink($page->ID)
            );
        }

        return rest_ensure_response($result);
    }

    /**
     * Obtener todos los productos WooCommerce
     */
    public function get_products($request) {
        if (!class_exists('WooCommerce')) {
            return new WP_Error('woocommerce_not_active', 'WooCommerce no está activo', array('status' => 400));
        }

        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC'
        );

        $search = $request->get_param('search');
        if ($search) {
            $args['s'] = sanitize_text_field($search);
        }

        $products = get_posts($args);
        $selected_ids = $this->get_selected_ids('product');

        $result = array();
        foreach ($products as $product) {
            $product_obj = wc_get_product($product->ID);
            $thumbnail_id = get_post_thumbnail_id($product->ID);
            $thumbnail_url = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'medium') : '';
            $price = $product_obj ? $product_obj->get_price_html() : '';

            $result[] = array(
                'id' => $product->ID,
                'title' => $product->post_title,
                'thumbnail' => $thumbnail_url,
                'price' => $price,
                'selected' => in_array($product->ID, $selected_ids),
                'url' => get_permalink($product->ID)
            );
        }

        return rest_ensure_response($result);
    }

    /**
     * Guardar selección
     */
    public function save_selection($request) {
        global $wpdb;

        $type = sanitize_text_field($request->get_param('type'));
        $items = $request->get_param('items');

        $valid_types = array('image', 'page');
        if (class_exists('WooCommerce')) {
            $valid_types[] = 'product';
        }

        if (!in_array($type, $valid_types)) {
            return new WP_Error('invalid_type', 'Tipo inválido', array('status' => 400));
        }

        $table_name = $wpdb->prefix . 'slidercards3d_selections';

        // Eliminar selecciones anteriores del tipo
        $wpdb->delete($table_name, array('type' => $type), array('%s'));

        // Insertar nuevas selecciones
        if (is_array($items)) {
            $order = 0;
            foreach ($items as $item) {
                $item_id = intval($item['id']);
                $selected = isset($item['selected']) ? intval($item['selected']) : 1;

                if ($selected) {
                    $wpdb->insert(
                        $table_name,
                        array(
                            'type' => $type,
                            'item_id' => $item_id,
                            'selected' => 1,
                            'order_index' => $order++
                        ),
                        array('%s', '%d', '%d', '%d')
                    );
                }
            }
        }

        return rest_ensure_response(array(
            'success' => true,
            'message' => 'Selección guardada correctamente'
        ));
    }

    /**
     * Obtener selección actual
     */
    public function get_selection($request) {
        $type = $request->get_param('type');

        // Normalizar el tipo (aceptar 'images' o 'image')
        if ($type === 'images') {
            $type = 'image';
        }

        if ($type) {
            $selected_ids = $this->get_selected_ids($type);
            return rest_ensure_response(array(
                'type' => $type,
                'ids' => $selected_ids,
                'count' => count($selected_ids)
            ));
        }

        // Obtener todas las selecciones
        $images = $this->get_selected_ids('image');
        $pages = $this->get_selected_ids('page');

        return rest_ensure_response(array(
            'images' => $images,
            'pages' => $pages,
            'images_count' => count($images),
            'pages_count' => count($pages)
        ));
    }

    /**
     * Obtener datos completos de productos (público para frontend)
     */
    public function get_products_data($request) {
        if (!class_exists('WooCommerce')) {
            return new WP_Error('woocommerce_not_active', 'WooCommerce no está activo', array('status' => 400));
        }

        $ids = $request->get_param('ids');
        if (empty($ids) || !is_array($ids)) {
            return rest_ensure_response(array());
        }

        $result = array();
        foreach ($ids as $id) {
            $id = intval($id);
            if (!$id) continue;

            $product = wc_get_product($id);
            if (!$product) continue;

            $thumbnail_id = get_post_thumbnail_id($id);
            $thumbnail_url = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'medium') : '';

            $result[] = array(
                'id' => $id,
                'title' => $product->get_name(),
                'thumbnail' => $thumbnail_url,
                'price' => $product->get_price_html(),
                'url' => get_permalink($id)
            );
        }

        return rest_ensure_response($result);
    }

    /**
     * Obtener IDs seleccionados
     */
    private function get_selected_ids($type) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'slidercards3d_selections';

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT item_id FROM $table_name WHERE type = %s AND selected = 1 ORDER BY order_index ASC",
            $type
        ));

        return array_map(function($row) {
            return intval($row->item_id);
        }, $results);
    }

    /**
     * Obtener configuración
     */
    public function get_settings($request) {
        $defaults = array(
            'separation_desktop' => 100,
            'separation_tablet' => 70,
            'separation_mobile' => 50,
            'autoplay' => false,
            'autoplay_interval' => 3000,
            'darkness_intensity' => 25,
            'filter_intensity' => 30,
            'brightness_intensity' => 50,
            'card_height_desktop' => 400,
            'card_height_tablet' => 350,
            'card_height_mobile' => 300
        );

        $settings = get_option('slidercards3d_settings', $defaults);

        // Asegurar que todos los valores estén presentes
        $settings = wp_parse_args($settings, $defaults);

        // Convertir autoplay a booleano si es necesario
        $settings['autoplay'] = (bool) $settings['autoplay'];

        // Asegurar que darkness_intensity esté en rango válido
        $settings['darkness_intensity'] = max(0, min(100, intval($settings['darkness_intensity'])));

        return rest_ensure_response($settings);
    }

    /**
     * Guardar configuración
     */
    public function save_settings($request) {
        $separation_desktop = intval($request->get_param('separation_desktop'));
        $separation_tablet = intval($request->get_param('separation_tablet'));
        $separation_mobile = intval($request->get_param('separation_mobile'));
        $autoplay = $request->get_param('autoplay') === '1' || $request->get_param('autoplay') === true || $request->get_param('autoplay') === 'true';
        $autoplay_interval = intval($request->get_param('autoplay_interval'));
        $darkness_intensity = intval($request->get_param('darkness_intensity'));
        $filter_intensity = intval($request->get_param('filter_intensity'));
        $brightness_intensity = intval($request->get_param('brightness_intensity'));
        $card_height_desktop = intval($request->get_param('card_height_desktop'));
        $card_height_tablet = intval($request->get_param('card_height_tablet'));
        $card_height_mobile = intval($request->get_param('card_height_mobile'));

        // Validar valores
        if ($separation_desktop < 0 || $separation_desktop > 500) {
            return new WP_Error('invalid_value', 'El valor de separación desktop debe estar entre 0 y 500', array('status' => 400));
        }

        if ($separation_tablet < 0 || $separation_tablet > 500) {
            return new WP_Error('invalid_value', 'El valor de separación tablet debe estar entre 0 y 500', array('status' => 400));
        }

        if ($separation_mobile < 0 || $separation_mobile > 500) {
            return new WP_Error('invalid_value', 'El valor de separación móvil debe estar entre 0 y 500', array('status' => 400));
        }

        if ($autoplay_interval < 1000 || $autoplay_interval > 10000) {
            return new WP_Error('invalid_value', 'El intervalo de reproducción debe estar entre 1000 y 10000 ms', array('status' => 400));
        }

        if ($darkness_intensity < 0 || $darkness_intensity > 100) {
            return new WP_Error('invalid_value', 'La intensidad de oscurecimiento debe estar entre 0 y 100', array('status' => 400));
        }

        if ($filter_intensity < 0 || $filter_intensity > 100) {
            return new WP_Error('invalid_value', 'La intensidad de filtro debe estar entre 0 y 100', array('status' => 400));
        }

        if ($brightness_intensity < 0 || $brightness_intensity > 100) {
            return new WP_Error('invalid_value', 'La intensidad de brillo debe estar entre 0 y 100', array('status' => 400));
        }

        // Validar alturas de cards
        if ($card_height_desktop < 200 || $card_height_desktop > 800) {
            $card_height_desktop = 400;
        }
        if ($card_height_tablet < 150 || $card_height_tablet > 700) {
            $card_height_tablet = 350;
        }
        if ($card_height_mobile < 100 || $card_height_mobile > 600) {
            $card_height_mobile = 300;
        }

        $settings = array(
            'separation_desktop' => $separation_desktop,
            'separation_tablet' => $separation_tablet,
            'separation_mobile' => $separation_mobile,
            'autoplay' => $autoplay,
            'autoplay_interval' => $autoplay_interval,
            'darkness_intensity' => $darkness_intensity,
            'filter_intensity' => $filter_intensity,
            'brightness_intensity' => $brightness_intensity,
            'card_height_desktop' => $card_height_desktop,
            'card_height_tablet' => $card_height_tablet,
            'card_height_mobile' => $card_height_mobile
        );

        update_option('slidercards3d_settings', $settings);

        return rest_ensure_response(array(
            'success' => true,
            'message' => 'Configuración guardada correctamente',
            'settings' => $settings
        ));
    }
}

