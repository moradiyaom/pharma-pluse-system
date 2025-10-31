<?php
session_start();
if (!isset($_SESSION['user_session'])) {
    header("location: login.php");
    exit();
}

$username = isset($_SESSION['user_session']) ? $_SESSION['user_session'] : "Guest";
$role = isset($_SESSION['role']) ? $_SESSION['role'] : "User";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Store Management</title>
    <link rel="stylesheet" href="../css/mean.css">
    <link href="https://fonts.googleapis.com/css2?family=Andika&display=swap" rel="stylesheet">
</head>
<body>
    
<!-- ✅ Navigation Bar -->
<header>
<nav class="navbar">
        <a href="#" class="logo">
            <img src="../images/i1.jpeg" alt="Medical Store Logo">
        </a>
    
    <div class="nav-right">    
        <a href="dashboard.php">Dashboard</a>
        <a href="mean.php">mean</a>
        <a href="home.php">Home</a>
        <a href="company/company_dashboard.php">Company</a>
        <a href="customer/customer_dashboard.php">Customer</a>
    </div>
    <div style="margin-right: 20px;">
        Logged in as: <b><?php echo ucfirst($username); ?></b> (<?php echo $role; ?>)
    </div>
    <a href="logout.php" class="logout-btn">Logout</a>
    </nav>
</header>

<section class="main">
    <div class="left">
        <h4>We Are Here For Your Care</h4>
        <h5>We The Best Pharmacy</h5>
        <p>We are here for your care 24/7. Any help just call us</p>
        <button>Make An Appointment</button>    
    </div>
    <div class="right">
        <img src="../images/i3.jpg">
    </div>
</section>

</body>
</html>
