<?php
require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;

$input = json_decode(file_get_contents('php://input'), true);
$amount = $input['amount'] ?? 0;

$api = new Api('rzp_test_RLJMjJZTQtLlPt', 'YOUR_SECRET_KEY');
$order  = $api->order->create([
    'receipt' => 'ORD' . time(),
    'amount' => $amount,
    'currency' => 'INR'
]);

echo json_encode($order);
?>
