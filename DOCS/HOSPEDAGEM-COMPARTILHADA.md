# 🚀 Guia: Como Subir o WP Resgate para Hospedagem Compartilhada

## 📋 **Preparação Pré-Upload**

### 1. **Configurações de Database**

#### **Backup do Banco Local**
```bash
# 1. Exportar banco de dados local
docker exec wordpress_db mysqldump -u root -p wordpress_db > wp-resgate-backup.sql

# 2. Ou via phpMyAdmin
# Acesse: http://localhost:8091
# Vá em "Exportar" > "Método personalizado" > "SQL"
```

#### **Configurar wp-config.php**
```php
// wp-config.php para produção
define('DB_NAME', 'seu_database_name');
define('DB_USER', 'seu_database_user');
define('DB_PASSWORD', 'sua_senha_forte');
define('DB_HOST', 'localhost'); // ou IP fornecido pela hospedagem

// Configurações de segurança para produção
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY', false);

// Chaves de segurança - GERAR NOVAS em https://api.wordpress.org/secret-key/1.1/salt/
define('AUTH_KEY',         'nova-chave-aqui');
define('SECURE_AUTH_KEY',  'nova-chave-aqui');
define('LOGGED_IN_KEY',    'nova-chave-aqui');
define('NONCE_KEY',        'nova-chave-aqui');
define('AUTH_SALT',        'nova-chave-aqui');
define('SECURE_AUTH_SALT', 'nova-chave-aqui');
define('LOGGED_IN_SALT',   'nova-chave-aqui');
define('NONCE_SALT',       'nova-chave-aqui');

// URLs de produção
define('WP_HOME','https://seudominio.com.br');
define('WP_SITEURL','https://seudominio.com.br');
```

### 2. **Otimizações para Produção**

#### **Performance do Tema**
```php
// Adicionar ao functions.php
function wp_resgate_optimize_production() {
    if (!is_admin()) {
        // Desabilitar emojis se não necessário
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        
        // Remover versões de CSS/JS para cache
        add_filter('style_loader_src', 'remove_version_from_style', 9999);
        add_filter('script_loader_src', 'remove_version_from_script', 9999);
    }
}
add_action('init', 'wp_resgate_optimize_production');

function remove_version_from_style($src) {
    return remove_query_arg('ver', $src);
}

function remove_version_from_script($src) {
    return remove_query_arg('ver', $src);
}
```

### 3. **Estrutura de Arquivos para Upload**

```
📁 Arquivos para Enviar:
├── 📁 wp-content/
│   ├── 📁 themes/
│   │   └── 📁 wp-resgate/ (TODO o tema)
│   ├── 📁 plugins/ (se houver plugins personalizados)
│   └── 📁 uploads/ (imagens e mídia)
├── 🗃️ wp-resgate-backup.sql (banco de dados)
└── 📄 wp-config.php (configurado para produção)
```

## 🛠️ **Processo de Upload**

### Passo 1: **Preparar Arquivos**
```bash
# Criar pasta de produção
mkdir wp-resgate-producao

# Copiar apenas o necessário
cp -r ./wordpress/wp-content/themes/wp-resgate/ wp-resgate-producao/
cp -r ./wordpress/wp-content/uploads/ wp-resgate-producao/ (se houver)

# Compactar para upload
zip -r wp-resgate-tema.zip wp-resgate-producao/
```

### Passo 2: **Na Hospedagem Compartilhada**

#### **Via cPanel/Painel de Controle:**
1. 📁 **File Manager** → Vá para `public_html/`
2. 🆕 **Fresh Install** → Instale WordPress limpo
3. 📤 **Upload Tema** → `wp-content/themes/`
4. 🗃️ **Import Database** → phpMyAdmin
5. ⚙️ **Ativar Tema** → WordPress Admin

#### **Via FTP:**
```bash
# Conectar via FTP
ftp seusite.com.br
# user: seu_usuario_ftp
# pass: sua_senha_ftp

# Navegar para pasta correta
cd public_html/wp-content/themes/

# Upload do tema
put wp-resgate/ (pasta completa)
```

### Passo 3: **Configurar Database**

#### **Criar Database na Hospedagem:**
1. 🗃️ **cPanel** → MySQL Databases
2. ➕ **Create Database:** `usuario_wpresgate`
3. 👤 **Create User:** `usuario_wpresgate_user`
4. 🔐 **Strong Password:** Use gerador
5. 🔗 **Add User to Database:** All privileges

