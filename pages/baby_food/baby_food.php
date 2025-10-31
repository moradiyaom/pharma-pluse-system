<?php
include("../../database/db_connect.php");

$sql = "SELECT * FROM products WHERE category = 'baby food' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>baby Food</title>
  <link rel="stylesheet" href="../../css/all_pr.css">
</head>
<body>


<!-- ✅ Navigation Bar -->
<header>
<nav class="navbar">
        <a href="#" class="logo">
            <img src="../../images/i1.jpeg" alt="Medical Store Logo">
        </a>
    
    <div class="nav-right">
        <a href="../customer/customer_dashboard.php">Dashboard</a>
        <a href="../medicine/medicine.php">medicines</a>
        <a href="../soap/soap.php">soap</a>
        <a href="../loction/loction.php">loction</a>
        <a href="../baby_food/baby_food.php">Baby Food</a>
        <a href="../food_drink/food_drink.php">Food & Drink</a>
    </div>
    <a href="../logout.php" class="logout-btn">Logout</a>
    </nav>
</header>


<div class="container">
  <h2>Baby Food</h2>
  <div class="products">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <div class="product-card">
          <!-- Product Image -->
          <div class="product-img">
            <?php if (!empty($row['image1'])): ?>
              <img src="../../uploads/<?php echo $row['image1']; ?>" alt="<?php echo $row['name']; ?>">
            <?php else: ?>
              <img src="../../uploads/default.png" alt="No Image">
            <?php endif; ?>
          </div>

          <!-- Product Info -->
          <div class="product-info">
            <h3><?php echo $row['name']; ?></h3>
            <p class="barcode">Barcode: <?php echo $row['barcode']; ?></p>
            <p class="category"><?php echo $row['category'] ?? "Uncategorized"; ?></p>
            <p class="price">₹<?php echo number_format($row['selling_price'], 2); ?></p>
          </div>


          <!-- ✅ Buttons -->
          <div class="btn-group">
            <!-- Add to Cart (send product id to add_to_cart.php) -->
            <form action="../cart/add_to_cart.php" method="POST">
              <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
              <button type="submit" class="btn btn-cart">Add to Cart</button>
            </form>

            <!-- Details (redirect to details.php?id=...) -->
            <a href="all_baby_food.php?id=<?php echo $row['id']; ?>" class="btn btn-details">Details</a>
          </div>
          
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No medicines found.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
<?php $conn->close(); ?>
