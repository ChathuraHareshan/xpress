<?php

include "process/connection.php";

session_start();

if (isset($_SESSION["u"])) {

    $user_id = $_SESSION["u"]["user_id"];
    $email = $_SESSION["u"]["email"];

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
        <style>
            .checkout-card {
                border-radius: 1.75rem;
                border: 1px solid rgba(255, 159, 67, 0.18);
                background: rgba(255, 255, 255, 0.98);
            }

            .checkout-card .form-control,
            .checkout-card .form-select {
                border-color: rgba(255, 159, 67, 0.3);
            }

            .checkout-card .form-control:focus,
            .checkout-card .form-select:focus {
                border-color: #ff8a00;
                box-shadow: 0 0 0 0.15rem rgba(255, 138, 0, 0.18);
            }

            .cart-item-card {
                border-radius: 1.5rem;
                border: 1px solid rgba(255, 159, 67, 0.18);
                background: rgba(255, 248, 242, 0.9);
            }

            .cart-item-card .product-image {
                width: 100%;
                height: 120px;
                object-fit: cover;
                border-radius: 1.2rem;
            }

            .cart-item-card .spec-badge {
                background: #fff3e0;
                color: #8a4b10;
            }

            .checkout-header {
                max-width: 960px;
            }
        </style>
    </head>

    <body class="bg-light">
        <div class="container py-5">
            <div class="checkout-header mb-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                    <div>
                        <h1 class="h3 fw-bold mb-2">Checkout</h1>
                        <p class="text-muted mb-0">Review your order details and add shipping information to complete your purchase.</p>
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
                                <input type="text" class="form-control form-control-lg" placeholder="<?php echo $_SESSION["u"]["fname"]; ?>" disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Last name</label>
                                <input type="text" class="form-control form-control-lg" placeholder="<?php echo $_SESSION["u"]["lname"]; ?>" disabled>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email address</label>
                                <input type="email" class="form-control form-control-lg" placeholder="<?php echo $_SESSION["u"]["email"]; ?>" disabled>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Phone number</label>
                                <input type="tel" class="form-control form-control-lg" placeholder="<?php echo $_SESSION["u"]["mobile"]; ?>" disabled>
                            </div>

                            <?php

                            $address_rs = Database::search("SELECT * FROM `address` WHERE `user_email` = '$email'");
                            $address_data = $address_rs->fetch_assoc();

                            ?>

                            <div class="col-12">
                                <label class="form-label">Address Line 01</label>
                                <input type="text" class="form-control form-control-lg" placeholder="<?php echo $address_data["line1"]; ?>" disabled>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address Line 02</label>
                                <input type="text" class="form-control form-control-lg" placeholder="<?php echo $address_data["line2"]; ?>" disabled>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control form-control-lg" placeholder="<?php echo $address_data["city"]; ?>" disabled>
                            </div>

                            <?php

                            $district_rs = Database::search("SELECT * FROM `district` WHERE `district_id` = '" . $address_data["district_id"] . "'");
                            $district_data = $district_rs->fetch_assoc();

                            ?>

                            <div class="col-12 col-md-6">
                                <label class="form-label">District</label>
                                <select class="form-select form-select-lg" required>
                                    <option value="<?php echo $district_data["district_name"]; ?>" selected disabled><?php echo $district_data["district_name"]; ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    
                </div>

                <div class="col-12 col-xl-4">
                    <div class="checkout-card p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h2 class="h5 fw-bold mb-1">Order summary</h2>
                                <p class="text-muted mb-0">3 items in cart</p>
                            </div>
                            <span class="badge bg-warning text-dark rounded-pill">$1,492</span>
                        </div>
                        <div class="row g-3">


                            <?php

                            $cart_rs = Database::search("SELECT * FROM `cart` WHERE `user_id` = '" . $_SESSION["u"]["user_id"] . "'");
                            $cart_num = $cart_rs->num_rows;

                            $sub_total = 0;
                            $delivery_fee = 500;
                            $discount = 0;
                            $total = 0;

                            for ($x = 0; $x < $cart_num; $x++) {
                                $cart_data = $cart_rs->fetch_assoc();

                                $product_rs = Database::search("SELECT * FROM `product` WHERE `product_id` = '" . $cart_data["product_id"] . "'");
                                $product_data = $product_rs->fetch_assoc();

                                $item_total = $product_data["price"] * $cart_data["order_qty"];
                                $sub_total += $item_total;

                                $img_rs = Database::search("SELECT * FROM `product_img` WHERE `product_id` = '" . $product_data["product_id"] . "' LIMIT 1");
                                $img_data = $img_rs->fetch_assoc();

                                $color_rs = Database::search("SELECT * FROM `color` WHERE `color_id` = '" . $product_data["color_id"] . "'");
                                $color_data = $color_rs->fetch_assoc();

                                $storage_rs = Database::search("SELECT * FROM `storage` WHERE `storage_id` = '" . $product_data["storage_id"] . "'");
                                $storage_data = $storage_rs->fetch_assoc();

                            ?>

                                <div class="col-12">
                                    <div class="cart-item-card p-3 d-flex gap-3 align-items-center">
                                        <div class="flex-shrink-0" style="width:90px;">
                                            <img src="<?php echo $img_data["path"]; ?>" alt="<?php echo $product_data["title"]; ?>" class="product-image">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h3 class="h6 mb-1"><?php echo $product_data["title"]; ?></h3>
                                            <p class="text-muted mb-2 small">Qty <?php echo $cart_data["order_qty"]; ?> · <?php echo $color_data["color_name"]; ?> · <?php echo $storage_data["storage_name"]; ?></p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-semibold">Rs.<?php echo number_format($product_data["price"], 2); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            <?php
                            }


                            ?>



                        </div>

                        <div class="border-top mt-4 pt-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal</span>
                                <span>Rs.<?php echo number_format($sub_total, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Shipping</span>
                                <span>Rs.500</span>
                            </div>
                           
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold">Total</span>
                                <span class="fs-5 fw-bold text-dark">Rs.<?php echo number_format($sub_total + 500, 2); ?></span>
                            </div>
                            <button class="btn btn-warning btn-lg w-100">Complete order</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>


<?php

} else {
?>
    <script>
        alert("Please Login First.");
        window.location = "adminsignin.php";
    </script>
<?php


}

?>