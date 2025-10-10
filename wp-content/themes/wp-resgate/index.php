<?php
/**
 * Template principal
 *
 * @package WP_Resgate
 */

get_header(); ?>

<!-- HERO PREMIUM -->
<?php 
// Configurações do Hero
$hero_badge = get_theme_mod('wp_resgate_hero_badge', 'Suporte humano • Resposta rápida');
$hero_title = get_theme_mod('wp_resgate_hero_title', 'Recupere seu site <span class="text-warning">WordPress</span> agora');
$hero_subtitle = get_theme_mod('wp_resgate_hero_subtitle', 'Correção de bugs, <strong>remoção de vírus</strong> e <strong>migração segura</strong> — com preservação de SEO e checklist final. Diagnóstico <u>gratuito</u> e transparente.');
$hero_btn1_text = get_theme_mod('wp_resgate_hero_btn1_text', 'Fazer diagnóstico');
$hero_btn2_text = get_theme_mod('wp_resgate_hero_btn2_text', 'Ver soluções');
?>
<header class="hero py-5 py-lg-6">
    <div class="container position-relative">
        <div class="row align-items-center g-5 py-4">
            <div class="col-12 col-lg-6">
                <div class="hero-content" data-aos="fade-right">
                    <span class="hero-badge badge bg-white rounded-pill mb-4 px-4 py-2">
                        <i class="bi bi-shield-check text-primary me-2"></i>
                        <span class="text-primary fw-bold"><?php echo esc_html($hero_badge); ?></span>
                    </span>

                    <h1 class="hero-title display-4 fw-bold lh-1 mb-4 text-white">
                        <?php echo wp_kses_post($hero_title); ?>
                    </h1>

                    <p class="hero-subtitle lead mb-5 text-white-75">
                        <?php echo wp_kses_post($hero_subtitle); ?>
                    </p>

                    <div class="hero-actions d-flex gap-3 flex-wrap mb-5">
                        <a href="#diagnostico" class="btn btn-light btn-lg text-primary shadow-lg px-4 py-3">
                            <i class="bi bi-clipboard2-pulse me-2"></i>
                            <?php echo esc_html($hero_btn1_text); ?>
                        </a>
                        <a href="#servicos" class="btn btn-outline-light btn-lg px-4 py-3">
                            <i class="bi bi-lightning-charge me-2"></i>
                            <?php echo esc_html($hero_btn2_text); ?>
                        </a>
                    </div>

                    <div class="hero-features">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="feature-item d-flex align-items-center">
                                    <div class="feature-icon me-3">
                                        <div class="icon-circle bg-transparent border border-white text-white">
                                            <i class="bi bi-shield-check"></i>
                                        </div>
                                    </div>
                                    <span
                                        class="small fw-semibold text-white"><?php esc_html_e('Backup e hardening inclusos', 'wp-resgate'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="feature-item d-flex align-items-center">
                                    <div class="feature-icon me-3">
                                        <div class="icon-circle bg-transparent border border-white text-white">
                                            <i class="bi bi-tools"></i>
                                        </div>
                                    </div>
                                    <span
                                        class="small fw-semibold text-white"><?php esc_html_e('Plano de manutenção opcional', 'wp-resgate'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="hero-card" data-aos="fade-left">
                    <div class="card border-0 shadow-lg bg-white bg-opacity-95">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle bg-primary text-white me-3" style="width: 50px; height: 50px;">
                                    <i class="bi bi-laptop" style="font-size: 1.3rem;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold text-dark">
                                        <?php esc_html_e('Prévia de restauração', 'wp-resgate'); ?></h5>
                                    <small
                                        class="text-muted"><?php esc_html_e('Exemplo de recuperação', 'wp-resgate'); ?></small>
                                </div>
                            </div>

                            <div class="preview-image">
                                <?php 
                                $hero_image = get_theme_mod('wp_resgate_hero_image');
                                if ($hero_image) : ?>
                                <img class="img-fluid rounded-3 shadow-sm" src="<?php echo esc_url($hero_image); ?>"
                                    alt="<?php esc_attr_e('Mockup WordPress', 'wp-resgate'); ?>" />
                                <?php else : ?>
                                <img class="img-fluid rounded-3 shadow-sm"
                                    src="https://placehold.co/900x520/f8f9fa/6c757d?text=Painel+WordPress+\n(Mockup+para+Protótipo)"
                                    alt="<?php esc_attr_e('Mockup WordPress', 'wp-resgate'); ?>" />
                                <?php endif; ?>
                            </div>



                            <div class="text-muted small mt-3 text-center">
                                <i class="bi bi-info-circle me-1"></i>
                                <?php esc_html_e('Imagem ilustrativa para prototipação. Sem dados reais.', 'wp-resgate'); ?>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- LOGOS CLEAN PREMIUM -->
<section class="py-5 logos-clean-section">
    <div class="container">
        <!-- Header Clean -->
        <div class="text-center mb-5">
            <div class="logos-header-badge">
                <span class="badge bg-light text-muted rounded-pill px-3 py-2">
                    <i class="bi bi-patch-check me-2"></i>
                    <?php esc_html_e('Confiança Comprovada', 'wp-resgate'); ?>
                </span>
            </div>
            <h3 class="fw-bold text-dark mt-3 mb-2">
                <?php esc_html_e('Empresas que Confiam', 'wp-resgate'); ?>
            </h3>
            <p class="text-muted mb-0">
                <?php esc_html_e('Profissionais WordPress escolhidos por marcas de sucesso', 'wp-resgate'); ?>
            </p>
        </div>

        <!-- Logos Grid Clean -->
        <div class="logos-clean-grid">
            <div class="row justify-content-center align-items-center g-4">
                <?php 
                $client_logos = wp_resgate_get_client_logos();
                
                foreach ($client_logos as $index => $logo) : ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="logo-clean-item" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                        <?php if (!empty($logo['website'])) : ?>
                        <a href="<?php echo esc_url($logo['website']); ?>" target="_blank" rel="noopener"
                            class="logo-clean-link" title="<?php echo esc_attr($logo['title']); ?>">
                            <div class="logo-clean-container">
                                <img class="logo-clean-image" src="<?php echo esc_url($logo['image']); ?>"
                                    alt="<?php echo esc_attr($logo['alt_text']); ?>" loading="lazy" />
                                <div class="logo-clean-overlay">
                                    <i class="bi bi-arrow-up-right-circle"></i>
                                </div>
                            </div>
                            <?php if (!empty($logo['description'])) : ?>
                            <div class="logo-clean-description">
                                <small class="text-muted"><?php echo esc_html($logo['description']); ?></small>
                            </div>
                            <?php endif; ?>
                        </a>
                        <?php else : ?>
                        <div class="logo-clean-container">
                            <img class="logo-clean-image" src="<?php echo esc_url($logo['image']); ?>"
                                alt="<?php echo esc_attr($logo['alt_text']); ?>" loading="lazy" />
                        </div>
                        <?php if (!empty($logo['description'])) : ?>
                        <div class="logo-clean-description">
                            <small class="text-muted"><?php echo esc_html($logo['description']); ?></small>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</section>

<!-- SERVIÇOS -->
<section id="servicos" class="py-5 services-premium-section position-relative overflow-hidden">
    <!-- Background decorativo -->
    <div class="services-bg-decoration"></div>

    <div class="container position-relative">
        <div class="row mb-5 text-center">
            <div class="col-12">
                <div class="services-header-wrapper">
                    <span class="badge bg-gradient-primary text-white rounded-pill mb-3 px-4 py-2 services-badge">
                        <i class="bi bi-lightning-charge me-2"></i>
                        <?php esc_html_e('Soluções de emergência', 'wp-resgate'); ?>
                    </span>
                    <h2 class="display-5 fw-bold text-dark mb-4 services-main-title">
                        <?php esc_html_e('Transformamos', 'wp-resgate'); ?>
                        <span class="text-primary position-relative services-highlight">
                            <?php esc_html_e('crise', 'wp-resgate'); ?>
                            <div class="services-underline"></div>
                        </span>
                        <?php esc_html_e('em solução', 'wp-resgate'); ?>
                    </h2>
                    <p class="lead text-muted mb-0 mx-auto services-subtitle" style="max-width: 600px;">
                        <?php esc_html_e('Para você voltar a vender e comunicar sem dor de cabeça. Especialistas em emergências WordPress com atendimento 24h.', 'wp-resgate'); ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <?php 
            $services = wp_resgate_get_services();
            
            foreach ($services as $index => $service) : 
                $gradient_colors = [
                    'bg-gradient-danger',
                    'bg-gradient-success', 
                    'bg-gradient-warning',
                    'bg-gradient-info'
                ];
                $gradient_class = $gradient_colors[$index % 4];
                ?>
            <div class="col-md-6 col-lg-3">
                <div class="h-100 service-card-premium position-relative" data-aos="fade-up"
                    data-aos-delay="<?php echo $index * 150; ?>">
                    <!-- Card principal -->
                    <div class="card border-0 shadow-lg h-100 service-main-card">
                        <!-- Header com ícone e gradiente -->
                        <div
                            class="service-header <?php echo $gradient_class; ?> text-white p-4 position-relative overflow-hidden">
                            <div class="service-header-bg"></div>
                            <div class="position-relative z-1">
                                <div class="service-icon-premium mb-3">
                                    <i class="bi <?php echo esc_attr($service['icon']); ?>"></i>
                                </div>
                                <h5 class="service-title-premium fw-bold mb-2">
                                    <?php echo esc_html($service['title']); ?></h5>
                            </div>
                        </div>

                        <!-- Conteúdo do card -->
                        <div class="card-body p-4 service-body-premium">
                            <p class="service-description-premium text-muted mb-4 lh-base">
                                <?php echo esc_html($service['description']); ?></p>
                        </div>
                    </div>

                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Botões de ação centralizados -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="services-cta-wrapper text-center">
                    <h4 class="fw-bold text-dark mb-4"><?php esc_html_e('Precisa de ajuda agora?', 'wp-resgate'); ?>
                    </h4>
                    <div
                        class="services-cta-buttons d-flex flex-column flex-md-row gap-3 justify-content-center align-items-center">
                        <?php 
                        $whatsapp = get_theme_mod('wp_resgate_whatsapp');
                        if ($whatsapp) : ?>
                        <a href="https://wa.me/<?php echo esc_attr($whatsapp); ?>"
                            class="btn btn-success btn-lg services-whatsapp-btn px-4 py-3" target="_blank"
                            rel="noopener noreferrer" data-track="services_whatsapp_click">
                            <i class="bi bi-whatsapp me-2"></i>
                            <?php esc_html_e('Falar no WhatsApp', 'wp-resgate'); ?>
                            <small
                                class="d-block mt-1 opacity-75"><?php esc_html_e('Resposta em minutos', 'wp-resgate'); ?></small>
                        </a>
                        <?php endif; ?>

                        <a href="#diagnostico" class="btn btn-primary btn-lg services-form-btn px-4 py-3"
                            data-track="services_form_click">
                            <i class="bi bi-clipboard2-pulse me-2"></i>
                            <?php esc_html_e('Diagnóstico Gratuito', 'wp-resgate'); ?>
                            <small
                                class="d-block mt-1 opacity-75"><?php esc_html_e('Análise completa', 'wp-resgate'); ?></small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- COMO FUNCIONA -->
<section id="como-funciona" class="how-it-works-premium-section py-5 position-relative overflow-hidden">
    <!-- Background decorativo dinâmico -->
    <div class="how-it-works-bg-wrapper">
        <div class="how-it-works-bg-shape shape-1"></div>
        <div class="how-it-works-bg-shape shape-2"></div>
        <div class="how-it-works-bg-shape shape-3"></div>
        <div class="how-it-works-particles">
            <div class="particle particle-1"></div>
            <div class="particle particle-2"></div>
            <div class="particle particle-3"></div>
            <div class="particle particle-4"></div>
            <div class="particle particle-5"></div>
        </div>
    </div>

    <div class="container position-relative">
        <div class="row mb-5 text-center">
            <div class="col-12">
                <div class="how-it-works-header-wrapper">
                    <span class="badge bg-gradient-light rounded-pill mb-3 px-4 py-2 how-it-works-badge">
                        <i class="bi bi-shield-check me-2"></i>
                        <?php esc_html_e('Método comprovado', 'wp-resgate'); ?>
                    </span>
                    <h2 class="display-4 fw-bold text-white mb-4 how-it-works-main-title">
                        <?php esc_html_e('Como', 'wp-resgate'); ?>
                        <span class="text-warning position-relative how-it-works-highlight">
                            <?php esc_html_e('resgatamos', 'wp-resgate'); ?>
                            <div class="how-it-works-underline"></div>
                        </span>
                        <br><?php esc_html_e('seu WordPress', 'wp-resgate'); ?>
                    </h2>
                    <p class="lead text-white-75 mb-0 mx-auto how-it-works-subtitle" style="max-width: 700px;">
                        <?php esc_html_e('Processo transparente e testado em centenas de casos. Cada etapa é documentada e você acompanha tudo em tempo real.', 'wp-resgate'); ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="row g-4 align-items-stretch">
            <?php 
            $steps = wp_resgate_get_process_steps();
            
            foreach ($steps as $index => $step) : 
                $step_colors = [
                    'primary', 'success', 'warning', 'info', 'danger', 'secondary'
                ];
                $color = $step_colors[$index % count($step_colors)];
                ?>
            <div class="col-lg-4 col-md-6">
                <div class="process-step-premium h-100" data-aos="fade-up" data-aos-delay="<?php echo $index * 200; ?>">
                    <!-- Número do passo -->
                   

                    <!-- Card principal -->
                    <div class="process-step-card">
                        <!-- Header com ícone -->
                        <div class="process-step-header">
                            <div class="process-step-icon bg-<?php echo $color; ?>">
                                <?php if (!empty($step['icon'])) : ?>
                                <i class="bi <?php echo esc_attr($step['icon']); ?>"></i>
                                <?php else : ?>
                                <i class="bi bi-check-circle"></i>
                                <?php endif; ?>
                            </div>
                            <div class="process-step-connector"></div>
                        </div>

                        <!-- Conteúdo -->
                        <div class="process-step-content">
                            <h4 class="process-step-title"><?php echo esc_html($step['title']); ?></h4>

                            <div class="process-step-description">
                                <?php 
                                    // Usar post_content se disponível, senão usar description
                                    $content = !empty($step['content']) ? $step['content'] : $step['description'];
                                    
                                    // Processar o conteúdo para permitir formatação básica
                                    $content = wpautop($content); // Converte quebras de linha em parágrafos
                                    $content = wp_kses_post($content); // Permite HTML seguro
                                    
                                    echo $content;
                                    ?>
                            </div>

                           
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- CTA Premium -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="how-it-works-cta-wrapper text-center">
                    <div class="how-it-works-cta-card">
                        <div class="how-it-works-cta-icon">
                            <i class="bi bi-rocket-takeoff"></i>
                        </div>
                        <h3 class="text-white fw-bold mb-3">
                            <?php esc_html_e('Pronto para resgatar seu WordPress?', 'wp-resgate'); ?>
                        </h3>
                        <p class="text-white-75 mb-4">
                            <?php esc_html_e('Nosso time está online agora e pode começar o resgate imediatamente', 'wp-resgate'); ?>
                        </p>
                        <div
                            class="how-it-works-cta-buttons d-flex flex-column flex-md-row gap-3 justify-content-center">
                            <a href="#diagnostico"
                                class="btn btn-primary btn-lg px-4 py-3 how-it-works-btn-primary form-btn">
                                <i class="bi bi-play-circle me-2"></i>
                                <?php esc_html_e('Iniciar Resgate Agora', 'wp-resgate'); ?>
                                <small
                                    class="d-block mt-1 opacity-75"><?php esc_html_e('Diagnóstico em 5 minutos', 'wp-resgate'); ?></small>
                            </a>

                            <?php 
                            $whatsapp = get_theme_mod('wp_resgate_whatsapp');
                            if ($whatsapp) : ?>
                            <a href="https://wa.me/<?php echo esc_attr($whatsapp); ?>"
                                class="btn btn-success btn-lg px-4 py-3 how-it-works-btn-secondary whatsapp-btn"
                                target="_blank" rel="noopener noreferrer">
                                <i class="bi bi-whatsapp me-2"></i>
                                <?php esc_html_e('Falar com Especialista', 'wp-resgate'); ?>
                                <small
                                    class="d-block mt-1 opacity-75"><?php esc_html_e('Resposta imediata', 'wp-resgate'); ?></small>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_template_part('template-parts/testimonials'); ?>
<?php get_template_part('template-parts/guarantee'); ?>
<?php get_template_part('template-parts/faq'); ?>
<?php get_template_part('template-parts/contact-form'); ?>

<?php get_footer(); ?>