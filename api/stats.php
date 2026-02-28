<?php
// api/stats.php
require_once '../admin/config.php';

header('Content-Type: application/json');

// Auth check
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    // Total Revenue
    $total_revenue = $pdo->query("SELECT SUM(amount) FROM leads WHERE status = 'approved'")->fetchColumn() ?: 0;
    
    // Total Leads
    $total_leads = $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
    
    // Pending Pix
    $pending_pix = $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'pending' AND pix_code IS NOT NULL")->fetchColumn();
    
    // Conversion Rate
    $recovered = $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'approved'")->fetchColumn();
    $conversion_rate = $total_leads > 0 ? round(($recovered / $total_leads) * 100, 1) : 0;

    // Last 7 days revenue (for charts)
    $stmt = $pdo->query("SELECT DATE(updated_at) as date, SUM(amount) as value FROM leads WHERE status = 'approved' AND updated_at >= CURRENT_DATE - INTERVAL '7 days' GROUP BY DATE(updated_at) ORDER BY date ASC");
    $daily_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'total_revenue' => (float)$total_revenue,
            'total_leads' => (int)$total_leads,
            'pending_pix' => (int)$pending_pix,
            'conversion_rate' => $conversion_rate,
            'daily_stats' => $daily_stats
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
