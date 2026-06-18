<?php
session_start();
include "connection.php";

if (!isset($_SESSION["au"])) {
    echo json_encode(["success" => false, "message" => "Please login first."]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit();
}

$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

$allowed = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
if ($order_id <= 0 || !in_array($status, $allowed)) {
    echo json_encode(["success" => false, "message" => "Invalid order or status"]);
    exit();
}

$check = Database::search("SELECT * FROM `orders` WHERE `history_id` = '$order_id'");
if ($check->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "Order not found"]);
    exit();
}

Database::iud("UPDATE `orders` SET `status` = '$status' WHERE `history_id` = '$order_id'");
echo json_encode(["success" => true, "status" => $status]);
