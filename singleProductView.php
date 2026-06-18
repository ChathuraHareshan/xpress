<?php
session_start();
include "process/connection.php";

$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product_data = null;
$images = [];
$color_data = null;
$storage_data = null;

if ($productId > 0) {
    $product_rs = Database::search("SELECT * FROM `product` WHERE `product_id` = '" . $productId . "'");
    if ($product_rs && $product_rs->num_rows > 0) {
        $product_data = $product_rs->fetch_assoc();

        $img_rs = Database::search("SELECT * FROM `product_img` WHERE `product_id` = '" . $product_data["product_id"] . "'");
        while ($img_rs && $img = $img_rs->fetch_assoc()) {
            $images[] = $img["path"];
        }

        if (empty($images)) {
            $images[] = "img/logo.jpeg";
        }

        $color_rs = Database::search("SELECT * FROM `color` WHERE `color_id` = '" . $product_data["color_id"] . "'");
        if ($color_rs && $color_rs->num_rows > 0) {
            $color_data = $color_rs->fetch_assoc();
        }

        $storage_rs = Database::search("SELECT * FROM `storage` WHERE `storage_id` = '" . $product_data["storage_id"] . "'");
        if ($storage_rs && $storage_rs->num_rows > 0) {
            $storage_data = $storage_rs->fetch_assoc();
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Xpress Product Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
        <div class="container align-items-center">
            <a class="navbar-brand d-flex align-items-center gap-2" href="home.php">
                <img src="img/logo.jpeg" alt="Xpress logo" class="brand-logo" onerror="this.style.display='none'">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="home.php#shop">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="home.php#contact">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="home.php#about">About</a></li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <a href="cart.php" class="btn btn-outline-warning btn-sm">Cart</a>
                    <a href="watchlist.php" class="btn btn-outline-warning btn-sm"><i class="bi bi-heart"></i> Watchlist</a>
                </div>
            </div>
        </div>
    </header>

    <main class="py-5">
        <div class="container">
            <?php if (!$product_data) : ?>
                <div class="alert alert-warning rounded-4 shadow-sm">Product not found. <a href="home.php" class="alert-link">Return to shop</a>.</div>
            <?php else : ?>
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="product-detail-card p-3 bg-white shadow-sm">
                            <div id="singleProductCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner rounded-4 overflow-hidden">
                                    <?php foreach ($images as $index => $path) : ?>
                                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                            <img src="<?php echo htmlspecialchars($path); ?>" class="d-block w-100 product-detail-image" alt="<?php echo htmlspecialchars($product_data["title"]); ?>">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if (count($images) > 1) : ?>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#singleProductCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#singleProductCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card product-detail-card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h1 class="h4 fw-bold mb-1"><?php echo htmlspecialchars($product_data["title"]); ?></h1>
                                        <div class="text-warning mb-2">4.8 ★</div>
                                    </div>
                                    <span class="badge bg-warning text-dark py-2 px-3">In stock: <?php echo intval($product_data["qty"]); ?></span>
                                </div>
                                <div class="mb-4">
                                    <span class="h3 fw-bold">Rs. <?php echo number_format($product_data["price"], 2); ?></span>
                                    <p class="text-muted mt-2 mb-0"><?php echo htmlspecialchars($product_data["description"]); ?></p>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        <?php if ($color_data) : ?>
                                            <span class="badge bg-light text-dark border border-warning">Color: <?php echo htmlspecialchars($color_data["color_name"]); ?></span>
                                        <?php endif; ?>
                                        <?php if ($storage_data) : ?>
                                            <span class="badge bg-light text-dark border border-warning">Storage: <?php echo htmlspecialchars($storage_data["storage_name"]); ?></span>
                                        <?php endif; ?>
                                        <span class="badge bg-light text-dark border border-warning">SKU: #<?php echo intval($product_data["product_id"]); ?></span>
                                    </div>
                                    <p class="text-muted small mb-0">Fast delivery, secure checkout, and 24/7 support.</p>
                                </div>
                                <div class="d-grid gap-3 mb-3">
                                    <button class="btn btn-warning btn-lg" onclick="addToCart(<?php echo intval($product_data["product_id"]); ?>)">Add to cart</button>
                                    <a href="watchlist.php" class="btn btn-outline-warning btn-lg"><i class="bi bi-heart"></i> View watchlist</a>
                                </div>
                                <div class="border-top pt-3 mt-3 text-muted small">
                                    <p class="mb-1"><strong>Condition:</strong> New</p>
                                    <p class="mb-0"><strong>Shipping:</strong> Free on orders above Rs. 5000</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer py-5 bg-dark text-white">
        <div class="container">
            <div class="row gy-4">
                <div class="col-md-4">
                    <a href="home.php" class="d-flex align-items-center gap-2 mb-3 text-decoration-none text-white">
                        <img src="img/logo.jpeg" alt="Xpress logo" class="brand-logo" onerror="this.style.display='none'">
                        <span class="fw-bold">Xpress Store</span>
                    </a>
                    <p class="text-light">Your trusted source for the latest electronics, accessories, and great deals.</p>
                </div>
                <div class="col-md-2">
                    <h6 class="fw-bold text-warning">Shop</h6>
                    <ul class="list-unstyled">
                        <li><a href="home.php#shop" class="text-light text-decoration-none">All Products</a></li>
                        <li><a href="home.php#about" class="text-light text-decoration-none">About</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold text-warning">Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light text-decoration-none">Contact us</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Track order</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold text-warning">Follow us</h6>
                    <p class="text-light mb-2">Stay in touch for special offers and launches.</p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/script.js"></script>
</body>

</html>
