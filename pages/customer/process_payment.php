<?php
session_start();
if (!isset($_SESSION['user_session'])) {
    header("location:../login.php");
    exit;
}

// Get payment method
$method = $_POST['method'] ?? $_GET['method'] ?? '';

// Process based on payment method
if ($method === 'COD') {
    // Handle Cash on Delivery
    header("location: payment.php?payment=success&method=COD");
    exit;
} elseif ($method === 'GPay') {
    // Handle Google Pay
    $transaction_id = $_POST['transaction_id'] ?? '';
    header("location: payment.php?payment=success&method=GPay&payment_id=" . urlencode($transaction_id));
    exit;
} elseif ($method === 'RazorPay') {
    // Handle RazorPay - payment already verified
    $payment_id = $_GET['payment_id'] ?? '';
    header("location: payment.php?payment=success&method=RazorPay&payment_id=" . urlencode($payment_id));
    exit;
} else {
    // Invalid payment method
    header("location: payment.php?error=invalid_method");
    exit;
}
?>