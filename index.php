<?php
require_once "config/config.php";

$sql = "SELECT products.*, categories.category_name
        FROM products
        LEFT JOIN categories
        ON products.category_id = categories.id
        WHERE products.status='Active'
        ORDER BY products.id DESC
        LIMIT 8";

$result = $conn->query($sql);

include "includes/header.php";
include "includes/navbar.php";
?>

<div class="container mt-4">

    <!-- Hero Banner -->

    <div class="p-5 mb-5 bg-primary text-white rounded shadow">

        <h1>Welcome to E-Commerce Store</h1>

        <p class="lead">
            Buy Quality Products at the Best Price.
        </p>

        <a href="#products" class="btn btn-light btn-lg">
            Shop Now
        </a>

    </div>

    <!-- Latest Products -->

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h2 id="products">Latest Products</h2>

    </div>

    <div class="row">

        <?php

        if($result && $result->num_rows > 0){

            while($row = $result->fetch_assoc()){

        ?>

        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">

            <div class="card shadow h-100">

                <?php

                if(!empty($row['image'])){

                ?>

                <img
                src="assets/uploads/products/<?= htmlspecialchars($row['image']) ?>"
                class="card-img-top"
                style="height:220px;object-fit:cover;">

                <?php

                }else{

                ?>

                <img
                src="https://via.placeholder.com/300x220?text=No+Image"
                class="card-img-top">

                <?php } ?>

                <div class="card-body">

                    <small class="text-muted">

                        <?= htmlspecialchars($row['category_name']) ?>

                    </small>

                    <h5 class="mt-2">

                        <?= htmlspecialchars($row['product_name']) ?>

                    </h5>

                    <h4 class="text-primary">

                        ৳<?= number_format($row['price'],2) ?>

                    </h4>

                    <p>

                        Stock :
                        <?= $row['quantity']; ?>

                    </p>

                </div>

                <div class="card-footer bg-white">

                    <a
                    href="product.php?id=<?= $row['id'] ?>"
                    class="btn btn-primary w-100">

                    View Product

                    </a>

                </div>

            </div>

        </div>

        <?php

            }

        }else{

        ?>

        <div class="col-12">

            <div class="alert alert-warning">

                No Products Available.

            </div>

        </div>

        <?php } ?>

    </div>

</div>

<?php
include "includes/footer.php";
?>