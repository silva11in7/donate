<?php
// security/csrf.php - CSRF Protection
// Protects forms from Cross-Site Request Forgery.

class CSRFProtector {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public static function getToken() {
        return $_SESSION['csrf_token'] ?? '';
    }

    public static function validate($token) {
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    public static function hiddenInput() {
        $token = self::getToken();
        return "<input type=\"hidden\" name=\"csrf_token\" value=\"$token\">";
    }

    public static function verifyRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!self::validate($token)) {
                header('HTTP/1.1 403 Forbidden');
                die("<h1>403 Forbidden</h1><p>CSRF verification failed. Request blocked.</p>");
            }
        }
    }
}
?>
