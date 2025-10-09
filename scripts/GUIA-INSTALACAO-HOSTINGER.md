# 🚀 Guia de Configuração - WP Resgate na Hostinger

## 📋 **Pré-requisitos**

- ✅ Conta Hostinger ativa
- ✅ Domínio configurado
- ✅ Banco de dados MySQL criado no painel Hostinger
- ✅ Deploy Git já configurado (arquivos WordPress já enviados)

## 🗄️ **1. Configuração do Banco de Dados**

### **Opção A: Importar Banco Completo**
1. **Acesse phpMyAdmin** da Hostinger
2. **Selecione seu banco** de dados
3. **Clique em "Importar"**
4. **Faça upload** do arquivo: `wp-resgate-database-export.sql`
5. **Execute a importação**

### **Opção B: Configuração Manual**
1. **Acesse phpMyAdmin** da Hostinger
2. **Execute o script**: `scripts/hostinger-database-setup.sql`
3. **Siga as instruções** no próprio script

## ⚙️ **2. Configuração do wp-config.php**

### **No painel de arquivos da Hostinger:**

1. **Acesse File Manager** → `public_html`
2. **Baixe o arquivo**: `scripts/wp-config-hostinger.php`
3. **Edite as seguintes linhas:**

```php
// ALTERAR: Dados do banco
define( 'DB_NAME', 'seu_banco_hostinger_aqui' );
define( 'DB_USER', 'seu_usuario_hostinger' );
define( 'DB_PASSWORD', 'sua_senha_hostinger' );

// ALTERAR: Seu domínio
define( 'WP_HOME', 'https://seu-dominio.com' );
define( 'WP_SITEURL', 'https://seu-dominio.com' );
define( 'COOKIE_DOMAIN', '.seu-dominio.com' );
```

4. **Gere novas chaves** em: https://api.wordpress.org/secret-key/1.1/salt/
5. **Substitua as 8 linhas** de chaves de segurança
6. **Salve como** `wp-config.php` na raiz do `public_html`

## 🌐 **3. Configuração de URLs no Banco**

**Execute no phpMyAdmin:**

```sql
-- Substitua 'https://seu-dominio.com' pelo seu domínio real
UPDATE wp_options SET option_value = 'https://seu-dominio.com' WHERE option_name = 'home';
UPDATE wp_options SET option_value = 'https://seu-dominio.com' WHERE option_name = 'siteurl';
```

## 🔐 **4. Primeiro Acesso**

### **Login WordPress:**
- **URL:** `https://seu-dominio.com/wp-admin`
- **Usuário:** `admin`
- **Senha:** `wp_resgate_2025!`

### **⚠️ IMPORTANTE - Primeira ação:**
1. **Altere a senha** imediatamente
2. **Crie seu usuário** pessoal
3. **Delete o usuário admin** (opcional)

## 📂 **5. Upload de Imagens**

### **Fazer upload das imagens:**
1. **Acesse:** `wp-admin` → `Media` → `Adicionar nova`
2. **Faça upload** das imagens do tema
3. **Atualize URLs** em: `Aparência` → `Personalizar`

### **Ou ajuste no banco:**
```sql
-- Atualize as URLs das imagens (substitua as URLs)
UPDATE wp_options SET option_value = REPLACE(option_value, 'localhost:8090', 'seu-dominio.com') WHERE option_name = 'theme_mods_wp-resgate';
```

## 🔧 **6. Configurações WordPress**

### **Configurar Permalinks:**
1. **Acesse:** `Configurações` → `Links permanentes`
2. **Selecione:** "Nome do post" ou "Personalizado": `/%postname%/`
3. **Salvar alterações**

### **Verificar Tema:**
1. **Acesse:** `Aparência` → `Temas`
2. **Ativar:** "WP Resgate" (se não estiver ativo)

## 📊 **7. Testar Sistema de Leads**

### **Verificar Admin:**
1. **Acesse:** Menu "Leads" no admin
2. **Verificar:** Se aparecem os leads de teste
3. **Testar:** Filtros e edição inline

### **Testar Formulário:**
1. **Acesse:** Página inicial do site
2. **Preencher:** formulário de contato
3. **Verificar:** Se lead aparece no admin
4. **Verificar:** Se sincroniza com Google Sheets

## 🔗 **8. Integração Google Sheets**

### **Verificar Configurações:**
1. **Acesse:** `wp-admin` → `Leads` → `Configurações`
2. **Verificar:** Webhook URL está configurada
3. **Testar:** Botão "Testar Integração"

### **URLs importantes:**
- **Webhook:** `https://script.google.com/macros/s/AKfycbzQwVfcbdBq9GPuXAWLgHX0DPaWKPFUxX_3dcJPqegxsJdnC9mo6TmhfO0lW7qEAdWR9g/exec`
- **Planilha:** `1ldFjuPTPQvxUBuAmPtyF_lUkX-6In0dJW4SX_l8QP9k`

## 🛡️ **9. Configurações de Segurança**

### **Recomendações:**
1. **SSL/HTTPS:** Ativar no painel Hostinger
2. **Backups:** Configurar backups automáticos
3. **Firewall:** Ativar se disponível no plano
4. **Updates:** Manter WordPress atualizado

### **Plugins recomendados:**
- Wordfence Security (firewall)
- UpdraftPlus (backup)
- WP Rocket (cache - se disponível)

## 🚨 **10. Solução de Problemas**

### **Site não carrega:**
- Verificar dados do banco no wp-config.php
- Verificar URLs no banco de dados
- Verificar permissões de arquivos (755/644)

### **Admin não acessa:**
- Verificar URL wp-admin
- Resetar senha via banco/email
- Verificar .htaccess

### **Formulário não funciona:**
- Verificar AJAX (console do navegador)
- Verificar webhook Google Sheets
- Verificar tabela wp_wp_resgate_leads

### **Tema não aparece:**
- Verificar se pasta wp-content/themes/wp-resgate existe
- Ativar tema em Aparência → Temas

## 📞 **11. Suporte**

### **Logs e Debug:**
- Ativar temporariamente: `define('WP_DEBUG', true);`
- Verificar logs no painel Hostinger
- Console do navegador (F12)

### **Recursos:**
- **Documentação:** Pasta `/docs/`
- **Scripts:** Pasta `/scripts/`
- **Google Apps Script:** Pasta `/google-apps-script/`

---

## ✅ **Checklist Final**

- [ ] Banco de dados importado/configurado
- [ ] wp-config.php configurado com dados corretos
- [ ] URLs atualizadas no banco
- [ ] Primeiro login realizado e senha alterada
- [ ] Permalinks configurados
- [ ] Tema WP Resgate ativo
- [ ] Sistema de leads funcionando
- [ ] Formulário testado
- [ ] Google Sheets sincronizando
- [ ] SSL/HTTPS ativo
- [ ] Backup configurado

**🎉 Seu WP Resgate está funcionando na Hostinger!**