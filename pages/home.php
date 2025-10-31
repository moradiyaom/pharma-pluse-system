<?php
session_start();
// Optional: Check if user is logged in to display personalized content
$isLoggedIn = isset($_SESSION['user_session']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
$role = $isLoggedIn ? $_SESSION['role'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MediCare - Medical Store Management System</title>
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
        --primary-light: #61a5c2;
        --primary-dark: #01497c;
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
        background-color: #f8f9fa;
        color: #333;
        line-height: 1.6;
    }

    .container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .btn {
        display: inline-block;
        padding: 12px 24px;
        background: linear-gradient(to right, var(--primary), var(--primary-dark));
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        text-align: center;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(44, 125, 160, 0.3);
    }

    .btn-outline {
        background: transparent;
        border: 2px solid var(--primary);
        color: var(--primary);
    }

    .btn-outline:hover {
        background: var(--primary);
        color: white;
    }

    .section-title {
        font-size: 2.2rem;
        color: var(--dark);
        text-align: center;
        margin-bottom: 3rem;
        position: relative;
        padding-bottom: 15px;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(to right, var(--primary), var(--secondary));
        border-radius: 2px;
    }

    /* Header Styles */
    header {
        background: linear-gradient(to right, var(--primary), var(--primary-dark));
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
        padding: 1rem 0;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        color: white;
    }

    .logo img {
        height: 50px;
        width: auto;
        border-radius: 8px;
    }

    .logo-text {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .nav-menu {
        display: flex;
        gap: 1.5rem;
        list-style: none;
    }

    .nav-menu a {
        color: white;
        text-decoration: none;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .nav-menu a:hover, .nav-menu a.active {
        background-color: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    .nav-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .user-info {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-size: 0.9rem;
    }

    /* Hero Section */
    .hero {
        background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1500&q=80');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 6rem 0;
        text-align: center;
    }

    .hero-content {
        max-width: 800px;
        margin: 0 auto;
    }

    .hero h1 {
        font-size: 3rem;
        margin-bottom: 1.5rem;
        line-height: 1.2;
    }

    .hero p {
        font-size: 1.2rem;
        margin-bottom: 2rem;
        opacity: 0.9;
    }

    .hero-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
    }

    /* Features Section */
    .features {
        padding: 5rem 0;
        background-color: white;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }

    .feature-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    .feature-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
        font-size: 1.8rem;
    }

    .feature-card h3 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        color: var(--dark);
    }

    /* Services Section */
    .services {
        padding: 5rem 0;
        background: linear-gradient(135deg, #e3f2fd 0%, #f8f9fa 100%);
    }

    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }

    .service-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease;
    }

    .service-card:hover {
        transform: translateY(-5px);
    }

    .service-img {
        height: 200px;
        background-size: cover;
        background-position: center;
    }

    .service-content {
        padding: 1.5rem;
    }

    .service-content h3 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        color: var(--dark);
    }

    /* Stats Section */
    .stats {
        padding: 4rem 0;
        background: linear-gradient(to right, var(--primary), var(--primary-dark));
        color: white;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
        text-align: center;
    }

    .stat-item h3 {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
    }

    /* Testimonials */
    .testimonials {
        padding: 5rem 0;
        background-color: white;
    }

    .testimonial-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }

    .testimonial-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .testimonial-text {
        font-style: italic;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .testimonial-text::before {
        content: """;
        font-size: 4rem;
        color: var(--secondary);
        position: absolute;
        top: -1.5rem;
        left: -1rem;
        opacity: 0.2;
    }

    .testimonial-author {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .author-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: var(--secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }

    /* CTA Section */
    .cta {
        padding: 5rem 0;
        background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)), url('https://images.unsplash.com/photo-1587854692152-cbe660dbde88?ixlib=rb-4.0.3&auto=format&fit=crop&w=1500&q=80');
        background-size: cover;
        background-position: center;
        color: white;
        text-align: center;
    }

    .cta h2 {
        font-size: 2.5rem;
        margin-bottom: 1.5rem;
    }

    .cta p {
        max-width: 700px;
        margin: 0 auto 2rem;
        font-size: 1.2rem;
        opacity: 0.9;
    }

    /* Footer */
    .footer {
        background: var(--dark);
        color: white;
        padding: 4rem 0 2rem;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .footer-section h3 {
        font-size: 1.2rem;
        margin-bottom: 1.5rem;
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

    .footer-links {
        list-style: none;
    }

    .footer-links li {
        margin-bottom: 0.8rem;
    }

    .footer-links a {
        color: #e3f2fd;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-links a:hover {
        color: white;
        text-decoration: underline;
    }

    .social-links {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .social-links a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .social-links a:hover {
        background: var(--primary);
        transform: translateY(-3px);
    }

    .footer-bottom {
        text-align: center;
        padding-top: 2rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .navbar {
            flex-direction: column;
            gap: 1rem;
        }
        
        .nav-menu {
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .hero h1 {
            font-size: 2.5rem;
        }
    }

    @media (max-width: 768px) {
        .hero h1 {
            font-size: 2rem;
        }
        
        .hero-buttons {
            flex-direction: column;
            align-items: center;
        }
        
        .section-title {
            font-size: 1.8rem;
        }
    }

    @media (max-width: 576px) {
        .hero {
            padding: 4rem 0;
        }
        
        .nav-menu {
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }
        
        .nav-actions {
            flex-direction: column;
            gap: 0.5rem;
        }
    }

    /* Mobile Menu */
    .menu-toggle {
        display: none;
        flex-direction: column;
        cursor: pointer;
    }

    .menu-toggle span {
        width: 25px;
        height: 3px;
        background: white;
        margin: 3px 0;
        transition: all 0.3s ease;
    }

    @media (max-width: 768px) {
        .menu-toggle {
            display: flex;
        }
        
        .nav-menu {
            display: none;
            flex-direction: column;
            width: 100%;
            text-align: center;
        }
        
        .nav-menu.active {
            display: flex;
        }
        
        .nav-actions {
            display: none;
            flex-direction: column;
            width: 100%;
            text-align: center;
        }
        
        .nav-actions.active {
            display: flex;
        }
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header>
    <div class="container">
      <nav class="navbar">
        <a href="home.php" class="logo">
          <img src="../images/i1.jpeg" alt="MediCare Logo">
          <span class="logo-text">MediCare</span>
        </a>
        
        <div class="menu-toggle" id="mobile-menu">
          <span></span>
          <span></span>
          <span></span>
        </div>
        
        <ul class="nav-menu">
          
          <li><a href="dashboard.php">Dashboard</a></li>
          <li><a href="analytics.php">Analytics</a></li>
          <li><a href="home.php">Home</a></li>
          <li><a href="../pages/company/company_dashboard.php">Companies</a></li>
          <li><a href="../pages/customer/customer_dashboard.php">Customers</a></li>
        </ul>
        
        <div class="nav-actions">
          <?php if ($isLoggedIn): ?>
            <div class="user-info">
              <i class="fas fa-user-circle"></i> 
              <b><?php echo $username; ?></b> (<?php echo $role; ?>)
            </div>
            <a href="logout.php" class="btn">Logout</a>
          <?php else: ?>
            <a href="login.php" class="btn">Login</a>
            <a href="register.php" class="btn btn-outline">Register</a>
          <?php endif; ?>
        </div>
      </nav>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="hero">
    <div class="container">
      <div class="hero-content">
        <h1>Advanced Medical Store Management Solution</h1>
        <p>Streamline your pharmacy operations with our comprehensive management system designed for efficiency, accuracy, and growth.</p>
        <div class="hero-buttons">
          <a href="#features" class="btn">Explore Features</a>
          <a href="<?php echo $isLoggedIn ? 'dashboard.php' : 'register.php'; ?>" class="btn btn-outline">
            <?php echo $isLoggedIn ? 'Go to Dashboard' : 'Get Started'; ?>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features" id="features">
    <div class="container">
      <h2 class="section-title">Why Choose MediCare?</h2>
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-pills"></i>
          </div>
          <h3>Inventory Management</h3>
          <p>Efficiently track and manage your medical inventory with real-time updates and automated alerts.</p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-shopping-cart"></i>
          </div>
          <h3>Sales Tracking</h3>
          <p>Monitor sales performance, track best-selling products, and analyze revenue trends with detailed reports.</p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-users"></i>
          </div>
          <h3>Customer Management</h3>
          <p>Maintain detailed customer records, purchase history, and personalized service options.</p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-chart-line"></i>
          </div>
          <h3>Advanced Analytics</h3>
          <p>Gain valuable insights with comprehensive analytics and visual data representations.</p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-file-invoice"></i>
          </div>
          <h3>Billing & Invoicing</h3>
          <p>Generate professional invoices, manage payments, and track financial transactions seamlessly.</p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-mobile-alt"></i>
          </div>
          <h3>Mobile Access</h3>
          <p>Access your store management system from anywhere with our responsive mobile interface.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Services Section -->
  <section class="services">
    <div class="container">
      <h2 class="section-title">Our Services</h2>
      <div class="services-grid">
        <div class="service-card">
          <div class="service-img" style="background-image: url('https://images.unsplash.com/photo-1587854692152-cbe660dbde88?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80');"></div>
          <div class="service-content">
            <h3>Retail Pharmacy Management</h3>
            <p>Complete solution for retail pharmacies to manage inventory, sales, and customer relationships.</p>
          </div>
        </div>
        
        <div class="service-card">
          <div class="service-img" style="background-image: url('https://images.unsplash.com/photo-1576671414121-aa0c81c830fe?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80');"></div>
          <div class="service-content">
            <h3>Wholesale Distribution</h3>
            <p>Tools for medical suppliers and distributors to manage bulk orders and inventory.</p>
          </div>
        </div>
        
        <div class="service-card">
          <div class="service-img" style="background-image: url('https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80');"></div>
          <div class="service-content">
            <h3>Hospital Pharmacy Solutions</h3>
            <p>Specialized system for hospital pharmacies with additional features for patient management.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Stats Section -->
  <section class="stats">
    <div class="container">
      <div class="stats-grid">
        <div class="stat-item">
          <h3>500+</h3>
          <p>Stores Managed</p>
        </div>
        
        <div class="stat-item">
          <h3>50,000+</h3>
          <p>Products Tracked</p>
        </div>
        
        <div class="stat-item">
          <h3>1M+</h3>
          <p>Transactions Processed</p>
        </div>
        
        <div class="stat-item">
          <h3>99.9%</h3>
          <p>Uptime Reliability</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials -->
  <section class="testimonials">
    <div class="container">
      <h2 class="section-title">What Our Clients Say</h2>
      <div class="testimonial-grid">
        <div class="testimonial-card">
          <div class="testimonial-text">
            MediCare has transformed how we manage our pharmacy. The inventory management system alone has saved us 15 hours per week.
          </div>
          <div class="testimonial-author">
            <div class="author-avatar">RS</div>
            <div>
              <h4>Raj Sharma</h4>
              <p>Sharma Medical Store</p>
            </div>
          </div>
        </div>
        
        <div class="testimonial-card">
          <div class="testimonial-text">
            The analytics dashboard provides insights we never had before. We've increased sales by 22% since implementation.
          </div>
          <div class="testimonial-author">
            <div class="author-avatar">PK</div>
            <div>
              <h4>Priya Kumar</h4>
              <p>City Pharmacy</p>
            </div>
          </div>
        </div>
        
        <div class="testimonial-card">
          <div class="testimonial-text">
            Customer management features have helped us build better relationships and improve our service quality significantly.
          </div>
          <div class="testimonial-author">
            <div class="author-avatar">AM</div>
            <div>
              <h4>Amit Mishra</h4>
              <p>Mishra Medicines</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="cta">
    <div class="container">
      <h2>Ready to Transform Your Medical Store?</h2>
      <p>Join thousands of medical stores that have improved their efficiency, accuracy, and profitability with our system.</p>
      <a href="<?php echo $isLoggedIn ? 'dashboard.php' : 'register.php'; ?>" class="btn">
        <?php echo $isLoggedIn ? 'Go to Dashboard' : 'Get Started Today'; ?>
      </a>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer" id="contact">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-section">
          <h3>About MediCare</h3>
          <p>Advanced medical store management system designed to streamline operations, improve efficiency, and drive growth.</p>
          <div class="social-links">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>
        
        <div class="footer-section">
          <h3>Quick Links</h3>
          <ul class="footer-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="mean.php">Analytics</a></li>
            <li><a href="../pages/company/company_dashboard.php">For Companies</a></li>
            <li><a href="../pages/customer/customer_dashboard.php">For Customers</a></li>
          </ul>
        </div>
        
        <div class="footer-section">
          <h3>Contact Us</h3>
          <p><i class="fas fa-map-marker-alt"></i> 123 Medical Street, Healthcare City</p>
          <p><i class="fas fa-phone"></i> +91 9484485519</p>
          <p><i class="fas fa-envelope"></i> ommoradiya22@gmail.com</p>
        </div>
      </div>
      
      <div class="footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> MediCare. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <script>
    // Mobile menu toggle
    document.getElementById('mobile-menu').addEventListener('click', function() {
      document.querySelector('.nav-menu').classList.toggle('active');
      document.querySelector('.nav-actions').classList.toggle('active');
    });
  </script>
</body>
</html>