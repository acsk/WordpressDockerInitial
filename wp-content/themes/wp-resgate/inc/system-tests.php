<?php
/**
 * Módulo de testes do sistema WP Resgate.
 *
 * Exibe verificações rápidas para confirmar se componentes críticos estão operando.
 *
 * @package WP_Resgate
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Resgate_System_Tests {
    private const AJAX_ACTION = 'wp_resgate_run_system_tests';
    private const NONCE_ACTION = 'wp_resgate_system_tests';

    private const STATUS_SUCCESS = 'success';
    private const STATUS_WARNING = 'warning';
    private const STATUS_ERROR = 'error';

    /**
     * Inicializa hooks necessários.
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'register_admin_page'], 20);
        add_action('wp_ajax_' . self::AJAX_ACTION, [$this, 'handle_ajax_run_tests']);
        add_action('admin_init', [$this, 'maybe_redirect_pretty_url']);
    }

    /**
     * Registra submenu dentro do painel WP Resgate.
     */
    public function register_admin_page() {
        add_submenu_page(
            'wp-resgate-leads',
            __('Testes do Sistema', 'wp-resgate'),
            __('Testes', 'wp-resgate'),
            'manage_options',
            'wp-resgate-system-tests',
            [$this, 'render_page']
        );
    }

    /**
     * Exibe página principal dos testes.
     */
    public function render_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $tests = $this->get_tests();
        $results = $this->run_all_tests($tests);
        $nonce = wp_create_nonce(self::NONCE_ACTION);
        ?>
        <div class="wrap wp-resgate-system-tests">
            <h1><?php esc_html_e('Testes do Sistema WP Resgate', 'wp-resgate'); ?></h1>
            <p><?php esc_html_e('Use este painel para verificar rapidamente se os componentes essenciais estão configurados corretamente.', 'wp-resgate'); ?></p>

            <p>
                <button
                    class="button button-primary"
                    id="wp-resgate-run-tests"
                    data-nonce="<?php echo esc_attr($nonce); ?>"
                >
                    <?php esc_html_e('Executar testes novamente', 'wp-resgate'); ?>
                </button>
            </p>

            <table class="widefat striped">
                <thead>
                    <tr>
                        <th scope="col"><?php esc_html_e('Verificação', 'wp-resgate'); ?></th>
                        <th scope="col"><?php esc_html_e('Status', 'wp-resgate'); ?></th>
                        <th scope="col"><?php esc_html_e('Mensagem', 'wp-resgate'); ?></th>
                    </tr>
                </thead>
                <tbody id="wp-resgate-system-tests-results">
                <?php foreach ($tests as $test_id => $test_config) : ?>
                    <?php $result = $results[$test_id]; ?>
                    <tr data-test="<?php echo esc_attr($test_id); ?>">
                        <td>
                            <strong><?php echo esc_html($test_config['label']); ?></strong>
                            <?php if (!empty($test_config['description'])) : ?>
                                <p class="description"><?php echo esc_html($test_config['description']); ?></p>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="wp-resgate-status wp-resgate-status-<?php echo esc_attr($result['status']); ?>">
                                <?php echo esc_html($this->get_status_label($result['status'])); ?>
                            </span>
                        </td>
                        <td class="wp-resgate-message"><?php echo esc_html($result['message']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <style>
            .wp-resgate-system-tests .wp-resgate-status {
                display: inline-flex;
                align-items: center;
                padding: 2px 10px;
                border-radius: 12px;
                font-weight: 600;
                text-transform: uppercase;
                font-size: 11px;
                letter-spacing: 0.5px;
            }
            .wp-resgate-status-success {
                background-color: #e1f3d8;
                color: #1c7c22;
            }
            .wp-resgate-status-warning {
                background-color: #fff3cd;
                color: #665200;
            }
            .wp-resgate-status-error {
                background-color: #fde7e9;
                color: #921616;
            }
            .wp-resgate-system-tests .description {
                margin: 4px 0 0;
            }
        </style>

        <script>
            (function() {
                const runButton = document.getElementById('wp-resgate-run-tests');
                const resultsBody = document.getElementById('wp-resgate-system-tests-results');

                if (!runButton || !resultsBody) {
                    return;
                }

                runButton.addEventListener('click', function() {
                    const nonce = runButton.dataset.nonce;

                    runButton.disabled = true;
                    runButton.classList.add('updating-message');
                    runButton.textContent = '<?php echo esc_js(__('Executando...', 'wp-resgate')); ?>';

                    window.jQuery.post(
                        window.ajaxurl,
                        {
                            action: '<?php echo esc_js(self::AJAX_ACTION); ?>',
                            nonce: nonce
                        }
                    )
                    .done(function(response) {
                        if (!response || !response.success || !response.data || !response.data.results) {
                            throw new Error('Resposta inesperada do servidor');
                        }

                        const results = response.data.results;
                        Object.keys(results).forEach(function(testId) {
                            const row = resultsBody.querySelector('tr[data-test="' + testId + '"]');
                            if (!row) {
                                return;
                            }

                            const statusCell = row.querySelector('.wp-resgate-status');
                            const messageCell = row.querySelector('.wp-resgate-message');
                            const result = results[testId];

                            statusCell.textContent = result.status_label;
                            statusCell.className = 'wp-resgate-status wp-resgate-status-' + result.status;
                            messageCell.textContent = result.message;
                        });
                    })
                    .fail(function(error) {
                        window.alert('<?php echo esc_js(__('Erro ao executar os testes. Verifique o console para mais detalhes.', 'wp-resgate')); ?>');
                        if (window.console && error) {
                            console.error('WP Resgate System Tests:', error);
                        }
                    })
                    .always(function() {
                        runButton.disabled = false;
                        runButton.classList.remove('updating-message');
                        runButton.textContent = '<?php echo esc_js(__('Executar testes novamente', 'wp-resgate')); ?>';
                    });
                });
            })();
        </script>
        <?php
    }

    /**
     * Permite acessar a página usando rota amigável (ex.: wp-admin/wp-resgate-system-tests).
     *
     * Redireciona para admin.php?page=wp-resgate-system-tests evitando 404 do Apache.
     */
    public function maybe_redirect_pretty_url() {
        if (!is_admin()) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_GET['page']) && $_GET['page'] === 'wp-resgate-system-tests') {
            return;
        }

        $request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash((string) $_SERVER['REQUEST_URI']) : '';
        if ($request_uri === '') {
            return;
        }

        $question_pos = strpos($request_uri, '?');
        if ($question_pos !== false) {
            $request_uri = substr($request_uri, 0, $question_pos);
        }

        $admin_path = parse_url(admin_url(), PHP_URL_PATH);
        if (!is_string($admin_path)) {
            return;
        }

        $target_path = trailingslashit($admin_path) . 'wp-resgate-system-tests';

        if ($request_uri === $target_path || $request_uri === $target_path . '/') {
            wp_safe_redirect(admin_url('admin.php?page=wp-resgate-system-tests'));
            exit;
        }
    }

    /**
     * Executa os testes via AJAX.
     */
    public function handle_ajax_run_tests() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permissão negada.', 'wp-resgate')], 403);
        }

        check_ajax_referer(self::NONCE_ACTION, 'nonce');

        $tests = $this->get_tests();
        $results = $this->run_all_tests($tests, true);

        wp_send_json_success([
            'results' => $results,
        ]);
    }

    /**
     * Define os testes disponíveis.
     *
     * @return array<string, array{label:string,description:string,callback:callable}>
     */
    private function get_tests() {
        return [
            'database_connection' => [
                'label' => __('Conexão com o banco', 'wp-resgate'),
                'description' => __('Confirma se o WordPress consegue consultar o banco de dados.', 'wp-resgate'),
                'callback' => [$this, 'check_database_connection'],
            ],
            'leads_table' => [
                'label' => __('Tabela de leads', 'wp-resgate'),
                'description' => __('Verifica se a tabela wp_resgate_leads existe e possui colunas essenciais.', 'wp-resgate'),
                'callback' => [$this, 'check_leads_table'],
            ],
            'webhook_settings' => [
                'label' => __('Configuração Google Sheets', 'wp-resgate'),
                'description' => __('Valida se a URL do webhook e a planilha estão configuradas.', 'wp-resgate'),
                'callback' => [$this, 'check_webhook_settings'],
            ],
            'recaptcha' => [
                'label' => __('Proteção reCAPTCHA', 'wp-resgate'),
                'description' => __('Indica se as chaves do reCAPTCHA foram definidas (opcional, porém recomendado).', 'wp-resgate'),
                'callback' => [$this, 'check_recaptcha_configuration'],
            ],
            'ajax_handlers' => [
                'label' => __('Ações AJAX do formulário', 'wp-resgate'),
                'description' => __('Garante que o endpoint de captura de leads está registrado para usuários logados e visitantes.', 'wp-resgate'),
                'callback' => [$this, 'check_ajax_handlers'],
            ],
            'google_sheets_class' => [
                'label' => __('Classe de integração ativa', 'wp-resgate'),
                'description' => __('Confirma se a classe WP_Resgate_Google_Sheets está carregada.', 'wp-resgate'),
                'callback' => [$this, 'check_google_sheets_class'],
            ],
        ];
    }

    /**
     * Executa todos os testes.
     *
     * @param array<string, array{callback:callable}> $tests
     * @param bool $prepare_for_ajax Se true, inclui rótulos prontos para JS.
     * @return array<string, array<string, mixed>>
     */
    private function run_all_tests($tests, $prepare_for_ajax = false) {
        $results = [];

        foreach ($tests as $test_id => $test_config) {
            try {
                $callback = $test_config['callback'];
                $result = is_callable($callback) ? call_user_func($callback) : null;
                $results[$test_id] = $this->normalize_result($result);
            } catch (Throwable $exception) {
                $results[$test_id] = [
                    'status' => self::STATUS_ERROR,
                    'message' => sprintf(
                        /* translators: %s: mensagem de erro */
                        __('Erro ao executar teste: %s', 'wp-resgate'),
                        $exception->getMessage()
                    ),
                ];
            }

            if ($prepare_for_ajax) {
                $results[$test_id]['status_label'] = $this->get_status_label($results[$test_id]['status']);
            }
        }

        return $results;
    }

    /**
     * Normaliza o resultado de um teste.
     *
     * @param mixed $result
     * @return array{status:string,message:string,data?:array,status_label?:string}
     */
    private function normalize_result($result) {
        if (!is_array($result)) {
            $result = [
                'message' => is_scalar($result) ? (string) $result : '',
            ];
        }

        $defaults = [
            'status' => self::STATUS_SUCCESS,
            'message' => '',
            'data' => [],
        ];

        $normalized = array_merge($defaults, $result);

        if (!in_array($normalized['status'], [self::STATUS_SUCCESS, self::STATUS_WARNING, self::STATUS_ERROR], true)) {
            $normalized['status'] = self::STATUS_ERROR;
        }

        if ($normalized['message'] === '') {
            $normalized['message'] = $this->get_default_message_for_status($normalized['status']);
        }

        return $normalized;
    }

    /**
     * Traduz status para rótulo amigável.
     *
     * @param string $status
     * @return string
     */
    private function get_status_label($status) {
        switch ($status) {
            case self::STATUS_SUCCESS:
                return __('OK', 'wp-resgate');
            case self::STATUS_WARNING:
                return __('Atenção', 'wp-resgate');
            case self::STATUS_ERROR:
            default:
                return __('Erro', 'wp-resgate');
        }
    }

    /**
     * Mensagem padrão baseada no status.
     *
     * @param string $status
     * @return string
     */
    private function get_default_message_for_status($status) {
        switch ($status) {
            case self::STATUS_SUCCESS:
                return __('Teste executado com sucesso.', 'wp-resgate');
            case self::STATUS_WARNING:
                return __('Verifique os detalhes deste teste.', 'wp-resgate');
            case self::STATUS_ERROR:
            default:
                return __('Falha ao executar o teste.', 'wp-resgate');
        }
    }

    /**
     * Verifica a conexão básica com o banco de dados.
     *
     * @return array
     */
    private function check_database_connection() {
        global $wpdb;

        $wpdb->hide_errors();
        $value = $wpdb->get_var('SELECT 1');

        if ($wpdb->last_error) {
            return [
                'status' => self::STATUS_ERROR,
                'message' => sprintf(
                    /* translators: %s: mensagem de erro do banco */
                    __('Erro ao consultar o banco de dados: %s', 'wp-resgate'),
                    $wpdb->last_error
                ),
            ];
        }

        if ((int) $value !== 1) {
            return [
                'status' => self::STATUS_WARNING,
                'message' => sprintf(
                    /* translators: %s: valor retornado */
                    __('Resposta inesperada do banco de dados: %s', 'wp-resgate'),
                    is_scalar($value) ? (string) $value : __('valor inválido', 'wp-resgate')
                ),
                'data' => [
                    'value' => $value,
                ],
            ];
        }

        return [
            'status' => self::STATUS_SUCCESS,
            'message' => __('Conexão com o banco de dados OK.', 'wp-resgate'),
        ];
    }

    /**
     * Verifica se a tabela wp_resgate_leads existe e possui colunas essenciais.
     *
     * @return array
     */
    private function check_leads_table() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wp_resgate_leads';
        $table_exists = $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $table_name)
        );

        if (!$table_exists) {
            return [
                'status' => self::STATUS_ERROR,
                'message' => sprintf(
                    /* translators: %s: nome da tabela */
                    __('Tabela %s não encontrada. Execute o script de criação de leads.', 'wp-resgate'),
                    $table_name
                ),
            ];
        }

        $columns = $wpdb->get_results("SHOW COLUMNS FROM {$table_name}", ARRAY_A);
        if (!is_array($columns)) {
            return [
                'status' => self::STATUS_WARNING,
                'message' => __('Não foi possível inspecionar as colunas da tabela de leads.', 'wp-resgate'),
            ];
        }

        $column_names = array_map(
            static function ($column) {
                return $column['Field'] ?? '';
            },
            $columns
        );

        $required_columns = [
            'id',
            'name',
            'email',
            'phone',
            'website',
            'problem_type',
            'urgency',
            'description',
            'source',
            'status',
            'created_at',
            'updated_at',
        ];

        $missing_columns = array_values(array_diff($required_columns, $column_names));

        if (!empty($missing_columns)) {
            return [
                'status' => self::STATUS_WARNING,
                'message' => sprintf(
                    /* translators: %s: lista de colunas */
                    __('Colunas ausentes na tabela de leads: %s', 'wp-resgate'),
                    implode(', ', $missing_columns)
                ),
                'data' => [
                    'missing_columns' => $missing_columns,
                ],
            ];
        }

        return [
            'status' => self::STATUS_SUCCESS,
            'message' => __('Tabela de leads encontrada e com estrutura principal OK.', 'wp-resgate'),
            'data' => [
                'columns' => $column_names,
            ],
        ];
    }

    /**
     * Verifica configurações do webhook Google Sheets.
     *
     * @return array
     */
    private function check_webhook_settings() {
        $webhook_url = trim((string) get_theme_mod('wp_resgate_webhook_url', ''));
        $sheet_id = trim((string) get_theme_mod('wp_resgate_sheet_id', ''));

        if ($webhook_url === '' && $sheet_id === '') {
            return [
                'status' => self::STATUS_WARNING,
                'message' => __('Webhook e planilha não configurados. A integração com o Google Sheets está inativa.', 'wp-resgate'),
            ];
        }

        if ($webhook_url === '') {
            return [
                'status' => self::STATUS_WARNING,
                'message' => __('URL do webhook vazia. Configure-a no Customizer antes de publicar.', 'wp-resgate'),
            ];
        }

        if (!filter_var($webhook_url, FILTER_VALIDATE_URL)) {
            return [
                'status' => self::STATUS_ERROR,
                'message' => __('URL do webhook inválida. Revise o endereço informado.', 'wp-resgate'),
            ];
        }

        if ($sheet_id === '') {
            return [
                'status' => self::STATUS_WARNING,
                'message' => __('Webhook configurado, mas ID da planilha vazio. Atualize o campo correspondente no Customizer.', 'wp-resgate'),
            ];
        }

        return [
            'status' => self::STATUS_SUCCESS,
            'message' => __('Webhook e planilha configurados.', 'wp-resgate'),
        ];
    }

    /**
     * Verifica se o reCAPTCHA está configurado.
     *
     * @return array
     */
    private function check_recaptcha_configuration() {
        if (!function_exists('wp_resgate_is_recaptcha_enabled')) {
            return [
                'status' => self::STATUS_ERROR,
                'message' => __('Funções de reCAPTCHA não encontradas. Verifique o arquivo functions.php.', 'wp-resgate'),
            ];
        }

        if (wp_resgate_is_recaptcha_enabled()) {
            return [
                'status' => self::STATUS_SUCCESS,
                'message' => __('Chaves do reCAPTCHA configuradas.', 'wp-resgate'),
            ];
        }

        if (wp_resgate_should_skip_recaptcha()) {
            return [
                'status' => self::STATUS_WARNING,
                'message' => __('reCAPTCHA desabilitado em ambiente de desenvolvimento (ignorar se for proposital).', 'wp-resgate'),
            ];
        }

        return [
            'status' => self::STATUS_WARNING,
            'message' => __('reCAPTCHA não configurado. Recomenda-se habilitar antes da produção.', 'wp-resgate'),
        ];
    }

    /**
     * Verifica se handlers AJAX do formulário estão registrados.
     *
     * @return array
     */
    private function check_ajax_handlers() {
        $logged_handler = has_action('wp_ajax_wp_resgate_form_submit');
        $public_handler = has_action('wp_ajax_nopriv_wp_resgate_form_submit');

        if ($logged_handler && $public_handler) {
            return [
                'status' => self::STATUS_SUCCESS,
                'message' => __('Endpoints AJAX do formulário de leads registrados.', 'wp-resgate'),
            ];
        }

        if ($logged_handler || $public_handler) {
            return [
                'status' => self::STATUS_WARNING,
                'message' => __('Ação AJAX registrada apenas para um público. Verifique se ambos (logado e visitante) estão cobertos.', 'wp-resgate'),
            ];
        }

        return [
            'status' => self::STATUS_ERROR,
            'message' => __('Nenhum handler AJAX encontrado para o formulário de leads.', 'wp-resgate'),
        ];
    }

    /**
     * Garante que a classe de integração Google Sheets existe.
     *
     * @return array
     */
    private function check_google_sheets_class() {
        if (class_exists('WP_Resgate_Google_Sheets')) {
            return [
                'status' => self::STATUS_SUCCESS,
                'message' => __('Classe WP_Resgate_Google_Sheets carregada.', 'wp-resgate'),
            ];
        }

        return [
            'status' => self::STATUS_ERROR,
            'message' => __('Classe WP_Resgate_Google_Sheets não encontrada. Verifique se o arquivo foi carregado.', 'wp-resgate'),
        ];
    }
}

if (is_admin()) {
    new WP_Resgate_System_Tests();
}
