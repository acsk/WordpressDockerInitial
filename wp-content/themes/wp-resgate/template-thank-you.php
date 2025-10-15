<?php
/**
 * Template Name: Página de Agradecimento (WP Resgate)
 * Description: Página exibida após o envio do formulário com conversão e redirecionamento automático.
 *
 * @package WP_Resgate
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$return_url = home_url('/');
if (isset($_GET['return'])) {
    $candidate = wp_unslash((string) $_GET['return']);
    if (wp_http_validate_url($candidate)) {
        $return_url = esc_url_raw($candidate);
    }
}

$delay_seconds = (int) apply_filters('wp_resgate_thank_you_redirect_seconds', 5);
if ($delay_seconds < 1) {
    $delay_seconds = 5;
}

$tracking_id = 'AW-17649415974/iTciCIX-wKwbEKbu8t9B';

$title       = get_theme_mod('wp_resgate_thank_you_title', __('Mensagem enviada!', 'wp-resgate'));
$message     = get_theme_mod('wp_resgate_thank_you_message', __('Recebemos sua solicitação e retornaremos em breve.', 'wp-resgate'));
$submessage  = get_theme_mod('wp_resgate_thank_you_submessage', __('Você será redirecionado automaticamente.', 'wp-resgate'));
?>

<main id="main" class="wp-resgate-thank-you" role="main">
    <section class="thank-you-hero" aria-labelledby="thank-you-title">
        <div class="container">
            <div class="thank-you-card">
                <div class="thank-you-icon" aria-hidden="true">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <h1 id="thank-you-title" class="thank-you-title"><?php echo esc_html($title); ?></h1>
                <p class="thank-you-message"><?php echo esc_html($message); ?></p>
                <p class="thank-you-submessage"><?php echo esc_html($submessage); ?></p>

                <div class="thank-you-countdown" role="status" aria-live="polite">
                    <span class="countdown-label"><?php esc_html_e('Redirecionando em', 'wp-resgate'); ?></span>
                    <span id="thank-you-counter-number" class="countdown-number" data-seconds="<?php echo esc_attr($delay_seconds); ?>">
                        <?php echo esc_html($delay_seconds); ?>
                    </span>
                    <span class="countdown-suffix"><?php esc_html_e('segundos', 'wp-resgate'); ?></span>
                </div>

                <a class="btn btn-primary btn-lg mt-3" href="<?php echo esc_url($return_url); ?>">
                    <span class="me-2" aria-hidden="true">&larr;</span>
                    <?php esc_html_e('Voltar para página anterior', 'wp-resgate'); ?>
                </a>
            </div>
        </div>
    </section>
</main>

<style>
.wp-resgate-thank-you {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #0d6efd 0%, #2a4fb6 100%);
    color: #fff;
}

.wp-resgate-thank-you .container {
    max-width: 640px;
    padding: 2rem;
}

.thank-you-card {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(18px);
    padding: 3rem 2.5rem;
    border-radius: 24px;
    box-shadow: 0 30px 60px rgba(13,110,253,0.25);
    text-align: center;
}

.thank-you-icon {
    width: 88px;
    height: 88px;
    border-radius: 999px;
    background: rgba(255,255,255,0.15);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    margin-bottom: 1.5rem;
    color: #fff;
}

.thank-you-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.thank-you-message {
    font-size: 1.1rem;
    margin-bottom: 0.75rem;
}

.thank-you-submessage {
    font-size: 0.95rem;
    opacity: 0.85;
    margin-bottom: 1.25rem;
}

.thank-you-countdown {
    display: inline-flex;
    align-items: baseline;
    gap: 0.75rem;
    background: rgba(255,255,255,0.14);
    border-radius: 999px;
    padding: 0.75rem 1.5rem;
    margin-bottom: 1.5rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.thank-you-countdown .countdown-label {
    font-size: 0.95rem;
    opacity: 0.8;
}

.thank-you-countdown .countdown-number {
    font-size: 2.5rem;
    font-weight: 800;
    line-height: 1;
}

.thank-you-countdown .countdown-suffix {
    font-size: 0.9rem;
    opacity: 0.8;
}

@media (max-width: 575px) {
    .thank-you-card {
        padding: 2.5rem 1.5rem;
    }
    .thank-you-title {
        font-size: 1.75rem;
    }
}
</style>

<!-- Event snippet for Enviar formulário de lead conversion page -->
<script>
(function() {
    const delaySeconds = <?php echo (int) $delay_seconds; ?>;
    const redirectUrl = <?php echo wp_json_encode($return_url); ?>;
    const counterNumberEl = document.getElementById('thank-you-counter-number');
    let remaining = delaySeconds;

    function updateCounter() {
        if (!counterNumberEl) return;
        counterNumberEl.textContent = remaining;
    }

    function trackConversion() {
        if (typeof window.gtag === 'function') {
            window.gtag('event', 'conversion', {
                'send_to': '<?php echo esc_js($tracking_id); ?>',
                'value': 1.0,
                'currency': 'BRL'
            });
        } else {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                'event': 'conversion',
                'send_to': '<?php echo esc_js($tracking_id); ?>',
                'value': 1.0,
                'currency': 'BRL'
            });
        }
    }

    updateCounter();
    const interval = window.setInterval(function() {
        remaining -= 1;
        if (remaining <= 0) {
            window.clearInterval(interval);
        }
        updateCounter();
    }, 1000);

    window.setTimeout(function() {
        window.location.href = redirectUrl;
    }, delaySeconds * 1000);

    trackConversion();
})();
</script>

<?php
get_footer();
