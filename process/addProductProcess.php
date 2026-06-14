<?php
session_start();
include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $qty = $_POST["qty"];
    $desc = $_POST["description"];
    $color = $_POST["color"];
    $cost = $_POST["price"];
    $storage = $_POST["storage"];
    
    Database::iud("INSERT INTO `product`(`title`,`description`,`color_id`,`qty`,`price`,`storage_id`,`status_id`) VALUES 
    ('" . $title . "','" . $desc . "','" . $color . "','" . $qty . "','" . $cost . "','" . $storage . "', 1)");
    
    $product_id = Database::$connection->insert_id;
    
    $base_path = dirname(dirname(__FILE__)) . "/resources/";
    
    if (!file_exists($base_path)) {
        mkdir($base_path, 0777, true);
    }
    
    if(isset($_FILES['images'])) {
        $allowed_types = array("image/jpeg", "image/png", "image/svg+xml");
        $upload_count = 0;
        
        for($x = 0; $x < count($_FILES['images']['name']); $x++) {
            if($_FILES['images']['error'][$x] == 0 && $_FILES['images']['size'][$x] > 0) {
                
                $img_type = $_FILES['images']['type'][$x];
                
                if(in_array($img_type, $allowed_types)) {
                    
                    $ext = "";
                    if($img_type == "image/jpeg") {
                        $ext = ".jpeg";
                    } else if($img_type == "image/png") {
                        $ext = ".png";
                    } else if($img_type == "image/svg+xml") {
                        $ext = ".svg";
                    }
                    
                    $filename = "product_" . $product_id . "_" . $x . "_" . uniqid() . $ext;
                    $full_path = $base_path . $filename;
                    $db_path = "resources/" . $filename;
                    
                    if(move_uploaded_file($_FILES['images']['tmp_name'][$x], $full_path)) {
                        Database::iud("INSERT INTO `product_img`(`path`,`product_id`) VALUES 
                        ('" . $db_path . "','" . $product_id . "')");
                        $upload_count++;
                    }
                }
            }
        }
        
        if($upload_count > 0) {
            echo "success";
        } else {
            echo "Failed to upload images";
        }
    } else {
        echo "No images selected";
    }
} else {
    echo "Invalid request";
}
?>