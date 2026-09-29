<?php
require 'db_connect.php';
$summary = $pdo->query(
"SELECT
COUNT(*) AS completed_orders,
COALESCE(SUM(total_amount), 0) AS revenue,
COALESCE(AVG(total_amount), 0) AS average_order
FROM orders
WHERE order_status = 'Collected'"
)->fetch();
$topItems = $pdo->query(
"SELECT m.item_name,
SUM(oi.quantity) AS quantity_sold,
SUM(oi.subtotal) AS item_sales
FROM order_items oi
JOIN menu_items m ON oi.item_id = m.item_id
JOIN orders o ON oi.order_id = o.order_id
WHERE o.order_status = 'Collected'
GROUP BY m.item_id, m.item_name
ORDER BY quantity_sold DESC
LIMIT 5"
)->fetchAll();
include 'header.php';
?>
<h2>Sales Report</h2>
<section class="cards">
<div class="card">
<h3><?= $summary['completed_orders'] ?></h3>
<p>Collected orders</p>
</div>
<div class="card">
<h3>RM <?= number_format($summary['revenue'], 2) ?></h3>
<p>Total revenue</p>
</div>
<div class="card">
<h3>RM <?= number_format($summary['average_order'], 2) ?></h3>
<p>Average order</p>
</div>
</section>
<h3>Top-Selling Items</h3>
<table>
<tr><th>Item</th><th>Quantity Sold</th><th>Sales</th></tr>
<?php foreach ($topItems as $item): ?>
<tr>
<td><?= htmlspecialchars($item['item_name']) ?></td>
<td><?= $item['quantity_sold'] ?></td>
<td>RM <?= number_format($item['item_sales'], 2) ?></td>
</tr>
<?php endforeach; ?>
</table>
</main>
<footer>UniBrew Café • Web Application Development Lab</footer>
</body>
</html>
