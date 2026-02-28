<?php
// api/automation.php
require_once '../admin/config.php';

header('Content-Type: application/json');

// Check auth
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? ($_GET['action'] ?? '');

$pdo->query("CREATE TABLE IF NOT EXISTS recovery_rules (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    delay_minutes INTEGER,
    message TEXT,
    active INTEGER DEFAULT 1
)");

if ($action === 'update_rule') {
    $rule_id = (int)($input['rule_id'] ?? 0);
    $delay = (int)($input['delay'] ?? 0);
    $active = (int)($input['active'] ?? 1);
    $message = $input['message'] ?? '';

    try {
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO recovery_rules (id, delay_minutes, message, active) VALUES (?, ?, ?, ?)");
        $stmt->execute([$rule_id ?: null, $delay, $message, $active]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} elseif ($action === 'list_rules') {
    $stmt = $pdo->query("SELECT * FROM recovery_rules ORDER BY delay_minutes ASC");
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>
