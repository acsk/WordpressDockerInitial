<?php
/**
 * Template Part: Depoimentos/Testimonials
 *
 * @package WP_Resgate
 */
?>

<!-- DEPOIMENTOS / PROVA SOCIAL -->
<section id="depoimentos" class="testimonials-modern-section py-5 position-relative overflow-hidden">
    <!-- Background decorativo -->
    <div class="testimonials-bg-wrapper">
        <div class="testimonials-bg-circle testimonials-bg-1"></div>
        <div class="testimonials-bg-circle testimonials-bg-2"></div>
        <div class="testimonials-bg-circle testimonials-bg-3"></div>
    </div>
    
    <div class="container position-relative">
        <div class="row mb-5 text-center">
            <div class="col-12">
                <div class="testimonials-header-wrapper">
                    <span class="badge bg-gradient-primary text-white rounded-pill mb-3 px-4 py-2 testimonials-badge">
                        <i class="bi bi-heart-fill me-2"></i>
                        <?php esc_html_e('Histórias reais', 'wp-resgate'); ?>
                    </span>
                    <h2 class="display-5 fw-bold text-dark mb-4 testimonials-main-title">
                        <?php esc_html_e('Clientes', 'wp-resgate'); ?>
                        <span class="text-primary position-relative testimonials-highlight">
                            <?php esc_html_e('satisfeitos', 'wp-resgate'); ?>
                            <div class="testimonials-underline"></div>
                        </span>
                    </h2>
                    <p class="lead text-muted mb-0 mx-auto testimonials-subtitle" style="max-width: 600px;">
                        <?php esc_html_e('Conheça as histórias de sucesso de quem confiou no WP Resgate para resolver emergências WordPress', 'wp-resgate'); ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <?php 
            // Query para buscar depoimentos (custom post type)
            // Priorizar depoimentos em destaque
            $testimonials_query = new WP_Query([
                'post_type' => 'testimonial',
                'posts_per_page' => 3,
                'post_status' => 'publish',
                'meta_query' => [
                    'relation' => 'OR',
                    [
                        'key' => '_featured',
                        'value' => '1',
                        'compare' => '='
                    ],
                    [
                        'key' => '_featured',
                        'compare' => 'NOT EXISTS'
                    ]
                ],
                'orderby' => [
                    'meta_value' => 'DESC',
                    'date' => 'DESC'
                ],
                'meta_key' => '_featured'
            ]);
            
            if ($testimonials_query->have_posts()) :
                while ($testimonials_query->have_posts()) : $testimonials_query->the_post();
                    $client_name = get_post_meta(get_the_ID(), '_client_name', true);
                    $client_company = get_post_meta(get_the_ID(), '_client_company', true);
                    $client_website = get_post_meta(get_the_ID(), '_client_website', true);
                    $featured_image = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="testimonial-card-modern h-100">
                            <!-- Quote icon decorativo -->
                            <div class="testimonial-quote-icon">
                                <i class="bi bi-quote"></i>
                            </div>
                            
                            <!-- Rating -->
                            <?php 
                            $rating = get_post_meta(get_the_ID(), '_rating', true) ?: 5;
                            ?>
                            <div class="testimonial-rating mb-3">
                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                    <i class="bi bi-star<?php echo $i <= $rating ? '-fill' : ''; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            
                            <!-- Conteúdo -->
                            <div class="testimonial-content mb-4">
                                <p class="testimonial-text"><?php echo wp_kses_post(get_the_content()); ?></p>
                            </div>
                            
                            <!-- Cliente info -->
                            <div class="testimonial-client">
                                <div class="d-flex align-items-center">
                                    <?php if ($featured_image) : ?>
                                        <div class="testimonial-avatar">
                                            <img src="<?php echo esc_url($featured_image); ?>" 
                                                 alt="<?php echo esc_attr($client_name); ?>" />
                                        </div>
                                    <?php else : ?>
                                        <div class="testimonial-avatar testimonial-avatar-letter">
                                            <span><?php echo esc_html(substr($client_name, 0, 1)); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="ms-3">
                                        <div class="testimonial-client-name">
                                            <?php if ($client_website) : ?>
                                                <a href="<?php echo esc_url($client_website); ?>" target="_blank" rel="noopener">
                                                    <?php echo esc_html($client_name); ?>
                                                </a>
                                            <?php else : ?>
                                                <?php echo esc_html($client_name); ?>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($client_company) : ?>
                                            <div class="testimonial-client-company">
                                                <?php echo esc_html($client_company); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                endwhile;
                wp_reset_postdata();
            else :
                // Fallback com depoimentos estáticos modernos
                $default_testimonials = [
                    [
                        'name' => __('Maria Silva', 'wp-resgate'),
                        'company' => __('E-commerce de Moda', 'wp-resgate'),
                        'content' => __('Estava desesperada com meu e-commerce invadido por malware. A equipe do WP Resgate não só limpou tudo em menos de 24h, como ainda otimizou a performance do site. Agora minhas vendas aumentaram 40%! Atendimento excepcional e muito transparente.', 'wp-resgate'),
                        'rating' => 5,
                        'initial' => 'M'
                    ],
                    [
                        'name' => __('João Santos', 'wp-resgate'),
                        'company' => __('Cursos Online', 'wp-resgate'),
                        'content' => __('Precisava migrar meu site educacional para um servidor melhor e estava com medo de perder o posicionamento no Google. A migração foi perfeita, sem perder uma única posição no SEO. Inclusive, o site ficou mais rápido! Recomendo demais.', 'wp-resgate'),
                        'rating' => 5,
                        'initial' => 'J'
                    ],
                    [
                        'name' => __('Ana Costa', 'wp-resgate'),
                        'company' => __('Consultoria Empresarial', 'wp-resgate'),
                        'content' => __('Meu WordPress vivia com erros 500 e eu não sabia o que fazer. Encontrei o WP Resgate e foi a melhor decisão! Resolveram todos os conflitos entre plugins, otimizaram o banco de dados e ainda me ensinaram como prevenir problemas futuros.', 'wp-resgate'),
                        'rating' => 5,
                        'initial' => 'A'
                    ]
                ];
                
                foreach ($default_testimonials as $index => $testimonial) : ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="testimonial-card-modern h-100">
                            <!-- Quote icon decorativo -->
                            <div class="testimonial-quote-icon">
                                <i class="bi bi-quote"></i>
                            </div>
                            
                            <!-- Rating -->
                            <div class="testimonial-rating mb-3">
                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                    <i class="bi bi-star<?php echo $i <= $testimonial['rating'] ? '-fill' : ''; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            
                            <!-- Conteúdo -->
                            <div class="testimonial-content mb-4">
                                <p class="testimonial-text"><?php echo esc_html($testimonial['content']); ?></p>
                            </div>
                            
                            <!-- Cliente info -->
                            <div class="testimonial-client">
                                <div class="d-flex align-items-center">
                                    <div class="testimonial-avatar testimonial-avatar-letter">
                                        <span><?php echo esc_html($testimonial['initial']); ?></span>
                                    </div>
                                    
                                    <div class="ms-3">
                                        <div class="testimonial-client-name">
                                            <?php echo esc_html($testimonial['name']); ?>
                                        </div>
                                        <div class="testimonial-client-company">
                                            <?php echo esc_html($testimonial['company']); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach;
            endif; ?>
        </div>
    </div>
</section>
