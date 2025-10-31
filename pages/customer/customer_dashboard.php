<?php
session_start();
include("../../database/db_connect.php");

// ✅ Redirect if not logged in
if (!isset($_SESSION['user_session'])) {
    header("location: ../login.php");
    exit;
}

// Username for display
$username = $_SESSION['user_session'];
$role     = isset($_SESSION['role']) ? $_SESSION['role'] : "User";

// ✅ Get user_id from session
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// ✅ Fetch sales statistics for this customer/company
$stats = [
    'total_orders' => 0,
    'total_spent'  => 0
];

$sql = "
    SELECT 
        IFNULL(SUM(quantity), 0) AS total_orders,
        IFNULL(SUM(total), 0) AS total_spent
    FROM sales
    WHERE barcode IS NOT NULL
";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $stats = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="../../css/customer_dashboard1.css">
</head>
<body>
    <aside class="sidebar">
        <div class="brand">
            <h2><a href="../Dashboard.php">💊 MedStore</a></h2>
        </div>

        <ul class="menu top-menu">
            <li><a href="customer_dashboard.php">📊 Dashboard</a></li>
            <li><a href="profile.php">👤 Profile</a></li>
            <li><a href="selling.php">💊 buy </a></li>
            <li><a href="customer_inventory.php">🛒 inventory</a></li>
            <a href="../customer/buying.php">📊 Sales Report</a>
            
            <li><a href="buying.php">🛒 Purchase Records</a></li>
            <li><a href="cart.php">🛒 payment</a></li>
        </ul>

        <ul class="menu bottom-menu">
            <li><a href="../support.php">💬 Support</a></li>
            <li><a href="../logout.php">🚪 Logout</a></li>
        </ul>
    </aside>

    <div class="dashboard-container">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?> 👋</h1>
        <h2>Customer Panel</h2>

        <div class="stats-grid">
            <div class="card orders">
                <h3>Total Buy</h3>
                <p><?php echo $stats['total_orders'] ?? 0; ?></p>
            </div>
            <div class="card spent">
                <h3>Total Cost</h3>
                <p>₹<?php echo $stats['total_spent'] ?? 0; ?></p>
            </div>
        </div>

        <div class="quick-links">
            <div class="qcard">
                <h3>🛒 Cart</h3>
                <p>Check medicines in your cart.</p>
                <a href="../cart/view_cart.php">Go →</a>
            </div>
            
            <div class="qcard">
                <h3>💊 Medicines</h3>
                <p>Browse and add medicines.</p>
                <a href="../medicine/medicine.php">Go →</a>
            </div>
            <div class="qcard">
                <h3>💬 Support</h3>
                <p>Need help? Contact us.</p>
                <a href="../support.php">Go →</a>
            </div>
        </div>
    </div>
</body>
</html>
