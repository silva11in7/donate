<?php
// api pix/amplo.php
require_once __DIR__ . '/../admin/config.php';
require_once __DIR__ . '/../include/utmfy_helper.php';
require_once __DIR__ . '/../include/tiktok_helper.php';

if (!function_exists('execute_amplo_payment')) {
    function execute_amplo_payment($identifier, $amount, $name, $email, $phone, $document, $config) {
        if (empty($config['public_key']) || empty($config['secret_key'])) {
            return ['success' => false, 'error' => 'Chaves da Amplo Pay não configuradas.'];
        }

        $public_key = $config['public_key'];
        $secret_key = $config['secret_key'];

        // Phone formatting: Clean digits only (expecting 10 or 11 digits for BR)
        $phone_clean = preg_replace('/\D/', '', $phone);
        $document_clean = preg_replace('/\D/', '', $document);

        $dueDate = date('Y-m-d', strtotime('+3 days'));

        $body = [
            "identifier" => $identifier,
            "amount" => (float)$amount,
            "client" => [
                "name" => $name,
                "email" => $email,
                "phone" => $phone_clean,
                "document" => $document_clean
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
        curl_close($ch);

        $resData = json_decode($response, true);

        if ($http_code === 201 && isset($resData['pix'])) {
            return [
                'success' => true,
                'pix_code' => $resData['pix']['code'],
                'qr_code_image' => $resData['pix']['image'] ?? $resData['pix']['base64'],
                'transaction_id' => $resData['transactionId']
            ];
        } else {
            return [
                'success' => false, 
                'error' => $resData['message'] ?? 'Erro no gateway Amplo Pay.',
                'details' => $resData
            ];
        }
    }
}

if (basename($_SERVER['PHP_SELF']) == 'amplo.php') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $pdo->prepare("SELECT * FROM gateways WHERE LOWER(name) = 'amplo' LIMIT 1");
    $stmt->execute();
    $gw = $stmt->fetch();
    $config = json_decode($gw['config_json'] ?? '{}', true);

    $amount = (float)($input['amount'] ?? 0);
    if (!$amount) {
        echo json_encode(['success' => false, 'error' => 'Valor inválido.']);
        exit;
    }

    $identifier = 'vakinha_' . uniqid();
    $result = execute_amplo_payment(
        $identifier, 
        $amount, 
        $input['name'] ?? 'Doador Anônimo', 
        $input['email'] ?? 'doador@exemplo.com', 
        $input['phone'] ?? '', 
        $input['document'] ?? '000.000.000-00', 
        $config
    );

    if ($result['success']) {
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
