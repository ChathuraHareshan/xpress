<?php
session_start();
include "connection.php";

if (!isset($_SESSION["au"])) {
    echo "Please login first.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo "Invalid request";
    exit();
}

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$title = $_POST['title'] ?? '';
$qty = $_POST['qty'] ?? 0;
$description = $_POST['description'] ?? '';
$color = $_POST['color'] ?? 0;
$storage = $_POST['storage'] ?? 0;
$price = $_POST['price'] ?? 0;

if ($product_id <= 0 || $title == '' || $qty == '' || $description == '' || $color == 0 || $storage == 0) {
    echo "Please fill all required fields.";
    exit();
}

Database::iud("UPDATE `product` SET 
    `title` = '" . $title . "',
    `description` = '" . $description . "',
    `color_id` = '" . $color . "',
    `qty` = '" . $qty . "',
    `price` = '" . $price . "',
    `storage_id` = '" . $storage . "'
    WHERE `product_id` = '" . $product_id . "'");

$base_path = dirname(dirname(__FILE__)) . "/resources/";
if (!file_exists($base_path)) {
    mkdir($base_path, 0777, true);
}

if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
    foreach ($_POST['delete_images'] as $delete_img_id) {
        $delete_img_id = (int)$delete_img_id;
        $img_check = Database::search("SELECT * FROM `product_img` WHERE `img_id` = '$delete_img_id' AND `product_id` = '$product_id'");
        if ($img_check->num_rows > 0) {
            $img_data = $img_check->fetch_assoc();
            $img_path = dirname(dirname(__FILE__)) . '/' . $img_data['path'];
            if (file_exists($img_path)) {
                unlink($img_path);
            }
            Database::iud("DELETE FROM `product_img` WHERE `img_id` = '$delete_img_id'");
        }
    }
}

$new_uploaded = false;
if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
    $allowed_types = array("image/jpeg", "image/png", "image/svg+xml");
    for ($x = 0; $x < count($_FILES['images']['name']); $x++) {
        if ($_FILES['images']['error'][$x] == 0 && $_FILES['images']['size'][$x] > 0) {
            $img_type = $_FILES['images']['type'][$x];
            if (in_array($img_type, $allowed_types)) {
                $ext = '';
                if ($img_type == 'image/jpeg') {
                    $ext = '.jpeg';
                } elseif ($img_type == 'image/png') {
                    $ext = '.png';
                } elseif ($img_type == 'image/svg+xml') {
                    $ext = '.svg';
                }

                $filename = 'product_' . $product_id . '_' . $x . '_' . uniqid() . $ext;
                $full_path = $base_path . $filename;
                $db_path = 'resources/' . $filename;

                if (move_uploaded_file($_FILES['images']['tmp_name'][$x], $full_path)) {
                    Database::iud("INSERT INTO `product_img`(`path`, `product_id`) VALUES ('" . $db_path . "', '" . $product_id . "')");
                    $new_uploaded = true;
                }
            }
        }
    }
}

$remaining_img_rs = Database::search("SELECT * FROM `product_img` WHERE `product_id` = '$product_id'");
if ($remaining_img_rs->num_rows == 0 && !$new_uploaded) {
    echo "At least one image is required for the product.";
    exit();
}

header('Location: ../admin/manageProducts.php');
exit();

?>