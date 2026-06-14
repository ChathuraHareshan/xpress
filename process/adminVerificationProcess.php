<?php

include "connection.php";

require "SMTP.php";
require "PHPMailer.php";
require "Exception.php";

use PHPMailer\PHPMailer\PHPMailer;

if(isset($_POST["e"])){

    $email = $_POST["e"];

    $admin_rs = Database::search("SELECT * FROM `admin` WHERE `email`='".$email."'");
    $admin_num = $admin_rs->num_rows;

    if($admin_num > 0){

        $code = uniqid();

        Database::iud("UPDATE `admin` SET `vcode`='".$code."' WHERE `email`='".$email."'");

        // email code
        $mail = new PHPMailer;
        $mail->IsSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'chathura.hareshan1432@gmail.com';
        $mail->Password = 'ipcppaqhteeazbka';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->setFrom('chathura.hareshan1432@gmail.com', 'Cinex.lk Community');
        $mail->addReplyTo('chathura.hareshan1432@gmail.com', 'Cinex.lk Community');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Xpress store Admin Verification Code';
        $bodyContent = '<h1 style="color:red;font-family: Segoe UI;">Your Verification Code Is : '.$code.'</h1>';
        $mail->Body    = $bodyContent;

        if(!$mail->send()){
           echo ("Email sent Failed.");
        }else{
           echo ("Success");
        }

    }else{
        echo ("You are not a valid Admin User.");
    }

}else{
    echo ("Please enter your Email address.");
}

?>