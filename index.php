<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BusinessPortal - Streamline Your Business Operations</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #2ecc71;
            --warning: #f39c12;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--dark);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Styles */
        header {
            background: var(--primary);
            color: white;
            padding: 15px 0;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo i {
            font-size: 28px;
            color: var(--secondary);
        }

        .logo h1 {
            font-weight: 700;
            font-size: 24px;
        }

        .auth-buttons {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            display: inline-block;
            text-align: center;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--secondary);
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(41, 128, 185, 0.3);
        }

        .btn-outline {
            border: 2px solid var(--secondary);
            color: var(--secondary);
            background: transparent;
        }

        .btn-outline:hover {
            background: var(--secondary);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(41, 128, 185, 0.3);
        }

        /* Hero Section */
        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 100px 0;
            gap: 40px;
        }

        .hero-content {
            flex: 1;
        }

        .hero-content h2 {
            font-size: 48px;
            margin-bottom: 20px;
            color: var(--primary);
            line-height: 1.2;
        }

        .hero-content p {
            font-size: 20px;
            line-height: 1.6;
            margin-bottom: 30px;
            color: #555;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .hero-image {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .hero-image img {
            max-width: 100%;
            border-radius: 10px;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .hero-image img:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        /* Features Section */
        .features {
            padding: 80px 0;
            background: white;
            border-radius: 10px;
            margin: 40px 0;
            box-shadow: var(--shadow);
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 36px;
            color: var(--primary);
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            border-radius: 2px;
        }

        .section-title p {
            color: #777;
            max-width: 700px;
            margin: 0 auto;
            font-size: 18px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .feature-card {
            background: var(--light);
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
        }

        .feature-card i {
            font-size: 48px;
            color: var(--secondary);
            margin-bottom: 20px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .feature-card h3 {
            margin-bottom: 15px;
            color: var(--primary);
            font-size: 22px;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        /* CTA Section */
        .cta {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 80px 0;
            border-radius: 10px;
            margin: 40px 0;
            text-align: center;
        }

        .cta h2 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        .cta p {
            font-size: 20px;
            margin-bottom: 30px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn-light {
            background: white;
            color: var(--primary);
        }

        .btn-light:hover {
            background: #f8f9fa;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
        }

        /* Footer */
        footer {
            background: var(--primary);
            color: white;
            padding: 60px 0 20px;
            margin-top: auto;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .footer-column h3 {
            font-size: 20px;
            margin-bottom: 20px;
            color: var(--secondary);
            position: relative;
            padding-bottom: 10px;
        }

        .footer-column h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: var(--secondary);
        }

        .footer-column ul {
            list-style: none;
        }

        .footer-column ul li {
            margin-bottom: 10px;
        }

        .footer-column a {
            color: #ddd;
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-column a:hover {
            color: var(--secondary);
            padding-left: 5px;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transition: var(--transition);
        }

        .social-links a:hover {
            background: var(--secondary);
            transform: translateY(-3px);
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 14px;
            color: #aaa;
        }

        /* Responsive Design */
        @media (max-width: 900px) {
            .hero {
                flex-direction: column;
                text-align: center;
                padding: 60px 0;
            }
            
            .hero-buttons, .cta-buttons {
                justify-content: center;
            }
            
            .hero-content h2 {
                font-size: 36px;
            }
        }

        @media (max-width: 600px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            .auth-buttons {
                width: 100%;
                justify-content: center;
            }
            
            .hero-content h2 {
                font-size: 32px;
            }
            
            .hero-content p {
                font-size: 18px;
            }
            
            .hero-buttons, .cta-buttons {
                flex-direction: column;
            }
            
            .feature-card {
                padding: 20px;
            }
            
            .section-title h2 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-content">
            <div class="logo">
                <i class="fas fa-building"></i>
                <h1>BusinessPortal</h1>
            </div>
            <div class="auth-buttons">
                <a href="pages/login.php" class="btn btn-outline">Login</a>
                <a href="pages/register.php" class="btn btn-primary">Register</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h2>Streamline Your Business Operations</h2>
                <p>An all-in-one platform for companies to manage products, inventory, orders, customers, and analytics in one place.</p>
                <div class="hero-buttons">
                    <a href="register.php" class="btn btn-primary">Get Started</a>
                    <a href="#features" class="btn btn-outline">Learn More</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&h=400&q=80" alt="Business Dashboard">
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="features">
            <div class="section-title">
                <h2>Powerful Features</h2>
                <p>Everything you need to manage your business efficiently</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-box-open"></i>
                    <h3>Product Management</h3>
                    <p>Easily add, edit, and organize your product catalog with detailed information.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Inventory Tracking</h3>
                    <p>Keep track of your stock levels and receive alerts when items are running low.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Order Processing</h3>
                    <p>Manage customer orders, process payments, and track order status efficiently.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-users"></i>
                    <h3>Customer Management</h3>
                    <p>Maintain customer profiles, order history, and communication all in one place.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-file-invoice"></i>
                    <h3>Invoicing & Payments</h3>
                    <p>Create professional invoices, track payments, and manage your finances.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-chart-pie"></i>
                    <h3>Sales Analytics</h3>
                    <p>Gain insights into your business performance with detailed reports and analytics.</p>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta">
            <h2>Ready to Transform Your Business?</h2>
            <p>Join thousands of companies that use BusinessPortal to streamline their operations and boost productivity.</p>
            <div class="cta-buttons">
                <a href="register.php" class="btn btn-light">Get Started Now</a>
                <a href="login.php" class="btn btn-outline">Login to Existing Account</a>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>BusinessPortal</h3>
                    <p>Comprehensive business management solution for companies of all sizes.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Support</h3>
                    <ul>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">FAQs</a></li>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact Us</h3>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> surat</li>
                        <li><i class="fas fa-phone"></i> +91 9484485519</li>
                        <li><i class="fas fa-envelope"></i> ommoradiya22@gmail.com</li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2023 BusinessPortal. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Add animation on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const featureCards = document.querySelectorAll('.feature-card');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });
            
            featureCards.forEach(card => {
                card.style.opacity = 0;
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(card);
            });
        });
    </script>
</body>
</html>