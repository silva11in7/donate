<?php
// vps_password_test.php
$pass = 'admin123';
$hash = '$2y$10$iAqqLCPZjiUKO.J6mMqNB.m/mfDLItVi23eerXThclSl128oGffJcG';

echo "Testing password: $pass\n";
echo "Against hash: $hash\n";

if (password_verify($pass, $hash)) {
    echo "SUCCESS: password_verify works!\n";
} else {
    echo "FAILED: password_verify FAILED!\n";
}

// Generate new one to see what it looks like here
$new_hash = password_hash($pass, PASSWORD_BCRYPT);
echo "New hash generated on this VPS: $new_hash\n";
if (password_verify($pass, $new_hash)) {
    echo "SUCCESS: newly generated hash verify works!\n";
}
?>
