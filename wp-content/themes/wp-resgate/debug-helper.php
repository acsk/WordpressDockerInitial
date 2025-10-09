<?php
/**
 * Debug helper para WP Resgate Google Sheets Integration
 * Adicione este código temporariamente ao functions.php para habilitar logs de debug
 */

// Habilitar debug logging (temporário)
if (!defined('WP_DEBUG')) {
    define('WP_DEBUG', true);
}
if (!defined('WP_DEBUG_LOG')) {
    define('WP_DEBUG_LOG', true);
}
if (!defined('WP_DEBUG_DISPLAY')) {
    define('WP_DEBUG_DISPLAY', false);
}

// Função para visualizar logs facilmente
function wp_resgate_show_debug_logs() {
    if (current_user_can('administrator')) {
        $log_file = WP_CONTENT_DIR . '/debug.log';
        if (file_exists($log_file)) {
            $logs = file_get_contents($log_file);
            $wp_resgate_logs = array_filter(
                explode("\n", $logs),
                function($line) {
                    return strpos($line, 'WP Resgate') !== false;
                }
            );
            
            if (!empty($wp_resgate_logs)) {
                echo '<div style="background: #f1f1f1; padding: 10px; margin: 10px 0; border-left: 4px solid #0073aa;">';
                echo '<h3>WP Resgate Debug Logs:</h3>';
                echo '<pre style="font-size: 12px; max-height: 400px; overflow-y: scroll;">';
                echo esc_html(implode("\n", array_slice($wp_resgate_logs, -20))); // Últimas 20 linhas
                echo '</pre>';
                echo '</div>';
            }
        }
    }
}

// Adicionar ao admin
add_action('admin_notices', 'wp_resgate_show_debug_logs');

// Função para limpar logs
function wp_resgate_clear_logs() {
    if (isset($_GET['clear_wp_resgate_logs']) && current_user_can('administrator')) {
        $log_file = WP_CONTENT_DIR . '/debug.log';
        if (file_exists($log_file)) {
            file_put_contents($log_file, '');
            wp_redirect(admin_url());
            exit;
        }
    }
}
add_action('init', 'wp_resgate_clear_logs');

// Adicionar botão para limpar logs
function wp_resgate_add_clear_logs_button() {
    if (current_user_can('administrator')) {
        echo '<p><a href="' . admin_url('?clear_wp_resgate_logs=1') . '" class="button">Limpar Logs WP Resgate</a></p>';
    }
}
add_action('admin_notices', 'wp_resgate_add_clear_logs_button');
?>