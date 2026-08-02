<?php
session_start();

require_once "config/config.php";

if(!isset($_POST['coupon_code'])){
    header("Location: cart.php");
    exit();
}

$coupon_code = strtoupper(trim($_POST['coupon_code']));

// Coupon খুঁজে বের করা
$stmt = $conn->prepare("
SELECT *
FROM coupons
WHERE coupon_code=? AND status=1
LIMIT 1
");

$stmt->bind_param("s", $coupon_code);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){

    $_SESSION['coupon_error'] = "Invalid coupon code.";

    header("Location: cart.php");
    exit();
}

$coupon = $result->fetch_assoc();

// Expiry Check
if(strtotime($coupon['expiry_date']) < strtotime(date("Y-m-d"))){

    $_SESSION['coupon_error'] = "Coupon has expired.";

    header("Location: cart.php");
    exit();
}

// Cart Total বের করা
$grandTotal = 0;

if(isset($_SESSION['cart'])){

    foreach($_SESSION['cart'] as $item){

        $grandTotal += $item['price'] * $item['quantity'];

    }

}

// Minimum Order Check
if($grandTotal < $coupon['minimum_amount']){

    $_SESSION['coupon_error'] =
    "Minimum order amount is ৳".$coupon['minimum_amount'];

    header("Location: cart.php");
    exit();
}

// Coupon Session Save
$_SESSION['coupon'] = [

    'id'    => $coupon['id'],
    'code'  => $coupon['coupon_code'],
    'type'  => strtolower($coupon['coupon_type']),
    'value' => $coupon['discount']

];

$_SESSION['coupon_success'] =
"Coupon applied successfully.";

header("Location: cart.php");
exit();