<?php
/**
 * wp-config.php para Hostinger - WP Resgate PRODUÇÃO
 * Configurado para: wpprotegido.com.br
 * 
 * CREDENCIAIS HOSTINGER REAIS - JÁ CONFIGURADAS
 */

// ** Configurações do banco de dados - Hostinger PRODUÇÃO ** //
/** Nome do banco de dados */
define( 'DB_NAME', 'u304177849_db' );

/** Usuário do banco de dados */
define( 'DB_USER', 'u304177849_root' );

/** Senha do banco de dados */
define( 'DB_PASSWORD', 's*V06;M&ZKw' );

/** Servidor do banco de dados */
define( 'DB_HOST', 'localhost' );

/** Charset do banco de dados */
define( 'DB_CHARSET', 'utf8mb4' );

/** Collate do banco de dados */
define( 'DB_COLLATE', '' );

/**#@+
 * Chaves únicas de autenticação e salts.
 * 
 * IMPORTANTE: Gere novas chaves em https://api.wordpress.org/secret-key/1.1/salt/
 * e substitua as linhas abaixo por questões de segurança
 */
define( 'AUTH_KEY',         'coloque-aqui-uma-chave-unica-gerada-no-site-do-wordpress' );
define( 'SECURE_AUTH_KEY',  'coloque-aqui-uma-chave-unica-gerada-no-site-do-wordpress' );
define( 'LOGGED_IN_KEY',    'coloque-aqui-uma-chave-unica-gerada-no-site-do-wordpress' );
define( 'NONCE_KEY',        'coloque-aqui-uma-chave-unica-gerada-no-site-do-wordpress' );
define( 'AUTH_SALT',        'coloque-aqui-uma-chave-unica-gerada-no-site-do-wordpress' );
define( 'SECURE_AUTH_SALT', 'coloque-aqui-uma-chave-unica-gerada-no-site-do-wordpress' );
define( 'LOGGED_IN_SALT',   'coloque-aqui-uma-chave-unica-gerada-no-site-do-wordpress' );
define( 'NONCE_SALT',       'coloque-aqui-uma-chave-unica-gerada-no-site-do-wordpress' );

/**#@-*/

/**
 * Prefixo da tabela do banco de dados
 */
$table_prefix = 'wp_';

/**
 * Configurações específicas do WP Resgate - PRODUÇÃO
 */

// URLs do site - wpprotegido.com.br
define( 'WP_HOME', 'https://wpprotegido.com.br' );
define( 'WP_SITEURL', 'https://wpprotegido.com.br' );

// Configurações de upload
define( 'UPLOADS', 'wp-content/uploads' );

/**
 * Configurações de PRODUÇÃO
 */

// Debug DESABILITADO em produção
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', false );

// Configurações de performance
define( 'WP_CACHE', true );
define( 'COMPRESS_CSS', true );
define( 'COMPRESS_SCRIPTS', true );
define( 'CONCATENATE_SCRIPTS', true );

// Configurações de segurança PRODUÇÃO
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISALLOW_FILE_MODS', false ); // Permitir instalação de plugins/temas
define( 'FORCE_SSL_ADMIN', true );
define( 'WP_POST_REVISIONS', 3 );

// Configurações de memória otimizadas para Hostinger
define( 'WP_MEMORY_LIMIT', '256M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );

// Configurações de cookies para wpprotegido.com.br
define( 'COOKIE_DOMAIN', '.wpprotegido.com.br' );

/**
 * Configurações específicas da Hostinger
 */

// Timezone brasileiro
date_default_timezone_set('America/Sao_Paulo');

// Configurações de sessão
define( 'AUTOSAVE_INTERVAL', 300 ); // 5 minutos

// Configurações de CRON (otimizado para hospedagem compartilhada)
define( 'DISABLE_WP_CRON', false ); // Manter ativo inicialmente
define( 'WP_CRON_LOCK_TIMEOUT', 60 );

/**
 * Configurações de email (se necessário)
 */
// define( 'SMTP_HOST', 'smtp.hostinger.com' );
// define( 'SMTP_PORT', 587 );
// define( 'SMTP_USER', 'noreply@wpprotegido.com.br' );
// define( 'SMTP_PASS', 'sua-senha-email' );

/**
 * Configurações avançadas de performance
 */

// Otimizações de PHP para Hostinger
ini_set( 'upload_max_filesize', '32M' );
ini_set( 'post_max_size', '32M' );
ini_set( 'max_execution_time', 300 );
ini_set( 'max_input_vars', 3000 );

// Configurações de cache específicas
define( 'WP_CACHE_KEY_SALT', 'wpresgate_' );

/**
 * Configurações específicas WP Resgate
 */

// Configurações para sistema de leads
define( 'WP_RESGATE_VERSION', '1.0' );
define( 'WP_RESGATE_LEADS_TABLE', 'wp_wp_resgate_leads' );

// Configurações Google Sheets (já configuradas no tema)
// Webhook: https://script.google.com/macros/s/AKfycbzQwVfcbdBq9GPuXAWLgHX0DPaWKPFUxX_3dcJPqegxsJdnC9mo6TmhfO0lW7qEAdWR9g/exec
// Sheet ID: 1ldFjuPTPQvxUBuAmPtyF_lUkX-6In0dJW4SX_l8QP9k

/**
 * Configurações de backup e segurança
 */

// Configurações de backup (se usar plugin)
// define( 'UPDRAFTPLUS_BACKUP_DIR', 'wp-content/backups' );

// Desabilitar atualizações automáticas de plugins (controle manual)
// define( 'AUTOMATIC_UPDATER_DISABLED', true );

/* Adicione valores personalizados entre esta linha e "pare de editar". */

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Configura as variáveis e arquivos do WordPress. */
require_once ABSPATH . 'wp-settings.php';

/*
========================================================
CONFIGURAÇÃO COMPLETA - PRODUÇÃO wpprotegido.com.br
========================================================

✅ CREDENCIAIS CONFIGURADAS:
Base de dados: u304177849_db
Usuário: u304177849_root  
Senha: s*V06;M&ZKw
Servidor: localhost

✅ DOMÍNIO CONFIGURADO:
Site: https://wpprotegido.com.br
Admin: https://wpprotegido.com.br/wp-admin

✅ OTIMIZAÇÕES ATIVAS:
- Cache habilitado
- Compressão CSS/JS
- Configurações de performance
- Configurações brasileiras
- SSL forçado no admin
- Memória otimizada (256M)

🔐 PRÓXIMO PASSO:
1. Gerar chaves de segurança em:
   https://api.wordpress.org/secret-key/1.1/salt/
2. Substituir as 8 linhas de chaves acima
3. Salvar como wp-config.php na raiz

🎯 TUDO PRONTO PARA PRODUÇÃO!
*/