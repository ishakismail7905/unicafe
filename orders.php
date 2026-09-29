<?php
require 'db_connect.php';
$sql = "SELECT o.*, c.customer_name,
p.payment_id, p.payment_method
FROM orders o
JOIN customers c ON o.customer_id = c.customer_id
LEFT JOIN payments p ON o.order_id = p.order_id
ORDER BY o.order_date DESC";
$orders = $pdo->query($sql)->fetchAll();
include 'header.php';
?>
<h2>Orders</h2>
<table>
<tr>
<th>ID</th><th>Customer</th><th>Pickup</th><th>Total</th>
<th>Status</th><th>Payment</th><th>Action</th>
</tr>
<?php foreach ($orders as $order): ?>
<tr>
<td>#<?= $order['order_id'] ?></td>
<td><?= htmlspecialchars($order['customer_name']) ?></td>
<td><?= date('d M Y, g:i A',
strtotime($order['pickup_time'])) ?></td>
<td>RM <?= number_format($order['total_amount'], 2) ?></td>
<td><?= $order['order_status'] ?></td>
<td>
<?= $order['payment_id']
? htmlspecialchars($order['payment_method'])
: 'Unpaid' ?>
</td>
<td>
<a href="update_order.php?id=<?= $order['order_id'] ?>">
Update
</a>
<?php if (!$order['payment_id']): ?>
| <a href="payment.php?id=<?= $order['order_id'] ?>">
Pay
</a>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</table>
</main>
<footer>UniBrew Café • Web Application Development Lab</footer>
</body>
</html>
