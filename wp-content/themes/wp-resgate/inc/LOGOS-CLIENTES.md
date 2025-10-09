# Como Gerenciar os Logos de Clientes

## 📋 Visão Geral

### Especificações Técnicas

### Tamanhos Recomendados
- **Ideal**: 200x80px (proporção 2.5:1)
- **Mínimo**: 160x64px
- **Máximo**: 300x120px
- **Exibição no site**: 80px altura máxima (desktop), 65px (mobile)om Post Type **"Logos de Clientes"** permite gerenciar facilmente os logos que aparecem na seção de prova social do seu site, diretamente pelo painel do WordPress.

## 🚀 Como Acessar

1. Entre no **painel do WordPress** (wp-admin)
2. Vá em **Aparência > Logos de Clientes**
3. Você verá uma lista dos logos já cadastrados

## ➕ Adicionar Novo Logo

### Passo 1: Criar o Logo
1. Clique em **"Adicionar Novo"**
2. Preencha o **título** (nome da empresa/cliente)
3. **Não é necessário** adicionar conteúdo no editor principal

### Passo 2: Upload da Imagem
1. Na lateral direita, clique em **"Definir imagem destacada"**
2. Faça upload do logo do cliente
3. **Especificações recomendadas**:
   - **Tamanho**: 200x80px (formato landscape)
   - **Formato**: PNG com fundo transparente
   - **Resolução**: Alta qualidade (mínimo 200px de largura)

### Passo 3: Detalhes do Cliente
Na seção **"Detalhes do Cliente"**:

- **Website do Cliente**: URL do site (opcional, torna o logo clicável)
- **Descrição do Cliente**: Área de atuação (ex: "E-commerce", "Educação")
- **Texto Alternativo**: Para SEO e acessibilidade (obrigatório)

### Passo 4: Ordenação
- Use o campo **"Ordem"** (lateral direita) para definir a sequência
- Números menores aparecem primeiro

### Passo 5: Publicar
1. Clique em **"Publicar"**
2. O logo aparecerá automaticamente na seção de prova social

## ✏️ Editar Logos Existentes

1. Na listagem, clique no **título do logo** que deseja editar
2. Faça as alterações necessárias
3. Clique em **"Atualizar"**

## 📊 Visualização na Listagem

A listagem mostra:
- **Logo** (miniatura do logo)
- **Título** (nome da empresa)
- **Informações** (descrição e texto alternativo)
- **Website** (link para o site, se fornecido)
- **Ordem** (número da ordenação)
- **Data** de criação

## 🎨 Especificações Técnicas

### Tamanhos Recomendados
- **Ideal**: 200x80px (proporção 2.5:1)
- **Mínimo**: 160x60px
- **Máximo**: 300x120px

### Formatos Suportados
- **PNG** (recomendado para logos com transparência)
- **JPG** (para logos com fundo sólido)
- **SVG** (melhor qualidade, mas nem sempre suportado)

### Qualidade da Imagem
- **Alta resolução** para telas Retina
- **Fundo transparente** quando possível
- **Boa compressão** para carregamento rápido

## 📱 Como Aparece no Site

### Estrutura Visual
- **Grid responsivo**: 5 colunas desktop, 2 mobile
- **Logos em escala de cinza** por padrão
- **Colorido no hover** com animação suave
- **Elevação sutil** ao passar o mouse
- **Descrição aparece** no hover (se fornecida)
- **Links clicáveis** quando website é fornecido

### Animações
- **Entrada escalonada**: Cada logo aparece com delay
- **Hover suave**: Transição de cinza para colorido
- **Elevação**: Logo sobe ligeiramente no hover
- **Scale effect**: Pequeno aumento de tamanho

## 🔄 Ordenação dos Logos

### Como Ordenar
1. **Edite cada logo** individualmente
2. Na lateral direita, encontre **"Atributos da página"**
3. Defina a **"Ordem"** (números menores aparecem primeiro)
4. **Salve** as alterações

### Estratégia de Ordenação
1. **Clientes mais importantes** primeiro (ordem 1, 2, 3)
2. **Por setor/categoria** se relevante
3. **Por tamanho/prestígio** da empresa
4. **Balanceamento visual** considerando os logos

## 💡 Melhores Práticas

### Seleção de Clientes
- **Diversifique setores** para mostrar versatilidade
- **Inclua clientes conhecidos** quando possível
- **Mantenha relevância** com seu público-alvo
- **Atualize regularmente** com novos clientes

