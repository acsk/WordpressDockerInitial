# 📊 Integração com Google Sheets via Webhook

## 🎯 **O que foi implementado?**

### ✅ **Sistema Completo de Captura de Leads**
- 📝 **Formulário melhorado** com mais campos (nome, email, telefone, site, tipo de problema, urgência, descrição)
- 🛡️ **Proteção anti-spam** com honeypot e validações
- 💾 **Salvamento local** no banco WordPress (backup)
- 📊 **Integração Google Sheets** via webhook
- 📧 **Notificação por email** automática
- ⚡ **Interface AJAX** sem reload da página

### 🗃️ **Estrutura dos Dados**
O formulário captura:
1. **Nome** (obrigatório)
2. **Email** (obrigatório)
3. **Telefone** (opcional, com máscara)
4. **Website** (opcional)
5. **Tipo de problema** (Malware, Erros, Migração, Performance, etc.)
6. **Urgência** (Baixa, Média, Alta, Crítica)
7. **Descrição** (obrigatório)
8. **Dados técnicos** (IP, User Agent, URL da página, timestamp)

---

## 🚀 **Como Configurar (Passo a Passo)**

### **Passo 1: Criar Google Apps Script**

1. 📱 Acesse [script.google.com](https://script.google.com)
2. ➕ **Novo projeto** → Nome: "WP Resgate Webhook"
3. 📝 **Cole este código**:

```javascript
function doPost(e) {
  try {
    // ID da sua planilha (pegar da URL da planilha)
    const SHEET_ID = 'SUA_PLANILHA_ID_AQUI';
    const SHEET_NAME = 'Leads'; // Nome da aba
    
    // Abrir planilha
    const sheet = SpreadsheetApp.openById(SHEET_ID).getSheetByName(SHEET_NAME);
    
    // Se a aba não existir, criar
    if (!sheet) {
      const newSheet = SpreadsheetApp.openById(SHEET_ID).insertSheet(SHEET_NAME);
      // Adicionar cabeçalhos
      newSheet.getRange(1, 1, 1, 11).setValues([[
        'Data/Hora', 'Nome', 'Email', 'Telefone', 'Website', 
        'Tipo Problema', 'Urgência', 'Descrição', 'Fonte', 'IP', 'URL Página'
      ]]);
      newSheet.getRange(1, 1, 1, 11).setFontWeight('bold');
    }
    
    // Parsear dados recebidos
    const data = JSON.parse(e.postData.contents);
    const values = data.values[0]; // Primeira linha de dados
    
    // Adicionar na planilha
    sheet.appendRow(values);
    
    // Log para debug
    console.log('Lead adicionado:', values);
    
    // Resposta de sucesso
    return ContentService
      .createTextOutput(JSON.stringify({success: true, message: 'Lead salvo com sucesso'}))
      .setMimeType(ContentService.MimeType.JSON);
      
  } catch (error) {
    console.error('Erro:', error);
    return ContentService
      .createTextOutput(JSON.stringify({success: false, error: error.toString()}))
      .setMimeType(ContentService.MimeType.JSON);
  }
}
```

4. 💾 **Salvar** o projeto

### **Passo 2: Criar Planilha Google Sheets**

1. 📊 Acesse [sheets.google.com](https://sheets.google.com)
2. ➕ **Nova planilha** → Nome: "WP Resgate - Leads"
3. 📝 **Criar aba "Leads"** com cabeçalhos:
   ```
   A1: Data/Hora
   B1: Nome  
   C1: Email
   D1: Telefone
   E1: Website
   F1: Tipo Problema
   G1: Urgência
   H1: Descrição
   I1: Fonte
   J1: IP
   K1: URL Página
   ```
4. 📋 **Copiar ID da planilha** (da URL: `https://docs.google.com/spreadsheets/d/ID_AQUI/edit`)

### **Passo 3: Configurar Apps Script**

1. 🔧 No Apps Script, **substituir `SUA_PLANILHA_ID_AQUI`** pelo ID real
2. 🚀 **Implantar** → "Nova implantação"
3. ⚙️ **Tipo**: "Aplicativo da web"
4. 🔓 **Executar como**: "Eu"
5. 🌐 **Quem tem acesso**: "Qualquer pessoa"
6. 📋 **Copiar URL** do webhook gerado

### **Passo 4: Configurar WordPress**

1. 🎨 **WordPress Admin** → Aparência → Personalizar
2. 🔧 **WP Resgate** → Configurações Gerais
3. 🔗 **URL do Webhook**: Colar URL do Apps Script
4. 📊 **ID da Planilha**: Colar ID da planilha
5. 💾 **Publicar**

---

## 🧪 **Testando Localmente**

### ✅ **Teste Completo**
1. 🌐 Acesse seu site: `http://localhost:8090`
2. 📝 Vá até o formulário (#diagnostico)
3. ✏️ Preencha todos os campos
4. 📤 Clique em "Solicitar Diagnóstico"
5. ✅ Verifique:
   - Mensagem de sucesso no site
   - Dados na planilha Google
   - Email de notificação
   - Registro no banco local

### 🔍 **Debug/Troubleshooting**

#### **Formulário não envia:**
```javascript
// Abrir Developer Tools (F12) → Console
// Procurar por erros JavaScript
```

#### **Erro 500:**
```php
// Verificar logs do WordPress
// wp-content/debug.log (se WP_DEBUG estiver ativo)
```

#### **Webhook não funciona:**
1. 🔧 **Apps Script** → Execuções → Ver logs
2. 🧪 **Testar** função doPost manualmente
3. 🔗 **Verificar** se URL do webhook está correta

---

## 📊 **Banco de Dados Local**

### 🗃️ **Tabela Criada Automaticamente**
```sql
CREATE TABLE wp_wp_resgate_leads (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    name tinytext NOT NULL,
    email varchar(100) NOT NULL,
    phone varchar(20),
    website varchar(255),
    problem_type varchar(50),
    urgency varchar(20),
    description text,
    source varchar(50),
    ip_address varchar(45),
    user_agent text,
    page_url varchar(255),
    status varchar(20) DEFAULT 'new',
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);
```

### 🔍 **Consultar Leads**
```sql
-- Ver todos os leads
SELECT * FROM wp_wp_resgate_leads ORDER BY created_at DESC;

-- Leads por urgência
SELECT * FROM wp_wp_resgate_leads WHERE urgency = 'critical';

-- Leads de hoje
SELECT * FROM wp_wp_resgate_leads WHERE DATE(created_at) = CURDATE();
```

---

## 🔧 **Personalizações Avançadas**

### 📧 **Customizar Email de Notificação**
```php
// No functions.php, adicionar:
function custom_lead_notification($data) {
    $subject = "🚨 Lead URGENTE: " . $data['name'];
    $message = "Lead com urgência " . $data['urgency'] . "\n\n";
    $message .= "Detalhes completos em anexo...";
    
    wp_mail('seuemail@exemplo.com', $subject, $message);
}
add_action('wp_resgate_new_lead', 'custom_lead_notification');
```

### 📊 **Adicionar Campos ao Formulário**
```php
// No arquivo de integração, método sanitize_form_data:
'company' => sanitize_text_field($post_data['company'] ?? ''),
'budget' => sanitize_text_field($post_data['budget'] ?? ''),
```

### 🎨 **Customizar Planilha**
```javascript
// No Apps Script, adicionar formatação:
const lastRow = sheet.getLastRow();
sheet.getRange(lastRow, 1, 1, 11).setBackground('#f0f8ff');

// Adicionar fórmulas
sheet.getRange(lastRow, 12).setFormula('=DAYS(NOW(),A' + lastRow + ')');
```

---

## 🚀 **Benefícios da Integração**

### ✅ **Para o Negócio**
- 📊 **Leads centralizados** no Google Sheets
- 📧 **Notificações instantâneas** por email
- 🔍 **Dados detalhados** para qualificação
- 📈 **Fácil análise** e relatórios

### ✅ **Para o Usuário**
- ⚡ **Resposta rápida** sem reload
- 🛡️ **Proteção** contra spam
- 📱 **Interface otimizada** mobile
- ✅ **Feedback visual** claro

### ✅ **Para o Desenvolvedor**
- 💾 **Backup duplo** (WordPress + Sheets)
- 🔧 **Facilmente extensível**
- 📊 **Logs completos** para debug
- 🚀 **Performance otimizada**

---

## 🎯 **Resultado Final**

Com esta integração, você terá:
1. 📝 **Formulário profissional** com validação
2. 📊 **Planilha automática** no Google Sheets
3. 📧 **Notificações** por email
4. 💾 **Backup local** no WordPress
5. 🛡️ **Proteção** anti-spam
6. 📱 **Experiência mobile** otimizada

**💡 Funciona tanto localmente quanto em produção!**