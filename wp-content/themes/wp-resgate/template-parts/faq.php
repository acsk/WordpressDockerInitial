<?php
/**
 * Template Part: FAQ
 *
 * @package WP_Resgate
 */
?>

<!-- FAQ PREMIUM -->
<section class="py-5 faq-premium-section" style="min-height: 400px; opacity: 1 !important; visibility: visible !important;">
    <div class="container" style="opacity: 1 !important; visibility: visible !important;">
        <!-- Header Premium -->
        <div class="text-center mb-5" style="opacity: 1 !important; visibility: visible !important;">
            <div class="badge bg-primary-soft text-primary rounded-pill px-4 py-2 mb-3" style="opacity: 1 !important; visibility: visible !important; color: #0d6efd !important;">
                <i class="bi bi-patch-question me-2"></i>
                <?php esc_html_e('Esclarecimentos', 'wp-resgate'); ?>
            </div>
            <h2 class="display-6 fw-bold text-dark mb-3" style="opacity: 1 !important; visibility: visible !important; color: #212529 !important;">
                <?php esc_html_e('Perguntas Frequentes', 'wp-resgate'); ?>
            </h2>
            <p class="lead text-muted mb-0" style="opacity: 1 !important; visibility: visible !important; color: #6c757d !important;">
                <?php esc_html_e('Tire suas dúvidas sobre nossos serviços e processos', 'wp-resgate'); ?>
            </p>
        </div>
        
        <!-- Accordion Premium -->
        <div class="row justify-content-center" style="opacity: 1 !important; visibility: visible !important;">
            <div class="col-lg-10" style="opacity: 1 !important; visibility: visible !important;">
                <div class="accordion faq-premium-accordion" id="faq" style="opacity: 1 !important; visibility: visible !important; background: white !important;">
                    <?php 
                    $faqs = get_faqs();
                    
                    foreach ($faqs as $index => $faq) : 
                        $faq_id = 'faq-' . ($index + 1);
                        ?>
                        <div class="accordion-item faq-premium-item" style="opacity: 1 !important; visibility: visible !important; background: white !important;">
                            <h3 class="accordion-header" id="<?php echo esc_attr($faq_id); ?>-header" style="opacity: 1 !important; visibility: visible !important;">
                                <button class="accordion-button faq-premium-button<?php echo !$faq['expanded'] ? ' collapsed' : ''; ?>" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#<?php echo esc_attr($faq_id); ?>" 
                                        aria-expanded="<?php echo $faq['expanded'] ? 'true' : 'false'; ?>" 
                                        aria-controls="<?php echo esc_attr($faq_id); ?>"
                                        style="opacity: 1 !important; visibility: visible !important; color: #2d3748 !important; background: transparent !important;">
                                    <div class="faq-question-wrapper" style="opacity: 1 !important; visibility: visible !important;">
                                        <div class="faq-icon" style="opacity: 1 !important; visibility: visible !important; color: #0d6efd !important;">
                                            <i class="bi bi-plus-circle"></i>
                                        </div>
                                        <span class="faq-question-text" style="opacity: 1 !important; visibility: visible !important; color: #2d3748 !important;"><?php echo esc_html($faq['question']); ?></span>
                                    </div>
                                </button>
                            </h3>
                            
                            <div id="<?php echo esc_attr($faq_id); ?>" 
                                 class="accordion-collapse collapse<?php echo $faq['expanded'] ? ' show' : ''; ?>" 
                                 aria-labelledby="<?php echo esc_attr($faq_id); ?>-header" 
                                 data-bs-parent="#faq"
                                 style="opacity: 1 !important; visibility: visible !important;">
                                <div class="accordion-body faq-premium-body" style="opacity: 1 !important; visibility: visible !important; background: rgba(13, 110, 253, 0.02) !important;">
                                    <div class="faq-answer-content" style="opacity: 1 !important; visibility: visible !important; background: white !important;">
                                        <p style="opacity: 1 !important; visibility: visible !important; color: #4a5568 !important;"><?php echo esc_html($faq['answer']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4" style="opacity: 1 !important; visibility: visible !important;">
            <p class="text-muted mb-3" style="opacity: 1 !important; visibility: visible !important; color: #6c757d !important;">
                <?php esc_html_e('Não encontrou sua resposta?', 'wp-resgate'); ?>
            </p>
            
            <?php 
            $whatsapp = get_theme_mod('wp_resgate_whatsapp');
            if ($whatsapp) : ?>
                <a href="https://wa.me/<?php echo esc_attr($whatsapp); ?>" 
                   class="btn btn-success me-3 whatsapp-btn" 
                   target="_blank" 
                   rel="noopener"
                   style="opacity: 1 !important; visibility: visible !important;">
                    <i class="bi bi-whatsapp me-1"></i> 
                    <?php esc_html_e('Fale no WhatsApp', 'wp-resgate'); ?>
                </a>
            <?php endif; ?>
            
            <a href="#diagnostico" class="btn btn-primary" style="opacity: 1 !important; visibility: visible !important;">
                <i class="bi bi-clipboard2-pulse me-1"></i> 
                <?php esc_html_e('Solicitar diagnóstico', 'wp-resgate'); ?>
            </a>
        </div>
    </div>
</section>