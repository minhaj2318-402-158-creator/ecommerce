<?php

require_once "config/config.php";

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    header("Location: cart.php");
    exit();
}

$total = 0;

foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

$message = "";

if (isset($_POST['place_order'])) {

    $customer_name    = trim($_POST['customer_name']);
    $customer_email   = trim($_POST['customer_email']);
    $customer_phone   = trim($_POST['customer_phone']);
    $customer_address = trim($_POST['customer_address']);

    if (
        empty($customer_name) ||
        empty($customer_email) ||
        empty($customer_phone) ||
        empty($customer_address)
    ) {

        $message = "<div class='alert alert-danger'>
                        All fields are required.
                    </div>";

    } else {

        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

        $conn->begin_transaction();

        try {

            $order = $conn->prepare("
                INSERT INTO orders
                (
                    user_id,
                    customer_name,
                    customer_email,
                    customer_phone,
                    customer_address,
                    total_amount
                )
                VALUES
                (?,?,?,?,?,?)
            ");

            $order->bind_param(
                "issssd",
                $user_id,
                $customer_name,
                $customer_email,
                $customer_phone,
                $customer_address,
                $total
            );

            $order->execute();

            $order_id = $conn->insert_id;

            $itemStmt = $conn->prepare("
                INSERT INTO order_items
                (
                    order_id,
                    product_id,
                    product_name,
                    price,
                    quantity,
                    subtotal
                )
                VALUES
                (?,?,?,?,?,?)
            ");

            foreach ($_SESSION['cart'] as $cart) {

                $subtotal = $cart['price'] * $cart['quantity'];

                $itemStmt->bind_param(
                    "iisdid",
                    $order_id,
                    $cart['id'],
                    $cart['name'],
                    $cart['price'],
                    $cart['quantity'],
                    $subtotal
                );

                $itemStmt->execute();

                // Stock Update
                $updateStock = $conn->prepare("
                    UPDATE products
                    SET quantity = quantity - ?
                    WHERE id = ?
                ");

                $updateStock->bind_param(
                    "ii",
                    $cart['quantity'],
                    $cart['id']
                );

                $updateStock->execute();
            }

            $conn->commit();

            $_SESSION['last_order'] = $order_id;

            $_SESSION['cart'] = [];

            header("Location: order-success.php");

            exit();

        } catch (Exception $e) {

            $conn->rollback();

            $message = "<div class='alert alert-danger'>
                            Order Failed.
                        </div>";
        }

    }

}

include "includes/header.php";
include "includes/navbar.php";
?>
<div class="container mt-5">

    <div class="row">

        <div class="col-md-7">

            <div class="card shadow">

                <div class="card-header bg-primary text-white">

                    <h4>Checkout</h4>

                </div>

                <div class="card-body">

                    <?= $message ?>

                    <form method="POST">

                        <div class="mb-3">

                            <label>Full Name</label>

                            <input
                            type="text"
                            name="customer_name"
                            class="form-control"
                            required>

                        </div>

                        <div class="mb-3">

                            <label>Email</label>

                            <input
                            type="email"
                            name="customer_email"
                            class="form-control"
                            required>

                        </div>

                        <div class="mb-3">

                            <label>Phone</label>

                            <input
                            type="text"
                            name="customer_phone"
                            class="form-control"
                            required>

                        </div>

                        <div class="mb-3">

                            <label>Address</label>

                            <textarea
                            name="customer_address"
                            class="form-control"
                            rows="4"
                            required></textarea>

                        </div>

                        <button
                        type="submit"
                        name="place_order"
                        class="btn btn-success">

                        Place Order

                        </button>

                    </form>

                </div>

            </div>

        </div>

        <div class="col-md-5">

            <div class="card shadow">

                <div class="card-header bg-dark text-white">

                    <h4>Order Summary</h4>

                </div>

                <div class="card-body">

                    <table class="table">

                        <thead>

                        <tr>

                            <th>Product</th>

                            <th>Total</th>

                        </tr>

                        </thead>

                        <tbody>

                        <?php

                        $grandTotal = 0;

                        foreach($_SESSION['cart'] as $item){

                            $subTotal = $item['price'] * $item['quantity'];

                            $grandTotal += $subTotal;

                        ?>

                        <tr>

                            <td>

                                <?= htmlspecialchars($item['name']) ?>

                                <br>

                                <small>

                                    Qty : <?= $item['quantity'] ?>

                                </small>

                            </td>

                            <td>

                                ৳<?= number_format($subTotal,2) ?>

                            </td>

                        </tr>

                        <?php } ?>

                        </tbody>

                        <tfoot>

                        <tr>

                            <th>Grand Total</th>

                            <th>

                                ৳<?= number_format($grandTotal,2) ?>

                            </th>

                        </tr>

                        </tfoot>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>