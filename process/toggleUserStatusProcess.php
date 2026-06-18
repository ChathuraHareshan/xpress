<?php
session_start();
include "connection.php";

if (!isset($_SESSION["au"])) {
    echo "Please login first.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST['user_id'])) {
    echo "Invalid request";
    exit();
}

$user_id = (int)$_POST['user_id'];
$user_rs = Database::search("SELECT * FROM `user` WHERE `user_id` = '$user_id'");

if ($user_rs->num_rows == 0) {
    echo "User not found.";
    exit();
}

$user_data = $user_rs->fetch_assoc();
$new_status = ($user_data['status_id'] == 1) ? 2 : 1;

Database::iud("UPDATE `user` SET `status_id` = '$new_status' WHERE `user_id` = '$user_id'");

header("Location: ../admin/manageUsers.php");
exit();
