<?php
session_start();
include("../../database/db_connect.php"); // adjust path

// ✅ Get product ID from URL (e.g., details.php?id=1)
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT * FROM products WHERE id = $product_id";
$result = $conn->query($sql);
$product = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $product['name'] ?? "Product"; ?></title>
  <link rel="stylesheet" href="../../css/all_pr.css">
  <link rel="stylesheet" href="../../css/all_med.css">
  <style>
    .product-page {
      display: flex;
      gap: 30px;
      margin-top: 30px;
    }

    .left-side {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .product-img {
      position: relative;
      width: 350px;
    }

    #mainImage {
      width: 100%;
      border: 1px solid #ccc;
      cursor: crosshair;
    }

    .thumbs {
      margin-top: 10px;
      display: flex;
      gap: 10px;
    }

    .thumbs img {
      width: 60px;
      cursor: pointer;
      border: 1px solid #ddd;
      padding: 3px;
      transition: 0.3s;
    }

    .thumbs img:hover {
      border-color: #333;
    }

    /* Zoom Lens */
    .zoom-lens {
      position: absolute;
      border: 1px solid #000;
      width: 100px;
      height: 100px;
      opacity: 0.4;
      background: #fff;
      display: none;
      pointer-events: none;
    }

    /* Zoom Result */
    .zoom-result {
      border: 1px solid #ccc;
      width: 400px;
      height: 400px;
      background-repeat: no-repeat;
      display: none;
    }

    .product-info {
      flex: 1;
    }

    .price {
      font-size: 18px;
      font-weight: bold;
      margin: 10px 0;
    }

    .mrp {
      text-decoration: line-through;
      color: gray;
      margin-left: 8px;
    }

    .discount {
      color: green;
      margin-left: 8px;
    }
  </style>
</head>
<body>
  <!-- Fixed Header -->
  <header>
    <nav class="navbar">
      <a href="#" class="logo">
        <img src="../../images/i1.jpeg" alt="Medical Store Logo">
      </a>
      <div class="nav-right">
        <a href="../customer/customer_dashboard.php">Dashboard</a>
        <a href="../medicine/medicine.php">Medicines</a>
        <a href="../soap/soap.php">Soap</a>
        <a href="../loction/loction.php">Location</a>
        <a href="../baby_food/baby_food.php">Baby Food</a>
        <a href="../food_drink/food_drink.php">Food & Drink</a>
      </div>
      <a href="../logout.php" class="logout-btn">Logout</a>
    </nav>
  </header>

  <div class="container">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
      <a href="../customer/customer_dashboard.php">Home</a> › 
      <a href="../<?php echo strtolower($product['category']); ?>/<?php echo strtolower($product['category']); ?>.php">
        <?php echo ucfirst($product['category']); ?>
      </a> › 
      <?php echo $product['name']; ?>
    </div>

    <?php if ($product): ?>
      <div class="product-page">
        <!-- Left Side: Main Image + Thumbnails -->
        <div class="left-side">
          <div class="product-img">
            <img id="mainImage" src="../../uploads/<?php echo $product['image1']; ?>" alt="<?php echo $product['name']; ?>">
            <div id="lens" class="zoom-lens"></div>
          </div>

          <!-- Thumbnails below main image -->
          <div class="thumbs">
            <?php if (!empty($product['image1'])): ?>
              <img src="../../uploads/<?php echo $product['image1']; ?>" alt="Image 1" onclick="changeImage(this.src)">
            <?php endif; ?>
            <?php if (!empty($product['image2'])): ?>
              <img src="../../uploads/<?php echo $product['image2']; ?>" alt="Image 2" onclick="changeImage(this.src)">
            <?php endif; ?>
          </div>
        </div>

        <!-- Right Side: Zoom + Product Info -->
        <div style="flex:1; display:flex; flex-direction:column; gap:20px;">
          <div id="result" class="zoom-result"></div>
          <div class="product-info">
            <h2><?php echo $product['name']; ?></h2>
            <p>Barcode: <?php echo $product['barcode']; ?></p>
            <p>Category: <?php echo ucfirst($product['category']); ?></p>
            <p class="price">
              ₹<?php echo $product['selling_price']; ?> 
              <span class="mrp">₹<?php echo $product['actual_price']; ?></span>
              <span class="discount">
                <?php echo round((($product['actual_price'] - $product['selling_price']) / $product['actual_price']) * 100); ?>% OFF
              </span>
            </p>
            <p style="color:gray;">Inclusive of all taxes</p>

            <!-- ✅ Add to Cart Form -->
            <form action="../cart/add_to_cart.php" method="POST">
              <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
              <button type="submit" class="btn">Add To Cart</button>
            </form>
          </div>
        </div>
      </div>
    <?php else: ?>
      <p>⚠ Product not found.</p>
    <?php endif; ?>
  </div>

  <script>
    function changeImage(src) {
      const mainImage = document.getElementById("mainImage");
      const result = document.getElementById("result");
      mainImage.src = src;
      result.style.backgroundImage = "url('" + src + "')";
    }

    // ✅ Image Zoom Effect
    const mainImage = document.getElementById("mainImage");
    const lens = document.getElementById("lens");
    const result = document.getElementById("result");

    if (mainImage) {
      result.style.backgroundImage = "url('" + mainImage.src + "')";
      result.style.backgroundSize = (mainImage.width * 2) + "px " + (mainImage.height * 2) + "px";

      mainImage.addEventListener("mouseenter", () => {
        lens.style.display = "block";
        result.style.display = "block";
      });

      mainImage.addEventListener("mouseleave", () => {
        lens.style.display = "none";
        result.style.display = "none";
      });

      mainImage.addEventListener("mousemove", moveLens);
      lens.addEventListener("mousemove", moveLens);

      function moveLens(e) {
        e.preventDefault();
        const pos = getCursorPos(e);
        let x = pos.x - lens.offsetWidth / 2;
        let y = pos.y - lens.offsetHeight / 2;

        if (x > mainImage.width - lens.offsetWidth) x = mainImage.width - lens.offsetWidth;
        if (x < 0) x = 0;
        if (y > mainImage.height - lens.offsetHeight) y = mainImage.height - lens.offsetHeight;
        if (y < 0) y = 0;

        lens.style.left = x + "px";
        lens.style.top = y + "px";

        result.style.backgroundPosition = "-" + (x * 2) + "px -" + (y * 2) + "px";
      }

      function getCursorPos(e) {
        const rect = mainImage.getBoundingClientRect();
        const x = e.pageX - rect.left - window.pageXOffset;
        const y = e.pageY - rect.top - window.pageYOffset;
        return {x: x, y: y};
      }
    }
  </script>
</body>
</html>
<?php $conn->close(); ?>
