<?php
/**
 * Desinstalación del plugin
 * 
 * Este archivo se ejecuta cuando el plugin es eliminado desde WordPress
 */

// Si no se llama desde WordPress, salir
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Eliminar tabla de selecciones
$table_name = $wpdb->prefix . 'slidercards3d_selections';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

// Eliminar opciones
delete_option('slidercards3d_version');
delete_option('slidercards3d_backups');

// Eliminar directorio de backups
$upload_dir = wp_upload_dir();
$backup_dir = $upload_dir['basedir'] . '/slidercards3d-backups';
if (file_exists($backup_dir)) {
    // Eliminar todos los archivos del directorio
    $files = glob($backup_dir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    rmdir($backup_dir);
}

