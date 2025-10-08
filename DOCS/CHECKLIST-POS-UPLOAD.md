# 🔍 Checklist de Verificação Pós-Upload

## ✅ **Testes Essenciais Após Upload**

### 🌐 **Acesso e URLs**
- [ ] Site carrega sem erros: `https://seudominio.com.br`
- [ ] Admin funciona: `https://seudominio.com.br/wp-admin`
- [ ] SSL ativo (cadeado verde no navegador)
- [ ] Redirecionamento HTTP → HTTPS funcionando

### 🎨 **Tema e Layout**
- [ ] Tema WP Resgate ativo e funcionando
- [ ] Seção "Soluções de emergência" carregando
- [ ] Seção "Como funciona" com animações
- [ ] Seção "Clientes satisfeitos" moderna
- [ ] Hero section com gradientes
- [ ] Menu de navegação funcionando
- [ ] Botões com cores padronizadas (Verde WhatsApp, Azul Diagnóstico)

### 📱 **Responsividade**
- [ ] Layout mobile funcionando
- [ ] Bottom navigation aparecendo no mobile
- [ ] Cards adaptando para tela pequena
- [ ] Texto legível em todos os tamanhos

### ⚙️ **Funcionalidades**
- [ ] Formulário de contato funcionando
- [ ] Botões WhatsApp redirecionando corretamente
- [ ] Links internos funcionando (#diagnostico, #servicos, etc.)
- [ ] Customizer acess ível: **Aparência > Personalizar > WP Resgate**

### 🔧 **Customizer WP Resgate**
- [ ] **Configurações Gerais**: Nome da empresa, descrição
- [ ] **Hero Banner**: Título, subtítulo, CTA funcionando
- [ ] **Cores**: Paleta de cores aplicada
- [ ] **WhatsApp**: Número configurado e botões funcionando

### 📊 **Performance**
- [ ] Site carregando em menos de 3 segundos
- [ ] CSS e JS minificados (se possível)
- [ ] Imagens carregando corretamente
- [ ] Sem erros 404 nos recursos

### 🔒 **Segurança**
- [ ] wp-config.php não acessível via browser
- [ ] Admin acessível apenas com login
- [ ] Não há informações sensíveis expostas
- [ ] Backup realizado

## 🐛 **Problemas Comuns e Soluções**

### ❌ **Site não carrega (Erro 500)**
```php
# Verificar wp-config.php
- Credenciais do database corretas?
- Chaves de segurança definidas?
- Sintaxe PHP correta?
```

### ❌ **CSS/JS não carregam**
```bash
# Verificar permissões
chmod 644 wp-content/themes/wp-resgate/style.css
chmod -R 755 wp-content/themes/wp-resgate/
```

### ❌ **Database connection error**
```sql
-- Testar conexão no phpMyAdmin
-- Verificar se usuário tem permissões:
GRANT ALL PRIVILEGES ON database_name.* TO 'username'@'localhost';
FLUSH PRIVILEGES;
```

### ❌ **URLs quebradas**
```sql
-- Atualizar URLs no database
UPDATE wp_options SET option_value = 'https://seudominio.com.br' WHERE option_name = 'home';
UPDATE wp_options SET option_value = 'https://seudominio.com.br' WHERE option_name = 'siteurl';
```

### ❌ **WhatsApp não funciona**
```php
// Verificar no Customizer
Aparência > Personalizar > WP Resgate > Configurações Gerais
- Campo WhatsApp preenchido?
- Formato: apenas números (5511999999999)
```

## 📈 **Otimizações Pós-Upload**

### 🚀 **Performance**
- [ ] Instalar plugin de cache (W3 Total Cache)
- [ ] Otimizar imagens (Smush ou similar)
- [ ] Configurar CDN se necessário
- [ ] Habilitar compressão GZIP

### 🔐 **Segurança**
- [ ] Instalar Wordfence Security
- [ ] Configurar backup automático
- [ ] Atualizar WordPress para última versão
- [ ] Configurar SSL force redirect

### 📊 **Analytics**
- [ ] Google Analytics configurado
- [ ] Google Search Console
- [ ] Sitemap XML ativo
- [ ] Robots.txt configurado

## 🎯 **Configurações Avançadas**

### 📧 **Email/SMTP**
```php
// wp-config.php - para emails funcionarem
define('SMTP_USER', 'seu-email@dominio.com.br');
define('SMTP_PASS', 'sua-senha-email');
define('SMTP_HOST', 'mail.dominio.com.br');
define('SMTP_FROM', 'seu-email@dominio.com.br');
define('SMTP_NAME', 'WP Resgate');
define('SMTP_PORT', '587');
define('SMTP_SECURE', 'tls');
define('SMTP_AUTH', true);
```

### 🛡️ **Hardening Extra**
```php
// wp-config.php - segurança avançada
define('DISALLOW_FILE_EDIT', true);
define('DISALLOW_FILE_MODS', true);
define('AUTOMATIC_UPDATER_DISABLED', true);
define('WP_AUTO_UPDATE_CORE', false);
```

---

## 📞 **Suporte**

Se encontrar problemas:
1. 📋 Verifique logs de erro da hospedagem
2. 🔍 Use Developer Tools do navegador (F12)
3. 📧 Entre em contato com suporte da hospedagem
4. 💬 Consulte documentação do WordPress

**💡 Dica:** Sempre teste em subdomínio primeiro!