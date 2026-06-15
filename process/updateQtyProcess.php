<?php
session_start();
require 'connection.php';

if (!isset($_SESSION["u"])) {
    echo "Please login first";
    exit();
}

if (isset($_POST['id']) && isset($_POST['qty'])) {
    $product_id = intval($_POST['id']);
    $new_qty = intval($_POST['qty']);
    $user_id = $_SESSION["u"]["user_id"];
    
    if ($new_qty < 1) {
        echo "Quantity cannot be less than 1";
        exit();
    }
    
    $stock_rs = Database::search("SELECT `qty`, `price` FROM `product` WHERE `product_id` = '$product_id'");
    if ($stock_rs->num_rows == 0) {
        echo "Product not found";
        exit();
    }
    
    $stock_data = $stock_rs->fetch_assoc();
    if ($new_qty > $stock_data['qty']) {
        echo "Only " . $stock_data['qty'] . " items available";
        exit();
    }
    
    Database::iud("UPDATE `cart` SET `order_qty` = '$new_qty' WHERE `user_id` = '$user_id' AND `product_id` = '$product_id'");
    
    $new_total = $stock_data['price'] * $new_qty;
    
    // Return as pipe-separated values
    echo "success|" . $new_qty . "|" . number_format($new_total, 2);
    exit();
} else {
    echo "Invalid request";
    exit();
}
?>