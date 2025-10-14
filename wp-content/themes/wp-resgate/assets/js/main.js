/**
 * WP Resgate - Main JavaScript
 * 
 * Funcionalidades principais do tema com performance e acessibilidade
 * 
 * @package WP_Resgate
 * @version 1.0.0
 */

(function() {
    'use strict';
    
    // Configurações globais
    const WPResgate = {
        config: {
            smoothScrollOffset: 80,
            lazyLoadOffset: 100,
            debounceDelay: 250
        },
        
        // Cache de elementos DOM
        elements: {},
        
        // Estado da aplicação
        state: {
            isScrolling: false,
            currentYear: new Date().getFullYear()
        }
    };
    
    /**
     * Inicialização quando DOM estiver pronto
     */
    function init() {
        try {
            cacheElements();
            bindEvents();
            setupAccessibility();
            initLazyLoading();
            updateCurrentYear();
            initSmoothScroll();
            initFormValidation();
            initAnalytics();
            
            // Indicar que o tema está carregado
            if (WPResgate.elements.body) {
                WPResgate.elements.body.classList.add('wp-resgate-loaded');
            }
        } catch (error) {
            console.error('Erro na inicialização do tema:', error);
        }
    }
    
    /**
     * Cache de elementos DOM para performance
     */
    function cacheElements() {
        try {
            WPResgate.elements = {
                body: document.body || document.documentElement,
                navbar: document.querySelector('.navbar'),
                heroSection: document.querySelector('.hero'),
                diagnosticForm: document.getElementById('diagnostic-form'),
                ctaButtons: document.querySelectorAll('a[href="#diagnostico"]') || [],
                whatsappLinks: document.querySelectorAll('a[href^="https://wa.me/"]') || [],
                lazyImages: document.querySelectorAll('img[loading="lazy"]') || [],
                yearElement: document.getElementById('current-year')
            };
        } catch (error) {
            console.warn('Erro ao cache elementos DOM:', error);
            WPResgate.elements = { body: document.body };
        }
    }
    
    /**
     * Vincular eventos
     */
    function bindEvents() {
        // Scroll events - usar throttle para melhor performance
        window.addEventListener('scroll', throttle(handleScroll, 16), { passive: true });
        
        // Resize events
        window.addEventListener('resize', debounce(handleResize, WPResgate.config.debounceDelay));
        
        // Form events
        if (WPResgate.elements.diagnosticForm) {
            WPResgate.elements.diagnosticForm.addEventListener('submit', handleFormSubmit);
        }
        
        // CTA tracking
        WPResgate.elements.ctaButtons.forEach(btn => {
            btn.addEventListener('click', () => trackEvent('diagnostic_cta_click', {
                element: btn.textContent.trim(),
                page: window.location.pathname
            }));
        });
        
        // WhatsApp tracking
        WPResgate.elements.whatsappLinks.forEach(btn => {
            btn.addEventListener('click', () => trackEvent('whatsapp_click', {
                page: window.location.pathname
            }));
        });
        
        // Keyboard navigation
        document.addEventListener('keydown', handleKeyboardNavigation);
    }
    
    /**
     * Configurações de acessibilidade
     */
    function setupAccessibility() {
        // Adicionar role e aria-label para elementos importantes
        const navbar = WPResgate.elements.navbar;
        if (navbar && !navbar.getAttribute('role')) {
            navbar.setAttribute('role', 'navigation');
            navbar.setAttribute('aria-label', 'Menu principal');
        }
        
        // Skip links para navegação por teclado
        const skipLink = document.querySelector('.visually-hidden-focusable');
        if (skipLink) {
            skipLink.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.focus();
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        }
        
        // Melhorar foco em elementos interativos
        const interactiveElements = document.querySelectorAll('button, [role="button"], a, input, textarea, select');
        interactiveElements.forEach(element => {
            if (!element.getAttribute('tabindex') && element.tagName !== 'A') {
                element.setAttribute('tabindex', '0');
            }
        });
    }
    
    /**
     * Lazy loading de imagens
     */
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const lazyImageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        img.classList.add('loaded');
                        lazyImageObserver.unobserve(img);
                    }
                });
            }, {
                rootMargin: `${WPResgate.config.lazyLoadOffset}px`
            });
            
            WPResgate.elements.lazyImages.forEach(img => {
                lazyImageObserver.observe(img);
            });
        } else {
            // Fallback para browsers antigos
            WPResgate.elements.lazyImages.forEach(img => {
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                }
                img.classList.add('loaded');
            });
        }
    }
    
    /**
     * Atualizar ano atual
     */
    function updateCurrentYear() {
        if (WPResgate.elements.yearElement) {
            WPResgate.elements.yearElement.textContent = WPResgate.state.currentYear;
        }
    }
    
    /**
     * Smooth scroll para âncoras
     */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                
                // Verificar se é uma âncora válida
                if (targetId === '#' || targetId === '') {
                    return;
                }
                
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    e.preventDefault();
                    
                    const navbar = WPResgate.elements.navbar;
                    const navbarHeight = navbar ? navbar.offsetHeight : 0;
                    const offsetTop = targetElement.getBoundingClientRect().top + window.pageYOffset - navbarHeight - 20;
                    
                    // Usar scroll suave nativo quando disponível
                    if ('scrollBehavior' in document.documentElement.style) {
                        window.scrollTo({
                            top: Math.max(0, offsetTop),
                            behavior: 'smooth'
                        });
                    } else {
                        // Fallback para browsers antigos
                        smoothScrollTo(Math.max(0, offsetTop));
                    }
                    
                    // Atualizar foco para acessibilidade
                    setTimeout(() => {
                        if (targetElement.hasAttribute('tabindex') || targetElement.tagName.match(/^(A|BUTTON|INPUT|TEXTAREA|SELECT)$/)) {
                            targetElement.focus();
                        }
                    }, 600);
                }
            });
        });
    }
    
    /**
     * Validação avançada de formulários
     */
    function initFormValidation() {
        const forms = document.querySelectorAll('form[novalidate]');
        
        forms.forEach(form => {
            const inputs = form.querySelectorAll('input, textarea, select');
            
            inputs.forEach(input => {
                // Validação em tempo real
                input.addEventListener('blur', () => validateField(input));
                input.addEventListener('input', debounce(() => validateField(input), 500));
            });
        });
    }
    
    /**
     * Validar campo individual
     */
    function validateField(field) {
        const value = field.value.trim();
        const fieldType = field.type;
        const isRequired = field.hasAttribute('required');
        let isValid = true;
        let errorMessage = '';
        
        // Reset estado anterior
        field.classList.remove('is-invalid', 'is-valid');
        const errorElement = field.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.textContent = '';
        }
        
        // Validações
        if (isRequired && !value) {
            isValid = false;
            errorMessage = 'Este campo é obrigatório.';
        } else if (value) {
            switch (fieldType) {
                case 'email':
                    if (!isValidEmail(value)) {
                        isValid = false;
                        errorMessage = 'Por favor, insira um e-mail válido.';
                    }
                    break;
                case 'url':
                    if (!isValidUrl(value)) {
                        isValid = false;
                        errorMessage = 'Por favor, insira uma URL válida.';
                    }
                    break;
                case 'tel':
                    if (!isValidPhone(value)) {
                        isValid = false;
                        errorMessage = 'Por favor, insira um número de WhatsApp válido.';
                    }
                    break;
            }
        }
        
        // Aplicar resultado da validação
        if (isValid) {
            field.classList.add('is-valid');
        } else {
            field.classList.add('is-invalid');
            if (errorElement) {
                errorElement.textContent = errorMessage;
            }
        }
        
        return isValid;
    }
    
    /**
     * Handle scroll events
     */
    function handleScroll() {
        // Usar requestAnimationFrame para melhor performance
        if (!WPResgate.state.isScrolling) {
            WPResgate.state.isScrolling = true;
            
            requestAnimationFrame(() => {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                // Navbar background on scroll
                if (WPResgate.elements.navbar) {
                    if (scrollTop > 50) {
                        WPResgate.elements.navbar.classList.add('scrolled');
                    } else {
                        WPResgate.elements.navbar.classList.remove('scrolled');
                    }
                }
                
                // Reset hero transform para evitar animações/parallax
                if (WPResgate.elements.heroSection) {
                    WPResgate.elements.heroSection.style.transform = '';
                }
                
                WPResgate.state.isScrolling = false;
            });
        }
    }
    
    /**
     * Handle resize events
     */
    function handleResize() {
        // Recalcular dimensões se necessário
        if (window.innerWidth !== WPResgate.state.windowWidth) {
            WPResgate.state.windowWidth = window.innerWidth;
            // Implementar lógica de redimensionamento se necessário
        }
    }
    
    /**
     * Handle keyboard navigation
     */
    function handleKeyboardNavigation(e) {
        // ESC key to close modals or dropdowns
        if (e.key === 'Escape') {
            const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
            openDropdowns.forEach(dropdown => {
                const toggle = dropdown.previousElementSibling;
                if (toggle) {
                    bootstrap.Dropdown.getInstance(toggle)?.hide();
                }
            });
        }
        
        // Enter key on buttons
        if (e.key === 'Enter' && e.target.getAttribute('role') === 'button') {
            e.target.click();
        }
    }
    
    /**
     * Handle form submission
     */
    function handleFormSubmit(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        let isValid = true;
        
        // Validar todos os campos
        const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });
        
        // Honeypot check
        if (formData.get('website')) {
            return; // Spam detected
        }
        
        if (isValid) {
            submitForm(form, formData);
        } else {
            // Focar no primeiro campo inválido
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.focus();
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    }
    
    /**
     * Submit form via AJAX
     */
    async function submitForm(form, formData) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        try {
            // Loading state
            setLoadingState(submitBtn, true);
            
            const response = await fetch(wpResgate.ajaxUrl, {
                method: 'POST',
                body: new URLSearchParams({
                    action: 'diagnostic_form',
                    nonce: wpResgate.nonce,
                    name: formData.get('client_name'),
                    email: formData.get('client_email'),
                    website: formData.get('website_url'),
                    problem: formData.get('problem_description')
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                showFormMessage(form, result.data, 'success');
                form.reset();
                
                // Track conversion
                trackEvent('diagnostic_form_submitted', {
                    website: formData.get('website_url')
                });
                
                // Redirect ou scroll para agradecimento
                setTimeout(() => {
                    form.scrollIntoView({ behavior: 'smooth' });
                }, 1000);
                
            } else {
                showFormMessage(form, result.data || 'Erro ao enviar formulário.', 'error');
            }
            
        } catch (error) {
            console.error('Form submission error:', error);
            showFormMessage(form, 'Erro de conexão. Tente novamente.', 'error');
        } finally {
            setLoadingState(submitBtn, false, originalText);
        }
    }
    
    /**
     * Set loading state for buttons
     */
    function setLoadingState(button, loading, originalText = '') {
        if (loading) {
            button.disabled = true;
            button.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Enviando...
            `;
        } else {
            button.disabled = false;
            button.innerHTML = originalText || button.innerHTML;
        }
    }
    
    /**
     * Show form message
     */
    function showFormMessage(form, message, type) {
        let messageElement = form.querySelector('.form-message');
        
        if (!messageElement) {
            messageElement = document.createElement('div');
            messageElement.className = 'form-message mt-3';
            form.appendChild(messageElement);
        }
        
        messageElement.className = `form-message mt-3 alert alert-${type === 'success' ? 'success' : 'danger'}`;
        messageElement.textContent = message;
        messageElement.setAttribute('role', 'alert');
        
        // Auto hide after 5 seconds for success messages
        if (type === 'success') {
            setTimeout(() => {
                messageElement.style.opacity = '0';
                setTimeout(() => messageElement.remove(), 300);
            }, 5000);
        }
    }
    
    /**
     * Initialize analytics tracking
     */
    function initAnalytics() {
        // Track page view
        trackEvent('page_view', {
            page: window.location.pathname,
            title: document.title
        });
        
        // Track scroll depth
        let scrollDepth = 0;
        const trackScrollDepth = debounce(() => {
            const scrollPercent = Math.round((window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100);
            
            if (scrollPercent > scrollDepth) {
                scrollDepth = Math.floor(scrollPercent / 25) * 25; // Track em intervalos de 25%
                
                if (scrollDepth > 0 && scrollDepth <= 100) {
                    trackEvent('scroll_depth', {
                        depth: scrollDepth,
                        page: window.location.pathname
                    });
                }
            }
        }, 1000);
        
        window.addEventListener('scroll', trackScrollDepth);
    }
    
    /**
     * Track events (GA4, Facebook Pixel, etc.)
     */
    function trackEvent(eventName, eventData = {}) {
        // Google Analytics 4
        if (typeof gtag !== 'undefined') {
            gtag('event', eventName, eventData);
        }
        
        // Facebook Pixel
        if (typeof fbq !== 'undefined') {
            fbq('track', eventName, eventData);
        }
        
        // Console log para desenvolvimento
        if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
            console.log('Event tracked:', eventName, eventData);
        }
    }
    
    /**
     * Smooth scroll fallback para browsers antigos
     */
    function smoothScrollTo(targetPosition) {
        const startPosition = window.pageYOffset;
        const distance = targetPosition - startPosition;
        const duration = Math.min(Math.abs(distance) * 0.5, 800); // Max 800ms
        let start = null;
        
        function animation(currentTime) {
            if (start === null) start = currentTime;
            const timeElapsed = currentTime - start;
            const progress = Math.min(timeElapsed / duration, 1);
            
            // Easing function (easeInOutQuad)
            const ease = progress < 0.5 
                ? 2 * progress * progress 
                : -1 + (4 - 2 * progress) * progress;
            
            window.scrollTo(0, startPosition + distance * ease);
            
            if (timeElapsed < duration) {
                requestAnimationFrame(animation);
            }
        }
        
        requestAnimationFrame(animation);
    }

    /**
     * Utility functions
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
    
    function isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
    
    function isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    }
    
    function isValidPhone(phone) {
        const regex = /^[\+]?[1-9][\d]{0,15}$/;
        return regex.test(phone.replace(/[\s\-\(\)]/g, ''));
    }
    
    // Inicializar quando DOM estiver pronto
    function safeInit() {
        try {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                // Aguardar um frame para garantir que tudo está carregado
                requestAnimationFrame(init);
            }
        } catch (error) {
            console.error('Erro na inicialização segura:', error);
            // Tentar inicializar de forma básica mesmo com erro
            setTimeout(init, 100);
        }
    }
    
    // Error handler global para o tema
    window.addEventListener('error', function(e) {
        if (e.filename && e.filename.includes('main.js')) {
            console.warn('Erro interceptado no tema WP Resgate:', e.message);
        }
    });
    
    // Inicializar
    safeInit();
    
    // Expor algumas funções globalmente se necessário
    window.WPResgate = window.WPResgate || {};
    Object.assign(window.WPResgate, {
        trackEvent,
        validateField,
        smoothScrollTo
    });
    
})();
