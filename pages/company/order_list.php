<?php
session_start();
include(__DIR__ . "/../../database/db_connect.php");

// ✅ Check if ANY user is logged in
if (!isset($_SESSION['user_session']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit();
}

$user_id   = $_SESSION['user_session'];
$username  = $_SESSION['username'];
$role      = $_SESSION['role'];

$company_id = $_SESSION['user_session'];

// ✅ Fetch orders for this company
$sql = "SELECT o.id, o.customer_id, u.username AS customer_name, o.total_amount, o.created_at 
        FROM orders o
        JOIN userdata u ON o.customer_id = u.id
        WHERE o.company_id = ?
        ORDER BY o.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>📦 Company Orders</title>
  <link rel="stylesheet" href="../../css/order_list1.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
<div class="container">
    <h1>📦 Company Order List</h1>

    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="🔍 Search by Customer or Order ID...">
    </div>

    <table id="orderTable">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total Amount</th>
                <th>Date</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td>#<?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['customer_name']); ?></td>
                <td>₹<?= number_format($row['total_amount'], 2); ?></td>
                <td><?= date("d M Y, h:i A", strtotime($row['created_at'])); ?></td>
                <td><a href="order_details.php?id=<?= $row['id']; ?>" class="btn">View</a></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
// 🔍 Simple Search Filter
document.getElementById("searchInput").addEventListener("keyup", function() {
  var value = this.value.toLowerCase();
  var rows = document.querySelectorAll("#orderTable tbody tr");
  rows.forEach(row => {
    row.style.display = row.innerText.toLowerCase().includes(value) ? "" : "none";
  });
});
</script>
</body>
</html>
