<?php
/**
 * Classe de gestion de la base de données
 * 
 * @package GestionAdherents
 * @version 1.0.0
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe de gestion de la base de données
 */
class GA_Database {
    
    private $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'adherents';
    }
    
    /**
     * Sauvegarder un adhérent
     */
    public function save_adherent($data) {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        // Nettoyer les valeurs NULL pour wp_user_id
        if (isset($data['wp_user_id']) && ($data['wp_user_id'] === '' || $data['wp_user_id'] === 0)) {
            $data['wp_user_id'] = null;
        }
        
        if (isset($data['id']) && $data['id']) {
            // Mise à jour
            $data['updated_by'] = $user_id;
            unset($data['created_at'], $data['created_by']);
            
            $result = $wpdb->update(
                $this->table_name,
                $data,
                array('id' => $data['id']),
                $this->get_format_array($data),
                array('%d')
            );
            
            return $result !== false ? $data['id'] : false;
        } else {
            // Insertion
            unset($data['id']);
            $data['created_by'] = $user_id;
            
            $result = $wpdb->insert(
                $this->table_name,
                $data,
                $this->get_format_array($data)
            );
            
            return $result ? $wpdb->insert_id : false;
        }
    }
    
    /**
     * Récupérer un adhérent par ID
     */
    public function get_adherent($id) {
        global $wpdb;
        
        $sql = $wpdb->prepare("
            SELECT a.*, u.display_name as wp_user_name 
            FROM {$this->table_name} a 
            LEFT JOIN {$wpdb->users} u ON a.wp_user_id = u.ID 
            WHERE a.id = %d
        ", $id);
        
        return $wpdb->get_row($sql, ARRAY_A);
    }
    
    /**
     * Récupérer tous les adhérents avec pagination
     */
    public function get_adherents($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'per_page' => 20,
            'page' => 1,
            'orderby' => 'nom',
            'order' => 'ASC',
            'search' => '',
            'status' => '',
            'is_junior' => '',
            'is_pole_excellence' => ''
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where_conditions = array('1=1');
        $where_values = array();
        
        // Recherche
        if (!empty($args['search'])) {
            $where_conditions[] = "(nom LIKE %s OR prenom LIKE %s OR email LIKE %s OR numero_licence LIKE %s)";
            $search_term = '%' . $wpdb->esc_like($args['search']) . '%';
            $where_values[] = $search_term;
            $where_values[] = $search_term;
            $where_values[] = $search_term;
            $where_values[] = $search_term;
        }
        
        // Filtres
        if (!empty($args['status'])) {
            $where_conditions[] = "status = %s";
            $where_values[] = $args['status'];
        }
        
        if ($args['is_junior'] !== '') {
            $where_conditions[] = "is_junior = %d";
            $where_values[] = (int) $args['is_junior'];
        }
        
        if ($args['is_pole_excellence'] !== '') {
            $where_conditions[] = "is_pole_excellence = %d";
            $where_values[] = (int) $args['is_pole_excellence'];
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        // Ordre
        $allowed_orderby = array('nom', 'prenom', 'email', 'date_adhesion', 'created_at');
        $orderby = in_array($args['orderby'], $allowed_orderby) ? $args['orderby'] : 'nom';
        $order = strtoupper($args['order']) === 'DESC' ? 'DESC' : 'ASC';
        
        // Pagination
        $offset = ($args['page'] - 1) * $args['per_page'];
        
        // Requête principale
        $sql = "
            SELECT a.*, u.display_name as wp_user_name 
            FROM {$this->table_name} a 
            LEFT JOIN {$wpdb->users} u ON a.wp_user_id = u.ID 
            WHERE {$where_clause}
            ORDER BY {$orderby} {$order}
            LIMIT %d OFFSET %d
        ";
        
        $where_values[] = $args['per_page'];
        $where_values[] = $offset;
        
        if (!empty($where_values)) {
            $sql = $wpdb->prepare($sql, $where_values);
        }
        
        $results = $wpdb->get_results($sql, ARRAY_A);
        
        // Compter le total
        $count_sql = "SELECT COUNT(*) FROM {$this->table_name} a WHERE {$where_clause}";
        if (!empty($where_values)) {
            // Enlever les valeurs de pagination pour le count
            $count_values = array_slice($where_values, 0, -2);
            if (!empty($count_values)) {
                $count_sql = $wpdb->prepare($count_sql, $count_values);
            }
        }
        $total = $wpdb->get_var($count_sql);
        
        return array(
            'adherents' => $results,
            'total' => $total,
            'pages' => ceil($total / $args['per_page'])
        );
    }
    
    /**
     * Supprimer un adhérent
     */
    public function delete_adherent($id) {
        global $wpdb;
        
        return $wpdb->delete(
            $this->table_name,
            array('id' => $id),
            array('%d')
        );
    }
    
    /**
     * Obtenir le format pour wpdb
     */
    private function get_format_array($data) {
        $formats = array();
        
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'id':
                case 'wp_user_id':
                case 'created_by':
                case 'updated_by':
                case 'is_junior':
                case 'is_pole_excellence':
                    $formats[] = '%d';
                    break;
                case 'date_naissance':
                case 'date_adhesion':
                case 'date_fin_adhesion':
                case 'created_at':
                case 'updated_at':
                    $formats[] = '%s';
                    break;
                default:
                    $formats[] = '%s';
                    break;
            }
        }
        
        return $formats;
    }
}