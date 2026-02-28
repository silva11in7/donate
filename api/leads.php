<?php
// api/leads.php
require_once '../admin/config.php';

header('Content-Type: application/json');

// Auth check
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? ($input['action'] ?? 'list');
$input = json_decode(file_get_contents('php://input'), true);

if ($action === 'list') {
    $search = $_GET['q'] ?? '';
    $status = $_GET['status'] ?? '';
    
    $query = "SELECT * FROM leads WHERE 1=1";
    $params = [];
    
    if ($search) {
        $query .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if ($status) {
        $query .= " AND status = ?";
        $params[] = $status;
    }
    
    $query .= " ORDER BY created_at DESC LIMIT 100";
    
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} elseif ($action === 'details') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) {
        echo json_encode(['success' => false, 'error' => 'ID inválido']);
        exit;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM leads WHERE id = ?");
    $stmt->execute([$id]);
    $lead = $stmt->fetch();
    
    if ($lead) {
        echo json_encode(['success' => true, 'data' => $lead]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Lead não encontrado']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Ação inválida']);
}
?>
