<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <?php wp_head(); ?>
    
    <style>
        :root{--brand:#0d6efd;--brand-2:#0b5ed7}
        body{scroll-behavior:smooth}
        .hero{background: radial-gradient(1000px 500px at 80% 10%, rgba(13,110,253,.15), transparent), linear-gradient(135deg, #0d6efd 0%, #2b61d3 60%, #2a4fb6 100%); color:#fff}
        .hero .btn-outline-light:hover{color:#0d6efd;background:#fff}
        .badge-soft{background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25)}
        .shadow-soft{box-shadow:0 10px 30px rgba(0,0,0,.08)}
        .icon-circle{width:44px;height:44px;border-radius:999px;display:inline-flex;align-items:center;justify-content:center}
        .process-step{position:relative}
        .process-step::after{content:"";position:absolute;top:22px;left:calc(100% + .75rem);width:60px;height:2px;background:var(--brand);opacity:.25}
        .process-step:last-child::after{display:none}
        .floating-whatsapp{position:fixed;right:16px;bottom:16px;z-index:1050}
        .floating-whatsapp .btn{border-radius:999px;padding:.85rem 1rem}
        .logo-grid img{filter:grayscale(100%);opacity:.7;transition:all .2s}
        .logo-grid img:hover{filter:none;opacity:1}
        .bg-soft{background:#f8f9fb}
        .guarantee{border-left:4px solid var(--brand);}
        .form-cta input, .form-cta textarea{background:#fff}
        .price-card .list-group-item{border:0;padding:.5rem 0}
        
        /* Acessibilidade */
        .btn:focus, .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        /* Performance - Critical CSS inline */
        .navbar-brand { font-weight: 700; }
        .display-5 { font-weight: 700; line-height: 1.2; }
        .lead { font-size: 1.25rem; font-weight: 300; }
        
        /* Header fixo com fundo branco */
        .header-navbar {
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,.1);
            transition: all 0.3s ease;
            z-index: 999;
        }
        
        /* Ajustar para barra de admin do WordPress */
        .admin-bar .header-navbar {
            top: 32px;
        }
        
        @media screen and (max-width: 782px) {
            .admin-bar .header-navbar {
                top: 46px;
            }
        }
        
        .header-navbar .navbar {
            padding: 0.75rem 0;
        }
        
        .header-navbar .navbar-brand {
            transition: all 0.3s ease;
        }
        
        .header-navbar .nav-link {
            color: #6c757d !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        
        .header-navbar .nav-link:hover {
            color: var(--brand) !important;
            transform: translateY(-1px);
        }
        
        .header-navbar .btn-primary {
            box-shadow: 0 4px 15px rgba(13,110,253,0.3);
            border-radius: 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .header-navbar .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13,110,253,0.4);
        }
        
        /* Hero com design premium e posicionamento correto */
        .hero {
            padding-top: calc(6rem + 80px) !important;
            background: radial-gradient(1200px 600px at 20% 0%, rgba(102, 16, 242, 0.15), transparent),
                        linear-gradient(135deg, #0d6efd 0%, #6610f2 25%, #0b5ed7 50%, #2a4fb6 100%) !important;
            position: relative;
            overflow: hidden;
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="hero-pattern" width="30" height="30" patternUnits="userSpaceOnUse"><circle cx="15" cy="15" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="7" cy="7" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="23" cy="23" r="1" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23hero-pattern)"/></svg>') repeat;
            pointer-events: none;
        }
        
        .hero::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(0deg, rgba(248, 249, 251, 0.1), transparent);
            pointer-events: none;
        }
        
        /* Ajustar hero para barra de admin do WordPress */
        .admin-bar .hero {
            padding-top: calc(6rem + 80px + 32px) !important;
            min-height: calc(100vh - 80px - 32px);
        }
        
        @media screen and (max-width: 782px) {
            .admin-bar .hero {
                padding-top: calc(4rem + 70px + 46px) !important;
                min-height: calc(100vh - 70px - 46px);
            }
        }
        
        /* Smooth scroll offset para links âncora */
        html {
            scroll-padding-top: 90px;
        }
        
        /* Hero Premium Styles */
        .hero-badge {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(15px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        .hero-badge .text-primary {
            color: #0d6efd !important;
        }
        
        .hero-title {
            text-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
        }
        
        .hero-subtitle {
            color: rgba(255, 255, 255, 0.9) !important;
            text-shadow: 0 1px 10px rgba(0, 0, 0, 0.2);
            font-size: 1.3rem;
            line-height: 1.6;
        }
        
        .hero-actions .btn {
            border-radius: 15px;
            font-weight: 700;
            text-transform: none;
            letter-spacing: 0.5px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }
        
        .hero-actions .btn-light {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(15px);
        }
        
        .hero-actions .btn-light:hover {
            background: #fff;
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }
        
        .hero-actions .btn-outline-light {
            border: 2px solid rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
        }
        
        .hero-actions .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.8);
            transform: translateY(-3px);
            color: #fff !important;
        }
        
        .hero-features .icon-circle {
            width: 35px;
            height: 35px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .hero-features .icon-circle.bg-transparent {
            background: transparent !important;
            border: 2px solid rgba(255, 255, 255, 0.6) !important;
        }
        
        .hero-features .feature-item:hover .icon-circle {
            border-color: rgba(255, 255, 255, 0.9) !important;
            background: rgba(255, 255, 255, 0.1) !important;
            transform: scale(1.1);
        }
        
        .hero-card .card {
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            transition: all 0.4s ease;
        }
        
        .hero-card .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2);
        }

        
        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .header-navbar .navbar-collapse {
                background: rgba(255,255,255,0.95);
                backdrop-filter: blur(10px);
                margin-top: 1rem;
                padding: 1rem;
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            }
            
            .header-navbar .nav-item {
                margin: 0.25rem 0;
            }
            
            .hero {
                padding-top: calc(4rem + 70px) !important;
                min-height: calc(100vh - 70px);
            }
            
            .hero-title {
                font-size: 2.5rem !important;
            }
            
            .hero-subtitle {
                font-size: 1.1rem !important;
            }
            
            .hero-actions .btn {
                padding: 0.75rem 1.5rem !important;
                font-size: 0.95rem !important;
            }
        }
        
        /* Print styles */
        @media print {
            .floating-whatsapp, .header-navbar, footer { display: none !important; }
            .hero { background: #fff !important; color: #000 !important; }
            .hero { padding-top: 3rem !important; }
        }
    </style>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Skip to content (acessibilidade) -->
<a class="visually-hidden-focusable btn btn-primary" href="#main"><?php esc_html_e('Pular para o conteúdo', 'wp-resgate'); ?></a>

<!-- HEADER / NAVBAR -->
<header class="header-navbar fixed-top bg-white shadow-sm" role="banner">
    <nav class="navbar navbar-expand-lg navbar-light" role="navigation" aria-label="<?php esc_attr_e('Menu principal', 'wp-resgate'); ?>">
        <div class="container">
            <!-- Logo / Brand -->
            <?php 
            $logo = get_theme_mod('wp_resgate_logo');
            if ($logo) : ?>
                <a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>" title="<?php bloginfo('name'); ?>">
                    <img src="<?php echo esc_url($logo); ?>" alt="<?php bloginfo('name'); ?>" height="45" class="d-inline-block align-top">
                </a>
            <?php else : ?>
                <a class="navbar-brand fw-bold text-primary" href="<?php echo esc_url(home_url('/')); ?>" style="font-size: 1.5rem;">
                    <i class="bi bi-shield-check me-2"></i>
                    <?php bloginfo('name'); ?>
                </a>
            <?php endif; ?>
            
            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="<?php esc_attr_e('Alternar navegação', 'wp-resgate'); ?>">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" href="#servicos">
                            <i class="bi bi-tools me-1"></i>
                            <?php esc_html_e('Serviços', 'wp-resgate'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" href="#como-funciona">
                            <i class="bi bi-gear me-1"></i>
                            <?php esc_html_e('Como funciona', 'wp-resgate'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" href="#depoimentos">
                            <i class="bi bi-chat-quote me-1"></i>
                            <?php esc_html_e('Depoimentos', 'wp-resgate'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" href="#faq">
                            <i class="bi bi-question-circle me-1"></i>
                            <?php esc_html_e('FAQ', 'wp-resgate'); ?>
                        </a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-primary btn-sm px-3 py-2" href="#diagnostico">
                            <i class="bi bi-clipboard2-pulse me-1"></i>
                            <?php esc_html_e('Diagnóstico Gratuito', 'wp-resgate'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main id="main" role="main">
