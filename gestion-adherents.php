<?php
/**
 * Plugin Name: Gestion d'Adhérents
 * Plugin URI: https://exemple.com/gestion-adherents
 * Description: Plugin professionnel pour la gestion d'une base d'adhérents avec liaison aux comptes WordPress
 * Version: 1.1.1
 * Author: Etienne Gagnon
 * Text Domain: gestion-adherents
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * Network: false
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

// Définition des constantes
define('GA_PLUGIN_VERSION', '1.1.1');
define('GA_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('GA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('GA_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Classe principale du plugin
 */
class GestionAdherents {
    
    /**
     * Instance unique du plugin (Singleton)
     */
    private static $instance = null;
    
    /**
     * Version de la base de données
     */
    private $db_version = '1.1.1';
    
    /**
     * Nom de la table des adhérents
     */
    private $table_name;
    
    /**
     * Constructeur privé pour le pattern Singleton
     */
    private function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'adherents';
        
        $this->init_hooks();
        $this->load_dependencies();
    }
    
    /**
     * Obtenir l'instance unique (Singleton)
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Initialisation des hooks WordPress
     */
    private function init_hooks() {
        // Hooks d'activation/désactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        register_uninstall_hook(__FILE__, array('GestionAdherents', 'uninstall'));
        
        // Hooks d'initialisation
        add_action('init', array($this, 'init'));
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        // Hook AJAX
        add_action('wp_ajax_ga_save_adherent', array($this, 'save_adherent_ajax'));
        add_action('wp_ajax_ga_delete_adherent', array($this, 'delete_adherent_ajax'));
        add_action('wp_ajax_ga_get_adherent', array($this, 'get_adherent_ajax'));
        
        // Internationalisation
        add_action('plugins_loaded', array($this, 'load_textdomain'));
    }
    
    /**
     * Chargement des dépendances
     */
    private function load_dependencies() {
        // Charger les classes
        require_once GA_PLUGIN_PATH . 'includes/class-ga-database.php';
        require_once GA_PLUGIN_PATH . 'includes/class-ga-admin.php';
        require_once GA_PLUGIN_PATH . 'includes/class-ga-validator.php';
        require_once GA_PLUGIN_PATH . 'includes/class-ga-sanitizer.php';
        
        // Charger la configuration si elle existe
        if (file_exists(GA_PLUGIN_PATH . 'includes/config.php')) {
            require_once GA_PLUGIN_PATH . 'includes/config.php';
        }
    }
    
    /**
     * Initialisation du plugin
     */
    public function init() {
        // Vérification des capacités utilisateur
        if (!current_user_can('manage_options')) {
            return;
        }
    }
    
    /**
     * Initialisation de l'administration
     */
    public function admin_init() {
        // Vérification de la version de la DB
        $this->check_database_version();
        
        // S'assurer que la table existe
        $this->ensure_table_exists();
    }
    
    /**
     * Activation du plugin
     */
    public function activate() {
        $this->create_database_tables();
        $this->set_default_options();
        
        // Mise à jour de la version
        update_option('ga_db_version', $this->db_version);
        
        // Capacités pour les rôles
        $this->add_capabilities();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Désactivation du plugin
     */
    public function deactivate() {
        // Nettoyage des tâches programmées si nécessaire
        wp_clear_scheduled_hook('ga_daily_cleanup');
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Désinstallation du plugin
     */
    public static function uninstall() {
        global $wpdb;
        
        // Suppression de la table
        $table_name = $wpdb->prefix . 'adherents';
        $wpdb->query("DROP TABLE IF EXISTS {$table_name}");
        
        // Suppression des options
        delete_option('ga_db_version');
        delete_option('ga_settings');
        
        // Suppression des capacités
        $roles = array('administrator', 'editor');
        foreach ($roles as $role_name) {
            $role = get_role($role_name);
            if ($role) {
                $role->remove_cap('manage_adherents');
                $role->remove_cap('edit_adherents');
                $role->remove_cap('delete_adherents');
            }
        }
    }
    
    /**
     * Création des tables de base de données
     */
    private function create_database_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$this->table_name} (
            id int(11) NOT NULL AUTO_INCREMENT,
            nom varchar(100) NOT NULL,
            prenom varchar(100) NOT NULL,
            date_naissance date NULL,
            email varchar(255) NOT NULL,
            numero_licence varchar(6) NULL,
            adresse_1 varchar(255) NULL,
            adresse_2 varchar(255) NULL,
            adresse_3 varchar(255) NULL,
            code_postal varchar(10) NULL,
            ville varchar(100) NULL,
            telephone varchar(20) NULL,
            date_adhesion date NOT NULL,
            date_fin_adhesion date NULL,
            is_junior tinyint(1) DEFAULT 0,
            is_pole_excellence tinyint(1) DEFAULT 0,
            wp_user_id bigint(20) unsigned NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by bigint(20) unsigned NULL,
            updated_by bigint(20) unsigned NULL,
            status enum('active', 'inactive', 'suspended') DEFAULT 'active',
            PRIMARY KEY (id),
            UNIQUE KEY email (email),
            UNIQUE KEY numero_licence (numero_licence),
            KEY wp_user_id (wp_user_id),
            KEY date_adhesion (date_adhesion),
            KEY status (status),
            KEY is_junior (is_junior),
            KEY is_pole_excellence (is_pole_excellence)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Vérification si la table a été créée
        if ($wpdb->get_var("SHOW TABLES LIKE '{$this->table_name}'") !== $this->table_name) {
            wp_die(__('Erreur lors de la création de la table des adhérents.', 'gestion-adherents'));
        }
    }
    
    /**
     * Définition des options par défaut
     */
    private function set_default_options() {
        $default_settings = array(
            'items_per_page' => 20,
            'date_format' => 'd/m/Y',
            'notification_email' => get_option('admin_email'),
            'auto_expire_notification' => true,
            'export_format' => 'csv'
        );
        
        if (!get_option('ga_settings')) {
            add_option('ga_settings', $default_settings);
        }
    }
    
    /**
     * Ajout des capacités aux rôles
     */
    private function add_capabilities() {
        $capabilities = array(
            'manage_adherents',
            'edit_adherents', 
            'delete_adherents'
        );
        
        $roles = array('administrator', 'editor');
        
        foreach ($roles as $role_name) {
            $role = get_role($role_name);
            if ($role) {
                foreach ($capabilities as $cap) {
                    $role->add_cap($cap);
                }
            }
        }
    }
    
    /**
     * Vérification de la version de la base de données
     */
    private function check_database_version() {
        $installed_version = get_option('ga_db_version');
        
        if ($installed_version !== $this->db_version) {
            $this->upgrade_database($installed_version);
            update_option('ga_db_version', $this->db_version);
        }
    }
    
    /**
     * Mise à jour de la base de données
     */
    private function upgrade_database($installed_version) {
        global $wpdb;
        
        // Mise à jour vers 1.1.0 - Ajout du numéro de licence
        if (version_compare($installed_version, '1.1.0', '<')) {
            $table_name = $this->table_name;
            
            // Vérifier si la colonne numero_licence existe déjà
            $column_exists = $wpdb->get_results("SHOW COLUMNS FROM {$table_name} LIKE 'numero_licence'");
            
            if (empty($column_exists)) {
                // Ajouter la colonne numero_licence
                $wpdb->query("ALTER TABLE {$table_name} ADD COLUMN numero_licence varchar(6) NULL AFTER email");
                
                // Ajouter l'index unique pour le numéro de licence
                $wpdb->query("ALTER TABLE {$table_name} ADD UNIQUE KEY numero_licence (numero_licence)");
                
                // Ajouter une notice d'information
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-info is-dismissible">';
                    echo '<p><strong>🔄 Plugin Gestion Adhérents mis à jour vers v1.1.0 :</strong> Champ "Numéro de licence" ajouté !</p>';
                    echo '</div>';
                });
            }
        }
    }
    
    /**
     * Ajout du menu d'administration
     */
    public function add_admin_menu() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Menu principal
        add_menu_page(
            __('Gestion Adhérents', 'gestion-adherents'),
            __('Adhérents', 'gestion-adherents'),
            'manage_options',
            'gestion-adherents',
            array($this, 'admin_page_adherents'),
            'dashicons-groups',
            26
        );
        
        // Sous-menus
        add_submenu_page(
            'gestion-adherents',
            __('Tous les adhérents', 'gestion-adherents'),
            __('Tous les adhérents', 'gestion-adherents'),
            'manage_options',
            'gestion-adherents',
            array($this, 'admin_page_adherents')
        );
        
        add_submenu_page(
            'gestion-adherents',
            __('Ajouter un adhérent', 'gestion-adherents'),
            __('Ajouter', 'gestion-adherents'),
            'manage_options',
            'gestion-adherents-add',
            array($this, 'admin_page_add_adherent')
        );
        
        add_submenu_page(
            'gestion-adherents',
            __('Paramètres', 'gestion-adherents'),
            __('Paramètres', 'gestion-adherents'),
            'manage_options',
            'gestion-adherents-settings',
            array($this, 'admin_page_settings')
        );
        
        add_submenu_page(
            'gestion-adherents',
            __('Export', 'gestion-adherents'),
            __('Export', 'gestion-adherents'),
            'manage_options',
            'gestion-adherents-export',
            array($this, 'admin_page_export')
        );
    }
    
    /**
     * Chargement des scripts et styles d'administration
     */
    public function admin_enqueue_scripts($hook) {
        // Charger uniquement sur nos pages
        if (strpos($hook, 'gestion-adherents') === false) {
            return;
        }
        
        wp_enqueue_script(
            'ga-admin-js',
            GA_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-util'),
            GA_PLUGIN_VERSION,
            true
        );
        
        wp_enqueue_style(
            'ga-admin-css',
            GA_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            GA_PLUGIN_VERSION
        );
        
        // Localisation pour AJAX
        wp_localize_script('ga-admin-js', 'ga_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ga_nonce'),
            'messages' => array(
                'confirm_delete' => __('Êtes-vous sûr de vouloir supprimer cet adhérent ?', 'gestion-adherents'),
                'error_occurred' => __('Une erreur est survenue.', 'gestion-adherents'),
                'success_saved' => __('Adhérent sauvegardé avec succès.', 'gestion-adherents'),
                'success_deleted' => __('Adhérent supprimé avec succès.', 'gestion-adherents')
            )
        ));
    }
    
    /**
     * Page d'administration - Liste des adhérents
     */
    public function admin_page_adherents() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions suffisantes.', 'gestion-adherents'));
        }
        
        $admin = new GA_Admin();
        $admin->display_adherents_list();
    }
    
    /**
     * Page d'administration - Ajouter un adhérent
     */
    public function admin_page_add_adherent() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions suffisantes.', 'gestion-adherents'));
        }
        
        $admin = new GA_Admin();
        $admin->display_add_adherent_form();
    }
    
    /**
     * Page d'administration - Paramètres
     */
    public function admin_page_settings() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions suffisantes.', 'gestion-adherents'));
        }
        
        $admin = new GA_Admin();
        $admin->display_settings_page();
    }
    
    /**
     * Page d'administration - Export
     */
    public function admin_page_export() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions suffisantes.', 'gestion-adherents'));
        }
        
        $admin = new GA_Admin();
        $admin->display_export_page();
    }
    
    /**
     * Gestion AJAX - Sauvegarde d'un adhérent
     */
    public function save_adherent_ajax() {
        // Vérification de sécurité
        if (!wp_verify_nonce($_POST['nonce'], 'ga_nonce')) {
            wp_die(__('Erreur de sécurité.', 'gestion-adherents'));
        }
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Permissions insuffisantes.', 'gestion-adherents'));
        }
        
        $sanitizer = new GA_Sanitizer();
        $validator = new GA_Validator();
        $database = new GA_Database();
        
        // Sanitisation des données
        $data = $sanitizer->sanitize_adherent_data($_POST);
        
        // Validation des données
        $validation_result = $validator->validate_adherent_data($data);
        
        if (!$validation_result['valid']) {
            wp_send_json_error(array(
                'message' => __('Données invalides.', 'gestion-adherents'),
                'errors' => $validation_result['errors']
            ));
        }
        
        // Sauvegarde en base
        $result = $database->save_adherent($data);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('Adhérent sauvegardé avec succès.', 'gestion-adherents'),
                'adherent_id' => $result
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Erreur lors de la sauvegarde.', 'gestion-adherents')
            ));
        }
    }
    
    /**
     * Gestion AJAX - Suppression d'un adhérent
     */
    public function delete_adherent_ajax() {
        if (!wp_verify_nonce($_POST['nonce'], 'ga_nonce')) {
            wp_die(__('Erreur de sécurité.', 'gestion-adherents'));
        }
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Permissions insuffisantes.', 'gestion-adherents'));
        }
        
        $adherent_id = intval($_POST['adherent_id']);
        
        if (!$adherent_id) {
            wp_send_json_error(array(
                'message' => __('ID adhérent invalide.', 'gestion-adherents')
            ));
        }
        
        $database = new GA_Database();
        $result = $database->delete_adherent($adherent_id);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('Adhérent supprimé avec succès.', 'gestion-adherents'),
                'redirect_url' => add_query_arg(
                    array(
                        'page' => 'gestion-adherents',
                        'message' => 'deleted'
                    ),
                    admin_url('admin.php')
                )
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Erreur lors de la suppression.', 'gestion-adherents')
            ));
        }
    }
    
    /**
     * Gestion AJAX - Récupération d'un adhérent
     */
    public function get_adherent_ajax() {
        if (!wp_verify_nonce($_POST['nonce'], 'ga_nonce')) {
            wp_die(__('Erreur de sécurité.', 'gestion-adherents'));
        }
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Permissions insuffisantes.', 'gestion-adherents'));
        }
        
        $adherent_id = intval($_POST['adherent_id']);
        
        if (!$adherent_id) {
            wp_send_json_error(array(
                'message' => __('ID adhérent invalide.', 'gestion-adherents')
            ));
        }
        
        $database = new GA_Database();
        $adherent = $database->get_adherent($adherent_id);
        
        if ($adherent) {
            wp_send_json_success(array(
                'adherent' => $adherent
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Adhérent non trouvé.', 'gestion-adherents')
            ));
        }
    }
    
    /**
     * Diagnostic et création de table si nécessaire
     */
    public function ensure_table_exists() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'adherents';
        
        // Vérifier si la table existe
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
        
        if ($table_exists != $table_name) {
            // La table n'existe pas, la créer
            $this->create_database_tables();
            
            // Ajouter une notice de succès
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible">';
                echo '<p><strong>✅ Plugin Gestion Adhérents :</strong> Table de base de données créée avec succès !</p>';
                echo '</div>';
            });
        }
    }
    
    /**
     * Chargement des traductions
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'gestion-adherents',
            false,
            dirname(GA_PLUGIN_BASENAME) . '/languages/'
        );
    }
    
    /**
     * Getter pour le nom de la table
     */
    public function get_table_name() {
        return $this->table_name;
    }
}

// Initialisation du plugin
function init_gestion_adherents() {
    return GestionAdherents::get_instance();
}

// Hook d'initialisation
add_action('plugins_loaded', 'init_gestion_adherents');

/**
 * Fonction d'aide pour récupérer l'instance du plugin
 */
function GA() {
    return GestionAdherents::get_instance();
}