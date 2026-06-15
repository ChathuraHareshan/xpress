<?php
session_start();
include "process/connection.php";
include "process/payhereConfig.php";

if (!isset($_SESSION["u"])) {
    echo '<script>alert("Please login first"); window.location = "signin.php";</script>';
    exit();
}

$user_id = $_SESSION["u"]["user_id"];
$email = $_SESSION["u"]["email"];
$user_data = $_SESSION["u"];
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Xpress Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>
    <style>
        .checkout-card {
            border-radius: 1.75rem;
            border: 1px solid rgba(255, 159, 67, 0.18);
            background: rgba(255, 255, 255, 0.98);
        }

        .cart-item-card {
            border-radius: 1.5rem;
            border: 1px solid rgba(255, 159, 67, 0.18);
            background: rgba(255, 248, 242, 0.9);
        }

        .cart-item-card .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 1rem;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                <div>
                    <h1 class="h3 fw-bold mb-2">Checkout</h1>
                    <p class="text-muted mb-0">Review your order and complete payment</p>
                </div>
                <a href="cart.php" class="btn btn-outline-warning btn-lg">Back to cart</a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <div class="checkout-card p-4 mb-4">
                    <h2 class="h5 fw-bold mb-4">Shipping details</h2>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label">First name</label>
                            <input type="text" class="form-control" value="<?php echo $user_data['fname']; ?>" disabled>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Last name</label>
                            <input type="text" class="form-control" value="<?php echo $user_data['lname']; ?>" disabled>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?php echo $email; ?>" disabled>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" value="<?php echo $user_data['mobile']; ?>" disabled>
                        </div>

                        <?php
                        $address_rs = Database::search("SELECT * FROM `address` WHERE `user_email` = '$email'");
                        if ($address_rs->num_rows > 0) {
                            $address_data = $address_rs->fetch_assoc();
                            $district_rs = Database::search("SELECT * FROM `district` WHERE `district_id` = '" . $address_data["district_id"] . "'");
                            $district_data = $district_rs->fetch_assoc();
                        ?>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" value="<?php echo $address_data['line1'] . ', ' . $address_data['line2']; ?>" disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" value="<?php echo $address_data['city']; ?>" disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">District</label>
                                <input type="text" class="form-control" value="<?php echo $district_data['district_name']; ?>" disabled>
                            </div>
                        <?php } else { ?>
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    Please <a href="profile.php">update your address</a> before checkout
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="checkout-card p-4">
                    <h2 class="h5 fw-bold mb-4">Order summary</h2>
                    
                    <?php

$cart_rs = Database::search("SELECT * FROM `cart` WHERE `user_id` = '$user_id'");
                    $cart_num = $cart_rs->num_rows;
                    $sub_total = 0;
                    $cart_items = array();
                    
                    while ($cart_data = $cart_rs->fetch_assoc()) {
                        $product_rs = Database::search("SELECT * FROM `product` WHERE `product_id` = '" . $cart_data["product_id"] . "'");
                        $product_data = $product_rs->fetch_assoc();
                        $item_total = $product_data["price"] * $cart_data["order_qty"];
                        $sub_total += $item_total;
                        
                        $img_rs = Database::search("SELECT * FROM `product_img` WHERE `product_id` = '" . $product_data["product_id"] . "' LIMIT 1");
                        $img_data = $img_rs->fetch_assoc();
                        
                        $cart_items[] = array(
                            'product_id' => $product_data['product_id'],
                            'title' => $product_data['title'],
                            'qty' => $cart_data['order_qty'],
                            'price' => $product_data['price'],
                            'image' => $img_data['path']
                        );
                    ?>
                        <div class="cart-item-card p-3 mb-3 d-flex gap-3 align-items-center">
                            <img src="<?php echo $img_data['path']; ?>" class="product-image" alt="<?php echo $product_data['title']; ?>">
                            <div class="flex-grow-1">
                                <h3 class="h6 mb-1"><?php echo $product_data['title']; ?></h3>
                                <p class="text-muted mb-1 small">Qty: <?php echo $cart_data['order_qty']; ?></p>
                                <span class="fw-semibold">Rs.<?php echo number_format($item_total, 2); ?></span>
                            </div>
                        </div>
                    <?php
                    }
                    
                    $delivery_fee = 500;
                    if (isset($district_data) && $district_data['district_name'] == "Colombo") {
                        $delivery_fee = 300;
                    }
                    
                    $total = $sub_total + $delivery_fee;
                    ?>
                    
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>Rs.<?php echo number_format($sub_total, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery fee</span>
                            <span>Rs.<?php echo number_format($delivery_fee, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between pt-2 border-top mt-2">
                            <strong>Total</strong>
                            <strong class="fs-5 text-warning">Rs.<?php echo number_format($total, 2); ?></strong>
                        </div>
                    </div>
                    
                    <button class="btn btn-warning btn-lg w-100 mt-4" onclick="processPayment()">
                        <i class="bi bi-credit-card"></i> Pay with PayHere
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        var cartItems = <?php echo json_encode($cart_items); ?>;
        var subtotal = <?php echo $sub_total; ?>;
        var deliveryFee = <?php echo $delivery_fee; ?>;
        var total = <?php echo $total; ?>;
        
        function processPayment() {

          var itemsString = "";
            for (var i = 0; i < cartItems.length; i++) {
                itemsString += cartItems[i].title;
                if (i < cartItems.length - 1) itemsString += ", ";
            }
            
            var orderId = "ORDER_" + Date.now() + "_" + Math.random().toString(36).substr(2, 6);
            

            var formData = new FormData();
            formData.append("cart", "true");
            formData.append("amount", total);
            formData.append("items", itemsString);
            formData.append("order_id", orderId);
            
            var request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (request.readyState === 4 && request.status === 200) {
                    var response = JSON.parse(request.responseText);
                    
                    if (response.status === "success") {

                    var payment = {
                            "sandbox": true,
                            "merchant_id": response.merchant_id,
                            "return_url": window.location.origin + "/xpress/cart.php",
                            "cancel_url": window.location.origin + "/xpress/cart.php",
                            "notify_url": window.location.origin + "/xpress/process/notify.php",
                            "order_id": response.order_id,
                            "items": response.items,
                            "amount": response.amount,
                            "currency": "LKR",
                            "hash": response.hash,
                            "first_name": response.first_name,
                            "last_name": response.last_name,
                            "email": response.email,
                            "phone": response.phone,
                            "address": response.address,
                            "city": response.city,
                            "country": "Sri Lanka"
                        };
                        
                        payhere.startPayment(payment);
                        

                        payhere.onCompleted = function(orderId) {
                            saveOrder(orderId, "cart");
                        };
                        
                        payhere.onDismissed = function() {
                            Swal.fire("Payment cancelled", "You cancelled the payment", "info");
                        };
                        
                        payhere.onError = function(error) {
                            Swal.fire("Payment error", error, "error");
                        };
                    } else {
                        Swal.fire("Error", response.message, "error");
                    }
                }
            };
            request.open("POST", "process/getPaymentData.php", true);
            request.send(formData);
        }
        
        function saveOrder(orderId, type) {
            var formData = new FormData();
            formData.append("order_id", orderId);
            formData.append("amount", total);
            formData.append("type", type);
            
            var request = new XMLHttpRequest();
            request.onreadystatechange = function() {
                if (request.readyState === 4 && request.status === 200) {
                    var response = JSON.parse(request.responseText);
                    if (response.status === "success") {
                        Swal.fire({
                            title: "Payment successful!",
                            text: "Your order has been placed successfully.",
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.href = "invoice.php?id=" + response.order_id;
                        });
                    }
                }
            };
            request.open("POST", "process/saveOrder.php", true);
            request.send(formData);
        }
    </script>
</body>

</html>