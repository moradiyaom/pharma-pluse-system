<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Logout</title>
  <link rel="stylesheet" href="../css/logoutpg.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <!-- Redirect after 3 seconds -->
  <meta http-equiv="refresh" content="5;url=./login.php">
</head>
<body>

<div class="container">
  <div class="logout-box">
    <i class="fa fa-sign-out-alt fa-4x"></i>
    <h2>You have logged out</h2>
    <p>Redirecting to <a href="./login.php">Login Page</a> in 5 seconds...</p>
  </div>
</div>

</body>
</html>
