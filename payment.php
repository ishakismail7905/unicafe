<?php
require 'db_connect.php';
$orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$message = '';
$error = '';

if (!$orderId) {
exit('Invalid order ID.');
}
$stmt = $pdo->prepare(
"SELECT o.*, c.customer_name
FROM orders o
JOIN customers c ON o.customer_id = c.customer_id
WHERE o.order_id = ?"
);
$stmt->execute([$orderId]);
$order = $stmt->fetch();
if (!$order) {
exit('Order not found.');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$method = $_POST['payment_method'] ?? '';
$allowed = ['Cash', 'Card', 'Online Banking', 'eWallet'];
if (!in_array($method, $allowed, true)) {
$error = 'Select a valid payment method.';
} else {
try {
$insert = $pdo->prepare(
"INSERT INTO payments
(order_id, payment_method, amount_paid)
VALUES (?, ?, ?)"
);
$insert->execute([
$orderId, $method, $order['total_amount']
]);
$message = 'Payment recorded successfully.';
} catch (PDOException $e) {
$error = 'A payment already exists for this order.';
}
}
}
include 'header.php';
?>
<h2>Payment for Order #<?= $orderId ?></h2>
<?php if ($message): ?><p class="message"><?= $message ?></p><?php endif; ?>
<?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
<p>Customer: <?= htmlspecialchars($order['customer_name']) ?></p>
<p>Amount: <strong>RM <?= number_format(
$order['total_amount'], 2
) ?></strong></p>
<form method="post">
<label>Payment Method</label>
<select name="payment_method" required>
<option value="">-- Select Method --</option>
<option>Cash</option>
<option>Card</option>
<option>Online Banking</option>
<option>eWallet</option>
</select>
<button type="submit">Confirm Payment</button>
</form>
</main>
<footer>UniBrew Café • Web Application Development Lab</footer>
</body>
</html>
