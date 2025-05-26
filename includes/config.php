<?php
/**
 * Fichier de configuration du plugin Gestion d'Adhérents
 * 
 * Contient toutes les constantes et configurations par défaut
 * 
 * @package GestionAdherents
 * @version 1.0.0
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Configuration générale du plugin
 */
class GA_Config {
    
    /**
     * Version du plugin
     */
    const VERSION = '1.0.0';
    
    /**
     * Version minimale de WordPress requise
     */
    const MIN_WP_VERSION = '5.0';
    
    /**
     * Version minimale de PHP requise
     */
    const MIN_PHP_VERSION = '7.4';
    
    /**
     * Nom de la table principale
     */
    const TABLE_NAME = 'adherents';
    
    /**
     * Préfixe pour les options
     */
    const OPTION_PREFIX = 'ga_';
    
    /**
     * Nom du text domain pour les traductions
     */
    const TEXT_DOMAIN = 'gestion-adherents';
    
    /**
     * Capacités requises
     */
    const CAPABILITIES = array(
        'manage_adherents' => 'Gérer les adhérents',
        'edit_adherents' => 'Modifier les adhérents', 
        'delete_adherents' => 'Supprimer les adhérents',
        'view_adherents' => 'Voir les adhérents',
        'export_adherents' => 'Exporter les adhérents',
        'import_adherents' => 'Importer les adhérents'
    );
    
    /**
     * Statuts possibles pour les adhérents
     */
    const STATUSES = array(
        'active' => 'Actif',
        'inactive' => 'Inactif',
        'suspended' => 'Suspendu'
    );
    
    /**
     * Types de champs autorisés
     */
    const FIELD_TYPES = array(
        'text' => 'Texte',
        'email' => 'Email',
        'tel' => 'Téléphone',
        'date' => 'Date',
        'select' => 'Liste déroulante',
        'checkbox' => 'Case à cocher',
        'textarea' => 'Zone de texte'
    );
    
    /**
     * Formats d'export supportés
     */
    const EXPORT_FORMATS = array(
        'csv' => 'CSV',
        'xlsx' => 'Excel (XLSX)',
        'pdf' => 'PDF'
    );
    
    /**
     * Taille maximale pour les uploads (en bytes)
     */
    const MAX_UPLOAD_SIZE = 5242880; // 5MB
    
    /**
     * Extensions de fichiers autorisées pour l'import
     */
    const ALLOWED_IMPORT_EXTENSIONS = array('csv', 'xlsx', 'xls');
    
    /**
     * Nombre maximum d'adhérents par page
     */
    const MAX_ITEMS_PER_PAGE = 100;
    
    /**
     * Nombre par défaut d'adhérents par page
     */
    const DEFAULT_ITEMS_PER_PAGE = 20;
    
    /**
     * Délai d'expiration pour les notifications (en jours)
     */
    const NOTIFICATION_DELAY_DAYS = 30;
    
    /**
     * Durée de conservation des logs (en jours)
     */
    const LOG_RETENTION_DAYS = 90;
    
    /**
     * Configuration des validations
     */
    const VALIDATION_RULES = array(
        'nom' => array(
            'required' => true,
            'min_length' => 2,
            'max_length' => 100,
            'pattern' => '/^[a-zA-ZÀ-ÿ\s\-\']+$/'
        ),
        'prenom' => array(
            'required' => true,
            'min_length' => 2,
            'max_length' => 100,
            'pattern' => '/^[a-zA-ZÀ-ÿ\s\-\']+$/'
        ),
        'email' => array(
            'required' => true,
            'unique' => true,
            'max_length' => 255
        ),
        'telephone' => array(
            'required' => false,
            'pattern' => '/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/',
            'max_length' => 20
        ),
        'code_postal' => array(
            'required' => false,
            'pattern' => '/^[0-9]{5}$/',
            'max_length' => 10
        ),
        'date_naissance' => array(
            'required' => false,
            'format' => 'Y-m-d',
            'min_age' => 0,
            'max_age' => 120
        ),
        'date_adhesion' => array(
            'required' => true,
            'format' => 'Y-m-d'
        )
    );
    
    /**
     * Configuration des emails
     */
    const EMAIL_CONFIG = array(
        'from_name' => 'Gestion Adhérents',
        'content_type' => 'text/html',
        'charset' => 'UTF-8',
        'templates_dir' => 'templates/emails/'
    );
    
    /**
     * Configuration de sécurité
     */
    const SECURITY_CONFIG = array(
        'max_login_attempts' => 5,
        'lockout_duration' => 1800, // 30 minutes
        'session_timeout' => 3600, // 1 heure
        'password_min_length' => 8,
        'require_special_chars' => true,
        'csrf_token_lifetime' => 3600 // 1 heure
    );
    
