<?php
session_start();
include "../database/db_connect.php";

// Insert COD order
$stmt = $conn->prepare("INSERT INTO payments (payment_type, amount, status) VALUES (?, ?, ?)");
$type = "COD";
$amount = 500; // Example, ideally fetch from cart
$status = "Pending";
$stmt->bind_param("sds", $type, $amount, $status);
$stmt->execute();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html>
<head><title>COD Order Placed</title></head>
<body>
  <h2>📦 Cash on Delivery Selected</h2>
  <p>Your order has been placed successfully. Please pay when your order arrives.</p>
</body>
</html>
