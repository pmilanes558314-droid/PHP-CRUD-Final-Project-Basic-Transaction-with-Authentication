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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['confirm'] === 'yes') {
        $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        header("Location: read.php");
        exit;
    } else {
        header("Location: read.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Delete | Financial Tracker</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .confirm-box {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
        }
        button {
            margin: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .yes {
            background: #e74c3c;
            color: #fff;
        }
        .no {
            background: #3498db;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="confirm-box">
        <h2>Delete Transaction</h2>
        <p>Are you sure you want to delete this transaction?</p>
        <p><strong><?= htmlspecialchars($transaction['description']) ?></strong> — ₱<?= number_format($transaction['amount'], 2) ?></p>
        <form method="POST">
            <button type="submit" name="confirm" value="yes" class="yes">Yes, Delete</button>
            <button type="submit" name="confirm" value="no" class="no">Cancel</button>
        </form>
    </div>
</body>
</html>