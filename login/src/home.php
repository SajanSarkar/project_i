<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Billing System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        header {
            width: 100%;
            background-color: #4267b2;
            color: white;
            padding: 1rem 0;
            text-align: center;
            position: fixed;
        }

        header h1 {
            margin: 0;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 2rem auto;
            padding: 1rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .welcome-message {
            margin-bottom: 2rem;
            text-align: center;
        }

        .actions {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }

        .actions .action-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin: 1rem;
            width: 250px;
            text-align: center;
            transition: transform 0.2s;
        }

        .actions .action-card:hover {
            transform: translateY(-5px);
        }

        .actions .action-card h2 {
            margin-bottom: 1rem;
        }

        .actions .action-card p {
            margin-bottom: 2rem;
        }

        .actions .action-card a {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #4267b2;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.2s;
        }

        .actions .action-card a:hover {
            background-color: #3b5998;
        }

        footer {
            width: 100%;
            padding: 1rem 0;
            background-color: #4267b2;
            color: white;
            text-align: center;
            position: fixed;
            bottom: 0;
        }
         .logout {
            position: fixed;
            top: 20px;
            right: 20px;
            color: white;
            text-decoration: none;
            font-size: 1rem;
        }
        
    </style>
</head>
<body>
<header>
    <h1>Billing System</h1>
</header>
<div class="container">
    <div class="welcome-message">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['firstName']); ?>!</h2>
        <p>Choose an action below to get started.</p>
    </div>
    <div class="actions">
        <div class="action-card">
            <h2>Create Invoice</h2>
            <p>Generate new invoices for your customers.</p>
            <a href="form.php">Create</a>
        </div>
        <div class="action-card">
            <h2>Inventory</h2>
            <p>Manage your inventory, add new items, and track stock levels.</p>
            <a href="inventory.php">Manage</a>
        </div>
        <div class="action-card">
            <h2>Register Company</h2>
            <p>Register your company.</p>
            <a href="entry.php">Register</a>
        </div>
        <!-- <div class="action-card">
            <h2>Setting</h2>
            <p>You can change your setting from here.</p>
            <a href="setting.php">Manage</a>
        </div> -->
    </div>
    <a href="logout.php" class="logout">Logout</a>
</div>
<footer>
    &copy; 2024 Billing System. All rights reserved.
</footer>
</body>
</html>

