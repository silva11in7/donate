<?php
// api pix/babylon.php
require_once '../admin/config.php';
require_once '../include/utmfy_helper.php';
require_once '../include/tiktok_helper.php';

header('Content-Type: application/json');

// Get active gateway configuration
try {
    $stmt = $pdo->prepare("SELECT * FROM gateways WHERE LOWER(name) = 'babylon' LIMIT 1");
    $stmt->execute();
    $gw = $stmt->fetch();

    if (!$gw) {
        $pdo->prepare("INSERT INTO gateways (name, active) VALUES ('Babylon', 1)")->execute();
        $stmt->execute();
        $gw = $stmt->fetch();
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Erro ao acessar banco de dados: ' . $e->getMessage()]);
    exit;
}

if (!$gw) {
    echo json_encode(['success' => false, 'error' => 'Configuração Babylon não encontrada.']);
    exit;
}

$config = json_decode($gw['config_json'] ?? '{}', true);

if (empty($config['api_key'])) {
    echo json_encode(['success' => false, 'error' => 'Chave da Babylon não configurada. Acesse Admin > Gateways.']);
    exit;
}

$api_key = $config['api_key'];

$input = json_decode(file_get_contents('php://input'), true);
$amount = (float)($input['amount'] ?? 0);
$name = $input['name'] ?? 'Doador Anônimo';
$email = $input['email'] ?? 'doador@exemplo.com';
$phone = $input['phone'] ?? '';
$document = $input['document'] ?? '000.000.000-00';

if (!$amount) {
    echo json_encode(['success' => false, 'error' => 'Valor inválido.']);
    exit;
}

// Phone/Doc cleaning
$phone_clean = preg_replace('/\D/', '', $phone);
if (empty($phone_clean)) $phone_clean = "11999999999";

$doc_clean = preg_replace('/\D/', '', $document);
$doc_type = (strlen($doc_clean) > 11) ? "CNPJ" : "CPF";

$identifier = 'vakinha_' . uniqid();

// Protocol and Host for callback
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? '72.61.58.79';
$callback_url = "$protocol://$host/api%20pix/webhook.php";

// Babylon API Request
$body = [
    "customer" => [
        "name" => $name,
        "email" => $email,
        "phone" => $phone_clean,
        "document" => [
            "number" => $doc_clean,
            "type" => $doc_type
        ]
    ],
    "paymentMethod" => "PIX",
    "amount" => (int)round($amount * 100), // In Cents
    "items" => [
        [
            "title" => "Doação Solidária - Vakinha",
            "unitPrice" => (int)round($amount * 100),
            "quantity" => 1
        ]
    ],
    "pix" => [
        "expiresInDays" => 1
    ],
    "metadata" => [
        "identifier" => $identifier,
        "source" => "vakinha_premium"
    ],
    "postbackUrl" => $callback_url
];

// Basic Auth
$auth = base64_encode($api_key . ":");

$ch = curl_init('https://api.bancobabylon.com/functions/v1/transactions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Basic ' . $auth
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Debug Log
$debug_data = [
    'time' => date('Y-m-d H:i:s'),
    'url' => 'https://api.bancobabylon.com/functions/v1/transactions',
    'body_sent' => $body,
    'http_code' => $http_code,
    'raw_response' => $response,
    'curl_error' => $curl_error
];
file_put_contents('babylon_debug.log', json_encode($debug_data, JSON_PRETTY_PRINT) . "\n---\n", FILE_APPEND);

$resData = json_decode($response, true);
$payload = $resData['data'] ?? $resData;

if (($http_code === 200 || $http_code === 201) && isset($payload['pix'])) {
    
    $qr_code = $payload['pix']['qrcode'] ?? $payload['pix']['qrcodeText'] ?? $payload['pix']['brcode'] ?? '';

    // Tracking - UTMfy
    send_utmfy_order($identifier, 'waiting', [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ], [
        'id' => 'doacao_solidaria',
        'name' => 'Doação Solidária',
        'price' => (float)$amount
    ], $input['tracking'] ?? []);

    // Tracking - TikTok
    send_tiktok_event('InitiateCheckout', $identifier, [
        'email' => $email,
        'phone' => $phone,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        'ttclid' => $input['tracking']['ttclid'] ?? null
    ], [
        'value' => (float)$amount,
        'currency' => 'BRL',
        'contents' => [['id' => 'doacao_solidaria', 'quantity' => 1, 'price' => (float)$amount]]
    ]);

    echo json_encode([
        'success' => true,
        'pix_code' => $qr_code,
        'qr_code_image' => null, // Regenerate on frontend or use a proxy
        'transaction_id' => $payload['id'] ?? $identifier
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'error' => $resData['message'] ?? 'Erro no gateway Babylon.',
        'details' => $resData
    ]);
}
?>
