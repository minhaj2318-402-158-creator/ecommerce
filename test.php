<?php

require_once "config/config.php";

if (isset($conn) && $conn instanceof mysqli) {
    echo "<h2 style='color:green'>Database Connected Successfully</h2>";
} else {
    echo "<h2 style='color:red'>Database Connection Failed</h2>";
}