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
        
        register_rest_route('slidercards3d/v1', '/selection', array(
            'methods' => 'POST',
            'callback' => array($this, 'save_selection'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        register_rest_route('slidercards3d/v1', '/selection', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_selection'),
            'permission_callback' => '__return_true'
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
     * Guardar selección
     */
    public function save_selection($request) {
        global $wpdb;
        
        $type = sanitize_text_field($request->get_param('type'));
        $items = $request->get_param('items');
        
        if (!in_array($type, array('image', 'page'))) {
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
        
        if ($type) {
            $selected_ids = $this->get_selected_ids($type);
            return rest_ensure_response(array(
                'type' => $type,
                'ids' => $selected_ids
            ));
        }
        
        // Obtener todas las selecciones
        $images = $this->get_selected_ids('image');
        $pages = $this->get_selected_ids('page');
        
        return rest_ensure_response(array(
            'images' => $images,
            'pages' => $pages
        ));
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
}

