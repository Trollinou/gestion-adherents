<?php
/**
 * Classe de gestion de l'interface d'administration
 * 
 * @package GestionAdherents
 * @version 1.0.0
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe de gestion de l'interface d'administration
 */
class GA_Admin {
    
    /**
     * Afficher la liste des adhérents
     */
    public function display_adherents_list() {
        // Pas de vérification de permissions ici, déjà fait dans le contrôleur principal
        
        $database = new GA_Database();
        
        // Paramètres de pagination et filtres
        $per_page = get_option('ga_settings')['items_per_page'] ?? 20;
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
        
        $args = array(
            'per_page' => $per_page,
            'page' => $current_page,
            'search' => $search,
            'status' => $status_filter
        );
        
        $result = $database->get_adherents($args);
        
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php _e('Gestion des Adhérents', 'gestion-adherents'); ?></h1>
            <a href="<?php echo admin_url('admin.php?page=gestion-adherents-add'); ?>" class="page-title-action">
                <?php _e('Ajouter un adhérent', 'gestion-adherents'); ?>
            </a>
            
            <?php
            // Afficher les messages de succès
            if (isset($_GET['message'])) {
                $message_type = sanitize_text_field($_GET['message']);
                switch ($message_type) {
                    case 'saved':
                        echo '<div class="notice notice-success is-dismissible">';
                        echo '<p><strong>' . __('Adhérent sauvegardé avec succès !', 'gestion-adherents') . '</strong></p>';
                        echo '</div>';
                        break;
                    case 'deleted':
                        echo '<div class="notice notice-success is-dismissible">';
                        echo '<p><strong>' . __('Adhérent supprimé avec succès !', 'gestion-adherents') . '</strong></p>';
                        echo '</div>';
                        break;
                }
            }
            ?>
            
            <!-- Formulaire de recherche -->
            <form method="get" class="ga-filters">
                <input type="hidden" name="page" value="gestion-adherents">
                <p class="search-box">
                    <input type="search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php _e('Rechercher...', 'gestion-adherents'); ?>">
                    <input type="submit" class="button" value="<?php _e('Filtrer', 'gestion-adherents'); ?>">
                </p>
            </form>
            
            <!-- Tableau des adhérents -->
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Nom', 'gestion-adherents'); ?></th>
                        <th><?php _e('Prénom', 'gestion-adherents'); ?></th>
                        <th><?php _e('N° Licence', 'gestion-adherents'); ?></th>
                        <th><?php _e('Email', 'gestion-adherents'); ?></th>
                        <th><?php _e('Téléphone', 'gestion-adherents'); ?></th>
                        <th><?php _e('Junior', 'gestion-adherents'); ?></th>
                        <th><?php _e('Pôle Excellence', 'gestion-adherents'); ?></th>
                        <th><?php _e('Statut', 'gestion-adherents'); ?></th>
                        <th><?php _e('Actions', 'gestion-adherents'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($result['adherents'])): ?>
                        <?php foreach ($result['adherents'] as $adherent): ?>
                            <tr>
                                <td><strong><?php echo esc_html($adherent['nom']); ?></strong></td>
                                <td><?php echo esc_html($adherent['prenom']); ?></td>
                                <td>
                                    <?php if ($adherent['numero_licence']): ?>
                                        <code class="ga-license-number"><?php echo esc_html($adherent['numero_licence']); ?></code>
                                    <?php else: ?>
                                        <span class="ga-no-license">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="mailto:<?php echo esc_attr($adherent['email']); ?>">
                                        <?php echo esc_html($adherent['email']); ?>
                                    </a>
                                    <?php if ($adherent['wp_user_name']): ?>
                                        <br><small><?php printf(__('Compte WP: %s', 'gestion-adherents'), esc_html($adherent['wp_user_name'])); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html($adherent['telephone'] ?? '-'); ?></td>
                                <td>
                                    <span class="ga-badge <?php echo $adherent['is_junior'] ? 'ga-badge-yes' : 'ga-badge-no'; ?>">
                                        <?php echo $adherent['is_junior'] ? __('Oui', 'gestion-adherents') : __('Non', 'gestion-adherents'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="ga-badge <?php echo $adherent['is_pole_excellence'] ? 'ga-badge-yes' : 'ga-badge-no'; ?>">
                                        <?php echo $adherent['is_pole_excellence'] ? __('Oui', 'gestion-adherents') : __('Non', 'gestion-adherents'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="ga-status ga-status-<?php echo esc_attr($adherent['status']); ?>">
                                        <?php 
                                        switch ($adherent['status']) {
                                            case 'active': _e('Actif', 'gestion-adherents'); break;
                                            case 'inactive': _e('Inactif', 'gestion-adherents'); break;
                                            case 'suspended': _e('Suspendu', 'gestion-adherents'); break;
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo admin_url('admin.php?page=gestion-adherents-add&id=' . $adherent['id']); ?>" class="button button-small">
                                        <?php _e('Modifier', 'gestion-adherents'); ?>
                                    </a>
                                    <button type="button" class="button button-small button-link-delete ga-delete-adherent" data-id="<?php echo $adherent['id']; ?>">
                                        <?php _e('Supprimer', 'gestion-adherents'); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 20px;">
                                <?php _e('Aucun adhérent trouvé.', 'gestion-adherents'); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if ($result['pages'] > 1): ?>
                <div class="tablenav bottom">
                    <div class="tablenav-pages">
                        <?php
                        $pagination_args = array(
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'prev_text' => __('&laquo;', 'gestion-adherents'),
                            'next_text' => __('&raquo;', 'gestion-adherents'),
                            'total' => $result['pages'],
                            'current' => $current_page
                        );
                        echo paginate_links($pagination_args);
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Afficher le formulaire d'ajout/modification d'adhérent
     */
    public function display_add_adherent_form() {
        $adherent_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $adherent = null;
        
        if ($adherent_id) {
            $database = new GA_Database();
            $adherent = $database->get_adherent($adherent_id);
            
            if (!$adherent) {
                wp_die(__('Adhérent non trouvé.', 'gestion-adherents'));
            }
        }
        
        // Traitement du formulaire
        if ($_POST && wp_verify_nonce($_POST['ga_nonce'], 'ga_adherent_form')) {
            $sanitizer = new GA_Sanitizer();
            $validator = new GA_Validator();
            $database = new GA_Database();
            
            $data = $sanitizer->sanitize_adherent_data($_POST);
            $validation_result = $validator->validate_adherent_data($data);
            
            if ($validation_result['valid']) {
                $result = $database->save_adherent($data);
                
                if ($result) {
                    // Redirection vers la liste avec message de succès
                    $redirect_url = add_query_arg(
                        array(
                            'page' => 'gestion-adherents',
                            'message' => 'saved',
                            'adherent_id' => $result
                        ),
                        admin_url('admin.php')
                    );
                    wp_redirect($redirect_url);
                    exit;
                } else {
                    $error_message = __('Erreur lors de la sauvegarde.', 'gestion-adherents');
                }
            } else {
                $errors = $validation_result['errors'];
            }
        }
        
        ?>
        <div class="wrap">
            <h1><?php echo $adherent_id ? __('Modifier l\'adhérent', 'gestion-adherents') : __('Ajouter un adhérent', 'gestion-adherents'); ?></h1>
            
            <?php if (isset($error_message)): ?>
                <div class="notice notice-error"><p><?php echo esc_html($error_message); ?></p></div>
            <?php endif; ?>
            
            <form method="post" class="ga-adherent-form">
                <?php wp_nonce_field('ga_adherent_form', 'ga_nonce'); ?>
                
                <?php if ($adherent_id): ?>
                    <input type="hidden" name="id" value="<?php echo $adherent_id; ?>">
                <?php endif; ?>
                
                <table class="form-table">
                    <tbody>
                        <!-- Informations personnelles -->
                        <tr>
                            <th colspan="2"><h3><?php _e('Informations personnelles', 'gestion-adherents'); ?></h3></th>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="nom"><?php _e('Nom', 'gestion-adherents'); ?> <span class="required">*</span></label>
                            </th>
                            <td>
                                <input type="text" id="nom" name="nom" value="<?php echo esc_attr($adherent['nom'] ?? ''); ?>" class="regular-text" required>
                                <?php if (isset($errors['nom'])): ?>
                                    <p class="error"><?php echo esc_html($errors['nom']); ?></p>
                                <?php endif; ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="prenom"><?php _e('Prénom', 'gestion-adherents'); ?> <span class="required">*</span></label>
                            </th>
                            <td>
                                <input type="text" id="prenom" name="prenom" value="<?php echo esc_attr($adherent['prenom'] ?? ''); ?>" class="regular-text" required>
                                <?php if (isset($errors['prenom'])): ?>
                                    <p class="error"><?php echo esc_html($errors['prenom']); ?></p>
                                <?php endif; ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="date_naissance"><?php _e('Date de naissance', 'gestion-adherents'); ?></label>
                            </th>
                            <td>
                                <input type="date" id="date_naissance" name="date_naissance" value="<?php echo esc_attr($adherent['date_naissance'] ?? ''); ?>">
                                <?php if (isset($errors['date_naissance'])): ?>
                                    <p class="error"><?php echo esc_html($errors['date_naissance']); ?></p>
                                <?php endif; ?>
                            </td>
                        </tr>
                        
                        <!-- Contact -->
                        <tr>
                            <th colspan="2"><h3><?php _e('Contact', 'gestion-adherents'); ?></h3></th>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="email"><?php _e('Email', 'gestion-adherents'); ?> <span class="required">*</span></label>
                            </th>
                            <td>
                                <input type="email" id="email" name="email" value="<?php echo esc_attr($adherent['email'] ?? ''); ?>" class="regular-text" required>
                                <?php if (isset($errors['email'])): ?>
                                    <p class="error"><?php echo esc_html($errors['email']); ?></p>
                                <?php endif; ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="numero_licence"><?php _e('Numéro de licence', 'gestion-adherents'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="numero_licence" name="numero_licence" value="<?php echo esc_attr($adherent['numero_licence'] ?? ''); ?>" class="regular-text" placeholder="A12345" maxlength="6" style="text-transform: uppercase;">
                                <?php if (isset($errors['numero_licence'])): ?>
                                    <p class="error"><?php echo esc_html($errors['numero_licence']); ?></p>
                                <?php endif; ?>
                                <p class="description"><?php _e('Format : 1 lettre suivie de 5 chiffres (ex: A12345)', 'gestion-adherents'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="telephone"><?php _e('Téléphone', 'gestion-adherents'); ?></label>
                            </th>
                            <td>
                                <input type="tel" id="telephone" name="telephone" value="<?php echo esc_attr($adherent['telephone'] ?? ''); ?>" class="regular-text">
                                <?php if (isset($errors['telephone'])): ?>
                                    <p class="error"><?php echo esc_html($errors['telephone']); ?></p>
                                <?php endif; ?>
                            </td>
                        </tr>
                        
                        <!-- Adresse -->
                        <tr>
                            <th colspan="2"><h3><?php _e('Adresse', 'gestion-adherents'); ?></h3></th>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="adresse_1"><?php _e('Adresse 1', 'gestion-adherents'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="adresse_1" name="adresse_1" value="<?php echo esc_attr($adherent['adresse_1'] ?? ''); ?>" class="regular-text">
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="adresse_2"><?php _e('Adresse 2', 'gestion-adherents'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="adresse_2" name="adresse_2" value="<?php echo esc_attr($adherent['adresse_2'] ?? ''); ?>" class="regular-text">
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="adresse_3"><?php _e('Adresse 3', 'gestion-adherents'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="adresse_3" name="adresse_3" value="<?php echo esc_attr($adherent['adresse_3'] ?? ''); ?>" class="regular-text">
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="code_postal"><?php _e('Code postal', 'gestion-adherents'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="code_postal" name="code_postal" value="<?php echo esc_attr($adherent['code_postal'] ?? ''); ?>" class="small-text">
                                <?php if (isset($errors['code_postal'])): ?>
                                    <p class="error"><?php echo esc_html($errors['code_postal']); ?></p>
                                <?php endif; ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="ville"><?php _e('Ville', 'gestion-adherents'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="ville" name="ville" value="<?php echo esc_attr($adherent['ville'] ?? ''); ?>" class="regular-text">
                            </td>
                        </tr>
                        
                        <!-- Adhésion -->
                        <tr>
                            <th colspan="2"><h3><?php _e('Adhésion', 'gestion-adherents'); ?></h3></th>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="date_adhesion"><?php _e('Date d\'adhésion', 'gestion-adherents'); ?> <span class="required">*</span></label>
                            </th>
                            <td>
                                <input type="date" id="date_adhesion" name="date_adhesion" value="<?php echo esc_attr($adherent['date_adhesion'] ?? date('Y-m-d')); ?>" required>
                                <?php if (isset($errors['date_adhesion'])): ?>
                                    <p class="error"><?php echo esc_html($errors['date_adhesion']); ?></p>
                                <?php endif; ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="date_fin_adhesion"><?php _e('Date de fin d\'adhésion', 'gestion-adherents'); ?></label>
                            </th>
                            <td>
                                <input type="date" id="date_fin_adhesion" name="date_fin_adhesion" value="<?php echo esc_attr($adherent['date_fin_adhesion'] ?? ''); ?>">
                                <?php if (isset($errors['date_fin_adhesion'])): ?>
                                    <p class="error"><?php echo esc_html($errors['date_fin_adhesion']); ?></p>
                                <?php endif; ?>
                                <p class="description"><?php _e('Laisser vide si pas de date de fin', 'gestion-adherents'); ?></p>
                            </td>
                        </tr>
                        
                        <!-- Classifications -->
                        <tr>
                            <th colspan="2"><h3><?php _e('Classifications', 'gestion-adherents'); ?></h3></th>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Junior', 'gestion-adherents'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="is_junior" value="1" <?php checked($adherent['is_junior'] ?? false, 1); ?>>
                                    <?php _e('Cet adhérent est classé comme Junior', 'gestion-adherents'); ?>
                                </label>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Pôle Excellence', 'gestion-adherents'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="is_pole_excellence" value="1" <?php checked($adherent['is_pole_excellence'] ?? false, 1); ?>>
                                    <?php _e('Cet adhérent fait partie du Pôle Excellence', 'gestion-adherents'); ?>
                                </label>
                            </td>
                        </tr>
                        
                        <!-- Compte WordPress -->
                        <tr>
                            <th colspan="2"><h3><?php _e('Compte WordPress', 'gestion-adherents'); ?></h3></th>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="wp_user_id"><?php _e('Utilisateur WordPress', 'gestion-adherents'); ?></label>
                            </th>
                            <td>
                                <select id="wp_user_id" name="wp_user_id" class="regular-text">
                                    <option value=""><?php _e('Aucun compte lié', 'gestion-adherents'); ?></option>
                                    <?php
                                    $users = get_users(array('orderby' => 'display_name'));
                                    $current_user_id = $adherent['wp_user_id'] ?? '';
                                    
                                    foreach ($users as $user) {
                                        $selected = selected($current_user_id, $user->ID, false);
                                        echo "<option value='{$user->ID}' {$selected}>{$user->display_name} ({$user->user_email})</option>";
                                    }
                                    ?>
                                </select>
                                <?php if (isset($errors['wp_user_id'])): ?>
                                    <p class="error"><?php echo esc_html($errors['wp_user_id']); ?></p>
                                <?php endif; ?>
                                <p class="description"><?php _e('Sélectionnez "Aucun compte lié" pour délier l\'adhérent d\'un compte WordPress.', 'gestion-adherents'); ?></p>
                            </td>
                        </tr>
                        
                        <!-- Statut -->
                        <tr>
                            <th scope="row">
                                <label for="status"><?php _e('Statut', 'gestion-adherents'); ?></label>
                            </th>
                            <td>
                                <select id="status" name="status">
                                    <option value="active" <?php selected($adherent['status'] ?? 'active', 'active'); ?>><?php _e('Actif', 'gestion-adherents'); ?></option>
                                    <option value="inactive" <?php selected($adherent['status'] ?? '', 'inactive'); ?>><?php _e('Inactif', 'gestion-adherents'); ?></option>
                                    <option value="suspended" <?php selected($adherent['status'] ?? '', 'suspended'); ?>><?php _e('Suspendu', 'gestion-adherents'); ?></option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <p class="submit">
                    <input type="submit" name="submit" class="button-primary" value="<?php echo $adherent_id ? __('Mettre à jour', 'gestion-adherents') : __('Ajouter l\'adhérent', 'gestion-adherents'); ?>">
                    <a href="<?php echo admin_url('admin.php?page=gestion-adherents'); ?>" class="button"><?php _e('Annuler', 'gestion-adherents'); ?></a>
                </p>
            </form>
            
            <style>
            .ga-adherent-form .form-table th {
                width: 200px;
            }
            .ga-adherent-form .required {
                color: #d63638;
            }
            .ga-adherent-form .error {
                color: #d63638;
                font-size: 12px;
                margin-top: 5px;
            }
            .ga-adherent-form h3 {
                margin: 0;
                padding: 10px 0 5px 0;
                border-bottom: 1px solid #ddd;
            }
            </style>
        </div>
        <?php
    }
    
    /**
     * Afficher la page des paramètres
     */
    public function display_settings_page() {
        if ($_POST && wp_verify_nonce($_POST['ga_settings_nonce'], 'ga_settings_form')) {
            $settings = array(
                'items_per_page' => intval($_POST['items_per_page']),
                'date_format' => sanitize_text_field($_POST['date_format']),
                'notification_email' => sanitize_email($_POST['notification_email']),
                'auto_expire_notification' => isset($_POST['auto_expire_notification']),
                'export_format' => in_array($_POST['export_format'], array('csv', 'xlsx')) ? $_POST['export_format'] : 'csv'
            );
            
            update_option('ga_settings', $settings);
            echo '<div class="notice notice-success"><p>' . __('Paramètres sauvegardés.', 'gestion-adherents') . '</p></div>';
        }
        
        $settings = get_option('ga_settings', array());
        
        ?>
        <div class="wrap">
            <h1><?php _e('Paramètres - Gestion Adhérents', 'gestion-adherents'); ?></h1>
            
            <form method="post">
                <?php wp_nonce_field('ga_settings_form', 'ga_settings_nonce'); ?>
                
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="items_per_page"><?php _e('Éléments par page', 'gestion-adherents'); ?></label>
                            </th>
                            <td>
                                <input type="number" id="items_per_page" name="items_per_page" value="<?php echo esc_attr($settings['items_per_page'] ?? 20); ?>" min="1" max="100" class="small-text">
                                <p class="description"><?php _e('Nombre d\'adhérents à afficher par page dans la liste.', 'gestion-adherents'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="notification_email"><?php _e('Email de notification', 'gestion-adherents'); ?></label>
                            </th>
                            <td>
                                <input type="email" id="notification_email" name="notification_email" value="<?php echo esc_attr($settings['notification_email'] ?? get_option('admin_email')); ?>" class="regular-text">
                                <p class="description"><?php _e('Adresse email pour recevoir les notifications du plugin.', 'gestion-adherents'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Notifications automatiques', 'gestion-adherents'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="auto_expire_notification" value="1" <?php checked($settings['auto_expire_notification'] ?? true, true); ?>>
                                    <?php _e('Envoyer une notification pour les adhésions qui expirent bientôt', 'gestion-adherents'); ?>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <p class="submit">
                    <input type="submit" name="submit" class="button-primary" value="<?php _e('Enregistrer les paramètres', 'gestion-adherents'); ?>">
                </p>
            </form>
        </div>
        <?php
    }
    
    /**
     * Afficher la page d'export
     */
    public function display_export_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Export des Adhérents', 'gestion-adherents'); ?></h1>
            <div class="notice notice-info">
                <p><strong><?php _e('Fonctionnalité en développement', 'gestion-adherents'); ?></strong></p>
                <p><?php _e('L\'export CSV/Excel sera disponible dans une prochaine version du plugin.', 'gestion-adherents'); ?></p>
                <p><?php _e('En attendant, vous pouvez consulter et gérer vos adhérents depuis la liste principale.', 'gestion-adherents'); ?></p>
            </div>
        </div>
        <?php
    }
}