<?php
require 'db_connect.php';
$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$name = trim($_POST['customer_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)
|| $phone === '') {
$error = 'Enter a name, valid email, and phone number.';
} else {
try {
$stmt = $pdo->prepare(
"INSERT INTO customers
(customer_name, email, phone)
VALUES (?, ?, ?)"
);
$stmt->execute([$name, $email, $phone]);
$message = 'Customer added successfully.';
} catch (PDOException $e) {
$error = 'The email may already be registered.';
}
}
}
include 'header.php';
?>
<h2>Add Customer</h2>
<?php if ($message): ?>
<p class="message"><?= $message ?></p>
<?php endif; ?>
<?php if ($error): ?>
<p class="error"><?= $error ?></p>
<?php endif; ?>
<form method="post">
<label>Customer Name</label>
<input type="text" name="customer_name" required>
<label>Email</label>
<input type="email" name="email" required>
<label>Phone</label>
<input type="text" name="phone" required>
<button type="submit">Save Customer</button>
</form>
</main>
<footer>UniBrew Café • Web Application Development Lab</footer>
</body>
</html>