    /**
     * Configuration du cache
     */
    const CACHE_CONFIG = array(
        'stats_ttl' => 3600, // 1 heure
        'reports_ttl' => 86400, // 24 heures
        'search_ttl' => 1800, // 30 minutes
        'export_ttl' => 600 // 10 minutes
    );
    
    /**
     * URLs d'API externes
     */
    const EXTERNAL_APIS = array(
        'postal_code' => 'https://api-adresse.data.gouv.fr/search/',
        'phone_validation' => 'https://api.phone-validator.net/api/v2/',
        'email_validation' => 'https://api.emailvalidation.io/v1/validation'
    );
    
    /**
     * Configuration des hooks personnalisés
     */
    const CUSTOM_HOOKS = array(
        'actions' => array(
            'ga_adherent_created',
            'ga_adherent_updated', 
            'ga_adherent_deleted',
            'ga_adherent_activated',
            'ga_adherent_suspended',
            'ga_membership_expired',
            'ga_bulk_action_completed',
            'ga_export_completed',
            'ga_import_completed'
        ),
        'filters' => array(
            'ga_before_save_adherent',
            'ga_after_save_adherent',
            'ga_search_args',
            'ga_export_fields',
            'ga_import_data',
            'ga_email_content',
            'ga_notification_recipients',
            'ga_dashboard_stats'
        )
    );
    
    /**
     * Messages par défaut
     */
    const DEFAULT_MESSAGES = array(
        'success' => array(
            'created' => 'Adhérent créé avec succès.',
            'updated' => 'Adhérent mis à jour avec succès.',
            'deleted' => 'Adhérent supprimé avec succès.',
            'exported' => 'Export terminé avec succès.',
            'imported' => 'Import terminé avec succès.',
            'settings_saved' => 'Paramètres sauvegardés avec succès.'
        ),
        'error' => array(
            'general' => 'Une erreur est survenue.',
            'permission' => 'Permissions insuffisantes.',
            'not_found' => 'Adhérent non trouvé.',
            'validation' => 'Données invalides.',
            'database' => 'Erreur de base de données.',
            'upload' => 'Erreur lors du téléchargement.',
            'export' => 'Erreur lors de l\'export.',
            'import' => 'Erreur lors de l\'import.'
        ),
        'warning' => array(
            'duplicate_email' => 'Cette adresse email existe déjà.',
            'membership_expired' => 'L\'adhésion a expiré.',
            'incomplete_data' => 'Données incomplètes.',
            'large_export' => 'L\'export peut prendre du temps.',
            'unsaved_changes' => 'Modifications non sauvegardées.'
        )
    );
    
    /**
     * Configuration des rapports
     */
    const REPORTS_CONFIG = array(
        'types' => array(
            'monthly' => 'Rapport mensuel',
            'annual' => 'Rapport annuel',
            'custom' => 'Rapport personnalisé',
            'membership_status' => 'Statut des adhésions',
            'demographics' => 'Données démographiques',
            'activity' => 'Rapport d\'activité'
        ),
        'formats' => array(
            'html' => 'HTML',
            'pdf' => 'PDF',
            'csv' => 'CSV',
            'excel' => 'Excel'
        ),
        'charts' => array(
            'pie' => 'Graphique en secteurs',
            'bar' => 'Graphique en barres',
            'line' => 'Graphique linéaire',
            'area' => 'Graphique en aires'
        )
    );
    
    /**
     * Obtenir une configuration
     */
    public static function get($key, $default = null) {
        $config_map = array(
            'version' => self::VERSION,
            'min_wp_version' => self::MIN_WP_VERSION,
            'min_php_version' => self::MIN_PHP_VERSION,
            'table_name' => self::TABLE_NAME,
            'option_prefix' => self::OPTION_PREFIX,
            'text_domain' => self::TEXT_DOMAIN,
            'capabilities' => self::CAPABILITIES,
            'statuses' => self::STATUSES,
            'field_types' => self::FIELD_TYPES,
            'export_formats' => self::EXPORT_FORMATS,
            'max_upload_size' => self::MAX_UPLOAD_SIZE,
            'allowed_import_extensions' => self::ALLOWED_IMPORT_EXTENSIONS,
            'max_items_per_page' => self::MAX_ITEMS_PER_PAGE,
            'default_items_per_page' => self::DEFAULT_ITEMS_PER_PAGE,
            'notification_delay_days' => self::NOTIFICATION_DELAY_DAYS,
            'log_retention_days' => self::LOG_RETENTION_DAYS,
            'validation_rules' => self::VALIDATION_RULES,
            'email_config' => self::EMAIL_CONFIG,
            'security_config' => self::SECURITY_CONFIG,
            'cache_config' => self::CACHE_CONFIG,
            'external_apis' => self::EXTERNAL_APIS,
            'custom_hooks' => self::CUSTOM_HOOKS,
            'default_messages' => self::DEFAULT_MESSAGES,
            'reports_config' => self::REPORTS_CONFIG
        );
        
        return isset($config_map[$key]) ? $config_map[$key] : $default;
    }
    
