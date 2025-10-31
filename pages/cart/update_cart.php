<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['qty'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] = max(1, intval($qty));
        }
    }
}

header("Location: view_cart.php");
exit();
?>
