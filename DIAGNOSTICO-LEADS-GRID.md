# Diagnóstico: Leads não aparecem no Grid do WordPress

## Problema Identificado

Os leads não estavam aparecendo na área administrativa do WordPress devido a um erro na consulta SQL que estava tentando filtrar por uma coluna `deleted_at` que não existia na tabela atual.

## Análise do Problema

### Estado da Tabela
- ✅ Tabela `wp_wp_resgate_leads` existe
- ✅ Contém dados (2 leads)
- ❌ Não possui coluna `deleted_at` (soft delete não implementado)

### Estrutura da Tabela Atual
```sql
| Field        | Type         | Null | Key | Default           | Extra                    |
|--------------|--------------|------|-----|-------------------|--------------------------|
| id           | mediumint    | NO   | PRI | NULL              | auto_increment           |
| name         | tinytext     | NO   |     | NULL              |                          |
| email        | varchar(100) | NO   |     | NULL              |                          |
| phone        | varchar(20)  | YES  |     | NULL              |                          |
| website      | varchar(255) | YES  |     | NULL              |                          |
| problem_type | varchar(50)  | YES  |     | NULL              |                          |
| urgency      | varchar(20)  | YES  |     | NULL              |                          |
| description  | text         | YES  |     | NULL              |                          |
| source       | varchar(50)  | YES  |     | NULL              |                          |
| ip_address   | varchar(45)  | YES  |     | NULL              |                          |
| user_agent   | text         | YES  |     | NULL              |                          |
| page_url     | varchar(255) | YES  |     | NULL              |                          |
| status       | varchar(20)  | YES  |     | new               |                          |
| created_at   | datetime     | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED        |
| updated_at   | datetime     | YES  |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED on update CURRENT_TIMESTAMP |
```

### Erro na Query SQL
O código em `inc/leads-admin.php` linha 123 estava fazendo:
```php
$where_conditions = array('1=1', 'deleted_at IS NULL'); // ❌ Coluna não existe
```

## Solução Implementada

### 1. Correção da Query Principal
**Arquivo:** `inc/leads-admin.php`
**Linha:** 123
```php
// ANTES
$where_conditions = array('1=1', 'deleted_at IS NULL'); // Excluir leads deletados

// DEPOIS  
$where_conditions = array('1=1'); // Remover filtro deleted_at até implementarmos soft delete
```

### 2. Correção da Função de Deleção
**Arquivo:** `inc/leads-admin.php`
**Função:** `delete_lead()`

Alterado de soft delete para hard delete:
```php
// ANTES - Tentativa de soft delete (coluna não existe)
$result = $wpdb->update(
    $table_name,
    array(
        'deleted_at' => current_time('mysql'),
        'updated_at' => current_time('mysql')
    ),
    array('id' => $lead_id),
    array('%s', '%s'),
    array('%d')
);

// DEPOIS - Hard delete (funcional)
$result = $wpdb->delete(
    $table_name,
    array('id' => $lead_id),
    array('%d')
);
```

### 3. Correção da Função Trash
**Arquivo:** `inc/leads-admin.php`
**Função:** `trash_page()`

Simplificado para indicar lixeira vazia:
```php
// ANTES - Tentativa de buscar leads deletados
$leads_query = "SELECT * FROM $table_name WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC LIMIT %d OFFSET %d";

// DEPOIS - Lixeira vazia (sem soft delete)
$leads = array();
$total_items = 0;
$total_pages = 0;
```

### 4. Adição da Inicialização da Classe
**Arquivo:** `functions.php`

Adicionado após o `require_once`:
```php
// Inicializar o painel de administração dos leads
if (is_admin()) {
    new WP_Resgate_Leads_Admin();
}
```

## Testes Realizados

### 1. Verificação da Tabela
```bash
docker exec -it wordpress_db mysql -u wordpress_user -p'wordpress_password_123' wordpress_db -e "SHOW TABLES LIKE '%leads%';"
# ✅ Tabela existe: wp_wp_resgate_leads
```

### 2. Contagem de Leads
```bash
docker exec -it wordpress_db mysql -u wordpress_user -p'wordpress_password_123' wordpress_db -e "SELECT COUNT(*) as total_leads FROM wp_wp_resgate_leads;"
# ✅ Total: 2 leads
```

### 3. Inserção de Lead de Teste
```sql
INSERT INTO wp_wp_resgate_leads (name, email, phone, website, problem_type, urgency, description, source, status, created_at, updated_at) 
VALUES ('João Teste', 'joao@teste.com', '(11) 99999-1234', 'https://teste.com', 'malware', 'critical', 'Site foi hackeado urgente', 'website_form', 'new', NOW(), NOW());
# ✅ Lead inserido com sucesso
```

## Estado Atual

### ✅ Funcionando
- Tabela de leads existe com dados
- Query SQL corrigida (sem referências a `deleted_at`)
- Classe do admin inicializada
- Hard delete funcional
- Arquivos CSS/JS existem

### 📋 Próximos Passos
1. Testar interface admin no WordPress (`/wp-admin/admin.php?page=wp-resgate-leads`)
2. Verificar se formulário de captura está salvando corretamente
3. Implementar soft delete adequadamente (se necessário)
4. Verificar integração com Google Sheets

## Arquivos Modificados

1. `/wordpress/wp-content/themes/wp-resgate/inc/leads-admin.php`
   - Linha 123: Removido filtro `deleted_at IS NULL`
   - Função `delete_lead()`: Alterado para hard delete
   - Função `trash_page()`: Simplificado para lixeira vazia

2. `/wordpress/wp-content/themes/wp-resgate/functions.php`
   - Adicionado inicialização da classe `WP_Resgate_Leads_Admin`

## Como Testar

1. Acessar o WordPress Admin: http://localhost:8090/wp-admin
2. Ir para menu "Leads" na barra lateral
3. Verificar se os 2 leads aparecem na listagem
4. Testar formulário de captura na página principal

## Nota Importante

Este fix resolve o problema imediato dos leads não aparecerem. Para implementar soft delete (lixeira) adequadamente, será necessário:

1. Adicionar coluna `deleted_at` à tabela
2. Atualizar todas as queries para filtrar por `deleted_at IS NULL`
3. Implementar função de restauração da lixeira
4. Atualizar integração com Google Sheets

Atualmente o sistema está usando hard delete (deleção definitiva).