# Plugin WordPress - Gestion d'Adhérents

Plugin professionnel pour la gestion d'une base d'adhérents avec liaison aux comptes WordPress. Développé selon les meilleures pratiques de sécurité, maintenabilité et évolutivité.

## 🚀 Fonctionnalités

### Gestion Complète des Adhérents
- **Informations personnelles** : Nom, prénom, date de naissance, email
- **Coordonnées** : Adresse complète (3 lignes), code postal, ville, téléphone
- **Adhésion** : Date d'adhésion, date de fin d'adhésion
- **Classifications** : Junior et Pôle Excellence (booléens)
- **Liaison WordPress** : Possibilité de lier un adhérent à un compte WordPress existant
- **Statuts** : Actif, Inactif, Suspendu

### Interface d'Administration
- **Liste des adhérents** avec pagination, recherche et filtres avancés
- **Formulaire d'ajout/modification** avec validation en temps réel
- **Actions groupées** pour les opérations en masse
- **Export** en CSV et Excel avec sélection des champs
- **Statistiques** en temps réel
- **Interface responsive** et accessible

### Sécurité et Performance
- **Validation côté serveur** et côté client
- **Sanitisation** de toutes les données
- **Nonces WordPress** pour la sécurité CSRF
- **Capacités utilisateur** personnalisées
- **Requêtes optimisées** avec indexation de base de données
- **Échappement** de toutes les sorties

### Fonctionnalités Avancées
- **Auto-sauvegarde** des brouillons
- **Validation des emails** avec vérification d'unicité
- **Auto-complétion** de ville basée sur le code postal (API française)
- **Formatage automatique** des numéros de téléphone
- **Notifications** pour les adhésions qui expirent
- **Traduction complète** en français

## 📋 Prérequis

- **WordPress** : Version 5.0 ou supérieure
- **PHP** : Version 7.4 ou supérieure
- **MySQL** : Version 5.6 ou supérieure
- **Permissions** : Capacité `manage_options` pour l'administration

## 🔧 Installation

### Installation Manuelle

1. **Télécharger le plugin**
   ```bash
   # Créer la structure de dossiers
   mkdir -p gestion-adherents/assets/css
   mkdir -p gestion-adherents/assets/js
   mkdir -p gestion-adherents/includes
   mkdir -p gestion-adherents/languages
   ```

2. **Copier les fichiers**
   - `gestion-adherents.php` : Fichier principal du plugin
   - `assets/css/admin.css` : Styles de l'interface d'administration
   - `assets/js/admin.js` : Scripts JavaScript
   - `languages/gestion-adherents-fr_FR.po` : Traductions françaises
   - `README.md` : Cette documentation

3. **Téléverser dans WordPress**
   - Copier le dossier `gestion-adherents` dans `/wp-content/plugins/`
   - Ou créer une archive ZIP et l'installer via l'interface WordPress

4. **Activer le plugin**
   - Aller dans `Extensions > Extensions installées`
   - Activer "Gestion d'Adhérents"

### Activation et Configuration

1. **Après activation**, le plugin :
   - Créera automatiquement la table `wp_adherents`
   - Ajoutera les capacités aux rôles Administrateur et Éditeur
   - Configurera les paramètres par défaut

2. **Menu d'administration** disponible dans :
   - `Adhérents` > `Tous les adhérents`
   - `Adhérents` > `Ajouter`
   - `Adhérents` > `Paramètres`
   - `Adhérents` > `Export`

## 🎯 Utilisation

### Ajouter un Adhérent

1. Aller dans `Adhérents > Ajouter`
2. Remplir les informations obligatoires :
   - Nom (minimum 2 caractères)
   - Prénom (minimum 2 caractères)
   - Email (unique dans la base)
   - Date d'adhésion
3. Compléter les informations optionnelles
4. Sélectionner les classifications (Junior/Pôle Excellence)
5. Lier à un compte WordPress si nécessaire
6. Cliquer sur "Ajouter l'adhérent"

