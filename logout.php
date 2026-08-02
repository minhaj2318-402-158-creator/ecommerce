<?php

require_once "config/config.php";

// Unset All Session Variables
$_SESSION = [];

// Session Destroy
session_destroy();

// Redirect to Login Page
header("Location: login.php");
exit();