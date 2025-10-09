<?php
/**
 * Template Part: Garantia
 *
 * @package WP_Resgate
 */
?>

<!-- GARANTIA E MANUTENÇÃO PREMIUM -->
<section class="py-5 guarantee-premium-section">
    <div class="container">
        <!-- Header Section -->
        <div class="text-center mb-5">
            <div class="badge bg-info-soft text-info-dark rounded-pill px-4 py-2 mb-3">
                <i class="bi bi-shield-heart me-2"></i>
                <?php esc_html_e('Tranquilidade Total', 'wp-resgate'); ?>
            </div>
            <h2 class="display-6 fw-bold text-dark mb-3">
                <?php esc_html_e('Garantia & Cuidado Contínuo', 'wp-resgate'); ?>
            </h2>
            <p class="lead text-muted">
                <?php esc_html_e('Trabalhamos até você ficar satisfeito e oferecemos cuidado contínuo para seu WordPress', 'wp-resgate'); ?>
            </p>
        </div>
        
        <div class="row g-4 align-items-stretch">
            <!-- Garantia Card -->
            <div class="col-lg-6">
                <div class="guarantee-card h-100">
                    <div class="guarantee-header">
                        <div class="guarantee-icon">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-2 text-white">
                                <?php esc_html_e('Garantia de Satisfação', 'wp-resgate'); ?>
                            </h3>
                            <div class="guarantee-badge">
                                <?php esc_html_e('100% Sem Riscos', 'wp-resgate'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="guarantee-content">
                        <p class="text-white-75 mb-4">
                            <?php esc_html_e('Se o problema não estiver resolvido conforme o diagnóstico, continuamos trabalhando sem custo adicional até alinharmos a solução.', 'wp-resgate'); ?>
                        </p>
                        
                        <div class="guarantee-features">
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill text-white me-2"></i>
                                <span class="text-white"><?php esc_html_e('Trabalho até a satisfação total', 'wp-resgate'); ?></span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill text-white me-2"></i>
                                <span class="text-white"><?php esc_html_e('Sem custos adicionais', 'wp-resgate'); ?></span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill text-white me-2"></i>
                                <span class="text-white"><?php esc_html_e('Compromisso com resultados', 'wp-resgate'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Manutenção Card -->
            <div class="col-lg-6">
                <div class="maintenance-card h-100">
                    <div class="maintenance-header">
                        <div class="maintenance-icon">
                            <i class="bi bi-gear-wide-connected"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-2">
                                <?php esc_html_e('Planos de Manutenção', 'wp-resgate'); ?>
                            </h3>
                            <div class="maintenance-badge">
                                <?php esc_html_e('Opcional • Recomendado', 'wp-resgate'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="maintenance-content">
                        <p class="text-muted mb-4">
                            <?php esc_html_e('Previna novas ocorrências e mantenha o site sempre saudável e atualizado.', 'wp-resgate'); ?>
                        </p>
                        
                        <div class="maintenance-features">
                            <div class="feature-item">
                                <div class="feature-icon bg-info">
                                    <i class="bi bi-arrow-repeat"></i>
                                </div>
                                <div class="feature-content">
                                    <strong><?php esc_html_e('Updates Automáticos', 'wp-resgate'); ?></strong>
                                    <small class="text-muted d-block"><?php esc_html_e('WordPress, temas e plugins sempre atualizados', 'wp-resgate'); ?></small>
                                </div>
                            </div>
                            
                            <div class="feature-item">
                                <div class="feature-icon bg-success">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                </div>
                                <div class="feature-content">
                                    <strong><?php esc_html_e('Backups Inteligentes', 'wp-resgate'); ?></strong>
                                    <small class="text-muted d-block"><?php esc_html_e('Backup automático e restauração rápida', 'wp-resgate'); ?></small>
                                </div>
                            </div>
                            
                            <div class="feature-item">
                                <div class="feature-icon bg-warning">
                                    <i class="bi bi-shield-lock"></i>
                                </div>
                                <div class="feature-content">
                                    <strong><?php esc_html_e('Segurança Ativa', 'wp-resgate'); ?></strong>
                                    <small class="text-muted d-block"><?php esc_html_e('Monitoramento 24/7 e hardening contínuo', 'wp-resgate'); ?></small>
                                </div>
                            </div>
                        </div>
                        
                        <a href="#diagnostico" class="btn btn-info btn-lg w-100 maintenance-cta">
                            <i class="bi bi-chat-dots me-2"></i>
                            <?php esc_html_e('Conversar sobre manutenção', 'wp-resgate'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>