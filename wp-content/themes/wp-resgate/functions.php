<?php
/**
 * Theme Name: WP Resgate
 * Description: Template profissional para serviços de WordPress com Bootstrap 5 e melhores práticas de SEO, performance e acessibilidade.
 * Version: 1.0.0
 * Author: Sua Empresa
 * Text Domain: wp-resgate
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 8.0
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Tags: business, landing-page, bootstrap, responsive, seo-ready, translation-ready
 */

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Constantes do tema
define('WP_RESGATE_VERSION', '1.0.0');
define('WP_RESGATE_THEME_URL', get_template_directory_uri());
define('WP_RESGATE_THEME_PATH', get_template_directory());

/**
 * Configuração do tema
 */
function wp_resgate_setup() {
    // Suporte a tradução
    load_theme_textdomain('wp-resgate', get_template_directory() . '/languages');

    // Suporte a recursos do WordPress
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script'
    ]);
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('responsive-embeds');

    // Menus
    register_nav_menus([
        'primary' => __('Menu Principal', 'wp-resgate'),
        'footer' => __('Menu do Rodapé', 'wp-resgate'),
    ]);

    // Tamanhos de imagem personalizados
    add_image_size('wp-resgate-hero', 900, 520, true);
    add_image_size('wp-resgate-testimonial', 56, 56, true);
    add_image_size('wp-resgate-logo', 160, 60, false);
}
add_action('after_setup_theme', 'wp_resgate_setup');

/**
 * Enfileiramento de scripts e estilos
 */
function wp_resgate_scripts() {
    // Bootstrap CSS
    wp_enqueue_style(
        'bootstrap',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
        [],
        '5.3.3'
    );

    // Bootstrap Icons
    wp_enqueue_style(
        'bootstrap-icons',
        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css',
        [],
        '1.11.3'
    );

    // Estilo principal do tema
    wp_enqueue_style(
        'wp-resgate-style',
        get_stylesheet_uri(),
        ['bootstrap'],
        WP_RESGATE_VERSION
    );

    // CSS adicional para correção de scroll
    wp_enqueue_style(
        'wp-resgate-scroll-fix',
        WP_RESGATE_THEME_URL . '/assets/css/scroll-fix.css',
        ['wp-resgate-style'],
        WP_RESGATE_VERSION
    );

    // Bootstrap JS
    wp_enqueue_script(
        'bootstrap',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
        [],
        '5.3.3',
        true
    );

    // Script principal do tema
    wp_enqueue_script(
        'wp-resgate-script',
        WP_RESGATE_THEME_URL . '/assets/js/main.js',
        ['bootstrap'],
        WP_RESGATE_VERSION,
        true
    );

    // Script de diagnóstico (apenas em desenvolvimento)
    if (WP_DEBUG || (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false)) {
        wp_enqueue_script(
            'wp-resgate-diagnostics',
            WP_RESGATE_THEME_URL . '/assets/js/diagnostics.js',
            [],
            WP_RESGATE_VERSION,
            false // Carregar no head para capturar erros cedo
        );
    }

    if (wp_resgate_is_recaptcha_enabled()) {
        wp_enqueue_script('google-recaptcha');
    }

    // Localização para AJAX
    wp_localize_script('wp-resgate-script', 'wpResgate', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wp_resgate_nonce'),
        'strings' => [
            'sending' => __('Enviando...', 'wp-resgate'),
            'success' => __('Mensagem enviada com sucesso!', 'wp-resgate'),
            'error' => __('Erro ao enviar mensagem. Tente novamente.', 'wp-resgate'),
        ]
    ]);
}
add_action('wp_enqueue_scripts', 'wp_resgate_scripts');

/**
 * Desabilita scripts e estilos de emojis para reduzir requests.
 */
function wp_resgate_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'wp_resgate_disable_emojis');

/**
 * Recupera as chaves configuradas do reCAPTCHA.
 *
 * @return array{site_key:string,secret_key:string}
 */
function wp_resgate_get_recaptcha_keys() {
    return [
        'site_key' => trim((string) get_theme_mod('wp_resgate_recaptcha_site_key', '')),
        'secret_key' => trim((string) get_theme_mod('wp_resgate_recaptcha_secret_key', '')),
    ];
}

/**
 * Verifica se o reCAPTCHA está configurado.
 */
function wp_resgate_is_recaptcha_enabled() {
    $keys = wp_resgate_get_recaptcha_keys();

    return $keys['site_key'] !== '' && $keys['secret_key'] !== '';
}

/**
 * Determina se o ambiente atual é local/desenvolvimento.
 */
function wp_resgate_is_local_environment() {
    if (function_exists('wp_get_environment_type')) {
        $env = wp_get_environment_type();
        if (in_array($env, ['local', 'development'], true)) {
            return true;
        }
    }

    if (defined('WP_ENVIRONMENT_TYPE') && in_array(WP_ENVIRONMENT_TYPE, ['local', 'development'], true)) {
        return true;
    }

    $host = isset($_SERVER['HTTP_HOST']) ? wp_unslash($_SERVER['HTTP_HOST']) : '';
    if ($host && (false !== strpos($host, 'localhost') || '127.0.0.1' === $host)) {
        return true;
    }

    return false;
}

/**
 * Verifica se a validação do reCAPTCHA deve ser ignorada (ex.: ambiente local).
 */
function wp_resgate_should_skip_recaptcha() {
    $skip = wp_resgate_is_local_environment();

    /**
     * Permite sobrescrever a decisão de ignorar o reCAPTCHA.
     *
     * @param bool $skip
     */
    return apply_filters('wp_resgate_should_skip_recaptcha', $skip);
}

/**
 * Registra o script do reCAPTCHA para reutilização no front-end e tela de login.
 */
function wp_resgate_register_recaptcha_script() {
    if (!wp_resgate_is_recaptcha_enabled()) {
        return;
    }

    $locale = determine_locale();
    $locale = $locale ? str_replace('_', '-', $locale) : '';
    $script_url = 'https://www.google.com/recaptcha/api.js';

    if ($locale) {
        $script_url = add_query_arg(['hl' => $locale], $script_url);
    }

    wp_register_script('google-recaptcha', $script_url, [], null, true);
    wp_script_add_data('google-recaptcha', 'async', true);
    wp_script_add_data('google-recaptcha', 'defer', true);
}
add_action('init', 'wp_resgate_register_recaptcha_script');

/**
 * Enfileira o reCAPTCHA na tela de login.
 */
function wp_resgate_login_enqueue_recaptcha() {
    if (!wp_resgate_is_recaptcha_enabled()) {
        return;
    }

    wp_enqueue_script('google-recaptcha');
}
add_action('login_enqueue_scripts', 'wp_resgate_login_enqueue_recaptcha');

/**
 * Renderiza o widget do reCAPTCHA na tela de login.
 */
function wp_resgate_render_login_recaptcha() {
    if (!wp_resgate_is_recaptcha_enabled()) {
        return;
    }

    $keys = wp_resgate_get_recaptcha_keys();

    echo '<p class="login-recaptcha">';
    echo '<div class="g-recaptcha" data-sitekey="' . esc_attr($keys['site_key']) . '"></div>';
    echo '</p>';
}
add_action('login_form', 'wp_resgate_render_login_recaptcha');

/**
 * Valida o reCAPTCHA durante a autenticação de login do WordPress.
 *
 * @param WP_User|WP_Error|null $user
 * @param string $username
 * @param string $password
 * @return WP_User|WP_Error|null
 */
function wp_resgate_verify_login_recaptcha($user, $username, $password) {
    if (!wp_resgate_is_recaptcha_enabled()) {
        return $user;
    }

    if (wp_resgate_should_skip_recaptcha()) {
        return $user;
    }

    if (!isset($_POST['log'])) {
        return $user;
    }

    if (is_wp_error($user) && $user->get_error_codes()) {
        return $user;
    }

    $token = isset($_POST['g-recaptcha-response']) ? sanitize_text_field(wp_unslash($_POST['g-recaptcha-response'])) : '';

    if ($token === '') {
        return new WP_Error('recaptcha_missing', __('Confirme que você não é um robô para continuar.', 'wp-resgate'));
    }

    if (!wp_resgate_verify_recaptcha($token)) {
        return new WP_Error('recaptcha_failed', __('Falha na verificação do reCAPTCHA. Tente novamente.', 'wp-resgate'));
    }

    return $user;
}
add_filter('authenticate', 'wp_resgate_verify_login_recaptcha', 21, 3);

/**
 * Valida um token do reCAPTCHA junto à API do Google.
 */
