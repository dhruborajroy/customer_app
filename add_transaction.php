<?php
   session_start();
   if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
     header("Location: pin.php");
     exit;
   }
      // Function to generate WhatsApp link with a URL-encoded message
      function generateWhatsAppLink($phone, $message) {
        // URL encode the message to ensure all characters are properly encoded
        $encodedMessage = urlencode($message);
        $whatsappLink = "https://wa.me/$phone?text=$encodedMessage"; // Create WhatsApp link
        return $whatsappLink;
      }
      
      // add_transaction.php
      include 'db.php';
      $customer_id = floatval($_GET['id'] ?? 0);
      $customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customers WHERE id = $customer_id"));
      if (!$customer) {
        echo "Customer not found.";
        exit;
      }
      
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       // echo "<pre>";
       // print_r($_POST);
       // die;
      $customer_phone = mysqli_real_escape_string($conn, $_POST['customer_phone']);
      $date = date("Y-m-d h:i:s");
      $bw_single = floatval($_POST['bw_single']) ?? floatval(0);
      $bw_double = floatval($_POST['bw_double']) ?? floatval(0);
      $pdf_bw_double = floatval($_POST['pdf_bw_double']) ?? floatval(0);
      $color_pages = floatval($_POST['color']) ?? floatval(0);
      $rate_bw_single = floatval($_POST['rate_bw_single']) ?? floatval(0);
      $rate_pdf_bw_double = floatval($_POST['rate_pdf_bw_double']) ?? floatval(0);
      $rate_bw_double = floatval($_POST['rate_bw_double']) ?? floatval(0);
      $rate_color = floatval($_POST['rate_color']) ?? floatval(0);
      
      $bw_single_total = $bw_single * $rate_bw_single;
      // echo $pdf_bw_double_total = $pdf_bw_double * $rate_pdf_bw_double;
      $pdf_bw_double_total = 0;
      $bw_double_total = $bw_double * $rate_bw_double;
      $color_total = $color_pages * $rate_color;
      
   
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
      $credit_note = "bw_single-".$bw_single."@{$rate_bw_single}:pdf_bw_double-".$pdf_bw_double."@{$rate_pdf_bw_double}:bw_double-".$bw_double."@{$rate_bw_double}:color_pages-".$color_pages."@{$rate_color}";
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
      $customerPhone = "+88".$customer_phone; 
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
      $message = "🖨️ *প্রিন্ট হিসাব* \n";
      $bw_single>0 ? $message .= "সাদা/কালো (Single Sided): {$bw_single} x {$rate_bw_single} = " . floatval($bw_single_total) . " Tk\n" : '' ;
      $bw_double>0 ? $message .=  "সাদা/কালো (Double Sided): {$bw_double} x {$rate_bw_double} = " . floatval($bw_double_total) . " Tk\n" : '' ;
      $pdf_bw_double>0 ? $message .=  "সাদা/কালো (Pdf Page/Double Sided): {$pdf_bw_double} x {$rate_pdf_bw_double} = " . floatval($pdf_bw_double_total) . " Tk\n" : '' ;
      $color_pages>0 ? $message .= "কালার পেজ: {$color_pages} x {$rate_color} = " . floatval($color_total) . " Tk\n" : '' ;
      $total_bill= floatval($bw_single_total)+floatval($bw_double_total)+floatval($color_total)+floatval($pdf_bw_double_total);
      $message .="----------------------------\n";
      $message .="মোট বিল";
      $message .=" $total_bill Tk\n\n";
      $discount_amount=floatval($total_bill) - floatval($credit_amount);

      $discount_amount> 0 ? $message .= "*ডিসকাউন্ট  ".$discount_amount. " TK*\n\n " : '' ;

      if ($balance > 0) {
         if ($previous_balance < 0) {
            $message .= "পূর্বের জমা " . abs($previous_balance) . " Tk\n";
            $message .= "বর্তমান বিল " . abs($credit_amount) . " Tk\n";
            $message .= "টাকা পেলাম $debit_amount Tk\n";
            $message .= "বর্তমান বাকি " . abs($balance) . " Tk\n\n";
         } else {
            $message .= "পূর্বের বাকি $previous_balance Tk\n";
            $message .= "বর্তমান বিল " . abs($credit_amount) . " Tk\n";
            $message .= "টাকা পেলাম $debit_amount Tk\n";
            $message .= "বর্তমান বাকি " . abs($balance) . " Tk\n\n";
         }
      } else {
         if ($previous_balance < 0) {
            $message .= "পূর্বের জমা " . abs($previous_balance) . " Tk\n";
            $message .= "বর্তমান বিল " . abs($credit_amount) . " Tk\n";
            $message .= "টাকা পেলাম $debit_amount Tk\n";
            $message .= "বর্তমান জমা " . abs($balance) . " Tk\n\n";
         } else {
            $message .= "পূর্বের বাকি $previous_balance Tk\n";
            $message .= "বর্তমান বিল " . abs($credit_amount) . " Tk\n";
            $message .= "টাকা পেলাম $debit_amount Tk\n";
            $message .= "বর্তমান জমা " . abs($balance) . " Tk\n\n";
         }
      }
      
      $message .= "*Bkash(send money): +8801705927257*\n\n";
      $message .= "Dhrubo's Printing Services\n";
      $message .= "Barishal Engineering College";
   //   echo nl2br($message);
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
   </head>
   <body style="background-color: #f8f9fa;">
      <div class="container py-5">
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
               <form method="post" target="_blank">
                  <div class="card-body">
                     <h5 class="mb-3 text-secondary">🖨️ Printing Price Calculator</h5>
                     <!-- Checkbox to show/hide rate customization -->
                     <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="toggleRates" onchange="toggleRateInputs()">
                        <label class="form-check-label" for="toggleRates">Customize Print Rates</label>
                     </div>
                     <!-- Page Quantity Row -->
                     <div class="row mb-3">
                        <div class="col-md-4">
                           <label class="form-label">B&W Single Sided Pages (2.5 Tk Each)</label>
                           <input type="number" class="form-control" id="bw_single" name="bw_single" onchange="calculateTotal()">
                        </div>
                        <div class="col-md-4">
                           <label class="form-label">B&W Double Sided Pages (3.5 Tk Each)</label>
                           <input type="number" class="form-control" id="bw_double" name="bw_double" onchange="calculateTotal()">
                        </div>
                        <!-- <div class="col-md-3">
                           <label class="form-label">Pdf Double(Default 1.75 Tk)</label>
                        </div> -->
                        <input type="hidden" class="form-control" id="pdf_bw_double" name="pdf_bw_double" onchange="calculateTotal()">
                        <div class="col-md-4">
                           <label class="form-label">Color Pages (5 Tk Each)</label>
                           <input type="number" class="form-control" id="color" name="color" onchange="calculateTotal()">
                        </div>
                     </div>
                     <!-- Hidden Rate Input Row -->
                     <div class="row mb-3" id="rateRow" style="display: none;">
                        <div class="col-md-4">
                           <label class="form-label">Rate for B&W Single (Default 2.5 Tk)</label>
                           <input type="number" step="0.01" class="form-control" id="rate_bw_single" name="rate_bw_single" value="2.5" onchange="calculateTotal()">
                        </div>
                        <div class="col-md-4">
                           <label class="form-label">Rate for B&W Double (Default 3.5 Tk)</label>
                           <input type="number" step="0.01" class="form-control" id="rate_bw_double" name="rate_bw_double" value="3.5" onchange="calculateTotal()">
                        </div>
                        <!-- <div class="col-md-3">
                           <label class="form-label">Pdf Double(Default 1.75 Tk)</label>
                        </div> -->
                        <input type="hidden" step="0.01" class="form-control" id="rate_pdf_bw_double" name="rate_pdf_bw_double" value="1.75" onchange="calculateTotal()">
                        <div class="col-md-4">
                           <label class="form-label">Rate for Color (Default 5 Tk)</label>
                           <input type="number" step="0.01" class="form-control" id="rate_color" name="rate_color" value="5" onchange="calculateTotal()">
                        </div>
                     </div>
                     <div class="row mb-4">
                        <div class="col-md-4">
                           <label class="form-label">Paid Amount (Tk)</label>
                           <input type="number" class="form-control" id="paid" onchange="calculateTotal()">
                        </div>
                        <div class="col-md-4">
                           <label class="form-label">Total Price (Tk)</label>
                           <input type="text" class="form-control" id="total" readonly>
                        </div>
                        <div class="col-md-4">
                           <label class="form-label">Due (Tk)</label>
                           <input type="text" class="form-control" id="due" readonly>
                        </div>
                     </div>
                     <div class="row g-4">
                        <div class="col-md-6">
                           <div class="border rounded p-3 bg-light">
                              <h5 class="text-success"><i class="bi bi-plus-circle"></i> Credit (বিক্রি করলাম)</h5>
                              <div class="mb-3">
                                 <label class="form-label">Amount (৳)</label>
                                 <input type="number" name="credit_amount" step="0.01" class="form-control" placeholder="বিক্রি করলাম" required>
                              </div>
                              <input type="hidden" name="credit_note" class="form-control" value="Printing Credit">
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="border rounded p-3 bg-light">
                              <h5 class="text-danger"><i class="bi bi-dash-circle"></i> Debit (টাকা পেলাম)</h5>
                              <div class="mb-3">
                                 <label class="form-label">Amount (৳)</label>
                                 <input type="number" name="debit_amount" step="0.01" class="form-control" placeholder="টাকা পেলাম">
                              </div>
                              <input type="hidden" name="debit_note" class="form-control" value="Payment Received">
                           </div>
                        </div>
                     </div>
                     <input type="hidden" name="customer_phone" value="<?= $customer['phone'] ?>">
                     <div class="mt-4 d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-circle"></i> সাবমিট করুন
                        </button>
                        <a href="details.php?id=<?= $customer_id ?>" class="btn btn-success">
                        <i class="bi bi-arrow-right-circle"></i> ডিটেইলস
                        </a>
                     </div>
                  </div>
                  <?php 
                     // Total Credit
                     $credit_result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total_credit FROM transactions WHERE type = 'credit' and customer_id='$customer_id'"));
                     $total_credit = $credit_result['total_credit'] ?? 0;
                     
                     // Total Debit
                     $debit_result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total_debit FROM transactions WHERE type = 'debit'  and customer_id='$customer_id'"));
                     $total_debit = $debit_result['total_debit'] ?? 0;
                     
                     // Balance
                     $balance = $total_credit - $total_debit;?>
                  <div class="card-body">
                     <div class="col-md-4">
                        <div class="card <?php echo  $balance>0 ? 'border-danger' : 'border-success' ?>  ">
                           <div class="card-body">
                              <h5 class="card-title   <?php echo  $balance>0 ? 'text-danger' : 'text-success' ?> ">বর্তমান  <?php echo  $balance>0 ? 'বাকি' : 'জমা' ?></h5>
                              <p class="card-text fs-4">৳ <?= floatval(abs($balance)) ?></p>
                           </div>
                        </div>
                     </div>
                  </div>
            </div>
            </form>
            <div class="card-footer text-center text-muted small">
               Developed by Dhrubo Raj Roy, Dept. of Civil Engineering, BEC
            </div>
         </div>
      </div>
      </div>
      <script>
         function toggleRateInputs() {
         const rateRow = document.getElementById('rateRow');
         rateRow.style.display = document.getElementById('toggleRates').checked ? 'flex' : 'none';
            calculateTotal();
         }
         
         function calculateTotal() {
         const bwSingle = parseFloat(document.getElementById('bw_single').value) || 0;
         const bwDouble = parseFloat(document.getElementById('bw_double').value) || 0;
         const bwPdfDouble = parseFloat(document.getElementById('pdf_bw_double').value) || 0;
         const color = parseFloat(document.getElementById('color').value) || 0;
         
         // Default rates
         let rateSingle = 2.5;
         let rateDouble = 3.5;
         let ratePdfDouble = 1.75;
         let rateColor = 5;
         
         // If custom rates are enabled, override defaults
         if (document.getElementById('toggleRates').checked) {
            rateSingle = parseFloat(document.getElementById('rate_bw_single').value) || rateSingle;
            rateDouble = parseFloat(document.getElementById('rate_bw_double').value) || rateDouble;
            ratePdfDouble = parseFloat(document.getElementById('rate_pdf_bw_double').value) || ratePdfDouble;
            rateColor = parseFloat(document.getElementById('rate_color').value) || rateColor;
         }
         
         const total = (bwSingle * rateSingle) + (bwDouble * rateDouble) + (color * rateColor)+ (bwPdfDouble * ratePdfDouble);
         let paid = parseFloat(document.getElementById('paid').value) || 0;
         let due = total - paid;
         
         document.getElementById('total').value = total.toFixed(2);
         document.getElementById('due').value = due.toFixed(2);
         }
      </script>
      <!-- Bootstrap Icons CDN -->
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
   </body>
</html>