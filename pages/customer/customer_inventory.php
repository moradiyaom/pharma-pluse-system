<?php
include("../../database/db_connect.php");
session_start();

// ✅ Fetch all available medicines from products
$sql = "SELECT id, barcode, name, category, selling_price, image1, image2, created_at 
        FROM products
        ORDER BY created_at DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Inventory</title>
  <link rel="stylesheet" href="../../css/customer_inventory1.css"> <!-- External CSS -->
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
  <h2>🩺 Customer Inventory</h2>



<div class="cards">
        <div class="card">
            <h2>Medicine</h2>
            <h3>All Medicine.</h3><br>
            <a href="../medicine/medicine.php" class="btn">medicine</a>
        </div>
        <div class="card">
            <h2>Loction</h2>
            <h3>All Loction.</h3><br>
            <a href="../loction/loction.php" class="btn">loction</a>
        </div>
        <div class="card">
            <h2>Soap</h2>
            <h3>All Soap.</h3><br>
            <a href="../soap/soap.php" class="btn">Soap</a>
        </div>
        <div class="card">
            <h2>Baby Food</h2>
            <h3>All Baby Food.</h3><br>
            <a href="../baby_food/baby_food.php" class="btn">Baby Food</a>
        </div>
        <div class="card">
            <h2>Food & Drink</h2>
            <h3>All Food & Drink.</h3><br>
            <a href="../food_drink/food_drink.php" class="btn">Food & Drink</a>
        </div>
    </div>








</div>
</body>
</html>
<?php $conn->close(); ?>
