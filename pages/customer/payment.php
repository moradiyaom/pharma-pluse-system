<?php
session_start();
if (!isset($_SESSION['user_session'])) {
    header("location:../login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header("location:inventory.php");
    exit;
}

// Calculate total amount
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['qty'];
}

// Store order details in session for later use
$_SESSION['order_total'] = $total;

// Check if payment was successful and generate bill
$payment_success = isset($_GET['payment']) && $_GET['payment'] === 'success';
$payment_method = $_GET['method'] ?? '';

if ($payment_success) {
    // Generate order details
    $order_id = 'ORD' . time() . rand(100, 999);
    $_SESSION['last_order'] = [
        'order_id' => $order_id,
        'total' => $total,
        'items' => $cart,
        'payment_method' => $payment_method,
        'payment_id' => $_GET['payment_id'] ?? '',
        'date' => date('Y-m-d H:i:s')
    ];

    // ✅ Insert into database
    $conn = new mysqli("localhost", "root", "", "users"); // change DB creds if needed
    if ($conn->connect_error) {
        die("DB Connection failed: " . $conn->connect_error);
    }

    foreach ($cart as $item) {
        $barcode = $conn->real_escape_string($item['barcode'] ?? '');
        $medicine = $conn->real_escape_string($item['name']);
        $category = $conn->real_escape_string($item['category'] ?? '');
        $qty = (int)$item['qty'];
        $price = (float)$item['price'];
        $totalPrice = $price * $qty;
        $method = $conn->real_escape_string($payment_method);

        $sql = "INSERT INTO sales (barcode, medicine, category, quantity, price, total, payment_method, sold_date)
                VALUES ('$barcode', '$medicine', '$category', '$qty', '$price', '$totalPrice', '$method', NOW())";

        if (!$conn->query($sql)) {
            echo "Error inserting into sales: " . $conn->error;
        }
    }

    $conn->close();

    // Clear cart after saving
    unset($_SESSION['cart']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Secure Payment | Checkout</title>
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <style>
    :root {
      --primary: #4a6cf7;
      --success: #28a745;
      --danger: #dc3545;
      --dark: #343a40;
      --light: #f8f9fa;
      --border: #dee2e6;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f5f7fb;
      color: #333;
      line-height: 1.6;
    }
    
    .container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 20px;
    }
    
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      padding-bottom: 15px;
      border-bottom: 1px solid var(--border);
    }
    
    .logo {
      font-size: 24px;
      font-weight: 700;
      color: var(--primary);
    }
    
    .secure-notice {
      display: flex;
      align-items: center;
      color: var(--success);
      font-weight: 500;
    }
    
    .secure-notice i {
      margin-right: 8px;
    }
    
    .checkout-container {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
    }
    
    @media (max-width: 768px) {
      .checkout-container {
        grid-template-columns: 1fr;
      }
    }
    
    .order-summary {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      padding: 25px;
    }
    
    .order-summary h2 {
      margin-bottom: 20px;
      color: var(--dark);
      font-size: 22px;
    }
    
    .order-items {
      margin-bottom: 20px;
    }
    
    .order-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 12px;
      padding-bottom: 12px;
      border-bottom: 1px solid var(--border);
    }
    
    .order-total {
      display: flex;
      justify-content: space-between;
      font-size: 18px;
      font-weight: 700;
      margin-top: 20px;
      padding-top: 15px;
      border-top: 2px solid var(--border);
    }
    
    .payment-methods {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      padding: 25px;
    }
    
    .payment-methods h2 {
      margin-bottom: 20px;
      color: var(--dark);
      font-size: 22px;
    }
    
    .payment-option {
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
      transition: all 0.3s ease;
    }
    
    .payment-option:hover {
      border-color: var(--primary);
      box-shadow: 0 5px 15px rgba(74, 108, 247, 0.1);
    }
    
    .payment-option h3 {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
    }
    
    .payment-option h3 i {
      margin-right: 10px;
      font-size: 20px;
    }
    
    .payment-details {
      margin-top: 15px;
    }
    
    .qr-code {
      text-align: center;
      margin: 15px 0;
    }
    
    .qr-code img {
      max-width: 180px;
      border: 1px solid var(--border);
      padding: 10px;
      border-radius: 8px;
    }
    
    .btn {
      display: inline-block;
      padding: 12px 25px;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-align: center;
    }
    
    .btn:hover {
      opacity: 0.9;
      transform: translateY(-2px);
    }
    
    .btn-success {
      background: var(--success);
    }
    
    .btn-razorpay {
      background: #2d86ff;
    }
    
    .btn-danger {
      background: var(--danger);
    }
    
    .btn-light {
      background: var(--light);
      color: var(--dark);
      border: 1px solid var(--border);
    }
    
    .payment-steps {
      background: #f8f9fa;
      border-radius: 8px;
      padding: 15px;
      margin-top: 15px;
      display: none;
    }
    
    .payment-steps ol {
      margin-left: 20px;
    }
    
    .payment-steps li {
      margin-bottom: 8px;
    }
    
    .success-message {
      display: <?php echo $payment_success ? 'block' : 'none'; ?>;
      background: #d4edda;
      color: #155724;
      padding: 20px;
      border-radius: 8px;
      margin-top: 20px;
      text-align: center;
    }
    
    .success-message i {
      font-size: 40px;
      margin-bottom: 15px;
      display: block;
    }
    
    .payment-form {
      margin-top: 15px;
    }
    
    .form-group {
      margin-bottom: 15px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: 500;
    }
    
    .form-group input {
      width: 100%;
      padding: 12px;
      border: 1px solid var(--border);
      border-radius: 6px;
      font-size: 16px;
    }
    
    .razorpay-payment-button {
      display: none;
    }
    
    .processing {
      display: none;
      text-align: center;
      margin: 15px 0;
    }
    
    .processing i {
      font-size: 40px;
      color: var(--primary);
      margin-bottom: 10px;
    }
    
    .bill-container {
      display: <?php echo $payment_success ? 'block' : 'none'; ?>;
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      padding: 25px;
      margin-top: 30px;
    }
    
    .bill-header {
      text-align: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 2px solid var(--primary);
    }
    
    .bill-details {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 20px;
    }
    
    .bill-items {
      margin-bottom: 20px;
    }
    
    .bill-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
      padding-bottom: 10px;
      border-bottom: 1px solid var(--border);
    }
    
    .bill-total {
      display: flex;
      justify-content: space-between;
      font-size: 18px;
      font-weight: 700;
      margin-top: 20px;
      padding-top: 15px;
      border-top: 2px solid var(--border);
    }
    
    .bill-actions {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 25px;
    }
    
    .bill-actions .btn {
      width: auto;
      min-width: 150px;
    }
  </style>