### Gérer les Adhérents

#### Recherche et Filtres
- **Recherche globale** : Par nom, prénom ou email
- **Filtres par statut** : Actif, Inactif, Suspendu
- **Filtres par classification** : Junior, Pôle Excellence
- **Pagination** : Configurable dans les paramètres

#### Actions Individuelles
- **Modifier** : Cliquer sur "Modifier" dans la colonne Actions
- **Supprimer** : Cliquer sur "Supprimer" avec confirmation
- **Voir le profil** : Cliquer sur le nom de l'adhérent

#### Actions Groupées
- Sélectionner plusieurs adhérents avec les cases à cocher
- Choisir une action dans le menu déroulant
- Appliquer l'action sélectionnée

### Export des Données

1. Aller dans `Adhérents > Export`
2. Choisir le format : CSV ou Excel
3. Sélectionner les filtres :
   - Statuts à inclure
   - Classifications à exporter
4. Choisir les champs à exporter
5. Cliquer sur "Télécharger l'export"

### Configuration

#### Paramètres Généraux
- **Éléments par page** : Nombre d'adhérents par page (1-100)
- **Format de date** : DD/MM/YYYY, YYYY-MM-DD, MM/DD/YYYY
- **Email de notification** : Pour les alertes système
- **Notifications automatiques** : Adhésions qui expirent
- **Format d'export** : CSV ou Excel par défaut

## 🔒 Sécurité

### Capacités Utilisateur
Le plugin définit trois capacités personnalisées :
- `manage_adherents` : Voir et gérer les adhérents
- `edit_adherents` : Créer et modifier les adhérents
- `delete_adherents` : Supprimer les adhérents

### Validation des Données
- **Côté serveur** : Validation PHP stricte
- **Côté client** : Validation JavaScript en temps réel
- **Sanitisation** : Nettoyage automatique des entrées
- **Échappement** : Protection contre les attaques XSS

### Protection CSRF
- Utilisation des **nonces WordPress**
- Vérification des permissions utilisateur
- Validation des sources de requêtes

## 🗄️ Structure de Base de Données

### Table `wp_adherents`
```sql
CREATE TABLE wp_adherents (
    id int(11) NOT NULL AUTO_INCREMENT,
    nom varchar(100) NOT NULL,
    prenom varchar(100) NOT NULL,
    date_naissance date NULL,
    email varchar(255) NOT NULL,
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
    KEY wp_user_id (wp_user_id),
    KEY date_adhesion (date_adhesion),
    KEY status (status)
);
```

### Index et Contraintes
- **Index unique** sur l'email
- **Clés étrangères** vers `wp_users` pour la liaison des comptes
- **Index composites** pour les requêtes de recherche optimisées

## 🔧 API et Hooks

### Hooks d'Actions
```php
// Après création d'un adhérent
do_action('ga_adherent_created', $adherent_id, $adherent_data);

// Après modification d'un adhérent
do_action('ga_adherent_updated', $adherent_id, $old_data, $new_data);

// Après suppression d'un adhérent
do_action('ga_adherent_deleted', $adherent_id, $adherent_data);
```

### Hooks de Filtres
```php
// Modifier les données avant sauvegarde
$data = apply_filters('ga_before_save_adherent', $data, $adherent_id);

// Modifier les critères de recherche
$search_args = apply_filters('ga_search_args', $args);

// Personnaliser les champs d'export
$export_fields = apply_filters('ga_export_fields', $fields);
```

### Fonctions Utilitaires
```php
// Récupérer un adhérent
$adherent = GA()->get_adherent($id);

// Rechercher des adhérents
$results = GA()->search_adherents($criteria);

// Vérifier les permissions
if (current_user_can('manage_adherents')) {
    // Code autorisé
}
```

## 🎨 Personnalisation

