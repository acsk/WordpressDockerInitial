/**
 * Form Handler para integração com Google Sheets
 * WP Resgate - Sistema de captura de leads (versão vanilla JS)
 */

(function () {
    'use strict';

    const GLOBAL_CONFIG = typeof window !== 'undefined' ? window.wpResgateForm || null : null;
    const FORM_ID = 'wp-resgate-contact-form';
    const MESSAGE_TIMEOUT = 5000;

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById(FORM_ID);

        if (!form || !GLOBAL_CONFIG) {
            return;
        }

        new WPResgateFormHandler(form, GLOBAL_CONFIG);
    });

    class WPResgateFormHandler {
        constructor(form, config) {
            this.form = form;
            this.config = config;
            this.submitBtn = form.querySelector('.submit-btn');
            this.originalButtonHTML = this.submitBtn ? this.submitBtn.innerHTML : '';
            this.messageTimer = null;
            this.recaptchaObserver = null;
            this.recaptchaPromise = null;
            this.recaptchaLoading = false;
            this.redirectTimeout = null;

            this.bindEvents();
            this.setupRequiredIndicators();
            this.setupRecaptchaLoader();
        }

        bindEvents() {
            this.form.addEventListener('submit', (event) => this.handleSubmit(event));

            const fields = Array.from(this.form.querySelectorAll('input, textarea, select'));
            fields.forEach((field) => {
                field.addEventListener('blur', () => this.validateField(field));
            });

            const phoneField = this.form.querySelector('input[name="phone"]');
            if (phoneField) {
                phoneField.addEventListener('input', () => this.applyPhoneMask(phoneField));
            }
        }

        async handleSubmit(event) {
            event.preventDefault();

            this.clearMessages();
            this.clearFieldErrors();

            const validation = this.validateForm();

            if (!validation.isValid) {
                this.showMessage(validation.message || this.config.messages.validation_error, 'error');
                return;
            }

            if (this.shouldValidateRecaptcha()) {
                await this.loadRecaptchaScript();

                if (typeof window.grecaptcha === 'undefined') {
                    this.showMessage(this.config.messages.error, 'error');
                    return;
                }

                const recaptchaResponse = window.grecaptcha.getResponse();
                if (!recaptchaResponse) {
                    this.showMessage(this.config.messages.recaptcha, 'error');
                    return;
                }
            }

            this.setLoading(true);

            const formData = new FormData(this.form);
            formData.append('action', 'wp_resgate_form_submit');
            formData.append('nonce', this.config.nonce);

            try {
                const response = await fetch(this.config.ajax_url, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                });

                const result = await response.json();

                if (result.success) {
                    this.showMessage(result.data.message, 'success');
                    this.resetForm();
                    this.trackConversion(result.data);
                    this.scheduleRedirect();
                } else {
                    this.showMessage(result.data.message, 'error');
                }
            } catch (error) {
                if (typeof console !== 'undefined' && console.error) {
                    console.error('Form submission error:', error);
                }
                this.showMessage(this.config.messages.error, 'error');
            } finally {
                this.setLoading(false);

                if (typeof window.grecaptcha !== 'undefined') {
                    window.grecaptcha.reset();
                }
            }
        }

        validateForm() {
            let isValid = true;
            let message = '';
            const requiredFields = this.form.querySelectorAll('input[required], textarea[required], select[required]');

            requiredFields.forEach((field) => {
                if (!field.value.trim()) {
                    this.showFieldError(field, 'Este campo é obrigatório');
                    isValid = false;
                }
            });

            const emailField = this.form.querySelector('input[name="email"]');
            if (emailField && emailField.value && !this.isValidEmail(emailField.value)) {
                this.showFieldError(emailField, 'Email inválido');
                isValid = false;
            }

            const phoneField = this.form.querySelector('input[name="phone"]');
            if (phoneField && phoneField.value) {
                const digits = phoneField.value.replace(/\D/g, '');
                if (digits.length < 10) {
                    this.showFieldError(phoneField, 'Telefone deve ter pelo menos 10 dígitos');
                    isValid = false;
                }
            }

            const websiteField = this.form.querySelector('input[name="website"]');
            if (websiteField && websiteField.value && !this.isValidUrl(websiteField.value)) {
                this.showFieldError(websiteField, 'URL inválida');
                isValid = false;
            }

            const honeypot = this.form.querySelector('input[name="honeypot"]');
            if (honeypot && honeypot.value.trim() !== '') {
                isValid = false;
            }

            const privacyConsent = this.form.querySelector('input[name="privacy_consent"]');
            if (privacyConsent && !privacyConsent.checked) {
                message = 'Você deve aceitar os termos de privacidade';
                isValid = false;
            }

            if (!isValid && !message) {
                message = this.config.messages.validation_error;
            }

            return {
                isValid,
                message,
            };
        }

        validateField(field) {
            if (!field) {
                return true;
            }

            const value = field.value.trim();
            const name = field.getAttribute('name');
            let message = '';

            if (field.required && !value) {
                message = 'Este campo é obrigatório';
            } else if (name === 'email' && value && !this.isValidEmail(value)) {
                message = 'Email inválido';
            } else if (name === 'phone' && value && value.replace(/\D/g, '').length < 10) {
                message = 'Telefone deve ter pelo menos 10 dígitos';
            } else if (name === 'website' && value && !this.isValidUrl(value)) {
                message = 'URL inválida';
            }

            if (message) {
                this.showFieldError(field, message);
                return false;
            }

            this.clearFieldError(field);
            return true;
        }

        showFieldError(field, message) {
            if (!field) {
                return;
            }

            this.clearFieldError(field);
            field.classList.add('is-invalid');

            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = message;

            if (field.type === 'checkbox' && field.parentElement) {
                field.parentElement.appendChild(feedback);
            } else {
                field.insertAdjacentElement('afterend', feedback);
            }
        }

        clearFieldError(field) {
            if (!field) {
                return;
            }

            field.classList.remove('is-invalid');

            const nextSibling = field.nextElementSibling;
            if (nextSibling && nextSibling.classList.contains('invalid-feedback')) {
                nextSibling.remove();
            }

            if (field.type === 'checkbox' && field.parentElement) {
                const checkboxFeedback = field.parentElement.querySelector('.invalid-feedback');
                if (checkboxFeedback) {
                    checkboxFeedback.remove();
                }
            }
        }

        clearFieldErrors() {
            this.form.querySelectorAll('.is-invalid').forEach((field) => {
                this.clearFieldError(field);
            });
        }

        showMessage(message, type) {
            if (!message) {
                return;
            }

            this.clearMessages();

            const alert = document.createElement('div');
            const isSuccess = type === 'success';
            const icon = isSuccess ? 'bi-check-circle' : 'bi-exclamation-triangle';

            alert.className = `form-message alert alert-${isSuccess ? 'success' : 'danger'} alert-dismissible fade show mt-3`;
            alert.setAttribute('role', 'alert');
            alert.innerHTML = `
                <i class="bi ${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;

            this.form.insertAdjacentElement('afterbegin', alert);
            this.scrollToForm();

            if (isSuccess) {
                this.messageTimer = window.setTimeout(() => {
                    alert.classList.remove('show');
                    alert.addEventListener('transitionend', () => alert.remove(), { once: true });
                }, MESSAGE_TIMEOUT);
            }
        }

        clearMessages() {
            if (this.messageTimer) {
                window.clearTimeout(this.messageTimer);
                this.messageTimer = null;
            }

            this.form.querySelectorAll('.form-message').forEach((message) => {
                message.remove();
            });
        }

        setLoading(isLoading) {
            if (!this.submitBtn) {
                return;
            }

            this.submitBtn.disabled = isLoading;

            if (isLoading) {
                this.submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    ${this.config.messages.sending}
                `;
            } else {
                this.submitBtn.innerHTML = this.originalButtonHTML;
            }
        }

        resetForm() {
            this.form.reset();
            this.clearFieldErrors();
        }

        setupRequiredIndicators() {
            const requiredFields = this.form.querySelectorAll('input[required], textarea[required], select[required]');

            requiredFields.forEach((field) => {
                const group = field.closest('.form-group');
                if (!group) {
                    return;
                }

                const label = group.querySelector('label');
                if (!label || label.querySelector('.required')) {
                    return;
                }

                const indicator = document.createElement('span');
                indicator.className = 'required text-danger ms-1';
                indicator.textContent = '*';
                label.appendChild(indicator);
            });
        }

        applyPhoneMask(field) {
            if (!field) {
                return;
            }

            let value = field.value.replace(/\D/g, '');

            if (value.length > 11) {
                value = value.substring(0, 11);
            }

            if (value.length > 2) {
                value = `(${value.substring(0, 2)}) ${value.substring(2)}`;
            }

            if (value.length > 9) {
                value = value.replace(/(\d{4,5})(\d{4})$/, '$1-$2');
            }

            field.value = value;
        }

        isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        isValidUrl(url) {
            try {
                const prefixed = url.startsWith('http') ? url : `https://${url}`;
                new URL(prefixed);
                return true;
            } catch (error) {
                return false;
            }
        }

        trackConversion(data) {
            if (typeof window.gtag !== 'undefined') {
                window.gtag('event', 'form_submit', {
                    event_category: 'engagement',
                    event_label: 'contact_form',
                    value: 1,
                });
            }

            if (typeof window.fbq !== 'undefined') {
                window.fbq('track', 'Lead', {
                    content_name: 'Diagnóstico WordPress',
                    content_category: 'form_submission',
                });
            }

            if (typeof window.customTracker !== 'undefined' && typeof window.customTracker.track === 'function') {
                window.customTracker.track('form_submission', {
                    form_type: 'contact',
                    lead_id: data?.lead_id ?? null,
                });
            }
        }

        scheduleRedirect() {
            const redirect = this.config.redirect;

            if (!redirect || redirect.enabled === false) {
                return;
            }

            const baseUrl = redirect.baseUrl || redirect.url;
            if (!baseUrl) {
                return;
            }

            const delay = Number(redirect.delay) || 1200;
            let finalUrl = baseUrl;

            if (redirect.appendReturn !== false) {
                try {
                    const url = new URL(baseUrl, window.location.origin);
                    const param = redirect.returnParam || 'return';
                    url.searchParams.set(param, window.location.href);
                    finalUrl = url.toString();
                } catch (error) {
                    finalUrl = baseUrl;
                }
            }

            if (this.redirectTimeout) {
                window.clearTimeout(this.redirectTimeout);
            }

            this.redirectTimeout = window.setTimeout(() => {
                window.location.href = finalUrl;
            }, delay);
        }

        scrollToForm() {
            const top = this.form.getBoundingClientRect().top + window.scrollY - 100;
            window.scrollTo({
                top: top > 0 ? top : 0,
                behavior: 'smooth',
            });
        }

        setupRecaptchaLoader() {
            if (!this.shouldValidateRecaptcha()) {
                return;
            }

            const triggerLoad = () => {
                this.loadRecaptchaScript();
            };

            if ('IntersectionObserver' in window) {
                this.recaptchaObserver = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            triggerLoad();
                            if (this.recaptchaObserver) {
                                this.recaptchaObserver.disconnect();
                            }
                        }
                    });
                }, { rootMargin: '200px' });

                this.recaptchaObserver.observe(this.form);
            } else {
                triggerLoad();
            }

            this.form.addEventListener('focusin', triggerLoad, { once: true });
        }

        shouldValidateRecaptcha() {
            return Boolean(this.config.recaptcha && this.config.recaptcha.enabled);
        }

        async loadRecaptchaScript() {
            if (!this.shouldValidateRecaptcha() || typeof window.grecaptcha !== 'undefined') {
                return Promise.resolve();
            }

            if (this.recaptchaPromise) {
                return this.recaptchaPromise;
            }

            const existingScript = document.querySelector('script[data-wp-resgate-recaptcha]');
            if (existingScript) {
                this.recaptchaPromise = new Promise((resolve, reject) => {
                    existingScript.addEventListener('load', () => resolve(), { once: true });
                    existingScript.addEventListener('error', () => reject(), { once: true });
                });
                return this.recaptchaPromise;
            }

            this.recaptchaLoading = true;

            this.recaptchaPromise = new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = this.config.recaptcha.scriptUrl || 'https://www.google.com/recaptcha/api.js';
                script.async = true;
                script.defer = true;
                script.setAttribute('data-wp-resgate-recaptcha', 'true');

                script.onload = () => {
                    this.recaptchaLoading = false;
                    resolve();
                };

                script.onerror = () => {
                    this.recaptchaLoading = false;
                    this.recaptchaPromise = null;
                    reject();
                };

                document.head.appendChild(script);
            });

            try {
                await this.recaptchaPromise;
            } catch (error) {
                if (typeof console !== 'undefined' && console.error) {
                    console.error('Não foi possível carregar o reCAPTCHA.', error);
                }
            }
        }
    }
})();
