<?php
session_start();
include '../../database/db_connect.php'; // Adjust path as needed

// ✅ Check login
if (!isset($_SESSION['user_session'])) {
    header("location:../login.php");
    exit;
}

$username = $_SESSION['username'];
$role     = $_SESSION['role'];

// ✅ Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $qty        = intval($_POST['qty'] ?? 1);

    $sql = "SELECT id, barcode, name, category, selling_price, image1 
            FROM products 
            WHERE id = $product_id LIMIT 1";
    $result = $conn->query($sql);
    $product = $result->fetch_assoc();

    if ($product) {
        if (!isset($_SESSION['cart'])) {$_SESSION['cart'] = [];}

        $barcode = $product['barcode'];

        if (isset($_SESSION['cart'][$barcode])) {
            $_SESSION['cart'][$barcode]['qty'] += $qty;
        } else {
            $_SESSION['cart'][$barcode] = [
                'name'     => $product['name'],
                'category' => $product['category'],
                'price'    => $product['selling_price'],
                'image1'   => $product['image1'],
                'qty'      => $qty
            ];
        }
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ✅ Handle Remove Single Item
if (isset($_GET['remove'])) {
    $barcode = $_GET['remove'];
    if (isset($_SESSION['cart'][$barcode])) unset($_SESSION['cart'][$barcode]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ✅ Handle Clear All
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}



// ✅ Load cart
$cart = $_SESSION['cart'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cart & Checkout</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    table, th, td { border: 1px solid #ddd; }
    th, td { padding: 10px; text-align: center; }
    th { background: #f4f4f4; }
    .total { font-weight: bold; font-size: 18px; text-align: right; }
    .btn { padding: 6px 12px; border: none; cursor: pointer; border-radius: 5px; text-decoration:none; }
    .btn-checkout { background: #28a745; color: white; }
    .btn-remove { background: #dc3545; color: white; }
    .btn-clear { background: #6c757d; color: white; }
    img { border-radius: 5px; margin: 2px; }
    /* ✅ Header (Fixed at Top) */
header {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 1000;
  }
  
  /* ✅ Navbar */
  .navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: linear-gradient(135deg, #6d4296, #40714f);
  padding: 15px 30px;
  color: #fff;
  font-size: 18px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.15);
  }
  
  /* ✅ Logo */
  .navbar .logo img {
  width: 60px;          /* medium size */
  height: 60px;
  object-fit: cover;    /* keeps aspect ratio */
  border-radius: 8px;   /* small rounded corners */
  display: block;
  }
  
  /* ✅ Navigation Links */
  .navbar .nav-right {
  flex: 1;
  text-align: center;
  }
  
  .navbar .nav-right a {
  color: #fff;
  text-decoration: none;
  margin: 0 12px;
  font-size: 18px;
  padding: 8px 14px;
  border-radius: 6px;
  transition: background 0.3s, color 0.3s;
  }
  
  .navbar .nav-right a:hover {
  background: rgba(255, 255, 255, 0.2);
  color: #ffd700;
  }
  
  /* ✅ Logout Button */
  .navbar .logout-btn {
  background: #6ec02b;
  font-weight: bold;
  color: #0000ff;
  padding: 8px 14px;
  border-radius: 6px;
  text-decoration: none;
  transition: background 0.3s;
  }
  
  .navbar .logout-btn:hover {
  background: #3ce753;
  }
  
  /* ✅ Prevent Content from Hiding under Navbar */
  body {
  margin: 0;
  padding-top: 80px;  /* equal to navbar height */
  }
  
  </style>
</head>
<body>
<!-- ✅ Navigation Bar -->
<header>
<nav class="navbar">
        <a href="#" class="logo">
            <img src="../../images/i1.jpeg" alt="Medical Store Logo">
        </a>
    
    <div class="nav-right">
        <a href="customer_dashboard.php">Dashboard</a>
        <a href="customer_inventory.php">Inventory</a>
    </div>
    <div style="margin-right: 20px;">
        Logged in as: <b><?php echo ucfirst($username); ?></b> (<?php echo ucfirst($role); ?>)
    </div>
    <a href="../../pages/logout.php" class="logout-btn">Logout</a>
</nav>
</header>


<div class="container">
  <h2>🛒 Your Cart</h2>

  <?php if (empty($cart)): ?>
    <p>Your cart is empty. <a href="../medicine/medicine.php">Go add products</a></p>
  <?php else: ?>
      <table>
        <tr>
          <th>Image</th>
          <th>Barcode</th> 
          <th>Product</th>
          <th>Category</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Subtotal</th>
          <th>Action</th>
        </tr>
        <?php 
          $grand = 0;
          foreach ($cart as $barcode => $item):
            $qty = $item['qty'];
            $price = $item['price'];
            $subtotal = $qty * $price;
            $grand += $subtotal;
        ?>
        <tr>
          <td>
            <?php if (!empty($item['image1'])): ?>
              <img src="../../uploads/<?= htmlspecialchars($item['image1']) ?>" width="60" height="60">
            <?php else: ?>
              <span>No Image</span>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($barcode) ?></td>
          <td><?= htmlspecialchars($item['name']) ?></td>
          <td><?= ucfirst(htmlspecialchars($item['category'])) ?></td>
          <td>₹<?= number_format($price, 2) ?></td>
          <td><?= $qty ?></td>
          <td>₹<?= number_format($subtotal, 2) ?></td>
          <td><a href="?remove=<?= $barcode ?>" class="btn btn-remove">❌ Remove</a></td>
        </tr>
        <?php endforeach; ?>
        <tr>
          <td colspan="6" class="total">Grand Total:</td>
          <td>₹<?= number_format($grand, 2) ?></td>
          <td></td>
        </tr>
      </table>

      <!-- Buttons -->
      <a href="payment.php" class="btn btn-checkout">💳 Proceed to Payment</a>
      <a href="?clear=1" class="btn btn-clear" onclick="return confirm('Clear all items from cart?')">🗑 Clear All</a>
  <?php endif; ?>
</div>
</body>
</html>
<?php $conn->close(); ?>
