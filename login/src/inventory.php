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
if (!array_key_exists("user_id", $_SESSION)) {
    header("Location: ./index.php");
    exit();
}

// Check if form is submitted for adding items or sales
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $itemName = $conn->real_escape_string($_POST["itemName"]);
    $quantity = intval($_POST["quantity"]);
    $rate = floatval($_POST["rate"]);
    $action = $_POST["action"];

    if ($action == "add") {
        // Add item to inventory
        $stmt = $conn->prepare("INSERT INTO inventory (item_name, quantity, rate) VALUES (?, ?, ?)");
        $stmt->bind_param("sid", $itemName, $quantity, $rate);
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
    } elseif ($action == "sell") {
        // Sell item and update inventory
        $stmt = $conn->prepare("SELECT quantity FROM inventory WHERE item_name = ?");
        $stmt->bind_param("s", $itemName);
        $stmt->execute();
        $stmt->bind_result($currentQuantity);
        if ($stmt->fetch()) {
            $stmt->close();
            if ($currentQuantity >= $quantity) {
                $newQuantity = $currentQuantity - $quantity;
                if ($newQuantity > 0) {
                    $stmt = $conn->prepare("UPDATE inventory SET quantity = ? WHERE item_name = ?");
                    $stmt->bind_param("is", $newQuantity, $itemName);
                } else {
                    $stmt = $conn->prepare("DELETE FROM inventory WHERE item_name = ?");
                    $stmt->bind_param("s", $itemName);
                }
                if ($stmt->execute()) {
                    $stmt->close();
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    echo "<script>alert('Error: " . $stmt->error . "');</script>";
                }
            } else {
                echo "<script>alert('Error: Insufficient stock');</script>";
            }
        } else {
            echo "<script>alert('Error: Item not found');</script>";
        }
    } elseif ($action == "remove") {
        // Remove item from inventory
        $stmt = $conn->prepare("DELETE FROM inventory WHERE item_name = ?");
        $stmt->bind_param("s", $itemName);
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
    }
}

$sql_create_table_inventory = "CREATE TABLE IF NOT EXISTS inventory (
  id INT AUTO_INCREMENT PRIMARY KEY,
  item_name VARCHAR(255) NOT NULL,
  quantity INT NOT NULL,
  rate DECIMAL(10, 2) NOT NULL
)";
$conn->query($sql_create_table_inventory);

$sql = "SELECT * FROM inventory";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.6;
        }

        header {
            background-color: #4CAF50;
            color: #fff;
            padding: 15px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .container {
            margin: 20px auto;
            padding: 20px;
            max-width: 1000px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .form-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .form-container input {
            padding: 10px;
            margin-right: 10px;
            width: calc(20% - 22px);
            border: 1px solid #ddd;
            border-radius: 4px;
            flex: 1;
        }

        .form-container button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .available-stock, .total-quantity, .total-rate {
            margin-top: 10px;
            font-weight: bold;
        }

        .home {
            position: absolute;
            top: 15px;
            right: 20px;
            color: white;
            text-decoration: none;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>Inventory Management</h1>
        <a href="home.php" class="home">Home</a>
    </header>
    <div class="container">
        <div class="form-container">
            <form method="post">
                <input type="text" name="itemName" id="itemName" placeholder="Item Name" required>
                <input type="number" name="quantity" id="quantity" placeholder="Quantity" min="1" required>
                <input type="number" name="rate" id="rate" placeholder="Rate" min="0.01" step="0.01" required>
                <button type="submit" name="action" value="add">Add Item</button>
            </form>
            <form method="post">
                <input type="text" name="itemName" id="sellItemName" placeholder="Item Name" required>
                <input type="number" name="quantity" id="sellQuantity" placeholder="Quantity" min="1" required>
                <button type="submit" name="action" value="sell">Sell Item</button>
            </form>
        </div>
        <div class="search-box-container">
            <input type="text" class="search-box" id="searchBox" placeholder="Search by Item Name" onkeyup="searchItem()">
            <button type="button" class="search-button" onclick="searchItem()">Search</button>
        </div>
        <div class="available-stock" id="availableStock"></div>
        <div class="total-quantity" id="totalQuantity"></div>
        <div class="total-rate" id="totalRate"></div>
        <table id="inventory">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Rate</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $totalQuantity = 0;
                    $totalRate = 0;
                    while ($row = $result->fetch_assoc()) {
                        $totalQuantity += $row['quantity'];
                        $totalRate += $row['quantity'] * $row['rate'];
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['rate']) . "</td>";
                        echo "<td>
                                <form method='post' style='display:inline;'>
                                    <input type='hidden' name='itemName' value='" . htmlspecialchars($row['item_name']) . "'>
                                    <button type='submit' name='action' value='remove' onclick='return confirm(\"Are you sure you want to remove this item?\")'>Remove</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                    echo "<script>
                            document.getElementById('totalQuantity').innerText = 'Total Quantity: ' + $totalQuantity;
                            document.getElementById('totalRate').innerText = 'Total Value: \$' + $totalRate.toFixed(2);
                          </script>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function searchItem() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchBox");
            filter = input.value.toUpperCase();
            table = document.getElementById("inventory");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</body>
</html>
