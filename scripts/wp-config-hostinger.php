<?php
/**
 * wp-config.php para Hostinger - WP Resgate
 * 
 * INSTRUÇÕES:
 * 1. Renomeie este arquivo para wp-config.php
 * 2. Substitua os dados do banco pelas credenciais da Hostinger
 * 3. Gere novas chaves de segurança em: https://api.wordpress.org/secret-key/1.1/salt/
 * 4. Ajuste o domínio nas constantes WP_HOME e WP_SITEURL
 */

// ** Configurações do banco de dados - Hostinger ** //
/** Nome do banco de dados */
define( 'DB_NAME', 'SEU_BANCO_HOSTINGER' );

/** Usuário do banco de dados */
define( 'DB_USER', 'SEU_USUARIO_HOSTINGER' );

/** Senha do banco de dados */
define( 'DB_PASSWORD', 'SUA_SENHA_HOSTINGER' );

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
 * e substitua as linhas abaixo
 */
define( 'AUTH_KEY',         'SUBSTITUA_ESTA_LINHA_COM_CHAVE_GERADA' );
define( 'SECURE_AUTH_KEY',  'SUBSTITUA_ESTA_LINHA_COM_CHAVE_GERADA' );
define( 'LOGGED_IN_KEY',    'SUBSTITUA_ESTA_LINHA_COM_CHAVE_GERADA' );
define( 'NONCE_KEY',        'SUBSTITUA_ESTA_LINHA_COM_CHAVE_GERADA' );
define( 'AUTH_SALT',        'SUBSTITUA_ESTA_LINHA_COM_CHAVE_GERADA' );
define( 'SECURE_AUTH_SALT', 'SUBSTITUA_ESTA_LINHA_COM_CHAVE_GERADA' );
define( 'LOGGED_IN_SALT',   'SUBSTITUA_ESTA_LINHA_COM_CHAVE_GERADA' );
define( 'NONCE_SALT',       'SUBSTITUA_ESTA_LINHA_COM_CHAVE_GERADA' );

/**#@-*/

/**
 * Prefixo da tabela do banco de dados
 */
$table_prefix = 'wp_';

/**
 * Configurações específicas do WP Resgate
 */

// URLs do site (SUBSTITUA pelo seu domínio)
define( 'WP_HOME', 'https://seu-dominio.com' );
define( 'WP_SITEURL', 'https://seu-dominio.com' );

// Configurações de upload
define( 'UPLOADS', 'wp-content/uploads' );

/**
 * Configurações de desenvolvimento/produção
 */

// Debug (desabilitado em produção)
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', false );

// Configurações de performance
define( 'WP_CACHE', true );
define( 'COMPRESS_CSS', true );
define( 'COMPRESS_SCRIPTS', true );
define( 'CONCATENATE_SCRIPTS', true );

// Configurações de segurança
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISALLOW_FILE_MODS', false ); // Permitir instalação de plugins/temas
define( 'FORCE_SSL_ADMIN', true );

// Configurações de memória
define( 'WP_MEMORY_LIMIT', '256M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );

// Configurações de revisões
define( 'WP_POST_REVISIONS', 3 );
define( 'AUTOSAVE_INTERVAL', 300 );

// Configurações de cookies
define( 'COOKIE_DOMAIN', '.seu-dominio.com' );

/**
 * Configurações específicas da Hostinger
 */

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de email (se necessário)
// define( 'SMTP_HOST', 'smtp.hostinger.com' );
// define( 'SMTP_PORT', 587 );
// define( 'SMTP_USER', 'noreply@seu-dominio.com' );
// define( 'SMTP_PASS', 'sua-senha-email' );

/**
 * Configurações avançadas (opcional)
 */

// Aumentar limites se necessário
// ini_set( 'upload_max_filesize', '32M' );
// ini_set( 'post_max_size', '32M' );
// ini_set( 'max_execution_time', 300 );

// Configurações de CRON (se necessário desabilitar)
// define( 'DISABLE_WP_CRON', true );

/* Adicione valores personalizados entre esta linha e "pare de editar". */

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Configura as variáveis e arquivos do WordPress. */
require_once ABSPATH . 'wp-settings.php';