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

// Robust phone formatting for Amplo Pay (wants (XX) XXXXX-XXXX)
$digits = preg_replace('/\D/', '', $phone);
$document = preg_replace('/\D/', '', $document);

if (strlen($digits) === 11) {
    // Format: (11) 99999-9999
    $phone = "(" . substr($digits, 0, 2) . ") " . substr($digits, 2, 5) . "-" . substr($digits, 7);
} elseif (strlen($digits) === 10) {
    // Format: (11) 9999-9999
    $phone = "(" . substr($digits, 0, 2) . ") " . substr($digits, 2, 4) . "-" . substr($digits, 6);
} else {
    // Fallback to original if unknown length
    $phone = $input['phone'] ?? '';
}

// Prepare identifier
$identifier = 'vakinha_' . uniqid();
$dueDate = date('Y-m-d', strtotime('+3 days'));

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
            "id" => "doacao_solidaria",
            "name" => "Doação Solidária",
            "quantity" => 1,
            "price" => (float)$amount
        ]
    ],
    "dueDate" => $dueDate,
    "metadata" => [
        "source" => "vakinha_premium",
        "campaign" => "SOS Arthur"
    ]
];

// Determine callback URL (assuming same server)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? '72.61.58.79';
$body['callbackUrl'] = "$protocol://$host/api%20pix/webhook.php";

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
