<?php
// Include DB connection
session_start();
include("../../database/db_connect.php");

// Fetch products with quantity less than 50
$sql = "SELECT id, barcode, name, category, selling_price, quantity 
        FROM products 
        WHERE quantity > 50
        ORDER BY quantity ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Low Stock Notification</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f9f9f9; }
        h2 { text-align: center; color: #d9534f; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: center; }
        th { background: #d9534f; color: white; }
        tr:nth-child(even) { background: #fdfdfd; }
        .critical { background: #f8d7da; color: #721c24; font-weight: bold; }
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
    <a href="logout.php" class="logout-btn">Logout</a>
</nav>
</header>

<h2>⚠ Low Stock Notification </h2>

<table>
    <tr>
        <th>ID</th>
        <th>Barcode</th>
        <th>Product Name</th>
        <th>Category</th>
        <th>Selling Price (₹)</th>
        <th>Quantity</th>
    </tr>

    <?php
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $class = ($row['quantity'] < 20) ? "critical" : ""; // Highlight very low stock
            echo "<tr class='$class'>
                    <td>".$row['id']."</td>
                    <td>".$row['barcode']."</td>
                    <td>".$row['name']."</td>
                    <td>".$row['category']."</td>
                    <td>".$row['selling_price']."</td>
                    <td>".$row['quantity']."</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='6'>✅ All products have sufficient stock.</td></tr>";
    }
    ?>
</table>

</body>
</html>
