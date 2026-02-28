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

// Sanitize inputs
$phone = preg_replace('/\D/', '', $phone);
$document = preg_replace('/\D/', '', $document);

// Ensure Brazil country code (55) for phone if it looks like a local number
if (strlen($phone) >= 10 && substr($phone, 0, 2) !== '55') {
    $phone = '55' . $phone;
}

if (!$amount) {
    echo json_encode(['success' => false, 'error' => 'Valor inválido.']);
    exit;
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
curl_close($ch);

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
