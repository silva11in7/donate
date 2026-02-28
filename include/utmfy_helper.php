<?php
// include/utmfy_helper.php

function format_utmfy_date($date_val = null) {
    if (!$date_val) return date('Y-m-d H:i:s');
    
    if (is_string($date_val)) {
        // Clean ISO format common in Supabase/PG
        $clean = preg_replace('/(\.\d+)?Z$/', '', str_replace('T', ' ', $date_val));
        $ts = strtotime($clean);
        return $ts ? date('Y-m-d H:i:s', $ts) : date('Y-m-d H:i:s');
    }
    
    return date('Y-m-d H:i:s');
}

/**
 * Sends order to UTMfy.
 * 
 * @param string $order_id Unique ID
 * @param string $status status (waiting, paid, etc)
 * @param array $user_data ['name', 'email', 'phone', 'ip']
 * @param array $product_data ['id', 'name', 'price']
 * @param array $tracking_data ['utm_source', etc]
 */
function send_utmfy_order($order_id, $status, $user_data, $product_data, $tracking_data = []) {
    global $pdo;
    
    // Fetch settings
    $settings_raw = $pdo->query("SELECT * FROM settings WHERE \"key\" IN ('utmfy_api_token', 'utmfy_platform')")->fetchAll();
    $settings = [];
    foreach ($settings_raw as $s) {
        $settings[$s['key']] = $s['value'];
    }

    $token = $settings['utmfy_api_token'] ?? '';
    if (!$token) return; // Silent skip if no token

    $platform = $settings['utmfy_platform'] ?? 'Vakinha';

    $status_map = [
        "waiting" => "waiting_payment",
        "pending" => "waiting_payment",
        "paid" => "paid",
        "confirmed" => "paid",
        "refused" => "refused",
        "refunded" => "refunded"
    ];
    $utm_status = $status_map[strtolower($status)] ?? "waiting_payment";

    $payload = [
        "orderId" => (string)$order_id,
        "platform" => $platform,
        "paymentMethod" => "pix",
        "status" => $utm_status,
        "createdAt" => format_utmfy_date(),
        "approvedDate" => ($utm_status === 'paid') ? format_utmfy_date() : null,
        "customer" => [
            "name" => $user_data['name'] ?? 'Doador Anônimo',
            "email" => $user_data['email'] ?? 'doador@exemplo.com',
            "phone" => preg_replace('/\D/', '', $user_data['phone'] ?? '00000000000'),
            "document" => null,
            "country" => "BR"
        ],
        "products" => [
            [
                "id" => (string)($product_data['id'] ?? 'doacao'),
                "name" => $product_data['name'] ?? 'Doação Solidária',
                "quantity" => 1,
                "priceInCents" => (int)round(($product_data['price'] ?? 0) * 100)
            ]
        ],
        "trackingParameters" => [
            "utm_source" => $tracking_data['utm_source'] ?? null,
            "utm_medium" => $tracking_data['utm_medium'] ?? null,
            "utm_campaign" => $tracking_data['utm_campaign'] ?? null,
            "utm_content" => $tracking_data['utm_content'] ?? null,
            "utm_term" => $tracking_data['utm_term'] ?? null,
            "src" => $tracking_data['src'] ?? null
        ],
        "commission" => [
            "totalPriceInCents" => (int)round(($product_data['price'] ?? 0) * 100),
            "gatewayFeeInCents" => 0,
            "userCommissionInCents" => (int)round(($product_data['price'] ?? 0) * 100)
        ],
        "isTest" => false
    ];

    if (!empty($user_data['ip'])) {
        $payload['customer']['ip'] = $user_data['ip'];
    }

    $ch = curl_init('https://api.utmify.com.br/api-credentials/orders');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'x-api-token: ' . $token
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Optional: Log for debug
    // file_put_contents('utmfy_debug.log', "[" . date('Y-m-d H:i:s') . "] Status: $utm_status | Response: $http_code $response\n", FILE_APPEND);
}
