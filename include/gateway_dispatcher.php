<?php
// include/gateway_dispatcher.php
require_once __DIR__ . '/../admin/config.php';
require_once __DIR__ . '/utmfy_helper.php';
require_once __DIR__ . '/tiktok_helper.php';

/**
 * Main Dispatcher: Standardizes and routes payment requests to the active provider.
 */
function create_payment($data) {
    global $pdo;

    $amount = (float)($data['amount'] ?? 0);
    $name = $data['name'] ?? 'Doador Anônimo';
    $email = $data['email'] ?? 'doador@exemplo.com';
    $phone = $data['phone'] ?? '';
    $document = $data['document'] ?? '000.000.000-00';
    $tracking = $data['tracking'] ?? [];
    
    // Identifier for tracking across systems
    $identifier = 'vakinha_' . uniqid();

    // 1. Fetch Active Gateway
    $stmt = $pdo->prepare("SELECT * FROM gateways WHERE active = 1 LIMIT 1");
    $stmt->execute();
    $gw = $stmt->fetch();

    if (!$gw) {
        return ['success' => false, 'error' => 'Nenhum gateway ativo configurado.'];
    }

    // 2. Dynamic Routing
    $provider_raw = $gw['name'];
    $provider = strtolower(str_replace(' ', '', $provider_raw)); // e.g. "Perfect Pay" -> "perfectpay"
    $config = json_decode($gw['config_json'] ?? '{}', true);

    $gateway_file = __DIR__ . "/../api pix/{$provider}.php";
    $function_name = "execute_{$provider}_payment";

    if (!file_exists($gateway_file)) {
        // Fallback or specific mappings
        if ($provider === 'perfectpay') {
            $gateway_file = __DIR__ . "/../api pix/amplo.php";
            $function_name = "execute_amplo_payment";
        }
    }

    if (!file_exists($gateway_file)) {
        return ['success' => false, 'error' => "Arquivo de integração não encontrado: $provider.php"];
    }

    require_once $gateway_file;

    if (!function_exists($function_name)) {
        return ['success' => false, 'error' => "Função $function_name não definida em $provider.php"];
    }

    // Call the dynamic function
    $result = $function_name($identifier, $amount, $name, $email, $phone, $document, $config);

    // 3. Handle Tracking if payment was initiated successfully
    if ($result && isset($result['success']) && $result['success']) {
        // UTMfy - Waiting
        send_utmfy_order($identifier, 'waiting', [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ], [
            'id' => 'doacao_solidaria',
            'name' => 'Doação Solidária',
            'price' => $amount
        ], $tracking);

        // TikTok - InitiateCheckout
        send_tiktok_event('InitiateCheckout', $identifier, [
            'email' => $email,
            'phone' => $phone,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'ttclid' => $tracking['ttclid'] ?? null
        ], [
            'value' => $amount,
            'currency' => 'BRL',
            'contents' => [['id' => 'doacao', 'quantity' => 1, 'price' => $amount]]
        ]);
        
        $result['identifier'] = $identifier;
    }

    return $result;
}
?>
