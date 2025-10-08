# Como Gerenciar os Depoimentos de Clientes

## 📋 Visão Geral

O Custom Post Type **"Depoimentos"** permite gerenciar facilmente os depoimentos de clientes que aparecem na seção "Clientes satisfeitos" do seu site, diretamente pelo painel do WordPress.

## 🚀 Como Acessar

1. Entre no **painel do WordPress** (wp-admin)
2. Vá em **Aparência > Depoimentos**
3. Você verá uma lista dos depoimentos já cadastrados

## ➕ Adicionar Novo Depoimento

### Passo 1: Criar o Depoimento
1. Clique em **"Adicionar Novo"**
2. Preencha o **título** (ex: "Depoimento - Maria Silva") - apenas para identificação
3. **Escreva o depoimento completo** no editor principal
   - Este é o texto que aparecerá no site
   - Use aspas se desejar, mas não é obrigatório
   - Mantenha entre 100-200 palavras para melhor apresentação

### Passo 2: Dados do Cliente
Na seção **"Dados do Cliente"**:

- **Nome do Cliente** *(obrigatório)*: Nome que aparecerá no site
- **Empresa/Área**: Empresa ou área de atuação (ex: "E-commerce", "Consultoria")
- **Website do Cliente**: Site do cliente (opcional, criará link no nome)
- **Avaliação**: Escolha de 1 a 5 estrelas
- **Depoimento em Destaque**: Marque para priorizar na exibição

### Passo 3: Foto do Cliente
1. Na lateral direita, clique em **"Definir imagem destacada"**
2. Faça upload da foto do cliente
3. **Tamanho recomendado**: 150x150px (quadrado)
4. A imagem será exibida em formato circular

### Passo 4: Publicar
1. Clique em **"Publicar"**
2. O depoimento aparecerá automaticamente no site

## ✏️ Editar Depoimentos Existentes

1. Na listagem, clique no **título do depoimento** que deseja editar
2. Faça as alterações necessárias
3. Clique em **"Atualizar"**

## 📊 Visualização na Listagem

A listagem mostra:
- **Foto** do cliente (miniatura circular)
- **Título** do depoimento
- **Cliente** (nome e empresa)
- **Avaliação** (estrelas)
- **Destaque** (se marcado como destaque)
- **Depoimento** (resumo do texto)
- **Data** de criação

## ⭐ Sistema de Destaques

### Como Funciona
- Depoimentos marcados como **"Destaque"** aparecem primeiro
- Máximo de 3 depoimentos são exibidos por vez
- Ordenação: Destaques primeiro, depois os mais recentes

### Estratégia Recomendada
- Marque como destaque os **melhores depoimentos**
- Varie ocasionalmente para mostrar diferentes tipos de cliente
- Mantenha 2-3 depoimentos sempre em destaque

## 🎨 Melhores Práticas

### Depoimentos Eficazes
- **Específicos**: Mencionem problemas resolvidos e resultados
- **Autênticos**: Linguagem natural, não muito "marketeira"
- **Variados**: Diferentes tipos de cliente e problemas
- **Quantificados**: Incluam números quando possível (tempo, % melhoria)

### Exemplo de Bom Depoimento
```
"Meu e-commerce estava com malware e perdendo vendas. 
A equipe resolveu em 24h e ainda otimizou o site. 
Resultado: 40% mais vendas no primeiro mês!"
```

### Fotos dos Clientes
- **Formato**: Quadrado (1:1)
- **Tamanho**: 150x150px ou maior
- **Qualidade**: Nítida e bem iluminada
- **Tipo**: Foto profissional ou selfie de qualidade

## 🔄 Ordenação e Exibição

### Prioridade de Exibição
1. **Depoimentos em destaque** (ordenados por data)
2. **Depoimentos normais** (ordenados por data)

### Para Reordenar
1. Use o sistema de **"Destaque"** para priorizar
2. **Datas mais recentes** aparecem primeiro dentro de cada categoria
3. Edite e salve novamente para "atualizar" a data se necessário

## 📱 Como Aparece no Site

### Estrutura Visual
- **Card elegante** com hover effects
- **Foto circular** do cliente à esquerda
- **Nome e empresa** em destaque
- **Depoimento** em formato de citação
- **Estrelas de avaliação** abaixo
- **Link no nome** se website fornecido

### Responsive Design
- **Desktop**: 3 colunas
- **Tablet**: 2 colunas
- **Mobile**: 1 coluna

## 🔧 Para Desenvolvedores

### Query Personalizada
Os depoimentos são carregados com prioridade para destacados:

```php
$testimonials_query = new WP_Query([
    'post_type' => 'testimonial',
    'posts_per_page' => 3,
    'meta_query' => [
        'relation' => 'OR',
        ['key' => '_featured', 'value' => '1'],
        ['key' => '_featured', 'compare' => 'NOT EXISTS']
    ],
    'orderby' => ['meta_value' => 'DESC', 'date' => 'DESC']
]);
```

### Meta Fields Disponíveis
- `_client_name`: Nome do cliente
- `_client_company`: Empresa/área
- `_client_website`: Website do cliente
- `_rating`: Avaliação (1-5)
- `_featured`: Destaque (1 ou vazio)

### Featured Image
- Usada como foto do cliente
- Fallback: Círculo com inicial do nome

## 🆘 Solução de Problemas

### Depoimentos não aparecem no site
1. Verifique se estão **publicados** (não em rascunho)
2. Confirme se o **nome do cliente** está preenchido
3. Limpe o cache do site se usar plugin de cache

### Fotos não aparecem corretamente
1. Verifique se a **imagem destacada** foi definida
2. Use imagens **quadradas** para melhor resultado
3. Tamanho mínimo recomendado: 150x150px

### Ordem incorreta dos depoimentos
1. Use o sistema de **"Destaque"** para priorizar
2. Depoimentos mais **recentes** aparecem primeiro
3. **Edite e salve** para "renovar" a data se necessário

### Estrelas não aparecem
1. Verifique se a **avaliação** foi selecionada
2. Salve o depoimento novamente
3. Certifique-se de que não há conflitos de CSS

## 💡 Dicas de Conversão

### Coletando Depoimentos
- **Peça especificamente** após resolver um problema
- **Facilite o processo**: envie perguntas direcionadas
- **Ofereça incentivos**: desconto na próxima manutenção
- **Use follow-up**: entre em contato algumas semanas depois

### Perguntas Sugeridas para Clientes
1. Qual era o problema específico com seu site?
2. Como nossa solução ajudou seu negócio?
3. Qual foi o resultado mais importante para você?
4. Recomendaria nossos serviços? Por quê?

### Otimização para Conversão
- **Varie os tipos** de problema resolvido
- **Inclua resultados** quantificáveis
- **Mostre diferentes** perfis de cliente
- **Atualize regularmente** com novos depoimentos