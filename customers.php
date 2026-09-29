<?php
require 'db_connect.php';
$customers = $pdo->query(
"SELECT * FROM customers ORDER BY customer_name"
)->fetchAll();
include 'header.php';
?>
<h2>Customers</h2>
<a class="button" href="add_customers.php">Add Customer</a>
<table>
<tr>
<th>ID</th><th>Name</th><th>Email</th><th>Phone</th>
</tr>
<?php foreach ($customers as $customer): ?>
<tr>
<td><?= $customer['customer_id'] ?></td>
<td><?= htmlspecialchars($customer['customer_name']) ?></td>
<td><?= htmlspecialchars($customer['email']) ?></td>
<td><?= htmlspecialchars($customer['phone']) ?></td>
</tr>
<?php endforeach; ?>
</table>
</main>
<footer>UniBrew Café • Web Application Development Lab</footer>
</body>
</html>
