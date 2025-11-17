<?php
/**
 * Gestor de versiones y changelog
 */

if (!defined('ABSPATH')) {
    exit;
}

class SliderCards3D_Version_Manager {
    
    private $changelog_file;
    
    public function __construct() {
        $this->changelog_file = SLIDERCARDS3D_PLUGIN_DIR . 'CHANGELOG.md';
        
        // Hook para detectar cambios en el código
        add_action('admin_init', array($this, 'check_version_update'));
    }
    
    /**
     * Verificar si hay actualización de versión
     */
    public function check_version_update() {
        $current_version = get_option('slidercards3d_version', '0.0.0');
        
        if (version_compare($current_version, SLIDERCARDS3D_VERSION, '<')) {
            $this->create_backup($current_version);
            $this->update_changelog($current_version, SLIDERCARDS3D_VERSION);
            update_option('slidercards3d_version', SLIDERCARDS3D_VERSION);
        }
    }
    
    /**
     * Crear copia de seguridad de la versión anterior
     */
    private function create_backup($old_version) {
        $upload_dir = wp_upload_dir();
        $backup_dir = $upload_dir['basedir'] . '/slidercards3d-backups';
        
        if (!file_exists($backup_dir)) {
            wp_mkdir_p($backup_dir);
        }
        
        $backup_filename = 'backup-v' . $old_version . '-' . date('Y-m-d-His') . '.zip';
        $backup_path = $backup_dir . '/' . $backup_filename;
        
        // Crear ZIP con todos los archivos del plugin
        $zip = new ZipArchive();
        if ($zip->open($backup_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $plugin_dir = SLIDERCARDS3D_PLUGIN_DIR;
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($plugin_dir),
                RecursiveIteratorIterator::LEAVES_ONLY
            );
            
            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $file_path = $file->getRealPath();
                    $relative_path = substr($file_path, strlen($plugin_dir) + 1);
                    
                    // Excluir backups y archivos temporales
                    if (strpos($relative_path, 'backups') === false && 
                        strpos($relative_path, '.git') === false) {
                        $zip->addFile($file_path, $relative_path);
                    }
                }
            }
            
            $zip->close();
            
            // Guardar registro de backup
            $backups = get_option('slidercards3d_backups', array());
            $backups[] = array(
                'version' => $old_version,
                'file' => $backup_filename,
                'date' => current_time('mysql'),
                'path' => $backup_path
            );
            update_option('slidercards3d_backups', $backups);
        }
    }
    
    /**
     * Actualizar changelog
     */
    private function update_changelog($old_version, $new_version) {
        $changelog_content = '';
        
        if (file_exists($this->changelog_file)) {
            $changelog_content = file_get_contents($this->changelog_file);
        }
        
        $entry = "\n## [{$new_version}] - " . date('Y-m-d') . "\n\n";
        $entry .= "### Cambios\n";
        $entry .= "- Actualización de versión de {$old_version} a {$new_version}\n";
        $entry .= "- Mejoras y correcciones generales\n\n";
        
        $new_content = "# Changelog\n\n" . $entry . $changelog_content;
        
        file_put_contents($this->changelog_file, $new_content);
    }
    
    /**
     * Obtener lista de backups disponibles
     */
    public static function get_backups() {
        return get_option('slidercards3d_backups', array());
    }
    
    /**
     * Incrementar versión menor (para cambios menores)
     */
    public static function increment_minor_version() {
        $current = SLIDERCARDS3D_VERSION;
        $parts = explode('.', $current);
        $parts[2] = intval($parts[2]) + 1;
        return implode('.', $parts);
    }
    
    /**
     * Incrementar versión mayor (para cambios mayores)
     */
    public static function increment_major_version() {
        $current = SLIDERCARDS3D_VERSION;
        $parts = explode('.', $current);
        $parts[0] = intval($parts[0]) + 1;
        $parts[1] = 0;
        $parts[2] = 0;
        return implode('.', $parts);
    }
}

