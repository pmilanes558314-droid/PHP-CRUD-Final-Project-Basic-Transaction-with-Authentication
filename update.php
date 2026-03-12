<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$transaction = $stmt->fetch();

if (!$transaction) {
    die("Transaction not found or access denied.");
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = $_POST['category'];
    $desc = trim($_POST['description']);
    $amount = $_POST['amount'];
    $date = $_POST['transaction_date'];

    if (!empty($desc) && $amount > 0 && !empty($date)) {
        $stmt = $pdo->prepare("UPDATE transactions
                               SET category=?, description=?, amount=?, transaction_date=?
                               WHERE id=? AND user_id=?");
        $stmt->execute([$category, $desc, $amount, $date, $id, $_SESSION['user_id']]);

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
    <title>Edit Transaction | Financial Tracker</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #f4f6f9;
            margin: 0;
            display: flex;
        }
        .sidebar {
            width: 220px;
            background: #2c3e50;
            color: #fff;
            padding: 1.5rem;
            height: 100vh;
        }
        .sidebar h2 {
            margin-bottom: 1.5rem;
        }
        .sidebar a {
            display: block;
            color: #ecf0f1;
            text-decoration: none;
            margin: 0.5rem 0;
        }
        .sidebar a:hover {
            color: #3498db;
        }
        .main {
            flex: 1;
            padding: 2rem;
        }
        h2 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        form {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 400px;
        }
        label {
            display: block;
            margin-top: 1rem;
        }
        input, select {
            width: 100%;
            padding: 0.5rem;
            margin-top: 0.25rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            margin-top: 1.5rem;
            width: 100%;
            padding: 0.75rem;
            background: #3498db;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #2980b9;
        }
        .error {
            color: red;
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
        <h2>Edit Transaction</h2>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="">
            <label for="category">Category</label>
            <select name="category" id="category" required>
                <option value="income" <?= $transaction['category'] == 'income' ? 'selected' : '' ?>>Income</option>
                <option value="expense" <?= $transaction['category'] == 'expense' ? 'selected' : '' ?>>Expense</option>
            </select>

            <label for="description">Description</label>
            <input type="text" name="description" id="description" value="<?= htmlspecialchars($transaction['description']) ?>" required>

            <label for="amount">Amount</label>
            <input type="number" step="0.01" name="amount" id="amount" value="<?= htmlspecialchars($transaction['amount']) ?>" required>

            <label for="transaction_date">Date</label>
            <input type="date" name="transaction_date" id="transaction_date" value="<?= htmlspecialchars($transaction['transaction_date']) ?>" required>

            <button type="submit">Update Transaction</button>
        </form>
    </div>
</body>
</html>