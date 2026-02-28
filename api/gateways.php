<?php
// api/gateways.php
require_once '../admin/config.php';

header('Content-Type: application/json');

// Auth check
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? ($_GET['action'] ?? 'list');

if ($action === 'list') {
    $stmt = $pdo->query("SELECT * FROM gateways");
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
} elseif ($action === 'activate') {
    $id = (int)($input['id'] ?? 0);
    $api_key = $input['api_key'] ?? '';
    
    try {
        $pdo->beginTransaction();
        $pdo->query("UPDATE gateways SET active = 0");
        $stmt = $pdo->prepare("UPDATE gateways SET active = 1, config_json = ? WHERE id = ?");
        $stmt->execute([json_encode(['api_key' => $api_key]), $id]);
        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} elseif ($action === 'add') {
    $name = $input['name'] ?? '';
    if ($name) {
        $stmt = $pdo->prepare("INSERT INTO gateways (name, active) VALUES (?, 0)");
        $stmt->execute([$name]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Nome obrigatório']);
    }
} elseif ($action === 'delete') {
    $id = (int)($input['id'] ?? 0);
    $stmt = $pdo->prepare("DELETE FROM gateways WHERE id = ? AND active = 0");
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>
