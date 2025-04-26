<?php
include 'db.php';

// Fetch overall totals
$sales_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) AS total_sales FROM transactions WHERE type = 'credit'"));
$total_sales = $sales_res['total_sales'] ?? 0;
$exp_res_tot = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) AS total_expenses FROM expenses"));
$total_expenses = $exp_res_tot['total_expenses'] ?? 0;
$profit_overall = $total_sales - $total_expenses;

// Fetch monthly sales
$sales_monthly_q = "
    SELECT DATE_FORMAT(date, '%Y-%m') AS month, SUM(amount) AS total_sales
    FROM transactions
    WHERE type = 'credit'
    GROUP BY month
    ORDER BY month
";
$sales_monthly_res = mysqli_query($conn, $sales_monthly_q);
$sales_monthly = [];
while ($row = mysqli_fetch_assoc($sales_monthly_res)) {
    $sales_monthly[$row['month']] = (float)$row['total_sales'];
}

// Fetch monthly expenses
$exp_monthly_q = "
    SELECT DATE_FORMAT(expense_date, '%Y-%m') AS month, SUM(amount) AS total_expenses
    FROM expenses
    GROUP BY month
    ORDER BY month
";
$exp_monthly_res = mysqli_query($conn, $exp_monthly_q);
$exp_monthly = [];
while ($row = mysqli_fetch_assoc($exp_monthly_res)) {
    $exp_monthly[$row['month']] = (float)$row['total_expenses'];
}

// Merge months
$months = array_unique(array_merge(array_keys($sales_monthly), array_keys($exp_monthly)));
sort($months);

$labels = [];
$profit_data = [];
foreach ($months as $m) {
    $labels[] = date('M Y', strtotime($m . '-01'));
    $sales_val = $sales_monthly[$m] ?? 0;
    $exp_val = $exp_monthly[$m] ?? 0;
    $profit_val = $sales_val - $exp_val;
    $profit_data[] = $profit_val;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Profit Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
      .dashboard-card { border-radius: 1rem; }
      .summary-card { border-radius: 0.75rem; }
      .chart-card { border-radius: 1rem; }
      .card-header-custom { background: linear-gradient(90deg, #4e73df, #224abe); color: #fff; border-top-left-radius: 1rem; border-top-right-radius: 1rem; }
      .card-nav .nav-link { color: #fff; }
      .card-nav .nav-link.active { background-color: rgba(255,255,255,0.2); }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Printing App</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"> <span class="navbar-toggler-icon"></span> </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="add_customer.php">Add Customer</a></li>
        <li class="nav-item"><a class="nav-link" href="add_transaction.php?id=1">Add Transaction</a></li>
        <li class="nav-item"><a class="nav-link" href="all_expenses.php">Expenses</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div class="card shadow-lg dashboard-card">
    <div class="card-header card-header-custom">
      <div class="d-flex justify-content-between align-items-center">
        <h4 class="mb-0">📊 Monthly Profit Dashboard</h4>
        <ul class="nav card-nav">
          <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button">Actions</a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="add_expense.php">Add Expense</a></li>
              <li><a class="dropdown-item" href="add_customer.php">Add Customer</a></li>
            </ul>
          </li>
        </ul>
      </div>
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