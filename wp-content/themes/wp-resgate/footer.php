</main>

<footer class="py-4 border-top" role="contentinfo">
    <div class="container d-flex flex-column flex-lg-row gap-2 justify-content-between align-items-center">
        <div class="small text-muted">
            © <span id="current-year"></span> 
            <?php bloginfo('name'); ?> — <?php esc_html_e('Soluções WordPress', 'wp-resgate'); ?>
        </div>
        
        <div class="small">
            <?php
            wp_nav_menu([
                'theme_location' => 'footer',
                'container' => false,
                'menu_class' => 'd-inline',
                'link_before' => '',
                'link_after' => '',
                'fallback_cb' => 'wp_resgate_footer_fallback_menu',
                'depth' => 1
            ]);
            ?>
        </div>
    </div>
    
    <?php if (is_active_sidebar('footer')) : ?>
        <div class="container mt-4">
            <div class="row">
                <div class="col">
                    <?php dynamic_sidebar('footer'); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</footer>

<!-- Floating WhatsApp CTA - Apenas Desktop -->
<?php 
$whatsapp = get_theme_mod('wp_resgate_whatsapp');
if ($whatsapp) : ?>
    <div class="floating-whatsapp d-none d-md-block">
        <a href="https://wa.me/<?php echo esc_attr($whatsapp); ?>" 
           class="btn btn-success shadow-soft whatsapp-btn" 
           target="_blank" 
           rel="noopener"
           aria-label="<?php esc_attr_e('Fale conosco no WhatsApp', 'wp-resgate'); ?>">
            <i class="bi bi-whatsapp me-2" aria-hidden="true"></i> 
            <span class="d-none d-sm-inline"><?php esc_html_e('Fale conosco no WhatsApp', 'wp-resgate'); ?></span>
            <span class="d-sm-none"><?php esc_html_e('WhatsApp', 'wp-resgate'); ?></span>
        </a>
    </div>
<?php endif; ?>

<!-- Bottom Navigation Mobile - Estilo App -->
<nav class="bottom-nav-mobile" role="navigation" aria-label="<?php esc_attr_e('Navegação Mobile', 'wp-resgate'); ?>">
    <ul class="bottom-nav-items">
        <li class="bottom-nav-item">
            <a href="#hero" class="bottom-nav-link active" data-section="hero">
                <i class="bi bi-house-fill bottom-nav-icon" aria-hidden="true"></i>
                <span class="bottom-nav-label"><?php esc_html_e('Início', 'wp-resgate'); ?></span>
            </a>
        </li>
        
        <li class="bottom-nav-item">
            <a href="#servicos" class="bottom-nav-link" data-section="servicos">
                <i class="bi bi-gear-fill bottom-nav-icon" aria-hidden="true"></i>
                <span class="bottom-nav-label"><?php esc_html_e('Serviços', 'wp-resgate'); ?></span>
            </a>
        </li>
        
        <li class="bottom-nav-item">
            <a href="#como-funciona" class="bottom-nav-link" data-section="como-funciona">
                <i class="bi bi-list-check bottom-nav-icon" aria-hidden="true"></i>
                <span class="bottom-nav-label"><?php esc_html_e('Processo', 'wp-resgate'); ?></span>
            </a>
        </li>
        
        <li class="bottom-nav-item">
            <a href="#depoimentos" class="bottom-nav-link" data-section="depoimentos">
                <i class="bi bi-chat-quote-fill bottom-nav-icon" aria-hidden="true"></i>
                <span class="bottom-nav-label"><?php esc_html_e('Avaliações', 'wp-resgate'); ?></span>
            </a>
        </li>
        
        <li class="bottom-nav-item">
            <?php if ($whatsapp) : ?>
                <a href="https://wa.me/<?php echo esc_attr($whatsapp); ?>" 
                   class="bottom-nav-link" 
                   target="_blank" 
                   rel="noopener"
                   data-section="whatsapp">
                    <i class="bi bi-whatsapp bottom-nav-icon" aria-hidden="true"></i>
                    <span class="bottom-nav-label"><?php esc_html_e('WhatsApp', 'wp-resgate'); ?></span>
                </a>
            <?php else : ?>
                <a href="#diagnostico" class="bottom-nav-link" data-section="diagnostico">
                    <i class="bi bi-envelope-fill bottom-nav-icon" aria-hidden="true"></i>
                    <span class="bottom-nav-label"><?php esc_html_e('Contato', 'wp-resgate'); ?></span>
                </a>
            <?php endif; ?>
        </li>
    </ul>
