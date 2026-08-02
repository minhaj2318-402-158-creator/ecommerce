<?php
session_start();

if(isset($_SESSION['coupon'])){

    unset($_SESSION['coupon']);

    $_SESSION['coupon_success'] = "Coupon removed successfully.";

}

header("Location: cart.php");
exit();