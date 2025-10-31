<?php
session_start();

// If no cart yet
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $cart = [];
} else {
    $cart = $_SESSION['cart'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Cart</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    table, th, td { border: 1px solid #ddd; }
    th, td { padding: 10px; text-align: center; }
    th { background: #f4f4f4; }
    .total { font-weight: bold; font-size: 18px; text-align: right; }
    .btn { padding: 6px 12px; border: none; cursor: pointer; border-radius: 5px; }
    .btn-remove { background: #dc3545; color: white; }
    .btn-checkout { background: #28a745; color: white; float: right; }
  </style>
</head>
<body>
  <h2>🛒 Your Shopping Cart</h2>

  <?php if (empty($cart)): ?>
    <p>Your cart is empty.</p>
  <?php else: ?>
    <form action="update_cart.php" method="post">
      <table>
        <tr>
          <th>Image</th>
          <th>Product</th>
          <th>Category</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Subtotal</th>
          <th>Action</th>
        </tr>
        <?php 
          $total = 0;
          foreach ($cart as $item): 
            $subtotal = $item['price'] * $item['qty'];
            $total += $subtotal;
        ?>
        <tr>
          <td><img src="../../uploads/<?php echo $item['image']; ?>" width="50"></td>
          <td><?php echo $item['name']; ?></td>
          <td><?php echo ucfirst($item['category']); ?></td>
          <td>₹<?php echo number_format($item['price'], 2); ?></td>
          <td>
            <input type="number" name="qty[<?php echo $item['id']; ?>]" value="<?php echo $item['qty']; ?>" min="1" style="width:50px;">
          </td>
          <td>₹<?php echo number_format($subtotal, 2); ?></td>
          <td>
            <a href="remove_item.php?id=<?php echo $item['id']; ?>" class="btn btn-remove">Remove</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <tr>
          <td colspan="5" class="total">Total:</td>
          <td colspan="2">₹<?php echo number_format($total, 2); ?></td>
        </tr>
      </table>
      <button type="submit" class="btn">Update Cart</button>
    </form>
    <a href="checkout.php" class="btn btn-checkout">Proceed to Checkout</a>
  <?php endif; ?>
</body>
</html>
