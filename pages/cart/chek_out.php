<?php
session_start();
include("../../database/db_connect.php");

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: selling.php");
    exit();
}

$cart = $_SESSION['cart'];

foreach ($cart as $item) {
    $barcode = $item['barcode'];
    $medicine = $item['name'];
    $qty = $item['qty'];
    $price = $item['price'];
    $total = $qty * $price;

    // Update inventory
    $conn->query("UPDATE inventory 
                  SET sold_qty = sold_qty + $qty, 
                      remain_qty = remain_qty - $qty 
                  WHERE code='$barcode'");

    // Insert into sales
    $stmt = $conn->prepare("INSERT INTO sales (barcode, medicine, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssidd", $barcode, $medicine, $qty, $price, $total);
    $stmt->execute();
}

// ✅ Clear cart after checkout
unset($_SESSION['cart']);

header("Location: selling.php?success=1");
exit();
?>
