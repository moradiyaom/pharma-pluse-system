<?php
session_start();
if (!isset($_SESSION['user_session'])) {
    header("location: login.php");
    exit();
}

// Include database connection
include '../database/db_connect.php';

// Get company_id from session if available
$company_id = isset($_SESSION['company_id']) ? $_SESSION['company_id'] : null;

// Fetch sales data from database using sales table
$salesData = ['labels' => [], 'data' => []];
$salesQuery = "SELECT 
    MONTHNAME(sold_date) as month, 
    YEAR(sold_date) as year,
    SUM(total) as total_sales
    FROM sales 
    WHERE sold_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY YEAR(sold_date), MONTH(sold_date)
    ORDER BY YEAR(sold_date), MONTH(sold_date)";


$salesResult = $conn->query($salesQuery);
if ($salesResult && $salesResult->num_rows > 0) {
    while ($row = $salesResult->fetch_assoc()) {
        $salesData['labels'][] = substr($row['month'], 0, 3) . ' ' . $row['year'];
        $salesData['data'][] = (float) $row['total_sales'];
    }
} else {
    // Default data if no records found
    $salesData = [
        'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        'data' => [65, 59, 80, 81, 56, 55, 72, 78, 80, 85, 90, 95]
    ];
}

// Fetch top products from sales table
$topProducts = [];
$productsQuery = "SELECT 
    medicine as name, 
    SUM(quantity) as total_sold,
    SUM(total) as total_revenue
    FROM sales 
    GROUP BY medicine
    ORDER BY total_sold DESC
    LIMIT 5";






// Fetch sales by category from sales table
$salesByCategory = [];
$categoryQuery = "
    SELECT 
        category,
        SUM(sold_qty * selling_price) AS category_revenue,
        COUNT(DISTINCT medicine) AS product_count
    FROM inventory
    WHERE category IS NOT NULL AND category != ''
    GROUP BY category
    ORDER BY category_revenue DESC
";

$categoryResult = $conn->query($categoryQuery);
if ($categoryResult && $categoryResult->num_rows > 0) {
    $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#6f42c1', '#fd7e14'];
    $i = 0;
    while ($row = $categoryResult->fetch_assoc()) {
        $salesByCategory[] = [
            'category' => $row['category'],
            'value' => (float) $row['category_revenue'],
            'color' => $colors[$i % count($colors)],
            'product_count' => (int) $row['product_count']
        ];
        $i++;
    }
} else {
    // Default data if no records found
    $salesByCategory = [
        ['category' => 'Medicines', 'value' => 45, 'color' => '#4e73df', 'product_count' => 12],
        ['category' => 'Supplements', 'value' => 25, 'color' => '#1cc88a', 'product_count' => 8],
        ['category' => 'Equipment', 'value' => 15, 'color' => '#36b9cc', 'product_count' => 5],
        ['category' => 'Personal Care', 'value' => 10, 'color' => '#f6c23e', 'product_count' => 7],
        ['category' => 'Others', 'value' => 5, 'color' => '#e74a3b', 'product_count' => 3]
    ];
}

// Fetch total revenue from sales table
$totalRevenue = 0;
$revenueQuery = "SELECT SUM(total) as total_revenue FROM sales";

$revenueResult = $conn->query($revenueQuery);
if ($revenueResult && $revenueResult->num_rows > 0) {
    $row = $revenueResult->fetch_assoc();
    $totalRevenue = (float) $row['total_revenue'];
}

// Fetch total sales (number of items sold) from sales table
$totalSales = 0;
$salesQuery = "SELECT SUM(quantity) as total_sales FROM sales";

$salesResult = $conn->query($salesQuery);
if ($salesResult && $salesResult->num_rows > 0) {
    $row = $salesResult->fetch_assoc();
    $totalSales = (int) $row['total_sales'];
}

// Calculate average order value
$avgOrderValue = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

// Fetch total number of sales transactions
$totalTransactions = 0;
$transactionsQuery = "SELECT COUNT(*) as total FROM sales";

$transactionsResult = $conn->query($transactionsQuery);
if ($transactionsResult && $transactionsResult->num_rows > 0) {
    $row = $transactionsResult->fetch_assoc();
    $totalTransactions = (int) $row['total'];
}

// Fetch payment method distribution
$paymentMethods = [];
$paymentQuery = "SELECT 
    payment_method,
    COUNT(*) as transaction_count,
    SUM(total) as total_amount
    FROM sales 
    GROUP BY payment_method
    ORDER BY total_amount DESC";