    /**
     * Vérifier la compatibilité système
     */
    public static function check_system_requirements() {
        $errors = array();
        
        // Vérifier la version de PHP
        if (version_compare(PHP_VERSION, self::MIN_PHP_VERSION, '<')) {
            $errors[] = sprintf(
                'PHP version %s ou supérieure requise. Version actuelle: %s',
                self::MIN_PHP_VERSION,
                PHP_VERSION
            );
        }
        
        // Vérifier la version de WordPress
        global $wp_version;
        if (version_compare($wp_version, self::MIN_WP_VERSION, '<')) {
            $errors[] = sprintf(
                'WordPress version %s ou supérieure requise. Version actuelle: %s',
                self::MIN_WP_VERSION,
                $wp_version
            );
        }
        
        // Vérifier les extensions PHP requises
        $required_extensions = array('mysqli', 'json', 'mbstring');
        foreach ($required_extensions as $extension) {
            if (!extension_loaded($extension)) {
                $errors[] = sprintf('Extension PHP requise: %s', $extension);
            }
        }
        
        // Vérifier les permissions de fichiers
        if (!is_writable(WP_CONTENT_DIR)) {
            $errors[] = 'Le dossier wp-content doit être accessible en écriture';
        }
        
        return $errors;
    }
    
    /**
     * Obtenir les paramètres par défaut
     */
    public static function get_default_settings() {
        return array(
            'items_per_page' => self::DEFAULT_ITEMS_PER_PAGE,
            'date_format' => 'd/m/Y',
            'notification_email' => get_option('admin_email'),
            'auto_expire_notification' => true,
            'export_format' => 'csv',
            'enable_logs' => true,
            'log_level' => 'info',
            'cache_enabled' => true,
            'auto_backup' => false,
            'email_notifications' => true,
            'require_confirmation' => true,
            'allow_self_registration' => false,
            'default_status' => 'active',
            'auto_junior_classification' => true,
            'junior_age_limit' => 18,
            'membership_duration_months' => 12,
            'grace_period_days' => 30,
            'max_export_records' => 10000,
            'session_timeout_minutes' => 60,
            'password_complexity' => 'medium',
            'two_factor_auth' => false,
            'api_enabled' => false,
            'webhook_enabled' => false,
            'audit_trail' => true,
            'data_retention_years' => 7,
            'gdpr_compliance' => true,
            'anonymize_on_delete' => true
        );
    }
    
    /**
     * Obtenir les réglages de validation par champ
     */
    public static function get_field_validation($field_name) {
        return isset(self::VALIDATION_RULES[$field_name]) ? self::VALIDATION_RULES[$field_name] : array();
    }
    
    /**
     * Obtenir les messages par type
     */
    public static function get_messages($type = 'all') {
        if ($type === 'all') {
            return self::DEFAULT_MESSAGES;
        }
        
        return isset(self::DEFAULT_MESSAGES[$type]) ? self::DEFAULT_MESSAGES[$type] : array();
    }
    
    /**
     * Vérifier si une capacité est valide
     */
    public static function is_valid_capability($capability) {
        return array_key_exists($capability, self::CAPABILITIES);
    }
    
    /**
     * Vérifier si un statut est valide
     */
    public static function is_valid_status($status) {
        return array_key_exists($status, self::STATUSES);
    }
    
    /**
     * Vérifier si un format d'export est supporté
     */
    public static function is_valid_export_format($format) {
        return array_key_exists($format, self::EXPORT_FORMATS);
    }
    
