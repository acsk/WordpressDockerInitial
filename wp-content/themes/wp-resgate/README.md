# WP Resgate Theme

Template WordPress profissional para serviços de WordPress com Bootstrap 5 e melhores práticas de desenvolvimento.

## 🚀 Características

- **Bootstrap 5** - Framework CSS moderno e responsivo
- **SEO Ready** - Otimizado para motores de busca
- **Performance** - Carregamento rápido e otimizado
- **Acessibilidade** - WCAG 2.1 AA compliant
- **Responsivo** - Funciona em todos os dispositivos
- **Customizer** - Configurações fáceis via WordPress Customizer
- **Translation Ready** - Pronto para tradução

## 📋 Requisitos

- WordPress 5.0+
- PHP 8.0+
- MySQL 5.7+ ou MariaDB 10.3+

## 🛠️ Instalação

1. Faça upload da pasta do tema para `/wp-content/themes/`
2. Ative o tema no painel do WordPress
3. Configure as opções no Customizer (Aparência > Personalizar)

## ⚙️ Configuração

### Customizer

Vá em **Aparência > Personalizar** para configurar:

#### Configurações Gerais
- Logo do site
- Número do WhatsApp

#### Seção Hero
- Título principal
- Subtítulo/descrição
- Imagem do hero

### Menus

Configure os menus em **Aparência > Menus**:

- **Menu Principal** - Navegação principal do site
- **Menu do Rodapé** - Links do rodapé

### Widgets

Áreas de widgets disponíveis:

- **Sidebar Principal** - Barra lateral
- **Rodapé** - Área do rodapé

## 📝 Recursos Incluídos

### Formulário de Diagnóstico
- Validação em tempo real
- Proteção anti-spam (honeypot)
- Envio por AJAX
- Integração com analytics

### SEO
- Schema.org structured data
- Meta tags otimizadas
- Open Graph tags
- Sitemap XML ready

### Performance
- CSS e JS minificados
- Lazy loading de imagens
- Critical CSS inline
- Cache-friendly

### Acessibilidade
- Skip links
- ARIA labels
- Navegação por teclado
- Alto contraste
- Screen reader friendly

## 🎨 Customização

### CSS Customizado

Adicione CSS customizado em **Aparência > Personalizar > CSS Adicional** ou crie um tema filho.

### JavaScript

Para adicionar JavaScript customizado, use o hook `wp_enqueue_scripts`:

```php
function meu_custom_script() {
    wp_enqueue_script(
        'meu-script',
        get_template_directory_uri() . '/js/meu-script.js',
        ['wp-resgate-script'],
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'meu_custom_script');
```

### Hooks Disponíveis

O tema oferece varios hooks para customização:

- `wp_resgate_before_header`
- `wp_resgate_after_header`
- `wp_resgate_before_footer`
- `wp_resgate_after_footer`

## 📊 Analytics

### Google Analytics 4

Adicione no `functions.php` do tema filho:

```php
function adicionar_gtag() {
    ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'GA_MEASUREMENT_ID');
    </script>
    <?php
}
add_action('wp_head', 'adicionar_gtag');
```

### Facebook Pixel

Similar ao GA4, adicione o código do Facebook Pixel no `wp_head`.

## 🔧 Troubleshooting

### Problema: Formulário não envia
**Solução**: Verifique se o AJAX está funcionando e se não há conflitos com plugins.

### Problema: Imagens não carregam
**Solução**: Certifique-se de que as imagens estão no tamanho correto e otimizadas.

### Problema: CSS não aplica
**Solução**: Limpe o cache do site e do navegador.

## 📱 Suporte a Dispositivos

- Desktop (1200px+)
- Tablet (768px - 1199px)
- Mobile (320px - 767px)

## 🔒 Segurança

- Sanitização de inputs
- Nonces para formulários
- Escape de outputs
- Proteção contra XSS
- Validação server-side

## 🌐 Traduções

O tema está preparado para traduções. Para criar uma tradução:

1. Use o arquivo `languages/wp-resgate.pot`
2. Crie arquivos `.po` e `.mo` para seu idioma
3. Coloque na pasta `languages/`

## 📈 Performance

### Core Web Vitals
- LCP (Largest Contentful Paint) < 2.5s
- FID (First Input Delay) < 100ms
- CLS (Cumulative Layout Shift) < 0.1

### Otimizações Incluídas
- Lazy loading
- Critical CSS inline
- Preload de recursos importantes
- Minificação de assets
- Compressão de imagens

## 🤝 Contribuição

Para contribuir com o desenvolvimento:

1. Fork o repositório
2. Crie uma branch para sua feature
3. Faça commit das mudanças
4. Abra um Pull Request

## 📄 Changelog

### v1.0.0 - 2025-10-07
- Lançamento inicial
- Bootstrap 5 integrado
- Formulário de diagnóstico
- SEO otimizado
- Acessibilidade implementada

## 📞 Suporte

Para suporte e dúvidas:
- Email: suporte@wpresgate.com
- WhatsApp: +55 11 99999-9999

## 📜 Licença

GPL v2 ou posterior. Veja o arquivo LICENSE para detalhes.