# 🔧 DEBUG: Erro de Atualização - Logs Detalhados

## 🚨 **PROBLEMA IDENTIFICADO:**

O Hash ID está funcionando (vemos WPR_bcfa9d0baa7a na planilha), mas a atualização ainda falha. Implementei logs detalhados para diagnosticar.

---

## 🔍 **CORREÇÕES IMPLEMENTADAS:**

### **1. Logs Detalhados:**
```javascript
✅ Log completo do processo de busca por hash
✅ Log dos dados recebidos para atualização  
✅ Log da linha encontrada na planilha
✅ Log dos dados antes e depois da atualização
✅ Log de erros com stack trace completo
```

### **2. Função de Busca Melhorada:**
```javascript
function findRowByHashId(sheet, hashId) {
  // Agora loga cada comparação
  // Mostra exatamente onde falha a busca
  // Debug completo do processo
}
```

### **3. Atualização Robusta:**
```javascript
function handleUpdateLead(data) {
  // Logs de início e fim do processo
  // Captura de erros específicos
  // Fallback para criar novo lead se não encontrar
}
```

---

## 🧪 **COMO TESTAR O DEBUG:**

### **Passo 1: Substituir Script**
```
1. Acesse: https://script.google.com
2. Substitua pelo código atualizado (google-apps-script-v3-final.gs)
3. Salve o projeto
```

### **Passo 2: Testar Funções Debug**
```javascript
// 1. Listar todos os hashes
listAllHashes();

// 2. Testar com hash real da sua planilha
testUpdateReal();

// 3. Verificar logs detalhados
```

### **Passo 3: Testar Atualização Real**
```
1. Acesse WordPress Admin → Leads
2. Mude status de um lead
3. Vá para Google Apps Script → Executions
4. Veja os logs detalhados do erro
```

---

## 📊 **LOGS QUE VOCÊ VERÁ:**

### **Logs de Busca:**
```
=== INICIANDO ATUALIZAÇÃO DE LEAD ===
Dados recebidos para atualização: {...}
Hash ID para busca: WPR_bcfa9d0baa7a
Procurando hash_id: WPR_bcfa9d0baa7a
Total de linhas na planilha: 4
Linha 2: comparando 'WPR_bcfa9d0baa7a' com 'WPR_bcfa9d0baa7a'
Hash encontrado na linha: 2
```

### **Logs de Atualização:**
```
Lead encontrado na linha: 2
Dados atuais da linha: [WPR_bcfa9d0baa7a, 14, 2025-10-09, ...]
Dados para atualização: [WPR_bcfa9d0baa7a, 14, 2025-10-09, ...]
Linha atualizada com sucesso
=== ATUALIZAÇÃO CONCLUÍDA COM SUCESSO ===
```

### **Logs de Erro (se houver):**
```
=== ERRO NA ATUALIZAÇÃO ===
Erro completo: [detalhes do erro]
Stack trace: [linha exata do erro]
```

---

## 🎯 **POSSÍVEIS CAUSAS DO ERRO:**

### **1. Hash não encontrado:**
- Hash gerado no WordPress diferente do armazenado
- Problema na comparação de strings

### **2. Erro de permissão:**
- Google Apps Script sem permissão para editar
- Planilha protegida

### **3. Erro de estrutura:**
- Dados recebidos em formato incorreto
- Colunas da planilha não batem com o esperado

### **4. Erro de rede:**
- Timeout na requisição
- Webhook não configurado corretamente

---

## 🔧 **FUNÇÕES DE DEBUG ADICIONADAS:**

### **testUpdateReal():**
```javascript
// Testa com hash real da sua planilha
// Hash: WPR_bcfa9d0baa7a (da imagem que você enviou)
```

### **listAllHashes():**
```javascript
// Lista todos os hashes da planilha
// Útil para verificar se hash existe
```

### **Logs Automáticos:**
```javascript
// Todos os webhooks agora geram logs detalhados
// Vá em Executions para ver exatamente onde falha
```

---

## 📋 **PRÓXIMOS PASSOS:**

### **1. Implementar Script Atualizado:**
- Cole o novo código no Google Apps Script
- Teste função `listAllHashes()` primeiro

### **2. Testar com Hash Real:**
- Execute `testUpdateReal()` 
- Verifique logs em Executions

### **3. Testar WordPress Admin:**
- Mude status de um lead
- Verifique logs de erro detalhados

### **4. Reportar Logs:**
- Me envie os logs específicos do erro
- Assim posso identificar a causa exata

---

## 🚀 **RESULTADO ESPERADO:**

Com os logs detalhados, você verá exatamente:

✅ **Se o hash está sendo encontrado**  
✅ **Onde a atualização falha**  
✅ **Dados recebidos vs dados esperados**  
✅ **Erro específico com linha exata**  

**🔍 Agora temos visibilidade completa do processo para corrigir definitivamente o erro!**

---

## 📞 **COMO ME REPORTAR:**

Após testar, me envie:
1. **Logs da função `listAllHashes()`**
2. **Logs da função `testUpdateReal()`** 
3. **Logs do erro real** (Executions → última execução com erro)

Com essas informações, posso identificar e corrigir o problema específico! 🎯