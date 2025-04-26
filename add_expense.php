<?php
include 'db.php';

// Handle Add Expense POST
$message = '';
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_expense'])) {
    $title = trim($_POST['title']);
    $amount = floatval($_POST['amount']);
    $note = trim($_POST['note']);

    if ($title !== '' && $amount > 0) {
        $stmt = mysqli_prepare($conn, "INSERT INTO expenses (title, amount, note) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sds", $title, $amount, $note);
        if (mysqli_stmt_execute($stmt)) {
            $message = "Expense added successfully!";
        } else {
            $message = "Error saving expense.";
        }
    } else {
        $message = "Please enter a valid title and amount.";
    }
}

// Fetch overall totals
$sales_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) AS total_sales FROM transactions WHERE type = 'credit'"));
$total_sales = $sales_res['total_sales'] ?? 0;
$exp_res_tot = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) AS total_expenses FROM expenses"));
$total_expenses = $exp_res_tot['total_expenses'] ?? 0;
$profit_overall = $total_sales - $total_expenses;

// Fetch monthly data
$sales_monthly_q = "SELECT DATE_FORMAT(date, '%Y-%m') AS month, SUM(amount) AS total_sales FROM transactions WHERE type = 'credit' GROUP BY month ORDER BY month";
$sales_monthly_res = mysqli_query($conn, $sales_monthly_q);
$sales_monthly = [];
while ($row = mysqli_fetch_assoc($sales_monthly_res)) {
    $sales_monthly[$row['month']] = (float)$row['total_sales'];
}
$exp_monthly_q = "SELECT DATE_FORMAT(expense_date, '%Y-%m') AS month, SUM(amount) AS total_expenses FROM expenses GROUP BY month ORDER BY month";
$exp_monthly_res = mysqli_query($conn, $exp_monthly_q);
$exp_monthly = [];
while ($row = mysqli_fetch_assoc($exp_monthly_res)) {
    $exp_monthly[$row['month']] = (float)$row['total_expenses'];
}
$months = array_unique(array_merge(array_keys($sales_monthly), array_keys($exp_monthly)));
sort($months);
$labels = [];
$profit_data = [];
foreach ($months as $m) {
    $labels[] = date('M Y', strtotime($m . '-01'));
    $sales_val = $sales_monthly[$m] ?? 0;
    $exp_val = $exp_monthly[$m] ?? 0;
    $profit_data[] = $sales_val - $exp_val;
}

// Fetch expenses list
$expenses_res = mysqli_query($conn, "SELECT * FROM expenses ORDER BY expense_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Monthly Profit & Expenses</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .dashboard-card { border-radius: 1rem; }
    .summary-card { border-radius: 0.75rem; }
    .chart-card, .form-card, .list-card { border-radius: 1rem; }
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
        <li class="nav-item"><a class="nav-link" href="#formCard">Add Expense</a></li>
        <li class="nav-item"><a class="nav-link" href="#listCard">Expense List</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-4">
  <!-- Summary & Chart -->
  <div class="card shadow-lg dashboard-card mb-4">
    <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
      <h4 class="mb-0">📊 Monthly Profit Dashboard</h4>
    </div>
    <div class="card-body">
      <div class="row text-center g-3 mb-4">
        <div class="col-md-4">
          <div class="card summary-card border-success shadow-sm">
            <div class="card-body">
              <h6 class="text-success">Total Sales</h6>
              <h4>৳<?= number_format($total_sales, 2) ?></h4>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card summary-card border-danger shadow-sm">
            <div class="card-body">
              <h6 class="text-danger">Total Expenses</h6>
              <h4>৳<?= number_format($total_expenses, 2) ?></h4>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card summary-card border-primary shadow-sm">
            <div class="card-body">
              <h6 class="text-primary">Overall Profit</h6>
              <h4>৳<?= number_format($profit_overall, 2) ?></h4>
            </div>
          </div>
        </div>
      </div>
      <div class="card chart-card shadow-sm">
        <div class="card-body">
          <canvas id="profitChart" height="80"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Expense Form -->
  <div class="card shadow-sm form-card mb-4" id="formCard">
    <div class="card-header">
      <h5 class="mb-0">➕ Add New Expense</h5>
    </div>
    <div class="card-body">
      <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>
      <form method="POST">
        <input type="hidden" name="add_expense" value="1">
        <div class="row g-3">
          <div class="col-md-5">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Amount (৳)</label>
            <input type="number" name="amount" step="0.01" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Note</label>
            <input type="text" name="note" class="form-control" placeholder="Optional">
          </div>
        </div>
        <div class="mt-3">
          <button type="submit" class="btn btn-primary">Add Expense</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Expenses List -->
  <div class="card shadow-sm list-card" id="listCard">
    <div class="card-header">
      <h5 class="mb-0">📋 Expenses List</h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Title</th>
            <th>Amount</th>
            <th>Note</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $i=1; while($exp = mysqli_fetch_assoc($expenses_res)): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($exp['title']) ?></td>
            <td>৳<?= number_format($exp['amount'],2) ?></td>
            <td><?= htmlspecialchars($exp['note']) ?></td>
            <td><?= date('d M Y', strtotime($exp['expense_date'])) ?></td>
            <td>
              <a href="edit_expense.php?id=<?= $exp['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
              <a href="delete_expense.php?id=<?= $exp['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this expense?')">Delete</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const ctx = document.getElementById('profitChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?= json_encode($labels) ?>,
      datasets: [{
        label: 'Profit (Tk)',
        data: <?= json_encode($profit_data) ?>,
        backgroundColor: 'rgba(78, 115, 223, 0.6)',
        borderColor: 'rgba(78, 115, 223, 1)',
        borderWidth: 1
      }]
    },
    options: {
      scales: { y: { beginAtZero: true } },
      responsive: true,
      plugins: {
        title: { display: true, text: 'Monthly Profit Overview', font: { size: 18 } },
        legend: { display: false }
      }
    }
  });
</script>
</body>
</html>