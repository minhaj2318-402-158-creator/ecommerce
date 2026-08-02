<?php

session_start();

require_once "config/config.php";


// ========================================
// INITIALIZE CART
// ========================================

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


// ========================================
// ADD TO CART
// ========================================

if (isset($_POST['add_to_cart'])) {

    $product_id = (int) $_POST['product_id'];
    $quantity   = (int) $_POST['quantity'];

    if ($quantity < 1) {
        $quantity = 1;
    }


    // Fetch product from database

    $stmt = $conn->prepare("
        SELECT
            id,
            product_name,
            price,
            quantity,
            image
        FROM products
        WHERE id = ?
        AND status = 'Active'
        LIMIT 1
    ");

    $stmt->bind_param("i", $product_id);

    $stmt->execute();

    $result = $stmt->get_result();


    // Check product

    if ($result->num_rows === 0) {

        die("Product not found.");

    }


    $product = $result->fetch_assoc();


    // Add product to cart

    if (isset($_SESSION['cart'][$product_id])) {

        $_SESSION['cart'][$product_id]['quantity'] += $quantity;

    } else {

        $_SESSION['cart'][$product_id] = [

            'id'       => $product['id'],

            'name'     => $product['product_name'],

            'price'    => $product['price'],

            'quantity' => $quantity,

            'image'    => $product['image']

        ];

    }


    // Prevent quantity from exceeding stock

    if (
        $_SESSION['cart'][$product_id]['quantity']
        > $product['quantity']
    ) {

        $_SESSION['cart'][$product_id]['quantity']
            = $product['quantity'];

    }


    // Redirect to cart

    header("Location: cart.php");

    exit();

}


// ========================================
// UPDATE CART
// ========================================

if (isset($_POST['update_cart'])) {

    if (isset($_POST['qty'])) {

        foreach ($_POST['qty'] as $id => $qty) {

            $id  = (int) $id;

            $qty = (int) $qty;


            if ($qty <= 0) {

                unset($_SESSION['cart'][$id]);

            } else {

                $_SESSION['cart'][$id]['quantity'] = $qty;

            }

        }

    }


    header("Location: cart.php");

    exit();

}


// ========================================
// REMOVE PRODUCT FROM CART
// ========================================

if (isset($_GET['remove'])) {

    $id = (int) $_GET['remove'];


    if (isset($_SESSION['cart'][$id])) {

        unset($_SESSION['cart'][$id]);

    }


    header("Location: cart.php");

    exit();

}


// ========================================
// HEADER & NAVBAR
// ========================================

include "includes/header.php";

include "includes/navbar.php";


$grandTotal = 0;

?>

<!-- ========================================
     SHOPPING CART
========================================= -->

<div class="container py-5">

```
<h2 class="mb-4">
    Shopping Cart
</h2>


<?php if (empty($_SESSION['cart'])): ?>


    <!-- EMPTY CART -->

    <div class="alert alert-warning">

        Your shopping cart is empty.

    </div>


    <a
        href="index.php"
        class="btn btn-primary">

        Continue Shopping

    </a>


<?php else: ?>


    <!-- CART TABLE -->

    <form method="POST">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">


                <!-- TABLE HEADER -->

                <thead class="table-dark">

                    <tr>

                        <th width="90">
                            Image
                        </th>

                        <th>
                            Product
                        </th>

                        <th width="130">
                            Price
                        </th>

                        <th width="120">
                            Quantity
                        </th>

                        <th width="150">
                            Subtotal
                        </th>

                        <th width="120">
                            Action
                        </th>

                    </tr>

                </thead>


                <!-- TABLE BODY -->

                <tbody>


                    <?php foreach ($_SESSION['cart'] as $item): ?>


                        <?php

                        $subtotal =
                            $item['price']
                            * $item['quantity'];

                        $grandTotal += $subtotal;

                        ?>


                        <tr>


                            <!-- PRODUCT IMAGE -->

                            <td>

                                <?php if (!empty($item['image'])): ?>


                                    <img

                                        src="assets/uploads/products/<?= htmlspecialchars($item['image']) ?>"

                                        width="70"

                                        height="70"

                                        class="rounded"

                                        style="object-fit: cover;"

                                        alt="<?= htmlspecialchars($item['name']) ?>">


                                <?php else: ?>


                                    <span class="text-muted">

                                        No Image

                                    </span>


                                <?php endif; ?>

                            </td>


                            <!-- PRODUCT NAME -->

                            <td>

                                <?= htmlspecialchars($item['name']) ?>

                            </td>


                            <!-- PRICE -->

                            <td>

                                ৳<?= number_format($item['price'], 2) ?>

                            </td>


                            <!-- QUANTITY -->

                            <td>

                                <input

                                    type="number"

                                    name="qty[<?= $item['id'] ?>]"

                                    value="<?= $item['quantity'] ?>"

                                    min="1"

                                    class="form-control">


                            </td>


                            <!-- SUBTOTAL -->

                            <td>

                                ৳<?= number_format($subtotal, 2) ?>

                            </td>


                            <!-- REMOVE -->

                            <td>

                                <a

                                    href="cart.php?remove=<?= $item['id'] ?>"

                                    class="btn btn-danger btn-sm"

                                    onclick="return confirm('Remove this product from cart?');">

                                    Remove

                                </a>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                </tbody>


                <!-- TABLE FOOTER -->

                <tfoot>

                    <tr>

                        <th
                            colspan="4"
                            class="text-end">

                            Grand Total

                        </th>


                        <th>

                            ৳<?= number_format($grandTotal, 2) ?>

                        </th>


                        <th></th>

                    </tr>

                </tfoot>


            </table>

        </div>


        <!-- CART ACTIONS -->

        <div class="row mt-4">


            <div class="col-md-6">


                <button

                    type="submit"

                    name="update_cart"

                    class="btn btn-warning">

                    Update Cart

                </button>


            </div>


            <div class="col-md-6 text-md-end mt-3 mt-md-0">


                <a

                    href="index.php"

                    class="btn btn-primary">

                    Continue Shopping

                </a>


                <a

                    href="checkout.php"

                    class="btn btn-success">

                    Proceed to Checkout

                </a>


            </div>


        </div>


    </form>


<?php endif; ?>
```

</div>

<?php

include "includes/footer.php";

?>
