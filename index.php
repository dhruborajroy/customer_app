<?php
// summary.php with PIN protection
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
  header("Location: pin.php");
  exit;
}

include 'db.php';

// Total Credit
$credit_result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total_credit FROM transactions WHERE type = 'credit'"));
$total_credit = $credit_result['total_credit'] ?? 0;

// Total Debit
$debit_result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total_debit FROM transactions WHERE type = 'debit'"));
$total_debit = $debit_result['total_debit'] ?? 0;

// Balance
$balance = $total_credit - $total_debit;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dhrubo's Printing Services</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.0/css/responsive.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/3.0.0/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

  <!-- JSZip and pdfmake -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

  <style>
    body {
      background-color: #f8f9fa;
    }
    .badge {
      font-size: 0.9rem;
    }
  </style>
</head>

<body>
<div class="container py-5">
  <div class="card shadow-lg">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0"><i class="bi bi-cash-stack"></i> Dhrubo's Printing Services - লেনদেন</h4>
      <a href="add_customer.php" class="btn btn-light btn-sm">
        <i class="bi bi-house-door"></i> Add Customer
      </a>
    </div>
    <div class="card-body">
      <div class="container py-4">
        <?php
          $result = mysqli_query($conn, "SELECT * FROM customers ORDER BY id DESC");
        ?>
        <div class="table-responsive">
          <table id="customerTable" class="table table-bordered table-hover nowrap" style="width:100%">
            <thead class="table-dark">
              <tr>
                <th>Name</th>
                <th class="d-none d-sm-table-cell">Phone</th>
                <th>Total Balance</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
              <?php
                $customer_id = $row['id'];
                $sum_result = mysqli_query($conn, "SELECT 
                  (SELECT IFNULL(SUM(amount), 0) FROM transactions WHERE customer_id = $customer_id AND type = 'credit') -
                  (SELECT IFNULL(SUM(amount), 0) FROM transactions WHERE customer_id = $customer_id AND type = 'debit') 
                  AS balance");
                $balance_data = mysqli_fetch_assoc($sum_result);
                $balance = $balance_data['balance'];
                $badge_class = $balance > 0 ? 'bg-danger' : ($balance < 0 ? 'bg-success' : 'bg-secondary');
              ?>
              <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td class="d-none d-sm-table-cell"><?= htmlspecialchars($row['phone']) ?></td>

                <td><span class="badge <?= $badge_class ?>">৳ <?= number_format(abs($balance), 2) ?></span></td>
                <td>
                  <a href="add_transaction.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">View</a>
                </td>
              </tr>
            <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="card-footer text-center text-muted small">
      Developed by Dhrubo Raj Roy, Dept. of Civil Engineering, BEC
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    $('#customerTable').DataTable({
      responsive: true,
      dom: 'Bfrtip',
      buttons: [], // Enable CSV, Excel, Print here if needed
      language: {
        search: "Search:",
        lengthMenu: "Show _MENU_ entries",
        info: "Showing _START_ to _END_ of _TOTAL_ customers",
        paginate: {
          previous: "Previous", 
          next: "Next"
        }
      }
    });
  });
</script>
</body>
</html>
