<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] == 'admin') {
    $stmt = $pdo->query("SELECT t.*, u.username
                         FROM transactions t
                         JOIN users u ON t.user_id=u.id
                         ORDER BY transaction_date DESC");
} else {
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC");
    $stmt->execute([$_SESSION['user_id']]);
}
$transactions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transactions | Financial Tracker</title>
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
            margin-bottom: 1.5rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #3498db;
            color: #fff;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        tr:hover {
            background: #eef6fc;
        }
        .actions a {
            margin-right: 0.5rem;
            text-decoration: none;
            font-weight: 600;
        }
        .edit {
            color: #27ae60;
        }
        .delete {
            color: #e74c3c;
        }
        .delete:hover {
            text-decoration: underline;
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
        <h2>Transactions</h2>
        <table>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Description</th>
                <th>Amount</th>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <th>User</th>
                <?php endif; ?>
                <th>Actions</th>
            </tr>
            <?php foreach ($transactions as $t): ?>
            <tr>
                <td><?= htmlspecialchars($t['transaction_date']) ?></td>
                <td><?= ucfirst(htmlspecialchars($t['category'])) ?></td>
                <td><?= htmlspecialchars($t['description']) ?></td>
                <td>₱<?= number_format($t['amount'], 2) ?></td>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <td><?= htmlspecialchars($t['username']) ?></td>
                <?php endif; ?>
                <td class="actions">
                    <a class="edit" href="update.php?id=<?= $t['id'] ?>">Edit</a>
                    <a class="delete" href="delete.php?id=<?= $t['id'] ?>" onclick="return confirm('Delete this record?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>