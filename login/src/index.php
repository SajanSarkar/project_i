<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_i";

$conn = new mysqli($servername, $username, $password, $dbname);
session_start();

if (array_key_exists("user_id", $_SESSION) && $_SESSION["user_id"] != "") {
  header("Location: ./home.php");
  exit;
}

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$error_message = "";
$email_error = "";
$password_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST["email"];
  $password = md5($_POST["password"]);

  if (empty($email)) {
    $email_error = "Please fill in the email.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $email_error = "Invalid email format.";
  }

  if (empty($password)) {
    $password_error = "Please fill in the password.";
  } elseif (strlen($_POST["password"]) < 5) {
    $password_error = "Password must be at least 5 characters long.";
  }

  if (empty($email_error) && empty($password_error)) {
    $sql = "SELECT * FROM users WHERE email=? AND password=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $row = $result->fetch_array();
      $_SESSION["firstName"] = $row["first_name"];
      $_SESSION["user_id"] = $row["id"];
      header("Location: ./home.php");
      exit;
    } else {
      $error_message = "Invalid email or password.";
    }
  }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DS Billing</title>
  <style>
    * {
      box-sizing: border-box;
    }

    .flex {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      background-image: url('./assets/bg.jpg');
      background-repeat: no-repeat;
      background-size: cover;
      font-family: Arial, sans-serif;
    }

    .container {
      background-color: white;
      border-radius: 20px;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
      padding: 50px;
      text-align: center;
      display: flex;
      justify-content: center;
    }

    h1 {
      color: #e8eae7;
      font-size: 3rem;
      margin-bottom: 50px;
      text-align: left;
    }

    .form {
      margin-bottom: 10px;
    }

    input {
      display: block;
      width: 100%;
      height: 40px;
      padding: 0 10px;
      margin-bottom: 5px;
      border: 1px solid black;
      border-radius: 10px;
      font-size: 1rem;
      line-height: 2.5;
    }

    input:focus {
      outline: none;
      box-shadow: 0 0 2px 1px #4d4a4a;
    }

    .login-button {
      display: block;
      width: 50%;
      height: 40px;
      background-color: #4267b2;
      border: none;
      border-radius: 10px;
      color: white;
      font-size: 1rem;
      cursor: pointer;
      margin-top: 10px;
    }

    .login-button:hover {
      background-color: #736b6c;
    }

    .links {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 1rem;
      color: #120202;
      text-decoration: none;
    }

    .links a {
      margin: 2rem;
    }

    .links a:hover {
      text-decoration: underline;
    }

    .flex-col {
      flex-direction: column;
    }

    #imglogo {
      padding-right: 10%;
    }

    .error {
      color: red;
      font-size: 0.875rem;
      margin-top: -5px;
      margin-bottom: 10px;
      text-align: left;
    }
  </style>
</head>
<body>
  <div class="container flex">
    <div id="imglogo">
      <img src="./assets/logo.svg" alt="Logo" height="200vh" width="200wv">
    </div>
    <div>
      <form action="#" class="flex flex-col form" method="POST" onsubmit="return validateForm()">
        <input type="text" placeholder="Email address" name="email" id="email" />
        <div class="error" id="email-error"></div>
        <input type="password" placeholder="Password" name="password" id="password" />
        <div class="error" id="password-error"></div>
        <button type="submit" name="submit" class="login-button">Log In</button>
        <?php if (!empty($error_message)): ?>
          <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
      </form>
      <div class="links">
        <h4>Don't have an account? </h4>
        <a href="create.php">Create account</a>
      </div>
    </div>
  </div>
  <script>
    function validateForm() {
      const email = document.getElementById("email").value.trim();
      const password = document.getElementById("password").value.trim();
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      let isValid = true;

      // Reset errors
      document.getElementById("email-error").textContent = "";
      document.getElementById("password-error").textContent = "";

      if (!email) {
        document.getElementById("email-error").textContent = "Please fill in the email.";
        isValid = false;
      } else if (!emailRegex.test(email)) {
        document.getElementById("email-error").textContent = "Invalid email format.";
        isValid = false;
      }

      if (!password) {
        document.getElementById("password-error").textContent = "Please fill in the password.";
        isValid = false;
      } else if (password.length < 5) {
        document.getElementById("password-error").textContent = "Password must be at least 5 characters long.";
        isValid = false;
      }

      return isValid;
    }
  </script>
</body>
</html>