$paymentResult = $conn->query($paymentQuery);
if ($paymentResult && $paymentResult->num_rows > 0) {
    while ($row = $paymentResult->fetch_assoc()) {
        $paymentMethods[] = [
            'method' => $row['payment_method'],
            'count' => (int) $row['transaction_count'],
            'amount' => (float) $row['total_amount']
        ];
    }
}

// Fetch unique products count from sales table
$totalProducts = 0;
$productsQuery = "SELECT COUNT(DISTINCT medicine) as total FROM sales WHERE 1=1";
$productsResult = $conn->query($productsQuery);
if ($productsResult && $productsResult->num_rows > 0) {
    $row = $productsResult->fetch_assoc();
    $totalProducts = (int) $row['total'];
}

// Fetch total categories count from sales table
$totalCategories = 0;
$categoriesQuery = "SELECT COUNT(DISTINCT category) as total FROM sales WHERE category IS NOT NULL AND category != ''" . ($company_id ? " AND company_id = $company_id" : "");
$categoriesResult = $conn->query($categoriesQuery);
if ($categoriesResult && $categoriesResult->num_rows > 0) {
    $row = $categoriesResult->fetch_assoc();
    $totalCategories = (int) $row['total'];
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sales Analytics | Medical Store Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        --gray: #6c757d;
        --light-gray: #e9ecef;
    }

    body {
        background: linear-gradient(135deg, #e3f2fd 0%, #f8f9fa 100%);
        min-height: 100vh;
        color: #333;
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
        padding: 2rem;
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .dashboard-header h1 {
        color: var(--dark);
        font-size: 2.2rem;
        position: relative;
        padding-bottom: 0.5rem;
    }

    .dashboard-header h1::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100px;
        height: 4px;
        background: linear-gradient(to right, var(--primary), var(--secondary));
        border-radius: 2px;
    }

    .date-filter {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .date-filter select {
        padding: 8px 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background: white;
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

    .stat-change {
        font-size: 0.9rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
    }

    .change-up {
        color: var(--success);
    }

    .change-down {
        color: var(--danger);
    }

    /* Charts Container */
    .charts-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .chart-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .chart-header h3 {
        color: var(--dark);
        font-size: 1.2rem;
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    /* Products Table */
    .products-table {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 2.5rem;
        overflow: hidden;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .table-header h3 {
        color: var(--dark);
        font-size: 1.2rem;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    th {
        font-weight: 600;
        color: var(--dark);
        background-color: #f8f9fa;
    }

    tr:hover {
        background-color: #f8f9fa;
    }

    .product-name {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .product-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: var(--light-gray);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
    }

    .badge {
        padding: 0.25rem 0.5rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-success {
        background-color: rgba(40, 167, 69, 0.15);
        color: var(--success);
    }

    .badge-warning {
        background-color: rgba(255, 193, 7, 0.15);
        color: var(--warning);
    }

    /* Payment Methods */
    .payment-methods {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 2.5rem;
    }

    .payment-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .payment-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        text-align: center;
    }

    .payment-icon {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        color: var(--primary);
    }

    .payment-name {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .payment-stats {
        font-size: 0.9rem;
        color: var(--gray);
    }

    /* Footer */
    .footer {
        background: linear-gradient(to right, var(--dark), var(--accent));
        color: white;
        padding: 2rem 0 1rem;
        margin-top: 2rem;
    }

    .footer-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        max-width: 1400px;
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

    .footer-bottom {
        text-align: center;
        padding-top: 2rem;
        margin-top: 2rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .charts-container {
            grid-template-columns: 1fr;
        }
    }

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
        
        .dashboard-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .stats-container {
            grid-template-columns: 1fr;
        }
        
        table {
            display: block;
            overflow-x: auto;
        }
    }

    @media (max-width: 576px) {
        .nav-right {
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .date-filter {
            flex-direction: column;
            align-items: flex-start;
        }
    }
  </style>
</head>
<body>

<!-- Navigation Bar -->
<header>
  <nav class="navbar">
    <a href="#" class="logo">
      <img src="../images/i1.jpeg" alt="Medical Store Logo">
      <span style="font-weight: bold; font-size: 1.2rem;">MediCare Analytics</span>
    </a>
    
    <div class="nav-right">
      <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="mean.php" class="active"><i class="fas fa-chart-line"></i> Analytics</a>
      <a href="home.php"><i class="fas fa-home"></i> Home</a>
      
      <a href="../pages/company/company_dashboard.php">Companies</a>
        <a href="../pages/customer/customer_dashboard.php">Customers</a>
    </div>
    
    <div style="display: flex; align-items: center; gap: 1rem;">
      <div style="background: rgba(255, 255, 255, 0.2); padding: 0.5rem 1rem; border-radius: 4px;">
        <i class="fas fa-user-circle"></i> 
        <b><?php echo $_SESSION['username'] ?? 'User'; ?></b> (<?php echo $_SESSION['role'] ?? 'User'; ?>)
      </div>
      <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </nav>
</header>

<!-- Dashboard Content -->
<div class="dashboard">
  <div class="dashboard-header">
    <h1>Sales Analytics Dashboard</h1>
    <div class="date-filter">
      <span>Filter by:</span>
      <select>
        <option>Last 7 Days</option>
        <option>Last 30 Days</option>
        <option selected>Last 90 Days</option>
        <option>This Year</option>
      </select>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="stats-container">
    <div class="stat-card">
      <div class="stat-icon">
        <i class="fas fa-dollar-sign"></i>
      </div>
      <div class="stat-title">TOTAL REVENUE</div>
      <div class="stat-value">$<?php echo number_format($totalRevenue, 2); ?></div>
      <div class="stat-change change-up">
        <i class="fas fa-arrow-up"></i> 12.5% from last period
      </div>
    </div>
    
    <div class="stat-card">
      <div class="stat-icon">
        <i class="fas fa-shopping-cart"></i>
      </div>
      <div class="stat-title">TOTAL ITEMS SOLD</div>
      <div class="stat-value"><?php echo number_format($totalSales); ?></div>
      <div class="stat-change change-up">
        <i class="fas fa-arrow-up"></i> 8.3% from last period
      </div>
    </div>
    
    <div class="stat-card">
      <div class="stat-icon">
        <i class="fas fa-receipt"></i>
      </div>
      <div class="stat-title">AVG ITEM VALUE</div>
      <div class="stat-value">$<?php echo number_format($avgOrderValue, 2); ?></div>
      <div class="stat-change change-up">
        <i class="fas fa-arrow-up"></i> 3.9% from last period
      </div>
    </div>
    
    <div class="stat-card">
      <div class="stat-icon">
        <i class="fas fa-file-invoice"></i>
      </div>
      <div class="stat-title">TOTAL TRANSACTIONS</div>
      <div class="stat-value"><?php echo number_format($totalTransactions); ?></div>
      <div class="stat-change change-up">
        <i class="fas fa-arrow-up"></i> 5.2% from last period
      </div>
    </div>
  </div>

  <!-- Additional Stats Row -->
  <div class="stats-container">
    <div class="stat-card">
      <div class="stat-icon">
        <i class="fas fa-pills"></i>
      </div>
      <div class="stat-title">UNIQUE PRODUCTS</div>
      <div class="stat-value"><?php echo number_format($totalProducts); ?></div>
      <div class="stat-change change-up">
        <i class="fas fa-arrow-up"></i> 7.1% from last period
      </div>
    </div>
    
    <div class="stat-card">
      <div class="stat-icon">
        <i class="fas fa-tags"></i>
      </div>
      <div class="stat-title">PRODUCT CATEGORIES</div>
      <div class="stat-value"><?php echo number_format($totalCategories); ?></div>
      <div class="stat-change change-up">
        <i class="fas fa-arrow-up"></i> 4.8% from last period
      </div>
    </div>
    
    <div class="stat-card">
      <div class="stat-icon">
        <i class="fas fa-credit-card"></i>
      </div>
      <div class="stat-title">PAYMENT METHODS</div>
      <div class="stat-value"><?php echo count($paymentMethods); ?></div>
      <div class="stat-change change-up">
        <i class="fas fa-arrow-up"></i> 2.3% from last period
      </div>
    </div>
    
    <div class="stat-card">
      <div class="stat-icon">
        <i class="fas fa-calendar"></i>
      </div>
      <div class="stat-title">SALES PERIOD</div>
      <div class="stat-value">12 Months</div>
      <div class="stat-change change-up">
        <i class="fas fa-arrow-up"></i> Current Data
      </div>
    </div>
  </div>

  <!-- Payment Methods -->
  <?php if (!empty($paymentMethods)): ?>
  <div class="payment-methods">
    <div class="chart-header">
      <h3>Payment Methods</h3>
      <span>Transaction Distribution</span>
    </div>
    <div class="payment-grid">
      <?php foreach ($paymentMethods as $payment): ?>
      <div class="payment-item">
        <div class="payment-icon">
          <?php 
          switch($payment['method']) {
            case 'Cash': echo '<i class="fas fa-money-bill-wave"></i>'; break;
            case 'Card': echo '<i class="fas fa-credit-card"></i>'; break;
            case 'UPI': echo '<i class="fas fa-mobile-alt"></i>'; break;
            case 'NetBanking': echo '<i class="fas fa-laptop"></i>'; break;
            default: echo '<i class="fas fa-money-check"></i>';
          }
          ?>
        </div>
        <div class="payment-name"><?php echo $payment['method']; ?></div>
        <div class="payment-stats">
          <?php echo $payment['count']; ?> transactions<br>
          $<?php echo number_format($payment['amount'], 2); ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Charts -->
  <div class="charts-container">
    <div class="chart-card">
      <div class="chart-header">
        <h3>Sales Overview</h3>
        <span>Last 12 Months</span>
      </div>
      <div class="chart-container">
        <canvas id="salesChart"></canvas>
      </div>
    </div>
    
    <div class="chart-card">
      <div class="chart-header">
        <h3>Sales by Category</h3>
        <span>Percentage</span>
      </div>
      <div class="chart-container">
        <canvas id="categoryChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Top Products Table -->
  <div class="products-table">
    <div class="table-header">
      <h3>Top Selling Products</h3>
      <button class="btn"><i class="fas fa-download"></i> Export Report</button>
    </div>
    
    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Category</th>
          <th>Units Sold</th>
          <th>Revenue</th>
          <th>Trend</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($topProducts as $product): ?>
        <tr>
          <td>
            <div class="product-name">
              <div class="product-icon">
                <i class="fas fa-pills"></i>
              </div>
              <span><?php echo htmlspecialchars($product['name']); ?></span>
            </div>
          </td>
          <td><?php echo htmlspecialchars($product['category']); ?></td>
          <td><?php echo number_format($product['sales']); ?></td>
          <td>$<?php echo number_format($product['revenue'], 2); ?></td>
          <td><span class="badge badge-success">+12.5%</span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Footer -->
<footer class="footer">
  <div class="footer-container">
    <div class="footer-section">
      <h3>About MediCare Analytics</h3>
      <p>Advanced analytics platform for medical stores to track sales, inventory, and business performance.</p>
    </div>
    <div class="footer-section">
      <h3>Quick Links</h3>
      <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="inventory.php">Inventory</a></li>
        <li><a href="orders.php">Orders</a></li>
        <li><a href="reports.php">Reports</a></li>
      </ul>
    </div>
    <div class="footer-section">
      <h3>Contact</h3>
      <p>Email: support@medicare.com</p>
      <p>Phone: +1 (555) 123-4567</p>
      <p>Address: 123 Medical Ave, Healthcare City</p>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© <?php echo date("Y"); ?> MediCare Analytics | All Rights Reserved</p>
  </div>
</footer>

<script>
  // Sales Chart
  const salesCtx = document.getElementById('salesChart').getContext('2d');
  const salesChart = new Chart(salesCtx, {
    type: 'line',
    data: {
      labels: <?php echo json_encode($salesData['labels']); ?>,
      datasets: [{
        label: 'Sales Revenue',
        data: <?php echo json_encode($salesData['data']); ?>,
        backgroundColor: 'rgba(44, 125, 160, 0.1)',
        borderColor: '#2c7da0',
        borderWidth: 2,
        tension: 0.3,
        fill: true
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            drawBorder: false
          }
        },
        x: {
          grid: {
            display: false
          }
        }
      }
    }
  });

  // Category Chart
  const categoryCtx = document.getElementById('categoryChart').getContext('2d');
  const categoryChart = new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
      labels: <?php echo json_encode(array_column($salesByCategory, 'category')); ?>,
      datasets: [{
        data: <?php echo json_encode(array_column($salesByCategory, 'value')); ?>,
        backgroundColor: <?php echo json_encode(array_column($salesByCategory, 'color')); ?>,
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '70%',
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });
</script>
</body>
</html>