<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Xpress Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="topbar bg-white border-bottom">
        <div class="container d-flex flex-wrap justify-content-between align-items-center py-2 small text-muted">
            <div>support@xpress.com | +94 123 456 789</div>
            <div class="d-flex gap-3">

                <?php

                include "process/connection.php";

                session_start();

                $watchlist_ids = [];
                $user_id = null;

                if (isset($_SESSION["u"])) {
                    $name = $_SESSION["u"]["fname"] . " " . $_SESSION["u"]["lname"];
                    $user_id = $_SESSION["u"]["user_id"];

                    $watchlist_rs = Database::search("SELECT `product_id` FROM `watchlist` WHERE `user_id` = '" . $user_id . "'");
                    while ($watchlist_item = $watchlist_rs->fetch_assoc()) {
                        $watchlist_ids[] = $watchlist_item["product_id"];
                    }
                ?>
                    <a href="home.php" class="text-warning text-decoration-none">Welcome, <?php echo $name; ?></a>
                <?php
                } else {
                ?>
                    <a href="index.php" class="text-warning text-decoration-none">Login</a>
                    <a href="index.php" class="text-warning text-decoration-none">Sign Up</a>
                <?php
                }
                ?>


            </div>
        </div>
    </div>

    <header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
        <div class="container align-items-center">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <img src="img/logo.jpeg" alt="Xpress logo" class="brand-logo" onerror="this.style.display='none'">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <a href="cart.php" class="btn btn-outline-warning btn-sm">Cart</a>
                    <a href="watchlist.php" class="btn btn-outline-warning btn-sm"><i class="bi bi-heart"></i> Watchlist</a>
                    <div class="dropdown">
                        <button class="btn btn-warning btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Profile
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                            <li><a class="dropdown-item" href="myOrders.php">My Orders</a></li>
                            <li><a class="dropdown-item" href="watchlist.php">Watchlist</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="hero-section py-5">
            <div class="container">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6">
                        <span class="badge bg-warning text-dark rounded-pill mb-3">Trusted electronics store</span>
                        <h1 class="display-5 fw-bold">Shop the latest phones, earbuds and accessories</h1>
                        <p class="lead text-muted mb-4">Discover your next gadget with fast delivery, exclusive deals, and a seamless shopping experience.</p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="#shop" class="btn btn-warning btn-lg">Shop Now</a>
                            <a href="#about" class="btn btn-outline-warning btn-lg">Learn More</a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div id="productCarousel" class="carousel slide shadow-sm rounded-4" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="img/insta-item3.jpg" class="d-block w-100 rounded-4" alt="Smartphone">
                                </div>
                                <div class="carousel-item">
                                    <img src="img/insta-item2.jpg" class="d-block w-100 rounded-4" alt="Earbuds">
                                </div>
                                <div class="carousel-item">
                                    <img src="img/insta-item1.jpg" class="d-block w-100 rounded-4" alt="Smart Watch">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="shop" class="product-section py-5 bg-white">
            <div class="container">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                    <div>
                        <h2 class="h3 fw-bold mb-1">Featured products</h2>
                        <p class="text-muted mb-0">Top-selling electronics with fast checkout.</p>
                    </div>
                    <a href="#" class="text-warning text-decoration-none">View all products &rarr;</a>
                </div>
                
                
                <div class="row g-4">

    <?php
    $product_rs = Database::search("SELECT * FROM `product` LIMIT 6");
    $product_num = $product_rs->num_rows;

    for ($i = 0; $i < $product_num; $i++) {
        $product_data = $product_rs->fetch_assoc();
        
        // Get all images for this product
        $img_rs = Database::search("SELECT path FROM `product_img` WHERE product_id = '" . $product_data["product_id"] . "'");
    ?>

        <div class="col-md-6 col-xl-4">
            <div class="card product-card h-100 border-0 shadow-sm">
                <div class="product-card-image-wrap">
                    <div id="carousel<?php echo $product_data["product_id"]; ?>" class="carousel slide product-carousel" data-bs-ride="carousel">
                        <div class="carousel-inner rounded-top-4">
                            <?php
                            $first = true;
                            for ($j = 0; $j < $img_rs->num_rows; $j++) {
                                $img_data = $img_rs->fetch_assoc();
                            ?>
                                <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                                    <img src="<?php echo $img_data["path"]; ?>" class="d-block w-100" alt="<?php echo $product_data["title"]; ?>">
                                </div>
                            <?php
                                $first = false;
                            }
                            ?>
                        </div>
                        <?php if($img_rs->num_rows > 1) { ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carousel<?php echo $product_data["product_id"]; ?>" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carousel<?php echo $product_data["product_id"]; ?>" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        <?php } ?>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <h5 class="card-title mb-0 fw-bold"><?php echo $product_data["title"]; ?></h5>
                        <span class="badge bg-light text-warning fw-semibold">4.8 ★</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="h4 mb-0 fw-bold text-dark">Rs.<?php echo number_format($product_data["price"], 2); ?></span>
                        <span class="small text-muted">In stock</span>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="singleProductView.php?id=<?php echo $product_data["product_id"]; ?>" class="btn btn-outline-warning">View details</a>
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-warning flex-grow-1" onclick="addToCart(<?php echo $product_data["product_id"]; ?>)">Add to cart</button>
                            <?php
                            $isInWatchlist = in_array($product_data["product_id"], $watchlist_ids);
                            ?>
                            <a onclick="addToWatchlist(<?php echo $product_data["product_id"]; ?>);" class="btn <?php echo $isInWatchlist ? 'btn-warning' : 'btn-outline-warning'; ?> btn-icon" title="Watchlist">
                                <i class="bi <?php echo $isInWatchlist ? 'bi-heart-fill' : 'bi-heart'; ?>"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php
    }
    ?>

