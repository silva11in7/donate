<?php
// security/rate_limit.php - IP-based Rate Limiting
// Simple mechanism to prevent brute force on sensitive endpoints.

class RateLimiter {
    private static $max_attempts = 5;
    private static $lockout_time = 900; // 15 minutes

    public static function check($action) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $key = 'rate_' . $action . '_' . $_SERVER['REMOTE_ADDR'];
        $attempts = $_SESSION[$key . '_count'] ?? 0;
        $last_attempt = $_SESSION[$key . '_last'] ?? 0;

        if ($attempts >= self::$max_attempts && (time() - $last_attempt) < self::$lockout_time) {
            $remaining = self::$lockout_time - (time() - $last_attempt);
            header('HTTP/1.1 429 Too Many Requests');
            die("<h1>429 Too Many Requests</h1><p>Muitas tentativas. Tente novamente em " . ceil($remaining / 60) . " minutos.</p>");
        }
    }

    public static function registerAttempt($action) {
        $key = 'rate_' . $action . '_' . $_SERVER['REMOTE_ADDR'];
        $_SESSION[$key . '_count'] = ($_SESSION[$key . '_count'] ?? 0) + 1;
        $_SESSION[$key . '_last'] = time();
    }

    public static function clear($action) {
        $key = 'rate_' . $action . '_' . $_SERVER['REMOTE_ADDR'];
        unset($_SESSION[$key . '_count']);
        unset($_SESSION[$key . '_last']);
    }
}
?>
