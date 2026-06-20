<?php
session_start();
include "connection.php";

if (!isset($_SESSION["u"])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../profile.php?error=Invalid request");
    exit();
}

$user_id = $_SESSION["u"]["user_id"];
$old_email = trim($_POST["old_email"] ?? "");

$fname = trim($_POST["fname"] ?? "");
$lname = trim($_POST["lname"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");

$line1 = trim($_POST["line1"] ?? "");
$line2 = trim($_POST["line2"] ?? "");
$city = trim($_POST["city"] ?? "");
$district = trim($_POST["district"] ?? "");

if (!empty($fname) && !empty($lname) && !empty($email) && !empty($mobile)) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../profile.php?error=Please enter a valid email address");
        exit();
    }

    if (!preg_match('/^07[0,1,2,4,5,6,7,8][0-9]{7}$/', $mobile)) {
        header("Location: ../profile.php?error=Please enter a valid mobile number");
        exit();
    }

    $check_rs = Database::search("SELECT * FROM `user` WHERE (`email` = '$email' OR `mobile` = '$mobile') AND `user_id` != '$user_id'");
    if ($check_rs->num_rows > 0) {
        header("Location: ../profile.php?error=Email or mobile number already used by another account");
        exit();
    }

    Database::iud("UPDATE `user` SET `fname` = '$fname', `lname` = '$lname', `email` = '$email', `mobile` = '$mobile' WHERE `user_id` = '$user_id'");

    $_SESSION["u"]["fname"] = $fname;
    $_SESSION["u"]["lname"] = $lname;
    $_SESSION["u"]["email"] = $email;
    $_SESSION["u"]["mobile"] = $mobile;
}

if (!empty($old_email) && !empty($line1) && !empty($city) && !empty($district)) {
    $address_rs = Database::search("SELECT * FROM `address` WHERE `user_email` = '$old_email'");
    if ($address_rs->num_rows > 0) {
        Database::iud(
            "UPDATE `address` SET `user_email` = '$email', `line1` = '$line1', `line2` = '$line2', `city` = '$city', `district_id` = '$district' WHERE `user_email` = '$old_email'"
        );
    } else {
        Database::iud(
            "INSERT INTO `address` (`user_email`, `line1`, `line2`, `city`, `district_id`) VALUES ('$email', '$line1', '$line2', '$city', '$district')"
        );
    }
}

header("Location: ../profile.php?success=1");
exit();
?>
