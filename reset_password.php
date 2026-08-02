<?php

require_once "config/config.php";

$message = "";

if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("Invalid Reset Link.");
}

$token = trim($_GET['token']);

$stmt = $conn->prepare("
SELECT id
FROM users
WHERE reset_token=?
AND reset_expire > NOW()
LIMIT 1
");

$stmt->bind_param("s", $token);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Reset link is invalid or expired.");
}

$user = $result->fetch_assoc();

if(isset($_POST['reset_password']))
{
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm_password']);

    if(empty($password) || empty($confirm))
    {
        $message = "<div class='alert alert-danger'>All fields are required.</div>";
    }
    elseif($password != $confirm)
    {
        $message = "<div class='alert alert-danger'>Passwords do not match.</div>";
    }
    elseif(strlen($password) < 6)
    {
        $message = "<div class='alert alert-danger'>Password must be at least 6 characters.</div>";
    }
    else
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $update = $conn->prepare("
        UPDATE users
        SET password=?,
            reset_token=NULL,
            reset_expire=NULL
        WHERE id=?
        ");

        $update->bind_param("si", $hash, $user['id']);

        if($update->execute())
        {
            echo "
            <div style='margin:40px;font-family:Arial;'>

            <h2 style='color:green'>
            Password Reset Successfully
            </h2>

            <a href='login.php'>
            Login Now
            </a>

            </div>";

            exit();
        }
        else
        {
            $message = "<div class='alert alert-danger'>Something went wrong.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<title>Reset Password</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-5">

<div class="card shadow">

<div class="card-header bg-danger text-white">

<h3>Reset Password</h3>

</div>

<div class="card-body">

<?= $message ?>

<form method="POST">

<div class="mb-3">

<label>New Password</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Confirm Password</label>

<input
type="password"
name="confirm_password"
class="form-control"
required>

</div>

<button
type="submit"
name="reset_password"
class="btn btn-danger w-100">

Reset Password

</button>

</form>

</div>

</div>

</div>

</div>

</div>

</body>

</html>