function wp_resgate_verify_recaptcha($token) {
    if (!wp_resgate_is_recaptcha_enabled()) {
        return true;
    }

    if (wp_resgate_should_skip_recaptcha()) {
        return true;
    }

    $token = trim((string) $token);

    if ($token === '') {
        return false;
    }

    $keys = wp_resgate_get_recaptcha_keys();
    $remote_ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';

    $response = wp_remote_post(
        'https://www.google.com/recaptcha/api/siteverify',
        [
            'timeout' => 10,
            'body' => [
                'secret' => $keys['secret_key'],
                'response' => $token,
                'remoteip' => $remote_ip,
            ],
        ]
    );

    if (is_wp_error($response)) {
        error_log('WP Resgate reCAPTCHA verification failed: ' . $response->get_error_message());
        return false;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    return !empty($body['success']);
}

/**
 * Registro de áreas de widgets
 */
function wp_resgate_widgets_init() {
    register_sidebar([
        'name' => __('Sidebar Principal', 'wp-resgate'),
        'id' => 'sidebar-main',
        'description' => __('Área de widgets da sidebar principal', 'wp-resgate'),
        'before_widget' => '<div id="%1$s" class="widget %2$s mb-4">',
        'after_widget' => '</div>',
        'before_title' => '<h5 class="widget-title fw-bold mb-3">',
        'after_title' => '</h5>',
    ]);

    register_sidebar([
        'name' => __('Rodapé', 'wp-resgate'),
        'id' => 'footer',
        'description' => __('Área de widgets do rodapé', 'wp-resgate'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h6 class="widget-title fw-bold mb-3">',
        'after_title' => '</h6>',
    ]);
}
add_action('widgets_init', 'wp_resgate_widgets_init');

/**
 * Customizer - Opções do tema
 */
function wp_resgate_customizer($wp_customize) {
    // ====================================
    // PAINEL: WP RESGATE
    // ====================================
    $wp_customize->add_panel('wp_resgate_panel', [
        'title' => __('⚙️ WP Resgate - Configurações', 'wp-resgate'),
        'description' => __('Todas as configurações do tema WP Resgate', 'wp-resgate'),
        'priority' => 25,
    ]);

    // ====================================
    // SEÇÃO: CONFIGURAÇÕES GERAIS
    // ====================================
    $wp_customize->add_section('wp_resgate_general', [
        'title' => __('🏢 Configurações Gerais', 'wp-resgate'),
        'panel' => 'wp_resgate_panel',
        'priority' => 10,
    ]);

    // Logo do Site
    $wp_customize->add_setting('wp_resgate_logo', [
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'refresh',
    ]);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'wp_resgate_logo', [
        'label' => __('Logo do Site', 'wp-resgate'),
        'description' => __('Logo que aparece no header (recomendado: 200x60px)', 'wp-resgate'),
        'section' => 'wp_resgate_general',
        'settings' => 'wp_resgate_logo',
    ]));

    // Nome da Empresa
    $wp_customize->add_setting('wp_resgate_company_name', [
        'default' => get_bloginfo('name'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ]);
    $wp_customize->add_control('wp_resgate_company_name', [
        'label' => __('Nome da Empresa', 'wp-resgate'),
        'description' => __('Nome que aparece no site e documentos', 'wp-resgate'),
        'section' => 'wp_resgate_general',
        'type' => 'text',
    ]);

    // Slogan/Tagline
    $wp_customize->add_setting('wp_resgate_tagline', [
        'default' => get_bloginfo('description'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ]);
    $wp_customize->add_control('wp_resgate_tagline', [
        'label' => __('Slogan/Tagline', 'wp-resgate'),
        'description' => __('Descrição curta da empresa', 'wp-resgate'),
        'section' => 'wp_resgate_general',
        'type' => 'text',
    ]);

    // Número do WhatsApp
    $wp_customize->add_setting('wp_resgate_whatsapp', [
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ]);
    $wp_customize->add_control('wp_resgate_whatsapp', [
        'label' => __('📱 Número do WhatsApp', 'wp-resgate'),
        'description' => __('Formato: 5511999999999 (código do país + DDD + número)', 'wp-resgate'),
        'section' => 'wp_resgate_general',
        'type' => 'text',
    ]);

    // Email de Contato
    $wp_customize->add_setting('wp_resgate_email', [
        'default' => get_option('admin_email'),
        'sanitize_callback' => 'sanitize_email',
        'transport' => 'postMessage',
    ]);
    $wp_customize->add_control('wp_resgate_email', [
        'label' => __('📧 Email de Contato', 'wp-resgate'),
        'description' => __('Email principal para contato', 'wp-resgate'),
        'section' => 'wp_resgate_general',
        'type' => 'email',
    ]);

    // URL do Webhook Google Sheets
    $wp_customize->add_setting('wp_resgate_webhook_url', [
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'refresh',
    ]);
    $wp_customize->add_control('wp_resgate_webhook_url', [
        'label' => __('🔗 URL do Webhook Google Sheets', 'wp-resgate'),
        'description' => __('URL do webhook para integração com Google Sheets (opcional)', 'wp-resgate'),
        'section' => 'wp_resgate_general',
        'type' => 'url',
    ]);

    // ID da Planilha Google Sheets
    $wp_customize->add_setting('wp_resgate_sheet_id', [
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ]);
    $wp_customize->add_control('wp_resgate_sheet_id', [
        'label' => __('📊 ID da Planilha Google', 'wp-resgate'),
        'description' => __('ID da planilha Google Sheets para backup (opcional)', 'wp-resgate'),
        'section' => 'wp_resgate_general',
        'type' => 'text',
    ]);

    // Endereço
    $wp_customize->add_setting('wp_resgate_address', [
        'default' => '',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport' => 'postMessage',
    ]);
    $wp_customize->add_control('wp_resgate_address', [
        'label' => __('📍 Endereço', 'wp-resgate'),
        'description' => __('Endereço completo da empresa', 'wp-resgate'),
        'section' => 'wp_resgate_general',
        'type' => 'textarea',
    ]);

    // ====================================
    // SEÇÃO: HERO BANNER
    // ====================================
    $wp_customize->add_section('wp_resgate_hero', [
        'title' => __('🎯 Hero Banner', 'wp-resgate'),
        'description' => __('Configure o banner principal do site', 'wp-resgate'),
        'panel' => 'wp_resgate_panel',
        'priority' => 20,
    ]);



    // Hero: Badge
    $wp_customize->add_setting('wp_resgate_hero_badge', [
        'default' => 'Suporte humano • Resposta rápida',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ]);
    $wp_customize->add_control('wp_resgate_hero_badge', [
        'label' => __('🏷️ Badge do Hero', 'wp-resgate'),
        'description' => __('Texto que aparece no badge superior', 'wp-resgate'),
        'section' => 'wp_resgate_hero',
        'type' => 'text',
    ]);

    // Hero: Título Principal
    $wp_customize->add_setting('wp_resgate_hero_title', [
        'default' => 'Recupere seu site <span class="text-warning">WordPress</span> agora',
        'sanitize_callback' => 'wp_kses_post',
        'transport' => 'postMessage',
    ]);
    $wp_customize->add_control('wp_resgate_hero_title', [
        'label' => __('📝 Título Principal', 'wp-resgate'),
        'description' => __('Título principal do hero (aceita HTML básico)', 'wp-resgate'),
        'section' => 'wp_resgate_hero',
        'type' => 'textarea',
    ]);

    // Hero: Subtítulo
    $wp_customize->add_setting('wp_resgate_hero_subtitle', [
        'default' => 'Correção de bugs, <strong>remoção de vírus</strong> e <strong>migração segura</strong> — com preservação de SEO e checklist final. Diagnóstico <u>gratuito</u> e transparente.',
        'sanitize_callback' => 'wp_kses_post',
        'transport' => 'postMessage',
    ]);
    $wp_customize->add_control('wp_resgate_hero_subtitle', [
        'label' => __('📄 Subtítulo', 'wp-resgate'),
        'description' => __('Descrição detalhada dos serviços (aceita HTML básico)', 'wp-resgate'),
        'section' => 'wp_resgate_hero',
        'type' => 'textarea',
    ]);

    // Hero: Texto do Primeiro Botão
    $wp_customize->add_setting('wp_resgate_hero_btn1_text', [
        'default' => 'Fazer diagnóstico',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ]);
    $wp_customize->add_control('wp_resgate_hero_btn1_text', [
        'label' => __('🔘 Texto do Primeiro Botão', 'wp-resgate'),
        'section' => 'wp_resgate_hero',
        'type' => 'text',
    ]);

    // Hero: Texto do Segundo Botão
    $wp_customize->add_setting('wp_resgate_hero_btn2_text', [
        'default' => 'Ver soluções',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ]);
    $wp_customize->add_control('wp_resgate_hero_btn2_text', [
        'label' => __('🔘 Texto do Segundo Botão', 'wp-resgate'),
        'section' => 'wp_resgate_hero',
        'type' => 'text',
    ]);

    // Hero: Imagem Lateral (Card)
    $wp_customize->add_setting('wp_resgate_hero_image', [
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'refresh',
    ]);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'wp_resgate_hero_image', [
        'label' => __('🖼️ Imagem do Card Hero', 'wp-resgate'),
        'description' => __('Imagem que aparece no card lateral (recomendado: 900x520px)', 'wp-resgate'),
        'section' => 'wp_resgate_hero',
        'settings' => 'wp_resgate_hero_image',
    ]));

    // ====================================
    // SEÇÃO: CORES E ESTILO
    // ====================================
    $wp_customize->add_section('wp_resgate_style', [
        'title' => __('🎨 Cores e Estilo', 'wp-resgate'),
        'description' => __('Personalize as cores do tema', 'wp-resgate'),
        'panel' => 'wp_resgate_panel',
        'priority' => 30,
    ]);

    // Cor Primária
    $wp_customize->add_setting('wp_resgate_primary_color', [
        'default' => '#0d6efd',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'wp_resgate_primary_color', [
        'label' => __('🎯 Cor Primária', 'wp-resgate'),
        'description' => __('Cor principal do tema', 'wp-resgate'),
        'section' => 'wp_resgate_style',
    ]));

    // Cor Secundária
    $wp_customize->add_setting('wp_resgate_secondary_color', [
        'default' => '#6c757d',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'wp_resgate_secondary_color', [
        'label' => __('🎨 Cor Secundária', 'wp-resgate'),
        'description' => __('Cor secundária do tema', 'wp-resgate'),
        'section' => 'wp_resgate_style',
    ]));

    // ====================================
    // SEÇÃO: SEGURANÇA E ANTI-SPAM
    // ====================================
    $wp_customize->add_section('wp_resgate_security', [
        'title' => __('🔒 Segurança e Anti-spam', 'wp-resgate'),
        'description' => __('Configure a proteção reCAPTCHA nos formulários e login.', 'wp-resgate'),
        'panel' => 'wp_resgate_panel',
        'priority' => 40,
    ]);

    $wp_customize->add_setting('wp_resgate_recaptcha_site_key', [
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ]);
    $wp_customize->add_control('wp_resgate_recaptcha_site_key', [
        'label' => __('Chave do Site reCAPTCHA (Site Key)', 'wp-resgate'),
        'description' => __('Cole a chave pública gerada no Google reCAPTCHA v2 (checkbox).', 'wp-resgate'),
        'section' => 'wp_resgate_security',
        'type' => 'text',
    ]);

    $wp_customize->add_setting('wp_resgate_recaptcha_secret_key', [
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ]);
    $wp_customize->add_control('wp_resgate_recaptcha_secret_key', [
        'label' => __('Chave Secreta reCAPTCHA (Secret Key)', 'wp-resgate'),
        'description' => __('Cole a chave secreta correspondente para validação no servidor.', 'wp-resgate'),
        'section' => 'wp_resgate_security',
        'type' => 'text',
    ]);
}
add_action('customize_register', 'wp_resgate_customizer');

/**
 * Gerar CSS customizado baseado nas configurações do Customizer
 */
function wp_resgate_custom_css() {
    $primary_color = get_theme_mod('wp_resgate_primary_color', '#0d6efd');
    $secondary_color = get_theme_mod('wp_resgate_secondary_color', '#6c757d');
    
    $css = "
    <style id='wp-resgate-custom-css'>
        :root {
            --wp-resgate-primary: {$primary_color};
            --wp-resgate-primary-dark: " . adjustBrightness($primary_color, -20) . ";
            --wp-resgate-secondary: {$secondary_color};
        }
        
        /* Textos primários - apenas quando não estão sobre fundo azul */
        .text-primary:not(.bg-primary *):not(.hero *):not(.btn *) {
            color: {$primary_color} !important;
        }
        
        /* Botões primários com fundo */
        .btn-primary {
            background-color: {$primary_color} !important;
            border-color: {$primary_color} !important;
            color: white !important;
        }
        
        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active {
            background-color: " . adjustBrightness($primary_color, -20) . " !important;
            border-color: " . adjustBrightness($primary_color, -20) . " !important;
            color: white !important;
        }
        
        /* Botões outline primários */
        .btn-outline-primary {
            color: {$primary_color} !important;
            border-color: {$primary_color} !important;
            background-color: transparent !important;
        }
        
        .btn-outline-primary:hover,
        .btn-outline-primary:focus,
        .btn-outline-primary:active {
            background-color: {$primary_color} !important;
            border-color: {$primary_color} !important;
            color: white !important;
        }
        
        /* Corrigir textos em fundos azuis */
        .hero .text-primary:not(.btn):not(.btn *),
        .bg-primary .text-primary:not(.btn):not(.btn *),
        .guarantee-card .text-primary:not(.btn):not(.btn *),
        .faq-premium-button .text-primary:not(.btn):not(.btn *) {
            color: white !important;
        }
        
        /* Corrigir badges em fundos azuis */
        .hero .badge .text-primary,
        .bg-primary .badge .text-primary {
            color: {$primary_color} !important;
        }
        
        /* Corrigir ícones em fundos azuis */
        .hero .icon-circle,
        .guarantee-card .icon-circle {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: white !important;
        }
        
        /* Backgrounds primários */
        .bg-primary {
            background-color: {$primary_color} !important;
        }
    </style>
    ";
    
    echo $css;
}
add_action('wp_head', 'wp_resgate_custom_css');

/**
 * Função auxiliar para ajustar brilho das cores
 */
function adjustBrightness($hex, $steps) {
    // Remover # se presente
    $hex = str_replace('#', '', $hex);
    
    // Separar em componentes RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Ajustar brilho
    $r = max(0, min(255, $r + $steps));
    $g = max(0, min(255, $g + $steps));
    $b = max(0, min(255, $b + $steps));
    
    return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . 
               str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . 
               str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
}

/**
 * Função para processar formulário de diagnóstico
 */
