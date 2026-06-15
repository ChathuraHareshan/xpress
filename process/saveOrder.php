<?php
session_start();
include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["u"]["user_id"];
    $order_id = $_POST["order_id"];
    $amount = $_POST["amount"];
    $type = $_POST["type"];
    
    $date = new DateTime();
    $date->setTimezone(new DateTimeZone("Asia/Colombo"));
    $order_date = $date->format("Y-m-d H:i:s");
    

    Database::iud("INSERT INTO `orders` (`order_id`, `order_date`, `amount`, `user_id`, `status`) 
                   VALUES ('$order_id', '$order_date', '$amount', '$user_id', 'completed')");
    
    $db_order_id = Database::$connection->insert_id;
    
    if ($type == "cart") {

    $cart_rs = Database::search("SELECT * FROM `cart` WHERE `user_id` = '$user_id'");
        
        while ($cart_data = $cart_rs->fetch_assoc()) {

        Database::iud("INSERT INTO `order_items` (`order_qty`, `order_history_id`, `product_id`) 
                           VALUES ('" . $cart_data["order_qty"] . "', '$db_order_id', '" . $cart_data["product_id"] . "')");
            

            $product_rs = Database::search("SELECT `qty` FROM `product` WHERE `product_id` = '" . $cart_data["product_id"] . "'");
            $product_data = $product_rs->fetch_assoc();
            $new_qty = $product_data["qty"] - $cart_data["order_qty"];
            Database::iud("UPDATE `product` SET `qty` = '$new_qty' WHERE `product_id` = '" . $cart_data["product_id"] . "'");
        }
        

        Database::iud("DELETE FROM `cart` WHERE `user_id` = '$user_id'");
    }
    
    echo json_encode(array("status" => "success", "order_id" => $db_order_id));
}
?>