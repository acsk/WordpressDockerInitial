<?php
/**
 * Integração com Google Sheets via Webhook
 * WP Resgate - Sistema de captura de leads
 */

class WP_Resgate_Google_Sheets {
    
    private $webhook_url;
    private $sheet_id;
    
    public function __construct() {
        // Configurações - podem ser definidas no Customizer
        $this->webhook_url = get_theme_mod('wp_resgate_webhook_url', '');
        $this->sheet_id = get_theme_mod('wp_resgate_sheet_id', '');
        
        // Hooks
        add_action('wp_ajax_wp_resgate_form_submit', array($this, 'handle_form_submission'));
        add_action('wp_ajax_nopriv_wp_resgate_form_submit', array($this, 'handle_form_submission'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    /**
     * Enfileirar scripts necessários
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'wp-resgate-form-handler',
            get_template_directory_uri() . '/assets/js/form-handler.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        // Localizar script com dados necessários
        wp_localize_script('wp-resgate-form-handler', 'wpResgateForm', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wp_resgate_form_nonce'),
            'messages' => array(
                'sending' => __('Enviando...', 'wp-resgate'),
                'success' => __('Mensagem enviada com sucesso! Retornaremos em breve.', 'wp-resgate'),
                'error' => __('Erro ao enviar mensagem. Tente novamente.', 'wp-resgate'),
                'validation_error' => __('Por favor, preencha todos os campos obrigatórios.', 'wp-resgate'),
                'recaptcha' => __('Confirme que você não é um robô.', 'wp-resgate')
            )
        ));
    }
    
    /**
     * Processar submissão do formulário
     */
    public function handle_form_submission() {
        // Verificar nonce
        if (!wp_verify_nonce($_POST['nonce'], 'wp_resgate_form_nonce')) {
            wp_die(__('Erro de segurança', 'wp-resgate'));
        }

        if (wp_resgate_is_recaptcha_enabled()) {
            $recaptcha_token = isset($_POST['g-recaptcha-response']) ? sanitize_text_field(wp_unslash($_POST['g-recaptcha-response'])) : '';

            if (!wp_resgate_verify_recaptcha($recaptcha_token)) {
                wp_send_json_error(array('message' => __('Falha na verificação do reCAPTCHA. Tente novamente.', 'wp-resgate')));
            }
        }
        
        // Sanitizar dados
        $data = $this->sanitize_form_data($_POST);
        
        // Validar dados
        $validation = $this->validate_form_data($data);
        if (!$validation['valid']) {
            wp_send_json_error(array('message' => $validation['message']));
        }
        
        // Salvar localmente (backup)
        $lead_id = $this->save_lead_locally($data);
        
        // Adicionar lead_id aos dados para Google Sheets
        $data['lead_id'] = $lead_id;
        $data['action'] = 'new_lead';
        $data['status'] = 'new';
        $data['created_at'] = $data['timestamp'];
        $data['updated_at'] = $data['timestamp'];
        
        // Enviar para Google Sheets
        $sheets_result = $this->send_to_google_sheets($data);
        
        // Enviar email de notificação (opcional)
        $this->send_notification_email($data);
        
        // Debug: Log do resultado do Google Sheets
        error_log('WP Resgate - Resultado Google Sheets: ' . print_r($sheets_result, true));
        
        // Resposta (sempre sucesso se salvou localmente, mesmo que Google Sheets falhe)
        if ($lead_id) {
            $message = __('Diagnóstico solicitado com sucesso! Retornaremos em breve.', 'wp-resgate');
            
            // Se Google Sheets falhou, adicionar aviso no log mas não falhar para o usuário
            if (!$sheets_result['success']) {
                error_log('WP Resgate - Falha na integração Google Sheets: ' . $sheets_result['message']);
                // Opcional: incluir aviso adicional
                // $message .= ' ' . __('(Dados salvos localmente para backup)', 'wp-resgate');
            }
            
            wp_send_json_success(array(
                'message' => $message,
                'lead_id' => $lead_id,
                'sheets_integration' => $sheets_result['success']
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Erro ao processar formulário. Tente novamente.', 'wp-resgate')
            ));
        }
    }
    
    /**
     * Sanitizar dados do formulário
     */
    private function sanitize_form_data($post_data) {
        // Debug: Log dos dados recebidos
        error_log('WP Resgate - Dados recebidos: ' . print_r($post_data, true));
        
        return array(
            'name' => sanitize_text_field($post_data['name'] ?? ''),
            'email' => sanitize_email($post_data['email'] ?? ''),
            'phone' => sanitize_text_field($post_data['phone'] ?? ''),
            'website' => sanitize_url($post_data['website'] ?? ''),
            'problem_type' => sanitize_text_field($post_data['problem_type'] ?? ''),
            'urgency' => sanitize_text_field($post_data['urgency'] ?? ''),
            'description' => sanitize_textarea_field($post_data['description'] ?? ''),
            'source' => sanitize_text_field($post_data['source'] ?? 'website'),
            'honeypot' => sanitize_text_field($post_data['honeypot'] ?? ''),
            'privacy_consent' => sanitize_text_field($post_data['privacy_consent'] ?? ''),
            'timestamp' => current_time('mysql'),
            'ip_address' => $this->get_client_ip(),
            'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? ''),
            'page_url' => sanitize_url($_SERVER['HTTP_REFERER'] ?? '')
        );
    }
    
    /**
     * Validar dados do formulário
     */
    private function validate_form_data($data) {
        $errors = array();
        
        // Verificar honeypot (deve estar vazio)
        if (!empty($data['honeypot'])) {
            $errors[] = __('Spam detectado', 'wp-resgate');
        }
        
        // Verificar consentimento de privacidade
        if (empty($data['privacy_consent']) || $data['privacy_consent'] !== 'on') {
            $errors[] = __('Você deve aceitar os termos de privacidade', 'wp-resgate');
        }
        
        if (empty($data['name'])) {
            $errors[] = __('Nome é obrigatório', 'wp-resgate');
        }
        
        if (empty($data['email']) || !is_email($data['email'])) {
            $errors[] = __('Email válido é obrigatório', 'wp-resgate');
        }
        
        if (empty($data['description'])) {
            $errors[] = __('Descrição do problema é obrigatória', 'wp-resgate');
        }
        
        // Validação de spam simples
        if ($this->is_spam($data)) {
            $errors[] = __('Mensagem detectada como spam', 'wp-resgate');
        }
        
        return array(
            'valid' => empty($errors),
            'message' => implode(', ', $errors)
        );
    }
    
    /**
     * Salvar lead localmente (WordPress database)
     */
    private function save_lead_locally($data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'wp_resgate_leads';
        
        // Criar tabela se não existir
        $this->create_leads_table();
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'website' => $data['website'],
                'problem_type' => $data['problem_type'],
                'urgency' => $data['urgency'],
                'description' => $data['description'],
                'source' => $data['source'],
                'ip_address' => $data['ip_address'],
                'user_agent' => $data['user_agent'],
                'page_url' => $data['page_url'],
                'status' => 'new',
                'created_at' => $data['timestamp']
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );
        
        return $wpdb->insert_id;
    }
    
    /**
     * Enviar dados para Google Sheets via webhook
     */
    private function send_to_google_sheets($data) {
        if (empty($this->webhook_url)) {
            error_log('WP Resgate - Webhook URL não configurada');
            return array('success' => false, 'message' => 'Webhook URL não configurada');
        }
        
        // Gerar hash ID único
        $hash_id = $this->generate_hash_id($data['lead_id'] ?? 0, $data['email'], $data['timestamp']);
        
        // Preparar dados completos para Google Sheets (todas as 12 colunas)
        $sheets_data = array(
            'action' => $data['action'] ?? 'new_lead',
            'hash_id' => $hash_id,
            'lead_id' => $data['lead_id'] ?? 0,
            'timestamp' => $data['timestamp'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? '',
            'website' => $data['website'] ?? '',
            'problem_type' => $this->get_problem_type_label($data['problem_type'] ?? ''),
            'urgency' => $this->get_urgency_label($data['urgency'] ?? ''),
            'description' => $data['description'] ?? '',
            'status' => $data['status'] ?? 'new',
            'created_at' => $data['created_at'] ?? $data['timestamp'],
            'updated_at' => $data['updated_at'] ?? $data['timestamp']
        );
        
        // Debug: Log dos dados que serão enviados
        error_log('WP Resgate - Enviando para Google Sheets: ' . json_encode($sheets_data));
        error_log('WP Resgate - Webhook URL: ' . $this->webhook_url);
        
        // Fazer requisição HTTP
        $response = wp_remote_post($this->webhook_url, array(
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode($sheets_data),
            'timeout' => 30,
            'sslverify' => false // Para desenvolvimento local
        ));
        
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            error_log('WP Resgate - Erro na requisição: ' . $error_message);
            return array(
                'success' => false, 
                'message' => $error_message
            );
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        
        // Debug: Log da resposta
        error_log('WP Resgate - Resposta do Google Sheets - Code: ' . $response_code);
        error_log('WP Resgate - Resposta do Google Sheets - Body: ' . $response_body);
        
        $success = $response_code === 200;
        
        return array(
            'success' => $success,
            'message' => $response_body,
            'code' => $response_code
        );
    }
    
    /**
     * Enviar email de notificação
     */
    private function send_notification_email($data) {
        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');
        
        $subject = sprintf('[%s] Novo diagnóstico solicitado - %s', $site_name, $data['name']);
        
        $message = "Novo diagnóstico solicitado:\n\n";
        $message .= "Nome: {$data['name']}\n";
        $message .= "Email: {$data['email']}\n";
        $message .= "Telefone: {$data['phone']}\n";
        $message .= "Website: {$data['website']}\n";
        $message .= "Tipo de problema: " . $this->get_problem_type_label($data['problem_type']) . "\n";
        $message .= "Urgência: " . $this->get_urgency_label($data['urgency']) . "\n";
        $message .= "Descrição: {$data['description']}\n\n";
        $message .= "Data/Hora: {$data['timestamp']}\n";
        $message .= "IP: {$data['ip_address']}\n";
        $message .= "Página: {$data['page_url']}\n";
        
        wp_mail($admin_email, $subject, $message);
    }
    
    /**
     * Criar tabela de leads
     */
    private function create_leads_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'wp_resgate_leads';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(20),
            website varchar(255),
            problem_type varchar(50),
            urgency varchar(20),
            description text,
            source varchar(50),
            ip_address varchar(45),
            user_agent text,
            page_url varchar(255),
            status varchar(20) DEFAULT 'new',
            deleted_at datetime NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Obter IP do cliente
     */
    private function get_client_ip() {
        $ip_keys = array('HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR');
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) && !empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                return sanitize_text_field(trim($ip));
            }
        }
        
        return 'unknown';
    }
    
