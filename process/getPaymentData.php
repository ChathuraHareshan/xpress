<?php
session_start();

include "connection.php";
include "payhereConfig.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["u"]["user_id"];
    $email = $_SESSION["u"]["email"];
    $user_data = $_SESSION["u"];
    
    $order_id = $_POST["order_id"];
    $amount = $_POST["amount"];
    $items = $_POST["items"];
    

    $address_rs = Database::search("SELECT * FROM `address` WHERE `user_email` = '$email'");
    $address_data = $address_rs->fetch_assoc();
    

    $hash = PayHereConfig::generateHash($order_id, $amount);
    
    $response = array(
        "status" => "success",
        "merchant_id" => PayHereConfig::getMerchantId(),
        "order_id" => $order_id,
        "items" => $items,
        "amount" => number_format($amount, 2, '.', ''),
        "hash" => $hash,
        "first_name" => $user_data["fname"],
        "last_name" => $user_data["lname"],
        "email" => $email,
        "phone" => $user_data["mobile"],
        "address" => $address_data["line1"] . ", " . $address_data["line2"],
        "city" => $address_data["city"]
    );
    
    echo json_encode($response);
}
?>