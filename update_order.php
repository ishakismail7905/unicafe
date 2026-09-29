<?php
require 'db_connect.php';
$orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$allowed = ['Pending', 'Preparing', 'Ready', 'Collected', 'Cancelled'];
$message = '';
if (!$orderId) {
exit('Invalid order ID.');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$status = $_POST['order_status'] ?? '';
if (in_array($status, $allowed, true)) {
$stmt = $pdo->prepare(
"UPDATE orders SET order_status = ? WHERE order_id = ?"
);
$stmt->execute([$status, $orderId]);
$message = 'Order status updated.';
}
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
include 'header.php';
?>
<h2>Update Order #<?= $orderId ?></h2>
<?php if ($message): ?><p class="message"><?= $message ?></p><?php endif; ?>
<p>Customer: <?= htmlspecialchars($order['customer_name']) ?></p>
<form method="post">
<label>Order Status</label>
<select name="order_status">
<?php foreach ($allowed as $status): ?>
<option value="<?= $status ?>"
<?= $order['order_status'] === $status ? 'selected' : '' ?>>
<?= $status ?>
</option>
<?php endforeach; ?>
</select>
<button type="submit">Update Status</button>
</form>
</main>
<footer>UniBrew Café • Web Application Development Lab</footer>
</body>
</html>
