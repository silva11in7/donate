<?php
// force_reset_vps.php
require_once 'admin/config.php';

$new_pass = 'admin123';
$new_hash = password_hash($new_pass, PASSWORD_BCRYPT);

echo "Generating new hash for '$new_pass': $new_hash\n";

try {
    // Update admin
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE LOWER(username) = 'admin'");
    $stmt->execute([$new_hash]);
    echo "Updated 'admin' (Rows: " . $stmt->rowCount() . ")\n";

    // Update escalaforte
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE LOWER(username) = 'escalaforte'");
    $stmt->execute([$new_hash]);
    echo "Updated 'escalaforte' (Rows: " . $stmt->rowCount() . ")\n";

    // Verify
    $stmt = $pdo->query("SELECT username, password_hash FROM users");
    while ($row = $stmt->fetch()) {
        $verify = password_verify($new_pass, $row['password_hash']) ? "Valid" : "INVALID";
        echo "Check: {$row['username']} -> $verify\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
