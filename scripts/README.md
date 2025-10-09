# 📁 Scripts de Configuração - WP Resgate

Esta pasta contém todos os scripts e arquivos necessários para configurar o WP Resgate na Hostinger.

## 📋 **Arquivos Disponíveis**

### **🗄️ Banco de Dados**
- **`wp-resgate-database-export.sql`** (787KB)
  - Export completo do banco local
  - Contém todos os dados, configurações e leads de teste
  - **Uso:** Importar direto no phpMyAdmin da Hostinger

- **`hostinger-database-setup.sql`**
  - Script de configuração personalizada
  - Ajusta URLs, configurações e cria estruturas necessárias
  - **Uso:** Executar após importar o banco ou para configuração manual

### **⚙️ Configuração WordPress**
- **`wp-config-hostinger.php`**
  - Template de wp-config.php otimizado para Hostinger
  - Pré-configurado com settings de performance e segurança
  - **Uso:** Editar dados do banco e salvar como wp-config.php

### **📖 Documentação**
- **`GUIA-INSTALACAO-HOSTINGER.md`**
  - Guia completo passo-a-passo
  - Checklist de configuração
  - Solução de problemas comuns

### **🔧 Scripts Auxiliares**
- **`prepare-hosting.sh`**
  - Script de preparação para hospedagem
  - Organiza arquivos e otimiza estrutura

- **`setup-local-folders.sh`**
  - Script de configuração do ambiente local
  - Cria estruturas de pastas necessárias

## 🚀 **Como Usar**

### **1. Deploy Rápido (Recomendado)**
```bash
# Os arquivos WordPress já estão na Hostinger via Git Deploy
# Apenas configure o banco de dados:

1. Acesse phpMyAdmin da Hostinger
2. Importe: wp-resgate-database-export.sql
3. Configure: wp-config-hostinger.php com seus dados
4. Salve como wp-config.php na raiz
```

### **2. Configuração Manual**
```bash
# Se preferir configurar passo a passo:

1. Execute: hostinger-database-setup.sql no phpMyAdmin
2. Siga: GUIA-INSTALACAO-HOSTINGER.md
3. Configure: wp-config-hostinger.php
```

## ⚠️ **Informações Importantes**

### **Dados que DEVEM ser alterados:**
- **Banco de dados:** Nome, usuário e senha da Hostinger
- **URLs:** Substitua por seu domínio real
- **Chaves de segurança:** Gere novas em api.wordpress.org
- **Senha admin:** Altere `wp_resgate_2025!` imediatamente

### **Configurações já incluídas:**
- ✅ Integração Google Sheets configurada
- ✅ Tema WP Resgate ativo
- ✅ Leads de teste incluídos
- ✅ Custom Post Types configurados
- ✅ Configurações de performance otimizadas

## 🔗 **Links Úteis**

- **Gerar chaves WordPress:** https://api.wordpress.org/secret-key/1.1/salt/
- **Planilha Google Sheets:** [1ldFjuPTPQvxUBuAmPtyF_lUkX-6In0dJW4SX_l8QP9k]
- **Webhook Apps Script:** [Ver arquivo em /google-apps-script/]

## 📊 **Status dos Arquivos**

| Arquivo | Tamanho | Status | Descrição |
|---------|---------|--------|-----------|
| wp-resgate-database-export.sql | 787KB | ✅ Pronto | Banco completo |
| hostinger-database-setup.sql | 7.8KB | ✅ Pronto | Config manual |
| wp-config-hostinger.php | 3.7KB | ✅ Pronto | Template config |
| GUIA-INSTALACAO-HOSTINGER.md | 5.5KB | ✅ Pronto | Documentação |

---

**🎯 Todos os arquivos estão prontos para uso na Hostinger!**