function wp_resgate_handle_diagnostic_form() {
    // Verificar nonce
    if (!wp_verify_nonce($_POST['nonce'], 'wp_resgate_nonce')) {
        wp_die(__('Erro de segurança', 'wp-resgate'));
    }

    if (wp_resgate_is_recaptcha_enabled()) {
        $recaptcha_token = isset($_POST['g-recaptcha-response']) ? sanitize_text_field(wp_unslash($_POST['g-recaptcha-response'])) : '';

        if (!wp_resgate_verify_recaptcha($recaptcha_token)) {
            wp_send_json_error(__('Falha na verificação do reCAPTCHA. Tente novamente.', 'wp-resgate'));
        }
    }

    // Sanitizar dados
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $website = esc_url_raw($_POST['website']);
    $problem = sanitize_textarea_field($_POST['problem']);

    // Validar dados
    if (empty($name) || empty($email) || empty($website) || empty($problem)) {
        wp_send_json_error(__('Todos os campos são obrigatórios', 'wp-resgate'));
    }

    if (!is_email($email)) {
        wp_send_json_error(__('E-mail inválido', 'wp-resgate'));
    }

    // Enviar e-mail
    $to = get_option('admin_email');
    $subject = '[WP Resgate] Nova solicitação de diagnóstico';
    $message = sprintf(
        "Nova solicitação de diagnóstico:\n\n" .
        "Nome: %s\n" .
        "E-mail: %s\n" .
        "Website: %s\n" .
        "Problema:\n%s\n\n" .
        "Data: %s",
        $name,
        $email,
        $website,
        $problem,
        current_time('d/m/Y H:i:s')
    );

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    ];

    if (wp_mail($to, $subject, $message, $headers)) {
        wp_send_json_success(__('Diagnóstico solicitado com sucesso!', 'wp-resgate'));
    } else {
        wp_send_json_error(__('Erro ao enviar solicitação', 'wp-resgate'));
    }
}
add_action('wp_ajax_diagnostic_form', 'wp_resgate_handle_diagnostic_form');
add_action('wp_ajax_nopriv_diagnostic_form', 'wp_resgate_handle_diagnostic_form');

/**
 * Otimizações de performance
 */
function wp_resgate_performance_optimizations() {
    // Remover versões dos scripts/estilos
    add_filter('style_loader_src', 'wp_resgate_remove_version', 15);
    add_filter('script_loader_src', 'wp_resgate_remove_version', 15);

    // Remover emojis se não necessário
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
}
add_action('init', 'wp_resgate_performance_optimizations');

function wp_resgate_remove_version($src) {
    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}

/**
 * Custom Post Types
 */
