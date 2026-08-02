<?php

require_once "config/config.php";

// =========================
// Change These Details
// =========================

$name = "Administrator";
$email = "admin@gmail.com";
$password = "admin123";

// =========================

$check = $conn->prepare("SELECT id FROM admins WHERE email=?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if($check->num_rows > 0)
{
    die("Admin already exists.");
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("
INSERT INTO admins
(name,email,password)
VALUES(?,?,?)
");

$stmt->bind_param(
    "sss",
    $name,
    $email,
    $hash
);

if($stmt->execute())
{
    echo "<h2 style='color:green'>Admin Created Successfully</h2>";

    echo "<hr>";

    echo "<strong>Email:</strong> ".$email."<br>";

    echo "<strong>Password:</strong> ".$password;
}
else
{
    echo "Failed.";
}