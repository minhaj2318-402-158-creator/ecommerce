<?php

require_once "config/config.php";

include "includes/header.php";
include "includes/navbar.php";

if(!isset($_SESSION['last_order']))
{
    header("Location:index.php");
    exit();
}

$order_id = $_SESSION['last_order'];

unset($_SESSION['last_order']);

?>

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card shadow border-0">

<div class="card-body text-center p-5">

<h1 class="text-success">

✅ Order Placed Successfully

</h1>

<hr>

<h4>

Your Order ID

</h4>

<h2 class="text-primary">

#<?= $order_id ?>

</h2>

<p class="mt-3">

Thank you for shopping with us.

</p>

<p>

We have received your order successfully.

</p>

<div class="mt-4">

<a
href="index.php"
class="btn btn-primary">

Continue Shopping

</a>

<a
href="my-orders.php"
class="btn btn-success">

My Orders

</a>

</div>

</div>

</div>

</div>

</div>

</div>

<?php include "includes/footer.php"; ?>