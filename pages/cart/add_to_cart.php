<?php
session_start();
include("../../database/db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $id = intval($_POST['product_id']);
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);

    if ($row = $result->fetch_assoc()) {
        // Initialize cart if not exist
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // If item already in cart, increase qty
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] += 1;
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $row['id'],
                'barcode' => $row['barcode'],
                'name' => $row['name'],
                'category' => $row['category'],
                'price' => $row['selling_price'],
                'qty' => 1,
                'image' => $row['image']
            ];
        }
    }
}

// Redirect to buying page directly
header("Location: ../customer/sell.php");
exit();
?>
