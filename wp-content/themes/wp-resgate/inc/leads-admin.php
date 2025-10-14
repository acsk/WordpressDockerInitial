<?php
/**
 * Painel de Administração dos Leads - WP Resgate
 * Sistema completo de gestão de leads no WordPress Admin
 */

class WP_Resgate_Leads_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_wp_resgate_update_lead_status', array($this, 'update_lead_status'));
        add_action('wp_ajax_wp_resgate_update_lead_urgency', array($this, 'update_lead_urgency'));
        add_action('wp_ajax_wp_resgate_delete_lead', array($this, 'delete_lead'));
        add_action('wp_ajax_wp_resgate_export_leads', array($this, 'export_leads'));
        add_action('wp_ajax_wp_resgate_test_webhook', array($this, 'test_webhook'));
        add_action('admin_post_wp_resgate_bulk_action', array($this, 'handle_bulk_actions'));
    }
    
    /**
     * Adicionar menu no admin
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Leads WP Resgate', 'wp-resgate'),
            __('Leads', 'wp-resgate'), 
            'manage_options',
            'wp-resgate-leads',
            array($this, 'leads_page'),
            'dashicons-email-alt',
            30
        );
        
        add_submenu_page(
            'wp-resgate-leads',
            __('Estatísticas', 'wp-resgate'),
            __('Estatísticas', 'wp-resgate'),
            'manage_options',
            'wp-resgate-stats',
            array($this, 'stats_page')
        );
        
        add_submenu_page(
            'wp-resgate-leads',
            __('Lixeira', 'wp-resgate'),
            __('Lixeira', 'wp-resgate'),
            'manage_options',
            'wp-resgate-trash',
            array($this, 'trash_page')
        );
        
        add_submenu_page(
            'wp-resgate-leads',
            __('Configurações', 'wp-resgate'),
            __('Configurações', 'wp-resgate'),
            'manage_options',
            'wp-resgate-leads-settings',
            array($this, 'settings_page')
        );
    }
    
    /**
     * Enfileirar scripts do admin
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'wp-resgate-leads') !== false) {
            wp_enqueue_style(
                'wp-resgate-admin',
                get_template_directory_uri() . '/assets/css/admin-leads.css',
                array(),
                '1.0.0'
            );
            
            wp_enqueue_script(
                'wp-resgate-admin',
                get_template_directory_uri() . '/assets/js/admin-leads.js',
                array('jquery'),
                '1.0.0',
                true
            );
            
            wp_localize_script('wp-resgate-admin', 'wpResgateAdmin', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wp_resgate_admin_nonce'),
                'strings' => array(
                    'confirm_delete' => __('Tem certeza que deseja excluir este lead?', 'wp-resgate'),
                    'confirm_bulk_delete' => __('Tem certeza que deseja excluir os leads selecionados?', 'wp-resgate'),
                    'success' => __('Ação realizada com sucesso!', 'wp-resgate'),
                    'error' => __('Erro ao realizar ação!', 'wp-resgate')
                )
            ));
        }
    }
    
    /**
     * Página principal dos leads
     */
    public function leads_page() {
        global $wpdb;
        
        // Processar ações
        $this->process_actions();
        
        // Parâmetros de paginação e filtros
        $per_page = 20;
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($current_page - 1) * $per_page;
        
        $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
        $urgency_filter = isset($_GET['urgency']) ? sanitize_text_field($_GET['urgency']) : '';
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        
        // Construir query
        $table_name = $wpdb->prefix . 'wp_resgate_leads';
        $where_conditions = array('1=1'); // Remover filtro deleted_at até implementarmos soft delete
        $where_values = array();
        
        if ($status_filter) {
            $where_conditions[] = 'status = %s';
            $where_values[] = $status_filter;
        }
        
        if ($urgency_filter) {
            $where_conditions[] = 'urgency = %s';
            $where_values[] = $urgency_filter;
        }
        
        if ($search) {
            $where_conditions[] = '(name LIKE %s OR email LIKE %s OR description LIKE %s)';
            $search_term = '%' . $wpdb->esc_like($search) . '%';
            $where_values[] = $search_term;
            $where_values[] = $search_term;
            $where_values[] = $search_term;
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        // Query para contar total
        $count_query = "SELECT COUNT(*) FROM $table_name WHERE $where_clause";
        if (!empty($where_values)) {
            $count_query = $wpdb->prepare($count_query, $where_values);
        }
        $total_items = $wpdb->get_var($count_query);
        
        // Query para buscar leads
        $leads_query = "SELECT * FROM $table_name WHERE $where_clause ORDER BY created_at DESC LIMIT %d OFFSET %d";
        $query_values = array_merge($where_values, array($per_page, $offset));
        $leads = $wpdb->get_results($wpdb->prepare($leads_query, $query_values));
        
        // Calcular páginas
        $total_pages = ceil($total_items / $per_page);
        
        // Estatísticas rápidas
        $stats = $this->get_quick_stats();
        
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">
                <?php esc_html_e('Leads WP Resgate', 'wp-resgate'); ?>
                <span class="title-count">(<?php echo $total_items; ?>)</span>
            </h1>
            
            <a href="#" class="page-title-action" onclick="location.reload();">
                <?php esc_html_e('Atualizar', 'wp-resgate'); ?>
            </a>
            
            <hr class="wp-header-end">
            
            <!-- Estatísticas Rápidas -->
            <div class="wp-resgate-stats-cards">
                <div class="stats-card">
                    <div class="stats-number"><?php echo $stats['total']; ?></div>
                    <div class="stats-label"><?php esc_html_e('Total de Leads', 'wp-resgate'); ?></div>
                </div>
                <div class="stats-card">
                    <div class="stats-number"><?php echo $stats['new']; ?></div>
                    <div class="stats-label"><?php esc_html_e('Novos', 'wp-resgate'); ?></div>
                </div>
                <div class="stats-card">
                    <div class="stats-number"><?php echo $stats['contacted']; ?></div>
                    <div class="stats-label"><?php esc_html_e('Contatados', 'wp-resgate'); ?></div>
                </div>
                <div class="stats-card critical">
                    <div class="stats-number"><?php echo $stats['critical']; ?></div>
                    <div class="stats-label"><?php esc_html_e('Críticos', 'wp-resgate'); ?></div>
                </div>
            </div>
            
            <!-- Filtros -->
            <div class="tablenav top">
                <form method="get" class="leads-filters">
                    <input type="hidden" name="page" value="wp-resgate-leads">
                    
                    <select name="status">
                        <option value=""><?php esc_html_e('Todos os status', 'wp-resgate'); ?></option>
                        <option value="new" <?php selected($status_filter, 'new'); ?>><?php esc_html_e('Novo', 'wp-resgate'); ?></option>
                        <option value="contacted" <?php selected($status_filter, 'contacted'); ?>><?php esc_html_e('Contatado', 'wp-resgate'); ?></option>
                        <option value="in_progress" <?php selected($status_filter, 'in_progress'); ?>><?php esc_html_e('Em andamento', 'wp-resgate'); ?></option>
                        <option value="completed" <?php selected($status_filter, 'completed'); ?>><?php esc_html_e('Concluído', 'wp-resgate'); ?></option>
                        <option value="rejected" <?php selected($status_filter, 'rejected'); ?>><?php esc_html_e('Rejeitado', 'wp-resgate'); ?></option>
                    </select>
                    
                    <select name="urgency">
                        <option value=""><?php esc_html_e('Todas as urgências', 'wp-resgate'); ?></option>
                        <option value="critical" <?php selected($urgency_filter, 'critical'); ?>><?php esc_html_e('Crítica', 'wp-resgate'); ?></option>
                        <option value="high" <?php selected($urgency_filter, 'high'); ?>><?php esc_html_e('Alta', 'wp-resgate'); ?></option>
                        <option value="medium" <?php selected($urgency_filter, 'medium'); ?>><?php esc_html_e('Média', 'wp-resgate'); ?></option>
                        <option value="low" <?php selected($urgency_filter, 'low'); ?>><?php esc_html_e('Baixa', 'wp-resgate'); ?></option>
                    </select>
                    
                    <input type="text" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php esc_attr_e('Buscar leads...', 'wp-resgate'); ?>">
                    
                    <button type="submit" class="button"><?php esc_html_e('Filtrar', 'wp-resgate'); ?></button>
                    
                    <?php if ($status_filter || $urgency_filter || $search): ?>
                        <a href="<?php echo admin_url('admin.php?page=wp-resgate-leads'); ?>" class="button">
                            <?php esc_html_e('Limpar filtros', 'wp-resgate'); ?>
                        </a>
                    <?php endif; ?>
                </form>
                
                <!-- Ações em massa -->
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="bulk-actions-form">
                    <input type="hidden" name="action" value="wp_resgate_bulk_action">
                    <?php wp_nonce_field('wp_resgate_bulk_action', 'bulk_nonce'); ?>
                    
                    <select name="bulk_action">
                        <option value=""><?php esc_html_e('Ações em massa', 'wp-resgate'); ?></option>
                        <option value="mark_contacted"><?php esc_html_e('Marcar como contatado', 'wp-resgate'); ?></option>
                        <option value="mark_in_progress"><?php esc_html_e('Marcar como em andamento', 'wp-resgate'); ?></option>
                        <option value="mark_completed"><?php esc_html_e('Marcar como concluído', 'wp-resgate'); ?></option>
                        <option value="delete"><?php esc_html_e('Excluir', 'wp-resgate'); ?></option>
                    </select>
                    <button type="submit" class="button" onclick="return confirm('<?php esc_attr_e('Confirma esta ação em massa?', 'wp-resgate'); ?>')">
                        <?php esc_html_e('Aplicar', 'wp-resgate'); ?>
                    </button>
                </form>
            </div>
            
            <!-- Tabela de Leads -->
            <table class="wp-list-table widefat fixed striped leads-table">
                <thead>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <input type="checkbox" id="cb-select-all">
                        </td>
                        <th class="manage-column column-name"><?php esc_html_e('Nome', 'wp-resgate'); ?></th>
                        <th class="manage-column column-contact"><?php esc_html_e('Contato', 'wp-resgate'); ?></th>
                        <th class="manage-column column-problem"><?php esc_html_e('Problema', 'wp-resgate'); ?></th>
                        <th class="manage-column column-urgency"><?php esc_html_e('Urgência', 'wp-resgate'); ?></th>
                        <th class="manage-column column-status"><?php esc_html_e('Status', 'wp-resgate'); ?></th>
                        <th class="manage-column column-date"><?php esc_html_e('Data', 'wp-resgate'); ?></th>
                        <th class="manage-column column-actions"><?php esc_html_e('Ações', 'wp-resgate'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($leads)): ?>
                        <tr>
                            <td colspan="8" class="no-items">
                                <?php esc_html_e('Nenhum lead encontrado.', 'wp-resgate'); ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($leads as $lead): ?>
                            <tr class="lead-row" data-lead-id="<?php echo $lead->id; ?>">
                                <th class="check-column">
                                    <input type="checkbox" name="lead_ids[]" value="<?php echo $lead->id; ?>">
                                </th>
                                <td class="column-name">
                                    <strong>
                                        <a href="#" class="lead-details-toggle" data-lead-id="<?php echo $lead->id; ?>">
                                            <?php echo esc_html($lead->name); ?>
                                        </a>
                                    </strong>
                                    <?php if ($lead->status === 'new'): ?>
                                        <span class="new-indicator">●</span>
                                    <?php endif; ?>
                                </td>
                                <td class="column-contact">
                                    <div class="contact-info">
                                        <div>
                                            <a href="mailto:<?php echo esc_attr($lead->email); ?>">
                                                <?php echo esc_html($lead->email); ?>
                                            </a>
                                        </div>
                                        <?php if ($lead->phone): ?>
                                            <div>
                                                <a href="tel:<?php echo esc_attr($lead->phone); ?>">
                                                    <?php echo esc_html($lead->phone); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="column-problem">
                                    <div class="problem-type">
                                        <?php echo esc_html($this->get_problem_type_label($lead->problem_type)); ?>
                                    </div>
                                    <?php if ($lead->website): ?>
                                        <div class="website">
                                            <a href="<?php echo esc_url($lead->website); ?>" target="_blank">
                                                <?php echo esc_html($lead->website); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="column-urgency">
                                    <select class="urgency-select" data-lead-id="<?php echo $lead->id; ?>">
                                        <option value="low" <?php selected($lead->urgency, 'low'); ?>><?php esc_html_e('Baixa', 'wp-resgate'); ?></option>
                                        <option value="medium" <?php selected($lead->urgency, 'medium'); ?>><?php esc_html_e('Média', 'wp-resgate'); ?></option>
                                        <option value="high" <?php selected($lead->urgency, 'high'); ?>><?php esc_html_e('Alta', 'wp-resgate'); ?></option>
                                        <option value="critical" <?php selected($lead->urgency, 'critical'); ?>><?php esc_html_e('Crítica', 'wp-resgate'); ?></option>
                                    </select>
                                </td>
                                <td class="column-status">
                                    <select class="status-select" data-lead-id="<?php echo $lead->id; ?>">
                                        <option value="new" <?php selected($lead->status, 'new'); ?>><?php esc_html_e('Novo', 'wp-resgate'); ?></option>
                                        <option value="contacted" <?php selected($lead->status, 'contacted'); ?>><?php esc_html_e('Contatado', 'wp-resgate'); ?></option>
                                        <option value="in_progress" <?php selected($lead->status, 'in_progress'); ?>><?php esc_html_e('Em andamento', 'wp-resgate'); ?></option>
                                        <option value="completed" <?php selected($lead->status, 'completed'); ?>><?php esc_html_e('Concluído', 'wp-resgate'); ?></option>
                                        <option value="rejected" <?php selected($lead->status, 'rejected'); ?>><?php esc_html_e('Rejeitado', 'wp-resgate'); ?></option>
                                    </select>
                                </td>
                                <td class="column-date">
                                    <?php echo date_i18n('d/m/Y H:i', strtotime($lead->created_at)); ?>
                                </td>
                                <td class="column-actions">
                                    <button type="button" class="button button-small view-details" data-lead-id="<?php echo $lead->id; ?>">
                                        <?php esc_html_e('Ver', 'wp-resgate'); ?>
                                    </button>
                                    <button type="button" class="button button-small delete-lead" data-lead-id="<?php echo $lead->id; ?>">
                                        <?php esc_html_e('Excluir', 'wp-resgate'); ?>
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Detalhes do Lead (oculto por padrão) -->
                            <tr class="lead-details" id="lead-details-<?php echo $lead->id; ?>" style="display: none;">
                                <td colspan="8">
                                    <div class="lead-details-content">
                                        <h3><?php esc_html_e('Detalhes do Lead', 'wp-resgate'); ?></h3>
                                        
                                        <div class="lead-details-grid">
                                            <div class="detail-section">
                                                <h4><?php esc_html_e('Informações Pessoais', 'wp-resgate'); ?></h4>
                                                <p><strong><?php esc_html_e('Nome:', 'wp-resgate'); ?></strong> <?php echo esc_html($lead->name); ?></p>
                                                <p><strong><?php esc_html_e('Email:', 'wp-resgate'); ?></strong> <?php echo esc_html($lead->email); ?></p>
                                                <?php if ($lead->phone): ?>
                                                    <p><strong><?php esc_html_e('WhatsApp:', 'wp-resgate'); ?></strong> <?php echo esc_html($lead->phone); ?></p>
                                                <?php endif; ?>
                                                <?php if ($lead->website): ?>
                                                    <p><strong><?php esc_html_e('Website:', 'wp-resgate'); ?></strong> 
                                                        <a href="<?php echo esc_url($lead->website); ?>" target="_blank">
                                                            <?php echo esc_html($lead->website); ?>
                                                        </a>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="detail-section">
                                                <h4><?php esc_html_e('Problema', 'wp-resgate'); ?></h4>
                                                <p><strong><?php esc_html_e('Tipo:', 'wp-resgate'); ?></strong> <?php echo esc_html($this->get_problem_type_label($lead->problem_type)); ?></p>
                                                <p><strong><?php esc_html_e('Urgência:', 'wp-resgate'); ?></strong> <?php echo esc_html($this->get_urgency_label($lead->urgency)); ?></p>
                                                <p><strong><?php esc_html_e('Descrição:', 'wp-resgate'); ?></strong></p>
                                                <div class="description-box">
                                                    <?php echo nl2br(esc_html($lead->description)); ?>
                                                </div>
                                            </div>
                                            
                                            <div class="detail-section">
                                                <h4><?php esc_html_e('Informações Técnicas', 'wp-resgate'); ?></h4>
                                                <p><strong><?php esc_html_e('IP:', 'wp-resgate'); ?></strong> <?php echo esc_html($lead->ip_address); ?></p>
                                                <p><strong><?php esc_html_e('Fonte:', 'wp-resgate'); ?></strong> <?php echo esc_html($lead->source); ?></p>
                                                <p><strong><?php esc_html_e('Data/Hora:', 'wp-resgate'); ?></strong> <?php echo date_i18n('d/m/Y H:i:s', strtotime($lead->created_at)); ?></p>
                                                <?php if ($lead->page_url): ?>
                                                    <p><strong><?php esc_html_e('Página:', 'wp-resgate'); ?></strong> 
                                                        <a href="<?php echo esc_url($lead->page_url); ?>" target="_blank">
                                                            <?php echo esc_html($lead->page_url); ?>
                                                        </a>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Ações Rápidas -->
                                        <div class="lead-quick-actions">
                                            <h4><?php esc_html_e('Ações Rápidas', 'wp-resgate'); ?></h4>
                                            <a href="mailto:<?php echo esc_attr($lead->email); ?>?subject=<?php echo esc_attr(sprintf(__('Re: %s - WP Resgate', 'wp-resgate'), $lead->name)); ?>" class="button button-primary">
                                                <?php esc_html_e('Enviar Email', 'wp-resgate'); ?>
                                            </a>
                                            <?php if ($lead->phone): ?>
                                                <a href="tel:<?php echo esc_attr($lead->phone); ?>" class="button">
                                                    <?php esc_html_e('Ligar', 'wp-resgate'); ?>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($lead->website): ?>
                                                <a href="<?php echo esc_url($lead->website); ?>" target="_blank" class="button">
                                                    <?php esc_html_e('Visitar Site', 'wp-resgate'); ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Paginação -->
            <?php if ($total_pages > 1): ?>
                <div class="tablenav bottom">
                    <div class="tablenav-pages">
                        <?php
                        $pagination_args = array(
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'prev_text' => __('&laquo;'),
                            'next_text' => __('&raquo;'),
                            'total' => $total_pages,
                            'current' => $current_page
                        );
                        echo paginate_links($pagination_args);
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Processar ações
     */
    private function process_actions() {
        if (isset($_GET['action']) && isset($_GET['lead_id']) && wp_verify_nonce($_GET['_wpnonce'], 'wp_resgate_lead_action')) {
            $lead_id = intval($_GET['lead_id']);
            $action = sanitize_text_field($_GET['action']);
            
            global $wpdb;
            $table_name = $wpdb->prefix . 'wp_resgate_leads';
            
            switch ($action) {
                case 'delete':
                    $wpdb->delete($table_name, array('id' => $lead_id), array('%d'));
                    wp_redirect(admin_url('admin.php?page=wp-resgate-leads&message=deleted'));
                    exit;
                    break;
            }
        }
    }
    
    /**
     * Atualizar status do lead via AJAX
     */
    public function update_lead_status() {
        check_ajax_referer('wp_resgate_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $lead_id = intval($_POST['lead_id']);
        $new_status = sanitize_text_field($_POST['status']);
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'wp_resgate_leads';
        
        $result = $wpdb->update(
            $table_name,
            array('status' => $new_status, 'updated_at' => current_time('mysql')),
            array('id' => $lead_id),
            array('%s', '%s'),
            array('%d')
        );
        
        if ($result !== false) {
            // Sincronizar com Google Sheets
            $this->sync_lead_update_to_sheets($lead_id, array('status' => $new_status));
            
            wp_send_json_success(array('message' => __('Status atualizado com sucesso!', 'wp-resgate')));
        } else {
            wp_send_json_error(array('message' => __('Erro ao atualizar status!', 'wp-resgate')));
        }
    }
    
    /**
     * Atualizar urgência do lead via AJAX
     */
    public function update_lead_urgency() {
        check_ajax_referer('wp_resgate_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $lead_id = intval($_POST['lead_id']);
        $new_urgency = sanitize_text_field($_POST['urgency']);
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'wp_resgate_leads';
        
        $result = $wpdb->update(
            $table_name,
            array('urgency' => $new_urgency, 'updated_at' => current_time('mysql')),
            array('id' => $lead_id),
            array('%s', '%s'),
            array('%d')
        );
        
        if ($result !== false) {
            // Sincronizar com Google Sheets
            $this->sync_lead_update_to_sheets($lead_id, array('urgency' => $new_urgency));
            
            wp_send_json_success(array('message' => __('Urgência atualizada com sucesso!', 'wp-resgate')));
        } else {
            wp_send_json_error(array('message' => __('Erro ao atualizar urgência!', 'wp-resgate')));
        }
    }
    
    /**
     * Excluir lead via AJAX
     */
    public function delete_lead() {
        check_ajax_referer('wp_resgate_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $lead_id = intval($_POST['lead_id']);
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'wp_resgate_leads';
        
        // Buscar dados do lead antes de deletar
        $lead_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $lead_id), ARRAY_A);
        
        if (!$lead_data) {
            wp_send_json_error(array('message' => __('Lead não encontrado!', 'wp-resgate')));
            return;
        }
        
        // Deletar definitivamente o lead
        $result = $wpdb->delete(
            $table_name,
            array('id' => $lead_id),
            array('%d')
        );
        
        if ($result !== false) {
            // Sincronizar deleção com Google Sheets
            $this->sync_lead_deletion_to_sheets($lead_id, $lead_data);
            
            wp_send_json_success(array('message' => __('Lead excluído com sucesso!', 'wp-resgate')));
        } else {
            wp_send_json_error(array('message' => __('Erro ao excluir lead!', 'wp-resgate')));
        }
    }
    
    /**
     * Lidar com ações em massa
     */
    public function handle_bulk_actions() {
        if (!wp_verify_nonce($_POST['bulk_nonce'], 'wp_resgate_bulk_action')) {
            wp_die('Security check failed');
        }
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $action = sanitize_text_field($_POST['bulk_action']);
        $lead_ids = array_map('intval', $_POST['lead_ids'] ?? array());
        
        if (empty($action) || empty($lead_ids)) {
            wp_redirect(admin_url('admin.php?page=wp-resgate-leads&message=no_action'));
            exit;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'wp_resgate_leads';
        $placeholders = implode(',', array_fill(0, count($lead_ids), '%d'));
        
        switch ($action) {
            case 'mark_contacted':
            case 'mark_in_progress':
            case 'mark_completed':
                $status = str_replace('mark_', '', $action);
                $wpdb->query($wpdb->prepare(
                    "UPDATE $table_name SET status = %s, updated_at = %s WHERE id IN ($placeholders)",
                    array_merge(array($status, current_time('mysql')), $lead_ids)
                ));
                
                // Sincronizar com Google Sheets
                $this->sync_bulk_actions_to_sheets($lead_ids, 'update_status', $status);
                break;
                
            case 'delete':
                $wpdb->query($wpdb->prepare(
                    "DELETE FROM $table_name WHERE id IN ($placeholders)",
                    $lead_ids
                ));
                
                // Sincronizar com Google Sheets
                $this->sync_bulk_actions_to_sheets($lead_ids, 'delete');
                break;
        }
        
        wp_redirect(admin_url('admin.php?page=wp-resgate-leads&message=bulk_success'));
        exit;
    }
    
    /**
     * Página de estatísticas
     */
    public function stats_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wp_resgate_leads';
        
        // Estatísticas gerais
        $total_leads = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $leads_today = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE DATE(created_at) = %s",
            current_time('Y-m-d')
        ));
        $leads_this_week = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE created_at >= %s",
            date('Y-m-d', strtotime('-7 days'))
        ));
        $leads_this_month = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE created_at >= %s",
            date('Y-m-01')
        ));
        
        // Por status
        $status_stats = $wpdb->get_results(
            "SELECT status, COUNT(*) as count FROM $table_name GROUP BY status ORDER BY count DESC"
        );
        
        // Por urgência
        $urgency_stats = $wpdb->get_results(
            "SELECT urgency, COUNT(*) as count FROM $table_name GROUP BY urgency ORDER BY 
             CASE urgency 
                WHEN 'critical' THEN 1 
                WHEN 'high' THEN 2 
                WHEN 'medium' THEN 3 
                WHEN 'low' THEN 4 
                ELSE 5 
             END"
        );
        
        // Por tipo de problema
        $problem_stats = $wpdb->get_results(
            "SELECT problem_type, COUNT(*) as count FROM $table_name GROUP BY problem_type ORDER BY count DESC"
        );
        
        // Leads por dia (últimos 30 dias)
        $daily_stats = $wpdb->get_results($wpdb->prepare(
            "SELECT DATE(created_at) as date, COUNT(*) as count 
             FROM $table_name 
             WHERE created_at >= %s 
             GROUP BY DATE(created_at) 
             ORDER BY date DESC",
            date('Y-m-d', strtotime('-30 days'))
        ));
        
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Estatísticas dos Leads', 'wp-resgate'); ?></h1>
            
            <!-- Cards de estatísticas gerais -->
            <div class="wp-resgate-stats-dashboard">
                <div class="stats-card large">
                    <div class="stats-number"><?php echo $total_leads; ?></div>
                    <div class="stats-label"><?php esc_html_e('Total de Leads', 'wp-resgate'); ?></div>
                </div>
                <div class="stats-card">
                    <div class="stats-number"><?php echo $leads_today; ?></div>
                    <div class="stats-label"><?php esc_html_e('Hoje', 'wp-resgate'); ?></div>
                </div>
                <div class="stats-card">
                    <div class="stats-number"><?php echo $leads_this_week; ?></div>
                    <div class="stats-label"><?php esc_html_e('Esta Semana', 'wp-resgate'); ?></div>
                </div>
                <div class="stats-card">
                    <div class="stats-number"><?php echo $leads_this_month; ?></div>
                    <div class="stats-label"><?php esc_html_e('Este Mês', 'wp-resgate'); ?></div>
                </div>
            </div>
            
            <!-- Gráficos e tabelas de estatísticas -->
            <div class="stats-charts">
                <div class="chart-section">
                    <h3><?php esc_html_e('Por Status', 'wp-resgate'); ?></h3>
                    <table class="wp-list-table widefat">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Status', 'wp-resgate'); ?></th>
                                <th><?php esc_html_e('Quantidade', 'wp-resgate'); ?></th>
                                <th><?php esc_html_e('Porcentagem', 'wp-resgate'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($status_stats as $stat): ?>
                                <tr>
                                    <td><?php echo esc_html($this->get_status_label($stat->status)); ?></td>
                                    <td><?php echo $stat->count; ?></td>
                                    <td><?php echo $total_leads > 0 ? round(($stat->count / $total_leads) * 100, 1) : 0; ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="chart-section">
                    <h3><?php esc_html_e('Por Urgência', 'wp-resgate'); ?></h3>
                    <table class="wp-list-table widefat">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Urgência', 'wp-resgate'); ?></th>
                                <th><?php esc_html_e('Quantidade', 'wp-resgate'); ?></th>
                                <th><?php esc_html_e('Porcentagem', 'wp-resgate'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($urgency_stats as $stat): ?>
                                <tr>
                                    <td>
                                        <span class="urgency-badge urgency-<?php echo esc_attr($stat->urgency); ?>">
                                            <?php echo esc_html($this->get_urgency_label($stat->urgency)); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $stat->count; ?></td>
                                    <td><?php echo $total_leads > 0 ? round(($stat->count / $total_leads) * 100, 1) : 0; ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="chart-section">
                    <h3><?php esc_html_e('Por Tipo de Problema', 'wp-resgate'); ?></h3>
                    <table class="wp-list-table widefat">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Tipo', 'wp-resgate'); ?></th>
                                <th><?php esc_html_e('Quantidade', 'wp-resgate'); ?></th>
                                <th><?php esc_html_e('Porcentagem', 'wp-resgate'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($problem_stats as $stat): ?>
                                <tr>
                                    <td><?php echo esc_html($this->get_problem_type_label($stat->problem_type)); ?></td>
                                    <td><?php echo $stat->count; ?></td>
                                    <td><?php echo $total_leads > 0 ? round(($stat->count / $total_leads) * 100, 1) : 0; ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Testar integração com o webhook via AJAX.
     */
    public function test_webhook() {
        check_ajax_referer('wp_resgate_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permissão negada.', 'wp-resgate')));
        }

        $webhook_url = isset($_POST['webhook_url']) ? esc_url_raw(wp_unslash($_POST['webhook_url'])) : '';

        if (empty($webhook_url)) {
            wp_send_json_error(array('message' => __('URL do webhook não configurada.', 'wp-resgate')));
        }

        $timestamp = current_time('mysql');
        $payload = array(
            'action' => 'test_integration',
            'hash_id' => md5(uniqid('wp_resgate_test', true)),
            'lead_id' => 0,
            'timestamp' => $timestamp,
            'name' => 'Teste Integração',
            'email' => 'teste@wpresgate.com',
            'phone' => '(11) 99999-9999',
            'website' => home_url(),
            'problem_type' => 'Teste',
            'urgency' => 'Baixa',
            'description' => 'Teste automatizado do painel WP Resgate.',
            'status' => 'test',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        );

        $sslverify = apply_filters('wp_resgate_webhook_sslverify', !wp_resgate_is_local_environment());

        $response = wp_remote_post($webhook_url, array(
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode($payload),
            'timeout' => 30,
            'sslverify' => $sslverify,
        ));

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            error_log('WP Resgate - Teste de integração falhou: ' . $error_message);

            wp_send_json_error(array('message' => $error_message));
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        error_log(sprintf('WP Resgate - Teste webhook resposta (HTTP %1$d): %2$s', $code, $body));

        if ($code >= 200 && $code < 300) {
            wp_send_json_success(array(
                'message' => __('Integração funcionando! Dados de teste enviados com sucesso.', 'wp-resgate'),
                'response' => $body,
            ));
        }

        $formatted_message = sprintf(__('Resposta inesperada do webhook (HTTP %1$d): %2$s', 'wp-resgate'), $code, $body);
        wp_send_json_error(array('message' => $formatted_message));
    }
    
    /**
     * Página de configurações
     */
    public function settings_page() {
        if (isset($_POST['submit'])) {
            // Processar configurações
            $webhook_url = sanitize_url($_POST['webhook_url']);
            $sheet_id = sanitize_text_field($_POST['sheet_id']);
            $notification_email = sanitize_email($_POST['notification_email']);
            $recaptcha_site_key = sanitize_text_field($_POST['recaptcha_site_key'] ?? '');
            $recaptcha_secret_key = sanitize_text_field($_POST['recaptcha_secret_key'] ?? '');
            
            set_theme_mod('wp_resgate_webhook_url', $webhook_url);
            set_theme_mod('wp_resgate_sheet_id', $sheet_id);
            set_theme_mod('wp_resgate_notification_email', $notification_email);
            set_theme_mod('wp_resgate_recaptcha_site_key', $recaptcha_site_key);
            set_theme_mod('wp_resgate_recaptcha_secret_key', $recaptcha_secret_key);
            
            echo '<div class="notice notice-success"><p>' . __('Configurações salvas com sucesso!', 'wp-resgate') . '</p></div>';
        }
        
        $current_webhook = get_theme_mod('wp_resgate_webhook_url', '');
        $current_sheet_id = get_theme_mod('wp_resgate_sheet_id', '');
        $current_email = get_theme_mod('wp_resgate_notification_email', get_option('admin_email'));
        $current_recaptcha_site = get_theme_mod('wp_resgate_recaptcha_site_key', '');
        $current_recaptcha_secret = get_theme_mod('wp_resgate_recaptcha_secret_key', '');
        
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Configurações dos Leads', 'wp-resgate'); ?></h1>
            
            <form method="post" action="">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e('URL Webhook Google Sheets', 'wp-resgate'); ?></th>
                        <td>
                            <input type="url" name="webhook_url" value="<?php echo esc_attr($current_webhook); ?>" class="regular-text">
                            <p class="description"><?php esc_html_e('URL do webhook para integração com Google Sheets', 'wp-resgate'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('ID da Planilha Google', 'wp-resgate'); ?></th>
                        <td>
                            <input type="text" name="sheet_id" value="<?php echo esc_attr($current_sheet_id); ?>" class="regular-text">
                            <p class="description"><?php esc_html_e('ID da planilha Google Sheets (da URL)', 'wp-resgate'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Email de Notificação', 'wp-resgate'); ?></th>
                        <td>
                            <input type="email" name="notification_email" value="<?php echo esc_attr($current_email); ?>" class="regular-text">
                            <p class="description"><?php esc_html_e('Email que receberá notificações de novos leads', 'wp-resgate'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('reCAPTCHA Site Key', 'wp-resgate'); ?></th>
                        <td>
                            <input type="text" name="recaptcha_site_key" value="<?php echo esc_attr($current_recaptcha_site); ?>" class="regular-text">
                            <p class="description"><?php esc_html_e('Chave pública do reCAPTCHA v2 (tipo checkbox).', 'wp-resgate'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('reCAPTCHA Secret Key', 'wp-resgate'); ?></th>
                        <td>
                            <input type="text" name="recaptcha_secret_key" value="<?php echo esc_attr($current_recaptcha_secret); ?>" class="regular-text">
                            <p class="description"><?php esc_html_e('Chave secreta do reCAPTCHA v2 usada na validação do servidor.', 'wp-resgate'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <!-- Teste de integração -->
            <div class="integration-test">
                <h3><?php esc_html_e('Teste de Integração', 'wp-resgate'); ?></h3>
                <p><?php esc_html_e('Clique no botão abaixo para testar a integração com Google Sheets:', 'wp-resgate'); ?></p>
                <button type="button" class="button" id="test-integration">
                    <?php esc_html_e('Testar Integração', 'wp-resgate'); ?>
                </button>
                <div id="test-result" style="margin-top: 10px;"></div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Obter estatísticas rápidas
     */
    private function get_quick_stats() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wp_resgate_leads';
        
        return array(
            'total' => $wpdb->get_var("SELECT COUNT(*) FROM $table_name"),
            'new' => $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'new'"),
            'contacted' => $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'contacted'"),
            'critical' => $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE urgency = 'critical'")
        );
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
     * Labels para status
     */
    private function get_status_label($status) {
        $statuses = array(
            'new' => 'Novo',
            'contacted' => 'Contatado',
            'in_progress' => 'Em andamento',
            'completed' => 'Concluído',
            'rejected' => 'Rejeitado'
        );
        
        return $statuses[$status] ?? $status;
    }
    
    /**
     * Sincronizar atualização de lead com Google Sheets
     */
    private function sync_lead_update_to_sheets($lead_id, $updated_data) {
        // Verificar se a classe de integração existe
        if (!class_exists('WP_Resgate_Google_Sheets')) {
            return false;
        }
        
        // Obter instância da integração (assumindo que é um singleton)
        $google_sheets = new WP_Resgate_Google_Sheets();
        
        try {
            $result = $google_sheets->update_lead_in_sheets($lead_id, $updated_data);
            
            // Log do resultado (opcional)
            if (!$result['success']) {
                error_log('WP Resgate - Falha na sincronização de atualização: ' . $result['message']);
            }
            
            return $result['success'];
        } catch (Exception $e) {
            error_log('WP Resgate - Erro na sincronização: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Sincronizar deleção de lead com Google Sheets
     */
    private function sync_lead_deletion_to_sheets($lead_id, $lead_data) {
        // Verificar se a classe de integração existe
        if (!class_exists('WP_Resgate_Google_Sheets')) {
            return false;
        }
        
        // Obter instância da integração
        $google_sheets = new WP_Resgate_Google_Sheets();
        
        try {
            $result = $google_sheets->delete_lead_from_sheets($lead_id, $lead_data);
            
            // Log do resultado (opcional)
            if (!$result['success']) {
                error_log('WP Resgate - Falha na sincronização de deleção: ' . $result['message']);
            }
            
            return $result['success'];
        } catch (Exception $e) {
            error_log('WP Resgate - Erro na sincronização: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Sincronizar ações em massa com Google Sheets
     */
    private function sync_bulk_actions_to_sheets($lead_ids, $action, $new_status = null) {
        // Verificar se a classe de integração existe
        if (!class_exists('WP_Resgate_Google_Sheets')) {
            return false;
        }
        
        // Obter instância da integração
        $google_sheets = new WP_Resgate_Google_Sheets();
        
        try {
            $results = $google_sheets->bulk_update_sheets($lead_ids, $action, $new_status);
            
            // Log dos resultados
            $success_count = 0;
            foreach ($results as $result) {
                if ($result['success']) {
                    $success_count++;
                } else {
                    error_log('WP Resgate - Falha na sincronização em massa: ' . $result['message']);
                }
            }
            
            return $success_count;
        } catch (Exception $e) {
            error_log('WP Resgate - Erro na sincronização em massa: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Sincronizar soft delete com Google Sheets
     */
    private function sync_lead_soft_delete_to_sheets($lead_id, $lead_data) {
        // Verificar se a classe de integração existe
        if (!class_exists('WP_Resgate_Google_Sheets')) {
            return false;
        }
        
        // Obter instância da integração
        $google_sheets = new WP_Resgate_Google_Sheets();
        
        try {
            // Em vez de deletar, marcar como deletado
            $result = $google_sheets->update_lead_in_sheets($lead_id, array(
                'status' => 'deleted',
                'deleted_at' => current_time('Y-m-d H:i:s')
            ));
            
            // Log do resultado
            if (!$result['success']) {
                error_log('WP Resgate - Falha na sincronização de soft delete: ' . $result['message']);
            }
            
            return $result['success'];
        } catch (Exception $e) {
            error_log('WP Resgate - Erro na sincronização de soft delete: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Página da lixeira
     */
    public function trash_page() {
        global $wpdb;
        
        // Parâmetros de paginação
        $per_page = 20;
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($current_page - 1) * $per_page;
        
        // Como não temos soft delete implementado, a lixeira está vazia
        $leads = array();
        $total_items = 0;
        $total_pages = 0;
        
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">
                <?php esc_html_e('Lixeira - Leads Excluídos', 'wp-resgate'); ?>
                <span class="title-count">(<?php echo $total_items; ?>)</span>
            </h1>
            
            <?php if ($total_items > 0): ?>
                <div class="tablenav top">
                    <div class="alignleft actions bulkactions">
                        <select name="action" id="bulk-action-selector-top">
                            <option value=""><?php esc_html_e('Ações em massa', 'wp-resgate'); ?></option>
                            <option value="restore"><?php esc_html_e('Restaurar', 'wp-resgate'); ?></option>
                            <option value="delete_permanently"><?php esc_html_e('Excluir permanentemente', 'wp-resgate'); ?></option>
                        </select>
                        <input type="submit" class="button action" value="<?php esc_attr_e('Aplicar', 'wp-resgate'); ?>">
                    </div>
                </div>
                
                <table class="wp-list-table widefat fixed striped leads-table">
                    <thead>
                        <tr>
                            <td class="manage-column column-cb check-column">
                                <input id="cb-select-all" type="checkbox">
                            </td>
                            <th class="manage-column column-name"><?php esc_html_e('Nome', 'wp-resgate'); ?></th>
                            <th class="manage-column column-contact"><?php esc_html_e('Contato', 'wp-resgate'); ?></th>
                            <th class="manage-column column-urgency"><?php esc_html_e('Urgência', 'wp-resgate'); ?></th>
                            <th class="manage-column column-deleted"><?php esc_html_e('Excluído em', 'wp-resgate'); ?></th>
                            <th class="manage-column column-actions"><?php esc_html_e('Ações', 'wp-resgate'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leads as $lead): ?>
                            <tr class="lead-row" data-lead-id="<?php echo $lead->id; ?>">
                                <td class="check-column">
                                    <input type="checkbox" name="lead_ids[]" value="<?php echo $lead->id; ?>">
                                </td>
                                <td class="column-name">
                                    <strong>
                                        <?php echo esc_html($lead->name); ?>
                                    </strong>
                                </td>
                                <td class="column-contact">
                                    <div class="contact-info">
                                        <div><strong><?php echo esc_html($lead->email); ?></strong></div>
                                        <?php if ($lead->phone): ?>
                                            <div><?php echo esc_html($lead->phone); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="column-urgency">
                                    <span class="urgency-badge urgency-<?php echo esc_attr($lead->urgency); ?>">
                                        <?php echo esc_html($this->get_urgency_label($lead->urgency)); ?>
                                    </span>
                                </td>
                                <td class="column-deleted">
                                    <?php echo date_i18n('d/m/Y H:i', strtotime($lead->deleted_at)); ?>
                                </td>
                                <td class="column-actions">
                                    <button type="button" class="button button-small restore-lead" data-lead-id="<?php echo $lead->id; ?>">
                                        <?php esc_html_e('Restaurar', 'wp-resgate'); ?>
                                    </button>
                                    <button type="button" class="button button-small delete-permanently" data-lead-id="<?php echo $lead->id; ?>">
                                        <?php esc_html_e('Excluir definitivamente', 'wp-resgate'); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if ($total_pages > 1): ?>
                    <div class="tablenav bottom">
                        <div class="tablenav-pages">
                            <?php
                            echo paginate_links(array(
                                'base' => add_query_arg('paged', '%#%'),
                                'format' => '',
                                'prev_text' => __('&laquo;'),
                                'next_text' => __('&raquo;'),
                                'total' => $total_pages,
                                'current' => $current_page
                            ));
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="no-items">
                    <p><?php esc_html_e('Nenhum lead na lixeira.', 'wp-resgate'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}
