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

    $provider = strtolower($gw['name']);
    $config = json_decode($gw['config_json'] ?? '{}', true);

    // 2. Route to Provider logic
    $result = null;

    if ($provider === 'babylon') {
        $result = create_babylon_payment($identifier, $amount, $name, $email, $phone, $document, $config);
    } elseif ($provider === 'amplo' || $provider === 'perfect pay') {
        $result = create_amplo_payment($identifier, $amount, $name, $email, $phone, $document, $config);
    } elseif ($provider === 'oasyfy') {
        $result = create_oasyfy_payment($identifier, $amount, $name, $email, $phone, $document, $config);
    } elseif ($provider === 'genesys') {
        $result = create_genesys_payment($identifier, $amount, $name, $email, $phone, $document, $config);
    } else {
        return ['success' => false, 'error' => "Provedor desconhecido: $provider"];
    }

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

/** 
 * Wrappers for individual gateway calls.
 */

function create_babylon_payment($id, $amount, $name, $email, $phone, $doc, $config) {
    include_once __DIR__ . '/../api pix/babylon.php';
    return execute_babylon_payment($id, $amount, $name, $email, $phone, $doc, $config);
}

function create_amplo_payment($id, $amount, $name, $email, $phone, $doc, $config) {
    include_once __DIR__ . '/../api pix/amplo.php';
    return execute_amplo_payment($id, $amount, $name, $email, $phone, $doc, $config);
}

function create_oasyfy_payment($id, $amount, $name, $email, $phone, $doc, $config) {
    include_once __DIR__ . '/../api pix/oasyfy.php';
    return execute_oasyfy_payment($id, $amount, $name, $email, $phone, $doc, $config);
}

function create_genesys_payment($id, $amount, $name, $email, $phone, $doc, $config) {
    // Genesys is currently not implemented as a separate function-file
    return ['success' => false, 'error' => 'Gateway Genesys não implementado nesta versão.'];
}
