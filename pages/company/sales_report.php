<?php
// Correct relative path for db connection
session_start();
include("../../database/db_connect.php");

// Fetch sales data
$sql = "SELECT id, barcode, medicine, quantity, price, total, payment_method, sold_date 
        FROM sales 
        ORDER BY sold_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f6f9; }
        h2 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: center; }
        th { background: #007bff; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .total { font-weight: bold; background: #eee; }
        /* Sidebar */
.sidebar {
    width: 240px;
    background: #2c3e50;
    color: #fff;
    padding: 10px 0;
    display: flex;
    flex-direction: column;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
}




.sidebar .brand {
    text-align: center;
    margin-bottom: 20px;
}

.sidebar .brand h2 a {
    font-size: 20px;
    color: #ecf0f1;
    text-decoration: none;
}

.sidebar .menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar .menu li {
    margin: 10px 0;
}

.sidebar .menu a {
    text-decoration: none;
    color: #ecf0f1;
    padding: 12px 20px;
    display: block;
    border-radius: 6px;
    transition: background 0.3s;
}

.sidebar .menu a:hover,
.sidebar .menu a.active {
    background: #34495e;
}

    </style>
</head>
<body>


        <aside class="sidebar">
        <div class="brand">
            
    
        <h2><a href="../Dashboard.php">🏢 Pharma Plus</a></h2>
    </div>

    <!-- Top Links -->
    <ul class="menu top-menu">
        <li><a href="company_dashboard.php" class="active">📊 Dashboard</a></li>
        <li><a href="profile.php">👤 Profile</a></li>
        <li><a href="product.php">💊 Add Medicine</a></li>
        <li><a href="inventory.php">📦 Inventory</a></li>
        <li><a href="order_list.php">🛒 Order List</a></li>
        <li><a href="sales_report.php">📊 Sales Report</a></li>
    </ul>
    <br><br><br>
    <!-- Bottom Links -->
    <ul class="menu bottom-menu">
        <li><a href="low_stock.php">🔔 Notifications</a></li>
        <li><a href="../logout.php">🚪 Logout</a></li>
    </ul>
</aside>

<h2>Sales Report</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Barcode</th>
        <th>Medicine</th>
        <th>Quantity</th>
        <th>Price (₹)</th>
        <th>Total (₹)</th>
        <th>Payment Method</th>
        <th>Sold Date</th>
    </tr>

    <?php
    $grand_total = 0;
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $grand_total += $row['total'];
            echo "<tr>
                    <td>".$row['id']."</td>
                    <td>".$row['barcode']."</td>
                    <td>".$row['medicine']."</td>
                    <td>".$row['quantity']."</td>
                    <td>".$row['price']."</td>
                    <td>".$row['total']."</td>
                    <td>".$row['payment_method']."</td>
                    <td>".$row['sold_date']."</td>
                  </tr>";
        }
        echo "<tr class='total'>
                <td colspan='6'>Grand Total</td>
                <td colspan='3'>₹ ".$grand_total."</td>
              </tr>";
    } else {
        echo "<tr><td colspan='9'>No sales records found.</td></tr>";
    }
    ?>
</table>

</body>
</html>
