<?php
session_start();
if (!isset($_SESSION['user_session'])) {
    header("location: login.php");
    exit();
}

// Database connection
include '../database/db_connect.php';

// ✅ If you stored only username in session
$username = $_SESSION['user_session'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : "User"; // default role

// Fetch statistics from database
$total_orders = 0;
$products_in_stock = 0;
$total_customers = 0;
$monthly_revenue = 0;

// Get total orders
$order_query = "SELECT COUNT(*) as total FROM sales";
$order_result = $conn->query($order_query);
if ($order_result && $order_result->num_rows > 0) {
    $order_data = $order_result->fetch_assoc();
    $total_orders = $order_data['total'];
}

// Get products in stock (assuming you have a products table with stock_quantity)
$product_query = "SELECT SUM(remain_qty) as total_stock FROM inventory WHERE remain_qty > 0";
$product_result = $conn->query($product_query);
if ($product_result && $product_result->num_rows > 0) {
    $product_data = $product_result->fetch_assoc();
    $products_in_stock = $product_data['total_stock'] ?: 0;
}

// Get total customers
$customer_query = "SELECT COUNT(*) as total FROM customers";
$customer_result = $conn->query($customer_query);
if ($customer_result && $customer_result->num_rows > 0) {
    $customer_data = $customer_result->fetch_assoc();
    $total_customers = $customer_data['total'];
}

$today_revenue_query = "SELECT SUM(total) as today_revenue 
                        FROM sales
                        WHERE DATE(sold_date) = CURDATE()";
$today_revenue_result = $conn->query($today_revenue_query);
$today_revenue_data = $today_revenue_result->fetch_assoc();
$today_revenue = $today_revenue_data['today_revenue'] ?? 0;


$month_revenue_query = "SELECT SUM(total) as month_revenue 
                        FROM sales
                        WHERE MONTH(sold_date) = MONTH(CURDATE())
                        AND YEAR(sold_date) = YEAR(CURDATE())";
$month_revenue_result = $conn->query($month_revenue_query);
$month_revenue_data = $month_revenue_result->fetch_assoc();
$month_revenue = $month_revenue_data['month_revenue'] ?? 0;



$total_revenue_query = "SELECT SUM(total) as total_revenue FROM sales";
$total_revenue_result = $conn->query($total_revenue_query);
$total_revenue_data = $total_revenue_result->fetch_assoc();
$total_revenue = $total_revenue_data['total_revenue'] ?? 0;




// Close connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | Medical Store Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Global Styles */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    :root {
      --primary: #2c7da0;
      --secondary: #a9d6e5;
      --accent: #01497c;
      --light: #f8f9fa;
      --dark: #012a4a;
      --success: #28a745;
      --danger: #dc3545;
      --warning: #ffc107;
      --info: #17a2b8;
    }

    body {
      background: linear-gradient(135deg, #e3f2fd 0%, #f8f9fa 100%);
      color: #333;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Header Styles */
    header {
      background: linear-gradient(to right, var(--primary), var(--accent));
      color: white;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.8rem 2rem;
    }

    .logo {
      display: flex;
      align-items: center;
    }

    .logo img {
      height: 50px;
      width: auto;
      border-radius: 8px;
      margin-right: 10px;
    }

    .nav-right {
      display: flex;
      gap: 1.5rem;
    }

    .nav-right a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      transition: all 0.3s ease;
    }

    .nav-right a:hover {
      background-color: rgba(255, 255, 255, 0.2);
      transform: translateY(-2px);
    }

    .logout-btn {
      background-color: var(--danger);
      color: white;
      padding: 0.5rem 1.2rem;
      border-radius: 4px;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .logout-btn:hover {
      background-color: #bd2130;
      transform: translateY(-2px);
    }

    /* Dashboard Content */
    .dashboard {
      flex: 1;
      padding: 2rem;
      max-width: 1200px;
      margin: 0 auto;
      width: 100%;
    }

    .dashboard h2 {
      color: var(--dark);
      margin-bottom: 1.5rem;
      font-size: 2.2rem;
      text-align: center;
      position: relative;
      padding-bottom: 0.5rem;
    }

    .dashboard h2::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 4px;
      background: linear-gradient(to right, var(--primary), var(--secondary));
      border-radius: 2px;
    }

    /* Stats Cards */
    .stats-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2.5rem;
    }

    .stat-card {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
      display: flex;
      flex-direction: column;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    .stat-icon {
      font-size: 2rem;
      margin-bottom: 1rem;
      color: var(--primary);
    }

    .stat-title {
      font-size: 0.9rem;
      color: #6c757d;
      margin-bottom: 0.5rem;
    }

    .stat-value {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--dark);
    }

    /* Activity Section */
    .activity-container {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
      margin-bottom: 2.5rem;
    }

    .activity-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      border-bottom: 2px solid var(--light);
      padding-bottom: 0.8rem;
    }

    .activity-header h3 {
      color: var(--dark);
      font-size: 1.5rem;
    }

    .activity-list {
      list-style: none;
    }

    .activity-item {
      padding: 1rem;
      border-left: 4px solid var(--primary);
      background-color: #f8f9fa;
      margin-bottom: 1rem;
      border-radius: 0 8px 8px 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .activity-content {
      flex: 1;
    }

    .activity-type {
      font-weight: 600;
      color: var(--dark);
      margin-bottom: 0.3rem;
    }

    .activity-desc {
      color: #6c757d;
      margin-bottom: 0.3rem;
    }

    .activity-time {
      font-size: 0.85rem;
      color: #6c757d;
    }

    /* Slider Styles */
    .slider {
      position: relative;
      margin: 2rem 0 3rem;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .slides {
      position: relative;
      height: 400px;
    }

    .slide {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      opacity: 0;
      transition: opacity 1s ease;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      padding: 2rem;
      color: white;
      background-size: cover;
      background-position: center;
    }

    .slide:nth-child(1) {
      background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../images/sales.jpeg') center/cover;
    }

    .slide:nth-child(2) {
      background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../images/Sales-Revenue.webp') center/cover;
    }

    .slide:nth-child(3) {
      background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../images/medicine.webp') center/cover;
    }

    .slide.active {
      opacity: 1;
    }

    .slide p {
      font-size: 1.5rem;
      font-weight: 500;
      max-width: 700px;
      margin-top: 1.5rem;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
    }

    .slider-controls {
      position: absolute;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 10px;
      z-index: 10;
    }

    .dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background-color: rgba(255, 255, 255, 0.5);
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .dot.active, .dot:hover {
      background-color: white;
      transform: scale(1.2);
    }

    /* Footer Styles */
    .footer {
      background: linear-gradient(to right, var(--dark), var(--accent));
      color: white;
      padding: 3rem 0 1rem;
      margin-top: auto;
    }

    .footer-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2rem;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 2rem;
    }

    .footer-section h3 {
      font-size: 1.2rem;
      margin-bottom: 1.2rem;
      position: relative;
      padding-bottom: 0.5rem;
    }

    .footer-section h3::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 40px;
      height: 3px;
      background-color: var(--secondary);
      border-radius: 2px;
    }

    .footer-section p {
      line-height: 1.6;
      margin-bottom: 1rem;
    }

    .footer-section ul {
      list-style: none;
    }

    .footer-section ul li {
      margin-bottom: 0.8rem;
    }

    .footer-section a {
      color: #e3f2fd;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .footer-section a:hover {
      color: white;
      text-decoration: underline;
    }

    .footer-bottom {
      text-align: center;
      padding-top: 2rem;
      margin-top: 2rem;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Responsive Design */
    @media (max-width: 992px) {
      .navbar {
        flex-direction: column;
        padding: 1rem;
      }
      
      .nav-right {
        margin: 1rem 0;
      }
    }

    @media (max-width: 768px) {
      .dashboard {
        padding: 1.5rem;
      }
      
      .slides {
        height: 300px;
      }
      
      .slide p {
        font-size: 1.2rem;
      }
      
      .stats-container {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 576px) {
      .nav-right {
        flex-wrap: wrap;
        justify-content: center;
      }
      
      .slides {
        height: 250px;
      }
      
      .slide p {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>

<!-- ✅ Navigation Bar -->
<header>
  <nav class="navbar">
    <a href="#" class="logo">
      <img src="../images/i1.jpeg" alt="Medical Store Logo">
      <span style="font-weight: bold; font-size: 1.2rem;">MediCare</span>
    </a>
    
    <div class="nav-right">
      <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="analytics.php"><i class="fas fa-chart-line"></i> Analytics</a>
      <a href="home.php"><i class="fas fa-home"></i> Home</a>
      
      <a href="../pages/company/company_dashboard.php">Companies</a>
          <a href="../pages/customer/customer_dashboard.php">Customers</a>
    </div>
    
    <div style="display: flex; align-items: center; gap: 1rem;">
      <div style="background: rgba(255, 255, 255, 0.2); padding: 0.5rem 1rem; border-radius: 4px;">
        <i class="fas fa-user-circle"></i> 
        <b><?php echo htmlspecialchars($username); ?></b> (<?php echo htmlspecialchars($role); ?>)
      </div>
      <a href="../pages/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </nav>
</header>

<div class="dashboard">
  <h2>Welcome to Your Dashboard, <?php echo htmlspecialchars($username); ?>!</h2>
  
  <!-- Stats Cards -->
  <div class="stats-container">
    <div class="stat-card">
      <div class="stat-icon">
        <i class="fas fa-shopping-cart"></i>
      </div>
      <div class="stat-title">TOTAL SALES</div>
      <div class="stat-value"><?php echo number_format($total_orders); ?></div>
    </div>
    
    <div class="stat-card">
      <div class="stat-icon">
        <i class="fas fa-pills"></i>
      </div>
      <div class="stat-title">PRODUCTS IN STOCK</div>
      <div class="stat-value"><?php echo number_format($products_in_stock); ?></div>
    </div>
    
    <div class="stat-card">
      <div class="stat-icon">
        <i class="fas fa-users"></i>
      </div>
      <div class="stat-title">TOTAL CUSTOMERS</div>
      <div class="stat-value"><?php echo number_format($total_customers); ?></div>
    </div>
    
    <div class="stat-card">
      <div class="stat-icon">
        <i class="fas fa-chart-line"></i>
      </div>
      <div class="stat-title">MONTHLY REVENUE</div>
      <div class="stat-value">$<?php echo number_format($today_revenue, 2); ?></div>
    </div>
  </div>

  <!-- Recent Activities -->
  <?php if (!empty($activities)): ?>
  <div class="activity-container">
    <div class="activity-header">
      <h3>Recent Activities</h3>
      <a href="activities.php" style="color: var(--primary); text-decoration: none;">View All</a>
    </div>
    <ul class="activity-list">
      <?php foreach ($activities as $activity): ?>
      <li class="activity-item">
        <div class="activity-content">
          <div class="activity-type"><?php echo htmlspecialchars($activity['activity_type']); ?></div>
          <div class="activity-desc"><?php echo htmlspecialchars($activity['description']); ?></div>
          <div class="activity-time">
            <i class="far fa-clock"></i> 
            <?php echo date('M j, Y g:i A', strtotime($activity['created_at'])); ?>
          </div>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <!-- Slider -->
  <div class="slider">
    <div class="slides">
      <!-- Slide 1 -->
      <div class="slide active">
        <p>📊 Sales Overview - Track your revenue and growth with advanced analytics</p>
      </div>

      <!-- Slide 2 -->
      <div class="slide">
        <p>💼 Inventory Management - Keep track of your medical supplies efficiently</p>
      </div>

      <!-- Slide 3 -->
      <div class="slide">
        <p>📧 Customer Relations - Stay connected with your clients and providers</p>
      </div>
    </div>

    <!-- Slider Dots -->
    <div class="slider-controls">
      <span class="dot active"></span>
      <span class="dot"></span>
      <span class="dot"></span>
    </div>
  </div>
</div>

<!-- ✅ Creative Footer -->
<footer class="footer">
  <div class="footer-container">
    <div class="footer-section">
      <h3>📌 About Us</h3>
      <p>We provide smart solutions to manage inventory, sales, and orders efficiently for medical stores and pharmacies.</p>
    </div>
    <div class="footer-section">
      <h3>🔗 Quick Links</h3>
      <ul>
        <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="analytics.php"><i class="fas fa-chart-bar"></i> Analytics</a></li>
      </ul>
    </div>
    <div class="footer-section">
      <h3>📧 Contact</h3>
      <p><i class="fas fa-envelope"></i> Email: ommoradiya22@gmail.com</p>
      <p><i class="fas fa-phone"></i> Phone: +91 9484485519</p>
      <p><i class="fas fa-map-marker-alt"></i> Address: 123 Medical St, Healthcare City</p>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© <?php echo date("Y"); ?> MediCare Dashboard System | All Rights Reserved</p>
  </div>
</footer>

<script>
  let index = 0;
  const slides = document.querySelectorAll(".slide");
  const dots = document.querySelectorAll(".dot");

  function showSlide(i) {
    slides.forEach(slide => slide.classList.remove("active"));
    dots.forEach(dot => dot.classList.remove("active"));
    
    slides[i].classList.add("active");
    dots[i].classList.add("active");
    index = i;
  }

  function autoSlide() {
    index = (index + 1) % slides.length;
    showSlide(index);
  }

  // Manual click on dot
  dots.forEach((dot, i) => {
    dot.addEventListener("click", () => {
      showSlide(i);
    });
  });

  // Start slideshow
  setInterval(autoSlide, 5000);
</script>

</body>
</html>