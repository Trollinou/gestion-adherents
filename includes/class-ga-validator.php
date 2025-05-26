<?php
/**
 * Classe de validation des données
 * 
 * @package GestionAdherents
 * @version 1.0.0
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe de validation des données
 */
class GA_Validator {
    
    /**
     * Valider les données d'un adhérent
     */
    public function validate_adherent_data($data) {
        $errors = array();
        
        // Validation du nom
        if (empty($data['nom']) || strlen(trim($data['nom'])) < 2) {
            $errors['nom'] = __('Le nom doit contenir au moins 2 caractères.', 'gestion-adherents');
        }
        
        // Validation du prénom
        if (empty($data['prenom']) || strlen(trim($data['prenom'])) < 2) {
            $errors['prenom'] = __('Le prénom doit contenir au moins 2 caractères.', 'gestion-adherents');
        }
        
        // Validation de l'email
        if (empty($data['email']) || !is_email($data['email'])) {
            $errors['email'] = __('Adresse email invalide.', 'gestion-adherents');
        } else {
            // Vérifier l'unicité de l'email
            if ($this->email_exists($data['email'], isset($data['id']) ? $data['id'] : 0)) {
                $errors['email'] = __('Cette adresse email est déjà utilisée.', 'gestion-adherents');
            }
        }
        
        // Validation du numéro de licence
        if (!empty($data['numero_licence']) && !$this->validate_license_number($data['numero_licence'])) {
            $errors['numero_licence'] = __('Le numéro de licence doit contenir une lettre suivie de 5 chiffres (ex: A12345).', 'gestion-adherents');
        } elseif (!empty($data['numero_licence']) && $this->license_exists($data['numero_licence'], isset($data['id']) ? $data['id'] : 0)) {
            $errors['numero_licence'] = __('Ce numéro de licence est déjà utilisé par un autre adhérent.', 'gestion-adherents');
        }
        
        // Validation de la date de naissance
        if (!empty($data['date_naissance']) && !$this->validate_date($data['date_naissance'])) {
            $errors['date_naissance'] = __('Date de naissance invalide.', 'gestion-adherents');
        }
        
        // Validation de la date d'adhésion
        if (empty($data['date_adhesion']) || !$this->validate_date($data['date_adhesion'])) {
            $errors['date_adhesion'] = __('Date d\'adhésion requise et valide.', 'gestion-adherents');
        }
        
        // Validation de la date de fin d'adhésion
        if (!empty($data['date_fin_adhesion'])) {
            if (!$this->validate_date($data['date_fin_adhesion'])) {
                $errors['date_fin_adhesion'] = __('Date de fin d\'adhésion invalide.', 'gestion-adherents');
            } elseif (!empty($data['date_adhesion']) && $data['date_fin_adhesion'] <= $data['date_adhesion']) {
                $errors['date_fin_adhesion'] = __('La date de fin doit être postérieure à la date d\'adhésion.', 'gestion-adherents');
            }
        }
        
        // Validation du téléphone
        if (!empty($data['telephone']) && !$this->validate_phone($data['telephone'])) {
            $errors['telephone'] = __('Numéro de téléphone invalide.', 'gestion-adherents');
        }
        
        // Validation du code postal
        if (!empty($data['code_postal']) && !$this->validate_postal_code($data['code_postal'])) {
            $errors['code_postal'] = __('Code postal invalide.', 'gestion-adherents');
        }
        
        // Validation de l'utilisateur WordPress
        if (!empty($data['wp_user_id']) && !$this->user_exists($data['wp_user_id'])) {
            $errors['wp_user_id'] = __('Utilisateur WordPress invalide.', 'gestion-adherents');
        }
        
        return array(
            'valid' => empty($errors),
            'errors' => $errors
        );
    }
    
    /**
     * Valider une date
     */
    private function validate_date($date) {
        if (empty($date)) {
            return false;
        }
        
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
    
    /**
     * Valider un numéro de téléphone
     */
    private function validate_phone($phone) {
        // Pattern pour téléphones français (basique)
        $pattern = '/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/';
        return preg_match($pattern, $phone);
    }
    
    /**
     * Valider un numéro de licence (1 lettre + 5 chiffres)
     */
    private function validate_license_number($license) {
        // Pattern : 1 lettre (A-Z) suivie de 5 chiffres
        return preg_match('/^[A-Z][0-9]{5}$/', strtoupper($license));
    }
    
    /**
     * Vérifier si un numéro de licence existe déjà
     */
    private function license_exists($license, $exclude_id = 0) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'adherents';
        $sql = $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_name} WHERE numero_licence = %s AND id != %d",
            strtoupper($license),
            $exclude_id
        );
        
        return $wpdb->get_var($sql) > 0;
    }
    
    /**
     * Valider un code postal français
     */
    private function validate_postal_code($code) {
        return preg_match('/^[0-9]{5}$/', $code);
    }
    
    /**
     * Vérifier si un email existe déjà
     */
    private function email_exists($email, $exclude_id = 0) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'adherents';
        $sql = $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_name} WHERE email = %s AND id != %d",
            $email,
            $exclude_id
        );
        
        return $wpdb->get_var($sql) > 0;
    }
    
    /**
     * Vérifier si un utilisateur WordPress existe
     */
    private function user_exists($user_id) {
        return get_user_by('id', $user_id) !== false;
    }
}
        