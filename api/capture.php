<?php
// api/capture.php
require_once '../admin/config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$action = $input['action'] ?? '';
$email = $input['email'] ?? null;
$name = $input['name'] ?? null;
$phone = $input['phone'] ?? null;
$amount = $input['amount'] ?? 0;
$step = $input['step'] ?? 'start';
$pix_code = $input['pix_code'] ?? null;
$status = $input['status'] ?? 'pending';

// Get active gateway
$gateway = $pdo->query("SELECT name FROM gateways WHERE active = 1")->fetchColumn() ?: 'Perfect Pay';

if ($action === 'capture') {
    // Check if lead exists by email or phone
    $stmt = $pdo->prepare("SELECT id FROM leads WHERE (email = ? AND email IS NOT NULL) OR (phone = ? AND phone IS NOT NULL) LIMIT 1");
    $stmt->execute([$email, $phone]);
    $lead_id = $stmt->fetchColumn();

    if ($lead_id) {
        // Update existing lead
        $sql = "UPDATE leads SET 
                name = COALESCE(?, name), 
                email = COALESCE(?, email), 
                phone = COALESCE(?, phone), 
                step = ?, 
                amount = CASE WHEN ? > 0 THEN ? ELSE amount END,
                pix_code = COALESCE(?, pix_code),
                status = ?,
                gateway = ?,
                updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        $pdo->prepare($sql)->execute([$name, $email, $phone, $step, $amount, $amount, $pix_code, $status, $gateway, $lead_id]);
    } else {
        // Insert new lead
        $sql = "INSERT INTO leads (name, email, phone, step, amount, pix_code, status, gateway) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$name, $email, $phone, $step, $amount, $pix_code, $status, $gateway]);
    }

    echo json_encode(['success' => true, 'gateway' => $gateway]);
} else {
    echo json_encode(['error' => 'Action not found']);
}
?>
