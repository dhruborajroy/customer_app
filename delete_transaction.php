<?php
// delete_transaction.php
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
  header("Location: pin.php");
  exit;
}

include 'db.php';
$transaction_id = intval($_GET['id'] ?? 0);
$customer_id = intval($_GET['customer_id'] ?? 0);

if ($transaction_id && $customer_id) {
  $stmt = mysqli_prepare($conn, "DELETE FROM transactions WHERE id = ? AND customer_id = ?");
  mysqli_stmt_bind_param($stmt, "ii", $transaction_id, $customer_id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
}

header("Location: details.php?id=$customer_id");
exit;
