#!/bin/bash

# 🚀 Script de Preparação para Hospedagem Compartilhada
# WP Resgate - Automatiza a preparação dos arquivos para upload

echo "🚀 WP RESGATE - Preparação para Hospedagem Compartilhada"
echo "=================================================="

# Verificar se está na pasta correta
if [ ! -f "docker-compose.yml" ]; then
    echo "❌ Erro: Execute este script na pasta raiz do projeto (onde está o docker-compose.yml)"
    exit 1
fi

# Criar pasta de produção
PROD_DIR="wp-resgate-producao"
echo "📁 Criando pasta de produção: $PROD_DIR"
rm -rf $PROD_DIR
mkdir -p $PROD_DIR

# Copiar tema
echo "🎨 Copiando tema wp-resgate..."
if [ -d "./wordpress/wp-content/themes/wp-resgate" ]; then
    cp -r ./wordpress/wp-content/themes/wp-resgate $PROD_DIR/
    echo "✅ Tema copiado com sucesso"
else
    echo "❌ Erro: Tema wp-resgate não encontrado"
    exit 1
fi

# Copiar uploads se existir
if [ -d "./wordpress/wp-content/uploads" ]; then
    echo "📷 Copiando uploads..."
    cp -r ./wordpress/wp-content/uploads $PROD_DIR/
    echo "✅ Uploads copiados"
else
    echo "ℹ️  Pasta uploads não encontrada (normal se não há mídia)"
fi

# Criar wp-config.php template
echo "⚙️ Criando wp-config.php template..."
cat > $PROD_DIR/wp-config-template.php << 'EOF'
<?php
/**
 * Configuração WordPress para Produção - WP Resgate
 * IMPORTANTE: Substitua os valores com os dados da sua hospedagem
 */

// ** Configurações do MySQL - ALTERAR COM SEUS DADOS ** //
define('DB_NAME', 'SEU_DATABASE_NAME');
define('DB_USER', 'SEU_DATABASE_USER'); 
define('DB_PASSWORD', 'SUA_SENHA_DATABASE');
define('DB_HOST', 'localhost'); // ou IP fornecido pela hospedagem
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

// ** URLs do Site - ALTERAR COM SEU DOMÍNIO ** //
define('WP_HOME','https://seudominio.com.br');
define('WP_SITEURL','https://seudominio.com.br');

// ** Chaves de Segurança - GERAR NOVAS EM: https://api.wordpress.org/secret-key/1.1/salt/ ** //
define('AUTH_KEY',         'cole-aqui-chave-gerada');
define('SECURE_AUTH_KEY',  'cole-aqui-chave-gerada');
define('LOGGED_IN_KEY',    'cole-aqui-chave-gerada');
define('NONCE_KEY',        'cole-aqui-chave-gerada');
define('AUTH_SALT',        'cole-aqui-chave-gerada');
define('SECURE_AUTH_SALT', 'cole-aqui-chave-gerada');
define('LOGGED_IN_SALT',   'cole-aqui-chave-gerada');
define('NONCE_SALT',       'cole-aqui-chave-gerada');

// ** Configurações de Produção ** //
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY', false);
define('DISALLOW_FILE_EDIT', true);
define('DISALLOW_FILE_MODS', false); // true para maior segurança
define('WP_POST_REVISIONS', 5);
define('EMPTY_TRASH_DAYS', 7);
define('WP_MEMORY_LIMIT', '256M');

// ** Força SSL no Admin ** //
define('FORCE_SSL_ADMIN', true);

// ** Tabela WordPress ** //
$table_prefix = 'wp_';

// ** WordPress Language ** //
define('WPLANG', 'pt_BR');

// ** Não edite abaixo desta linha ** //
if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');

require_once(ABSPATH . 'wp-settings.php');
EOF

echo "✅ wp-config template criado"

# Criar .htaccess otimizado
echo "🔧 Criando .htaccess otimizado..."
cat > $PROD_DIR/.htaccess << 'EOF'
# WP Resgate - .htaccess Otimizado para Produção

# Segurança
<Files wp-config.php>
order allow,deny
deny from all
</Files>

<Files "wp-config-template.php">
order allow,deny
deny from all
</Files>

# Cache do Navegador
<IfModule mod_expires.c>
ExpiresActive On
ExpiresByType text/css "access plus 1 month"
ExpiresByType application/javascript "access plus 1 month"
ExpiresByType image/png "access plus 1 year"
ExpiresByType image/jpg "access plus 1 year"
ExpiresByType image/jpeg "access plus 1 year"
ExpiresByType image/gif "access plus 1 year"
ExpiresByType image/webp "access plus 1 year"
ExpiresByType image/svg+xml "access plus 1 year"
ExpiresByType font/woff "access plus 1 year"
ExpiresByType font/woff2 "access plus 1 year"
</IfModule>

