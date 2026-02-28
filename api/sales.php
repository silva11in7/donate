<?php
// api/sales.php
require_once '../admin/config.php';

header('Content-Type: application/json');

// Auth check
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $stmt = $pdo->query("SELECT * FROM leads WHERE status = 'approved' ORDER BY updated_at DESC LIMIT 100");
    $sales = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $sales]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
