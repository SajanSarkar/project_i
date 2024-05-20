<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_i";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$email_error = ""; // Initialize email error variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $first_name = $_POST["first_name"];
  $last_name = $_POST["last_name"];
  $email = $_POST["email"];
  $password = md5($_POST["password"]);

  // Server-side validation
  if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
    die("Please fill in all fields.");
  }

  if ($_POST["password"] !== $_POST["confirmPassword"]) {
    die("Passwords do not match.");
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format.");
  }

  // Check for duplicate email
  $check_duplicate_email = "SELECT * FROM users WHERE email = ?";
  $stmt_check = $conn->prepare($check_duplicate_email);
  $stmt_check->bind_param("s", $email);
  $stmt_check->execute();
  $result = $stmt_check->get_result();

  if ($result->num_rows > 0) {
    $email_error = "Email already exists."; // Set email error message
  } else {
    // Continue with database operations
    $sql1 = "CREATE TABLE IF NOT EXISTS users (
      id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      first_name VARCHAR(240) NOT NULL,
      last_name VARCHAR(240) NOT NULL,
      email VARCHAR(240) NOT NULL UNIQUE,
      password VARCHAR(255) NOT NULL
    )";

    if (!$conn->query($sql1)) {
      die("Error creating table: " . $conn->error);
    }

    $sql = "INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $first_name, $last_name, $email, $password);

    if (!$stmt->execute()) {
      die("Error inserting data: " . $conn->error);
    }

    echo "Successfully data inserted";
    header("Location:./index.php");
  }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <style>
   * {
     box-sizing: border-box;
    }
  
  body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 100;
    background-image: url('./assets/cr.jpeg');
      background-repeat: no-repeat;
      background-size:cover ;
      font-family: Arial, sans-serif;
  }
  
  .container {
    width: 500px;
    background-color: white;
    border-radius: 20px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    padding: 10px;
    text-align: center;
  }
    .signup-page {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 50px;
  }
  
  .signup-page h1 {
    font-size: 2.5rem;
    margin-bottom: -15px;
    color: black;
  }
  .signup-page h3 {
    font-size: 1.5rem;
    margin-bottom: 25px;
    color: black;
  }
  
  .signup-page form {
    display: flex;
    flex-direction: column;
    width: 300px;
  }
  
  .signup-page label {
    font-size: 1rem;
    margin-bottom: 5px;
    
  }
  
  .signup-page input[type="text"],
  .signup-page input[type="text"],
  .signup-page input[type="email"],
  .signup-page input[type="password"] {
    height: 40px;
    border: 1px solid black;
    border-radius: 10px;
    padding: 0 10px;
    margin-bottom: 5px;
  }
  
  .signup-page button[type="submit"] {
    height: 40px;
    background-color: #4267b2;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 10px;
  }
  
  .signup-page button[type="submit"]:hover {
    background-color: #3b5998;
  }
  </style>
</head>
<body>
  
<div class="container">
  <div class="signup-page">
    <div id="imglogo">
      <img src="./assets/logo.svg" alt="Logo" height="200vh" width="200wv">
    </div>
    <h1>Sign Up</h1>
    <h3>It's quick and easy</h3>
    <form action="#" class="form" method="POST" id="signup-form">
      <input type="text" placeholder="First Name" required name="first_name"/>
      <input type="text" placeholder="Surname" name="last_name" required />
      <input type="text" placeholder="Email address" required name="email" />
      <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($email_error)): ?>
        <p style="color: red;"><?php echo $email_error; ?></p>
      <?php endif; ?>
      <input type="password" placeholder=" New Password" required name="password"/>
      <input type="password" placeholder="Confirm Password" required name="confirmPassword"/>
      <button type="submit" name="submit">Sign Up</button>
    </form>
  </div>
</div>
</body>
</html>
