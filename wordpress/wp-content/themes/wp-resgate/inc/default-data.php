<?php
/**
 * Dados iniciais para CPT Process Steps
 * Insere dados apenas se não existirem etapas cadastradas
 */

function wp_resgate_insert_default_process_steps() {
    // Verificar se já existem etapas
    $existing_steps = get_posts([
        'post_type' => 'process_steps',
        'posts_per_page' => 1,
        'post_status' => 'any'
    ]);

    // Se já existem etapas, não inserir dados padrão
    if (!empty($existing_steps)) {
        return;
    }

    $default_steps = [
        [
            'title' => 'Diagnóstico gratuito',
            'description' => 'Analisamos seu site e enviamos um relatório com causas e plano de ação.',
            'number' => '1',
            'icon' => 'bi-clipboard2-pulse',
            'content' => 'Realizamos uma análise completa do seu site WordPress, identificando problemas de segurança, performance, bugs e outros issues. Você recebe um relatório detalhado com:\n\n• Diagnóstico técnico completo\n• Identificação das causas dos problemas\n• Plano de ação personalizado\n• Orçamento transparente\n• Cronograma estimado\n\nTudo isso sem custo e sem compromisso.'
        ],
        [
            'title' => 'Execução segura',
            'description' => 'Limpamos, corrigimos e testamos em ambiente seguro com backup.',
            'number' => '2', 
            'icon' => 'bi-shield-check',
            'content' => 'Com sua aprovação, iniciamos o trabalho seguindo os mais altos padrões de segurança:\n\n• Backup completo antes de qualquer alteração\n• Trabalho em ambiente de teste/staging\n• Limpeza de malware e vulnerabilidades\n• Correção de bugs e conflitos\n• Otimização de performance\n• Testes rigorosos de funcionalidade\n\nSeu site permanece online e seguro durante todo o processo.'
        ],
        [
            'title' => 'Entrega + prevenção',
            'description' => 'Checklist final e recomendações de segurança e manutenção.',
            'number' => '3',
            'icon' => 'bi-check-circle',
            'content' => 'Após a conclusão dos trabalhos, você recebe:\n\n• Site totalmente funcional e seguro\n• Checklist detalhado das correções realizadas\n• Hardening de segurança aplicado\n• Relatório de performance melhorada\n• Guia de prevenção e boas práticas\n• Recomendações de manutenção\n• Suporte pós-entrega incluído\n\nSeu WordPress estará blindado contra futuros problemas.'
        ]
    ];

    foreach ($default_steps as $step_data) {
        $post_id = wp_insert_post([
            'post_title' => $step_data['title'],
            'post_content' => $step_data['content'],
            'post_excerpt' => $step_data['description'],
            'post_status' => 'publish',
            'post_type' => 'process_steps',
            'post_author' => 1
        ]);

        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, '_step_number', $step_data['number']);
            update_post_meta($post_id, '_step_icon', $step_data['icon']);
        }
    }
}

/**
 * Dados iniciais para CPT Testimonials
 * Insere dados apenas se não existirem depoimentos cadastrados
 */
function wp_resgate_insert_default_testimonials() {
    // Verificar se já existem depoimentos
    $existing_testimonials = get_posts([
        'post_type' => 'testimonial',
        'posts_per_page' => 1,
        'post_status' => 'any'
    ]);

    // Se já existem depoimentos, não inserir dados padrão
    if (!empty($existing_testimonials)) {
        return;
    }

    $default_testimonials = [
        [
            'title' => 'Depoimento - Maria Silva',
            'content' => 'Estava desesperada com meu e-commerce invadido por malware. A equipe do WP Resgate não só limpou tudo em menos de 24h, como ainda otimizou a performance do site. Agora minhas vendas aumentaram 40%! Atendimento excepcional e muito transparente.',
            'client_name' => 'Maria Silva',
            'client_company' => 'E-commerce de Moda',
            'client_website' => '',
            'rating' => '5',
            'featured' => '1'
        ],
        [
            'title' => 'Depoimento - João Santos',
            'content' => 'Precisava migrar meu site educacional para um servidor melhor e estava com medo de perder o posicionamento no Google. A migração foi perfeita, sem perder uma única posição no SEO. Inclusive, o site ficou mais rápido! Recomendo demais.',
            'client_name' => 'João Santos',
            'client_company' => 'Cursos Online',
            'client_website' => '',
            'rating' => '5',
            'featured' => '1'
        ],
        [
            'title' => 'Depoimento - Ana Costa',
            'content' => 'Meu WordPress vivia com erros 500 e eu não sabia o que fazer. Encontrei o WP Resgate e foi a melhor decisão! Resolveram todos os conflitos entre plugins, otimizaram o banco de dados e ainda me ensinaram como prevenir problemas futuros.',
            'client_name' => 'Ana Costa',
            'client_company' => 'Consultoria Empresarial',
            'client_website' => '',
            'rating' => '5',
            'featured' => '0'
        ]
    ];

    foreach ($default_testimonials as $testimonial_data) {
        $post_id = wp_insert_post([
            'post_title' => $testimonial_data['title'],
            'post_content' => $testimonial_data['content'],
            'post_status' => 'publish',
            'post_type' => 'testimonial',
            'post_author' => 1
        ]);

        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, '_client_name', $testimonial_data['client_name']);
            update_post_meta($post_id, '_client_company', $testimonial_data['client_company']);
            update_post_meta($post_id, '_client_website', $testimonial_data['client_website']);
            update_post_meta($post_id, '_rating', $testimonial_data['rating']);
            update_post_meta($post_id, '_featured', $testimonial_data['featured']);
        }
    }
}

// Executar após ativação do tema ou quando CPT for registrado
add_action('after_switch_theme', 'wp_resgate_insert_default_process_steps');
add_action('after_switch_theme', 'wp_resgate_insert_default_testimonials');

add_action('init', function() {
    if (get_option('wp_resgate_process_steps_created') !== 'yes') {
        wp_resgate_insert_default_process_steps();
        update_option('wp_resgate_process_steps_created', 'yes');
    }
    
    if (get_option('wp_resgate_testimonials_created') !== 'yes') {
        wp_resgate_insert_default_testimonials();
        update_option('wp_resgate_testimonials_created', 'yes');
    }
}, 99);
?>