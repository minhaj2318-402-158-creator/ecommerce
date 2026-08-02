<?php

require_once "config/config.php";


// ========================================
// SEARCH
// ========================================

$search = "";

if (isset($_GET['search'])) {

    $search = trim($_GET['search']);

}


// ========================================
// PRODUCT QUERY
// ========================================

if (!empty($search)) {

    $searchTerm = "%" . $search . "%";


    $stmt = $conn->prepare("

        SELECT
            products.*,
            categories.category_name

        FROM products

        LEFT JOIN categories

        ON products.category_id = categories.id

        WHERE products.status = 'Active'

        AND (

            products.product_name LIKE ?

            OR products.description LIKE ?

            OR categories.category_name LIKE ?

        )

        ORDER BY products.id DESC

    ");


    $stmt->bind_param(

        "sss",

        $searchTerm,

        $searchTerm,

        $searchTerm

    );


    $stmt->execute();

    $result = $stmt->get_result();


} else {


    $sql = "

        SELECT
            products.*,
            categories.category_name

        FROM products

        LEFT JOIN categories

        ON products.category_id = categories.id

        WHERE products.status = 'Active'

        ORDER BY products.id DESC

    ";


    $result = $conn->query($sql);

}


include "includes/header.php";

include "includes/navbar.php";

?>


<div class="container mt-5">


    <?php if (!empty($search)): ?>

        <h2 class="mb-4">

            Search Results For:

            <strong>

                <?= htmlspecialchars($search) ?>

            </strong>

        </h2>


    <?php else: ?>

        <h2 class="mb-4">

            All Products

        </h2>


    <?php endif; ?>


    <div class="row">


        <?php if ($result && $result->num_rows > 0): ?>


            <?php while ($row = $result->fetch_assoc()): ?>


                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">


                    <div class="card shadow h-100">


                        <?php if (!empty($row['image'])): ?>


                            <img

                                src="assets/uploads/products/<?= htmlspecialchars($row['image']) ?>"

                                class="card-img-top"

                                style="height:220px; object-fit:cover;"

                                alt="<?= htmlspecialchars($row['product_name']) ?>">


                        <?php else: ?>


                            <img

                                src="https://via.placeholder.com/300x220?text=No+Image"

                                class="card-img-top">


                        <?php endif; ?>


                        <div class="card-body">


                            <small class="text-muted">

                                <?= htmlspecialchars($row['category_name']) ?>

                            </small>


                            <h5 class="mt-2">

                                <?= htmlspecialchars($row['product_name']) ?>

                            </h5>


                            <h4 class="text-primary">

                                ৳<?= number_format($row['price'], 2) ?>

                            </h4>


                            <p>

                                Stock:

                                <?= $row['quantity'] ?>

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


            <?php endwhile; ?>


        <?php else: ?>


            <div class="col-12">


                <div class="alert alert-warning">

                    No products found.

                </div>


            </div>


        <?php endif; ?>


    </div>


</div>


<?php

include "includes/footer.php";

?>