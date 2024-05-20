<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_i";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['company_id'])) {
    header("Location: ./index.php");
    exit();
}

// Fetch products from the inventory table
$sql = "SELECT item_name, quantity, rate FROM inventory";
$result = $conn->query($sql);

// Store product details in an array
$products = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Fetch company details
$company_id = $_SESSION['company_id'];
$sql = "SELECT * FROM company WHERE id = $company_id";
$company_result = $conn->query($sql);
$company = $company_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice Form</title>
  <style>
    body {
      font-family: 'Helvetica Neue', Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f5f5f5;
    }
    header {
      background-color: #4CAF50;
      color: #fff;
      padding: 20px;
      text-align: center;
      font-size: 24px;
      font-weight: bold;
      position: relative;
    }
    .home {
      position: absolute;
      top: 50%;
      right: 20px;
      transform: translateY(-50%);
      color: white;
      text-decoration: none;
      font-size: 1rem;
    }
    .container {
      margin: 20px auto;
      padding: 20px;
      max-width: 800px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    form {
      display: flex;
      flex-direction: column;
    }
    input, select {
      margin-bottom: 15px;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 16px;
    }
    button {
      padding: 10px 20px;
      background-color: #4CAF50;
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }
    button:hover {
      background-color: #45a049;
    }
    .product-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }
    .remove-product {
      background-color: #ff5555;
      border: none;
      border-radius: 5px;
      color: #fff;
      padding: 8px;
      cursor: pointer;
      font-size: 14px;
    }
    .remove-product:hover {
      background-color: #ff4444;
    }
  </style>
</head>
<body>
<header>
    <h1>Invoice Form</h1>
    <!-- <a href="home.php" class="home">Home</a> -->
</header>
<div class="container">
    <form id="invoiceForm" method="post" action="invoice.php" onsubmit="return validateForm()">
      <div>
        <label for="customerName">Customer Name:</label>
        <input type="text" name="customerName" id="customerName" required>
      </div>
      <div>
        <label for="customerLocation">Customer Location:</label>
        <input type="text" name="customerLocation" id="customerLocation" required>
      </div>
      <div>
        <label for="vatNumber">VAT Number:</label>
        <input type="text" name="vatNumber" id="vatNumber" pattern="\d{9}" title="VAT Number must be exactly 9 digits">
      </div>
      <div id="productFields">
        <div class="product-row">
          <div>
            <label for="productName">Product Name:</label>
            <select name="productName[]" required onchange="updateProductDetails(this)">
              <option value="">Select a product</option>
              <?php foreach ($products as $product) {
                  echo "<option value='{$product['item_name']}' data-rate='{$product['rate']}' data-quantity='{$product['quantity']}'>{$product['item_name']} (Stock: {$product['quantity']})</option>";
              } ?>
            </select>
          </div>
          <div>
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity[]" min="1" required>
          </div>
          <div>
            <label for="rate">Rate:</label>
            <input type="number" name="rate[]" step="0.01" required readonly>
          </div>
          <button type="button" class="remove-product" onclick="removeProduct(this)">Remove</button>
        </div>
      </div>
      <button type="button" onclick="addProduct()">Add Product</button>
      <button type="submit">Submit Invoice</button>
    </form>
</div>
<script>
  function addProduct() {
    const productFields = document.getElementById('productFields');
    const productRow = document.createElement('div');
    productRow.className = 'product-row';
    productRow.innerHTML = `
      <div>
        <label for="productName">Product Name:</label>
        <select name="productName[]" required onchange="updateProductDetails(this)">
          <option value="">Select a product</option>
          <?php foreach ($products as $product) {
              echo "<option value='{$product['item_name']}' data-rate='{$product['rate']}' data-quantity='{$product['quantity']}'>{$product['item_name']} (Stock: {$product['quantity']})</option>";
          } ?>
        </select>
      </div>
      <div>
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity[]" min="1" required>
      </div>
      <div>
        <label for="rate">Rate:</label>
        <input type="number" name="rate[]" step="0.01" required readonly>
      </div>
      <button type="button" class="remove-product" onclick="removeProduct(this)">Remove</button>
    `;
    productFields.appendChild(productRow);
  }

  function removeProduct(button) {
    const productRow = button.parentElement;
    productRow.remove();
  }

  function updateProductDetails(select) {
    const rateInput = select.closest('.product-row').querySelector('input[name="rate[]"]');
    const quantityInput = select.closest('.product-row').querySelector('input[name="quantity[]"]');
    const selectedOption = select.selectedOptions[0];
    rateInput.value = selectedOption.getAttribute('data-rate');
    quantityInput.max = selectedOption.getAttribute('data-quantity');
  }

  function validateForm() {
    const form = document.getElementById('invoiceForm');
    const productSelects = form.querySelectorAll('select[name="productName[]"]');
    for (const select of productSelects) {
      if (select.value === '') {
        alert('Please select a product for each row.');
        return false;
      }
    }
    return true;
  }
</script>
</body>
</html>
