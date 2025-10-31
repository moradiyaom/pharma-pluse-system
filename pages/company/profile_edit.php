<?php
session_start();
include("../../database/db_connect.php");

if (!isset($_SESSION['user_session'])) {
    header("location: ../login.php");
    exit;
}

$username = $_SESSION['user_session'];
$message = "";

// Fetch current user info
$query = "SELECT * FROM userdata WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Update profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];

    $update = "UPDATE userdata SET email=?, contact=?, address=? WHERE username=?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("ssss", $email, $contact, $address, $username);
    if ($stmt->execute()) {
        $message = "✅ Profile updated successfully!";
        header("Refresh:1; url=profile.php"); // redirect after 1 second
    } else {
        $message = "⚠️ Error updating profile!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Profile</title>
<link rel="stylesheet" href="../../css/profile_edit.css">
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
        <li><a href="order_list.php">🛒 Order List</a></li>
        <li><a href="sales_report.php">📊 Sales Report</a></li>
    </ul>

    <!-- Bottom Links -->
    <ul class="menu bottom-menu">
        <li><a href="low_stock.php">🔔 Notifications</a></li>
        <li><a href="../logout.php">🚪 Logout</a></li>
    </ul>
</aside>

    <main class="main-content">
        <h1>Edit Profile</h1>
        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="POST" class="profile-form">
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <label>Contact:</label>
            <input type="text" name="contact" value="<?php echo htmlspecialchars($user['contact']); ?>" required>
            <label>Address:</label>
            <textarea name="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>
            <button type="submit" class="btn-save">💾 Save Changes</button>
        </form>
    </main>
</div>
</body>
</html>
