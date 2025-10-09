# 🚀 Configuração PRODUÇÃO - wpprotegido.com.br

## 📊 **Dados de Produção - Hostinger**

### **🗄️ Banco de Dados:**
- **Base:** `u304177849_db`
- **Usuário:** `u304177849_root`
- **Senha:** `s*V06;M&ZKw`
- **Servidor:** `localhost`

### **🌐 Domínio:**
- **Site:** `https://wpprotegido.com.br`
- **Admin:** `https://wpprotegido.com.br/wp-admin`

### **🔐 Login WordPress:**
- **Usuário:** `wpresgate_admin`
- **Senha:** `WPR3sg4t3_2025#Pr0d` ⚠️ **ALTERAR IMEDIATAMENTE**
- **Email:** `admin@wpprotegido.com.br`

## 🚀 **Configuração Rápida (5 minutos)**

### **1️⃣ Configurar Banco de Dados**
```sql
-- No phpMyAdmin da Hostinger:
-- Selecionar base: u304177849_db
-- Importar arquivo: wp-resgate-database-export.sql
-- OU executar: hostinger-producao-setup.sql
```

### **2️⃣ Configurar wp-config.php**
```bash
# No File Manager da Hostinger:
1. Baixar: scripts/wp-config-producao.php
2. Gerar chaves em: https://api.wordpress.org/secret-key/1.1/salt/
3. Substituir as 8 linhas de chaves de segurança
4. Salvar como: wp-config.php (na raiz do public_html)
```

### **3️⃣ Primeiro Acesso**
```
URL: https://wpprotegido.com.br/wp-admin
Usuário: wpresgate_admin
Senha: WPR3sg4t3_2025#Pr0d

PRIMEIRA AÇÃO: Alterar senha!
```

## ⚙️ **Configurações Automáticas Incluídas**

### **✅ WordPress:**
- URLs configuradas para wpprotegido.com.br
- Timezone brasileiro (America/Sao_Paulo)
- Formato de data brasileiro (d/m/Y)
- Idioma português (pt_BR)
- Tema WP Resgate ativo

### **✅ Sistema de Leads:**
- Tabela `wp_wp_resgate_leads` criada
- 3 leads de exemplo inseridos
- Admin panel configurado
- Google Sheets integrado

### **✅ Performance:**
- Cache habilitado
- Compressão CSS/JS ativa
- Memória otimizada (256M)
- Configurações para hospedagem compartilhada

### **✅ Segurança:**
- SSL forçado no admin
- XML-RPC desabilitado
- File edit desabilitado
- Revisões limitadas (3)

## 🔗 **Integrações Configuradas**

### **📊 Google Sheets:**
- **Webhook:** `https://script.google.com/macros/s/AKfycbzQwVfcbdBq9GPuXAWLgHX0DPaWKPFUxX_3dcJPqegxsJdnC9mo6TmhfO0lW7qEAdWR9g/exec`
- **Planilha ID:** `1ldFjuPTPQvxUBuAmPtyF_lUkX-6In0dJW4SX_l8QP9k`
- **Status:** ✅ Funcionando

### **📱 WhatsApp:**
- **Número:** (82) 98837-6381
- **Configurado no:** Customizer do tema

## 📂 **Upload de Imagens Necessárias**

### **Fazer upload via wp-admin → Mídia:**
1. **Logo:** `logo.png` (recomendado: 200x60px)
2. **Hero Background:** `hero-bg.jpg` (recomendado: 1920x1080px)
3. **Hero Image:** `hero-img.jpg` (recomendado: 800x600px)

### **Ou ajustar URLs no Customizer:**
`Aparência → Personalizar → WP Resgate Options`

## 🧪 **Testes Essenciais**

### **✅ Lista de Verificação:**
- [ ] Login no wp-admin funcionando
- [ ] Senha alterada
- [ ] Site carregando (wpprotegido.com.br)
- [ ] Tema WP Resgate ativo
- [ ] Menu "Leads" aparece no admin
- [ ] Leads de exemplo visíveis
- [ ] Formulário da página inicial funciona
- [ ] Novo lead aparece no admin
- [ ] Novo lead sincroniza no Google Sheets
- [ ] SSL/HTTPS ativo
- [ ] WhatsApp funcionando

## 🔧 **Configurações Adicionais Recomendadas**

### **1. Permalinks:**
```
wp-admin → Configurações → Links Permanentes
Selecionar: "Nome do post" ou Personalizado: /%postname%/
Salvar alterações
```

### **2. SSL/HTTPS (Hostinger):**
```
Painel Hostinger → SSL → Ativar
Aguardar propagação (até 24h)
```

### **3. Backup Automático:**
```
Instalar plugin: UpdraftPlus
Configurar backup automático
Testar backup e restauração
```

### **4. Segurança Adicional:**
```
Instalar plugin: Wordfence Security
Configurar firewall
Ativar scan de malware
```

## 🚨 **Solução de Problemas**

### **Site não carrega:**
1. Verificar dados do banco no wp-config.php
2. Verificar se domínio está apontado para Hostinger
3. Aguardar propagação DNS (até 48h)
4. Verificar SSL está ativo

### **Erro de conexão com banco:**
1. Confirmar credenciais no wp-config.php:
   - DB_NAME: u304177849_db
   - DB_USER: u304177849_root
   - DB_PASSWORD: s*V06;M&ZKw
2. Verificar se banco existe no painel Hostinger
3. Testar conexão via phpMyAdmin

### **Admin não acessa:**
1. URL correta: wpprotegido.com.br/wp-admin
2. Usuário: wpresgate_admin
3. Senha: WPR3sg4t3_2025#Pr0d
4. Verificar .htaccess na raiz

### **Formulário não funciona:**
1. Verificar AJAX no console (F12)
2. Testar webhook Google Sheets
3. Verificar tabela wp_wp_resgate_leads existe
4. Verificar configurações do tema

## 📞 **Suporte Técnico**

### **Logs de Erro:**
- Painel Hostinger → Arquivo → Error Logs
- wp-admin → Ferramentas → Site Health

### **Debug Temporário:**
```php
// No wp-config.php (temporário)
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
// Verificar: /wp-content/debug.log
```

### **Contato:**
- **WhatsApp:** (82) 98837-6381
- **Email:** admin@wpprotegido.com.br

---

## 🎯 **Status Final**

### **✅ PRONTO PARA PRODUÇÃO:**
- **Banco:** Configurado com dados reais Hostinger
- **WordPress:** Otimizado para wpprotegido.com.br
- **Tema:** WP Resgate ativo e configurado  
- **Leads:** Sistema funcionando + Google Sheets
- **Performance:** Otimizado para hospedagem compartilhada
- **Segurança:** Configurações de produção aplicadas

### **📊 Arquivos de Produção:**
- `hostinger-producao-setup.sql` (banco configurado)
- `wp-config-producao.php` (configuração otimizada)
- `wp-resgate-database-export.sql` (dados completos)

**🚀 Seu WP Resgate está PRONTO para funcionar em wpprotegido.com.br!**