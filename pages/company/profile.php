<?php
session_start();
include("../../database/db_connect.php");

// Redirect if not logged in
if (!isset($_SESSION['user_session'])) {
    header("location: ../login.php");
    exit;
}

// Get user info
$username = $_SESSION['user_session'];

// Fetch user info from userdata table
$query = "SELECT * FROM userdata WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Company Profile</title>
    <link rel="stylesheet" href="../../css/profile.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="dashboard-wrapper">
    <!-- Sidebar -->
         <aside class="sidebar">
    <div class="brand">
        <h2>🏢 Pharma Plus</h2>
    </div>

    <!-- Top Links -->
    <ul class="menu top-menu">
        <li><a href="company_dashboard.php" class="active">📊 Dashboard</a></li>
        <li><a href="profile.php">👤 Profile</a></li>
        <li><a href="product.php">💊 Add Medicine</a></li>
        <li><a href="inventory.php">📦 Inventory</a></li>
        <li><a href="sales_billing.php">💰 Sales Billing</a></li>
        <li><a href="order_list.php">🛒 Order List</a></li>
        <li><a href="report_sales.php">📊 Sales Report</a></li>
    </ul>
    <br><br><br>
    <!-- Bottom Links -->
    <ul class="menu bottom-menu">
        <li><a href="low_stock.php">🔔 Notifications</a></li>
        <li><a href="../logout.php">🚪 Logout</a></li>
    </ul>
</aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="profile-header">
            <div class="avatar">
                <img src="../../images/i1.jpeg" alt="Company Avatar">
            </div>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user['username'] ?? 'company user'); ?></h1>
                <p>Company Profile Overview</p>
                <a href="profile_edit.php" class="btn-edit">✏️ Edit Profile</a>
            </div>
        </div>

        <div class="profile-cards">
            <div class="card">
                <h3>Email</h3>
                <p><?php echo htmlspecialchars($user['email'] ?? 'Not Provided'); ?></p>
            </div>
            <div class="card">
                <h3>Contact</h3>
                <p><?php echo htmlspecialchars($user['contact'] ?? 'Not Provided'); ?></p>
            </div>
            <div class="card">
                <h3>Address</h3>
                <p><?php echo htmlspecialchars($user['address'] ?? 'Not Provided'); ?></p>
            </div>
        </div>

        <?php if (!$user): ?>
            <p style="color:red; margin-top:20px;">⚠️ User profile not found in the database.</p>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
