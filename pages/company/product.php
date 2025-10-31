<?php 
session_start();
include(__DIR__ . "/../../database/db_connect.php");

// ✅ Redirect if not logged in
if (!isset($_SESSION['user_session'])) {
    header("location:../login.php");
    exit;
}

// ✅ Add Product with Image + Inventory Fields
if (isset($_POST['add_product'])) {
    $barcode = trim($_POST['barcode']);
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $registered_qty = intval($_POST['registered_qty']);
    $expiry_date = $_POST['expiry_date'];
    $remark = trim($_POST['remark']);
    $actual_price = floatval($_POST['actual_price']);
    $selling_price = floatval($_POST['selling_price']);

    // ✅ Check if barcode already exists
    $check = $conn->prepare("SELECT id FROM products WHERE barcode=?");
    $check->bind_param("s", $barcode);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('❌ Barcode already exists. Please use a unique barcode!'); window.location.href='product.php';</script>";
        exit;
    }
    $check->close();

    // Handle Image Upload
    $targetDir = __DIR__ . "/../../uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName1 = time() . "_1_" . basename($_FILES["image1"]["name"]);
    $fileName2 = time() . "_2_" . basename($_FILES["image2"]["name"]);
    $targetFile1 = $targetDir . $fileName1;
    $targetFile2 = $targetDir . $fileName2;

    if (move_uploaded_file($_FILES["image1"]["tmp_name"], $targetFile1) &&
        move_uploaded_file($_FILES["image2"]["tmp_name"], $targetFile2)) {

        // Insert into products
        $stmt = $conn->prepare("INSERT INTO products 
            (barcode, name, category, actual_price, selling_price, image1, image2) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssddss", $barcode, $name, $category, $actual_price, $selling_price, $fileName1, $fileName2);

        if ($stmt->execute()) {
            // Insert into inventory
            $remain_qty = $registered_qty; 
            $inv = $conn->prepare("INSERT INTO inventory 
                (code, medicine, category, registered_qty, sold_qty, remain_qty, registered_date, expiry_date, remark, actual_price, selling_price, status) 
                VALUES (?, ?, ?, ?, 0, ?, CURDATE(), ?, ?, ?, ?, 'Available')");
            
            $inv->bind_param(
                "sssisssdd", 
                $barcode, 
                $name, 
                $category, 
                $registered_qty, 
                $remain_qty, 
                $expiry_date, 
                $remark, 
                $actual_price, 
                $selling_price
            );
            $inv->execute();
            $inv->close();

            header("location:product.php");
            exit;
        } else {
            echo "<script>alert('Error while inserting product!');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Image upload failed!');</script>";
    }
}

// ✅ Delete Product + Inventory
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $res = $conn->query("SELECT image1, image2, barcode FROM products WHERE id=$id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $imgPath1 = __DIR__ . "/../../uploads/" . $row['image1'];
        $imgPath2 = __DIR__ . "/../../uploads/" . $row['image2'];
        if (file_exists($imgPath1)) unlink($imgPath1);
        if (file_exists($imgPath2)) unlink($imgPath2);

        $barcode = $row['barcode'];
        $conn->query("DELETE FROM inventory WHERE code='$barcode'");
    }

    $conn->query("DELETE FROM products WHERE id=$id");
    header("location:product.php");
    exit;
}

// ✅ Fetch all products with inventory
$sql = "SELECT p.*, i.registered_qty, i.sold_qty, i.remain_qty, i.registered_date, i.expiry_date, i.remark, i.status
        FROM products p
        JOIN inventory i ON p.barcode = i.code
        ORDER BY p.id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Products & Inventory</title>
  <link rel="stylesheet" href="../../css/product1.css">
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="brand">
            <h2>🏢 Pharma Plus</h2>
        </div>

        <ul class="menu top-menu">
        <li><a href="company_dashboard.php" class="active">📊 Dashboard</a></li>
        <li><a href="profile.php">👤 Profile</a></li>
        <li><a href="product.php">💊 Add Medicine</a></li>
        <li><a href="inventory.php">📦 Inventory</a></li>
        <li><a href="order_list.php">🛒 Order List</a></li>
        <li><a href="sales_report.php">📊 Sales Report</a></li>
        </ul>
        <br><br><br>
        <ul class="menu bottom-menu">
            <li><a href="low_stock.php">🔔 Notifications</a></li>
            <li><a href="../logout.php">🚪 Logout</a></li>
        </ul>
    </aside>

<div class="container">
    <h1>➕ Add Product with Inventory</h1>

    <!-- Add Product Form -->
    <form method="POST" enctype="multipart/form-data" class="product-form">
        <input type="text" name="barcode" placeholder="Code / Barcode" required>
        <input type="text" name="name" placeholder="Item Name" required>
        <input type="text" name="category" placeholder="Category" required>
        <input type="number" name="registered_qty" placeholder="Registered Qty" required>
        <input type="date" name="expiry_date" required>
        <textarea name="remark" placeholder="Remark"></textarea>
        <input type="number" step="0.01" name="actual_price" placeholder="Actual Price (₹)" required>
        <input type="number" step="0.01" name="selling_price" placeholder="Selling Price (₹)" required>
        <label>Upload Image 1:</label>
        <input type="file" name="image1" accept="image/*" required>
        <label>Upload Image 2:</label>
        <input type="file" name="image2" accept="image/*" required>
        <button type="submit" name="add_product">Add Product</button>
    </form>

    <!-- Product + Inventory Table -->
    <h2>📦 Product & Inventory List</h2>
    <table border="1" cellpadding="8" cellspacing="0">
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
              <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td>
                <img src="../../uploads/<?= $row['image1']; ?>" width="60">
                <img src="../../uploads/<?= $row['image2']; ?>" width="60">
              </td>
              <td><?= $row['barcode']; ?></td>
              <td><?= $row['name']; ?></td>
              <td><?= $row['category']; ?></td>
              <td><?= $row['registered_qty']; ?></td>
              <td><?= $row['sold_qty']; ?></td>
              <td><?= $row['remain_qty']; ?></td>
              <td><?= $row['registered_date']; ?></td>
              <td><?= $row['expiry_date']; ?></td>
              <td><?= $row['remark']; ?></td>
              <td>₹<?= $row['actual_price']; ?></td>
              <td>₹<?= $row['selling_price']; ?></td>
              <td><?= $row['status']; ?></td>
              <td>
                <a href="product.php?delete=<?= $row['id']; ?>" onclick="return confirm('Delete this product?')"> 🗑️ Delete</a>
              </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
