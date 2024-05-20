<?php
session_start();
// Set up database connection
if (!array_key_exists("user_id", $_SESSION)) {
    header("Location: ./index.php");
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_i";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
include("business_table.php");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST["name"];
    $_SESSION["cpName"] = $name;
    $location = $_POST["location"];
    $vat_number = $_POST["vat_number"];
    $category = $_POST["category"];
    $website = $_POST["website"];
   
    // Insert data into database
    $stmt = $conn->prepare("INSERT INTO business_table (name, location, vat_number, category, website, user_id) VALUES (?, ?, ?, ?, ?, ?)");

    if (array_key_exists("user_id", $_SESSION)) {
        $stmt->bind_param("ssssss", $name, $location, $vat_number, $category, $website, $_SESSION["user_id"]);
    }

    $stmt->execute();

    // Close database connection
    $stmt->close();
    $conn->close();

    // Redirect to success page
    header("Location: home.php");  
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Business Registration</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f7f9fc;
      color: #333;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    h1 {
  background-color: #0073e6;
  color: #fff;
  font-size: 2em;
  margin: 0;
  padding: 15rem;
  text-align: left;
  width: 100%;
  border-top-left-radius: 8px;
  border-top-right-radius: 8px;
}

    form {
      background-color: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 2rem;
      width: 100%;
      max-width: 500px;
    }
    label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: bold;
    }
    input[type="text"], input[type="url"], textarea, select {
      border: 1px solid #ccc;
      border-radius: 4px;
      display: block;
      margin-bottom: 1rem;
      padding: 0.5rem;
      width: 100%;
    }
    input[type="text"].error, input[type="url"].error, textarea.error, select.error {
      border-color: #e74c3c;
    }
    .error-message {
      color: #e74c3c;
      font-size: 0.875rem;
      margin-top: -0.5rem;
      margin-bottom: 1rem;
      display: none;
    }
    button[type="submit"] {
      background-color: #0073e6;
      border: none;
      color: #fff;
      padding: 0.75rem 1.5rem;
      border-radius: 4px;
      cursor: pointer;
      font-size: 1rem;
    }
    button[type="submit"]:hover {
      background-color: #005bb5;
    }
    .home {
            position: absolute;
            top: 15px;
            right: 20px;
            color: black;
            text-decoration: none;
            font-size: 1rem;
        }
  </style>
  <script>
    // JavaScript validation
    function validateForm() {
      let isValid = true;
      const name = document.getElementById("name");
      const location = document.getElementById("location");
      const vat_number = document.getElementById("vat_number");
      const category = document.getElementById("category");
      const vatError = document.getElementById("vat-error");

      // Clear previous error states
      vat_number.classList.remove("error");
      vatError.style.display = "none";

      if (name.value.trim() === "") {
        alert("Name is required.");
        name.focus();
        return false;
      }

      if (location.value.trim() === "") {
        alert("Location is required.");
        location.focus();
        return false;
      }

      if (category.value === "") {
        alert("Category is required.");
        category.focus();
        return false;
      }

      const vatPattern = /^\d{9}$/;
      if (!vatPattern.test(vat_number.value.trim())) {
        vat_number.classList.add("error");
        vatError.style.display = "block";
        isValid = false;
      }

      return isValid;
    }
  </script>
</head>
<body>
  <h1>Register Your Business</h1>
  <a href="home.php" class="home">Home</a>
  <form action="entry.php" method="post" onsubmit="return validateForm()">
    <label for="name">Company Name:</label><br>
    <input type="text" id="name" name="name" placeholder="Enter your company Name" required><br>
    <label for="location">Location:</label><br>
    <input type="text" id="location" name="location" placeholder="Enter your location" required><br>
    <label for="vat_number">VAT Number:</label><br>
    <input type="text" id="vat_number" name="vat_number" placeholder="Enter your VAT number" required><br>
    <span id="vat-error" class="error-message">VAT Number must be exactly 9 digits.</span>
    <label for="category">Category:</label><br>
    <select id="category" name="category" required>
      <option value="">Select a category</option>
      <option value="technology">Technology</option>
      <option value="finance">Finance</option>
      <option value="healthcare">Healthcare</option>
      <option value="departmental_store">Departmental Store</option>
    </select><br>
    <label for="website">Website:</label><br>
    <input type="url" id="website" name="website" placeholder="Enter your website if you have"><br>
    <button type="submit">Register</button>
  </form>
</body>
</html>
