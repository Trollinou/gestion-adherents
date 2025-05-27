/* assets/js/admin.js */

(function($) {
    'use strict';
    
    /**
     * Plugin principal pour la gestion d'adhérents
     */
    var GestionAdherents = {
        
        /**
         * Initialisation
         */
        init: function() {
            this.bindEvents();
            this.initComponents();
        },
        
        /**
         * Liaison des événements
         */
        bindEvents: function() {
            // Suppression d'adhérent
            $(document).on('click', '.ga-delete-adherent', this.confirmDelete);
            
            // Soumission de formulaire via AJAX
            $('.ga-adherent-form').on('submit', this.handleFormSubmit);
            
            // Recherche en temps réel (avec délai)
            var searchTimeout;
            $('.ga-filters input[name="s"]').on('input', function() {
                clearTimeout(searchTimeout);
                var $form = $(this).closest('form');
                searchTimeout = setTimeout(function() {
                    // Auto-submit après 500ms d'inactivité
                    // $form.submit();
                }, 500);
            });
            
            // Validation en temps réel du formulaire
            $('.ga-adherent-form input, .ga-adherent-form select').on('blur', this.validateField);
            
            // Auto-complétion pour les villes avec code postal
            $('#code_postal').on('blur', this.autoCompleteCity);
            
            // Formatage automatique du téléphone
            $('#telephone').on('input', this.formatPhone);
            
            // Formatage automatique du numéro de licence
            $('#numero_licence').on('input', this.formatLicenseNumber);
            
            // Sélection/désélection de tous les champs d'export
            $('.ga-export-form').on('change', 'input[name="select_all_fields"]', this.toggleAllFields);
            
            // Confirmation avant export important
            $('.ga-export-form').on('submit', this.confirmExport);
        },
        
        /**
         * Initialisation des composants
         */
        initComponents: function() {
            // Initialiser les tooltips si nécessaire
            this.initTooltips();
            
            // Initialiser la validation des dates
            this.initDateValidation();
            
            // Initialiser l'auto-sauvegarde des brouillons
            this.initAutosave();
            
            // Charger les statistiques si on est sur la page principale
            if ($('.ga-stats-grid').length) {
                this.loadStats();
            }
        },
        
        /**
         * Confirmation de suppression d'adhérent
         */
        confirmDelete: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var adherentId = $button.data('id');
            var adherentName = $button.closest('tr').find('td:first strong').text();
            
            if (!adherentId) {
                alert(ga_ajax.messages.error_occurred);
                return;
            }
            
            var message = ga_ajax.messages.confirm_delete;
            if (adherentName) {
                message = message.replace('%s', adherentName);
            }
            
            if (confirm(message)) {
                GestionAdherents.deleteAdherent(adherentId, $button);
            }
        },
        
        /**
         * Suppression d'adhérent via AJAX
         */
        deleteAdherent: function(adherentId, $button) {
            var $row = $button.closest('tr');
            
            // Ajouter l'état de chargement
            $row.addClass('ga-loading');
            $button.prop('disabled', true);
            
            $.ajax({
                url: ga_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'ga_delete_adherent',
                    adherent_id: adherentId,
                    nonce: ga_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Animation de suppression
                        $row.fadeOut(400, function() {
                            $(this).remove();
                            GestionAdherents.showNotice('success', response.data.message);
                            
                            // Redirection optionnelle si fournie
                            if (response.data.redirect_url) {
                                setTimeout(function() {
                                    window.location.href = response.data.redirect_url;
                                }, 1500);
                            }
                        });
                    } else {
                        GestionAdherents.showNotice('error', response.data.message);
                        $row.removeClass('ga-loading');
                        $button.prop('disabled', false);
                    }
                },
                error: function() {
                    GestionAdherents.showNotice('error', ga_ajax.messages.error_occurred);
                    $row.removeClass('ga-loading');
                    $button.prop('disabled', false);
                }
            });
        },
        
        /**
         * Gestion de soumission de formulaire
         */
        handleFormSubmit: function(e) {
            var $form = $(this);
            
            // Vérifier si c'est un formulaire AJAX
            if (!$form.hasClass('ga-ajax-form')) {
                return true; // Laisser le formulaire se soumettre normalement
            }
            
            e.preventDefault();
            
            var formData = new FormData(this);
            formData.append('action', 'ga_save_adherent');
            formData.append('nonce', ga_ajax.nonce);
            
            // État de chargement
            $form.addClass('ga-loading');
            $form.find('input[type="submit"]').prop('disabled', true);
            
            $.ajax({
                url: ga_ajax.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        GestionAdherents.showNotice('success', response.data.message);
                        
                        // Rediriger si c'est un nouvel adhérent
                        if (response.data.adherent_id && !$('input[name="id"]').val()) {
                            setTimeout(function() {
                                window.location.href = 'admin.php?page=gestion-adherents&message=saved';
                            }, 1500);
                        }
                    } else {
                        GestionAdherents.showNotice('error', response.data.message);
                        GestionAdherents.displayErrors(response.data.errors);
                    }
                },
                error: function() {
                    GestionAdherents.showNotice('error', ga_ajax.messages.error_occurred);
                },
                complete: function() {
                    $form.removeClass('ga-loading');
                    $form.find('input[type="submit"]').prop('disabled', false);
                }
            });
        },
        
        /**
         * Validation d'un champ en temps réel
         */
        validateField: function() {
            var $field = $(this);
            var fieldName = $field.attr('name');
            var value = $field.val();
            var isValid = true;
            var errorMessage = '';
            
            // Nettoyer les erreurs précédentes
            $field.removeClass('error');
            $field.siblings('.error').remove();
            
            // Validation selon le type de champ
            switch (fieldName) {
                case 'email':
                    if (value && !GestionAdherents.isValidEmail(value)) {
                        isValid = false;
                        errorMessage = 'Adresse email invalide.';
                    }
                    break;
                    
                case 'telephone':
                    if (value && !GestionAdherents.isValidPhone(value)) {
                        isValid = false;
                        errorMessage = 'Numéro de téléphone invalide.';
                    }
                    break;
                
                case 'numero_licence':
                    if (value && !GestionAdherents.isValidLicense(value)) {
                        isValid = false;
                        errorMessage = 'Format invalide : 1 lettre + 5 chiffres (ex: A12345).';
                    }
                    break;
                    
                case 'code_postal':
                    if (value && !GestionAdherents.isValidPostalCode(value)) {
                        isValid = false;
                        errorMessage = 'Code postal invalide.';
                    }
                    break;
                    
                case 'date_naissance':
                case 'date_adhesion':
                case 'date_fin_adhesion':
                    if (value && !GestionAdherents.isValidDate(value)) {
                        isValid = false;
                        errorMessage = 'Date invalide.';
                    }
                    break;
            }
            
            // Afficher l'erreur si nécessaire
            if (!isValid) {
                $field.addClass('error');
                $field.after('<p class="error">' + errorMessage + '</p>');
            }
            
            return isValid;
        },
        
        /**
         * Auto-complétion de ville basée sur le code postal
         */
        autoCompleteCity: function() {
            var $codePostal = $(this);
            var $ville = $('#ville');
            var codePostal = $codePostal.val();
            
            if (codePostal && codePostal.length === 5 && /^\d{5}$/.test(codePostal)) {
                // API française des codes postaux
                $.ajax({
                    url: 'https://api-adresse.data.gouv.fr/search/',
                    data: {
                        q: codePostal,
                        type: 'municipality',
                        limit: 1
                    },
                    success: function(response) {
                        if (response.features && response.features.length > 0) {
                            var city = response.features[0].properties.city;
                            if (!$ville.val() || confirm('Remplacer la ville par "' + city + '" ?')) {
                                $ville.val(city);
                            }
                        }
                    },
                    error: function() {
                        // Échec silencieux de l'auto-complétion
                    }
                });
            }
        },
        
        /**
         * Formatage automatique du numéro de téléphone
         */
        formatPhone: function() {
            var $phone = $(this);
            var value = $phone.val().replace(/\D/g, ''); // Garder seulement les chiffres
            
            if (value.length > 0) {
                // Format français : 01 23 45 67 89
                if (value.length <= 10) {
                    value = value.replace(/(\d{2})(?=\d)/g, '$1 ');
                }
            }
            
            $phone.val(value);
        },
        
        /**
         * Formatage automatique du numéro de licence
         */
        formatLicenseNumber: function() {
            var $license = $(this);
            var value = $license.val().toUpperCase().replace(/[^A-Z0-9]/g, ''); // Majuscules et alphanumerique seulement
            
            // Limiter à 6 caractères
            if (value.length > 6) {
                value = value.substring(0, 6);
            }
            
            $license.val(value);
        },
        
        /**
         * Sélection/désélection de tous les champs d'export
         */
        toggleAllFields: function() {
            var $checkbox = $(this);
            var checked = $checkbox.is(':checked');
            
            $checkbox.closest('form').find('input[name="fields[]"]').prop('checked', checked);
        },
        
        /**
         * Confirmation avant export important
         */
        confirmExport: function(e) {
            var $form = $(this);
            var selectedFields = $form.find('input[name="fields[]"]:checked').length;
            
            if (selectedFields === 0) {
                alert('Veuillez sélectionner au moins un champ à exporter.');
                e.preventDefault();
                return false;
            }
            
            // Si export de données sensibles, demander confirmation
            var sensitiveFields = ['email', 'telephone', 'date_naissance'];
            var hasSensitiveData = false;
            
            $form.find('input[name="fields[]"]:checked').each(function() {
                if (sensitiveFields.indexOf($(this).val()) !== -1) {
                    hasSensitiveData = true;
                    return false;
                }
            });
            
            if (hasSensitiveData) {
                return confirm('Cet export contient des données sensibles. Confirmez-vous l\'export ?');
            }
            
            return true;
        },
        
        /**
         * Initialisation des tooltips
         */
        initTooltips: function() {
            $('[data-tooltip]').each(function() {
                var $element = $(this);
                var tooltipText = $element.data('tooltip');
                
                $element.on('mouseenter', function() {
                    var $tooltip = $('<div class="ga-tooltip">' + tooltipText + '</div>');
                    $('body').append($tooltip);
                    
                    var pos = $element.offset();
                    $tooltip.css({
                        position: 'absolute',
                        top: pos.top - $tooltip.outerHeight() - 5,
                        left: pos.left + ($element.outerWidth() / 2) - ($tooltip.outerWidth() / 2),
                        zIndex: 9999
                    });
                });
                
                $element.on('mouseleave', function() {
                    $('.ga-tooltip').remove();
                });
            });
        },
        
        /**
         * Validation des dates avec contraintes métier
         */
        initDateValidation: function() {
            $('#date_fin_adhesion').on('change', function() {
                var $dateFin = $(this);
                var $dateDebut = $('#date_adhesion');
                
                var dateFin = new Date($dateFin.val());
                var dateDebut = new Date($dateDebut.val());
                
                if (dateFin && dateDebut && dateFin <= dateDebut) {
                    alert('La date de fin d\'adhésion doit être postérieure à la date d\'adhésion.');
                    $dateFin.val('');
                }
            });
            
            $('#date_naissance').on('change', function() {
                var $dateNaissance = $(this);
                var dateNaissance = new Date($dateNaissance.val());
                var aujourd_hui = new Date();
                
                if (dateNaissance > aujourd_hui) {
                    alert('La date de naissance ne peut pas être dans le futur.');
                    $dateNaissance.val('');
                } else {
                    // Calcul automatique du statut Junior (moins de 18 ans)
                    var age = GestionAdherents.calculateAge(dateNaissance);
                    if (age < 18) {
                        $('#is_junior').prop('checked', true);
                    }
                }
            });
        },
        
        /**
         * Auto-sauvegarde des brouillons
         */
        initAutosave: function() {
            if ($('.ga-adherent-form').length && localStorage) {
                var formId = 'ga_adherent_draft_' + ($('input[name="id"]').val() || 'new');
                
                // Charger le brouillon
                var draft = localStorage.getItem(formId);
                if (draft) {
                    try {
                        var draftData = JSON.parse(draft);
                        if (confirm('Un brouillon a été trouvé. Voulez-vous le restaurer ?')) {
                            GestionAdherents.loadDraft(draftData);
                        } else {
                            localStorage.removeItem(formId);
                        }
                    } catch (e) {
                        localStorage.removeItem(formId);
                    }
                }
                
                // Sauvegarder automatiquement toutes les 30 secondes
                setInterval(function() {
                    GestionAdherents.saveDraft(formId);
                }, 30000);
                
                // Sauvegarder lors de la modification
                $('.ga-adherent-form input, .ga-adherent-form select, .ga-adherent-form textarea').on('change', function() {
                    setTimeout(function() {
                        GestionAdherents.saveDraft(formId);
                    }, 1000);
                });
                
                // Nettoyer le brouillon lors de la soumission réussie
                $('.ga-adherent-form').on('submit', function() {
                    localStorage.removeItem(formId);
                });
            }
        },
        
        /**
         * Chargement des statistiques
         */
        loadStats: function() {
            $.ajax({
                url: ga_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'ga_get_stats',
                    nonce: ga_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        GestionAdherents.displayStats(response.data);
                    }
                }
            });
        },
        
        /**
         * Affichage des statistiques
         */
        displayStats: function(stats) {
            $('.ga-stat-card[data-stat="total"] .ga-stat-number').text(stats.total || 0);
            $('.ga-stat-card[data-stat="active"] .ga-stat-number').text(stats.active || 0);
            $('.ga-stat-card[data-stat="junior"] .ga-stat-number').text(stats.junior || 0);
            $('.ga-stat-card[data-stat="pole"] .ga-stat-number').text(stats.pole_excellence || 0);
        },
        
        /**
         * Sauvegarde de brouillon
         */
        saveDraft: function(formId) {
            var draftData = {};
            $('.ga-adherent-form input, .ga-adherent-form select, .ga-adherent-form textarea').each(function() {
                var $field = $(this);
                var name = $field.attr('name');
                var value = $field.val();
                
                if (name && value) {
                    if ($field.is(':checkbox')) {
                        draftData[name] = $field.is(':checked');
                    } else {
                        draftData[name] = value;
                    }
                }
            });
            
            if (Object.keys(draftData).length > 0) {
                localStorage.setItem(formId, JSON.stringify(draftData));
            }
        },
        
        /**
         * Chargement de brouillon
         */
        loadDraft: function(draftData) {
            for (var name in draftData) {
                var $field = $('[name="' + name + '"]');
                var value = draftData[name];
                
                if ($field.length) {
                    if ($field.is(':checkbox')) {
                        $field.prop('checked', value);
                    } else {
                        $field.val(value);
                    }
                }
            }
        },
        
        /**
         * Affichage d'une notice
         */
        showNotice: function(type, message) {
            var $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
            
            // Insérer la notice
            if ($('.wrap h1').length) {
                $('.wrap h1').after($notice);
            } else {
                $('.wrap').prepend($notice);
            }
            
            // Auto-suppression après 5 secondes
            setTimeout(function() {
                $notice.fadeOut(400, function() {
                    $(this).remove();
                });
            }, 5000);
            
            // Bouton de fermeture
            $notice.on('click', '.notice-dismiss', function() {
                $notice.fadeOut(400, function() {
                    $(this).remove();
                });
            });
        },
        
        /**
         * Affichage des erreurs de validation
         */
        displayErrors: function(errors) {
            if (!errors) return;
            
            // Nettoyer les erreurs précédentes
            $('.ga-adherent-form .error').remove();
            $('.ga-adherent-form input, .ga-adherent-form select').removeClass('error');
            
            // Afficher les nouvelles erreurs
            for (var field in errors) {
                var $field = $('[name="' + field + '"]');
                var errorMessage = errors[field];
                
                if ($field.length) {
                    $field.addClass('error');
                    $field.after('<p class="error">' + errorMessage + '</p>');
                }
            }
            
            // Défiler vers la première erreur
            var $firstError = $('.ga-adherent-form .error').first();
            if ($firstError.length) {
                $('html, body').animate({
                    scrollTop: $firstError.offset().top - 100
                }, 500);
            }
        },
        
        /**
         * Utilitaires de validation
         */
        isValidEmail: function(email) {
            var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        },
        
        isValidPhone: function(phone) {
            var cleaned = phone.replace(/\D/g, '');
            return cleaned.length >= 10 && cleaned.length <= 15;
        },
        
        isValidPostalCode: function(code) {
            return /^\d{5}$/.test(code);
        },
        
        isValidLicense: function(license) {
            // 1 lettre suivie de 5 chiffres
            return /^[A-Z][0-9]{5}$/.test(license.toUpperCase());
        },
        
        isValidDate: function(dateString) {
            var date = new Date(dateString);
            return date instanceof Date && !isNaN(date);
        },
        
        /**
         * Calcul de l'âge
         */
        calculateAge: function(birthDate) {
            var today = new Date();
            var age = today.getFullYear() - birthDate.getFullYear();
            var monthDiff = today.getMonth() - birthDate.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            
            return age;
        }
    };
    
    /**
     * Initialisation au chargement de la page
     */
    $(document).ready(function() {
        GestionAdherents.init();
    });
    
    // Exposer l'objet pour usage externe si nécessaire
    window.GestionAdherents = GestionAdherents;
    
})(jQuery);