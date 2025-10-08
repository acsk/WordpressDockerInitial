# Custom Post Type: FAQ

## Visão Geral

O CPT **FAQ** permite gerenciar perguntas frequentes diretamente pelo admin do WordPress, tornando a seção de FAQ totalmente dinâmica e editável.

## Características Técnicas

### Configurações do CPT
- **Nome:** `faq`
- **Público:** Não (apenas admin)
- **Menu:** Dashboard lateral com ícone de help
- **Suporte:** Título e ordenação (page-attributes)
- **Hierárquico:** Não

### Campos Personalizados (Meta Fields)

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `_faq_question` | Text | ✅ | A pergunta que será exibida |
| `_faq_answer` | Textarea | ✅ | A resposta completa |
| `_faq_expanded` | Checkbox | ❌ | Se deve aparecer expandida |

### Interface Admin

#### Colunas Customizadas
- **Pergunta:** Exibe a pergunta configurada
- **Resposta:** Preview da resposta (12 palavras)
- **Expandida:** Badge indicando se aparece aberta
- **Ordem:** Campo menu_order para ordenação

#### Recursos
- ✅ Ordenação por campo "Ordem" e "Expandida"
- ✅ Preview da pergunta e resposta na listagem
- ✅ Validação de campos obrigatórios
- ✅ Dicas de usabilidade no meta box
- ✅ Interface intuitiva com badges coloridos

## Implementação Técnica

### Registro do CPT
```php
function wp_resgate_register_faq_cpt() {
    // Configurações completas do post type
}
add_action('init', 'wp_resgate_register_faq_cpt', 0);
```

### Meta Boxes
```php
function wp_resgate_add_faq_meta_boxes() {
    add_meta_box('faq_details', 'Detalhes da FAQ', 
                'wp_resgate_faq_meta_box_callback', 'faq');
}
```

### Função Helper
```php
function get_faqs($args = []) {
    // Busca FAQs com fallback para dados padrão
}
```

## Uso no Template

### Template Atualizado
O arquivo `template-parts/faq.php` foi modificado para usar dados dinâmicos:

```php
<?php 
$faqs = get_faqs();
foreach ($faqs as $index => $faq) : 
    // Renderização do accordion
endforeach; 
?>
```

### Estrutura de Dados
Cada FAQ retorna:
```php
[
    'id' => 123,                    // ID do post
    'question' => 'Pergunta...',    // Pergunta configurada
    'answer' => 'Resposta...',      // Resposta completa
    'expanded' => true,             // Se deve aparecer aberta
    'order' => 1                    // Ordem de exibição
]
```

## Dados Padrão

### Inserção Automática
- ✅ 6 FAQs de exemplo inseridas automaticamente
- ✅ Apenas na primeira ativação (não duplica)
- ✅ Baseadas nas perguntas mais comuns
- ✅ Uma expandida por padrão (primeira)

### FAQs Padrão Incluídas
1. **Tempo de resolução** (expandida)
2. **Acesso e senhas**
3. **Garantia de serviço**
4. **Custo dos serviços**
5. **Política de backup**
6. **Atendimento internacional**

## Como Usar

### Gerenciar FAQs
1. Acesse **FAQs** no menu lateral do admin
2. Clique em **Adicionar Nova** para criar
3. Preencha pergunta e resposta (obrigatórios)
4. Marque se deve aparecer expandida
5. Use o campo **Ordem** para organizar sequência

### Boas Práticas
- ✅ Use perguntas que clientes realmente fazem
- ✅ Mantenha respostas claras e objetivas
- ✅ Ordene por relevância (campo Ordem)
- ✅ Deixe apenas 1 FAQ expandida por padrão
- ✅ Use de 4-8 FAQs para melhor UX

### Fallback System
Se não houver FAQs cadastradas, o sistema automaticamente exibe as 6 FAQs padrão para manter a funcionalidade.

## Recursos Avançados

### Validação
- Campos obrigatórios validados no backend
- Mensagens de erro claras para admin
- Sanitização automática dos dados

### SEO Friendly
- Estrutura de accordion otimizada
- IDs únicos para cada FAQ
- Marcação semântica adequada

### Performance
- Query otimizada com meta_query
- Cache-friendly (usa get_posts)
- Fallback rápido quando necessário

## Extensibilidade

### Hooks Disponíveis
- `manage_faq_posts_columns` - Customizar colunas
- `manage_faq_posts_custom_column` - Conteúdo das colunas
- `pre_get_posts` - Modificar queries

### Possíveis Melhorias Futuras
- Categorização de FAQs
- Campo de busca na seção
- Analytics de FAQs mais acessadas
- Integração com chatbot
- Export/import de FAQs

---

## Status: ✅ Completo e Funcional

O CPT de FAQ está completamente implementado e pronto para uso em produção.