# Compressão GZIP
<IfModule mod_deflate.c>
AddOutputFilterByType DEFLATE text/plain
AddOutputFilterByType DEFLATE text/html
AddOutputFilterByType DEFLATE text/xml
AddOutputFilterByType DEFLATE text/css
AddOutputFilterByType DEFLATE application/xml
AddOutputFilterByType DEFLATE application/xhtml+xml
AddOutputFilterByType DEFLATE application/rss+xml
AddOutputFilterByType DEFLATE application/javascript
AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# WordPress Rewrite Rules
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress
EOF

echo "✅ .htaccess criado"

# Exportar banco de dados
echo "🗃️ Exportando banco de dados..."
if docker ps | grep -q "wordpress_db"; then
    docker exec wordpress_db mysqldump -u root -p${DB_ROOT_PASSWORD:-rootpassword} wordpress_db > $PROD_DIR/wp-resgate-database.sql 2>/dev/null
    if [ $? -eq 0 ]; then
        echo "✅ Database exportado: wp-resgate-database.sql"
    else
        echo "⚠️  Erro ao exportar database. Exporte manualmente via phpMyAdmin:"
        echo "   http://localhost:8091"
    fi
else
    echo "⚠️  Container do database não está rodando"
    echo "   Execute 'docker-compose up -d' primeiro ou exporte via phpMyAdmin"
    echo "   http://localhost:8091"
fi

# Criar arquivo de instruções
echo "📋 Criando arquivo de instruções..."
cat > $PROD_DIR/INSTRUCOES-UPLOAD.md << 'EOF'
# 🚀 Instruções de Upload - WP Resgate

## 📋 Checklist Pré-Upload

### 1. **Preparar Hospedagem**
- [ ] WordPress instalado na hospedagem
- [ ] Database MySQL criado
- [ ] Usuário do database criado com todas as permissões
- [ ] SSL configurado (certificado HTTPS)

### 2. **Configurar Arquivos**
- [ ] Editar `wp-config-template.php` com dados da hospedagem
- [ ] Renomear para `wp-config.php`
- [ ] Gerar novas chaves de segurança: https://api.wordpress.org/secret-key/1.1/salt/

### 3. **Upload dos Arquivos**
- [ ] Upload da pasta `wp-resgate/` para `wp-content/themes/`
- [ ] Upload do `wp-config.php` para raiz do WordPress
- [ ] Upload do `.htaccess` para raiz do WordPress
- [ ] Upload da pasta `uploads/` para `wp-content/` (se houver)

### 4. **Importar Database**
- [ ] Acessar phpMyAdmin da hospedagem
- [ ] Importar arquivo `wp-resgate-database.sql`
- [ ] Executar SQL para atualizar URLs:

```sql
UPDATE wp_options SET option_value = 'https://seudominio.com.br' WHERE option_name = 'home';
UPDATE wp_options SET option_value = 'https://seudominio.com.br' WHERE option_name = 'siteurl';
```

### 5. **Configurar WordPress**
- [ ] Acessar wp-admin da hospedagem
- [ ] Ativar tema "WP Resgate"
- [ ] Verificar Customizer > WP Resgate (WhatsApp, cores, etc.)
- [ ] Testar formulários e funcionalidades

## 🆘 Suporte
Em caso de problemas, verifique:
- Permissões de arquivo (644 para arquivos, 755 para pastas)
- Configurações do wp-config.php
- Logs de erro da hospedagem
EOF

# Compactar tudo
echo "📦 Compactando arquivos para upload..."
cd $PROD_DIR
zip -r ../wp-resgate-producao.zip . -x "*.DS_Store"
cd ..

echo ""
echo "🎉 PREPARAÇÃO CONCLUÍDA!"
echo "==============================================="
echo "📁 Pasta criada: $PROD_DIR/"
echo "📦 Arquivo ZIP: wp-resgate-producao.zip"
echo ""
echo "📋 Próximos passos:"
echo "1. Edite wp-config-template.php com dados da hospedagem"
echo "2. Renomeie para wp-config.php"
echo "3. Faça upload dos arquivos"
echo "4. Importe o database"
echo "5. Ative o tema no WordPress"
echo ""
echo "📖 Leia: $PROD_DIR/INSTRUCOES-UPLOAD.md"
echo ""
echo "✅ Pronto para hospedagem compartilhada!"