#### **Importar Dados:**
1. 📊 **phpMyAdmin** → Select database
2. 📥 **Import** → Choose file (`wp-resgate-backup.sql`)
3. 🔄 **Execute** → Wait for completion

#### **URLs no Database (IMPORTANTE!):**
```sql
-- Atualizar URLs no banco
UPDATE wp_options SET option_value = 'https://seudominio.com.br' WHERE option_name = 'home';
UPDATE wp_options SET option_value = 'https://seudominio.com.br' WHERE option_name = 'siteurl';

-- Atualizar URLs em posts/páginas (se necessário)
UPDATE wp_posts SET post_content = REPLACE(post_content, 'http://localhost:8090', 'https://seudominio.com.br');
```

## 🔧 **Configurações Específicas da Hospedagem**

### **Limites de Hosting Compartilhado:**
```php
// wp-config.php - Otimizações para shared hosting
define('WP_MEMORY_LIMIT', '256M'); // ou o máximo permitido
define('WP_MAX_MEMORY_LIMIT', '512M');

// Desabilitar file editing no admin
define('DISALLOW_FILE_EDIT', true);

// Limitar revisões de posts
define('WP_POST_REVISIONS', 5);

// Limitar lixeira
define('EMPTY_TRASH_DAYS', 7);
```

### **.htaccess Otimizado:**
```apache
# Segurança
<Files wp-config.php>
order allow,deny
deny from all
</Files>

# Cache do navegador
<IfModule mod_expires.c>
ExpiresActive On
ExpiresByType text/css "access plus 1 month"
ExpiresByType application/javascript "access plus 1 month"
ExpiresByType image/png "access plus 1 year"
ExpiresByType image/jpg "access plus 1 year"
ExpiresByType image/jpeg "access plus 1 year"
ExpiresByType image/gif "access plus 1 year"
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

# WordPress padrão
# BEGIN WordPress
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
# END WordPress
```

## 🔒 **Segurança em Produção**

### **Hardening WordPress:**
```php
// wp-config.php
// Desabilitar instalação de plugins/temas via admin
define('DISALLOW_FILE_MODS', true);

// Forçar SSL Admin
define('FORCE_SSL_ADMIN', true);

// Ocultar versão do WordPress
function wp_resgate_remove_version() {
    return '';
}
add_filter('the_generator', 'wp_resgate_remove_version');
```

### **Plugins Recomendados para Produção:**
1. 🛡️ **Wordfence Security** (segurança)
2. 🚀 **W3 Total Cache** (performance)
3. 📱 **Contact Form 7** (formulários)
4. 🔄 **UpdraftPlus** (backup)

## ✅ **Checklist Final**

- [ ] ✅ WordPress instalado na hospedagem
- [ ] 🗃️ Database criado e importado
- [ ] 📁 Tema wp-resgate uploadado
- [ ] ⚙️ wp-config.php configurado
- [ ] 🌐 URLs atualizadas no database
- [ ] 🎨 Tema ativado no WordPress Admin
- [ ] 📱 WhatsApp configurado no Customizer
- [ ] 🔒 SSL configurado (https://)
- [ ] 📊 Analytics configurado (se necessário)
- [ ] 🧪 Testes de funcionalidade

## 🆘 **Problemas Comuns**

### **CSS/JS não carregam:**
```php
// Verificar se o caminho está correto
// No functions.php
function wp_resgate_check_assets() {
    $theme_uri = get_template_directory_uri();
    error_log('Theme URI: ' . $theme_uri);
}
```

### **Database connection error:**
- ✅ Verificar credenciais em wp-config.php
- ✅ Confirmar que database foi criado
- ✅ Testar conexão com usuário/senha

### **URLs quebradas:**
```sql
-- Executar no phpMyAdmin se URLs estiverem erradas
UPDATE wp_options SET option_value = 'https://seudominio.com.br' WHERE option_name = 'home';
UPDATE wp_options SET option_value = 'https://seudominio.com.br' WHERE option_name = 'siteurl';
```

---

**💡 Dica Extra:** Sempre faça backup antes de qualquer alteração e teste tudo em um subdomínio primeiro (ex: `teste.seudominio.com.br`).