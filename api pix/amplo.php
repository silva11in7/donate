<?php
// api pix/amplo.php
require_once '../admin/config.php';

header('Content-Type: application/json');

// Get active gateway configuration
try {
    // Force lowercase comparison for resilience
    $stmt = $pdo->prepare("SELECT * FROM gateways WHERE LOWER(name) = 'amplo' LIMIT 1");
    $stmt->execute();
    $gw = $stmt->fetch();

    if (!$gw) {
        // Auto-create if completely missing
        $pdo->prepare("INSERT INTO gateways (name, active) VALUES ('Amplo', 1)")->execute();
        $stmt->execute();
        $gw = $stmt->fetch();
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Erro ao acessar banco de dados: ' . $e->getMessage()]);
    exit;
}

if (!$gw) {
    echo json_encode([
        'success' => false, 
        'error' => 'Configuração Amplo Pay não encontrada no banco.',
        'debug_available' => $pdo->query("SELECT name FROM gateways")->fetchAll(PDO::FETCH_COLUMN)
    ]);
    exit;
}

$config = json_decode($gw['config_json'] ?? '{}', true);

// Keys must be configured via Admin > Gateways panel
if (empty($config['public_key']) || empty($config['secret_key'])) {
    echo json_encode(['success' => false, 'error' => 'Chaves da Amplo Pay não configuradas. Acesse Admin > Gateways.']);
    exit;
}

$public_key = $config['public_key'];
$secret_key = $config['secret_key'];

$input = json_decode(file_get_contents('php://input'), true);
$amount = $input['amount'] ?? 0;
$name = $input['name'] ?? 'Doador Anônimo';
$email = $input['email'] ?? 'doador@exemplo.com';
$phone = $input['phone'] ?? '';
$document = $input['document'] ?? '000.000.000-00';

if (!$amount) {
    file_put_contents('amplo_error.log', "[" . date('Y-m-d H:i:s') . "] Valor inválido: " . json_encode($input) . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'error' => 'Valor inválido.']);
    exit;
}

// Robust phone sanitization for Brazil
$phone = preg_replace('/\D/', '', $phone); // Remove any non-digit
$document = preg_replace('/\D/', '', $document); // Remove any non-digit
if (strlen($phone) === 11 && substr($phone, 0, 1) === '0') {
    $phone = substr($phone, 1); // Remove leading 0 if it exists
}

// Amplo documentation says (11) 99999-9999. Usually means no 55 for national sales unless requested.
// If it's 10 or 11 digits, we assume it's a clean Brazilian number without country code.
// Don't prepend 55 automatically unless it's clear it's too short (like missing DDD).
if (strlen($phone) <= 9) {
    file_put_contents('amplo_error.log', "[" . date('Y-m-d H:i:s') . "] Telefone muito curto: $phone\n", FILE_APPEND);
}

// Prepare identifier
$identifier = 'vakinha_' . uniqid();

// Amplo Pay API Request
$body = [
    "identifier" => $identifier,
    "amount" => (float)$amount,
    "client" => [
        "name" => $name,
        "email" => $email,
        "phone" => $phone,
        "document" => $document
    ],
    "products" => [
        [
            "id" => "doacao",
            "name" => "Doação Solidária",
            "quantity" => 1,
            "price" => (float)$amount
        ]
    ]
];

$ch = curl_init('https://app.amplopay.com/api/v1/gateway/pix/receive');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'x-public-key: ' . $public_key,
    'x-secret-key: ' . $secret_key
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Debug Log
$debug_data = [
    'time' => date('Y-m-d H:i:s'),
    'url' => 'https://app.amplopay.com/api/v1/gateway/pix/receive',
    'body_sent' => $body,
    'http_code' => $http_code,
    'raw_response' => $response,
    'curl_error' => $curl_error
];
file_put_contents('amplo_debug.log', json_encode($debug_data, JSON_PRETTY_PRINT) . "\n---\n", FILE_APPEND);

$resData = json_decode($response, true);

if ($http_code === 201 && isset($resData['pix'])) {
    echo json_encode([
        'success' => true,
        'pix_code' => $resData['pix']['code'],
        'qr_code_image' => $resData['pix']['image'] ?? $resData['pix']['base64'],
        'transaction_id' => $resData['transactionId']
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'error' => $resData['message'] ?? 'Erro no gateway Amplo Pay.',
        'details' => $resData
    ]);
}
?>
