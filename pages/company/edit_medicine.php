<?php
session_start();
include("../../database/db_connect.php");

// ✅ Get Medicine Data by ID
if (!isset($_GET['id'])) {
    header("location: inventory.php");
    exit;
}
$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM inventory WHERE id=$id");
$row = $result->fetch_assoc();

if (!$row) {
    echo "❌ Medicine not found!";
    exit;
}

// ✅ Update Medicine
if (isset($_POST['update_item'])) {
    $stmt = $conn->prepare("UPDATE inventory SET 
        code=?, medicine=?, category=?, registered_qty=?, registered_date=?, expiry_date=?, remark=?, actual_price=?, selling_price=?, status=? 
        WHERE id=?");
    $stmt->bind_param("sssisssddsi",
        $_POST['code'], $_POST['medicine'], $_POST['category'], $_POST['registered_qty'],
        $_POST['registered_date'], $_POST['expiry_date'], $_POST['remark'],
        $_POST['actual_price'], $_POST['selling_price'], $_POST['status'], $id
    );
    $stmt->execute();

    header("location: inventory.php?updated=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Medicine</title>
  <link rel="stylesheet" href="../css/edit_medicine.css">
</head>
<body>

<header>
    <nav class="navbar">
        <div class="nav-right">
            <a href="company_dashboard.php" class="active">📊 Dashboard</a> 
            <a href="profile.php">👤 Profile</a>    
            <a href="product.php">💊 Add Medicine</a>   
            <a href="inventory.php">📦 Inventory</a>    
            <a href="order_list.php">🛒 Order List</a>  
            <a href="sales_report.php">📊 Sales Report</a>  
        </div>
        <a href="low_stock.php">🔔 Notifications</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </nav>
</header>

<div class="form-container">
    <h2>✏️ Edit Medicine</h2>
    <form method="POST">
        <label>Code:</label>
        <input type="text" name="code" value="<?= $row['code'] ?>" required>

        <label>Medicine Name:</label>
        <input type="text" name="medicine" value="<?= $row['medicine'] ?>" required>

        <label>Category:</label>
        <input type="text" name="category" value="<?= $row['category'] ?>" required>

        <label>Registered Qty:</label>
        <input type="number" name="registered_qty" value="<?= $row['registered_qty'] ?>" required>

        <label>Registered Date:</label>
        <input type="date" name="registered_date" value="<?= $row['registered_date'] ?>" required>

        <label>Expiry Date:</label>
        <input type="date" name="expiry_date" value="<?= $row['expiry_date'] ?>" required>

        <label>Remark:</label>
        <input type="text" name="remark" value="<?= $row['remark'] ?>">

        <label>Actual Price:</label>
        <input type="number" step="0.01" name="actual_price" value="<?= $row['actual_price'] ?>" required>

        <label>Selling Price:</label>
        <input type="number" step="0.01" name="selling_price" value="<?= $row['selling_price'] ?>" required>

        <label>Status:</label>
        <select name="status">
            <option value="Available" <?= $row['status']=="Available" ? "selected":"" ?>>Available</option>
            <option value="Out of Stock" <?= $row['status']=="Out of Stock" ? "selected":"" ?>>Out of Stock</option>
        </select>

        <button type="submit" name="update_item">💾 Update</button>
        <a href="inventory.php" class="cancel-btn">❌ Cancel</a>
    </form>
</div>

</body>
</html>
