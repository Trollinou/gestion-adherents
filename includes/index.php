<?php
/**
 * Fichier de sécurité pour empêcher l'accès direct aux dossiers
 * 
 * Ce fichier doit être placé dans chaque dossier du plugin :
 * - /gestion-adherents/index.php
 * - /gestion-adherents/assets/index.php
 * - /gestion-adherents/assets/css/index.php
 * - /gestion-adherents/assets/js/index.php
 * - /gestion-adherents/includes/index.php
 * - /gestion-adherents/languages/index.php
 * 
 * @package GestionAdherents
 * @version 1.0.0
 */

// Empêcher l'accès direct à ce fichier
if (!defined('ABSPATH')) {
    exit;
}

// Rediriger vers la page d'accueil WordPress
wp_safe_redirect(home_url());
exit;