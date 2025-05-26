# 📋 Changelog - Gestion d'Adhérents

## 🔧 **Version 1.1.1 (2025-05-25)**

### **🐛 Corrections de Bugs**
- ✅ **Déliaison compte WordPress** : Correction du problème où sélectionner "Aucun compte lié" n'était pas pris en compte
- ✅ **Page blanche après sauvegarde** : Redirection automatique vers la liste des adhérents avec message de confirmation
- ✅ **UX améliorée** : Messages de succès/erreur cohérents dans toute l'interface

### **🎯 Améliorations UX**
- ✅ **Messages informatifs** : Notifications claires lors des actions (sauvegarde, suppression)
- ✅ **Workflow optimisé** : Retour automatique à la liste pour modifier d'autres adhérents facilement
- ✅ **Description d'aide** : Explication claire sur la déliaison des comptes WordPress

---

## 🆕 **Version 1.1.0 (2025-05-25)**

### **Nouvelles Fonctionnalités**
- ✅ **Nouveau champ** : Numéro de licence (format : 1 lettre + 5 chiffres)
- ✅ **Validation automatique** : Format A12345, B67890, etc.
- ✅ **Unicité garantie** : Pas de doublons possibles
- ✅ **Affichage dans la liste** : Colonne dédiée avec style visuel
- ✅ **Recherche étendue** : Recherche par numéro de licence
- ✅ **Formatage automatique** : Conversion en majuscules automatique

### **Améliorations Techniques**
- ✅ **Migration automatique** : Mise à jour transparente depuis v1.0.0
- ✅ **Interface responsive** : Compatible mobile/tablette
- ✅ **Validation JavaScript** : Contrôle temps réel du format

---

## 🎉 **Version 1.0.0 (2025-05-25)**

### **Fonctionnalités Initiales**
- ✅ **Gestion complète des adhérents** avec tous les champs requis
- ✅ **Interface d'administration** WordPress native
- ✅ **Sécurité niveau entreprise** (validation, sanitisation, CSRF)
- ✅ **Classifications** Junior et Pôle Excellence
- ✅ **Liaison comptes WordPress** par les administrateurs
- ✅ **Traduction française** complète (200+ chaînes)
- ✅ **Export des données** (fonctionnalité de base)
- ✅ **Architecture modulaire** et extensible

---

## 🔄 **Guide de Mise à Jour**

### **De 1.1.0 vers 1.1.1**
1. **Remplacer** les fichiers du plugin
2. **Aucune migration** de base de données nécessaire
3. **Tester** la déliaison des comptes WordPress
4. **Vérifier** les redirections après sauvegarde

### **De 1.0.0 vers 1.1.1**
1. **Sauvegarder** la base de données (recommandé)
2. **Remplacer** les fichiers du plugin
3. **La migration** vers 1.1.0 se fait automatiquement (ajout colonne licence)
4. **Les corrections** 1.1.1 sont appliquées immédiatement

### **Installation Propre**
- **Télécharger** le package complet v1.1.1
- **Installer** via l'interface WordPress
- **Toutes les fonctionnalités** disponibles immédiatement

---

## 🎯 **Fonctionnalités par Version**

| Fonctionnalité | v1.0.0 | v1.1.0 | v1.1.1 |
|---|---|---|---|
| Gestion adhérents de base | ✅ | ✅ | ✅ |
| Classifications Junior/Pôle | ✅ | ✅ | ✅ |
| Liaison comptes WordPress | ✅ | ✅ | ✅ |
| Numéro de licence | ❌ | ✅ | ✅ |
| Déliaison comptes WP | ⚠️ | ⚠️ | ✅ |
| Redirection après sauvegarde | ❌ | ❌ | ✅ |
| Messages UX cohérents | ⚠️ | ⚠️ | ✅ |

**Légende :** ✅ Fonctionne parfaitement | ⚠️ Problème connu | ❌ Non disponible

---

## 📞 **Support et Bug Reports**

### **Problèmes Résolus dans v1.1.1**
- ✅ Déliaison compte WordPress ne fonctionnait pas
- ✅ Page blanche après modification d'adhérent
- ✅ Messages d'erreur/succès incohérents

### **Signaler un Nouveau Bug**
Si vous rencontrez un problème :
1. **Vérifiez** que vous utilisez la version 1.1.1
2. **Consultez** les logs WordPress (`wp-content/debug.log`)
3. **Décrivez** les étapes pour reproduire le problème
4. **Incluez** votre version WordPress et PHP

**Version 1.1.1 développée par Etienne Gagnon** - Corrections UX importantes ! 🚀

## 🆕 **Nouvelles Fonctionnalités**

### **Numéro de Licence**
- ✅ **Nouveau champ** : Numéro de licence (format : 1 lettre + 5 chiffres)
- ✅ **Validation automatique** : Format A12345, B67890, etc.
- ✅ **Unicité garantie** : Pas de doublons possibles
- ✅ **Affichage dans la liste** : Colonne dédiée avec style visuel
- ✅ **Recherche étendue** : Recherche par numéro de licence
- ✅ **Formatage automatique** : Conversion en majuscules automatique

## 🔧 **Améliorations Techniques**

