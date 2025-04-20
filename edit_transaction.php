<?php
// edit_transaction.php
include 'db.php';
$transaction_id = intval($_GET['id'] ?? 0);
$customer_id = intval($_GET['customer_id'] ?? 0);
$transaction = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM transactions WHERE id = $transaction_id"));

if (!$transaction) {
  echo "Transaction not found.";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $type = $_POST['type'];
  $amount = floatval($_POST['amount']);
  $note = mysqli_real_escape_string($conn, $_POST['note']);
  $date = date("Y-m-d");

  $stmt = mysqli_prepare($conn, "UPDATE transactions SET type = ?, amount = ?, description = ?, date = ? WHERE id = ?");
  mysqli_stmt_bind_param($stmt, "sdssi", $type, $amount, $note, $date, $transaction_id);
  mysqli_stmt_execute($stmt);

  header("Location: details.php?id=$customer_id");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Transaction</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
  <h3>Edit Transaction</h3>
  <form method="post" class="mt-4">
    <div class="mb-3">
      <label class="form-label">Transaction Type</label>
      <select name="type" class="form-select" required>
        <option value="credit" <?= $transaction['type'] === 'credit' ? 'selected' : '' ?>>বিক্রি করলাম</option>
        <option value="debit" <?= $transaction['type'] === 'debit' ? 'selected' : '' ?>>টাকা পেলাম</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Amount (৳)</label>
      <input type="number" name="amount" step="0.01" class="form-control" required value="<?= $transaction['amount'] ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Note</label>
      <input type="text" name="note" class="form-control" value="<?= htmlspecialchars($transaction['description']) ?>">
    </div>
    <button type="submit" class="btn btn-primary">Update Transaction</button>
    <a href="details.php?id=<?= $customer_id ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>
</body>
</html>