### Qualidade dos Logos
- **Sempre peça** logos oficiais aos clientes
- **Mantenha proporções** adequadas
- **Use alta resolução** para melhor resultado
- **Teste em diferentes** tamanhos de tela

### SEO e Acessibilidade
- **Sempre preencha** o texto alternativo
- **Use nomes descritivos** nos títulos
- **Adicione links** para sites dos clientes (se permitido)
- **Mantenha logos** com boa compressão

## 🔗 Links para Sites dos Clientes

### Quando Usar
- ✅ **Com permissão** expressa do cliente
- ✅ **Sites profissionais** e bem desenvolvidos
- ✅ **Clientes ativos** e satisfeitos
- ✅ **Relacionamento positivo** mantido

### Quando NÃO Usar
- ❌ **Sem autorização** do cliente
- ❌ **Sites temporariamente** fora do ar
- ❌ **Relacionamentos** encerrados mal
- ❌ **Concorrentes diretos** entre si

## 📈 Estratégias de Prova Social

### Mix Ideal de Clientes
- **30%** - Clientes conhecidos/grandes
- **40%** - Clientes médios diversos setores
- **30%** - Clientes pequenos/nichos específicos

### Rotação de Logos
- **Atualize mensalmente** com novos clientes
- **Remove clientes** que não querem exposição
- **Destaque temporariamente** clientes especiais
- **Mantenha sempre** 5-8 logos visíveis

## 🎯 Dicas de Conversão

### Credibilidade
- **Mostre diversidade** de setores atendidos
- **Inclua empresas** conhecidas pelo público
- **Atualize regularmente** para mostrar atividade
- **Mantenha qualidade** visual consistente

### Confiança
- **Links funcionais** aumentam credibilidade
- **Logos profissionais** transmitem seriedade
- **Descrições breves** ajudam contexto
- **Organização visual** passa profissionalismo

## 🔧 Para Desenvolvedores

### Função Helper
```php
$logos = wp_resgate_get_client_logos();
```

### Estrutura dos Dados
```php
[
    'title' => 'Nome da Empresa',
    'image' => 'url-da-imagem.png',
    'website' => 'https://site-cliente.com',
    'description' => 'Área de atuação',
    'alt_text' => 'Logo da Empresa XYZ'
]
```

### Meta Fields Disponíveis
- `_client_website`: URL do site do cliente
- `_client_description`: Descrição/área de atuação
- `_logo_alt_text`: Texto alternativo da imagem

### Ordenação
- Por `menu_order` (campo "Ordem" no admin)
- Máximo de 10 logos por performance

## 🆘 Solução de Problemas

### Logos não aparecem no site
1. Verifique se estão **publicados** (não em rascunho)
2. Confirme se a **imagem destacada** foi definida
3. Limpe o cache do site se usar plugin de cache

### Logos com qualidade ruim
1. Use **logos em alta resolução** (mínimo 200px largura)
2. Prefira **formato PNG** com fundo transparente
3. Evite **logos muito pequenos** ou pixelizados

### Links não funcionam
1. Verifique se a **URL está completa** (com http/https)
2. Teste se o **site do cliente** está funcionando
3. Confirme se o **link foi salvo** corretamente

### Ordem incorreta dos logos
1. Ajuste o campo **"Ordem"** de cada logo
2. Números **menores aparecem primeiro**
3. **Salve** as alterações após ajustar

## 💼 Considerações Legais

### Uso de Logos
- **Sempre obtenha permissão** para usar logos
- **Mantenha documentação** das autorizações
- **Respeite guidelines** de marca dos clientes
- **Remove imediatamente** se solicitado

### Direitos Autorais
- **Logos são propriedade** dos respectivos donos
- **Uso apenas** para prova social autorizada
- **Não modifique** logos sem autorização
- **Mantenha arquivos** originais fornecidos

## 📞 Suporte aos Clientes

### Pedindo Autorização
```
Olá [Nome],

Gostaríamos de incluir o logo da [Empresa] em nossa seção de 
clientes no site, como forma de demonstrar nosso trabalho. 

Você poderia nos enviar:
- Logo oficial em alta resolução (PNG preferencialmente)
- Autorização para uso no site
- Se podemos incluir link para seu site

Muito obrigado!
```

### Removendo Logo
- **Atenda imediatamente** pedidos de remoção
- **Confirme por email** que foi removido
- **Mantenha registro** da solicitação
- **Seja sempre cordial** no processo