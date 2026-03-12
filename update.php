<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$transaction = $stmt->fetch();

if (!$transaction) {
    die("Transaction not found or access denied.");
}

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
    </div>
</body>
</html>