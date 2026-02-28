<?php
// api pix/babylon.php
require_once __DIR__ . '/../admin/config.php';
require_once __DIR__ . '/../include/utmfy_helper.php';
require_once __DIR__ . '/../include/tiktok_helper.php';

if (!function_exists('execute_babylon_payment')) {
    function execute_babylon_payment($identifier, $amount, $name, $email, $phone, $document, $config) {
        if (empty($config['api_key'])) {
            return ['success' => false, 'error' => 'Chave da Babylon não configurada.'];
        }

        $api_key = $config['api_key'];
        $phone_clean = preg_replace('/\D/', '', $phone);
        if (empty($phone_clean)) $phone_clean = "11999999999";

        // Ensure 55 prefix
        if (strlen($phone_clean) === 10 || strlen($phone_clean) === 11) {
            $phone_clean = "55" . $phone_clean;
        }

        $doc_clean = preg_replace('/\D/', '', $document);
        $doc_type = (strlen($doc_clean) > 11) ? "CNPJ" : "CPF";

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
        curl_close($ch);

        $resData = json_decode($response, true);
        $payload = $resData['data'] ?? $resData;

        if (($http_code === 200 || $http_code === 201) && isset($payload['pix'])) {
            return [
                'success' => true,
                'pix_code' => $payload['pix']['qrcode'] ?? $payload['pix']['qrcodeText'] ?? $payload['pix']['brcode'] ?? '',
                'qr_code_image' => null,
                'transaction_id' => $payload['id'] ?? $identifier
            ];
        } else {
            return [
                'success' => false, 
                'error' => $resData['message'] ?? 'Erro no gateway Babylon.',
                'details' => $resData
            ];
        }
    }
}

// Handle direct POST if not included by dispatcher
if (basename($_SERVER['PHP_SELF']) == 'babylon.php') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Fetch active gateway config
    $stmt = $pdo->prepare("SELECT * FROM gateways WHERE LOWER(name) = 'babylon' LIMIT 1");
    $stmt->execute();
    $gw = $stmt->fetch();
    $config = json_decode($gw['config_json'] ?? '{}', true);

    $amount = (float)($input['amount'] ?? 0);
    if (!$amount) {
        echo json_encode(['success' => false, 'error' => 'Valor inválido.']);
        exit;
    }

    $identifier = 'vakinha_' . uniqid();
    $result = execute_babylon_payment(
        $identifier, 
        $amount, 
        $input['name'] ?? 'Doador Anônimo', 
        $input['email'] ?? 'doador@exemplo.com', 
        $input['phone'] ?? '', 
        $input['document'] ?? '000.000.000-00', 
        $config
    );

    if ($result['success']) {
        // Tracking - UTMfy
        send_utmfy_order($identifier, 'waiting', [
            'name' => $input['name'] ?? 'Doador',
            'email' => $input['email'] ?? 'doador@exemplo.com',
            'phone' => $input['phone'] ?? '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ], [
            'id' => 'doacao',
            'name' => 'Doação',
            'price' => $amount
        ], $input['tracking'] ?? []);

        // Tracking - TikTok
        send_tiktok_event('InitiateCheckout', $identifier, [
            'email' => $input['email'] ?? '',
            'phone' => $input['phone'] ?? '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ], [
            'value' => $amount,
            'currency' => 'BRL'
        ]);
    }

    echo json_encode($result);
}
?>
