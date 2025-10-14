<?php
/**
 * Template Part: Formulário de Contato
 *
 * @package WP_Resgate
 */

$recaptcha_site_key = get_theme_mod('wp_resgate_recaptcha_site_key', '');
?>

<!-- CTA FINAL / FORM -->
<section id="diagnostico" class="py-5 bg-gradient-primary text-white position-relative">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <span class="badge bg-white text-primary rounded-pill mb-3 px-3 py-2">
                    <i class="bi bi-clipboard2-pulse me-1"></i> 
                    <?php esc_html_e('Diagnóstico gratuito', 'wp-resgate'); ?>
                </span>
                <h2 class="display-6 fw-bold mb-3"><?php esc_html_e('Pronto para recuperar seu site?', 'wp-resgate'); ?></h2>
                <p class="lead text-white-75 mx-auto" style="max-width: 700px;">
                    <?php esc_html_e('Conte o que está acontecendo e receba um plano personalizado para resolver tudo', 'wp-resgate'); ?>
                </p>
            </div>
        </div>
     
        <!-- Formulário em linha única, centralizado -->
        <div class="row justify-content-center g-5">
            <div class="col-12 col-lg-8 col-xl-7">
                <div class="form-card">
                    <div class="card border-0 shadow-lg bg-white">
                        <div class="card-header bg-transparent border-0 pt-4 pb-2">
                            <div class="text-center">
                                <div class="form-icon mb-3">
                                    <div class="icon-circle bg-primary text-white mx-auto" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                        <i class="bi bi-clipboard2-pulse"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold text-dark mb-2"><?php esc_html_e('Solicite seu diagnóstico', 'wp-resgate'); ?></h4>
                                <p class="text-muted small mb-0"><?php esc_html_e('Preencha os dados e receba uma análise completa', 'wp-resgate'); ?></p>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-4">
                        <form id="wp-resgate-contact-form" class="form-cta" novalidate>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="form-label">
                                            <?php esc_html_e('Seu nome', 'wp-resgate'); ?>
                                        </label>
                                        <input type="text" 
                                               id="name" 
                                               name="name" 
                                               class="form-control" 
                                               placeholder="<?php esc_attr_e('Nome completo', 'wp-resgate'); ?>" 
                                               required />
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="form-label">
                                            <?php esc_html_e('E-mail', 'wp-resgate'); ?>
                                        </label>
                                        <input type="email" 
                                               id="email" 
                                               name="email" 
                                               class="form-control" 
                                               placeholder="<?php esc_attr_e('seuemail@exemplo.com', 'wp-resgate'); ?>" 
                                               required />
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone" class="form-label">
                                            <?php esc_html_e('Telefone', 'wp-resgate'); ?>
                                        </label>
                                        <input type="tel" 
                                               id="phone" 
                                               name="phone" 
                                               class="form-control" 
                                               placeholder="<?php esc_attr_e('(11) 99999-9999', 'wp-resgate'); ?>" />
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="website" class="form-label">
                                            <?php esc_html_e('URL do site', 'wp-resgate'); ?>
                                        </label>
                                        <input type="url" 
                                               id="website" 
                                               name="website" 
                                               class="form-control" 
                                               placeholder="<?php esc_attr_e('https://seusite.com', 'wp-resgate'); ?>" />
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="problem_type" class="form-label">
                                            <?php esc_html_e('Tipo de problema', 'wp-resgate'); ?>
                                        </label>
                                        <select id="problem_type" name="problem_type" class="form-select">
                                            <option value=""><?php esc_html_e('Selecione...', 'wp-resgate'); ?></option>
                                            <option value="malware"><?php esc_html_e('Malware/Hack', 'wp-resgate'); ?></option>
                                            <option value="error"><?php esc_html_e('Erros 500/502', 'wp-resgate'); ?></option>
                                            <option value="migration"><?php esc_html_e('Migração', 'wp-resgate'); ?></option>
                                            <option value="performance"><?php esc_html_e('Performance', 'wp-resgate'); ?></option>
                                            <option value="maintenance"><?php esc_html_e('Manutenção', 'wp-resgate'); ?></option>
                                            <option value="other"><?php esc_html_e('Outro', 'wp-resgate'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="urgency" class="form-label">
                                            <?php esc_html_e('Urgência', 'wp-resgate'); ?>
                                        </label>
                                        <select id="urgency" name="urgency" class="form-select">
                                            <option value=""><?php esc_html_e('Selecione...', 'wp-resgate'); ?></option>
                                            <option value="low"><?php esc_html_e('Baixa - Posso aguardar alguns dias', 'wp-resgate'); ?></option>
                                            <option value="medium"><?php esc_html_e('Média - Preciso resolver essa semana', 'wp-resgate'); ?></option>
                                            <option value="high"><?php esc_html_e('Alta - Preciso resolver hoje', 'wp-resgate'); ?></option>
                                            <option value="critical"><?php esc_html_e('Crítica - Site fora do ar', 'wp-resgate'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="description" class="form-label">
                                            <?php esc_html_e('Descreva o problema', 'wp-resgate'); ?>
                                        </label>
                                        <textarea id="description" 
                                                  name="description" 
                                                  class="form-control" 
                                                  rows="4" 
                                                  placeholder="<?php esc_attr_e('Conte detalhadamente o que está acontecendo com seu site...', 'wp-resgate'); ?>" 
                                                  required></textarea>
                                        <div class="form-text">
                                            <?php esc_html_e('Quanto mais detalhes, melhor poderemos ajudar', 'wp-resgate'); ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Honeypot para spam -->
                                <div class="col-12 d-none">
                                    <input type="text" name="honeypot" tabindex="-1" autocomplete="off" />
                                </div>
                                
                                <!-- Campo source -->
                                <input type="hidden" name="source" value="website_form" />
                                
                                <?php if ($recaptcha_site_key) : ?>
                                <div class="col-12">
                                    <div class="g-recaptcha" data-sitekey="<?php echo esc_attr($recaptcha_site_key); ?>"></div>
                                </div>
                                <?php endif; ?>

                                <div class="col-12 d-grid">
                                    <button type="submit" 
                                            class="btn btn-primary btn-lg submit-btn form-btn">
                                        <i class="bi bi-clipboard2-check me-2"></i> 
                                        <?php esc_html_e('Solicitar Diagnóstico Gratuito', 'wp-resgate'); ?>
                                    </button>
                                </div>
                                
                                <div class="col-12 text-center small text-muted mt-3">
                                    <div class="d-flex align-items-center justify-content-center gap-3">
                                        <div>
                                            <i class="bi bi-shield-check text-success me-1"></i>
                                            <?php esc_html_e('100% Seguro', 'wp-resgate'); ?>
                                        </div>
                                        <div>
                                            <i class="bi bi-clock text-primary me-1"></i>
                                            <?php esc_html_e('Resposta em 2h', 'wp-resgate'); ?>
                                        </div>
                                        <div>
                                            <i class="bi bi-gift text-warning me-1"></i>
                                            <?php esc_html_e('Gratuito', 'wp-resgate'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('diagnostic-form');
    const submitBtn = document.getElementById('submit-btn');
    const formMessage = document.getElementById('form-message');
    
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Reset previous states
            clearValidationErrors();
            setLoadingState(true);
            
            // Get form data
            const formData = new FormData(form);
            
            // Validate client-side
            if (!validateForm(formData)) {
                setLoadingState(false);
                return;
            }
            
            try {
                const response = await fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
                    method: 'POST',
                    body: new URLSearchParams({
                        action: 'diagnostic_form',
                        nonce: '<?php echo wp_create_nonce('wp_resgate_nonce'); ?>',
                        name: formData.get('client_name'),
                        email: formData.get('client_email'),
                        website: formData.get('website_url'),
                        problem: formData.get('problem_description')
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage(result.data, 'success');
                    form.reset();
                    
                    // Track conversion
                    if (typeof trackEvent === 'function') {
                        trackEvent('diagnostic_form_submitted', {
                            website: formData.get('website_url')
                        });
                    }
                } else {
                    showMessage(result.data, 'error');
                }
            } catch (error) {
                console.error('Form submission error:', error);
                showMessage('<?php esc_html_e('Erro ao enviar formulário. Tente novamente.', 'wp-resgate'); ?>', 'error');
            } finally {
                setLoadingState(false);
            }
        });
    }
    
    function validateForm(formData) {
        let isValid = true;
        
        // Name validation
        if (!formData.get('client_name').trim()) {
            showFieldError('client-name', '<?php esc_html_e('Nome é obrigatório', 'wp-resgate'); ?>');
            isValid = false;
        }
        
        // Email validation
        const email = formData.get('client_email');
        if (!email.trim()) {
            showFieldError('client-email', '<?php esc_html_e('E-mail é obrigatório', 'wp-resgate'); ?>');
            isValid = false;
        } else if (!isValidEmail(email)) {
            showFieldError('client-email', '<?php esc_html_e('E-mail inválido', 'wp-resgate'); ?>');
            isValid = false;
        }
        
        // URL validation
        const url = formData.get('website_url');
        if (!url.trim()) {
            showFieldError('website-url', '<?php esc_html_e('URL do site é obrigatória', 'wp-resgate'); ?>');
            isValid = false;
        } else if (!isValidUrl(url)) {
            showFieldError('website-url', '<?php esc_html_e('URL inválida', 'wp-resgate'); ?>');
            isValid = false;
        }
        
        // Problem description validation
        if (!formData.get('problem_description').trim()) {
            showFieldError('problem-description', '<?php esc_html_e('Descrição do problema é obrigatória', 'wp-resgate'); ?>');
            isValid = false;
        }
        
        // Honeypot check
        if (formData.get('website')) {
            isValid = false; // Spam detected
        }
        
        return isValid;
    }
    
    function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = field.nextElementSibling;
        
        field.classList.add('is-invalid');
        errorDiv.textContent = message;
    }
    
    function clearValidationErrors() {
        form.querySelectorAll('.is-invalid').forEach(field => {
            field.classList.remove('is-invalid');
        });
        
        form.querySelectorAll('.invalid-feedback').forEach(error => {
            error.textContent = '';
        });
        
        formMessage.classList.add('d-none');
    }
    
    function showMessage(message, type) {
        formMessage.className = `alert alert-${type === 'success' ? 'success' : 'danger'}`;
        formMessage.textContent = message;
        formMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    function setLoadingState(loading) {
        const btnText = submitBtn.querySelector('.btn-text');
        const spinner = submitBtn.querySelector('.spinner-border');
        
        submitBtn.disabled = loading;
        
        if (loading) {
            btnText.classList.add('d-none');
            spinner.classList.remove('d-none');
        } else {
            btnText.classList.remove('d-none');
            spinner.classList.add('d-none');
        }
    }
    
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
    
    function isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    }
});
</script>
