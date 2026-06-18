<?php
session_start();

include "connection.php";

if (!isset($_SESSION["u"])) {
    echo "Please log in to add items to your cart.";
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

$check_cart = Database::search("SELECT * FROM `cart` WHERE `user_id` = '$user_id' AND `product_id` = '$productId'");

if ($check_cart->num_rows > 0) {

Database::iud("UPDATE `cart` SET `order_qty` = `order_qty` + 1 WHERE `user_id` = '$user_id' AND `product_id` = '$productId'");
    echo "success";
} else {

Database::iud("INSERT INTO `cart` (`user_id`, `product_id`, `order_qty`) VALUES ('$user_id', '$productId', '1')");
    echo "success";
}
?>