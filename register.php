<?php
require_once "config/config.php";

$message = "";

if(isset($_POST['register']))
{
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm_password']);

    if(empty($name) || empty($email) || empty($password) || empty($confirm))
    {
        $message = "<div class='alert alert-danger'>All fields are required.</div>";
    }
    elseif(!filter_var($email,FILTER_VALIDATE_EMAIL))
    {
        $message = "<div class='alert alert-danger'>Invalid Email Address.</div>";
    }
    elseif($password != $confirm)
    {
        $message = "<div class='alert alert-danger'>Passwords do not match.</div>";
    }
    else
    {
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s",$email);
        $check->execute();
        $check->store_result();

        if($check->num_rows>0)
        {
            $message = "<div class='alert alert-warning'>Email already exists.</div>";
        }
        else
        {
            $hash = password_hash($password,PASSWORD_DEFAULT);

            $token = bin2hex(random_bytes(32));

            $insert = $conn->prepare("
                INSERT INTO users
                (name,email,password,verify_token)
                VALUES(?,?,?,?)
            ");

            $insert->bind_param(
                "ssss",
                $name,
                $email,
                $hash,
                $token
            );

            if($insert->execute())
            {
                $_SESSION['verify_email']=$email;

                header("Location: send_verification.php");
                exit();
            }
            else
            {
                $message="<div class='alert alert-danger'>Registration Failed.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Register</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h3>Create Account</h3>

</div>

<div class="card-body">

<?= $message ?>

<form method="POST">

<div class="mb-3">

<label>Name</label>

<input
type="text"
name="name"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Email</label>

<input
type="email"
name="email"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Password</label>

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
name="register"
class="btn btn-primary w-100">

Register

</button>

</form>

<hr>

<p class="text-center">

Already have an account?

<a href="login.php">

Login

</a>

</p>

</div>

</div>

</div>

</div>

</div>

</body>

</html>