<?php
include 'db.php';

// Initialize variables
$message = '';
$expense = null;
$expense_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch expense details
if ($expense_id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM expenses WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $expense_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $expense = mysqli_fetch_assoc($result);
    if (!$expense) {
        $message = "Expense not found.";
    }
} else {
    $message = "Invalid expense ID.";
}

// Handle Edit Expense POST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_expense'])) {
    $title = trim($_POST['title']);
    $amount = floatval($_POST['amount']);
    $note = trim($_POST['note']);
    $expense_date = $_POST['expense_date'];

    if ($title !== '' && $amount > 0 && $expense_id > 0) {
        $stmt = mysqli_prepare($conn, "UPDATE expenses SET title = ?, amount = ?, note = ?, expense_date = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "sdssi", $title, $amount, $note, $expense_date, $expense_id);
        if (mysqli_stmt_execute($stmt)) {
            $message = "Expense updated successfully!";
            // Refresh expense data
            $stmt = mysqli_prepare($conn, "SELECT * FROM expenses WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $expense_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $expense = mysqli_fetch_assoc($result);
        } else {
            $message = "Error updating expense.";
        }
    } else {
        $message = "Please enter valid expense details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Expense</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .dashboard-card { border-radius: 1rem; }
    .summary-card { border-radius: 0.75rem; }
    .form-card { border-radius: 1rem; }
    .card-header-custom { background: linear-gradient(90deg, #4e73df, #224abe); color: #fff; }
    .card-header h5 { color: #224abe; }
  </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Printing App</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php#formCard">Add Expense</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php#listCard">Expense List</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-4">
  <!-- Edit Expense Form -->
  <div class="card shadow-sm form-card mb-4">
    <div class="card-header">
      <h5 class="mb-0">✏️ Edit Expense</h5>
    </div>
    <div class="card-body">
      <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>
      <?php if ($expense): ?>
        <form method="POST">
          <input type="hidden" name="edit_expense" value="1">
          <div class="row g-3">
            <div class="col-md-5">
              <label class="form-label">Title</label>
              <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($expense['title']) ?>" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Amount (৳)</label>
              <input type="number" name="amount" step="0.01" class="form-control" value="<?= number_format($expense['amount'], 2) ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Note</label>
              <input type="text" name="note" class="form-control" value="<?= htmlspecialchars($expense['note']) ?>" placeholder="Optional">
            </div>
            <div class="col-md-4">
              <label class="form-label">Date</label>
              <input type="date" name="expense_date" class="form-control" value="<?= $expense['expense_date'] ?>" required>
            </div>
          </div>
          <div class="mt-3">
            <button type="submit" class="btn btn-primary">Update Expense</button>
            <a href="index.php#listCard" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      <?php else: ?>
        <p class="text-danger">No expense found to edit.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>