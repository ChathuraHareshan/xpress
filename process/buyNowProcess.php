<?php
session_start();
include "connection.php";

if (!isset($_SESSION["u"])) {
    echo "1";
    exit();
}

$user_id = $_SESSION["u"]["user_id"];
$user_email = $_SESSION["u"]["email"];
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$qty = isset($_GET['qty']) ? intval($_GET['qty']) : 1;

if ($product_id <= 0) {
    echo "Invalid product";
    exit();
}


$product_rs = Database::search("SELECT * FROM `product` WHERE `product_id` = '$product_id'");
if ($product_rs->num_rows == 0) {
    echo "Product not found";
    exit();
}

$product_data = $product_rs->fetch_assoc();


$address_rs = Database::search("SELECT * FROM `address` WHERE `user_email` = '$user_email'");
if ($address_rs->num_rows == 0) {
    echo "2"; 
    exit();
}

$address_data = $address_rs->fetch_assoc();


$district_rs = Database::search("SELECT * FROM `district` WHERE `district_id` = '" . $address_data["district_id"] . "'");
$district_data = $district_rs->fetch_assoc();


$delivery_fee = ($district_data["district_name"] == "Colombo") ? 300 : 500;
$amount = ($product_data["price"] * $qty) + $delivery_fee;
$order_id = uniqid();


$user_rs = Database::search("SELECT * FROM `user` WHERE `user_id` = '$user_id'");
$user_data = $user_rs->fetch_assoc();


$payment_data = array(
    "id" => $order_id,
    "item" => $product_data["title"],
    "amount" => $amount,
    "fname" => $user_data["fname"],
    "lname" => $user_data["lname"],
    "mobile" => $user_data["mobile"],
    "address" => $address_data["line1"] . ", " . $address_data["line2"],
    "city" => $address_data["city"],
    "umail" => $user_email,
    "product_id" => $product_id,
    "qty" => $qty,
    "delivery_fee" => $delivery_fee
);


$hash = PayHereConfig::generateHash($order_id, $amount);
$payment_data["hash"] = $hash;
$payment_data["merchant_id"] = PayHereConfig::getMerchantId();

echo json_encode($payment_data);
?>