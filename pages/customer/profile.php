<?php
session_start();
include("../../database/db_connect.php");

// ✅ Check if ANY user is logged in
if (!isset($_SESSION['user_session']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit();
}

$user_id   = $_SESSION['user_session'];
$username  = $_SESSION['username'];
$role      = $_SESSION['role'];

// ✅ Fetch user details
$sql = "SELECT * FROM userdata WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("❌ User not found!");
}

// ✅ Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username    = $_POST['username'];
    $email       = $_POST['email'];
    $phone       = $_POST['phone'];
    $address     = $_POST['address'];

    // ✅ Handle Photo Upload
    $profile_photo = $user['profile_photo'] ?? 'default-avatar.jpg';
    if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === 0) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $fileExtension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $fileName = "user_" . $user_id . "_" . time() . "." . $fileExtension;
        $uploadFile = $uploadDir . $fileName;

        if (getimagesize($_FILES['photo']['tmp_name'])) {
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
                $profile_photo = $fileName;

                // Delete old photo if exists
                if (!empty($user['profile_photo']) && $user['profile_photo'] !== 'default-avatar.jpg') {
                    @unlink($uploadDir . $user['profile_photo']);
                }
            }
        }
    }

    // ✅ Update Database
    $sql = "UPDATE userdata SET username=?, email=?, phone=?, address=?, profile_photo=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $username, $email, $phone, $address, $profile_photo, $user_id);

    if ($stmt->execute()) {
        $successMessage = "✅ Profile updated successfully!";
        // Refresh user info
        $stmt = $conn->prepare("SELECT * FROM userdata WHERE id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
    } else {
        $errorMessage = "❌ Error updating profile.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../../css/cus_profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<aside class="sidebar">
    <div class="brand">
        <h2><a href="../Dashboard.php">💊 MedStore</a></h2>
    </div>

    <ul class="menu top-menu">
        <li><a href="customer_dashboard.php">📊 Dashboard</a></li>
        <li><a href="profile.php">👤 Profile</a></li>
        <li><a href="selling.php">💊 Buy</a></li>
        <li><a href="customer_inventory.php">🛒 Inventory</a></li>
        <li><a href="buying.php">🛒 Purchase Records</a></li>
    </ul>

    <ul class="menu bottom-menu">   
        <li><a href="../support.php">💬 Support</a></li>
        <li><a href="../logout.php">🚪 Logout</a></li>
    </ul>
</aside>

<div class="profile-container">
    <div class="profile-header">
        <h1>👤 User Profile</h1>
        <p>Manage your account and personal information</p>
    </div>

    <?php if (isset($successMessage)): ?>
        <div class="alert success"><?php echo $successMessage; ?></div>
    <?php endif; ?>
    
    <?php if (isset($errorMessage)): ?>
        <div class="alert error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <div class="profile-content">
        <!-- ✅ ONE FORM for everything -->
        <form method="POST" enctype="multipart/form-data" class="profile-form">

            <!-- Sidebar -->
            <div class="profile-sidebar">
                <div class="profile-photo">
                    <img src="uploads/<?php echo $user['profile_photo'] ?? 'default-avatar.jpg'; ?>" 
                         alt="Profile Photo" id="profile-preview">

                    <input type="file" name="photo" id="photo-upload" accept="image/*" style="display:none;">
                    <label for="photo-upload" class="upload-btn">
                        <i class="fas fa-camera"></i> Change Photo
                    </label>
                </div>
                
                <div class="profile-stats">
                    <h3>Account Info</h3>
                    <div class="stat-item"><strong>🆔 User ID:</strong> #<?php echo $user['id']; ?></div>
                    <div class="stat-item"><strong>⚡ Role:</strong> <?php echo ucfirst($role); ?></div>
                    <div class="stat-item"><strong>📧 Email:</strong> <?php echo htmlspecialchars($user['email']); ?></div>
                </div>
            </div>

            <!-- Details -->
            <div class="profile-details">
                <h2>Personal Information</h2>
                
                <div class="form-group">
                    <label for="username">Full Name</label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($user['phone']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address"><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                    <button type="reset" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Preview uploaded image
    document.getElementById('photo-upload').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile-preview').setAttribute('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });
</script>
</body>
</html>
