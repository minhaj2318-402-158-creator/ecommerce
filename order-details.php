<?php

require_once "config/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: my-orders.php");
    exit();
}

$order_id = (int)$_GET['id'];
$user_id  = $_SESSION['user_id'];

/* Order Information */

$stmt = $conn->prepare("
SELECT *
FROM orders
WHERE id=? AND user_id=?
LIMIT 1
");

$stmt->bind_param("ii",$order_id,$user_id);
$stmt->execute();

$order = $stmt->get_result();

if($order->num_rows==0){
    die("Order not found.");
}

$order = $order->fetch_assoc();

/* Order Items */

$itemStmt = $conn->prepare("
SELECT *
FROM order_items
WHERE order_id=?
");

$itemStmt->bind_param("i",$order_id);
$itemStmt->execute();

$items = $itemStmt->get_result();

include "includes/header.php";
include "includes/navbar.php";

?>
<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h3>

Order Details

</h3>

</div>

<div class="card-body">

<div class="row mb-4">

<div class="col-md-6">

<h5>Customer Information</h5>

<hr>

<p>

<strong>Name :</strong>

<?= htmlspecialchars($order['customer_name']) ?>

</p>

<p>

<strong>Email :</strong>

<?= htmlspecialchars($order['customer_email']) ?>

</p>

<p>

<strong>Phone :</strong>

<?= htmlspecialchars($order['customer_phone']) ?>

</p>

<p>

<strong>Address :</strong>

<?= nl2br(htmlspecialchars($order['customer_address'])) ?>

</p>

</div>

<div class="col-md-6">

<h5>Order Information</h5>

<hr>

<p>

<strong>Order ID :</strong>

#<?= $order['id'] ?>

</p>

<p>

<strong>Date :</strong>

<?= $order['created_at'] ?>

</p>

<p>

<strong>Status :</strong>

<?= $order['order_status'] ?>

</p>

<p>

<strong>Total :</strong>

৳<?= number_format($order['total_amount'],2) ?>

</p>

</div>

</div>

<h4>

Ordered Products

</h4>

<table class="table table-bordered">

<thead class="table-dark">

<tr>

<th>Product</th>

<th>Price</th>

<th>Qty</th>

<th>Subtotal</th>

</tr>

</thead>

<tbody>

<?php while($item=$items->fetch_assoc()){ ?>

<tr>

<td>

<?= htmlspecialchars($item['product_name']) ?>

</td>

<td>

৳<?= number_format($item['price'],2) ?>

</td>

<td>

<?= $item['quantity'] ?>

</td>

<td>

৳<?= number_format($item['subtotal'],2) ?>

</td>

</tr>

<?php } ?>

</tbody>

<tfoot>

<tr>

<th colspan="3" class="text-end">

Grand Total

</th>

<th>

৳<?= number_format($order['total_amount'],2) ?>

</th>

</tr>

</tfoot>

</table>

<div class="mt-4">

<a
href="my-orders.php"
class="btn btn-secondary">

Back

</a>

<a href="invoice.php?id=<?= $order['id'] ?>" class="btn btn-success">
    Print Invoice
</a>

</div>

</div>

</div>

</div>

<?php include "includes/footer.php"; ?>