</nav>

<!-- Schema.org Structured Data -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "<?php bloginfo('name'); ?>",
    "description": "<?php bloginfo('description'); ?>",
    "url": "<?php echo esc_url(home_url()); ?>",
    "telephone": "<?php echo esc_attr($whatsapp ? '+' . $whatsapp : ''); ?>",
    "address": {
        "@type": "PostalAddress",
        "addressCountry": "BR"
    },
    "serviceType": "WordPress Development and Maintenance",
    "areaServed": {
        "@type": "Country",
        "name": "Brazil"
    }
}
</script>

<?php wp_footer(); ?>

<script>
    // Ano no rodapé
    document.getElementById('current-year').textContent = new Date().getFullYear();
    
    // Smooth scrolling para âncoras
    document.querySelectorAll('a[href^="#"]:not([target="_blank"])').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Bottom Navigation - Controle de seção ativa
    function updateActiveNavItem() {
        const sections = ['hero', 'servicos', 'como-funciona', 'depoimentos', 'diagnostico'];
        const navLinks = document.querySelectorAll('.bottom-nav-link[data-section]');
        
        let currentSection = 'hero';
        
        sections.forEach(sectionId => {
            const section = document.getElementById(sectionId);
            if (section) {
                const rect = section.getBoundingClientRect();
                const isVisible = rect.top <= window.innerHeight / 2 && rect.bottom >= window.innerHeight / 2;
                
                if (isVisible) {
                    currentSection = sectionId;
                }
            }
        });
        
        navLinks.forEach(link => {
            const section = link.getAttribute('data-section');
            if (section === currentSection) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }
    
    // Atualizar navegação ativa no scroll (apenas mobile)
    if (window.innerWidth <= 768) {
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(updateActiveNavItem, 50);
        });
        
        // Atualizar inicialmente
        updateActiveNavItem();
    }
    
    // Analytics de eventos (exemplo)
    function trackEvent(eventName, eventData = {}) {
        if (typeof gtag !== 'undefined') {
            gtag('event', eventName, eventData);
        }
        
        if (typeof fbq !== 'undefined') {
            fbq('track', eventName, eventData);
        }
        
        console.log('Event tracked:', eventName, eventData);
    }
    
    // Rastrear cliques em CTAs importantes
    document.querySelectorAll('a[href="#diagnostico"], .btn[href="#diagnostico"]').forEach(btn => {
        btn.addEventListener('click', () => {
            // Identificar se é um CTA de serviço
            const isServiceCTA = btn.classList.contains('service-cta-btn');
            const serviceCard = isServiceCTA ? btn.closest('.service-card') : null;
            const serviceTitle = serviceCard ? serviceCard.querySelector('.service-title')?.textContent : null;
            
            trackEvent('diagnostic_cta_click', {
                element: btn.textContent.trim(),
                page: window.location.pathname,
                source: isServiceCTA ? 'service_card' : 'general',
                service: serviceTitle || 'unknown'
            });
        });
    });
    
    // Rastrear cliques no WhatsApp
    document.querySelectorAll('a[href^="https://wa.me/"]').forEach(btn => {
        btn.addEventListener('click', () => {
            trackEvent('whatsapp_click', {
                page: window.location.pathname
            });
        });
    });
</script>

</body>
</html>

<?php
/**
 * Menu fallback para o rodapé
 */
function wp_resgate_footer_fallback_menu() {
    echo '<a href="#" class="text-decoration-none me-3">' . esc_html__('Política de Privacidade', 'wp-resgate') . '</a>';
    echo '<a href="#" class="text-decoration-none">' . esc_html__('Termos de Uso', 'wp-resgate') . '</a>';
}
?>