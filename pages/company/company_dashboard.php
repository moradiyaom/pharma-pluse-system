<?php
session_start();
include("../../database/db_connect.php");

// ✅ Redirect if not logged in
if (!isset($_SESSION['user_session'])) {
    header("location: ../login.php");
    exit;
}

// Example company info
$companyName = "Pharma Plus Pvt. Ltd.";
$username = $_SESSION['user_session'];

// ✅ Fetch total medicines, sold, stock, revenue
$query = "
    SELECT 
        COUNT(*) AS total_medicines,
        SUM(sold_qty) AS total_sold,
        SUM(remain_qty) AS total_stock,
        SUM(sold_qty * selling_price) AS total_revenue
    FROM inventory
";
$result = $conn->query($query);
$stats = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Company Dashboard</title>
    <link rel="stylesheet" href="../../css/company_dashboard1.css">
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

        
    <div class="dashboard-container">
        <h1>Welcome, <?php echo htmlspecialchars($companyName); ?> 👋</h1>
        <h2>User: <?php echo htmlspecialchars($username); ?>  Company</h2>

        <div class="stats-grid">
            <div class="card total">
                <h3>Total Medicines</h3>
                <p><?php echo $stats['total_medicines'] ?? 0; ?></p>
            </div>
            <div class="card sold">
                <h3>Total Sold</h3>
                <p><?php echo $stats['total_sold'] ?? 0; ?></p>
            </div>
            <div class="card stock">
                <h3>Stock Remaining</h3>
                <p><?php echo $stats['total_stock'] ?? 0; ?></p>
            </div>
            <div class="card revenue">
                <h3>Total Revenue</h3>
                <p>₹<?php echo $stats['total_revenue'] ?? 0; ?></p>
            </div>
        </div>
    </div>
</body>
</html>