</div>

            </div>
        </section>

        <section id="about" class="feature-section py-5">
            <div class="container">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="feature-card p-4 rounded-4 shadow-sm bg-white h-100">
                            <h5 class="fw-bold">Fast shipping</h5>
                            <p class="text-muted mb-0">Get your order delivered quickly with express dispatch options.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card p-4 rounded-4 shadow-sm bg-white h-100">
                            <h5 class="fw-bold">Secure payments</h5>
                            <p class="text-muted mb-0">Shop with confidence using safe payment methods and buyer protection.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card p-4 rounded-4 shadow-sm bg-white h-100">
                            <h5 class="fw-bold">Dedicated support</h5>
                            <p class="text-muted mb-0">Our customer care team is ready to help you 24/7.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer py-5 bg-dark text-white">
        <div class="container">
            <div class="row gy-4">
                <div class="col-md-4">
                    <a href="#" class="d-flex align-items-center gap-2 mb-3 text-decoration-none text-white">
                        <img src="img/logo.jpeg" alt="Xpress logo" class="brand-logo" onerror="this.style.display='none'">
                        <span class="fw-bold">Xpress Store</span>
                    </a>
                    <p class="text-light">Your trusted source for the latest electronics, accessories, and great deals in a clean orange-white experience.</p>
                </div>
                <div class="col-md-2">
                    <h6 class="fw-bold text-warning">Shop</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light text-decoration-none">All Products</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Deals</a></li>
                        <li><a href="#" class="text-light text-decoration-none">New Arrivals</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold text-warning">Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light text-decoration-none">Contact us</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Track order</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Returns</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold text-warning">Follow us</h6>
                    <p class="text-light mb-2">Stay in touch for special offers and product launches.</p>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-outline-warning btn-sm">Facebook</a>
                        <a href="#" class="btn btn-outline-warning btn-sm">Instagram</a>
                    </div>
                </div>
            </div>
            <div class="pt-4 mt-4 border-top border-secondary border-opacity-25 text-center text-light small">
                &copy; 2026 Xpress Store. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>