### CSS Personnalisé
Ajouter des styles dans le fichier `assets/css/admin.css` ou utiliser le hook :
```php
add_action('admin_enqueue_scripts', function($hook) {
    if (strpos($hook, 'gestion-adherents') !== false) {
        wp_enqueue_style('custom-ga-style', 'chemin/vers/style.css');
    }
});
```

### Templates Personnalisés
Créer des templates dans le thème :
```
themes/mon-theme/
├── gestion-adherents/
│   ├── adherent-list.php
│   ├── adherent-form.php
│   └── adherent-profile.php
```

### Champs Supplémentaires
Ajouter des champs via les hooks :
```php
add_filter('ga_adherent_fields', function($fields) {
    $fields['profession'] = array(
        'type' => 'text',
        'label' => 'Profession',
        'required' => false
    );
    return $fields;
});
```

## 🔍 Dépannage

### Problèmes Courants

#### Le plugin ne s'active pas
- Vérifier la version de PHP (minimum 7.4)
- Vérifier les permissions de fichiers
- Consulter les logs d'erreur WordPress

#### La table n'est pas créée
- Vérifier les permissions de base de données
- Désactiver/réactiver le plugin
- Vérifier les logs MySQL

#### Les traductions ne s'affichent pas
- Vérifier que le fichier `.po` est dans `languages/`
- Compiler le fichier `.po` en `.mo` si nécessaire
- Vérifier la locale WordPress

#### Les exports ne fonctionnent pas
- Vérifier les permissions d'écriture
- Augmenter `memory_limit` PHP si nécessaire
- Vérifier les extensions PHP (mbstring, iconv)

### Mode Debug
Activer le debug WordPress dans `wp-config.php` :
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

## 📈 Performance

### Optimisations Incluses
- **Requêtes paginées** pour éviter la surcharge mémoire
- **Index de base de données** pour des recherches rapides
- **Lazy loading** des ressources JavaScript
- **Cache des requêtes** via l'Object Cache WordPress
- **Nettoyage automatique** des données obsolètes

### Recommandations
- Utiliser un plugin de cache (WP Rocket, W3 Total Cache)
- Optimiser la base de données régulièrement
- Limiter le nombre d'adhérents par page (20-50)
- Utiliser un CDN pour les ressources statiques

## 🔄 Mises à Jour

### Processus de Mise à Jour
1. **Sauvegarde** de la base de données
2. **Désactivation** temporaire du plugin
3. **Remplacement** des fichiers
4. **Réactivation** du plugin
5. **Vérification** du bon fonctionnement

### Migration des Données
Le plugin gère automatiquement :
- Les mises à jour de structure de base de données
- La migration des paramètres
- La conservation des données existantes

## 🤝 Contribution

### Rapporter un Bug
1. Vérifier que le bug n'est pas déjà signalé
2. Fournir des informations détaillées :
   - Version WordPress et PHP
   - Messages d'erreur
   - Étapes pour reproduire
3. Utiliser les modèles de rapport fournis

### Proposer une Fonctionnalité
1. Décrire le cas d'usage
2. Expliquer l'implémentation envisagée
3. Fournir des maquettes si nécessaire

### Contribuer au Code
1. Fork du repository
2. Création d'une branche feature
3. Développement avec tests
4. Pull request avec description détaillée

## 📄 Licence

Ce plugin est distribué sous licence GPL v2 ou ultérieure.

## 📞 Support

- **Documentation** : Ce fichier README
- **Forum WordPress** : Section plugins
- **Email** : support@exemple.com
- **Issues GitHub** : Pour les bugs et améliorations

## 🏷️ Changelog

### Version 1.0.0
- Première version stable
- Gestion complète des adhérents
- Interface d'administration
- Export CSV/Excel
- Traduction française
- Documentation complète

---

**Développé avec ❤️ par Etienne Gagnon pour la communauté WordPress française**
