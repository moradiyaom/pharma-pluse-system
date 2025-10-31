<?php
include("../../database/db_connect.php");
session_start();

// ✅ Fetch all sales data
$sql = "SELECT id, barcode, medicine, quantity, price, total, sold_date FROM sales ORDER BY sold_date DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Buying Page</title>
  <link rel="stylesheet" href="../../css/buying1.css">
</head>
<body>


    <aside class="sidebar">
        <div class="brand">
            <h2><a href="../Dashboard.php">💊 MedStore</a></h2>
        </div>

        <ul class="menu top-menu">
            <li><a href="customer_dashboard.php">📊 Dashboard</a></li>
            <li><a href="../dashboard.php">🏠 Main Dashboard</a></li>
            <li><a href="customer_profile.php">👤 Profile</a></li>
            <li><a href="selling.php">💊 buy </a></li>
            <li><a href="customer_inventory.php">🛒 inventory</a></li>
            <li><a href="buying.php">🛒 Purchase Records</a></li>
        </ul>

        <ul class="menu bottom-menu">
            <li><a href="../../pages/support.php">💬 Support</a></li>
            <li><a href="../logout.php">🚪 Logout</a></li>
        </ul>
    </aside>

    
<div class="container">
  <h2>🛒 Purchase Records</h2>
  
  <?php if ($result->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Barcode</th>
          <th>Medicine</th>
          <th>Quantity</th>
          <th>Price (₹)</th>
          <th>Total (₹)</th>
          <th>Sold Date</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['barcode']; ?></td>
            <td><?php echo $row['medicine']; ?></td>
            <td><?php echo $row['quantity']; ?></td>
            <td><?php echo number_format($row['price'], 2); ?></td>
            <td><?php echo number_format($row['total'], 2); ?></td>
            <td><?php echo $row['sold_date']; ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No records found.</p>
  <?php endif; ?>

</div>
</body>
</html>
<?php $conn->close(); ?>
