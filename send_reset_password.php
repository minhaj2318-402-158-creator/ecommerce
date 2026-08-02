<?php

require_once "config/config.php";
require_once "config/mail.php";

if (!isset($_POST['reset'])) {
    header("Location: forgot_password.php");
    exit();
}

$email = trim($_POST['email']);

$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Email not found.");
}

$user = $result->fetch_assoc();

// Generate Reset Token
$token = bin2hex(random_bytes(32));

// Token Expiry (1 Hour)
$expire = date("Y-m-d H:i:s", strtotime("+1 hour"));

// Save Token
$update = $conn->prepare("
UPDATE users
SET reset_token=?,
reset_expire=?
WHERE id=?
");

$update->bind_param(
    "ssi",
    $token,
    $expire,
    $user['id']
);

$update->execute();

// Reset Link
$link = "http://localhost/ecommerce/reset_password.php?token=".$token;

try{

    $mail = getMailer();

    $mail->addAddress($user['email'],$user['name']);

    $mail->Subject = "Reset Password";

    $mail->Body = "

    <h2>Hello {$user['name']}</h2>

    <p>Click the button below to reset your password.</p>

    <a href='{$link}'
    style='padding:12px 20px;
    background:#dc3545;
    color:white;
    text-decoration:none;
    border-radius:5px;'>

    Reset Password

    </a>

    <br><br>

    <small>

    This link will expire in 1 hour.

    </small>

    ";

    $mail->send();

    echo "

    <div style='margin:40px;font-family:Arial;'>

    <h2 style='color:green;'>

    Reset Email Sent

    </h2>

    <p>

    Please check your email.

    </p>

    <a href='login.php'>

    Back to Login

    </a>

    </div>

    ";

}catch(Exception $e){

    echo $mail->ErrorInfo;

}