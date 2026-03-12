<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT
        SUM(CASE WHEN category='income' THEN amount ELSE 0 END) AS total_income,
        SUM(CASE WHEN category='expense' THEN amount ELSE 0 END) AS total_expense
    FROM transactions WHERE user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$data = $stmt->fetch();
$balance = $data['total_income'] - $data['total_expense'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Financial Tracker</title>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .cards {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .card {
            flex: 1;
            background: #fff;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        .card h3 {
            margin: 0;
            color: #34495e;
        }
        .card p {
            font-size: 1.4rem;
            font-weight: bold;
            margin-top: 0.5rem;
        }
        .income { color: #27ae60; }
        .expense { color: #e74c3c; }
        .balance { color: #2980b9; }
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
        <div class="header">
            <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h2>
            <p>Last login: <?= $_COOKIE['last_login'] ?? 'First time login' ?></p>
        </div>
        <div class="cards">
            <div class="card income">
                <h3>Total Income</h3>
                <p>₱<?= number_format($data['total_income'], 2) ?></p>
            </div>
            <div class="card expense">
                <h3>Total Expense</h3>
                <p>₱<?= number_format($data['total_expense'], 2) ?></p>
            </div>
            <div class="card balance">
                <h3>Balance</h3>
                <p>₱<?= number_format($balance, 2) ?></p>
            </div>
        </div>
    </div>
</body>
</html>