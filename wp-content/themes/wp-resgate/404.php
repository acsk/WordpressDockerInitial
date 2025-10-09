<?php
/**
 * Template: 404 Error Page
 *
 * @package WP_Resgate
 */

get_header(); ?>

<section class="py-5">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="mb-4">
                    <h1 class="display-1 fw-bold text-primary">404</h1>
                    <h2 class="h3 mb-3"><?php esc_html_e('Página não encontrada', 'wp-resgate'); ?></h2>
                    <p class="text-muted mb-4">
                        <?php esc_html_e('A página que você está procurando não existe ou foi movida.', 'wp-resgate'); ?>
                    </p>
                </div>
                
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                        <i class="bi bi-house me-1"></i>
                        <?php esc_html_e('Ir para Home', 'wp-resgate'); ?>
                    </a>
                    
                    <a href="#diagnostico" class="btn btn-outline-primary">
                        <i class="bi bi-clipboard2-pulse me-1"></i>
                        <?php esc_html_e('Solicitar Diagnóstico', 'wp-resgate'); ?>
                    </a>
                </div>
                
                <div class="mt-4">
                    <p class="small text-muted">
                        <?php esc_html_e('Ou use a busca abaixo:', 'wp-resgate'); ?>
                    </p>
                    <?php get_search_form(); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>