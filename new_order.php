<?php
require 'db_connect.php';
$customers = $pdo->query(
"SELECT * FROM customers ORDER BY customer_name"
)->fetchAll();
$items = $pdo->query(
"SELECT * FROM menu_items
WHERE is_available = 1 AND stock_quantity > 0
ORDER BY category, item_name"

)->fetchAll();
$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$customerId = (int)($_POST['customer_id'] ?? 0);
$pickupTime = $_POST['pickup_time'] ?? '';
$quantities = $_POST['quantity'] ?? [];
try {
if ($customerId < 1 || $pickupTime === '') {
throw new Exception('Select a customer and pickup time.');
}
$pdo->beginTransaction();
$stmt = $pdo->prepare(
"INSERT INTO orders (customer_id, pickup_time)
VALUES (?, ?)"
);
$stmt->execute([$customerId, $pickupTime]);
$orderId = $pdo->lastInsertId();
$grandTotal = 0;
$selectedCount = 0;
foreach ($quantities as $itemId => $quantity) {
$itemId = (int)$itemId;
$quantity = (int)$quantity;
if ($quantity <= 0) {
continue;
}
$find = $pdo->prepare(
"SELECT * FROM menu_items
WHERE item_id = ? FOR UPDATE"
);
$find->execute([$itemId]);
$item = $find->fetch();
if (!$item || $quantity > $item['stock_quantity']) {
throw new Exception('Insufficient stock for an item.');
}
$subtotal = $item['price'] * $quantity;
$grandTotal += $subtotal;
$selectedCount++;
$addItem = $pdo->prepare(
"INSERT INTO order_items
(order_id, item_id, quantity, unit_price, subtotal)
VALUES (?, ?, ?, ?, ?)"
);
$addItem->execute([
$orderId, $itemId, $quantity,
$item['price'], $subtotal
]);
$reduce = $pdo->prepare(
"UPDATE menu_items
SET stock_quantity = stock_quantity - ?
WHERE item_id = ?"
);
$reduce->execute([$quantity, $itemId]);
}
if ($selectedCount === 0) {
throw new Exception('Choose at least one menu item.');
}
$update = $pdo->prepare(
"UPDATE orders SET total_amount = ? WHERE order_id = ?"
);

$update->execute([$grandTotal, $orderId]);
$pdo->commit();
$message = "Order #$orderId created. Total: RM "
. number_format($grandTotal, 2);
} catch (Throwable $e) {
if ($pdo->inTransaction()) {
$pdo->rollBack();
}
$error = $e->getMessage();
}
}
include 'header.php';
?>

<h2>Create New Order</h2>
<?php if ($message): ?>
<p class="message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>
<?php if ($error): ?>
<p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<form method="post">
<label>Customer</label>
<select name="customer_id" required>
<option value="">-- Select Customer --</option>
<?php foreach ($customers as $customer): ?>
<option value="<?= $customer['customer_id'] ?>">
<?= htmlspecialchars($customer['customer_name']) ?>
</option>
<?php endforeach; ?>
</select>
<label>Pickup Time</label>
<input type="datetime-local" name="pickup_time" required>
<h3>Select Menu Items</h3>
<table>
<tr>
<th>Item</th><th>Price</th><th>Stock</th><th>Quantity</th>
</tr>
<?php foreach ($items as $item): ?>
<tr>
<td><?= htmlspecialchars($item['item_name']) ?></td>
<td>RM <?= number_format($item['price'], 2) ?></td>
<td><?= $item['stock_quantity'] ?></td>
<td>
<input type="number"
name="quantity[<?= $item['item_id'] ?>]"

min="0"
max="<?= $item['stock_quantity'] ?>"
value="0">

</td>
</tr>
<?php endforeach; ?>
</table>
<button type="submit">Place Order</button>
</form>
</main>
<footer>UniBrew Café • Web Application Development Lab</footer>
</body>
</html>
