<?php
include '../database/db_connect.php';

session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user with role
    $stmt = $conn->prepare("SELECT id, username, role, password FROM userdata WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $role, $hashedPassword);
        $stmt->fetch();

        // ✅ Verify password (if you are storing hashed password)
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_session'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            // Redirect by role
            if ($role == 'admin') {
                header("Location: dashboard.php");
            } elseif ($role == 'company') {
                header("Location: ../pages/company/company_dashboard.php");
            } elseif ($role == 'customer') {
                header("Location: ../pages/customer/customer_dashboard.php");
            } else {
                $message = "❌ Unknown role assigned!";
            }
            exit();
        } else {
            $message = "❌ Invalid users";
        }
    } else {
        $message = "❌ Invalid email or password!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Secure Login | Professional Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    .container {
      width: 100%;
      max-width: 450px;
      perspective: 1000px;
    }

    .register-form {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
      transform-style: preserve-3d;
      animation: formEntrance 0.8s ease-out;
      transition: transform 0.3s;
    }

    .register-form:hover {
      transform: translateZ(10px);
    }

    @keyframes formEntrance {
      0% {
        opacity: 0;
        transform: translateY(30px) rotateX(-10deg);
      }
      100% {
        opacity: 1;
        transform: translateY(0) rotateX(0);
      }
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #2c3e50;
      font-weight: 600;
      font-size: 28px;
    }

    h2 i {
      margin-right: 10px;
      color: #3498db;
    }

    .message {
      background: #ffdddd;
      color: #d63031;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 20px;
      text-align: center;
      font-size: 14px;
      border-left: 4px solid #d63031;
      animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-5px); }
      75% { transform: translateX(5px); }
    }

    .input-group {
      margin-bottom: 25px;
      position: relative;
    }

    .input-group input {
      width: 100%;
      padding: 15px 15px 15px 45px;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      font-size: 16px;
      transition: all 0.3s;
      outline: none;
    }

    .input-group input:focus {
      border-color: #3498db;
      box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
    }

    .input-group i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #7f8c8d;
      transition: color 0.3s;
    }

    .input-group input:focus + i {
      color: #3498db;
    }

    .btn {
      width: 100%;
      padding: 15px;
      background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      box-shadow: 0 4px 10px rgba(37, 117, 252, 0.3);
    }

    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(37, 117, 252, 0.4);
    }

    .btn:active {
      transform: translateY(0);
    }

    .login-link {
      text-align: center;
      margin-top: 25px;
      color: #7f8c8d;
      font-size: 14px;
    }

    .login-link a {
      color: #3498db;
      text-decoration: none;
      transition: color 0.3s;
      font-weight: 500;
    }

    .login-link a:hover {
      color: #2980b9;
      text-decoration: underline;
    }

    .divider {
      display: flex;
      align-items: center;
      margin: 25px 0;
    }

    .divider::before, .divider::after {
      content: "";
      flex: 1;
      height: 1px;
      background: #e0e0e0;
    }

    .divider span {
      padding: 0 15px;
      color: #7f8c8d;
      font-size: 14px;
    }

    .social-login {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 20px;
    }

    .social-btn {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 18px;
      transition: all 0.3s;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .social-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }

    .fb {
      background: #3b5998;
    }

    .google {
      background: #dd4b39;
    }

    .linkedin {
      background: #0077b5;
    }

    /* Responsive design */
    @media (max-width: 480px) {
      .register-form {
        padding: 25px;
      }
      
      h2 {
        font-size: 24px;
      }
      
      .input-group input {
        padding: 12px 12px 12px 40px;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <form method="POST" class="register-form">
    <h2><i class="fa fa-sign-in-alt"></i> Secure Login</h2>

        <?php if (isset($message) && !empty($message)): ?>
          <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>


    <div class="input-group">
      <input type="email" name="email" placeholder="Email" required>
      <i class="fa fa-envelope"></i>
    </div>

    <div class="input-group">
      <input type="password" name="password" placeholder="Password" required>
      <i class="fa fa-lock"></i>
    </div>

    <button type="submit" class="btn">Login to Account</button>

    <div class="divider"><span>Or</span></div>

    

    <p class="login-link"><a href="./resetpassword.php">Forgot Password?</a></p>

    <p class="login-link">Don't have an account? <a href="./register.php">Register Now</a></p>
  </form>
</div>

<script>
  // Add subtle input field animations
  document.querySelectorAll('input').forEach(input => {
    input.addEventListener('focus', (e) => {
      e.target.parentElement.style.transform = 'translateZ(15px)';
    });
    
    input.addEventListener('blur', (e) => {
      e.target.parentElement.style.transform = 'translateZ(0)';
    });
  });
</script>

</body>
</html>