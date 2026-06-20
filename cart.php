<?php session_start(); ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Xpress Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .cart-item {
            border-radius: 1.75rem;
            border: 1px solid rgba(255, 159, 67, 0.18);
            background: rgba(255, 255, 255, 0.96);
        }

        .cart-item .product-image {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 1.25rem;
        }

        .cart-item .spec-chip {
            background: #fff8f2;
            border: 1px solid rgba(255, 159, 67, 0.18);
            color: #6e4d0e;
        }

        .cart-item .remove-btn {
            color: #d46b02;
        }

        .cart-summary {
            border-radius: 1.75rem;
            border: 1px solid rgba(255, 159, 67, 0.18);
            background: rgba(255, 255, 255, 0.96);
        }

        .quantity-btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h3 fw-bold mb-1">Your cart</h1>
                <p class="text-muted mb-0">Review your selected products before checkout.</p>
            </div>
            <a href="home.php" class="btn btn-outline-warning btn-lg">Continue shopping</a>
        </div>

        <div class="row g-4">
            <div class="col-12 col-xl-8">

                <?php
                include "process/connection.php";


                if (!isset($_SESSION["u"])) {
                    if (!isset($_SESSION["u"])) {
    echo '<script>alert("Please login first"); window.location = "index.php";</script>';
    exit();
}
                } else {
                    $cart_rs = Database::search("SELECT * FROM `cart` WHERE `user_id` = '" . $_SESSION["u"]["user_id"] . "'");
                    $cart_num = $cart_rs->num_rows;


                    $sub_total = 0;
                    $delivery_fee = 500;
                    $discount = 0;
                    $total = 0;

                    if ($cart_num == 0) {
                        echo '<div class="text-center py-5">
                            <h2 class="h4 fw-bold mb-3">Your cart is empty</h2>
                            <p class="text-muted mb-4">Looks like you haven\'t added anything to your cart yet.</p>
                            <a href="home.php" class="btn btn-warning btn-lg">Start shopping</a>
                        </div>';
                    } else {
                        while ($cart_data = $cart_rs->fetch_assoc()) {
                            $product_rs = Database::search("SELECT * FROM `product` WHERE `product_id` = '" . $cart_data["product_id"] . "'");
                            $product_data = $product_rs->fetch_assoc();

                            $item_total = $product_data["price"] * $cart_data["order_qty"];
                            $sub_total += $item_total;
                ?>
                            <div class="cart-item p-4 mb-4" data-product-id="<?php echo $product_data["product_id"]; ?>">
                                <div class="row align-items-center g-3">
                                    <?php
                                    $img_rs = Database::search("SELECT * FROM `product_img` WHERE `product_id` = '" . $product_data["product_id"] . "' LIMIT 1");
                                    $img_data = $img_rs->fetch_assoc();
                                    ?>
                                    <div class="col-auto">
                                        <img src="<?php echo $img_data["path"]; ?>" alt="<?php echo $product_data["title"]; ?>" class="product-image">
                                    </div>
                                    <div class="col">
                                        <div class="d-flex justify-content-between align-items-start gap-3">
                                            <div>
                                                <h2 class="h5 fw-bold mb-2"><?php echo $product_data["title"]; ?></h2>
                                                <p class="text-muted mb-2"><?php echo substr($product_data["description"], 0, 100); ?>...</p>

                                                <?php
                                                $color_rs = Database::search("SELECT * FROM `color` WHERE `color_id` = '" . $product_data["color_id"] . "'");
                                                $color_data = $color_rs->fetch_assoc();

                                                $storage_rs = Database::search("SELECT * FROM `storage` WHERE `storage_id` = '" . $product_data["storage_id"] . "'");
                                                $storage_data = $storage_rs->fetch_assoc();
                                                ?>

                                                <div class="d-flex flex-wrap gap-2">
                                                    <span class="badge spec-chip rounded-pill py-2 px-3">Color: <?php echo $color_data["color_name"]; ?></span>
                                                    <span class="badge spec-chip rounded-pill py-2 px-3">Storage: <?php echo $storage_data["storage_name"]; ?></span>
                                                </div>
                                            </div>
                                            <button class="btn btn-sm btn-outline-warning remove-btn" onclick="removeFromCart(<?php echo $product_data['product_id']; ?>)" type="button">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-auto text-sm-end">
                                        <div class="mb-2">
                                            <span class="text-muted small d-block">Order qty</span>
                                            <div class="d-flex align-items-center gap-2 justify-content-sm-end">
                                                <button class="btn btn-sm btn-outline-warning quantity-btn" onclick="updateQuantity(<?php echo $product_data['product_id']; ?>, 'decrease')">
                                                    <i class="bi bi-dash"></i>
                                                </button>

                                                <span class="badge bg-warning text-dark rounded-pill py-2 px-3 quantity-display" data-product="<?php echo $product_data['product_id']; ?>"><?php echo $cart_data["order_qty"]; ?></span>
                                                
                                                <button class="btn btn-sm btn-outline-warning quantity-btn" onclick="updateQuantity(<?php echo $product_data['product_id']; ?>, 'increase')">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="fw-semibold fs-5 text-dark item-price" data-product="<?php echo $product_data['product_id']; ?>">
                                            Rs.<?php echo number_format($item_total, 2); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                <?php
                        }
                    }
                }
                ?>

            </div>

            <div class="col-12 col-xl-4">
                <div class="cart-summary p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h2 class="h5 fw-bold mb-3">Order summary</h2>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span class="subtotal-amount">Rs.<?php echo number_format($sub_total, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Delivery fee</span>
                            <span class="delivery-fee">Rs.<?php echo number_format($delivery_fee, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Discount</span>
                            <span class="text-success discount-amount">-Rs.<?php echo number_format($discount, 2); ?></span>
                        </div>

                        <?php

                        $total = $sub_total + $delivery_fee - $discount;
                        ?>

                        <div class="border-top pt-3 mt-3 d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Total</span>
                            <span class="fs-5 fw-bold text-dark total-amount">Rs.<?php echo number_format($total, 2); ?></span>
                        </div>
                    </div>
                    <button class="btn btn-warning btn-lg w-100 mt-4" onclick="checkout()">Proceed to checkout</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/script.js"></script>


</body>

</html>