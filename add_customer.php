
<?php
// index.php
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Balance App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #e0f7fa, #f8f9fa);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .form-container {
      background-color: #ffffff;
      padding: 2rem 2.5rem;
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      max-width: 500px;
      width: 100%;
    }
    h3 {
      font-weight: 600;
      color: #0d6efd;
    }
  </style>
</head>
<body>
<!-- add_customer.php -->
<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && basename($_SERVER['PHP_SELF']) === 'add_customer.php') {
  include 'db.php';
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $phone = mysqli_real_escape_string($conn, $_POST['phone']);
  mysqli_query($conn, "INSERT INTO customers (name, phone) VALUES ('$name', '$phone')");
  header("Location: index.php");
  exit;
} ?>

<div class="form-container">
  <h3 class="text-center mb-4">➕ Add New Customer</h3>

  <form method="POST" action="">
    <div class="mb-3">
      <label for="name" class="form-label">👤 Name</label>
      <input type="text" name="name" id="name" class="form-control" placeholder="Enter customer name" required>
    </div>
    <div class="mb-3">
      <label for="phone" class="form-label">📞 Phone</label>
      <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter phone number" required>
    </div>
    <div class="d-grid gap-2 mt-4">
      <button type="submit" class="btn btn-primary">✅ Add Customer</button>
      <a href="index.php" class="btn btn-secondary">🏠 Back to Home</a>
    </div>
  </form>
</div>