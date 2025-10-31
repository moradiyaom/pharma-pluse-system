<?php
session_start();
include("../../database/db_connect.php");

// ✅ Check login
if (!isset($_SESSION['user_session'])) {
    header("location: ../login.php");
    exit;
}

// Username for display
$username = $_SESSION['user_session'];
$role     = isset($_SESSION['role']) ? $_SESSION['role'] : "User";

// ✅ Load cart
$cart = $_SESSION['cart'] ?? [];

// ✅ Handle Checkout
if (isset($_POST['checkout']) && !empty($cart)) {
    foreach ($cart as $item) {
        $barcode  = $item['barcode'] ?? '';
        $medicine = $item['name'] ?? '';
        $qty      = intval($item['qty'] ?? 0);
        $price    = floatval($item['price'] ?? 0);
        $total    = $qty * $price;

        if ($barcode && $qty > 0) {
            // Update inventory
            $conn->query("UPDATE inventory 
                          SET sold_qty = sold_qty + $qty, 
                              remain_qty = remain_qty - $qty 
                          WHERE code='$barcode'");

            // Insert into sales
            $stmt = $conn->prepare("INSERT INTO sales (barcode, medicine, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssidd", $barcode, $medicine, $qty, $price, $total);
            $stmt->execute();
        }
    }

    // ✅ Clear cart after checkout
    unset($_SESSION['cart']);
    $success = "✅ Payment successful! Sales recorded.";
}



// ✅ Set username & role from session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : "Guest";
$role = isset($_SESSION['role']) ? $_SESSION['role'] : "User";

// ✅ Fetch inventory for dropdown/JS DB
$inventory = [];
$result = $conn->query("SELECT * FROM inventory WHERE status='Available'");
while($row = $result->fetch_assoc()){
    $inventory[] = $row;
}

// ✅ Handle Sale (AJAX)
if (isset($_POST['sell_item'])) {
    $barcode = $_POST['barcode'];
    $medicine = $_POST['medicine'];
    $qty = intval($_POST['quantity']);
    $price = floatval($_POST['price']);
    $total = $qty * $price;

    // Update inventory (sold_qty, remain_qty)
    $conn->query("UPDATE inventory 
                  SET sold_qty = sold_qty + $qty, 
                      remain_qty = registered_qty - (sold_qty + $qty) 
                  WHERE code='$barcode'");

    // Insert into sales report
    $stmt = $conn->prepare("INSERT INTO sales (barcode, medicine, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssidd", $barcode, $medicine, $qty, $price, $total);
    $stmt->execute();

    echo json_encode(["success"=>true]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Selling Page - Cart & Checkout</title>
  <link rel="stylesheet" href="../../css/selling.css">
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    table, th, td { border: 1px solid #ddd; }
    th, td { padding: 10px; text-align: center; }
    th { background: #f4f4f4; }
    .total { font-weight: bold; font-size: 18px; text-align: right; }
    .btn { padding: 8px 16px; border: none; cursor: pointer; border-radius: 5px; }
    .btn-checkout { background: #28a745; color: white; }
    .btn-remove { background: #dc3545; color: white; }
    .msg { padding:10px; background:#d4edda; color:#155724; border:1px solid #c3e6cb; margin-bottom:15px; }
  </style>
</head>
<body>











<!-- ✅ Navigation Bar -->
<header>
<nav class="navbar">
        <a href="#" class="logo">
            <img src="../../images/i1.jpeg" alt="Medical Store Logo">
        </a>
    
    <div class="nav-right">
        <a href="customer_dashboard.php">Dashboard</a>
        <a href="customer_inventory.php">Inventory</a>
    </div>
    <div style="margin-right: 20px;">
        Logged in as: <b><?php echo ucfirst($username); ?></b> (<?php echo ucfirst($role); ?>)
    </div>
    <a href="../../pages/logout.php" class="logout-btn">Logout</a>
</nav>
</header>

<div class="container">
  <h2>💊 Medicine Selling Page</h2>
  <p id="datetime"></p>

  <form id="sellingForm">
    <label>Barcode:</label>
    <input type="text" id="barcode" placeholder="Enter Barcode..." onkeyup="fetchMedicine()">

    <label>Medicine:</label>
    <input type="text" id="medicine" placeholder="Auto-filled" readonly>

    <label>Quantity:</label>
    <input type="number" id="quantity" value="1" min="1">

    <label>Price ($):</label>
    <input type="number" id="price" readonly>

    <button type="button" onclick="addToBill()">Add to Bill</button>
  </form>

  <hr>
  
  <h3>🧾 Bill Preview</h3>
  <table id="billTable">
    <thead>
      <tr>
        <th>Medicine</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

  <h3 id="grandTotal">Grand Total: $0</h3>


  <button onclick="payBill()">💳 Pay</button>
  <button onclick="downloadBill()">⬇️ Download Bill</button>
</div>

<script>
// ✅ Load inventory from PHP
let inventory = <?php echo json_encode($inventory); ?>;
let bill = [];

function fetchMedicine(){
  let barcode = document.getElementById("barcode").value;
  let item = inventory.find(i => i.code === barcode);
  if(item){
    document.getElementById("medicine").value = item.medicine;
    document.getElementById("price").value = item.selling_price;
  }
}

function addToBill(){
  let barcode = document.getElementById("barcode").value;
  let med = document.getElementById("medicine").value;
  let qty = parseInt(document.getElementById("quantity").value);
  let price = parseFloat(document.getElementById("price").value);

  if(!barcode || !med || qty <= 0) return alert("Enter valid details!");

  let total = qty * price;
  bill.push({barcode, med, qty, price, total});
  updateTable();
}

function updateTable(){
  let tbody = document.querySelector("#billTable tbody");
  tbody.innerHTML = "";
  let gTotal = 0;
  
  bill.forEach(item=>{
    tbody.innerHTML += `<tr>
      <td>${item.med}</td>
      <td>${item.qty}</td>
      <td>$${item.price}</td>
      <td>$${item.total}</td>
    </tr>`;
    gTotal += item.total;
  });
  document.getElementById("grandTotal").innerText = "Grand Total: $" + gTotal;
}

// ✅ Pay Bill (save to DB, update inventory)
function payBill(){
  if(bill.length === 0) return alert("Add items first!");

  bill.forEach(item=>{
    fetch("selling.php", {
      method:"POST",
      headers:{"Content-Type":"application/x-www-form-urlencoded"},
      body:`sell_item=1&barcode=${item.barcode}&medicine=${item.med}&quantity=${item.qty}&price=${item.price}`
    });
  });

  alert("✅ Payment successful! Sales recorded.");
  bill = [];
  updateTable();
}

// ✅ Download PDF (only bill generation, no DB update)
function downloadBill(){
  if(bill.length === 0) return alert("Add items first!");

  const { jsPDF } = window.jspdf;
  let doc = new jsPDF();
  doc.setFontSize(18);
  doc.text("Pharmacy Bill", 70, 15);

  const now = new Date();
  doc.setFontSize(12);
  doc.text(`Date: ${now.toLocaleString()}`, 14, 25);

  let startY = 40;
  doc.text("Medicine", 14, startY);
  doc.text("Qty", 84, startY);
  doc.text("Price", 124, startY);
  doc.text("Total", 164, startY);

  let y = startY + 10;
  let gTotal = 0;
  bill.forEach(item=>{
    doc.text(item.med, 14, y);
    doc.text(item.qty.toString(), 94, y);
    doc.text("$" + item.price, 124, y);
    doc.text("$" + item.total, 164, y);
    gTotal += item.total;
    y += 10;
  });

  doc.setFontSize(14);
  doc.text(`Grand Total: $${gTotal}`, 124, y + 10);

  doc.save("pharmacy_bill.pdf");
}

// ✅ Show Time
setInterval(()=>{
  document.getElementById("datetime").innerText = "🕒 " + new Date().toLocaleString();
},1000);
</script>












</body>
</html>
