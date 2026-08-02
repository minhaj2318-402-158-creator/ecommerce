<?php

require_once "config/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT *
FROM orders
WHERE user_id=?
ORDER BY id DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$orders = $stmt->get_result();

include "includes/header.php";
include "includes/navbar.php";

?>

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-dark text-white">

<h3>My Orders</h3>

</div>

<div class="card-body">

<?php if($orders->num_rows>0){ ?>

<table class="table table-bordered table-hover">

<thead class="table-primary">

<tr>

<th>#</th>

<th>Order ID</th>

<th>Date</th>

<th>Total</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

$sl=1;

while($row=$orders->fetch_assoc()){

?>

<tr>

<td><?= $sl++ ?></td>

<td>#<?= $row['id'] ?></td>

<td><?= $row['created_at'] ?></td>

<td>৳<?= number_format($row['total_amount'],2) ?></td>

<td>

<?php

$status=$row['order_status'];

if($status=="Pending"){

echo "<span class='badge bg-warning'>Pending</span>";

}elseif($status=="Processing"){

echo "<span class='badge bg-info'>Processing</span>";

}elseif($status=="Completed"){

echo "<span class='badge bg-success'>Completed</span>";

}else{

echo "<span class='badge bg-danger'>Cancelled</span>";

}

?>

</td>

<td>

<a
href="order-details.php?id=<?= $row['id'] ?>"
class="btn btn-primary btn-sm">

View

</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

<?php }else{ ?>

<div class="alert alert-warning">

You have not placed any orders yet.

</div>

<a
href="index.php"
class="btn btn-primary">

Start Shopping

</a>

<?php } ?>

</div>

</div>

</div>

<?php include "includes/footer.php"; ?>