<?php

require_once "config/config.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("
SELECT products.*, categories.category_name
FROM products
LEFT JOIN categories
ON products.category_id = categories.id
WHERE products.id=?
LIMIT 1
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Product Not Found.");
}

$product = $result->fetch_assoc();

include "includes/header.php";
include "includes/navbar.php";
?>

<div class="container mt-5">

<div class="row">

<div class="col-md-5">

<?php if(!empty($product['image'])){ ?>

<img
src="assets/uploads/products/<?= htmlspecialchars($product['image']) ?>"
class="img-fluid rounded shadow">

<?php }else{ ?>

<img
src="https://via.placeholder.com/500x500?text=No+Image"
class="img-fluid rounded">

<?php } ?>

</div>

<div class="col-md-7">

<h2>

<?= htmlspecialchars($product['product_name']) ?>

</h2>

<p class="text-muted">

Category :
<strong><?= htmlspecialchars($product['category_name']) ?></strong>

</p>

<h3 class="text-success">

৳<?= number_format($product['price'],2) ?>

</h3>

<p>

<?= nl2br(htmlspecialchars($product['description'])) ?>

</p>

<p>

<strong>Available Stock :</strong>

<?= $product['quantity'] ?>

</p>

<form action="cart.php" method="POST">

<input
type="hidden"
name="product_id"
value="<?= $product['id'] ?>">

<div class="mb-3">

<label>Quantity</label>

<input
type="number"
name="quantity"
class="form-control"
value="1"
min="1"
max="<?= $product['quantity'] ?>"
style="width:120px;">

</div>

<button
type="submit"
name="add_to_cart"
class="btn btn-primary">

🛒 Add To Cart

</button>

<a
href="index.php"
class="btn btn-secondary">

Back

</a>

</form>

</div>

</div>

</div>

<?php
include "includes/footer.php";
?>