<?php
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

<?php
// Function to generate WhatsApp link with a URL-encoded message
function generateWhatsAppLink($phone, $message) {
  // URL encode the message to ensure all characters are properly encoded
  $encodedMessage = urlencode($message);
  $whatsappLink = "https://wa.me/$phone?text=$encodedMessage"; // Create WhatsApp link
  return $whatsappLink;
}

// add_transaction.php
include 'db.php';
$customer_id = intval($_GET['id'] ?? 0);
$customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customers WHERE id = $customer_id"));
if (!$customer) {
  echo "Customer not found.";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $type = mysqli_real_escape_string($conn, $_POST['type']);
  $amount = floatval($_POST['amount']);
  $customer_phone = mysqli_real_escape_string($conn, $_POST['customer_phone']);
  $note = mysqli_real_escape_string($conn, $_POST['note']);
  $date = date("Y-m-d h:i:s");

  // Total Credit
  $credit_result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total_credit FROM transactions WHERE type = 'credit' and customer_id='$customer_id'"));
  $total_credit = $credit_result['total_credit'] ?? 0;

  // Total Debit
  $debit_result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total_debit FROM transactions WHERE type = 'debit'  and customer_id='$customer_id'"));
  $total_debit = $debit_result['total_debit'] ?? 0;
  // Balance
  $previous_balance = $total_credit - $total_debit;


  $date = date("Y-m-d H:i:s");
  $customer_phone = mysqli_real_escape_string($conn, $_POST['customer_phone']);

  // Credit entry
  $credit_amount = floatval($_POST['credit_amount']);
  $credit_note = mysqli_real_escape_string($conn, $_POST['credit_note']);
  if ($credit_amount > 0) {
    mysqli_query($conn, "INSERT INTO transactions (customer_id, type, amount, description, date) 
                         VALUES ($customer_id, 'credit', $credit_amount, '$credit_note', '$date')");
  }

  // Debit entry
  $debit_amount = floatval($_POST['debit_amount']);
  $debit_note = mysqli_real_escape_string($conn, $_POST['debit_note']);
  if ($debit_amount > 0) {
    mysqli_query($conn, "INSERT INTO transactions (customer_id, type, amount, description, date) 
                         VALUES ($customer_id, 'debit', $debit_amount, '$debit_note', '$date')");
  }
  
  $customerPhone = "+88".$customer_phone; // Customer's phone number (including country code)

  // Total Credit
  $credit_result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total_credit FROM transactions WHERE type = 'credit' and customer_id='$customer_id'"));
  $total_credit = $credit_result['total_credit'] ?? 0;

  // Total Debit
  $debit_result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total_debit FROM transactions WHERE type = 'debit'  and customer_id='$customer_id'"));
  $total_debit = $debit_result['total_debit'] ?? 0;

  $message = "লেনদেন রেকর্ড\n";
  $message .= "কাস্টমার মোবাইল নং $customer_phone\n";

  // Balance
  $balance = $total_credit - $total_debit;
  if($balance>0){
    if($previous_balance<0){
      $message .= "পূর্বের জমা ".abs($previous_balance)."\n";
      $message .= "বর্তমান বিল ".abs($credit_amount)."\n";
      $message .= "টাকা পেলাম $debit_amount\n";
      $message .= "বর্তমান বাকি ".abs($balance)."\n\n";
    }else{
      $message .= "পূর্বের বাকি $previous_balance\n";
      $message .= "বর্তমান বিল ".abs($credit_amount)."\n";
      $message .= "টাকা পেলাম $debit_amount\n";
      $message .= "বর্তমান বাকি ".abs($balance)."\n\n";
    }
  }else{
    if($previous_balance<0){
      $message .= "পূর্বের জমা ".abs($previous_balance)."\n";
      $message .= "বর্তমান বিল".abs($credit_amount)."\n";
      $message .= "টাকা পেলাম $debit_amount\n";
      $message .= "বর্তমান জমা ".abs($balance)."\n\n";
    }else{
      $message .= "পূর্বের বাকি $previous_balance\n";
      $message .= "বর্তমান বিল".abs($credit_amount)."\n";
      $message .= "টাকা পেলাম $debit_amount\n";
      $message .= "বর্তমান জমা ".abs($balance)."\n\n";
    }
  }
  $message .= "Dhrubo's Printing Services\n";
  $message .= "Barishal Engineering College";

  // echo nl2br($message);
  // // Generate WhatsApp link
  $whatsappLink = generateWhatsAppLink($customerPhone, $message);

  // Redirect
  header("Location: $whatsappLink");
  exit;
  
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Transaction</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body style="background-color: #f8f9fa;">
<div class="container py-5">
  <div class="card shadow-lg">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0">
        <i class="bi bi-cash-stack"></i> <?= htmlspecialchars($customer['name']) ?> - লেনদেন
      </h4>
      <a href="index.php" class="btn btn-light btn-sm">
        <i class="bi bi-house-door"></i> হোম
      </a>
      <a href="add_transaction.php?id=<?= $customer_id ?>" class="btn btn-light btn-sm">
      <i class="bi bi-house-door"></i> + Add Transaction
      </a>

    </div>
    <div class="card-body">
      
  <!-- Summary Card Section -->
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card card-summary border-info">
        <div class="card-body">
          <h6 class="text-muted">গ্রাহকের নাম</h6>
          <h5 class="text-dark"><?= htmlspecialchars($customer['name']) ?></h5>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card card-summary border-success">
        <div class="card-body">
          <h6 class="text-success">মোট বিক্রি</h6>
          <h5 class="text-success">৳ <?= number_format($total_credit, 2) ?></h5>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card card-summary border-danger">
        <div class="card-body">
          <h6 class="text-danger">মোট পরিশোধ</h6>
          <h5 class="text-danger">৳ <?= number_format($total_debit, 2) ?></h5>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card card-summary border-primary">
        <div class="card-body">
          <h6 class="text-primary">বর্তমান <?= $balance > 0 ? 'বাকি' : 'জমা' ?></h6>
          <h5 class="text-primary">৳ <?= number_format(abs($balance), 2) ?></h5>
        </div>
      </div>
    </div>
  </div>

  <!-- Transactions Table -->
  <h4 class="mb-3">লেনদেনের তালিকা</h4>
  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
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
            <td>
              <span class="badge <?= $row['type'] === 'credit' ? 'bg-success' : 'bg-danger' ?>">
                <?= $row['type'] === 'credit' ? 'বিক্রি করলাম' : 'টাকা পেলাম' ?>
              </span>
            </td>
            <td><?= number_format($row['amount'], 2) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td>
              <a href="edit_transaction.php?id=<?= $row['id'] ?>&customer_id=<?= $customer_id ?>" class="btn btn-warning btn-sm">Edit</a>
              <a href="delete_transaction.php?id=<?= $row['id'] ?>&customer_id=<?= $customer_id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
    <div class="card-footer text-center text-muted small">
      Developed by Dhrubo Raj Roy, Dept. of Civil Engineering, BEC
    </div>
    </div>
  </div>
</div>

<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</body>

</html>

<?php
die;
// details.php
include 'database.php';
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
              
              <p class="card-text fs-4">৳ <?= number_format(trim($balance), 2) ?></p>
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
          <td><?= htmlspecialchars($row['description']) ?></td>
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
