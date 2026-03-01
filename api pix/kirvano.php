<?php
// api pix/kirvano.php
// Kirvano integration - Standardized for Vakinha Premium

if (!function_exists('execute_kirvano_payment')) {
    function execute_kirvano_payment($identifier, $amount, $name, $email, $phone, $document, $config) {
        if (empty($config['public_key']) || empty($config['secret_key'])) {
            return ['success' => false, 'error' => 'Chaves da Kirvano não configuradas.'];
        }

        $public_key = $config['public_key'];
        $secret_key = $config['secret_key'];

        $phone_clean = preg_replace('/\D/', '', $phone);
        $document_clean = preg_replace('/\D/', '', $document);

        $body = [
            "identifier" => $identifier,
            "amount" => (float)$amount,
            "client" => [
                "name" => $name,
                "email" => $email,
                "phone" => $phone_clean,
                "document" => $document_clean
            ]
        ];

        // Placeholder for Kirvano API - adjust URL if necessary
        $ch = curl_init('https://api.kirvano.com/v1/pix/receive'); 
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
                'qr_code_image' => $resData['pix']['base64'] ?? null,
                'transaction_id' => $resData['transactionId'] ?? $identifier
            ];
        } else {
            return [
                'success' => false, 
                'error' => $resData['message'] ?? 'Erro no gateway Kirvano.',
                'details' => $resData
            ];
        }
    }
}
?>