    /**
     * Verificação básica de spam
     */
    private function is_spam($data) {
        // Verificações básicas de spam
        $spam_keywords = array('viagra', 'cialis', 'casino', 'poker', 'loan', 'credit');
        $text = strtolower($data['description'] . ' ' . $data['name']);
        
        foreach ($spam_keywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return true;
            }
        }
        
        // Verificar se tem muitos links
        if (substr_count($data['description'], 'http') > 2) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Labels para tipos de problema
     */
    private function get_problem_type_label($type) {
        $types = array(
            'malware' => 'Malware/Hack',
            'error' => 'Erros 500/502',
            'migration' => 'Migração',
            'performance' => 'Performance',
            'maintenance' => 'Manutenção',
            'other' => 'Outro'
        );
        
        return $types[$type] ?? $type;
    }
    
    /**
     * Labels para urgência
     */
    private function get_urgency_label($urgency) {
        $urgencies = array(
            'low' => 'Baixa',
            'medium' => 'Média',
            'high' => 'Alta',
            'critical' => 'Crítica'
        );
        
        return $urgencies[$urgency] ?? $urgency;
    }
    
    /**
     * Gerar hash ID único para identificação na planilha
     */
    private function generate_hash_id($lead_id, $email, $timestamp) {
        // Criar hash baseado em dados únicos
        $unique_string = $lead_id . '|' . $email . '|' . $timestamp . '|' . get_option('siteurl');
        return 'WPR_' . substr(md5($unique_string), 0, 12); // 12 caracteres + prefixo
    }
    
