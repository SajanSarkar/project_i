<?php
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
if (!array_key_exists("user_id", $_SESSION) || !array_key_exists("company_id", $_SESSION)) {
    header("Location: ./index.php");
    exit();
}

// Get the submitted form data
$customerName = $_POST['customerName'];
$customerLocation = $_POST['customerLocation'];
$vatNumber = $_POST['vatNumber'];
$companyName = $_POST['companyName'];
$companyAddress = $_POST['companyAddress'];
$companyEmail = $_POST['companyEmail'];
$companyPhone = $_POST['companyPhone'];
$productNames = $_POST['productName'];
$quantities = $_POST['quantity'];
$rates = $_POST['rate'];
$discountRate = $_POST['discountRate'] ?? 0; // Discount rate in percentage

// Generate the invoice data
$invoiceItems = [];
for ($i = 0; $i < count($productNames); $i++) {
    $invoiceItems[] = [
        'productName' => $productNames[$i],
        'quantity' => $quantities[$i],
        'rate' => $rates[$i],
        'total' => $quantities[$i] * $rates[$i]
    ];
}

// Calculate the total amount and discount
$totalAmount = 0;
foreach ($invoiceItems as $item) {
    $totalAmount += $item['total'];
}

$discountAmount = ($totalAmount * $discountRate) / 100;
$amountAfterDiscount = $totalAmount - $discountAmount;

// Calculate VAT
$vatPercentage = 13; // Assume VAT is 13%
$vatAmount = ($amountAfterDiscount * $vatPercentage) / 100;

// Final total
$finalTotal = $amountAfterDiscount + $vatAmount;

// Update the inventory quantities
foreach ($invoiceItems as $item) {
    $productName = $item['productName'];
    $quantity = $item['quantity'];

    // Fetch the current quantity from the inventory
    $sql = "SELECT quantity FROM inventory WHERE item_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $productName);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $currentQuantity = $row['quantity'];

    // Calculate the new quantity
    $newQuantity = $currentQuantity - $quantity;

    // Update the inventory
    $sql = "UPDATE inventory SET quantity = ? WHERE item_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $newQuantity, $productName);
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .container {
            margin: 20px auto;
            padding: 20px;
            max-width: 800px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
        }

        .details-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .company-details, .customer-details {
            width: 45%;
        }

        .company-details h2, .customer-details h2 {
            font-size: 20px;
            color: #4CAF50;
            margin-bottom: 10px;
        }

        .invoice-table, .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .invoice-table th, .invoice-table td, .summary-table th, .summary-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .invoice-table th {
            background-color: #4CAF50;
            color: #fff;
        }

        .invoice-table td {
            background-color: #f9f9f9;
        }

        .summary-table th {
            background-color: #f1f1f1;
            text-align: right;
        }

        .summary-table td {
            background-color: #fff;
            text-align: right;
        }

        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .print-button, .home-button {
            display: inline-block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            text-align: center;
        }

        .print-button:hover, .home-button:hover {
            background-color: #45a049;
        }

        @media print {
            .print-button, .home-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Invoice</h1>
        <div class="details-container">
            <div class="company-details">
                <h2>Company Details</h2>
                <p>Name: <?php echo htmlspecialchars($companyName); ?></p>
                <p>Address: <?php echo htmlspecialchars($companyAddress); ?></p>
                <p>Email: <?php echo htmlspecialchars($companyEmail); ?></p>
                <p>Phone: <?php echo htmlspecialchars($companyPhone); ?></p>
            </div>
            <div class="customer-details">
                <h2>Customer Details</h2>
                <p>Name: <?php echo htmlspecialchars($customerName); ?></p>
                <p>Location: <?php echo htmlspecialchars($customerLocation); ?></p>
                <p>VAT Number: <?php echo htmlspecialchars($vatNumber); ?></p>
            </div>
        </div>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Rate</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoiceItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['productName']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($item['rate']); ?></td>
                        <td><?php echo htmlspecialchars($item['total']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <table class="summary-table">
            <tr>
                <th>Total Amount:</th>
                <td><?php echo number_format($totalAmount, 2); ?></td>
            </tr>
            <tr>
                <th>Discount (<?php echo $discountRate; ?>%):</th>
                <td>-<?php echo number_format($discountAmount, 2); ?></td>
            </tr>
            <tr>
                <th>Amount after Discount:</th>
                <td><?php echo number_format($amountAfterDiscount, 2); ?></td>
            </tr>
            <tr>
                <th>VAT (<?php echo $vatPercentage; ?>%):</th>
                <td><?php echo number_format($vatAmount, 2); ?></td>
            </tr>
            <tr>
                <th>Final Total:</th>
                <td><?php echo number_format($finalTotal, 2); ?></td>
            </tr>
        </table>
        <div class="button-container">
            <button class="print-button" onclick="window.print()">Print Invoice</button>
            <a href="index.php" class="home-button">Home</a>
        </div>
    </div>
</body>
</html>