### **Base de Données**
- ✅ **Nouvelle colonne** : `numero_licence VARCHAR(6)`
- ✅ **Index unique** : Garantit l'unicité des numéros
- ✅ **Migration automatique** : Mise à jour transparente depuis v1.0.0
- ✅ **Compatibilité descendante** : Les anciens adhérents restent valides

### **Interface Utilisateur**
- ✅ **Nouveau champ de formulaire** avec validation temps réel
- ✅ **Placeholder indicatif** : "A12345"
- ✅ **Description d'aide** : Format clairement expliqué
- ✅ **Style visuel** : Numéro affiché avec style `code`
- ✅ **Responsive design** : Compatible mobile/tablette

### **Validation et Sécurité**
- ✅ **Regex stricte** : `/^[A-Z][0-9]{5}$/`
- ✅ **Sanitisation** : Nettoyage automatique des caractères non autorisés
- ✅ **Vérification d'unicité** : Contrôle en base de données
- ✅ **Messages d'erreur** : Feedback utilisateur informatif

## 📊 **Cas d'Usage**

### **Pour les Associations Sportives**
- **Licences FFS** : A12345, B67890
- **Licences Régionales** : R12345, R67890
- **Licences Jeunes** : J12345, J67890

### **Pour les Organisations Professionnelles**
- **Numéros d'adhérents** : M12345, M67890
- **Codes membres** : C12345, C67890
- **Identifiants uniques** : I12345, I67890

## 🎯 **Comment Utiliser**

### **Ajouter un Numéro de Licence**
1. **Aller dans** `Adhérents > Ajouter` ou modifier un adhérent existant
2. **Remplir le champ** "Numéro de licence" (optionnel)
3. **Format requis** : 1 lettre (A-Z) + 5 chiffres (0-9)
4. **Exemples valides** : A12345, B67890, Z99999
5. **Le plugin vérifie** automatiquement l'unicité

### **Rechercher par Numéro**
1. **Dans la liste des adhérents**, utiliser la barre de recherche
2. **Taper le numéro** : A12345
3. **La recherche fonctionne** sur nom, prénom, email ET numéro de licence

### **Voir les Numéros**
- **Dans la liste** : Nouvelle colonne "N° Licence"
- **Style visuel** : Fond bleu clair, police monospace
- **Vide** : Affiché par un tiret "-"

## ⚡ **Migration Automatique**

### **Depuis la Version 1.0.0**
- ✅ **Détection automatique** de la version installée
- ✅ **Ajout de la colonne** `numero_licence` si manquante
- ✅ **Création de l'index unique** automatique
- ✅ **Notification** de mise à jour dans l'admin
- ✅ **Zéro perte de données** existantes

### **Process de Migration**
1. **Le plugin détecte** la version 1.0.0
2. **Exécute** `ALTER TABLE` pour ajouter la colonne
3. **Crée l'index unique** sur `numero_licence`
4. **Affiche une notification** de succès
5. **Met à jour** la version en base

## 🔍 **Validation des Numéros**

### **Format Autorisé**
```regex
^[A-Z][0-9]{5}$
```

### **Exemples Valides**
- ✅ A12345
- ✅ B67890
- ✅ Z99999
- ✅ M00001

### **Exemples Invalides**
- ❌ 123456 (pas de lettre)
- ❌ AB1234 (2 lettres)
- ❌ A1234 (4 chiffres seulement)
- ❌ a12345 (minuscule - converti automatiquement)

## 🎨 **Interface Visuelle**

### **Dans la Liste**
```html
<code class="ga-license-number">A12345</code>
```

### **Style CSS**
```css
.ga-license-number {
    background-color: #f0f6fc;
    color: #0073aa;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
    font-weight: bold;
    font-size: 12px;
    border: 1px solid #c3e6cb;
}
```

## 🚀 **Installation de la Mise à Jour**

### **Étapes**
1. **Sauvegarder** votre base de données (recommandé)
2. **Remplacer** les fichiers du plugin par la v1.1.0
3. **Aller dans l'admin** WordPress
4. **La migration se fait automatiquement** au premier chargement
5. **Vérifier** que le message de mise à jour apparaît

### **Vérifications Post-Migration**
- ✅ **Nouveau champ** visible dans le formulaire d'ajout
- ✅ **Nouvelle colonne** dans la liste des adhérents
- ✅ **Recherche fonctionne** avec les numéros de licence
- ✅ **Validation** empêche les doublons

## 🔄 **Compatibilité**

### **Versions WordPress**
- ✅ **5.0+** : Support complet
- ✅ **6.0+** : Optimisé
- ✅ **6.4** : Testé et validé

### **Versions PHP**
- ✅ **7.4+** : Minimum requis
- ✅ **8.0+** : Recommandé
- ✅ **8.2** : Entièrement compatible

### **Navigateurs**
- ✅ **Chrome** 90+
- ✅ **Firefox** 85+
- ✅ **Safari** 14+
- ✅ **Edge** 90+

---

## 📞 **Support**

Si vous rencontrez des problèmes avec la migration ou l'utilisation des numéros de licence :

1. **Vérifiez** que la colonne `numero_licence` existe dans votre table `wp_adherents`
2. **Consultez** les logs WordPress (`wp-content/debug.log`)
3. **Contactez** le support avec les détails de l'erreur

**Version 1.1.0 développée par Etienne Gagnon** 🚀