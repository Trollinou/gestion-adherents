<?php
/**
 * Classe de nettoyage des données - VERSION CORRIGÉE
 * 
 * @package GestionAdherents
 * @version 1.1.2
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe de nettoyage des données
 */
class GA_Sanitizer {
    
    /**
     * Nettoyer les données d'un adhérent
     */
    public function sanitize_adherent_data($data) {
        $sanitized = array();
        
        // ID
        if (isset($data['id'])) {
            $sanitized['id'] = intval($data['id']);
        }
        
        // Champs texte
        $text_fields = array('nom', 'prenom', 'adresse_1', 'adresse_2', 'adresse_3', 'ville');
        foreach ($text_fields as $field) {
            if (isset($data[$field])) {
                $sanitized[$field] = sanitize_text_field($data[$field]);
            }
        }
        
        // Email
        if (isset($data['email'])) {
            $sanitized['email'] = sanitize_email($data['email']);
        }
        
        // Numéro de licence
        if (isset($data['numero_licence'])) {
            if (empty($data['numero_licence']) || trim($data['numero_licence']) === '') {
                $sanitized['numero_licence'] = null;
            } else {
                // Nettoyer et convertir en majuscules
                $sanitized['numero_licence'] = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $data['numero_licence']));
            }
        }
        
        // Dates
        $date_fields = array('date_naissance', 'date_adhesion', 'date_fin_adhesion');
        foreach ($date_fields as $field) {
            if (isset($data[$field])) {
                if (empty($data[$field]) || trim($data[$field]) === '') {
                    $sanitized[$field] = null;
                } else {
                    $sanitized[$field] = sanitize_text_field($data[$field]);
                }
            }
        }
        
        // Téléphone et code postal
        if (isset($data['telephone'])) {
            if (empty($data['telephone']) || trim($data['telephone']) === '') {
                $sanitized['telephone'] = null;
            } else {
                $sanitized['telephone'] = preg_replace('/[^0-9+\s\.-]/', '', $data['telephone']);
            }
        }
        
        if (isset($data['code_postal'])) {
            if (empty($data['code_postal']) || trim($data['code_postal']) === '') {
                $sanitized['code_postal'] = null;
            } else {
                $sanitized['code_postal'] = preg_replace('/[^0-9]/', '', $data['code_postal']);
            }
        }
        
        // Booléens
        $sanitized['is_junior'] = isset($data['is_junior']) ? (bool) $data['is_junior'] : false;
        $sanitized['is_pole_excellence'] = isset($data['is_pole_excellence']) ? (bool) $data['is_pole_excellence'] : false;
        
        // Utilisateur WordPress
        if (isset($data['wp_user_id'])) {
            if (empty($data['wp_user_id']) || $data['wp_user_id'] === '' || $data['wp_user_id'] === '0') {
                $sanitized['wp_user_id'] = null; // Délier explicitement
            } else {
                $sanitized['wp_user_id'] = intval($data['wp_user_id']);
            }
        }
        
        // Statut
        if (isset($data['status'])) {
            $allowed_status = array('active', 'inactive', 'suspended');
            $sanitized['status'] = in_array($data['status'], $allowed_status) ? $data['status'] : 'active';
        }
        
        return $sanitized;
    }
}