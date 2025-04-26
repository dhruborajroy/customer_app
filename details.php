<?php
   session_start();
   if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
     header("Location: pin.php");
     exit;
   }
   // details.php
   include 'db.php';
   if (!isset($_GET['id'])) {
     header("Location: index.php");
     exit;
   }
   
   $customer_id = intval($_GET['id']);
   $customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customers WHERE id = $customer_id"));
   if (!$customer) {
     echo "Customer not found.";
     exit;
   }
   $transactions = mysqli_query($conn, "SELECT * FROM transactions WHERE customer_id = $customer_id ORDER BY date DESC");
   
   // Total Credit
   $credit_result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total_credit FROM transactions WHERE type = 'credit' and customer_id='$customer_id'"));
   $total_credit = $credit_result['total_credit'] ?? 0;
   
   // Total Debit
   $debit_result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total_debit FROM transactions WHERE type = 'debit'  and customer_id='$customer_id'"));
   $total_debit = $debit_result['total_debit'] ?? 0;
   
   // Balance
   $balance = $total_credit - $total_debit;
   
   
   ?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Transaction Details</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
      <div class="container py-4">
         <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Transactions of <?= htmlspecialchars($customer['name']) ?></h3>
            <div>
               <a href="index.php" class="btn btn-success">Home</a>
               <a href="add_transaction.php?id=<?= $customer_id ?>" class="btn btn-success">+ Add Transaction</a>
               <a href="index.php" class="btn btn-secondary">Back</a>
            </div>
         </div>
         <div class="container py-5">
            <h3 class="mb-4">All Customers Summary</h3>
            <div class="row g-4">
               <div class="col-md-4">
                  <div class="card border-success">
                     <div class="card-body">
                        <h5 class="card-title text-success">মোট বিক্রি </h5>
                        <p class="card-text fs-4">৳ <?= number_format($total_credit, 2) ?></p>
                     </div>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="card border-danger">
                     <div class="card-body">
                        <h5 class="card-title text-danger">মোট পরিশোধ</h5>
                        <p class="card-text fs-4">৳ <?= number_format($total_debit, 2) ?></p>
                     </div>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="card border-primary">
                     <div class="card-body">
                        <h5 class="card-title text-primary">বর্তমান  <?php echo  $balance>0 ? 'বাকি' : 'জমা' ?></h5>
                        <p class="card-text fs-4">৳ <?= number_format(abs($balance), 2) ?></p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <table class="table table-bordered table-striped">
            <thead class="table-dark">
               <tr>
                  <th>Date</th>
                  <th>Type</th>
                  <th>Amount (৳)</th>
                  <th>Note</th>
                  <th>Actions</th>
               </tr>
            </thead>
            <tbody>
               <?php while ($row = mysqli_fetch_assoc($transactions)): ?>
               <tr>
                  <td><?= htmlspecialchars($row['date']) ?></td>
                  <td><span class="badge <?= $row['type'] === 'credit' ? 'bg-success' : 'bg-danger' ?>"><?= $row['type'] === 'credit' ? 'বিক্রি করলাম' : 'টাকা পেলাম' ?></span></td>
                  <td><?= number_format($row['amount'], 2) ?></td>
                  <td><?php
                     $items = explode(':', $row['description']);
                     // Prepare a mapping of keys to nice names// Labels
                      $labels = [
                        'bw_single' => 'BW Single',
                        'pdf_bw_double' => 'PDF BW Double',
                        'bw_double' => 'BW Double',
                        'color_pages' => 'Color Pages',
                      ];

                      // Initialize default values
                      $data = [];
                      foreach ($labels as $key => $label) {
                        $data[$key] = [
                            'quantity' => 0,
                            'price' => 0
                        ];
                      }

                      // Fill data safely
                      foreach ($items as $item) {
                        if (strpos($item, '@') !== false) { // Check if '@' exists
                            list($type_quantity, $price) = explode('@', $item);
                            
                            if (strpos($type_quantity, '-') !== false) { // Check if '-' exists
                                list($type, $quantity) = explode('-', $type_quantity);
                                
                                if (isset($data[$type])) {
                                    $data[$type]['quantity'] = $quantity;
                                    $data[$type]['price'] = $price;
                                }
                            }
                        }
                      }

                      // Print output
                      foreach ($labels as $type => $label) {
                        $quantity = $data[$type]['quantity'];
                        $price = $data[$type]['price'];
                        $page_text = ($quantity == 1) ? "page" : "pages";
                        if($quantity>0) {
                          echo "{$label}: {$quantity} {$page_text} @ {$price}Tk each<br>";
                        }
                      }
                      ?></td>
                  <td>
                     <a href="edit_transaction.php?id=<?= $row['id'] ?>&customer_id=<?= $customer_id ?>" class="btn btn-warning btn-sm">Edit</a>
                     <a href="delete_transaction.php?id=<?= $row['id'] ?>&customer_id=<?= $customer_id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                  </td>
               </tr>
               <?php endwhile; ?>
            </tbody>
         </table>
      </div>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   </body>
</html>