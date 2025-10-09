/**
 * Script de diagnóstico para problemas de scroll
 * Adiciona logs detalhados e verificações de performance
 */
(function() {
    'use strict';
    
    // Diagnosticar problemas de performance
    const diagnostics = {
        scrollEvents: 0,
        scrollTime: 0,
        errors: []
    };
    
    // Monitor de eventos de scroll
    let scrollStart = null;
    
    window.addEventListener('scroll', function() {
        if (!scrollStart) {
            scrollStart = performance.now();
        }
        
        diagnostics.scrollEvents++;
        
        // Verificar se há lag excessivo
        const scrollTime = performance.now() - scrollStart;
        if (scrollTime > 16) { // Mais que 1 frame (16ms)
            console.warn('Scroll lag detectado:', scrollTime + 'ms');
        }
        
        scrollStart = performance.now();
    }, { passive: true });
    
    // Monitor de erros JavaScript
    window.addEventListener('error', function(e) {
        diagnostics.errors.push({
            message: e.message,
            source: e.filename,
            line: e.lineno,
            column: e.colno,
            stack: e.error ? e.error.stack : null
        });
        
        if (e.filename && e.filename.includes('main.js')) {
            console.error('Erro no tema WP Resgate:', {
                message: e.message,
                line: e.lineno,
                column: e.colno
            });
        }
    });
    
    // Verificar se elementos essenciais existem
    document.addEventListener('DOMContentLoaded', function() {
        const essentialElements = [
            '.navbar',
            '.hero',
            'body'
        ];
        
        essentialElements.forEach(selector => {
            const element = document.querySelector(selector);
            if (!element) {
                console.warn('Elemento essencial não encontrado:', selector);
            }
        });
        
        // Verificar se Bootstrap está carregado
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap JavaScript não carregado!');
        }
        
        // Verificar se jQuery está presente (não deveria ser necessário)
        if (typeof jQuery !== 'undefined') {
            console.info('jQuery detectado - pode não ser necessário');
        }
        
        // Log de status após 3 segundos
        setTimeout(() => {
            console.log('Diagnóstico WP Resgate:', {
                scrollEvents: diagnostics.scrollEvents,
                errors: diagnostics.errors.length,
                performance: {
                    domInteractive: performance.timing.domInteractive - performance.timing.navigationStart,
                    domComplete: performance.timing.domComplete - performance.timing.navigationStart
                }
            });
        }, 3000);
    });
    
    // Expor diagnósticos globalmente
    window.WPResgateDiagnostics = diagnostics;
    
})();