    /**
     * Atualizar lead existente no Google Sheets
     */
    public function update_lead_in_sheets($lead_id, $lead_data) {
        if (empty($this->webhook_url)) {
            return array(
                'success' => false,
                'message' => 'URL do webhook não configurada'
            );
        }
        
        // Buscar dados completos do lead
        global $wpdb;
        $table_name = $wpdb->prefix . 'wp_resgate_leads';
        $lead = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $lead_id));
        
        if (!$lead) {
            return array(
                'success' => false,
                'message' => 'Lead não encontrado'
            );
        }
        
        // Gerar hash ID para busca (usar mesmo critério da criação)
        $hash_id = $this->generate_hash_id($lead_id, $lead->email, $lead->created_at);
        
        // Preparar dados para envio
        $update_data = array(
            'action' => 'update_lead',
            'hash_id' => $hash_id,
            'lead_id' => $lead_id,
            'timestamp' => current_time('Y-m-d H:i:s'),
            'name' => $lead->name,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'website' => $lead->website,
            'problem_type' => $this->get_problem_type_label($lead->problem_type),
            'urgency' => $this->get_urgency_label($lead->urgency),
            'description' => $lead->description,
            'status' => $lead->status,
            'created_at' => $lead->created_at,
            'updated_at' => current_time('Y-m-d H:i:s')
        );
        
        return $this->send_to_google_sheets($update_data);
    }
    
    /**
     * Deletar lead do Google Sheets
     */
    public function delete_lead_from_sheets($lead_id, $lead_data = null) {
        if (empty($this->webhook_url)) {
            return array(
                'success' => false,
                'message' => 'URL do webhook não configurada'
            );
        }
        
        // Gerar hash ID para busca (baseado nos dados do lead)
        $hash_id = $this->generate_hash_id($lead_id, $lead_data['email'] ?? '', $lead_data['created_at'] ?? '');
        
        $delete_data = array(
            'action' => 'delete_lead',
            'hash_id' => $hash_id,
            'lead_id' => $lead_id,
            'timestamp' => current_time('Y-m-d H:i:s'),
            'email' => $lead_data['email'] ?? '',
            'name' => $lead_data['name'] ?? ''
        );
        
        return $this->send_to_google_sheets($delete_data);
    }
    
    /**
     * Sincronizar status em massa
     */
    public function bulk_update_sheets($lead_ids, $action, $new_status = null) {
        if (empty($this->webhook_url)) {
            return array(
                'success' => false,
                'message' => 'URL do webhook não configurada'
            );
        }
        
        $results = array();
        
        foreach ($lead_ids as $lead_id) {
            if ($action === 'delete') {
                $result = $this->delete_lead_from_sheets($lead_id);
            } else {
                // Atualizar status
                global $wpdb;
                $table_name = $wpdb->prefix . 'wp_resgate_leads';
                
                // Atualizar no banco primeiro
                $wpdb->update(
                    $table_name,
                    array('status' => $new_status),
                    array('id' => $lead_id),
                    array('%s'),
                    array('%d')
                );
                
                // Sincronizar com Google Sheets
                $result = $this->update_lead_in_sheets($lead_id, array('status' => $new_status));
            }
            
            $results[] = $result;
        }
        
        return $results;
    }
}

// Inicializar a classe
new WP_Resgate_Google_Sheets();
