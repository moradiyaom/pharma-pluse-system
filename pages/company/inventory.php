<?php
session_start();
include("../../database/db_connect.php");

// ✅ Check login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// ✅ Fetch inventory with product image
$sql = "SELECT i.*, p.image1 
        FROM inventory i 
        JOIN products p ON i.code = p.barcode 
        ORDER BY i.id DESC";
$result = $conn->query($sql);

// ✅ Count total medicines
$total_medicines = $result->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Inventory</title>
  <link rel="stylesheet" href="../../css/inv.css">
</head>
<body>

<header>
<nav class="navbar">
    <a href="#" class="logo">
        <img src="../../images/i1.jpeg" alt="Medical Store Logo">
    </a>
    
    <div class="nav-right">
          <a href="company_dashboard.php" class="active">📊 Dashboard</a>
          <a href="profile.php">👤 Profile</a>
          <a href="product.php">💊 Add Medicine</a>
          <a href="inventory.php">📦 Inventory</a>
          <a href="order_list.php">🛒 Order List</a>
          <a href="sales_report.php">📊 Sales Report</a>
    </div>

    <div style="margin-right: 20px;">
        Logged in as: <b><?php echo ucfirst($_SESSION['username']); ?></b>
    </div>
    <a href="low_stock.php">🔔</a>
    <a href="logout.php" class="logout-btn">Logout</a>
</nav>
</header>

<h2>Inventory</h2>

<p style="color:#842093; font-weight:bold; text-align:center; font-size:20px;">
    Total Medicines: [<?= $total_medicines; ?>]
</p>

<!-- ✅ Inventory Table -->
<table>
  <thead>
    <tr>
      <th>Image</th>
      <th>Code</th>
      <th>Medicine</th>
      <th>Category</th>
      <th>Registered Qty</th>
      <th>Sold Qty</th>
      <th>Remain Qty</th>
      <th>Registered Date</th>
      <th>Expiry</th>
      <th>Remark</th>
      <th>Actual Price</th>
      <th>Selling Price</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
  <?php while($row = $result->fetch_assoc()): ?>
    <tr>
      <td><img src="../../uploads/<?= $row['image1'] ?>" width="60" height="60" style="border-radius:5px;"></td>
      <td><?= $row['code'] ?></td>
      <td><?= $row['medicine'] ?></td>
      <td><?= $row['category'] ?></td>
      <td><?= $row['registered_qty'] ?></td>
      <td><?= $row['sold_qty'] ?></td>
      <td><?= $row['remain_qty'] ?></td>
      <td><?= $row['registered_date'] ?></td>
      <td><?= $row['expiry_date'] ?></td>
      <td><?= $row['remark'] ?></td>
      <td>₹<?= $row['actual_price'] ?></td>
      <td><b>₹<?= $row['selling_price'] ?></b></td>
      <td><?= $row['status'] ?></td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>
</body>
</html>
