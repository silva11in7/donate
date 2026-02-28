<?php
// vps_db_check.php
require_once 'admin/config.php';

echo "Database Driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n";

try {
    $stmt = $pdo->query("SELECT id, username, password_hash, full_name FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($users) . " users:\n";
    foreach ($users as $u) {
        echo "- ID: {$u['id']} | User: {$u['username']} | Hash: {$u['password_hash']}\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
