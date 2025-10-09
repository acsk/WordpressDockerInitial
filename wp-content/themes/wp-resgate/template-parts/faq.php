<?php
/**
 * Template Part: FAQ
 *
 * @package WP_Resgate
 */
?>

<!-- FAQ PREMIUM -->
<section class="py-5 faq-premium-section">
    <div class="container">
        <!-- Header Premium -->
        <div class="text-center mb-5">
            <div class="badge bg-primary-soft text-primary rounded-pill px-4 py-2 mb-3">
                <i class="bi bi-patch-question me-2"></i>
                <?php esc_html_e('Esclarecimentos', 'wp-resgate'); ?>
            </div>
            <h2 class="display-6 fw-bold text-dark mb-3">
                <?php esc_html_e('Perguntas Frequentes', 'wp-resgate'); ?>
            </h2>
            <p class="lead text-muted mb-0">
                <?php esc_html_e('Tire suas dúvidas sobre nossos serviços e processos', 'wp-resgate'); ?>
            </p>
        </div>
        
        <!-- Accordion Premium -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="accordion faq-premium-accordion" id="faq">
                    <?php 
                    $faqs = get_faqs();
                    
                    foreach ($faqs as $index => $faq) : 
                        $faq_id = 'faq-' . ($index + 1);
                        ?>
                        <div class="accordion-item faq-premium-item">
                            <h3 class="accordion-header" id="<?php echo esc_attr($faq_id); ?>-header">
                                <button class="accordion-button faq-premium-button<?php echo !$faq['expanded'] ? ' collapsed' : ''; ?>" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#<?php echo esc_attr($faq_id); ?>" 
                                        aria-expanded="<?php echo $faq['expanded'] ? 'true' : 'false'; ?>" 
                                        aria-controls="<?php echo esc_attr($faq_id); ?>">
                                    <div class="faq-question-wrapper">
                                        <div class="faq-icon">
                                            <i class="bi bi-plus-circle"></i>
                                        </div>
                                        <span class="faq-question-text"><?php echo esc_html($faq['question']); ?></span>
                                    </div>
                                </button>
                            </h3>
                            
                            <div id="<?php echo esc_attr($faq_id); ?>" 
                                 class="accordion-collapse collapse<?php echo $faq['expanded'] ? ' show' : ''; ?>" 
                                 aria-labelledby="<?php echo esc_attr($faq_id); ?>-header" 
                                 data-bs-parent="#faq">
                                <div class="accordion-body faq-premium-body">
                                    <div class="faq-answer-content">
                                        <p><?php echo esc_html($faq['answer']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <p class="text-muted mb-3">
                <?php esc_html_e('Não encontrou sua resposta?', 'wp-resgate'); ?>
            </p>
            
            <?php 
            $whatsapp = get_theme_mod('wp_resgate_whatsapp');
            if ($whatsapp) : ?>
                <a href="https://wa.me/<?php echo esc_attr($whatsapp); ?>" 
                   class="btn btn-success me-3 whatsapp-btn" 
                   target="_blank" 
                   rel="noopener">
                    <i class="bi bi-whatsapp me-1"></i> 
                    <?php esc_html_e('Fale no WhatsApp', 'wp-resgate'); ?>
                </a>
            <?php endif; ?>
            
            <a href="#diagnostico" class="btn btn-primary">
                <i class="bi bi-clipboard2-pulse me-1"></i> 
                <?php esc_html_e('Solicitar diagnóstico', 'wp-resgate'); ?>
            </a>
        </div>
    </div>
</section>