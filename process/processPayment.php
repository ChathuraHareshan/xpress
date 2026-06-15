<?php
session_start();
include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["payment"])) {
    
    $payment = json_decode($_POST["payment"], true);
    
    $order_id = $payment["order_id"];
    $amount = $payment["amount"];
    $user_id = $_SESSION["u"]["user_id"];
    $payment_method = $payment["payment_method"] ?? "PayHere";
    $status = $payment["status"] ?? "pending";
    

    $date = new DateTime();
    $date->setTimezone(new DateTimeZone("Asia/Colombo"));
    $order_date = $date->format("Y-m-d H:i:s");
    

    Database::iud("INSERT INTO `orders` (`order_id`, `order_date`, `amount`, `user_id`, `payment_method`, `status`) 
                   VALUES ('$order_id', '$order_date', '$amount', '$user_id', '$payment_method', '$status')");
    
    $order_history_id = Database::$connection->insert_id;
    

    if (isset($payment["cart"]) && $payment["cart"] == "true") {

    $cart_rs = Database::search("SELECT * FROM `cart` WHERE `user_id` = '$user_id'");
        
        while ($cart_data = $cart_rs->fetch_assoc()) {
            // Insert order items
            Database::iud("INSERT INTO `order_items` (`order_qty`, `order_id`, `product_id`) 
                           VALUES ('" . $cart_data["order_qty"] . "', '$order_history_id', '" . $cart_data["product_id"] . "')");
            
            // Update product stock
            $product_rs = Database::search("SELECT `qty` FROM `product` WHERE `product_id` = '" . $cart_data["product_id"] . "'");
            $product_data = $product_rs->fetch_assoc();
            $new_qty = $product_data["qty"] - $cart_data["order_qty"];
            Database::iud("UPDATE `product` SET `qty` = '$new_qty' WHERE `product_id` = '" . $cart_data["product_id"] . "'");
        }
        
        
        Database::iud("DELETE FROM `cart` WHERE `user_id` = '$user_id'");
        
    } else {
       
        $product_id = $payment["product_id"];
        $qty = $payment["qty"];
        
        Database::iud("INSERT INTO `order_items` (`order_qty`, `order_id`, `product_id`) 
                       VALUES ('$qty', '$order_history_id', '$product_id')");
        
        
        $product_rs = Database::search("SELECT `qty` FROM `product` WHERE `product_id` = '$product_id'");
        $product_data = $product_rs->fetch_assoc();
        $new_qty = $product_data["qty"] - $qty;
        Database::iud("UPDATE `product` SET `qty` = '$new_qty' WHERE `product_id` = '$product_id'");
    }
    
    $response = array(
        "status" => "success",
        "order_id" => $order_history_id,
        "message" => "Order placed successfully"
    );
    
    echo json_encode($response);
} else {
    echo "Invalid request";
}
?>