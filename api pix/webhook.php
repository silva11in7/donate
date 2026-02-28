<?php
// api pix/webhook.php
require_once '../admin/config.php';
require_once '../include/utmfy_helper.php';

header('Content-Type: application/json');

$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

if (!$data) {
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

$id = $data['id'] ?? null;
$external_id = $data['external_id'] ?? null;
$status = $data['status'] ?? '';

if (!$external_id) {
    echo json_encode(['error' => 'No external_id provided']);
    exit;
}

// Convert Genesys status to our status
$our_status = 'pending';
if ($status === 'AUTHORIZED') {
    $our_status = 'approved';
}

// Update lead status in the database
// We use external_id which contains the lead ID (e.g., lead_65df...)
$lead_db_id = str_replace('lead_', '', $external_id);

$stmt = $pdo->prepare("UPDATE leads SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? OR (pix_code LIKE ? AND status != 'approved')");
// Fallback to searching by external_id suffix if it's strictly the ID, or search by a wildcard of the PIX code if provided
$stmt->execute([$our_status, $lead_db_id, "%$id%"]);

if ($our_status === 'approved') {
    // Fetch lead data for UTMfy
    $lead = $pdo->query("SELECT * FROM leads WHERE id = '$lead_db_id' OR pix_code LIKE '%$id%'")->fetch();
    if ($lead) {
        send_utmfy_order($lead['id'], 'paid', [
            'name' => $lead['name'],
            'email' => $lead['email'],
            'phone' => $lead['phone'],
            'ip' => '' // IP not usually available in webhook payload
        ], [
            'id' => 'doacao_solidaria',
            'name' => 'Doação Solidária',
            'price' => (float)$lead['amount']
        ]);
    }
}

echo json_encode(['success' => true]);
?>
