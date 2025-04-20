<?php

// summary.php with PIN protection
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
  header("Location: pin.php");
  exit;
}

// index.php
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
  <title>Add Transaction</title>
  
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dhrubo's Printing Services</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .badge {
      font-size: 0.9rem;
    }
  </style>
  <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- DataTables Buttons Extension -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<!-- JSZip and pdfmake -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- DataTables Responsive -->
<script src="https://cdn.datatables.net/responsive/3.0.0/js/dataTables.responsive.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.0/css/responsive.dataTables.min.css">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body style="background-color: #f8f9fa;">
<div class="container py-5">
  <div class="card shadow-lg">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0">
        <i class="bi bi-cash-stack"></i> Dhrubo's Printing Services        - লেনদেন
      </h4>
      <a href="index.php" class="btn btn-light btn-sm">
        <i class="bi bi-house-door"></i> হোম
      </a>
    </div>
    <div class="card-body">
            
      <div class="container py-4">
        <div>
          <div class="container py-5">
            <h3 class="mb-4">All Customers Summary</h3>
            <div class="row g-4">
              <div class="col-md-4">
                <div class="card border-success">
                  <div class="card-body">
                    <h5 class="card-title text-success">মোট পাবো</h5>
                    <p class="card-text fs-4">৳ <?= number_format($balance>0 ? abs($balance) :'0' , 2) ?></p>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card border-danger">
                  <div class="card-body">
                    <h5 class="card-title text-danger">মোট দেবো</h5>
                    <p class="card-text fs-4">৳ <?= number_format($balance<0 ? abs($balance) :'0' , 2) ?></p>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card border-primary">
                  <div class="card-body">
                    <h5 class="card-title text-primary">Overall Balance</h5>
                    <p class="card-text fs-4">৳ <?= number_format($balance, 2) ?></p>
                  </div>
                </div>
              </div>
            </div>
        </div>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM customers ORDER BY id DESC");
        ?>
        <table id="customerTable" class="table table-bordered table-hover nowrap" style="width:100%">
          <thead class="table-dark">
            <tr>
              <th>Name</th>
              <th>Phone</th>
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
              <td><?= htmlspecialchars($row['phone']) ?></td>
              <td><span class="badge <?= $badge_class ?>">৳ <?= number_format(abs($balance), 2) ?></span></td>
              <td>
                <a href="details.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">View</a>
                <!-- <a href="edit_customer.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="delete_customer.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a> -->
              </td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
      </body>
      <script>
      $(document).ready(function() {
        $('#customerTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
            // 'copy', 'csv', 'excel', 'pdf', 'print'
          ],
          responsive: true,
          language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ customers",
            paginate: {
              previous: "Previous",
              next: "Next"
            },
            columnDefs: [
              {
                targets: -1, // last column
                visible: false,
                searchable: false
              }
            ]
          }
        });
      });
      </script>
    </div>
    <div class="card-footer text-center text-muted small">
      Developed by Dhrubo Raj Roy, Dept. of Civil Engineering, BEC
    </div>
  </div>
</div>

<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</body>

</html>

<!DOCTYPE html>
<html lang="en">
<head></head>
<body>
