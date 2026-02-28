<?php
// security/waf.php - Web Application Firewall
// Detects and blocks common web attacks like SQLi, XSS, and Path Traversal.

class AntigravityWAF {
    private static $malicious_patterns = [
        'sqli' => [
            '/(union\s+select|select\s+.*\s+from|insert\s+into|update\s+.*\s+set|delete\s+from|drop\s+table)/i',
            '/(\'|--|#|\/\*|\*\/)/',
            '/(sleep\(\d+\)|benchmark\s*\(|extractvalue|updatexml)/i'
        ],
        'xss' => [
            '/(<script|script>|<iframe|iframe>|<object|object>|<embed|embed>|<style|style>)/i',
            '/(javascript:|onload=|onclick=|onerror=|onmouseover=)/i',
            '/(<img\s+src=.*onerror=)/i'
        ],
        'traversal' => [
            '/(\.\.\/|\.\.\\\\)/',
            '/(etc\/passwd|windows\/win\.ini|boot\.ini)/i'
        ],
        'bots' => [
            '/(sqlmap|nikto|dirbuster|gobuster|acunetix|w3af|metasploit)/i'
        ]
    ];

    public static function run() {
        self::checkPayloads();
        self::checkUserAgent();
    }

    private static function checkPayloads() {
        $data = [$_GET, $_POST, $_COOKIE, $_REQUEST];
        foreach ($data as $input) {
            self::inspectRecursive($input);
        }
    }

    private static function inspectRecursive($input) {
        if (is_array($input)) {
            foreach ($input as $v) {
                self::inspectRecursive($v);
            }
        } else {
            self::detectAttack($input);
        }
    }

    private static function detectAttack($value) {
        if (empty($value)) return;

        foreach (self::$malicious_patterns as $type => $patterns) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    self::blockRequest($type, $value);
                }
            }
        }
    }

    private static function checkUserAgent() {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        foreach (self::$malicious_patterns['bots'] as $pattern) {
            if (preg_match($pattern, $ua)) {
                self::blockRequest('bot_attack', $ua);
            }
        }
    }

    private static function blockRequest($type, $offending_data) {
        // Log the attack (optional, could be added to a DB table)
        header('HTTP/1.1 403 Forbidden');
        echo "<h1>403 Forbidden</h1>";
        echo "<p>Security violation detected ($type). Your request has been blocked for safety.</p>";
        exit;
    }
}
?>