</head>
<body>
  <div class="container">
    <header>
      <div class="logo">ShopEasy</div>
      <div class="secure-notice">
        <i class="fas fa-lock"></i>
        <span>Secure Payment Gateway</span>
      </div>
    </header>
    
    <?php if ($payment_success): ?>
    <!-- Success Message -->
    <div class="success-message">
      <i class="fas fa-check-circle"></i>
      <h3>Payment Successful!</h3>
      <p>Your <?php echo $payment_method; ?> transaction has been completed successfully.</p>
    </div>
    
    <!-- Bill Section -->
    <div class="bill-container" id="bill">
      <div class="bill-header">
        <h2><i class="fas fa-receipt"></i> Order Invoice</h2>
        <p>Order ID: <?php echo $_SESSION['last_order']['order_id']; ?></p>
        <p>Date: <?php echo $_SESSION['last_order']['date']; ?></p>
      </div>
      
      <div class="bill-details">
        <div>
          <h3>Billing Details</h3>
          <p><strong>Payment Method:</strong> <?php echo $_SESSION['last_order']['payment_method']; ?></p>
          <p><strong>Payment ID:</strong> <?php echo $_SESSION['last_order']['payment_id'] ?: 'N/A'; ?></p>
        </div>
        <div>
          <h3>Order Summary</h3>
          <p><strong>Items:</strong> <?php echo count($_SESSION['last_order']['items']); ?></p>
          <p><strong>Status:</strong> Paid</p>
        </div>
      </div>
      
      <div class="bill-items">
        <h3>Order Items</h3>
        <?php foreach ($_SESSION['last_order']['items'] as $item): ?>
        <div class="bill-item">
          <div><?php echo $item['name']; ?> x <?php echo $item['qty']; ?></div>
          <div>₹<?php echo number_format($item['price'] * $item['qty'], 2); ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      
      <div class="bill-total">
        <div>Total Amount:</div>
        <div>₹<?php echo number_format($_SESSION['last_order']['total'], 2); ?></div>
      </div>
      
      <div class="bill-actions">
        <button class="btn btn-success" id="download-bill">
          <i class="fas fa-download"></i> Download Bill
        </button>
        <a href="customer_inventory.php" class="btn btn-primary">
          <i class="fas fa-shopping-cart"></i> Continue Shopping
        </a>
        <button class="btn btn-light" id="print-bill">
          <i class="fas fa-print"></i> Print Bill
        </button>
      </div>
    </div>
    <?php else: ?>
    
    <!-- Payment Methods (Original Content) -->
    <div class="checkout-container">
      <div class="order-summary">
        <h2><i class="fas fa-receipt"></i> Order Summary</h2>
        
        <div class="order-items">
          <?php foreach ($cart as $item): ?>
          <div class="order-item">
            <div><?php echo $item['name']; ?> x <?php echo $item['qty']; ?></div>
            <div>₹<?php echo number_format($item['price'] * $item['qty'], 2); ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        
        <div class="order-total">
          <div>Total Amount:</div>
          <div>₹<?php echo number_format($total, 2); ?></div>
        </div>
      </div>
      
      <div class="payment-methods">
        <h2><i class="fas fa-credit-card"></i> Payment Methods</h2>
        
        <div class="payment-option">
          <h3><i class="fas fa-money-bill-wave"></i> Cash on Delivery</h3>
          <p>Pay with cash when your order is delivered.</p>
          <form method="post" action="process_payment.php" class="payment-form">
            <input type="hidden" name="method" value="COD">
            <button type="submit" class="btn btn-success">Pay on Delivery</button>
          </form>
        </div>
        
        <div class="payment-option">
          <h3><i class="fab fa-google-pay"></i> Google Pay / UPI</h3>
          <p>Scan the QR code or use our UPI ID to pay instantly.</p>
          
          <div class="qr-code">
            <img src="../../images/qr-code.jpg" alt="UPI QR Code">
            <p>UPI ID: <strong>ommoradiya22@ohkhdfcbank</strong></p>
          </div>
          
          <form method="post" action="process_payment.php" class="payment-form" id="gpay-form">
            <input type="hidden" name="method" value="GPay">
            <div class="form-group">
              <label for="transaction-id">Transaction ID (Optional)</label>
              <input type="text" id="transaction-id" name="transaction_id" placeholder="Enter UPI transaction ID">
            </div>
            <button type="submit" class="btn">Confirm Payment</button>
          </form>
        </div>
        
        
      </div>
    </div>
    <?php endif; ?>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      <?php if (!$payment_success): ?>
      // Show RazorPay steps
      document.getElementById('show-razorpay-steps').addEventListener('click', function() {
        const steps = document.getElementById('razorpay-steps');
        steps.style.display = steps.style.display === 'none' ? 'block' : 'none';
        this.textContent = steps.style.display === 'none' ? 'Show Payment Steps' : 'Hide Payment Steps';
      });
      
      // Form submission handlers
      document.getElementById('gpay-form').addEventListener('submit', function(e) {
        e.preventDefault();
        this.submit();
      });
      
      // RazorPay payment handler
      document.getElementById('razorpay-button').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Show processing message
        document.getElementById('razorpay-processing').style.display = 'block';
        this.style.display = 'none';
        
        // Create order via AJAX
        fetch('create_razorpay_order.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ amount: <?php echo $total * 100; ?> }) // Convert to paise
        })
        .then(response => response.json())
        .then(order => {
          // RazorPay options
          var options = {
            "key": "rzp_test_RLJMjJZTQtLlPt",
            "amount": order.amount, 
            "currency": "INR",
            "name": "PAYMENT",
            "description": "Order Payment",
            "image": "https://example.com/your_logo.jpg",
            "order_id": order.id,
            "handler": function (response){
              // Handle successful payment
              window.location.href = 'process_payment.php?method=RazorPay&status=success&payment_id=' + response.razorpay_payment_id;
            }
          };
          
          var rzp1 = new Razorpay(options);
          rzp1.open();
        })
        .catch(error => {
          console.error('Error:', error);
          document.getElementById('razorpay-processing').style.display = 'none';
          document.getElementById('razorpay-button').style.display = 'block';
          alert('An error occurred while creating the order. Please try again.');
        });
      });
      <?php else: ?>
      // Bill download functionality
      document.getElementById('download-bill').addEventListener('click', function() {
        const element = document.getElementById('bill');
        const options = {
          margin: 10,
          filename: 'invoice_<?php echo $_SESSION['last_order']['order_id']; ?>.pdf',
          image: { type: 'jpeg', quality: 0.98 },
          html2canvas: { scale: 2 },
          jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        
        html2pdf().set(options).from(element).save();
      });
      
      // Bill print functionality
      document.getElementById('print-bill').addEventListener('click', function() {
        window.print();
      });
      <?php endif; ?>
    });
  </script>

   <?php if ($payment_success): ?>
    <!-- Success Message -->
    <div class="success-message">
      <i class="fas fa-check-circle"></i>
      <h3>Payment Successful!</h3>
      <p>Your <?php echo $payment_method; ?> transaction has been completed successfully.</p>
    </div>
    
    <!-- Bill Section -->
    <div class="bill-container" id="bill">
      <div class="bill-header">
        <h2><i class="fas fa-receipt"></i> Order Invoice</h2>
        <p>Order ID: <?php echo $_SESSION['last_order']['order_id']; ?></p>
        <p>Date: <?php echo $_SESSION['last_order']['date']; ?></p>
      </div>
      
      <div class="bill-details">
        <div>
          <h3>Billing Details</h3>
          <p><strong>Payment Method:</strong> <?php echo $_SESSION['last_order']['payment_method']; ?></p>
          <p><strong>Payment ID:</strong> <?php echo $_SESSION['last_order']['payment_id'] ?: 'N/A'; ?></p>
        </div>
        <div>
          <h3>Order Summary</h3>
          <p><strong>Items:</strong> <?php echo count($_SESSION['last_order']['items']); ?></p>
          <p><strong>Status:</strong> Paid</p>
        </div>
      </div>
      
      <div class="bill-items">
        <h3>Order Items</h3>
        <?php foreach ($_SESSION['last_order']['items'] as $item): ?>
        <div class="bill-item">
          <div><?php echo $item['name']; ?> x <?php echo $item['qty']; ?></div>
          <div>₹<?php echo number_format($item['price'] * $item['qty'], 2); ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      
      <div class="bill-total">
        <div>Total Amount:</div>
        <div>₹<?php echo number_format($_SESSION['last_order']['total'], 2); ?></div>
      </div>
      
      <div class="bill-actions">
        <button class="btn btn-success" id="download-bill">
          <i class="fas fa-download"></i> Download Bill
        </button>
        <a href="customer_inventory.php" class="btn btn-primary">
          <i class="fas fa-shopping-cart"></i> Continue Shopping
        </a>
        <button class="btn btn-light" id="print-bill">
          <i class="fas fa-print"></i> Print Bill
        </button>
      </div>
    </div>
    <?php else: ?>
    <!-- Payment methods (keep your original code) -->
    <?php endif; ?>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      <?php if ($payment_success): ?>
      // Bill download functionality
      document.getElementById('download-bill').addEventListener('click', function() {
        const element = document.getElementById('bill');
        const options = {
          margin: 10,
          filename: 'invoice_<?php echo $_SESSION['last_order']['order_id']; ?>.pdf',
          image: { type: 'jpeg', quality: 0.98 },
          html2canvas: { scale: 2 },
          jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(options).from(element).save();
      });
      
      // Bill print functionality
      document.getElementById('print-bill').addEventListener('click', function() {
        window.print();
      });
      <?php endif; ?>
    });
  </script>
</body>
</html>