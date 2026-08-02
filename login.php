<?php

require_once "config/config.php";

$message = "";

if(isset($_POST['login']))
{
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if(empty($email) || empty($password))
    {
        $message = "<div class='alert alert-danger'>
        Email and Password are required.
        </div>";
    }
    else
    {
        $stmt = $conn->prepare("
        SELECT id,name,email,password,is_verified
        FROM users
        WHERE email=?
        LIMIT 1
        ");

        $stmt->bind_param("s",$email);
        $stmt->execute();

        $result = $stmt->get_result();

        if($result->num_rows==1)
        {
            $user = $result->fetch_assoc();

            if($user['is_verified']==0)
            {
                $message="<div class='alert alert-warning'>
                Please verify your email first.
                </div>";
            }
            elseif(password_verify($password,$user['password']))
            {
                $_SESSION['user_id']=$user['id'];
                $_SESSION['user_name']=$user['name'];
                $_SESSION['user_email']=$user['email'];

                header("Location:index.php");
                exit();
            }
            else
            {
                $message="<div class='alert alert-danger'>
                Incorrect Password.
                </div>";
            }
        }
        else
        {
            $message="<div class='alert alert-danger'>
            Email not found.
            </div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>User Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-5">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h3>User Login</h3>

</div>

<div class="card-body">

<?= $message ?>

<form method="POST">

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

<button
type="submit"
name="login"
class="btn btn-success w-100">

Login

</button>

</form>

<hr>

<div class="text-center">

<a href="forgot_password.php">

Forgot Password?

</a>

<br><br>

Don't have an account?

<a href="register.php">

Register

</a>

</div>

</div>

</div>

</div>

</div>

</div>

</body>

</html>