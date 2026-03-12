<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = $_POST['category'];
    $desc = trim($_POST['description']);
    $amount = $_POST['amount'];
    $date = $_POST['transaction_date'];

    if (!empty($desc) && $amount > 0 && !empty($date)) {
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, category, description, amount, transaction_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $category, $desc, $amount, $date]);
        header("Location: read.php");
        exit;
    } else {
        $error = "Invalid input.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Transaction | Financial Tracker</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #f4f6f9;
            display: flex;
        }
        .sidebar {
            width: 220px;
            background: #2c3e50;
            color: #fff;
            height: 100vh;
            padding: 1rem;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 2rem;
        }
        .sidebar a {
            display: block;
            color: #ecf0f1;
            text-decoration: none;
            padding: 0.7rem;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background: #34495e;
        }
        .main {
            flex: 1;
            padding: 2rem;
        }
        .form-container {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 500px;
            margin: auto;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 1.5rem;
        }
        label {
            font-weight: 600;
            color: #34495e;
            display: block;
            margin-bottom: 0.5rem;
        }
        input, select {
            width: 100%;
            padding: 0.7rem;
            margin-bottom: 1rem;
            border: 1px solid #dcdcdc;
            border-radius: 6px;
            transition: border 0.3s;
        }
        input:focus, select:focus {
            border-color: #3498db;
            outline: none;
        }
        button {
            width: 100%;
            padding: 0.8rem;
            background: #3498db;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #2980b9;
        }
        .error {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Tracker</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="create.php">Add Transaction</a>
        <a href="read.php">View Transactions</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="main">
        <div class="form-container">
            <h2>Add Transaction</h2>
            <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="POST" action="">
                <label for="category">Category</label>
                <select name="category" id="category" required>
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>

                <label for="description">Description</label>
                <input type="text" name="description" id="description" required>

                <label for="amount">Amount</label>
                <input type="number" step="0.01" name="amount" id="amount" required>

                <label for="transaction_date">Date</label>
                <input type="date" name="transaction_date" id="transaction_date" required>

                <button type="submit">Save Transaction</button>
            </form>
        </div>
    </div>
</body>
</html>