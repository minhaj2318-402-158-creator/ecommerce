<?php

require_once "config/config.php";
require_once "config/mail.php";

if (!isset($_SESSION['verify_email'])) {
    header("Location: register.php");
    exit();
}

$email = $_SESSION['verify_email'];

// Find User
$stmt = $conn->prepare("SELECT id, name, email, verify_token FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();

// Verification Link
$verifyLink = "http://localhost/ecommerce/verify.php?token=" . $user['verify_token'];

try {

    $mail = getMailer();

    $mail->addAddress($user['email'], $user['name']);

    $mail->Subject = "Verify Your Email";

    $mail->Body = "
    <div style='font-family:Arial'>
        <h2>Welcome {$user['name']}</h2>

        <p>Thank you for registering.</p>

        <p>Please verify your email by clicking the button below.</p>

        <a href='{$verifyLink}'
        style='display:inline-block;
        padding:12px 25px;
        background:#0d6efd;
        color:#fff;
        text-decoration:none;
        border-radius:5px;'>

        Verify Email

        </a>

        <br><br>

        <small>
        If you did not create this account, ignore this email.
        </small>

    </div>
    ";

    $mail->send();

    unset($_SESSION['verify_email']);

    echo "
    <div style='font-family:Arial;margin:40px;'>

    <h2 style='color:green;'>Registration Successful</h2>

    <p>A verification email has been sent to:</p>

    <strong>{$email}</strong>

    <br><br>

    <a href='login.php'>Go to Login</a>

    </div>";

} catch (Exception $e) {

    echo "<h3>Email could not be sent.</h3>";

    echo $mail->ErrorInfo;

}