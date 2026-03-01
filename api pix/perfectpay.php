<?php
// api pix/perfectpay.php
// Perfect Pay integration - Standardized for Vakinha Premium

if (!function_exists('execute_perfectpay_payment')) {
    function execute_perfectpay_payment($identifier, $amount, $name, $email, $phone, $document, $config) {
        // Many 'Vakinha' systems use Amplo/Oasyfy as the backend for Perfect Pay or vice-versa
        // If Perfect Pay uses the same standard:
        include_once __DIR__ . '/amplo.php';
        return execute_amplo_payment($identifier, $amount, $name, $email, $phone, $document, $config);
    }
}
?>
