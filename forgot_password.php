<?php
require_once "config/config.php";

$message = "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Forgot Password</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-5">

<div class="card shadow">

<div class="card-header bg-warning">

<h3>Forgot Password</h3>

</div>

<div class="card-body">

<?= $message ?>

<form action="send_reset_password.php" method="POST">

<div class="mb-3">

<label>Email Address</label>

<input
type="email"
name="email"
class="form-control"
required>

</div>

<button
type="submit"
name="reset"
class="btn btn-warning w-100">

Send Reset Link

</button>

</form>

<hr>

<div class="text-center">

<a href="login.php">

Back to Login

</a>

</div>

</div>

</div>

</div>

</div>

</div>

</body>

</html>