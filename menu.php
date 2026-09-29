<?php
require 'db_connect.php';

$items = $pdo->query(
"SELECT * FROM menu_items ORDER BY category, item_name"
)->fetchAll();
include 'header.php';
?>
<h2>Menu Items</h2>
<table>
<tr>
<th>Item</th><th>Category</th><th>Price</th>
<th>Stock</th><th>Availability</th>
</tr>
<?php foreach ($items as $item): ?>
<tr>
<td><?= htmlspecialchars($item['item_name']) ?></td>
<td><?= $item['category'] ?></td>
<td>RM <?= number_format($item['price'], 2) ?></td>
<td>
<?= $item['stock_quantity'] ?>
<?= $item['stock_quantity'] < 10 ? ' (Low)' : '' ?>
</td>
<td><?= $item['is_available'] ? 'Available' : 'Hidden' ?></td>
</tr>
<?php endforeach; ?>
</table>
</main>
<footer>UniBrew Café • Web Application Development Lab</footer>
</body>
</html>
