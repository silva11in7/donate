<?php
// include/tiktok_helper.php

/**
 * Hashes a value using SHA256 as required by TikTok (lowercase first).
 */
function hash_tiktok_value($value) {
    if (empty($value)) return null;
    return hash('sha256', strtolower(trim((string)$value)));
}

/**
 * Sends a server-side event to TikTok Ads API.
 * 
 * @param string $event_name Event name (InitiateCheckout, CompletePayment, etc)
 * @param string $event_id Unique event ID
 * @param array $user_data ['email', 'phone', 'ip', 'user_agent', 'ttclid']
 * @param array $properties ['value', 'currency', 'content_id', etc]
 */
function send_tiktok_event($event_name, $event_id = null, $user_data = [], $properties = []) {
    global $pdo;

    // Fetch TikTok settings
    $settings_raw = $pdo->query("SELECT * FROM settings WHERE \"key\" IN ('tiktok_token', 'tiktok_pixel')")->fetchAll();
    $settings = [];
    foreach ($settings_raw as $s) {
        $settings[$s['key']] = $s['value'];
    }

    $access_token = $settings['tiktok_token'] ?? '';
    $pixel_id = $settings['tiktok_pixel'] ?? '';

    if (!$access_token || !$pixel_id) return; // Silent skip if not configured

    $ttclid = $user_data['ttclid'] ?? null;
    $ip = $user_data['ip'] ?? ($_SERVER['REMOTE_ADDR'] ?? '');
    $ua = $user_data['user_agent'] ?? ($_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148');

    // Prepare User Payload
    $user_payload = [
        "ip" => $ip,
        "user_agent" => $ua
    ];

    if (!empty($user_data['email'])) {
        $user_payload["email"] = hash_tiktok_value($user_data['email']);
    }
    
    if (!empty($user_data['phone'])) {
        // TikTok expects phone in E.164 without +
        $phone_digits = preg_replace('/\D/', '', $user_data['phone']);
        $user_payload["phone_number"] = hash_tiktok_value($phone_digits);
    }
    
    if (!empty($user_data['external_id'])) {
         $user_payload["external_id"] = hash_tiktok_value($user_data['external_id']);
    }

    if ($ttclid) {
        $user_payload["ttclid"] = $ttclid;
    }

    // Event Payload
    $payload = [
        "pixel_code" => $pixel_id,
        "event" => $event_name,
        "event_id" => $event_id ?: "evt_" . uniqid() . "_" . time(),
        "timestamp" => date('Y-m-d\TH:i:s\Z', time()),
        "context" => [
            "ad" => $ttclid ? ["callback" => $ttclid] : (object)[],
            "user" => $user_payload
        ],
        "properties" => (object)($properties ?: [])
    ];

    $ch = curl_init('https://business-api.tiktok.com/open_api/v1.3/event/track/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Access-Token: ' . $access_token,
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Optional: Log for debug
    // file_put_contents('tiktok_debug.log', "[" . date('Y-m-d H:i:s') . "] Event: $event_name | Response: $http_code $response\n", FILE_APPEND);
}
