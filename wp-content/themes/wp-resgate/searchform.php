<?php
/**
 * Template: Search Form
 *
 * @package WP_Resgate
 */
?>

<form role="search" method="get" class="search-form d-flex" action="<?php echo esc_url(home_url('/')); ?>">
    <div class="input-group">
        <input type="search" 
               class="form-control" 
               placeholder="<?php esc_attr_e('Buscar...', 'wp-resgate'); ?>" 
               value="<?php echo get_search_query(); ?>" 
               name="s"
               aria-label="<?php esc_attr_e('Buscar no site', 'wp-resgate'); ?>" />
        <button class="btn btn-outline-primary" type="submit" aria-label="<?php esc_attr_e('Executar busca', 'wp-resgate'); ?>">
            <i class="bi bi-search"></i>
        </button>
    </div>
</form>