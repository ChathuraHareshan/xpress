<?php

include __DIR__ . "/connection.php";

$fname = $_POST["f"];
$lname = $_POST["l"];
$email = $_POST["e"];
$mobile = $_POST["m"];
$password = $_POST["p"];
$line1 = $_POST["l1"];
$line2 = $_POST["l2"];
$city = $_POST["c"];
$district = $_POST["d"];

if(empty($fname)){
    echo ("Please enter your First Name.");
}else if (strlen($fname) > 45){
    echo ("First Name must contain LOWER THAN 45 Characters.");
}else if (empty($lname)){
    echo ("Please enter your Last Name.");
}else if (strlen($lname) > 45){
    echo ("Last Name must contain LOWER THAN 45 Characters.");
}else if (empty($email)){
    echo ("Please enter your Email Address.");
}else if (strlen($email) > 100){
    echo ("Email Address must contain LOWER THAN 100 Characters.");
}else if (!filter_var($email,FILTER_VALIDATE_EMAIL)){
    echo ("Invalid Email Address.");
}else if (empty($password)){
    echo ("Please enter your Password.");
}else if (strlen($password) < 5 || strlen($password) > 20){
    echo ("The password must contain 5 to 20 characters.");
}else if (empty($mobile)){
    echo ("Please enter your Mobile Number.");
}else if (strlen($mobile) != 10){
    echo ("Mobile Number must contain 10 Characters.");
}else if (!preg_match("/07[0,1,2,4,5,6,7,8]{1}[0-9]{7}/",$mobile)){
    echo ("Invalid Mobile Number.");
}else if (empty($line1)){
    echo ("Please enter your Address Line 1.");
}else if (strlen($line1) > 100){
    echo ("Address Line 1 must contain LOWER THAN 100 Characters.");
}else if (strlen($line2) > 100){
    echo ("Address Line 2 must contain LOWER THAN 100 Characters.");
}else if (empty($city)){
    echo ("Please enter your City.");
}else if (strlen($city) > 45){
    echo ("City must contain LOWER THAN 45 Characters.");
}else if (empty($district)){
    echo ("Please select your District.");
}
else{

    $rs = Database::search("SELECT * FROM `user` WHERE `email`='".$email."' OR `mobile`='".$mobile."'");
    $n = $rs->num_rows;

    if($n > 0){
        echo ("User with the same Email Address or Mobile Number already exists.");
    }else{

        $d = new DateTime();
        $tz = new DateTimeZone("Asia/Colombo");
        $d->setTimezone($tz);
        $date = $d->format("Y-m-d H:i:s");

        Database::iud("INSERT INTO `user`
        (`fname`,`lname`,`email`,`password`,`mobile`,`joined_date`,`status_id`) VALUES 
        ('".$fname."','".$lname."','".$email."','".$password."','".$mobile."','".$date."','1')");

        Database::iud("INSERT INTO `address` (`user_email`,`line1`,`line2`,`city`,`district_id`) VALUES
        ('".$email."','".$line1."','".$line2."','".$city."','".$district."')");

        echo ("success");

    }

}

?>