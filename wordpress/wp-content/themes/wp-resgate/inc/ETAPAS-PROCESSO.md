# Como Gerenciar as Etapas do Processo

## 📋 Visão Geral

O Custom Post Type **"Etapas do Processo"** permite gerenciar facilmente as etapas que aparecem na seção "Como funciona" do seu site, diretamente pelo painel do WordPress.

## 🚀 Como Acessar

1. Entre no **painel do WordPress** (wp-admin)
2. Vá em **Aparência > Etapas do Processo**
3. Você verá uma lista das etapas já cadastradas

## ➕ Adicionar Nova Etapa

### Passo 1: Criar a Etapa
1. Clique em **"Adicionar Nova"**
2. Preencha o **título** da etapa (ex: "Diagnóstico gratuito")
3. Adicione o **conteúdo** detalhado (opcional, para futuras expansões)

### Passo 2: Configurar Detalhes
Na seção **"Detalhes da Etapa"**:

- **Número da Etapa**: Digite o número que aparecerá (1, 2, 3, etc.)
- **Ícone Bootstrap**: Escolha um ícone do [Bootstrap Icons](https://icons.getbootstrap.com/)
  - Exemplos: `bi-clipboard2-pulse`, `bi-shield-check`, `bi-check-circle`
- **Descrição Resumida**: Texto que aparece abaixo do título no site

### Passo 3: Publicar
1. Clique em **"Publicar"**
2. A etapa aparecerá automaticamente no site

## ✏️ Editar Etapas Existentes

1. Na listagem, clique no **título da etapa** que deseja editar
2. Faça as alterações necessárias
3. Clique em **"Atualizar"**

## 🎨 Ícones Disponíveis

Você pode usar qualquer ícone do Bootstrap Icons. Exemplos populares:

- `bi-clipboard2-pulse` - Diagnóstico/análise
- `bi-shield-check` - Segurança/proteção  
- `bi-check-circle` - Finalização/aprovação
- `bi-tools` - Correção/manutenção
- `bi-arrow-left-right` - Migração/transferência
- `bi-speedometer2` - Performance/velocidade
- `bi-bug` - Correção de bugs
- `bi-download` - Download/backup

## 📊 Visualização na Listagem

A listagem mostra:
- **Título** da etapa
- **Número** (badge azul)
- **Ícone** (se configurado)
- **Descrição** (resumida)
- **Data** de criação

## 🔄 Ordenação

As etapas são ordenadas automaticamente pelo **número da etapa**. Para reordenar:

1. Edite a etapa que deseja mover
2. Altere o **número da etapa**
3. Salve as alterações

## 🎯 Dicas de Uso

### Boas Práticas
- Use números sequenciais (1, 2, 3...)
- Escolha ícones que representem bem cada etapa
- Mantenha descrições concisas (máximo 2 linhas)
- Use títulos claros e objetivos

### Exemplos de Etapas
```
1. Diagnóstico gratuito (bi-clipboard2-pulse)
2. Execução segura (bi-shield-check) 
3. Entrega + prevenção (bi-check-circle)
```

## 🔧 Para Desenvolvedores

### Função Helper
```php
$steps = wp_resgate_get_process_steps();
```

### Estrutura dos Dados
```php
[
    'number' => '1',
    'title' => 'Título da Etapa',
    'description' => 'Descrição resumida',
    'icon' => 'bi-clipboard2-pulse',
    'content' => 'Conteúdo completo'
]
```

### Template
O template automaticamente busca e exibe as etapas do banco de dados. Se não houver etapas cadastradas, usa dados padrão como fallback.

## 🆘 Solução de Problemas

### Etapas não aparecem no site
1. Verifique se as etapas estão **publicadas** (não em rascunho)
2. Confirme se o **número da etapa** está preenchido
3. Limpe o cache do site se usar plugin de cache

### Ícones não aparecem
1. Verifique se a classe do ícone está correta (ex: `bi-check-circle`)
2. Consulte a [documentação oficial](https://icons.getbootstrap.com/) para ver os ícones disponíveis
3. Certifique-se de não incluir prefixos extras

### Ordem incorreta
1. Verifique os **números das etapas** - devem ser sequenciais
2. Use números diferentes para cada etapa
3. A ordenação é automática por número crescente