/**
 * JavaScript para Painel Administrativo dos Leads
 * WP Resgate - Interface de gestão de leads
 */

(function($) {
    'use strict';
    
    class WPResgateLeadsAdmin {
        constructor() {
            this.init();
        }
        
        init() {
            this.bindEvents();
            this.setupCheckboxes();
        }
        
        bindEvents() {
            // Toggle de detalhes do lead
            $(document).on('click', '.lead-details-toggle, .view-details', (e) => {
                e.preventDefault();
                const leadId = $(e.target).data('lead-id');
                this.toggleLeadDetails(leadId);
            });
            
            // Atualizar status via select
            $(document).on('change', '.status-select', (e) => {
                const leadId = $(e.target).data('lead-id');
                const newStatus = $(e.target).val();
                this.updateLeadStatus(leadId, newStatus);
            });
            
            // Atualizar urgência via select
            $(document).on('change', '.urgency-select', (e) => {
                const leadId = $(e.target).data('lead-id');
                const newUrgency = $(e.target).val();
                this.updateLeadUrgency(leadId, newUrgency);
            });
            
            // Excluir lead
            $(document).on('click', '.delete-lead', (e) => {
                e.preventDefault();
                const leadId = $(e.target).data('lead-id');
                this.deleteLead(leadId);
            });
            
            // Checkbox "selecionar todos"
            $('#cb-select-all').on('change', (e) => {
                $('input[name="lead_ids[]"]').prop('checked', e.target.checked);
            });
            
            // Teste de integração
            $('#test-integration').on('click', () => {
                this.testIntegration();
            });
            
            // Auto-refresh da página (a cada 5 minutos)
            if (window.location.href.indexOf('wp-resgate-leads') > -1) {
                setTimeout(() => {
                    if (document.visibilityState === 'visible') {
                        location.reload();
                    }
                }, 300000); // 5 minutos
            }
        }
        
        setupCheckboxes() {
            // Atualizar estado do "selecionar todos" baseado nos checkboxes individuais
            $(document).on('change', 'input[name="lead_ids[]"]', () => {
                const totalCheckboxes = $('input[name="lead_ids[]"]').length;
                const checkedCheckboxes = $('input[name="lead_ids[]"]:checked').length;
                
                $('#cb-select-all').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
                $('#cb-select-all').prop('checked', checkedCheckboxes === totalCheckboxes);
            });
        }
        
        toggleLeadDetails(leadId) {
            const detailsRow = $('#lead-details-' + leadId);
            
            if (detailsRow.is(':visible')) {
                detailsRow.hide();
            } else {
                // Esconder outros detalhes abertos
                $('.lead-details').hide();
                detailsRow.show();
                
                // Scroll para os detalhes
                $('html, body').animate({
                    scrollTop: detailsRow.offset().top - 100
                }, 300);
            }
        }
        
        updateLeadStatus(leadId, newStatus) {
            const $select = $(`.status-select[data-lead-id="${leadId}"]`);
            const originalStatus = $select.data('original-status') || $select.val();
            
            // Salvar status original para rollback em caso de erro
            if (!$select.data('original-status')) {
                $select.data('original-status', originalStatus);
            }
            
            // Mostrar loading
            $select.addClass('loading').prop('disabled', true);
            
            $.ajax({
                url: wpResgateAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'wp_resgate_update_lead_status',
                    nonce: wpResgateAdmin.nonce,
                    lead_id: leadId,
                    status: newStatus
                },
                success: (response) => {
                    if (response.success) {
                        this.showNotice(wpResgateAdmin.strings.success, 'success');
                        $select.data('original-status', newStatus);
                        
                        // Atualizar indicador visual se necessário
                        const $row = $(`.lead-row[data-lead-id="${leadId}"]`);
                        const $indicator = $row.find('.new-indicator');
                        
                        if (newStatus !== 'new' && $indicator.length) {
                            $indicator.remove();
                        } else if (newStatus === 'new' && !$indicator.length) {
                            $row.find('.column-name strong a').after('<span class="new-indicator">●</span>');
                        }
                        
                    } else {
                        this.showNotice(response.data.message, 'error');
                        $select.val(originalStatus); // Rollback
                    }
                },
                error: () => {
                    this.showNotice(wpResgateAdmin.strings.error, 'error');
                    $select.val(originalStatus); // Rollback
                },
                complete: () => {
                    $select.removeClass('loading').prop('disabled', false);
                }
            });
        }
        
        updateLeadUrgency(leadId, newUrgency) {
            const $select = $(`.urgency-select[data-lead-id="${leadId}"]`);
            const originalUrgency = $select.data('original-urgency') || $select.val();
            
            // Salvar urgência original para rollback em caso de erro
            if (!$select.data('original-urgency')) {
                $select.data('original-urgency', originalUrgency);
            }
            
            // Mostrar loading
            $select.addClass('loading').prop('disabled', true);
            
            $.ajax({
                url: wpResgateAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'wp_resgate_update_lead_urgency',
                    nonce: wpResgateAdmin.nonce,
                    lead_id: leadId,
                    urgency: newUrgency
                },
                success: (response) => {
                    if (response.success) {
                        this.showNotice('Urgência atualizada com sucesso!', 'success');
                        $select.data('original-urgency', newUrgency);
                        
                        // Atualizar classe CSS para cores visuais
                        const $row = $(`.lead-row[data-lead-id="${leadId}"]`);
                        $row.removeClass('urgency-low urgency-medium urgency-high urgency-critical');
                        $row.addClass('urgency-' + newUrgency);
                        
                    } else {
                        this.showNotice(response.data.message, 'error');
                        $select.val(originalUrgency); // Rollback
                    }
                },
                error: () => {
                    this.showNotice('Erro ao atualizar urgência!', 'error');
                    $select.val(originalUrgency); // Rollback
                },
                complete: () => {
                    $select.removeClass('loading').prop('disabled', false);
                }
            });
        }
        
        deleteLead(leadId) {
            if (!confirm(wpResgateAdmin.strings.confirm_delete)) {
                return;
            }
            
            const $button = $(`.delete-lead[data-lead-id="${leadId}"]`);
            const $row = $(`.lead-row[data-lead-id="${leadId}"]`);
            
            // Mostrar loading
            $button.addClass('loading').prop('disabled', true);
            
            $.ajax({
                url: wpResgateAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'wp_resgate_delete_lead',
                    nonce: wpResgateAdmin.nonce,
                    lead_id: leadId
                },
                success: (response) => {
                    if (response.success) {
                        this.showNotice('Lead movido para lixeira com sucesso!', 'success');
                        
                        // Animar remoção da linha
                        $row.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Esconder detalhes se estiverem abertos
                            $('#lead-details-' + leadId).remove();
                            
                            // Atualizar contadores e refresh da página
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        });
                        
                    } else {
                        this.showNotice(response.data.message, 'error');
                        $button.removeClass('loading').prop('disabled', false);
                    }
                },
                error: () => {
                    this.showNotice(wpResgateAdmin.strings.error, 'error');
                    $button.removeClass('loading').prop('disabled', false);
                }
            });
        }
        
        testIntegration() {
            const $button = $('#test-integration');
            const $result = $('#test-result');
            
            $button.addClass('loading').prop('disabled', true);
            $result.hide().removeClass('success error');
            
            // Dados de teste
            const testData = {
                values: [[
                    new Date().toLocaleString('pt-BR'),
                    'Teste Integração',
                    'teste@wpresgate.com',
                    '(11) 99999-9999',
                    'https://exemplo.com',
                    'Teste',
                    'Baixa',
                    'Este é um teste da integração com Google Sheets',
                    'admin_test',
                    '127.0.0.1',
                    window.location.href
                ]]
            };
            
            // Fazer requisição para o webhook
            const webhookUrl = $('input[name="webhook_url"]').val();
            
            if (!webhookUrl) {
                $result.addClass('error').text('URL do webhook não configurada').show();
                $button.removeClass('loading').prop('disabled', false);
                return;
            }
            
            $.ajax({
                url: webhookUrl,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(testData),
                timeout: 30000,
                success: (response) => {
                    $result.addClass('success').text('✅ Integração funcionando! Dados enviados para Google Sheets com sucesso.').show();
                },
                error: (xhr, status, error) => {
                    let errorMessage = '❌ Erro na integração: ';
                    
                    if (status === 'timeout') {
                        errorMessage += 'Timeout - verifique a URL do webhook';
                    } else if (xhr.responseText) {
                        errorMessage += xhr.responseText;
                    } else {
                        errorMessage += error || 'Erro desconhecido';
                    }
                    
                    $result.addClass('error').text(errorMessage).show();
                },
                complete: () => {
                    $button.removeClass('loading').prop('disabled', false);
                }
            });
        }
        
        showNotice(message, type) {
            // Remover notices existentes
            $('.wp-resgate-notice').remove();
            
            const noticeClass = type === 'success' ? 'notice-success' : 'notice-error';
            const notice = $(`
                <div class="notice ${noticeClass} is-dismissible wp-resgate-notice">
                    <p>${message}</p>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                </div>
            `);
            
            // Adicionar notice
            $('.wrap h1').after(notice);
            
            // Auto-hide após 5 segundos
            setTimeout(() => {
                notice.fadeOut();
            }, 5000);
            
            // Bind dismiss button
            notice.find('.notice-dismiss').on('click', function() {
                notice.fadeOut();
            });
        }
        
        // Função utilitária para formatar datas
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        // Função para exportar dados (futura implementação)
        exportLeads(format) {
            // TODO: Implementar exportação CSV/Excel
            console.log('Exportar leads em formato:', format);
        }
    }
    
    // Inicializar quando o documento estiver pronto
    $(document).ready(() => {
        if (window.location.href.indexOf('wp-resgate-leads') > -1) {
            new WPResgateLeadsAdmin();
        }
    });
    
    // Funções globais para compatibilidade
    window.wpResgateLeadsAdmin = {
        updateStatus: function(leadId, status) {
            // Função global para atualizar status (se necessário)
        },
        
        refreshStats: function() {
            // Função para atualizar estatísticas (futura implementação)
            location.reload();
        }
    };
    
})(jQuery);

// Utility functions fora do jQuery
document.addEventListener('DOMContentLoaded', function() {
    // Melhorar acessibilidade
    const tables = document.querySelectorAll('.leads-table');
    tables.forEach(table => {
        table.setAttribute('role', 'table');
        
        const headers = table.querySelectorAll('th');
        headers.forEach((header, index) => {
            header.setAttribute('scope', 'col');
        });
    });
    
    // Keyboard navigation para detalhes
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            // Fechar detalhes abertos com ESC
            const openDetails = document.querySelectorAll('.lead-details:not([style*="display: none"])');
            openDetails.forEach(detail => {
                detail.style.display = 'none';
            });
        }
    });
});