<?php
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $pin = $_POST['pin'] ?? '';
  if ($pin === '0185') {
    $_SESSION['authenticated'] = true;
    header("Location: index.php");
    exit;
  } else {
    $error = "Incorrect PIN. Please try again.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Enter PIN</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #dbeafe, #f0f9ff);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .pin-card {
      max-width: 400px;
      width: 100%;
      background-color: #ffffff;
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      padding: 2rem;
    }
    .pin-input {
      font-size: 1.5rem;
      text-align: center;
      letter-spacing: 8px;
    }
  </style>
</head>
<body>
  <div class="pin-card text-center">
    <h4 class="mb-3 text-primary">Dhrubo's Printing Services 🔐 Secure Access</h4>
    <p class="text-muted">Enter your PIN to access the dashboard</p>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="post" class="mt-3">
      <div class="mb-4">
        <input type="password" name="pin" maxlength="4" class="form-control pin-input" required autofocus placeholder="">
      </div>
      <button type="submit" class="btn btn-primary w-100">🔓 Unlock</button>
    </form>
  </div>
</body>
</html>