    /**
     * Obtenir la configuration complète
     */
    public static function get_all_config() {
        return array(
            'plugin' => array(
                'version' => self::VERSION,
                'min_wp_version' => self::MIN_WP_VERSION,
                'min_php_version' => self::MIN_PHP_VERSION,
                'text_domain' => self::TEXT_DOMAIN
            ),
            'database' => array(
                'table_name' => self::TABLE_NAME,
                'option_prefix' => self::OPTION_PREFIX
            ),
            'security' => self::SECURITY_CONFIG,
            'validation' => self::VALIDATION_RULES,
            'email' => self::EMAIL_CONFIG,
            'cache' => self::CACHE_CONFIG,
            'capabilities' => self::CAPABILITIES,
            'statuses' => self::STATUSES,
            'field_types' => self::FIELD_TYPES,
            'export_formats' => self::EXPORT_FORMATS,
            'external_apis' => self::EXTERNAL_APIS,
            'hooks' => self::CUSTOM_HOOKS,
            'messages' => self::DEFAULT_MESSAGES,
            'reports' => self::REPORTS_CONFIG,
            'limits' => array(
                'max_upload_size' => self::MAX_UPLOAD_SIZE,
                'max_items_per_page' => self::MAX_ITEMS_PER_PAGE,
                'default_items_per_page' => self::DEFAULT_ITEMS_PER_PAGE,
                'notification_delay_days' => self::NOTIFICATION_DELAY_DAYS,
                'log_retention_days' => self::LOG_RETENTION_DAYS
            ),
            'allowed_extensions' => self::ALLOWED_IMPORT_EXTENSIONS
        );
    }
}

/**
 * Constantes globales pour compatibilité
 */
if (!defined('GA_VERSION')) {
    define('GA_VERSION', GA_Config::VERSION);
}

if (!defined('GA_MIN_WP_VERSION')) {
    define('GA_MIN_WP_VERSION', GA_Config::MIN_WP_VERSION);
}

if (!defined('GA_MIN_PHP_VERSION')) {
    define('GA_MIN_PHP_VERSION', GA_Config::MIN_PHP_VERSION);
}

if (!defined('GA_TEXT_DOMAIN')) {
    define('GA_TEXT_DOMAIN', GA_Config::TEXT_DOMAIN);
}

if (!defined('GA_TABLE_NAME')) {
    define('GA_TABLE_NAME', GA_Config::TABLE_NAME);
}

if (!defined('GA_OPTION_PREFIX')) {
    define('GA_OPTION_PREFIX', GA_Config::OPTION_PREFIX);
}

/**
 * Fonction d'aide pour accéder à la configuration
 */
function ga_config($key = null, $default = null) {
    if ($key === null) {
        return GA_Config::get_all_config();
    }
    
    return GA_Config::get($key, $default);
}

/**
 * Fonction d'aide pour les messages
 */
function ga_message($type, $key, $default = '') {
    $messages = GA_Config::get_messages($type);
    return isset($messages[$key]) ? $messages[$key] : $default;
}

/**
 * Fonction d'aide pour la validation
 */
function ga_validation_rule($field, $rule = null) {
    $rules = GA_Config::get_field_validation($field);
    
    if ($rule === null) {
        return $rules;
    }
    
    return isset($rules[$rule]) ? $rules[$rule] : null;
}

/**
 * Configuration d'environnement
 */
class GA_Environment {
    
    /**
     * Détecter l'environnement (dev, staging, production)
     */
    public static function detect() {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            return 'development';
        }
        
        if (strpos(home_url(), 'staging') !== false || strpos(home_url(), 'dev') !== false) {
            return 'staging';
        }
        
        return 'production';
    }
    
    /**
     * Vérifier si on est en développement
     */
    public static function is_development() {
        return self::detect() === 'development';
    }
    
    /**
     * Vérifier si on est en production
     */
    public static function is_production() {
        return self::detect() === 'production';
    }
    
    /**
     * Obtenir la configuration spécifique à l'environnement
     */
    public static function get_config() {
        $env = self::detect();
        
        $configs = array(
            'development' => array(
                'debug_mode' => true,
                'log_level' => 'debug',
                'cache_enabled' => false,
                'minify_assets' => false,
                'show_errors' => true,
                'performance_monitoring' => true
            ),
            'staging' => array(
                'debug_mode' => false,
                'log_level' => 'warning',
                'cache_enabled' => true,
                'minify_assets' => true,
                'show_errors' => false,
                'performance_monitoring' => true
            ),
            'production' => array(
                'debug_mode' => false,
                'log_level' => 'error',
                'cache_enabled' => true,
                'minify_assets' => true,
                'show_errors' => false,
                'performance_monitoring' => false
            )
        );
        
        return isset($configs[$env]) ? $configs[$env] : $configs['production'];
    }
}

/**
 * Vérification des prérequis au chargement
 */
function ga_check_requirements() {
    $errors = GA_Config::check_system_requirements();
    
    if (!empty($errors)) {
        add_action('admin_notices', function() use ($errors) {
            echo '<div class="notice notice-error"><p>';
            echo '<strong>Plugin Gestion Adhérents :</strong> Prérequis non satisfaits :<br>';
            foreach ($errors as $error) {
                echo '• ' . esc_html($error) . '<br>';
            }
            echo '</p></div>';
        });
        
        return false;
    }
    
    return true;
}

// Vérifier les prérequis
ga_check_requirements();