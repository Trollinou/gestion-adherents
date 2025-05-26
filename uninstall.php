<?php
/**
 * Fichier de désinstallation du plugin Gestion d'Adhérents
 * 
 * Ce fichier est exécuté lorsque le plugin est désinstallé
 * via l'interface d'administration WordPress.
 * 
 * @package GestionAdherents
 * @version 1.0.0
 */

// Sécurité : vérifier que la désinstallation est légitime
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Vérifier les permissions
if (!current_user_can('activate_plugins')) {
    return;
}

// Vérifier que c'est bien notre plugin qui est désinstallé
if (__FILE__ !== WP_UNINSTALL_PLUGIN) {
    return;
}

/**
 * Suppression des données du plugin
 */
class GA_Uninstaller {
    
    /**
     * Exécuter la désinstallation complète
     */
    public static function uninstall() {
        global $wpdb;
        
        // Supprimer les tables de base de données
        self::drop_tables();
        
        // Supprimer les options
        self::delete_options();
        
        // Supprimer les capacités des rôles
        self::remove_capabilities();
        
        // Supprimer les tâches programmées
        self::clear_scheduled_events();
        
        // Supprimer les métadonnées utilisateur
        self::delete_user_meta();
        
        // Supprimer les fichiers de cache
        self::clear_cache();
        
        // Nettoyer les transients
        self::delete_transients();
        
        // Log de désinstallation
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Plugin Gestion Adhérents désinstallé avec succès');
        }
    }
    
    /**
     * Supprimer les tables de base de données
     */
    private static function drop_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'adherents';
        
        // Vérifier si la table existe avant de la supprimer
        $table_exists = $wpdb->get_var($wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $table_name
        ));
        
        if ($table_exists) {
            // Supprimer les contraintes de clés étrangères d'abord
            $wpdb->query("SET FOREIGN_KEY_CHECKS = 0");
            
            // Supprimer la table
            $wpdb->query("DROP TABLE IF EXISTS {$table_name}");
            
            // Réactiver les contraintes
            $wpdb->query("SET FOREIGN_KEY_CHECKS = 1");
        }
        
        // Supprimer les tables de logs si elles existent
        $log_table = $wpdb->prefix . 'adherents_logs';
        $wpdb->query("DROP TABLE IF EXISTS {$log_table}");
        
        // Supprimer les tables de sauvegarde si elles existent
        $backup_table = $wpdb->prefix . 'adherents_backup';
        $wpdb->query("DROP TABLE IF EXISTS {$backup_table}");
    }
    
    /**
     * Supprimer toutes les options du plugin
     */
    private static function delete_options() {
        // Options principales
        delete_option('ga_db_version');
        delete_option('ga_settings');
        delete_option('ga_plugin_version');
        delete_option('ga_activation_date');
        delete_option('ga_last_cleanup');
        
        // Options de configuration
        delete_option('ga_email_settings');
        delete_option('ga_export_settings');
        delete_option('ga_notification_settings');
        delete_option('ga_security_settings');
        
        // Options de cache
        delete_option('ga_stats_cache');
        delete_option('ga_reports_cache');
        
        // Options d'API
        delete_option('ga_api_keys');
        delete_option('ga_webhook_urls');
        
        // Supprimer toutes les options avec le préfixe ga_
        $wpdb = $GLOBALS['wpdb'];
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'ga_%'");
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'gestion_adherents_%'");
    }
    
    /**
     * Supprimer les capacités des rôles utilisateur
     */
    private static function remove_capabilities() {
        $capabilities = array(
            'manage_adherents',
            'edit_adherents',
            'delete_adherents',
            'view_adherents',
            'export_adherents',
            'import_adherents'
        );
        
        // Obtenir tous les rôles
        $roles = wp_roles();
        
        foreach ($roles->get_names() as $role_name => $display_name) {
            $role = get_role($role_name);
            
            if ($role) {
                foreach ($capabilities as $cap) {
                    $role->remove_cap($cap);
                }
            }
        }
    }
    
    /**
     * Supprimer les tâches programmées
     */
    private static function clear_scheduled_events() {
        // Supprimer les événements cron
        wp_clear_scheduled_hook('ga_daily_cleanup');
        wp_clear_scheduled_hook('ga_weekly_reports');
        wp_clear_scheduled_hook('ga_monthly_stats');
        wp_clear_scheduled_hook('ga_membership_expiry_check');
        wp_clear_scheduled_hook('ga_backup_database');
        wp_clear_scheduled_hook('ga_send_notifications');
        
        // Supprimer les événements personnalisés
        $scheduled_events = array(
            'ga_process_imports',
            'ga_cleanup_logs',
            'ga_update_stats',
            'ga_send_reminders'
        );
        
        foreach ($scheduled_events as $event) {
            wp_clear_scheduled_hook($event);
        }
    }
    
    /**
     * Supprimer les métadonnées utilisateur
     */
    private static function delete_user_meta() {
        global $wpdb;
        
        // Supprimer les métadonnées liées aux adhérents
        $meta_keys = array(
            'ga_adherent_id',
            'ga_membership_status',
            'ga_last_login_adherent',
            'ga_preferences',
            'ga_notifications_enabled'
        );
        
        foreach ($meta_keys as $meta_key) {
            delete_metadata('user', 0, $meta_key, '', true);
        }
        
        // Supprimer toutes les meta avec le préfixe ga_
        $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'ga_%'");
    }
    
    /**
     * Supprimer les fichiers de cache
     */
    private static function clear_cache() {
        // Supprimer le cache WordPress
        wp_cache_flush();
        
        // Supprimer les fichiers de cache du plugin
        $cache_dir = WP_CONTENT_DIR . '/cache/gestion-adherents/';
        if (is_dir($cache_dir)) {
            self::delete_directory($cache_dir);
        }
        
        // Supprimer les fichiers temporaires
        $temp_dir = WP_CONTENT_DIR . '/uploads/gestion-adherents/temp/';
        if (is_dir($temp_dir)) {
            self::delete_directory($temp_dir);
        }
        
        // Supprimer les fichiers de logs
        $log_file = WP_CONTENT_DIR . '/uploads/gestion-adherents/logs/plugin.log';
        if (file_exists($log_file)) {
            unlink($log_file);
        }
    }
    
    /**
     * Supprimer les transients
     */
    private static function delete_transients() {
        global $wpdb;
        
        // Supprimer les transients du plugin
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_ga_%'");
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_ga_%'");
        
        // Supprimer les transients spécifiques
        $transients = array(
            'ga_stats_dashboard',
            'ga_recent_members',
            'ga_expiring_memberships',
            'ga_monthly_report',
            'ga_export_progress'
        );
        
        foreach ($transients as $transient) {
            delete_transient($transient);
        }
    }
    
    /**
     * Supprimer récursivement un dossier
     */
    private static function delete_directory($dir) {
        if (!is_dir($dir)) {
            return false;
        }
        
        $files = array_diff(scandir($dir), array('.', '..'));
        
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            
            if (is_dir($path)) {
                self::delete_directory($path);
            } else {
                unlink($path);
            }
        }
        
        return rmdir($dir);
    }
    
    /**
     * Nettoyer les données multisite
     */
    private static function cleanup_multisite() {
        if (!is_multisite()) {
            return;
        }
        
        global $wpdb;
        
        // Obtenir tous les sites du réseau
        $sites = get_sites(array('number' => 0));
        
        foreach ($sites as $site) {
            switch_to_blog($site->blog_id);
            
            // Exécuter le nettoyage pour chaque site
            self::delete_options();
            self::remove_capabilities();
            self::clear_scheduled_events();
            self::delete_user_meta();
            
            restore_current_blog();
        }
        
        // Supprimer les options réseau
        delete_site_option('ga_network_settings');
        delete_site_option('ga_network_stats');
    }
    
    /**
     * Sauvegarde de sécurité avant désinstallation
     */
    private static function create_backup() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'adherents';
        $backup_file = WP_CONTENT_DIR . '/uploads/ga_backup_' . date('Y-m-d_H-i-s') . '.sql';
        
        // Créer le dossier de sauvegarde s'il n'existe pas
        $backup_dir = dirname($backup_file);
        if (!is_dir($backup_dir)) {
            wp_mkdir_p($backup_dir);
        }
        
        // Vérifier si la table existe
        $table_exists = $wpdb->get_var($wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $table_name
        ));
        
        if ($table_exists) {
            // Exporter les données
            $export_query = "SELECT * FROM {$table_name}";
            $results = $wpdb->get_results($export_query, ARRAY_A);
            
            if ($results) {
                $sql_content = "-- Sauvegarde des adhérents - " . date('Y-m-d H:i:s') . "\n";
                $sql_content .= "-- Cette sauvegarde a été créée automatiquement lors de la désinstallation du plugin\n\n";
                
                foreach ($results as $row) {
                    $values = array();
                    foreach ($row as $value) {
                        $values[] = "'" . esc_sql($value) . "'";
                    }
                    $sql_content .= "INSERT INTO {$table_name} VALUES (" . implode(', ', $values) . ");\n";
                }
                
                file_put_contents($backup_file, $sql_content);
                
                // Créer un fichier de notification
                $notice_file = dirname($backup_file) . '/README_BACKUP.txt';
                $notice_content = "SAUVEGARDE AUTOMATIQUE - Plugin Gestion d'Adhérents\n";
                $notice_content .= "=================================================\n\n";
                $notice_content .= "Date de sauvegarde : " . date('Y-m-d H:i:s') . "\n";
                $notice_content .= "Fichier de sauvegarde : " . basename($backup_file) . "\n\n";
                $notice_content .= "Cette sauvegarde contient toutes les données des adhérents\n";
                $notice_content .= "qui ont été supprimées lors de la désinstallation du plugin.\n\n";
                $notice_content .= "Pour restaurer ces données :\n";
                $notice_content .= "1. Réinstallez le plugin Gestion d'Adhérents\n";
                $notice_content .= "2. Importez le fichier SQL via phpMyAdmin ou un outil similaire\n";
                $notice_content .= "3. Ou contactez votre administrateur système\n\n";
                $notice_content .= "ATTENTION : Supprimez ces fichiers manuellement si vous n'en avez plus besoin.\n";
                
                file_put_contents($notice_file, $notice_content);
            }
        }
    }
}

// Demander confirmation avant désinstallation complète
if (isset($_GET['ga_confirm_uninstall']) && $_GET['ga_confirm_uninstall'] === 'yes') {
    // Créer une sauvegarde de sécurité
    GA_Uninstaller::create_backup();
    
    // Exécuter la désinstallation
    GA_Uninstaller::uninstall();
    
    // Nettoyer les données multisite si applicable
    if (is_multisite()) {
        GA_Uninstaller::cleanup_multisite();
    }
} else {
    // Afficher un message de confirmation (optionnel)
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Gestion Adhérents: Désinstallation initiée. Une sauvegarde sera créée automatiquement.');
    }
    
    // Exécution normale de la désinstallation
    GA_Uninstaller::create_backup();
    GA_Uninstaller::uninstall();
}

// Nettoyer les autoloads
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
}

// Message final
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('Plugin Gestion Adhérents complètement désinstallé. Sauvegarde créée dans /wp-content/uploads/');
}