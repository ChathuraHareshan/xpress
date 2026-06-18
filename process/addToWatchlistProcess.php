<?php
session_start();
include "connection.php";

if (!isset($_SESSION["u"])) {
    echo "Please log in to add items to your watchlist.";
    exit();
}

$user_id = $_SESSION["u"]["user_id"];
$productId = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($productId <= 0) {
    echo "Invalid product ID.";
    exit();
}


$check_product = Database::search("SELECT * FROM `product` WHERE `product_id` = '$productId' AND `qty` > 0");
if ($check_product->num_rows == 0) {
    echo "Product is out of stock or doesn't exist.";
    exit();
}



$check_watchlist = Database::search("SELECT * FROM `watchlist` WHERE `user_id` = '$user_id' AND `product_id` = '$productId'");

if ($check_watchlist->num_rows > 0) {

    Database::iud("DELETE FROM `watchlist` WHERE `user_id` = '$user_id' AND `product_id` = '$productId'");
    echo "removed";
} else {
    // Add to watchlist
    Database::iud("INSERT INTO `watchlist` (`user_id`, `product_id`) VALUES ('$user_id', '$productId')");
    echo "added";
}
