<?php
session_start();
require 'connection.php';

if (!isset($_SESSION["u"])) {
    echo "Please login first";
    exit();
}

if (isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $user_id = $_SESSION["u"]["user_id"];
    
    Database::iud("DELETE FROM `cart` WHERE `user_id` = '$user_id' AND `product_id` = '$product_id'");
    echo "success";
} else {
    echo "Invalid request";
}
?>