function wp_resgate_register_cpts() {
    // CPT para Etapas do Processo
    register_post_type('process_steps', [
        'labels' => [
            'name' => __('Etapas do Processo', 'wp-resgate'),
            'singular_name' => __('Etapa', 'wp-resgate'),
            'add_new' => __('Adicionar Nova', 'wp-resgate'),
            'add_new_item' => __('Adicionar Nova Etapa', 'wp-resgate'),
            'edit_item' => __('Editar Etapa', 'wp-resgate'),
            'new_item' => __('Nova Etapa', 'wp-resgate'),
            'view_item' => __('Ver Etapa', 'wp-resgate'),
            'search_items' => __('Buscar Etapas', 'wp-resgate'),
            'not_found' => __('Nenhuma etapa encontrada', 'wp-resgate'),
            'not_found_in_trash' => __('Nenhuma etapa encontrada na lixeira', 'wp-resgate'),
            'all_items' => __('Todas as Etapas', 'wp-resgate'),
            'menu_name' => __('Etapas do Processo', 'wp-resgate'),
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'themes.php',
        'show_in_admin_bar' => false,
        'show_in_nav_menus' => false,
        'can_export' => true,
        'has_archive' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'capability_type' => 'post',
        'menu_icon' => 'dashicons-list-view',
        'supports' => ['title', 'editor', 'page-attributes'],
        'rewrite' => false,
    ]);

    // CPT para Depoimentos/Testimonials
    register_post_type('testimonial', [
        'labels' => [
            'name' => __('Depoimentos', 'wp-resgate'),
            'singular_name' => __('Depoimento', 'wp-resgate'),
            'add_new' => __('Adicionar Novo', 'wp-resgate'),
            'add_new_item' => __('Adicionar Novo Depoimento', 'wp-resgate'),
            'edit_item' => __('Editar Depoimento', 'wp-resgate'),
            'new_item' => __('Novo Depoimento', 'wp-resgate'),
            'view_item' => __('Ver Depoimento', 'wp-resgate'),
            'search_items' => __('Buscar Depoimentos', 'wp-resgate'),
            'not_found' => __('Nenhum depoimento encontrado', 'wp-resgate'),
            'not_found_in_trash' => __('Nenhum depoimento encontrado na lixeira', 'wp-resgate'),
            'all_items' => __('Todos os Depoimentos', 'wp-resgate'),
            'menu_name' => __('Depoimentos', 'wp-resgate'),
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'themes.php',
        'show_in_admin_bar' => false,
        'show_in_nav_menus' => false,
        'can_export' => true,
        'has_archive' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'capability_type' => 'post',
        'menu_icon' => 'dashicons-testimonial',
        'supports' => ['title', 'editor', 'thumbnail'],
        'rewrite' => false,
    ]);

    // CPT para Serviços
    register_post_type('service', [
        'labels' => [
            'name' => __('Serviços', 'wp-resgate'),
            'singular_name' => __('Serviço', 'wp-resgate'),
            'add_new' => __('Adicionar Novo', 'wp-resgate'),
            'add_new_item' => __('Adicionar Novo Serviço', 'wp-resgate'),
            'edit_item' => __('Editar Serviço', 'wp-resgate'),
            'new_item' => __('Novo Serviço', 'wp-resgate'),
            'view_item' => __('Ver Serviço', 'wp-resgate'),
            'search_items' => __('Buscar Serviços', 'wp-resgate'),
            'not_found' => __('Nenhum serviço encontrado', 'wp-resgate'),
            'not_found_in_trash' => __('Nenhum serviço encontrado na lixeira', 'wp-resgate'),
            'all_items' => __('Todos os Serviços', 'wp-resgate'),
            'menu_name' => __('Serviços', 'wp-resgate'),
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'themes.php',
        'show_in_admin_bar' => false,
        'show_in_nav_menus' => false,
        'can_export' => true,
        'has_archive' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'capability_type' => 'post',
        'menu_icon' => 'dashicons-admin-tools',
        'supports' => ['title', 'editor', 'page-attributes'],
        'rewrite' => false,
    ]);

    // CPT para Logos/Clientes
    register_post_type('client_logo', [
        'labels' => [
            'name' => __('Logos de Clientes', 'wp-resgate'),
            'singular_name' => __('Logo', 'wp-resgate'),
            'add_new' => __('Adicionar Novo', 'wp-resgate'),
            'add_new_item' => __('Adicionar Novo Logo', 'wp-resgate'),
            'edit_item' => __('Editar Logo', 'wp-resgate'),
            'new_item' => __('Novo Logo', 'wp-resgate'),
            'view_item' => __('Ver Logo', 'wp-resgate'),
            'search_items' => __('Buscar Logos', 'wp-resgate'),
            'not_found' => __('Nenhum logo encontrado', 'wp-resgate'),
            'not_found_in_trash' => __('Nenhum logo encontrado na lixeira', 'wp-resgate'),
            'all_items' => __('Todos os Logos', 'wp-resgate'),
            'menu_name' => __('Logos de Clientes', 'wp-resgate'),
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'themes.php',
        'show_in_admin_bar' => false,
        'show_in_nav_menus' => false,
        'can_export' => true,
        'has_archive' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'capability_type' => 'post',
        'menu_icon' => 'dashicons-format-image',
        'supports' => ['title', 'thumbnail', 'page-attributes'],
        'rewrite' => false,
    ]);
}
add_action('init', 'wp_resgate_register_cpts');

/**
 * Meta boxes para Process Steps
 */
function wp_resgate_add_process_steps_meta_boxes() {
    add_meta_box(
        'process_step_details',
        __('Detalhes da Etapa', 'wp-resgate'),
        'wp_resgate_process_step_meta_box_callback',
        'process_steps',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'wp_resgate_add_process_steps_meta_boxes');

/**
 * Meta boxes para Testimonials
 */
function wp_resgate_add_testimonials_meta_boxes() {
    add_meta_box(
        'testimonial_details',
        __('Dados do Cliente', 'wp-resgate'),
        'wp_resgate_testimonial_meta_box_callback',
        'testimonial',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'wp_resgate_add_testimonials_meta_boxes');

/**
 * Meta boxes para Services
 */
function wp_resgate_add_services_meta_boxes() {
    add_meta_box(
        'service_details',
        __('Detalhes do Serviço', 'wp-resgate'),
        'wp_resgate_service_meta_box_callback',
        'service',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'wp_resgate_add_services_meta_boxes');

/**
 * Meta boxes para Client Logos
 */
function wp_resgate_add_client_logos_meta_boxes() {
    add_meta_box(
        'client_logo_details',
        __('Detalhes do Cliente', 'wp-resgate'),
        'wp_resgate_client_logo_meta_box_callback',
        'client_logo',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'wp_resgate_add_client_logos_meta_boxes');

/**
 * Callback do meta box
 */
function wp_resgate_process_step_meta_box_callback($post) {
    wp_nonce_field('wp_resgate_process_step_meta', 'wp_resgate_process_step_nonce');
    
    $step_number = get_post_meta($post->ID, '_step_number', true);
    $step_icon = get_post_meta($post->ID, '_step_icon', true);
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="step_number"><?php _e('Número da Etapa', 'wp-resgate'); ?></label>
            </th>
            <td>
                <input type="number" id="step_number" name="step_number" value="<?php echo esc_attr($step_number); ?>" min="1" max="10" />
                <p class="description"><?php _e('Número que aparecerá no badge da etapa (1, 2, 3, etc.)', 'wp-resgate'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="step_icon"><?php _e('Ícone Bootstrap (opcional)', 'wp-resgate'); ?></label>
            </th>
            <td>
                <input type="text" id="step_icon" name="step_icon" value="<?php echo esc_attr($step_icon); ?>" class="regular-text" placeholder="bi-check-circle" />
                <p class="description">
                    <?php _e('Classe do ícone Bootstrap Icons (ex: bi-check-circle, bi-clipboard2-pulse)', 'wp-resgate'); ?><br>
                    <a href="https://icons.getbootstrap.com/" target="_blank"><?php _e('Ver ícones disponíveis', 'wp-resgate'); ?></a>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="post_excerpt"><?php _e('Descrição Resumida', 'wp-resgate'); ?></label>
            </th>
            <td>
                <textarea id="post_excerpt" name="excerpt" rows="3" cols="50" class="large-text"><?php echo esc_textarea($post->post_excerpt); ?></textarea>
                <p class="description"><?php _e('Descrição que aparecerá abaixo do título da etapa', 'wp-resgate'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Callback do meta box para testimonials
 */
function wp_resgate_testimonial_meta_box_callback($post) {
    wp_nonce_field('wp_resgate_testimonial_meta', 'wp_resgate_testimonial_nonce');
    
    $client_name = get_post_meta($post->ID, '_client_name', true);
    $client_company = get_post_meta($post->ID, '_client_company', true);
    $client_website = get_post_meta($post->ID, '_client_website', true);
    $rating = get_post_meta($post->ID, '_rating', true);
    $featured = get_post_meta($post->ID, '_featured', true);
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="client_name"><?php _e('Nome do Cliente', 'wp-resgate'); ?> *</label>
            </th>
            <td>
                <input type="text" id="client_name" name="client_name" value="<?php echo esc_attr($client_name); ?>" class="regular-text" required />
                <p class="description"><?php _e('Nome que aparecerá no depoimento', 'wp-resgate'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="client_company"><?php _e('Empresa/Área', 'wp-resgate'); ?></label>
            </th>
            <td>
                <input type="text" id="client_company" name="client_company" value="<?php echo esc_attr($client_company); ?>" class="regular-text" />
                <p class="description"><?php _e('Empresa ou área de atuação do cliente (ex: "E-commerce", "Educação")', 'wp-resgate'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="client_website"><?php _e('Website do Cliente', 'wp-resgate'); ?></label>
            </th>
            <td>
                <input type="url" id="client_website" name="client_website" value="<?php echo esc_attr($client_website); ?>" class="regular-text" placeholder="https://" />
                <p class="description"><?php _e('Site do cliente (opcional)', 'wp-resgate'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="rating"><?php _e('Avaliação', 'wp-resgate'); ?></label>
            </th>
            <td>
                <select id="rating" name="rating">
                    <option value=""><?php _e('Selecione...', 'wp-resgate'); ?></option>
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <option value="<?php echo $i; ?>" <?php selected($rating, $i); ?>>
                            <?php echo str_repeat('★', $i) . str_repeat('☆', 5-$i) . " ($i estrelas)"; ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <p class="description"><?php _e('Avaliação em estrelas do cliente', 'wp-resgate'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="featured"><?php _e('Depoimento em Destaque', 'wp-resgate'); ?></label>
            </th>
            <td>
                <label>
                    <input type="checkbox" id="featured" name="featured" value="1" <?php checked($featured, '1'); ?> />
                    <?php _e('Marcar como depoimento em destaque', 'wp-resgate'); ?>
                </label>
                <p class="description"><?php _e('Depoimentos em destaque aparecem primeiro na listagem', 'wp-resgate'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label><?php _e('Foto do Cliente', 'wp-resgate'); ?></label>
            </th>
            <td>
                <p class="description">
                    <?php _e('Use a seção "Imagem destacada" no lado direito para adicionar a foto do cliente.', 'wp-resgate'); ?><br>
                    <?php _e('Tamanho recomendado: 150x150px (quadrado)', 'wp-resgate'); ?>
                </p>
            </td>
        </tr>
    </table>
    
    <hr />
    
    <h4><?php _e('Como usar este depoimento:', 'wp-resgate'); ?></h4>
    <ol>
        <li><?php _e('Preencha o nome do cliente e empresa/área', 'wp-resgate'); ?></li>
        <li><?php _e('Escreva o depoimento completo no editor principal acima', 'wp-resgate'); ?></li>
        <li><?php _e('Defina a avaliação em estrelas', 'wp-resgate'); ?></li>
        <li><?php _e('Adicione a foto do cliente na "Imagem destacada"', 'wp-resgate'); ?></li>
        <li><?php _e('Publique para que apareça no site', 'wp-resgate'); ?></li>
    </ol>
    <?php
}

/**
 * Callback do meta box para services
 */
function wp_resgate_service_meta_box_callback($post) {
    wp_nonce_field('wp_resgate_service_meta', 'wp_resgate_service_nonce');
    
    $service_icon = get_post_meta($post->ID, '_service_icon', true);
    $service_color = get_post_meta($post->ID, '_service_color', true);
    $service_featured = get_post_meta($post->ID, '_service_featured', true);
    $service_badge_text = get_post_meta($post->ID, '_service_badge_text', true);
    $service_price_from = get_post_meta($post->ID, '_service_price_from', true);
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="service_icon"><?php _e('Ícone do Serviço', 'wp-resgate'); ?> *</label>
            </th>
            <td>
                <input type="text" id="service_icon" name="service_icon" value="<?php echo esc_attr($service_icon); ?>" class="regular-text" placeholder="bi-bug" required />
                <p class="description">
                    <?php _e('Classe do ícone Bootstrap Icons (ex: bi-bug, bi-tools, bi-speedometer2)', 'wp-resgate'); ?><br>
                    <a href="https://icons.getbootstrap.com/" target="_blank"><?php _e('Ver ícones disponíveis', 'wp-resgate'); ?></a>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="service_color"><?php _e('Cor do Ícone', 'wp-resgate'); ?></label>
            </th>
            <td>
                <select id="service_color" name="service_color">
                    <option value="primary" <?php selected($service_color, 'primary'); ?>><?php _e('Azul (Padrão)', 'wp-resgate'); ?></option>
                    <option value="success" <?php selected($service_color, 'success'); ?>><?php _e('Verde', 'wp-resgate'); ?></option>
                    <option value="warning" <?php selected($service_color, 'warning'); ?>><?php _e('Laranja', 'wp-resgate'); ?></option>
                    <option value="danger" <?php selected($service_color, 'danger'); ?>><?php _e('Vermelho', 'wp-resgate'); ?></option>
                    <option value="info" <?php selected($service_color, 'info'); ?>><?php _e('Ciano', 'wp-resgate'); ?></option>
                    <option value="secondary" <?php selected($service_color, 'secondary'); ?>><?php _e('Cinza', 'wp-resgate'); ?></option>
                </select>
                <p class="description"><?php _e('Cor do círculo do ícone', 'wp-resgate'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="service_price_from"><?php _e('Preço "A partir de"', 'wp-resgate'); ?></label>
            </th>
            <td>
                <input type="text" id="service_price_from" name="service_price_from" value="<?php echo esc_attr($service_price_from); ?>" class="regular-text" placeholder="R$ 150" />
                <p class="description"><?php _e('Preço inicial do serviço (ex: "R$ 150", "Consulte")', 'wp-resgate'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="service_featured"><?php _e('Serviço em Destaque', 'wp-resgate'); ?></label>
            </th>
            <td>
                <label>
                    <input type="checkbox" id="service_featured" name="service_featured" value="1" <?php checked($service_featured, '1'); ?> />
                    <?php _e('Marcar como serviço em destaque', 'wp-resgate'); ?>
                </label>
                <p class="description"><?php _e('Serviços em destaque aparecem primeiro e com visual diferenciado', 'wp-resgate'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="service_badge_text"><?php _e('Texto da Badge (opcional)', 'wp-resgate'); ?></label>
            </th>
            <td>
                <input type="text" id="service_badge_text" name="service_badge_text" value="<?php echo esc_attr($service_badge_text); ?>" class="regular-text" placeholder="Popular" />
                <p class="description"><?php _e('Texto personalizado para a badge de destaque. Deixe vazio para usar "Popular" ou desativar se não for destaque.', 'wp-resgate'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="post_excerpt"><?php _e('Descrição Resumida', 'wp-resgate'); ?></label>
            </th>
            <td>
                <textarea id="post_excerpt" name="excerpt" rows="3" cols="50" class="large-text"><?php echo esc_textarea($post->post_excerpt); ?></textarea>
                <p class="description"><?php _e('Descrição curta que aparecerá no card do serviço (máximo 2 linhas)', 'wp-resgate'); ?></p>
            </td>
        </tr>
    </table>
    
    <hr />
    
    <h4><?php _e('Como criar um serviço eficaz:', 'wp-resgate'); ?></h4>
    <ol>
        <li><?php _e('Escolha um ícone que represente bem o serviço', 'wp-resgate'); ?></li>
        <li><?php _e('Escreva um título claro e objetivo', 'wp-resgate'); ?></li>
        <li><?php _e('Use a descrição resumida para apresentar o benefício principal', 'wp-resgate'); ?></li>
        <li><?php _e('No editor principal, detalhe o que está incluído no serviço', 'wp-resgate'); ?></li>
        <li><?php _e('Defina um preço inicial se apropriado', 'wp-resgate'); ?></li>
        <li><?php _e('Marque como destaque se for um serviço principal', 'wp-resgate'); ?></li>
    </ol>
    <?php
}

/**
 * Callback do meta box para client logos
 */
function wp_resgate_client_logo_meta_box_callback($post) {
    wp_nonce_field('wp_resgate_client_logo_meta', 'wp_resgate_client_logo_nonce');
    
    $client_website = get_post_meta($post->ID, '_client_website', true);
    $client_description = get_post_meta($post->ID, '_client_description', true);
    $logo_alt_text = get_post_meta($post->ID, '_logo_alt_text', true);
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="client_website"><?php _e('Website do Cliente', 'wp-resgate'); ?></label>
            </th>
            <td>
                <input type="url" id="client_website" name="client_website" value="<?php echo esc_attr($client_website); ?>" class="regular-text" placeholder="https://exemplo.com" />
                <p class="description"><?php _e('URL do site do cliente (opcional). Se preenchido, o logo será clicável.', 'wp-resgate'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="client_description"><?php _e('Descrição do Cliente', 'wp-resgate'); ?></label>
            </th>
            <td>
                <input type="text" id="client_description" name="client_description" value="<?php echo esc_attr($client_description); ?>" class="regular-text" placeholder="E-commerce de moda" />
                <p class="description"><?php _e('Breve descrição da empresa/área de atuação (opcional)', 'wp-resgate'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="logo_alt_text"><?php _e('Texto Alternativo', 'wp-resgate'); ?></label>
            </th>
            <td>
                <input type="text" id="logo_alt_text" name="logo_alt_text" value="<?php echo esc_attr($logo_alt_text); ?>" class="regular-text" placeholder="Logo da Empresa XYZ" />
                <p class="description"><?php _e('Texto alternativo para a imagem (importante para SEO e acessibilidade)', 'wp-resgate'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label><?php _e('Logo do Cliente', 'wp-resgate'); ?></label>
            </th>
            <td>
                <p class="description">
                    <?php _e('Use a seção "Imagem destacada" no lado direito para fazer upload do logo.', 'wp-resgate'); ?><br>
                    <strong><?php _e('Tamanho recomendado:', 'wp-resgate'); ?></strong> 200x80px (formato landscape)<br>
                    <strong><?php _e('Formato:', 'wp-resgate'); ?></strong> PNG com fundo transparente para melhor resultado
                </p>
            </td>
        </tr>
    </table>
    
    <hr />
    
    <h4><?php _e('Dicas para um bom logo de cliente:', 'wp-resgate'); ?></h4>
    <ol>
        <li><?php _e('Use logos em alta resolução (mínimo 200x80px)', 'wp-resgate'); ?></li>
        <li><?php _e('Prefira formato PNG com fundo transparente', 'wp-resgate'); ?></li>
        <li><?php _e('Mantenha proporções adequadas (landscape funciona melhor)', 'wp-resgate'); ?></li>
        <li><?php _e('Adicione o website do cliente para criar um link', 'wp-resgate'); ?></li>
        <li><?php _e('Sempre preencha o texto alternativo para acessibilidade', 'wp-resgate'); ?></li>
        <li><?php _e('Use a "Ordem" (lateral direita) para controlar a posição', 'wp-resgate'); ?></li>
    </ol>
    <?php
}

/**
 * Salvar meta dados das etapas
 */
function wp_resgate_save_process_step_meta($post_id) {
    if (!isset($_POST['wp_resgate_process_step_nonce']) || 
        !wp_verify_nonce($_POST['wp_resgate_process_step_nonce'], 'wp_resgate_process_step_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['step_number'])) {
        update_post_meta($post_id, '_step_number', sanitize_text_field($_POST['step_number']));
    }

    if (isset($_POST['step_icon'])) {
        update_post_meta($post_id, '_step_icon', sanitize_text_field($_POST['step_icon']));
    }
}
add_action('save_post', 'wp_resgate_save_process_step_meta');

/**
 * Salvar meta dados dos testimonials
 */
function wp_resgate_save_testimonial_meta($post_id) {
    if (!isset($_POST['wp_resgate_testimonial_nonce']) || 
        !wp_verify_nonce($_POST['wp_resgate_testimonial_nonce'], 'wp_resgate_testimonial_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = ['client_name', 'client_company', 'client_website', 'rating', 'featured'];
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            if ($field === 'featured') {
                update_post_meta($post_id, '_' . $field, $_POST[$field] === '1' ? '1' : '');
            } else {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        } else {
            delete_post_meta($post_id, '_' . $field);
        }
    }
}
add_action('save_post', 'wp_resgate_save_testimonial_meta');

/**
 * Salvar meta dados dos services
 */
function wp_resgate_save_service_meta($post_id) {
    if (!isset($_POST['wp_resgate_service_nonce']) || 
        !wp_verify_nonce($_POST['wp_resgate_service_nonce'], 'wp_resgate_service_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = ['service_icon', 'service_color', 'service_price_from', 'service_featured', 'service_badge_text'];
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            if ($field === 'service_featured') {
                update_post_meta($post_id, '_' . $field, $_POST[$field] === '1' ? '1' : '');
            } else {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        } else {
            if ($field === 'service_featured') {
                delete_post_meta($post_id, '_' . $field);
            }
        }
    }
}
add_action('save_post', 'wp_resgate_save_service_meta');

/**
 * Salvar meta dados dos client logos
 */
function wp_resgate_save_client_logo_meta($post_id) {
    if (!isset($_POST['wp_resgate_client_logo_nonce']) || 
        !wp_verify_nonce($_POST['wp_resgate_client_logo_nonce'], 'wp_resgate_client_logo_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = ['client_website', 'client_description', 'logo_alt_text'];
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        } else {
            delete_post_meta($post_id, '_' . $field);
        }
    }
}
add_action('save_post', 'wp_resgate_save_client_logo_meta');

/**
 * Customizar colunas da listagem de etapas
 */
function wp_resgate_process_steps_columns($columns) {
    $new_columns = [];
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['step_number'] = __('Número', 'wp-resgate');
    $new_columns['step_icon'] = __('Ícone', 'wp-resgate');
    $new_columns['description'] = __('Descrição', 'wp-resgate');
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}
add_filter('manage_process_steps_posts_columns', 'wp_resgate_process_steps_columns');

/**
 * Conteúdo das colunas customizadas
 */
function wp_resgate_process_steps_column_content($column, $post_id) {
    switch ($column) {
        case 'step_number':
            $number = get_post_meta($post_id, '_step_number', true);
            echo $number ? '<span class="badge" style="background-color: #0d6efd; color: white; padding: 4px 8px; border-radius: 4px;">' . esc_html($number) . '</span>' : '—';
            break;
            
        case 'step_icon':
            $icon = get_post_meta($post_id, '_step_icon', true);
            if ($icon) {
                echo '<i class="bi ' . esc_attr($icon) . '" style="font-size: 18px;"></i>';
            } else {
                echo '—';
            }
            break;
            
        case 'description':
            $post = get_post($post_id);
            echo $post->post_excerpt ? wp_trim_words($post->post_excerpt, 15) : '—';
            break;
    }
}
add_action('manage_process_steps_posts_custom_column', 'wp_resgate_process_steps_column_content', 10, 2);

/**
 * Tornar colunas ordenáveis
 */
function wp_resgate_process_steps_sortable_columns($columns) {
    $columns['step_number'] = 'step_number';
    return $columns;
}
add_filter('manage_edit-process_steps_sortable_columns', 'wp_resgate_process_steps_sortable_columns');

/**
 * Ordenação por meta fields
 */
function wp_resgate_process_steps_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->get('orderby') === 'step_number') {
        $query->set('meta_key', '_step_number');
        $query->set('orderby', 'meta_value_num');
    }
}
add_action('pre_get_posts', 'wp_resgate_process_steps_orderby');

/**
 * Customizar colunas da listagem de testimonials
 */
function wp_resgate_testimonials_columns($columns) {
    $new_columns = [];
    $new_columns['cb'] = $columns['cb'];
    $new_columns['featured_image'] = __('Foto', 'wp-resgate');
    $new_columns['title'] = $columns['title'];
    $new_columns['client_info'] = __('Cliente', 'wp-resgate');
    $new_columns['rating'] = __('Avaliação', 'wp-resgate');
    $new_columns['featured'] = __('Destaque', 'wp-resgate');
    $new_columns['testimonial_content'] = __('Depoimento', 'wp-resgate');
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}
add_filter('manage_testimonial_posts_columns', 'wp_resgate_testimonials_columns');

/**
 * Conteúdo das colunas customizadas para testimonials
 */
function wp_resgate_testimonials_column_content($column, $post_id) {
    switch ($column) {
        case 'featured_image':
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, [40, 40], ['style' => 'border-radius: 50%;']);
            } else {
                $client_name = get_post_meta($post_id, '_client_name', true);
                $initial = $client_name ? substr($client_name, 0, 1) : '?';
                echo '<div style="width: 40px; height: 40px; background: #007bff; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">' . esc_html($initial) . '</div>';
            }
            break;
            
        case 'client_info':
            $client_name = get_post_meta($post_id, '_client_name', true);
            $client_company = get_post_meta($post_id, '_client_company', true);
            echo '<strong>' . esc_html($client_name) . '</strong>';
            if ($client_company) {
                echo '<br><small style="color: #666;">' . esc_html($client_company) . '</small>';
            }
            break;
            
        case 'rating':
            $rating = get_post_meta($post_id, '_rating', true);
            if ($rating) {
                for ($i = 1; $i <= 5; $i++) {
                    echo $i <= $rating ? '★' : '☆';
                }
                echo " ($rating/5)";
            } else {
                echo '—';
            }
            break;
            
        case 'featured':
            $featured = get_post_meta($post_id, '_featured', true);
            if ($featured === '1') {
                echo '<span style="color: #d63384; font-weight: bold;">★ Destaque</span>';
            } else {
                echo '—';
            }
            break;
            
        case 'testimonial_content':
            $post = get_post($post_id);
            echo wp_trim_words(strip_tags($post->post_content), 10);
            break;
    }
}
add_action('manage_testimonial_posts_custom_column', 'wp_resgate_testimonials_column_content', 10, 2);

/**
 * Tornar colunas ordenáveis para testimonials
 */
function wp_resgate_testimonials_sortable_columns($columns) {
    $columns['rating'] = 'rating';
    $columns['featured'] = 'featured';
    return $columns;
}
add_filter('manage_edit-testimonial_sortable_columns', 'wp_resgate_testimonials_sortable_columns');

/**
 * Ordenação por meta fields para testimonials
 */
function wp_resgate_testimonials_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    $orderby = $query->get('orderby');
    
    if ($orderby === 'rating') {
        $query->set('meta_key', '_rating');
        $query->set('orderby', 'meta_value_num');
    } elseif ($orderby === 'featured') {
        $query->set('meta_key', '_featured');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'wp_resgate_testimonials_orderby');

/**
 * Customizar colunas da listagem de services
 */
function wp_resgate_services_columns($columns) {
    $new_columns = [];
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['service_icon'] = __('Ícone', 'wp-resgate');
    $new_columns['service_color'] = __('Cor', 'wp-resgate');
    $new_columns['service_description'] = __('Descrição', 'wp-resgate');
    $new_columns['service_price'] = __('Preço', 'wp-resgate');
    $new_columns['featured'] = __('Destaque', 'wp-resgate');
    $new_columns['menu_order'] = __('Ordem', 'wp-resgate');
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}
add_filter('manage_service_posts_columns', 'wp_resgate_services_columns');

/**
 * Conteúdo das colunas customizadas para services
 */
function wp_resgate_services_column_content($column, $post_id) {
    switch ($column) {
        case 'service_icon':
            $icon = get_post_meta($post_id, '_service_icon', true);
            $color = get_post_meta($post_id, '_service_color', true) ?: 'primary';
            if ($icon) {
                $color_map = [
                    'primary' => '#0d6efd',
                    'success' => '#198754',
                    'warning' => '#fd7e14',
                    'danger' => '#dc3545',
                    'info' => '#0dcaf0',
                    'secondary' => '#6c757d'
                ];
                $hex_color = $color_map[$color] ?? '#0d6efd';
                echo '<div style="width: 40px; height: 40px; background: ' . $hex_color . '; color: white; border-radius: 8px; display: flex; align-items: center; justify-content: center;"><i class="bi ' . esc_attr($icon) . '" style="font-size: 18px;"></i></div>';
            } else {
                echo '—';
            }
            break;
            
        case 'service_color':
            $color = get_post_meta($post_id, '_service_color', true) ?: 'primary';
            $color_names = [
                'primary' => 'Azul',
                'success' => 'Verde',
                'warning' => 'Laranja',
                'danger' => 'Vermelho',
                'info' => 'Ciano',
                'secondary' => 'Cinza'
            ];
            echo esc_html($color_names[$color] ?? 'Azul');
            break;
            
        case 'service_description':
            $post = get_post($post_id);
            $excerpt = $post->post_excerpt ?: wp_trim_words($post->post_content, 15);
            echo wp_trim_words($excerpt, 10);
            break;
            
        case 'service_price':
            $price = get_post_meta($post_id, '_service_price_from', true);
            echo $price ? '<strong>' . esc_html($price) . '</strong>' : '—';
            break;
            
        case 'featured':
            $featured = get_post_meta($post_id, '_service_featured', true);
            if ($featured === '1') {
                echo '<span style="color: #d63384; font-weight: bold;">★ Destaque</span>';
            } else {
                echo '—';
            }
            break;
            
        case 'menu_order':
            $post = get_post($post_id);
            echo $post->menu_order;
            break;
    }
}
add_action('manage_service_posts_custom_column', 'wp_resgate_services_column_content', 10, 2);

/**
 * Tornar colunas ordenáveis para services
 */
function wp_resgate_services_sortable_columns($columns) {
    $columns['featured'] = 'featured';
    $columns['menu_order'] = 'menu_order';
    $columns['service_price'] = 'service_price';
    return $columns;
}
add_filter('manage_edit-service_sortable_columns', 'wp_resgate_services_sortable_columns');

/**
 * Ordenação por meta fields para services
 */
function wp_resgate_services_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    $orderby = $query->get('orderby');
    
    if ($orderby === 'featured') {
        $query->set('meta_key', '_service_featured');
        $query->set('orderby', 'meta_value');
    } elseif ($orderby === 'service_price') {
        $query->set('meta_key', '_service_price_from');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'wp_resgate_services_orderby');

/**
 * Customizar colunas da listagem de client logos
 */
function wp_resgate_client_logos_columns($columns) {
    $new_columns = [];
    $new_columns['cb'] = $columns['cb'];
    $new_columns['featured_image'] = __('Logo', 'wp-resgate');
    $new_columns['title'] = $columns['title'];
    $new_columns['client_info'] = __('Informações', 'wp-resgate');
    $new_columns['client_website'] = __('Website', 'wp-resgate');
    $new_columns['menu_order'] = __('Ordem', 'wp-resgate');
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}
add_filter('manage_client_logo_posts_columns', 'wp_resgate_client_logos_columns');

/**
 * Conteúdo das colunas customizadas para client logos
 */
function wp_resgate_client_logos_column_content($column, $post_id) {
    switch ($column) {
        case 'featured_image':
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, [80, 32], ['style' => 'max-height: 32px; width: auto; object-fit: contain; background: #f8f9fa; padding: 4px; border-radius: 4px;']);
            } else {
                echo '<div style="width: 80px; height: 32px; background: #e9ecef; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #6c757d;">Sem logo</div>';
            }
            break;
            
        case 'client_info':
            $description = get_post_meta($post_id, '_client_description', true);
            $alt_text = get_post_meta($post_id, '_logo_alt_text', true);
            if ($description) {
                echo '<strong>' . esc_html($description) . '</strong><br>';
            }
            if ($alt_text) {
                echo '<small style="color: #666;">Alt: ' . esc_html($alt_text) . '</small>';
            }
            if (!$description && !$alt_text) {
                echo '—';
            }
            break;
            
        case 'client_website':
            $website = get_post_meta($post_id, '_client_website', true);
            if ($website) {
                echo '<a href="' . esc_url($website) . '" target="_blank" rel="noopener">' . 
                     '<i class="dashicons dashicons-external" style="font-size: 14px; vertical-align: middle;"></i> ' . 
                     esc_html(parse_url($website, PHP_URL_HOST)) . '</a>';
            } else {
                echo '—';
            }
            break;
            
        case 'menu_order':
            $post = get_post($post_id);
            echo $post->menu_order;
            break;
    }
}
add_action('manage_client_logo_posts_custom_column', 'wp_resgate_client_logos_column_content', 10, 2);

/**
 * Tornar colunas ordenáveis para client logos
 */
function wp_resgate_client_logos_sortable_columns($columns) {
    $columns['menu_order'] = 'menu_order';
    return $columns;
}
add_filter('manage_edit-client_logo_sortable_columns', 'wp_resgate_client_logos_sortable_columns');

/**
 * Função helper para buscar etapas do processo
 */
function wp_resgate_get_process_steps() {
    $steps = get_posts([
        'post_type' => 'process_steps',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_key' => '_step_number',
        'orderby' => 'meta_value_num',
        'order' => 'ASC'
    ]);

    $process_steps = [];
    
    foreach ($steps as $step) {
        $step_number = get_post_meta($step->ID, '_step_number', true);
        $step_icon = get_post_meta($step->ID, '_step_icon', true);
        
        $process_steps[] = [
            'number' => $step_number ?: '?',
            'title' => $step->post_title,
            'description' => $step->post_excerpt ?: wp_trim_words($step->post_content, 20),
            'icon' => $step_icon,
            'content' => $step->post_content
        ];
    }

    // Fallback para dados padrão se não houver etapas cadastradas
    if (empty($process_steps)) {
        $process_steps = [
            [
                'number' => '1',
                'title' => __('Diagnóstico gratuito', 'wp-resgate'),
                'description' => __('Analisamos seu site e enviamos um relatório com causas e plano de ação.', 'wp-resgate'),
                'icon' => 'bi-clipboard2-pulse',
                'content' => __('Realizamos uma análise completa do seu site WordPress, identificando problemas de segurança, performance, bugs e outros issues. Você recebe um relatório detalhado com:\n\n• Diagnóstico técnico completo\n• Identificação das causas dos problemas\n• Plano de ação personalizado\n• Orçamento transparente\n• Cronograma estimado\n\nTudo isso sem custo e sem compromisso.', 'wp-resgate')
            ],
            [
                'number' => '2', 
                'title' => __('Execução segura', 'wp-resgate'),
                'description' => __('Limpamos, corrigimos e testamos em ambiente seguro com backup.', 'wp-resgate'),
                'icon' => 'bi-shield-check',
                'content' => __('Com sua aprovação, iniciamos o trabalho seguindo os mais altos padrões de segurança:\n\n• Backup completo antes de qualquer alteração\n• Trabalho em ambiente de teste/staging\n• Limpeza de malware e vulnerabilidades\n• Correção de bugs e conflitos\n• Otimização de performance\n• Testes rigorosos de funcionalidade\n\nSeu site permanece online e seguro durante todo o processo.', 'wp-resgate')
            ],
            [
                'number' => '3',
                'title' => __('Entrega + prevenção', 'wp-resgate'),
                'description' => __('Checklist final e recomendações de segurança e manutenção.', 'wp-resgate'),
                'icon' => 'bi-check-circle',
                'content' => __('Após a conclusão dos trabalhos, você recebe:\n\n• Site totalmente funcional e seguro\n• Checklist detalhado das correções realizadas\n• Hardening de segurança aplicado\n• Relatório de performance melhorada\n• Guia de prevenção e boas práticas\n• Recomendações de manutenção\n• Suporte pós-entrega incluído\n\nSeu WordPress estará blindado contra futuros problemas.', 'wp-resgate')
            ]
        ];
    }

    return $process_steps;
}

/**
 * Função helper para buscar serviços
 */
function wp_resgate_get_services() {
    $services = get_posts([
        'post_type' => 'service',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'meta_query' => [
            'relation' => 'OR',
            [
                'key' => '_service_featured',
                'value' => '1',
                'compare' => '='
            ],
            [
                'key' => '_service_featured',
                'compare' => 'NOT EXISTS'
            ]
        ]
    ]);

    $service_list = [];
    
    foreach ($services as $service) {
        $service_icon = get_post_meta($service->ID, '_service_icon', true);
        $service_color = get_post_meta($service->ID, '_service_color', true) ?: 'primary';
        $service_price = get_post_meta($service->ID, '_service_price_from', true);
        $service_featured = get_post_meta($service->ID, '_service_featured', true);
        $service_badge_text = get_post_meta($service->ID, '_service_badge_text', true);
        
        $service_list[] = [
            'title' => $service->post_title,
            'description' => $service->post_excerpt ?: wp_trim_words($service->post_content, 20),
            'content' => $service->post_content,
            'icon' => $service_icon,
            'color' => $service_color,
            'price' => $service_price,
            'featured' => $service_featured === '1',
            'badge_text' => $service_badge_text ?: 'Popular'
        ];
    }

    // Fallback para dados padrão se não houver serviços cadastrados
    if (empty($service_list)) {
        $service_list = [
            [
                'title' => __('Remoção de malware', 'wp-resgate'),
                'description' => __('Limpeza pós-hack, restauração de arquivos e reforço de segurança (hardening).', 'wp-resgate'),
                'icon' => 'bi-bug',
                'color' => 'danger',
                'price' => '',
                'featured' => true,
                'badge_text' => 'Urgente'
            ],
            [
                'title' => __('Correção de bugs', 'wp-resgate'),
                'description' => __('Erros 500/502, conflitos de plugins/tema e falhas após atualização.', 'wp-resgate'),
                'icon' => 'bi-tools',
                'color' => 'warning',
                'price' => '',
                'featured' => false,
                'badge_text' => 'Popular'
            ],
            [
                'title' => __('Migração segura', 'wp-resgate'),
                'description' => __('Transferência sem perda de conteúdo ou SEO, com checklist pós-migração.', 'wp-resgate'),
                'icon' => 'bi-arrow-left-right',
                'color' => 'info',
                'price' => '',
                'featured' => false,
                'badge_text' => 'Recomendado'
            ],
            [
                'title' => __('Performance', 'wp-resgate'),
                'description' => __('Otimização de carregamento, cache, imagens e Core Web Vitals.', 'wp-resgate'),
                'icon' => 'bi-speedometer2',
                'color' => 'success',
                'price' => '',
                'featured' => false,
                'badge_text' => 'Popular'
            ]
        ];
    }

    return $service_list;
}

/**
 * Função helper para buscar logos de clientes
 */
function wp_resgate_get_client_logos() {
    $logos = get_posts([
        'post_type' => 'client_logo',
        'posts_per_page' => 10, // Máximo 10 logos
        'post_status' => 'publish',
        'orderby' => 'menu_order',
        'order' => 'ASC'
    ]);

    $logo_list = [];
    
    foreach ($logos as $logo) {
        $client_website = get_post_meta($logo->ID, '_client_website', true);
        $client_description = get_post_meta($logo->ID, '_client_description', true);
        $logo_alt_text = get_post_meta($logo->ID, '_logo_alt_text', true);
        $featured_image = get_the_post_thumbnail_url($logo->ID, 'medium');
        
        $logo_list[] = [
            'title' => $logo->post_title,
            'image' => $featured_image,
            'website' => $client_website,
            'description' => $client_description,
            'alt_text' => $logo_alt_text ?: $logo->post_title
        ];
    }

    // Fallback para dados padrão se não houver logos cadastrados
    if (empty($logo_list)) {
        $logo_list = [
            [
                'title' => 'Cliente 1',
                'image' => 'https://placehold.co/200x80/007bff/ffffff?text=Cliente+1',
                'website' => '',
                'description' => 'E-commerce',
                'alt_text' => 'Logo Cliente 1'
            ],
            [
                'title' => 'Cliente 2', 
                'image' => 'https://placehold.co/200x80/28a745/ffffff?text=Cliente+2',
                'website' => '',
                'description' => 'Educação',
                'alt_text' => 'Logo Cliente 2'
            ],
            [
                'title' => 'Cliente 3',
                'image' => 'https://placehold.co/200x80/ffc107/ffffff?text=Cliente+3',
                'website' => '',
                'description' => 'Saúde',
                'alt_text' => 'Logo Cliente 3'
            ],
            [
                'title' => 'Cliente 4',
                'image' => 'https://placehold.co/200x80/dc3545/ffffff?text=Cliente+4', 
                'website' => '',
                'description' => 'Tecnologia',
                'alt_text' => 'Logo Cliente 4'
            ],
            [
                'title' => 'Cliente 5',
                'image' => 'https://placehold.co/200x80/6f42c1/ffffff?text=Cliente+5',
                'website' => '',
                'description' => 'Consultoria',
                'alt_text' => 'Logo Cliente 5'
            ]
        ];
    }

    return $logo_list;
}

/**
 * Carregar Bootstrap Icons no admin para visualizar ícones
 */
function wp_resgate_admin_enqueue_scripts($hook) {
    global $post_type;
    
    if (in_array($post_type, ['process_steps', 'testimonial', 'service', 'client_logo'])) {
        wp_enqueue_style(
            'bootstrap-icons-admin',
            'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css',
            [],
            '1.11.3'
        );
        
        wp_add_inline_style('bootstrap-icons-admin', '
            .manage-column .bi { font-size: 16px; }
            .badge { display: inline-block; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 700; line-height: 1; text-align: center; white-space: nowrap; vertical-align: baseline; border-radius: 0.25rem; }
            .testimonials-rating { color: #ffc107; }
        ');
    }
}
add_action('admin_enqueue_scripts', 'wp_resgate_admin_enqueue_scripts');

// =============================================================================
// FAQ CUSTOM POST TYPE
// =============================================================================

/**
 * Registrar Custom Post Type para FAQ
 */
function wp_resgate_register_faq_cpt() {
    $labels = [
        'name'                  => _x('FAQs', 'Post Type General Name', 'wp-resgate'),
        'singular_name'         => _x('FAQ', 'Post Type Singular Name', 'wp-resgate'),
        'menu_name'             => __('FAQs', 'wp-resgate'),
        'name_admin_bar'        => __('FAQ', 'wp-resgate'),
        'archives'              => __('FAQ Archives', 'wp-resgate'),
        'attributes'            => __('FAQ Attributes', 'wp-resgate'),
        'parent_item_colon'     => __('Parent FAQ:', 'wp-resgate'),
        'all_items'             => __('Todas as FAQs', 'wp-resgate'),
        'add_new_item'          => __('Adicionar Nova FAQ', 'wp-resgate'),
        'add_new'               => __('Adicionar Nova', 'wp-resgate'),
        'new_item'              => __('Nova FAQ', 'wp-resgate'),
        'edit_item'             => __('Editar FAQ', 'wp-resgate'),
        'update_item'           => __('Atualizar FAQ', 'wp-resgate'),
        'view_item'             => __('Ver FAQ', 'wp-resgate'),
        'view_items'            => __('Ver FAQs', 'wp-resgate'),
        'search_items'          => __('Buscar FAQs', 'wp-resgate'),
        'not_found'             => __('Não encontrado', 'wp-resgate'),
        'not_found_in_trash'    => __('Não encontrado na lixeira', 'wp-resgate'),
        'featured_image'        => __('Imagem Destacada', 'wp-resgate'),
        'set_featured_image'    => __('Definir imagem destacada', 'wp-resgate'),
        'remove_featured_image' => __('Remover imagem destacada', 'wp-resgate'),
        'use_featured_image'    => __('Usar como imagem destacada', 'wp-resgate'),
        'insert_into_item'      => __('Inserir na FAQ', 'wp-resgate'),
        'uploaded_to_this_item' => __('Enviado para esta FAQ', 'wp-resgate'),
        'items_list'            => __('Lista de FAQs', 'wp-resgate'),
        'items_list_navigation' => __('Navegação da lista de FAQs', 'wp-resgate'),
        'filter_items_list'     => __('Filtrar lista de FAQs', 'wp-resgate'),
    ];

    $args = [
        'label'                 => __('FAQ', 'wp-resgate'),
        'description'           => __('Perguntas frequentes sobre os serviços', 'wp-resgate'),
        'labels'                => $labels,
        'supports'              => ['title', 'page-attributes'],
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 25,
        'menu_icon'             => 'dashicons-editor-help',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'capability_type'       => 'post',
        'show_in_rest'          => false,
    ];

    register_post_type('faq', $args);
}
add_action('init', 'wp_resgate_register_faq_cpt', 0);

/**
 * Adicionar meta boxes para FAQ
 */
function wp_resgate_add_faq_meta_boxes() {
    add_meta_box(
        'faq_details',
        __('Detalhes da FAQ', 'wp-resgate'),
        'wp_resgate_faq_meta_box_callback',
        'faq',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'wp_resgate_add_faq_meta_boxes');

/**
 * Callback do meta box de FAQ
 */
function wp_resgate_faq_meta_box_callback($post) {
    wp_nonce_field('wp_resgate_save_faq_meta', 'wp_resgate_faq_meta_nonce');
    
    $question = get_post_meta($post->ID, '_faq_question', true);
    $answer = get_post_meta($post->ID, '_faq_answer', true);
    $expanded = get_post_meta($post->ID, '_faq_expanded', true);
    ?>
    
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="faq_question"><?php _e('Pergunta', 'wp-resgate'); ?> <span style="color: #d63384;">*</span></label>
            </th>
            <td>
                <input type="text" 
                       id="faq_question" 
                       name="faq_question" 
                       value="<?php echo esc_attr($question); ?>" 
                       class="large-text" 
                       required />
                <p class="description"><?php _e('A pergunta que será exibida no accordion', 'wp-resgate'); ?></p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="faq_answer"><?php _e('Resposta', 'wp-resgate'); ?> <span style="color: #d63384;">*</span></label>
            </th>
            <td>
                <textarea id="faq_answer" 
                          name="faq_answer" 
                          rows="4" 
                          class="large-text" 
                          required><?php echo esc_textarea($answer); ?></textarea>
                <p class="description"><?php _e('A resposta completa para a pergunta', 'wp-resgate'); ?></p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="faq_expanded"><?php _e('Expansão inicial', 'wp-resgate'); ?></label>
            </th>
            <td>
                <label>
                    <input type="checkbox" 
                           id="faq_expanded" 
                           name="faq_expanded" 
                           value="1" 
                           <?php checked($expanded, '1'); ?> />
                    <?php _e('Mostrar esta FAQ expandida por padrão', 'wp-resgate'); ?>
                </label>
                <p class="description"><?php _e('Marque para que esta pergunta apareça aberta quando a página carregar', 'wp-resgate'); ?></p>
            </td>
        </tr>
    </table>
    
    <div style="margin-top: 20px; padding: 15px; background: #e7f3ff; border: 1px solid #b8daff; border-radius: 4px;">
        <h4 style="margin: 0 0 10px 0; color: #0c63e4;"><?php _e('💡 Dicas para uma boa FAQ', 'wp-resgate'); ?></h4>
        <ul style="margin: 0; padding-left: 20px;">
            <li><?php _e('Use perguntas que seus clientes realmente fazem', 'wp-resgate'); ?></li>
            <li><?php _e('Mantenha as respostas claras e objetivas', 'wp-resgate'); ?></li>
            <li><?php _e('Ordene por relevância usando o campo "Ordem" no lado direito', 'wp-resgate'); ?></li>
            <li><?php _e('Deixe apenas a primeira FAQ expandida por padrão', 'wp-resgate'); ?></li>
        </ul>
    </div>
    
    <?php
}

/**
 * Salvar meta dados da FAQ
 */
function wp_resgate_save_faq_meta($post_id) {
    if (!isset($_POST['wp_resgate_faq_meta_nonce']) || 
        !wp_verify_nonce($_POST['wp_resgate_faq_meta_nonce'], 'wp_resgate_save_faq_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Salvar pergunta
    if (isset($_POST['faq_question'])) {
        $question = sanitize_text_field($_POST['faq_question']);
        if (empty(trim($question))) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>' . __('Erro: A pergunta da FAQ é obrigatória.', 'wp-resgate') . '</p></div>';
            });
            return;
        }
        update_post_meta($post_id, '_faq_question', $question);
    }

    // Salvar resposta
    if (isset($_POST['faq_answer'])) {
        $answer = sanitize_textarea_field($_POST['faq_answer']);
        if (empty(trim($answer))) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>' . __('Erro: A resposta da FAQ é obrigatória.', 'wp-resgate') . '</p></div>';
            });
            return;
        }
        update_post_meta($post_id, '_faq_answer', $answer);
    }

    // Salvar se expandida
    $expanded = isset($_POST['faq_expanded']) ? '1' : '0';
    update_post_meta($post_id, '_faq_expanded', $expanded);
}
add_action('save_post', 'wp_resgate_save_faq_meta');

/**
 * Customizar colunas da listagem de FAQs
 */
function wp_resgate_faq_columns($columns) {
    $new_columns = [];
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['faq_question'] = __('Pergunta', 'wp-resgate');
    $new_columns['faq_answer'] = __('Resposta', 'wp-resgate');
    $new_columns['faq_expanded'] = __('Expandida', 'wp-resgate');
    $new_columns['menu_order'] = __('Ordem', 'wp-resgate');
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}
add_filter('manage_faq_posts_columns', 'wp_resgate_faq_columns');

/**
 * Conteúdo das colunas customizadas para FAQs
 */
function wp_resgate_faq_column_content($column, $post_id) {
    switch ($column) {
        case 'faq_question':
            $question = get_post_meta($post_id, '_faq_question', true);
            echo $question ? '<strong>' . esc_html($question) . '</strong>' : '<em style="color: #999;">' . __('Não definida', 'wp-resgate') . '</em>';
            break;
            
        case 'faq_answer':
            $answer = get_post_meta($post_id, '_faq_answer', true);
            if ($answer) {
                $short_answer = wp_trim_words($answer, 12, '...');
                echo '<div style="max-width: 250px;">' . esc_html($short_answer) . '</div>';
            } else {
                echo '<em style="color: #999;">' . __('Não definida', 'wp-resgate') . '</em>';
            }
            break;
            
        case 'faq_expanded':
            $expanded = get_post_meta($post_id, '_faq_expanded', true);
            if ($expanded === '1') {
                echo '<span class="badge" style="background: #198754; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px;">' . __('Sim', 'wp-resgate') . '</span>';
            } else {
                echo '<span class="badge" style="background: #6c757d; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px;">' . __('Não', 'wp-resgate') . '</span>';
            }
            break;
            
        case 'menu_order':
            $order = get_post_field('menu_order', $post_id);
            echo '<strong>' . intval($order) . '</strong>';
            break;
    }
}
add_filter('manage_faq_posts_custom_column', 'wp_resgate_faq_column_content', 10, 2);

/**
 * Tornar colunas de FAQ ordenáveis
 */
function wp_resgate_faq_sortable_columns($columns) {
    $columns['menu_order'] = 'menu_order';
    $columns['faq_expanded'] = 'faq_expanded';
    return $columns;
}
add_filter('manage_edit-faq_sortable_columns', 'wp_resgate_faq_sortable_columns');

/**
 * Ordenação customizada para FAQs
 */
function wp_resgate_faq_orderby($query) {
    if (!is_admin()) {
        return;
    }

    $orderby = $query->get('orderby');
    
    if ($orderby === 'faq_expanded') {
        $query->set('meta_key', '_faq_expanded');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'wp_resgate_faq_orderby');

/**
 * Função helper para buscar FAQs
 */
function get_faqs($args = []) {
    $defaults = [
        'post_type' => 'faq',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'meta_query' => [
            [
                'key' => '_faq_question',
                'compare' => 'EXISTS'
            ],
            [
                'key' => '_faq_answer',
                'compare' => 'EXISTS'
            ]
        ]
    ];
    
    $args = wp_parse_args($args, $defaults);
    $posts = get_posts($args);
    
    $faqs = [];
    foreach ($posts as $post) {
        $question = get_post_meta($post->ID, '_faq_question', true);
        $answer = get_post_meta($post->ID, '_faq_answer', true);
        $expanded = get_post_meta($post->ID, '_faq_expanded', true) === '1';
        
        if ($question && $answer) {
            $faqs[] = [
                'id' => $post->ID,
                'question' => $question,
                'answer' => $answer,
                'expanded' => $expanded,
                'order' => $post->menu_order
            ];
        }
    }
    
    // Fallback para dados padrão se não houver FAQs
    if (empty($faqs)) {
        $faqs = [
            [
                'id' => 'default-1',
                'question' => __('Quanto tempo leva para resolver?', 'wp-resgate'),
                'answer' => __('Depende da complexidade — fazemos diagnóstico gratuito para estimar prazo e custo com precisão.', 'wp-resgate'),
                'expanded' => true,
                'order' => 0
            ],
            [
                'id' => 'default-2',
                'question' => __('Vocês mexem em senhas/FTP?', 'wp-resgate'),
                'answer' => __('Sim — sempre com autorização e seguindo boas práticas de segurança. Podemos criar acessos temporários.', 'wp-resgate'),
                'expanded' => false,
                'order' => 1
            ],
            [
                'id' => 'default-3',
                'question' => __('E se o problema voltar?', 'wp-resgate'),
                'answer' => __('Incluímos ações preventivas e oferecemos planos de manutenção para reduzir muito essa chance.', 'wp-resgate'),
                'expanded' => false,
                'order' => 2
            ],
            [
                'id' => 'default-4',
                'question' => __('Qual o custo dos serviços?', 'wp-resgate'),
                'answer' => __('Cada caso é único. Por isso oferecemos diagnóstico gratuito com orçamento transparente e sem surpresas.', 'wp-resgate'),
                'expanded' => false,
                'order' => 3
            ],
            [
                'id' => 'default-5',
                'question' => __('Fazem backup antes de mexer no site?', 'wp-resgate'),
                'answer' => __('Sempre! É o primeiro passo obrigatório. Trabalhamos em ambiente seguro e só aplicamos mudanças após testes.', 'wp-resgate'),
                'expanded' => false,
                'order' => 4
            ],
            [
                'id' => 'default-6',
                'question' => __('Atendem sites em outros idiomas/países?', 'wp-resgate'),
                'answer' => __('Sim, trabalhamos com sites WordPress em qualquer idioma e localização. O atendimento é em português ou inglês.', 'wp-resgate'),
                'expanded' => false,
                'order' => 5
            ]
        ];
    }
    
    return $faqs;
}

/**
 * Incluir dados padrão
 */
require_once get_template_directory() . '/inc/default-data.php';

/**
 * SEO básico
 */
/**
 * Recupera URLs de ativos de marca para metadados/SEO.
 *
 * @return array{logo:string,share_image:string}
 */
function wp_resgate_get_brand_assets() {
    $logo_url = '';
    $custom_logo_id = get_theme_mod('custom_logo');

    if ($custom_logo_id) {
        $custom_logo = wp_get_attachment_image_url($custom_logo_id, 'full');
        if ($custom_logo) {
            $logo_url = $custom_logo;
        }
    }

    if ($logo_url === '') {
        $theme_logo = get_theme_mod('wp_resgate_logo', '');
        if ($theme_logo) {
            $logo_url = $theme_logo;
        }
    }

    if ($logo_url === '') {
        $site_icon = get_site_icon_url(512);
        if ($site_icon) {
            $logo_url = $site_icon;
        }
    }

    $share_image = get_theme_mod('wp_resgate_hero_image', '');
    if ($share_image === '') {
        $share_image = $logo_url;
    }

    return [
        'logo' => $logo_url,
        'share_image' => $share_image,
    ];
}

/**
 * Normaliza telefone para formato aceito pelo Schema (E.164 simplificado).
 *
 * @param string $phone
 */
function wp_resgate_normalize_phone_for_schema($phone) {
    $digits = preg_replace('/\D+/', '', (string) $phone);

    if ($digits === '') {
        return '';
    }

    if (strpos($digits, '00') === 0) {
        $digits = substr($digits, 2);
    }

    return '+' . ltrim($digits, '+');
}

/**
 * Metadados essenciais para SEO/social.
 */
function wp_resgate_seo_meta() {
    if (!is_front_page()) {
        return;
    }

    $site_name = wp_strip_all_tags(get_theme_mod('wp_resgate_company_name', get_bloginfo('name')));
    $raw_description = get_theme_mod('wp_resgate_tagline', get_bloginfo('description'));
    $description = wp_strip_all_tags($raw_description);
    if ($description === '') {
        $description = wp_strip_all_tags(get_bloginfo('description'));
    }
    $description = wp_trim_words($description, 50, '');

    $canonical = trailingslashit(home_url());
    $locale = str_replace('_', '-', get_locale());
    $title = wp_get_document_title();
    $robots = wp_resgate_is_local_environment() ? 'noindex,nofollow' : 'index,follow';
    $brand_assets = wp_resgate_get_brand_assets();
    $share_image = $brand_assets['share_image'];

    printf('<link rel="canonical" href="%s" />' . "\n", esc_url($canonical));
    printf('<meta name="description" content="%s" />' . "\n", esc_attr($description));
    printf('<meta name="robots" content="%s" />' . "\n", esc_attr($robots));
    printf('<meta property="og:locale" content="%s" />' . "\n", esc_attr($locale));
    printf('<meta property="og:type" content="website" />' . "\n");
    printf('<meta property="og:title" content="%s" />' . "\n", esc_attr($title));
    printf('<meta property="og:description" content="%s" />' . "\n", esc_attr($description));
    printf('<meta property="og:url" content="%s" />' . "\n", esc_url($canonical));
    printf('<meta property="og:site_name" content="%s" />' . "\n", esc_attr($site_name));

    if ($share_image) {
        printf('<meta property="og:image" content="%s" />' . "\n", esc_url($share_image));
    }

    $twitter_card = $share_image ? 'summary_large_image' : 'summary';
    printf('<meta name="twitter:card" content="%s" />' . "\n", esc_attr($twitter_card));
    printf('<meta name="twitter:title" content="%s" />' . "\n", esc_attr($title));
    printf('<meta name="twitter:description" content="%s" />' . "\n", esc_attr($description));

    if ($share_image) {
        printf('<meta name="twitter:image" content="%s" />' . "\n", esc_url($share_image));
    }
}
add_action('wp_head', 'wp_resgate_seo_meta', 20);

/**
 * Estrutura JSON-LD para rich results.
 */
function wp_resgate_output_structured_data() {
    if (!is_front_page()) {
        return;
    }

    $site_url = trailingslashit(home_url());
    $site_name = wp_strip_all_tags(get_theme_mod('wp_resgate_company_name', get_bloginfo('name')));
    $description = wp_strip_all_tags(get_theme_mod('wp_resgate_tagline', get_bloginfo('description')));
    $locale = str_replace('_', '-', get_locale());
    $brand_assets = wp_resgate_get_brand_assets();

    $graph = [];

    $organization = [
        '@type' => 'ProfessionalService',
        'name' => $site_name,
        'url' => $site_url,
        'description' => $description,
    ];

    if ($brand_assets['logo']) {
        $organization['logo'] = $brand_assets['logo'];
    }

    $email = sanitize_email(get_theme_mod('wp_resgate_email', get_option('admin_email')));
    if ($email) {
        $organization['email'] = $email;
    }

    $raw_phone = get_theme_mod('wp_resgate_whatsapp', '');
    $normalized_phone = wp_resgate_normalize_phone_for_schema($raw_phone);
    if ($normalized_phone) {
        $organization['telephone'] = $normalized_phone;
        $organization['contactPoint'] = [
            [
                '@type' => 'ContactPoint',
                'telephone' => $normalized_phone,
                'contactType' => 'customer service',
                'areaServed' => ['BR'],
                'availableLanguage' => ['Portuguese', 'English'],
            ],
        ];
    }

    $address = wp_strip_all_tags(get_theme_mod('wp_resgate_address', ''));
    if ($address) {
        $organization['address'] = [
            '@type' => 'PostalAddress',
            'streetAddress' => $address,
        ];
    }

    $graph[] = $organization;

    $website = [
        '@type' => 'WebSite',
        'name' => $site_name,
        'url' => $site_url,
        'description' => $description,
        'inLanguage' => $locale,
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => add_query_arg('s', '{search_term_string}', home_url('/')),
            'query-input' => 'required name=search_term_string',
        ],
    ];

    if ($brand_assets['share_image']) {
        $website['image'] = $brand_assets['share_image'];
    }

    $graph[] = $website;

    $faq_entries = function_exists('get_faqs') ? get_faqs() : [];
    if (!empty($faq_entries)) {
        $faq_schema = [
            '@type' => 'FAQPage',
            'name' => sprintf(__('Perguntas frequentes sobre %s', 'wp-resgate'), $site_name),
            'mainEntity' => [],
        ];

        foreach ($faq_entries as $entry) {
            $question_text = wp_strip_all_tags($entry['question'] ?? '');
            $answer_text = wp_strip_all_tags($entry['answer'] ?? '');

            if ($question_text === '' || $answer_text === '') {
                continue;
            }

            $faq_schema['mainEntity'][] = [
                '@type' => 'Question',
                'name' => $question_text,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $answer_text,
                ],
            ];
        }

        if (!empty($faq_schema['mainEntity'])) {
            $graph[] = $faq_schema;
        }
    }

    if (empty($graph)) {
        return;
    }

    $structured_data = [
        '@context' => 'https://schema.org',
        '@graph' => $graph,
    ];

    echo '<script type="application/ld+json">' . wp_json_encode($structured_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}
add_action('wp_head', 'wp_resgate_output_structured_data', 30);

/**
 * Adiciona dicas de recursos para CDNs externos e host principal.
 *
 * @param array<string> $hints
 * @param string $relation_type
 * @return array<string>
 */
function wp_resgate_resource_hints($hints, $relation_type) {
    if (is_admin()) {
        return $hints;
    }

    if (in_array($relation_type, ['dns-prefetch', 'preconnect'], true)) {
        $hints[] = 'https://cdn.jsdelivr.net';
    }

    if ($relation_type === 'preconnect') {
        $hints[] = home_url('/');
    }

    return array_unique($hints);
}
add_filter('wp_resource_hints', 'wp_resgate_resource_hints', 10, 2);

/**
 * Converte CSS críticos em preload sem bloquear renderização.
 *
 * @param string $html
 * @param string $handle
 * @param string $href
 * @param string $media
 * @return string
 */
function wp_resgate_async_styles($html, $handle, $href, $media) {
    if (is_admin()) {
        return $html;
    }

    $async_handles = [
        'wp-resgate-style',
        'wp-resgate-scroll-fix',
        'bootstrap',
        'bootstrap-icons',
        'dashicons', // fallback para editores logados
    ];

    if (!in_array($handle, $async_handles, true)) {
        return $html;
    }

    $href_attr = esc_url($href);
    $media_attr = esc_attr($media);
    $crossorigin = strpos($href, '//cdn.jsdelivr.net') !== false ? ' crossorigin' : '';

    $preload = sprintf(
        "<link rel='preload' href='%s' as='style'%s onload=\"this.onload=null;this.rel='stylesheet'\" />",
        $href_attr,
        $crossorigin
    );

    $noscript = sprintf(
        "<noscript><link rel='stylesheet' href='%s' media='%s' /></noscript>",
        $href_attr,
        $media_attr
    );

    return $preload . "\n" . $noscript;
}
add_filter('style_loader_tag', 'wp_resgate_async_styles', 10, 4);

/**
 * Adiciona defer a scripts do tema e move jQuery para o rodapé.
 */
function wp_resgate_optimize_scripts() {
    add_filter(
        'script_loader_tag',
        function ($tag, $handle, $src) {
            if (is_admin()) {
                return $tag;
            }

            $defer_handles = [
                'bootstrap',
                'wp-resgate-script',
                'wp-resgate-diagnostics',
            ];

            if (in_array($handle, $defer_handles, true)) {
                if (false === stripos($tag, ' defer')) {
                    $tag = str_replace('<script ', '<script defer ', $tag);
                }
            }

            return $tag;
        },
        10,
        3
    );

    add_action(
        'wp_default_scripts',
        function ($scripts) {
            if (is_admin()) {
                return;
            }

            foreach (['jquery', 'jquery-core', 'jquery-migrate'] as $handle) {
                if (isset($scripts->registered[$handle])) {
                    $scripts->add_data($handle, 'group', 1);
                }
            }
        }
    );
}
add_action('after_setup_theme', 'wp_resgate_optimize_scripts');

/**
 * Pré-carrega a imagem principal do hero para melhorar o LCP.
 */
function wp_resgate_preload_hero_image() {
    if (!is_front_page()) {
        return;
    }

    $hero_image = get_theme_mod('wp_resgate_hero_image');
    if (!$hero_image) {
        return;
    }

    printf(
        "<link rel='preload' as='image' href='%s' fetchpriority='high' />\n",
        esc_url($hero_image)
    );
}
add_action('wp_head', 'wp_resgate_preload_hero_image', 5);

/**
 * Remove estilos de blocos do WordPress na home para evitar CSS desnecessário.
 */
function wp_resgate_trim_block_styles() {
    if (!is_front_page()) {
        return;
    }

    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('global-styles');
}
add_action('wp_enqueue_scripts', 'wp_resgate_trim_block_styles', 100);

/**
 * Incluir integração com Google Sheets
 */
require_once get_template_directory() . '/inc/google-sheets-integration.php';
require_once get_template_directory() . '/inc/system-tests.php';

/**
 * Incluir painel administrativo dos leads
 */
require_once get_template_directory() . '/inc/leads-admin.php';

// Inicializar o painel de administração dos leads
if (is_admin()) {
    new WP_Resgate_Leads_Admin();
}

/**
 * Debug helper (temporário - remover em produção)
 */
if (defined('WP_DEBUG') && WP_DEBUG) {
    require_once get_template_directory() . '/debug-helper.php';
}
