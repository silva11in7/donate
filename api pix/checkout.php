<?php
// api pix/checkout.php
require_once __DIR__ . '/../include/gateway_dispatcher.php';
// Individual gateways are included dynamically by the dispatcher using require_once.

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['amount'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Dados de entrada inválidos ou valor ausente.']);
    exit;
}

try {
    // Call the central dispatcher
    $result = create_payment($input);
    
    if ($result) {
        echo json_encode($result);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro interno ao processar pagamento.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro sistêmico: ' . $e->getMessage()]);
}
