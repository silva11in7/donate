<?php
// security/firewall.php - Security Gateway
// Global entry point for security layers and headers.

require_once 'waf.php';
require_once 'csrf.php';

class AntigravityFirewall {
    public static function init() {
        // 1. Set Security Headers
        self::setHeaders();

        // 2. Run WAF
        AntigravityWAF::run();

        // 3. Init CSRF
        CSRFProtector::start();
        
        // 4. Global CSRF verification (optional, or manual per form)
        // CSRFProtector::verifyRequest(); 
    }

    private static function setHeaders() {
        // Prevent Clickjacking
        header('X-Frame-Options: SAMEORIGIN');
        
        // XSS Protection for older browsers
        header('X-XSS-Protection: 1; mode=block');
        
        // Prevent MIME sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Strictly use HTTPS (if enabled)
        // header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        
        // Content Security Policy (Basic set)
        header("Content-Security-Policy: default-src 'self' https:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:;");
    }
}

// Auto-init
AntigravityFirewall::init();
?>
