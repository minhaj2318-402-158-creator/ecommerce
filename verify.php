<?php

require_once "config/config.php";

if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("Invalid Verification Link.");
}

$token = trim($_GET['token']);

$stmt = $conn->prepare("SELECT id, is_verified FROM users WHERE verify_token=? LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Invalid or Expired Verification Link.");
}

$user = $result->fetch_assoc();

if ($user['is_verified'] == 1) {
    echo "
    <div style='font-family:Arial;margin:40px'>
        <h2 style='color:blue'>Email Already Verified</h2>
        <a href='login.php'>Login Now</a>
    </div>";
    exit();
}

$update = $conn->prepare("
    UPDATE users
    SET is_verified = 1,
        verify_token = NULL
    WHERE id = ?
");

$update->bind_param("i", $user['id']);

if ($update->execute()) {

    echo "
    <div style='font-family:Arial;margin:40px'>

        <h2 style='color:green'>
            Email Verified Successfully
        </h2>

        <p>Your account is now active.</p>

        <a class='btn btn-primary' href='login.php'>
            Login Now
        </a>

    </div>";

} else {

    echo "Verification Failed.";

}
?>