<?php
require 'db_connect.php';
$customerCount = $pdo->query(
"SELECT COUNT(*) FROM customers"
)->fetchColumn();
$menuCount = $pdo->query(
"SELECT COUNT(*) FROM menu_items WHERE is_available = 1"
)->fetchColumn();
$pendingCount = $pdo->query(
"SELECT COUNT(*) FROM orders
WHERE order_status IN ('Pending', 'Preparing')"
)->fetchColumn();
include 'header.php';
?>
<h2>Dashboard</h2>
<p>Use this system to manage café pre-orders.</p>
<section class="cards">
<div class="card">

<h3><?= $customerCount ?></h3>
<p>Registered customers</p>
</div>
<div class="card">
<h3><?= $menuCount ?></h3>
<p>Available menu items</p>
</div>
<div class="card">
<h3><?= $pendingCount ?></h3>
<p>Orders in progress</p>
</div>
</section>
<a class="button" href="new_order.php">Create New Order</a>
</main>
<footer>UniBrew Café • Web Application Development Lab</footer>
</body>
</html>
