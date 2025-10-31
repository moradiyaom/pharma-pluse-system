<?php
session_start();
include "../db_connect.php";

// Insert Cancelled payment record
$stmt = $conn->prepare("INSERT INTO payments (payment_type, amount, status) VALUES (?, ?, ?)");
$type = "PayPal"; // or Razorpay if you detect from query
$amount = 500; 
$status = "Cancelled";
$stmt->bind_param("sds", $type, $amount, $status);
$stmt->execute();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html>
<head><title>Payment Cancelled</title></head>
<body>
  <h2>❌ Payment Cancelled</h2>
  <p>Your transaction was cancelled. Please try again.</p>
</body>
</html>
