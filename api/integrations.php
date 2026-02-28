<?php
// api/integrations.php
require_once '../admin/config.php';

header('Content-Type: application/json');

// SIMPLE SECURITY: Check if user is logged in for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Acesso negado']);
        exit;
    }
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? ($_GET['action'] ?? '');

if ($action === 'update') {
    $type = $input['type'] ?? '';
    $token = $input['api_token'] ?? '';
    $pixel = $input['pixel_id'] ?? '';

    $pdo->query("CREATE TABLE IF NOT EXISTS settings (key TEXT PRIMARY KEY, value TEXT)");

    try {
        if ($type === 'utmfy') {
            $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('utmfy_token', ?)");
            $stmt->execute([$token]);
        } elseif ($type === 'tiktok') {
            $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('tiktok_token', ?)");
            $stmt->execute([$token]);
            $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('tiktok_pixel', ?)");
            $stmt->execute([$pixel]);
        } elseif ($type === 'openai') {
            $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('openai_token', ?)");
            $stmt->execute([$token]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Tipo inválido']);
            exit;
        }
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} elseif ($action === 'get') {
    $stmt = $pdo->query("SELECT * FROM settings");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    echo json_encode(['success' => true, 'data' => $settings]);
} else {
    echo json_encode(['success' => false, 'error' => 'Ação não encontrada']);
}
?>
