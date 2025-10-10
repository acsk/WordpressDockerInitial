/**
 * Form Handler para integração com Google Sheets
 * WP Resgate - Sistema de captura de leads
 */

(function($) {
    'use strict';
    
    class WPResgateFormHandler {
        constructor() {
            this.form = $('#wp-resgate-contact-form');
            this.submitBtn = this.form.find('.submit-btn');
            this.originalBtnText = this.submitBtn.html();
            
            this.init();
        }
        
        init() {
            if (this.form.length) {
                this.bindEvents();
                this.setupValidation();
            }
        }
        
        bindEvents() {
            this.form.on('submit', (e) => this.handleSubmit(e));
            
            // Real-time validation
            this.form.find('input, textarea, select').on('blur', (e) => {
                this.validateField($(e.target));
            });
            
            // Phone mask
            this.form.find('input[name="phone"]').on('input', (e) => {
                this.applyPhoneMask($(e.target));
            });
        }
        
        setupValidation() {
            // Add required indicators
            this.form.find('input[required], textarea[required], select[required]').each(function() {
                const label = $(this).closest('.form-group').find('label');
                if (!label.find('.required').length) {
                    label.append('<span class="required text-danger ms-1">*</span>');
                }
            });
        }
        
        handleSubmit(e) {
            e.preventDefault();
            
            if (!this.validateForm()) {
                this.showMessage(wpResgateForm.messages.validation_error, 'error');
                return;
            }
            
            if (typeof grecaptcha !== 'undefined' && !grecaptcha.getResponse()) {
                this.showMessage(wpResgateForm.messages.recaptcha, 'error');
                return;
            }

            this.setLoading(true);
            
            const formData = new FormData(this.form[0]);
            formData.append('action', 'wp_resgate_form_submit');
            formData.append('nonce', wpResgateForm.nonce);
            
            $.ajax({
                url: wpResgateForm.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: (response) => this.handleSuccess(response),
                error: (xhr, status, error) => this.handleError(xhr, status, error),
                complete: () => {
                    this.setLoading(false);

                    if (typeof grecaptcha !== 'undefined') {
                        grecaptcha.reset();
                    }
                }
            });
        }
        
        handleSuccess(response) {
            if (response.success) {
                this.showMessage(response.data.message, 'success');
                this.resetForm();
                
                // Track conversion (Google Analytics, Facebook Pixel, etc.)
                this.trackConversion(response.data);
                
                // Redirect to thank you page (optional)
                // window.location.href = '/obrigado/';
                
            } else {
                this.showMessage(response.data.message, 'error');
            }
        }
        
        handleError(xhr, status, error) {
            console.error('Form submission error:', {xhr, status, error});
            this.showMessage(wpResgateForm.messages.error, 'error');
        }
        
        validateForm() {
            let isValid = true;
            
            this.form.find('input[required], textarea[required], select[required]').each((index, element) => {
                if (!this.validateField($(element))) {
                    isValid = false;
                }
            });
            
            // Email validation
            const email = this.form.find('input[name="email"]');
            if (email.val() && !this.isValidEmail(email.val())) {
                this.showFieldError(email, 'Email inválido');
                isValid = false;
            }
            
            return isValid;
        }
        
        validateField($field) {
            const value = $field.val().trim();
            const fieldName = $field.attr('name');
            
            // Clear previous errors
            this.clearFieldError($field);
            
            // Required field validation
            if ($field.prop('required') && !value) {
                this.showFieldError($field, 'Este campo é obrigatório');
                return false;
            }
            
            // Specific field validations
            switch (fieldName) {
                case 'email':
                    if (value && !this.isValidEmail(value)) {
                        this.showFieldError($field, 'Email inválido');
                        return false;
                    }
                    break;
                    
                case 'phone':
                    if (value && value.length < 10) {
                        this.showFieldError($field, 'Telefone deve ter pelo menos 10 dígitos');
                        return false;
                    }
                    break;
                    
                case 'website':
                    if (value && !this.isValidUrl(value)) {
                        this.showFieldError($field, 'URL inválida');
                        return false;
                    }
                    break;
            }
            
            return true;
        }
        
        showFieldError($field, message) {
            $field.addClass('is-invalid');
            
            let errorDiv = $field.siblings('.invalid-feedback');
            if (!errorDiv.length) {
                errorDiv = $('<div class="invalid-feedback"></div>');
                $field.after(errorDiv);
            }
            
            errorDiv.text(message);
        }
        
        clearFieldError($field) {
            $field.removeClass('is-invalid');
            $field.siblings('.invalid-feedback').remove();
        }
        
        setLoading(isLoading) {
            if (isLoading) {
                this.submitBtn.prop('disabled', true);
                this.submitBtn.html(`
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    ${wpResgateForm.messages.sending}
                `);
            } else {
                this.submitBtn.prop('disabled', false);
                this.submitBtn.html(this.originalBtnText);
            }
        }
        
        showMessage(message, type) {
            // Remove existing messages
            this.form.find('.form-message').remove();
            
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const iconClass = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
            
            const messageHtml = `
                <div class="form-message alert ${alertClass} alert-dismissible fade show mt-3" role="alert">
                    <i class="bi ${iconClass} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            this.form.prepend(messageHtml);
            
            // Scroll to message
            $('html, body').animate({
                scrollTop: this.form.offset().top - 100
            }, 500);
            
            // Auto-hide success messages
            if (type === 'success') {
                setTimeout(() => {
                    this.form.find('.form-message').fadeOut();
                }, 5000);
            }
        }
        
        resetForm() {
            this.form[0].reset();
            this.form.find('.is-invalid').removeClass('is-invalid');
            this.form.find('.invalid-feedback').remove();
        }
        
        isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        
        isValidUrl(url) {
            try {
                new URL(url.startsWith('http') ? url : 'https://' + url);
                return true;
            } catch {
                return false;
            }
        }
        
        applyPhoneMask($field) {
            let value = $field.val().replace(/\D/g, '');
            
            if (value.length <= 11) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{4,5})(\d{4})$/, '$1-$2');
            }
            
            $field.val(value);
        }
        
        trackConversion(data) {
            // Google Analytics 4
            if (typeof gtag !== 'undefined') {
                gtag('event', 'form_submit', {
                    'event_category': 'engagement',
                    'event_label': 'contact_form',
                    'value': 1
                });
            }
            
            // Facebook Pixel
            if (typeof fbq !== 'undefined') {
                fbq('track', 'Lead', {
                    content_name: 'Diagnóstico WordPress',
                    content_category: 'form_submission'
                });
            }
            
            // Custom tracking
            if (typeof window.customTracker !== 'undefined') {
                window.customTracker.track('form_submission', {
                    form_type: 'contact',
                    lead_id: data.lead_id
                });
            }
        }
    }
    
    // Initialize when DOM is ready
    $(document).ready(() => {
        new WPResgateFormHandler();
    });
    
})(jQuery);
