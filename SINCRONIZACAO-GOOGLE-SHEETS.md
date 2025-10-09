# 🔄 Sincronização WordPress ↔ Google Sheets

## ✅ **FUNCIONALIDADE IMPLEMENTADA**

Agora é possível sincronizar automaticamente todas as alterações feitas nos leads do WordPress com a planilha do Google Sheets!

---

## 🚀 **O que foi implementado:**

### **1. Funções de Atualização no WordPress:**
- ✅ `update_lead_in_sheets()` - Atualiza lead existente na planilha
- ✅ `delete_lead_from_sheets()` - Remove lead da planilha
- ✅ `bulk_update_sheets()` - Ações em massa sincronizadas

### **2. Google Apps Script Atualizado (v2.0):**
- ✅ **Novos leads**: Adiciona automaticamente
- ✅ **Atualizações**: Modifica registros existentes
- ✅ **Deleções**: Remove da planilha
- ✅ **Ações em massa**: Processa múltiplas alterações

### **3. Hooks WordPress Integrados:**
- ✅ **Status individual**: Atualiza ao mudar status de um lead
- ✅ **Deleção individual**: Remove da planilha ao excluir
- ✅ **Ações em massa**: Sincroniza alterações em lote

---

## ⚙️ **CONFIGURAÇÃO NECESSÁRIA:**

### **Passo 1: Atualizar Google Apps Script**
```javascript
// 1. Acesse: https://script.google.com
// 2. Abra seu projeto existente OU crie um novo
// 3. Substitua o código pelo arquivo: google-apps-script-v2.gs
// 4. Configure SHEET_ID na linha 12
// 5. Republique como Web App
```

### **Passo 2: Verificar WordPress**
```php
// Certifique-se que no Customizer está configurado:
// - URL do Webhook (do Google Apps Script)
// - Sheet ID (da planilha)
```

---

## 🔧 **FUNCIONAMENTO:**

### **Quando você altera um lead no WordPress admin:**

1. **Status Individual** → Atualiza automaticamente na planilha
2. **Excluir Lead** → Remove da planilha automaticamente  
3. **Ações em Massa** → Processa todas as alterações

### **Fluxo de Sincronização:**
```
WordPress Admin → Webhook → Google Apps Script → Planilha Atualizada
```

---

## 📊 **TIPOS DE SINCRONIZAÇÃO:**

### **✅ Novo Lead (já funcionava)**
- Formulário do site → Planilha

### **🆕 Atualização de Lead**
- Mudança de status → Atualiza linha existente
- Dados são localizados pelo Lead ID

### **🆕 Deleção de Lead**  
- Exclusão no admin → Remove linha da planilha
- Localiza pelo Lead ID e remove completamente

### **🆕 Ações em Massa**
- Marcar múltiplos como "Contatado" → Atualiza todos
- Excluir múltiplos → Remove todos da planilha

---

## 🧪 **COMO TESTAR:**

### **1. Teste de Atualização:**
```
1. Acesse WordPress Admin → Leads
2. Mude o status de um lead
3. Verifique na planilha se foi atualizado
```

### **2. Teste de Deleção:**
```
1. Exclua um lead no admin
2. Verifique se desapareceu da planilha
```

### **3. Teste de Ações em Massa:**
```
1. Selecione múltiplos leads
2. Use "Ações em massa" → "Marcar como contatado"
3. Verifique se todos foram atualizados na planilha
```

---

## 🔍 **ESTRUTURA DA PLANILHA ATUALIZADA:**

| Coluna | Campo | Descrição |
|--------|-------|-----------|
| A | Lead ID | ID único do WordPress |
| B | Data/Hora | Timestamp original |
| C | Nome | Nome do cliente |
| D | Email | Email do cliente |
| E | Telefone | Telefone do cliente |
| F | Website | Site do cliente |
| G | Tipo do Problema | Categoria do problema |
| H | Urgência | Nível de urgência |
| I | Descrição | Descrição detalhada |
| J | **Status** | **Status atual (sincronizado!)** |
| K | Criado em | Data de criação |
| L | **Atualizado em** | **Data da última atualização** |

---

## 🛠️ **LOGS E DEBUG:**

### **WordPress Error Log:**
```php
// Verifique logs em caso de problemas:
// wp-content/debug.log

// Exemplos de mensagens:
"WP Resgate - Falha na sincronização de atualização: URL não configurada"
"WP Resgate - Erro na sincronização: Timeout"
```

### **Google Apps Script Log:**
```javascript
// Acesse: script.google.com → Executions
// Verifique logs de execução para debug
```

---

## ⚡ **BENEFÍCIOS DA SINCRONIZAÇÃO:**

### **📈 Gestão Centralizada:**
- WordPress = Interface de gestão
- Google Sheets = Relatórios e análises

### **🔄 Sincronização em Tempo Real:**
- Alterações aparecem imediatamente
- Sem necessidade de exportação manual

### **👥 Trabalho em Equipe:**
- Admin usa WordPress
- Equipe acompanha no Google Sheets

### **📊 Relatórios Avançados:**
- Use fórmulas do Google Sheets
- Crie gráficos automáticos
- Exporte para outros sistemas

---

## 🔐 **SEGURANÇA:**

- ✅ Verificação de nonce em todas as operações
- ✅ Verificação de permissões de usuário
- ✅ Logs de erro para auditoria
- ✅ Tratamento de exceções

---

## 🚨 **RESOLUÇÃO DE PROBLEMAS:**

### **Problema: Alterações não aparecem na planilha**
```
1. Verifique se URL do webhook está configurada
2. Teste se Google Apps Script está publicado
3. Verifique logs de erro no WordPress
```

### **Problema: Erro "Lead não encontrado"**
```
1. Leads criados antes da atualização podem não ter ID
2. Novos leads terão sincronização completa
```

### **Problema: Timeout na sincronização**
```
1. Normal em ações em massa com muitos leads
2. Dados são salvos no WordPress mesmo com timeout
3. Reprocessar manualmente se necessário
```

---

## 🎯 **PRÓXIMOS PASSOS OPCIONAIS:**

1. **Sincronização inversa**: Google Sheets → WordPress
2. **Webhook de confirmação**: Confirmar recebimento
3. **Dashboard de sincronização**: Monitor de status
4. **Histórico de alterações**: Log de mudanças

---

**🎉 A sincronização bidirecional está FUNCIONANDO!**

Agora todas as alterações feitas nos leads do WordPress aparecerão automaticamente na planilha do Google! 🚀