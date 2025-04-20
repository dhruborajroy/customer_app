
<!-- edit_customer.php -->
<?php
if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $result = mysqli_query($conn, "SELECT * FROM customers WHERE id = $id");
  $customer = mysqli_fetch_assoc($result);

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    mysqli_query($conn, "UPDATE customers SET name = '$name', phone = '$phone' WHERE id = $id");
    header("Location: index.php");
    exit;
  }
?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Transactions of <?= htmlspecialchars($customer['name']) ?></h3>
    <div>
      <a href="index.php" class="btn btn-success">Home</a>
      <a href="add_transaction.php?id=<?= $customer_id ?>" class="btn btn-success">+ Add Transaction</a>
      <a href="index.php" class="btn btn-secondary">Back</a>
    </div>
  </div>
  <form method="POST" action="">
    <div class="mb-3">
      <label for="name" class="form-label">Name</label>
      <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($customer['name']) ?>" required>
    </div>
    <div class="mb-3">
      <label for="phone" class="form-label">Phone</label>
      <input type="text" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($customer['phone']) ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>
<?php } ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
