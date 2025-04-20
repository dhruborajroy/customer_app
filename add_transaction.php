
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
  $customer_phone = mysqli_real_escape_string($conn, $_POST['customer_phone']);
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
      $message .= "বর্তমান বিল ".abs($credit_amount)."\n";
      $message .= "টাকা পেলাম $debit_amount\n";
      $message .= "বর্তমান জমা ".abs($balance)."\n\n";
    }else{
      $message .= "পূর্বের বাকি $previous_balance\n";
      $message .= "বর্তমান বিল ".abs($credit_amount)."\n";
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
    </div>
    <div class="card-body">
      <form method="post"  target="_blank">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="border rounded p-3 bg-light">
              <h5 class="text-success"><i class="bi bi-plus-circle"></i> Credit (বিক্রি করলাম)</h5>
              <div class="mb-3">
                <label class="form-label">Amount (৳)</label>
                <input type="number" name="credit_amount" step="0.01" class="form-control" placeholder="বিক্রি করলাম">
              </div>
              <div class="mb-3">
                <!-- <label class="form-label">Note</label> -->
                <input type="hidden" name="credit_note" class="form-control" placeholder="ক্রেডিট এর নোট">
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="border rounded p-3 bg-light">
              <h5 class="text-danger"><i class="bi bi-dash-circle"></i> Debit (টাকা পেলাম)</h5>
              <div class="mb-3">
                <label class="form-label">Amount (৳)</label>
                <input type="number" name="debit_amount" step="0.01" class="form-control" placeholder="টাকা পেলাম">
              </div>
              <div class="mb-3">
                <!-- <label class="form-label">Note</label> -->
                <input type="hidden" name="debit_note" class="form-control" placeholder="ডেবিট এর নোট">
              </div>
            </div>
          </div>
        </div>

        <input type="hidden" name="customer_phone" value="<?= $customer['phone'] ?>">

        <!-- <div class="row mt-4">
          <div class="col-md-6">
            <label class="form-label">তারিখ</label>
            <input type="date" name="date" class="form-control" required value="<?= date('Y-m-d') ?>">
          </div>
        </div> -->

        <div class="mt-4 d-flex justify-content-between align-items-center">
          <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-circle"></i> সাবমিট করুন
          </button>
          <a href="details.php?id=<?= $customer_id ?>" class="btn btn-success">
            <i class="bi bi-arrow-circle"></i> Details
          </a>
        </div>
      </form>
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
