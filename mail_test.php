<?php

require_once "config/mail.php";

try {

    $mail = getMailer();

    // enter the recipient's email address
    $mail->addAddress("minhaj2318-402-158@bsdi-bd.org");

    $mail->Subject = "PHPMailer Test";

    $mail->Body = "<h2>PHPMailer Working Successfully</h2>";

    $mail->send();

    echo "<h2 style='color:green'>Mail Sent Successfully</h2>";

} catch (Exception $e) {

    echo "<h2 style='color:red'>Mail Failed</h2>";
    echo $mail->ErrorInfo;
}