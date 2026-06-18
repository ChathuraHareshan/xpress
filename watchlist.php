<?php
session_start();
include "process/connection.php";

if (!isset($_SESSION["u"])) {
    echo '<script>alert("Please login first"); window.location = "index.php";</script>';
    exit();
}

$user_id = $_SESSION["u"]["user_id"];
$watchlist_items = [];
$is_watchlist_available = false;

$watchlist_table = Database::search("SHOW TABLES LIKE 'watchlist'");
if ($watchlist_table && $watchlist_table->num_rows > 0) {
    $is_watchlist_available = true;
    $watchlist_rs = Database::search("SELECT * FROM `watchlist` WHERE `user_id` = '" . $user_id . "'");

    if ($watchlist_rs && $watchlist_rs->num_rows > 0) {
        while ($item = $watchlist_rs->fetch_assoc()) {
            $product_rs = Database::search("SELECT * FROM `product` WHERE `product_id` = '" . $item["product_id"] . "'");
            if ($product_rs && $product_rs->num_rows > 0) {
                $product = $product_rs->fetch_assoc();
                $img_rs = Database::search("SELECT * FROM `product_img` WHERE `product_id` = '" . $product["product_id"] . "' LIMIT 1");
                $img_data = $img_rs && $img_rs->num_rows > 0 ? $img_rs->fetch_assoc() : ["path" => "img/logo.jpeg"];

                $color_rs = Database::search("SELECT * FROM `color` WHERE `color_id` = '" . $product["color_id"] . "'");
                $color_data = $color_rs && $color_rs->num_rows > 0 ? $color_rs->fetch_assoc() : null;

                $storage_rs = Database::search("SELECT * FROM `storage` WHERE `storage_id` = '" . $product["storage_id"] . "'");
                $storage_data = $storage_rs && $storage_rs->num_rows > 0 ? $storage_rs->fetch_assoc() : null;

                $watchlist_items[] = [
                    "product" => $product,
                    "image" => $img_data["path"],
                    "color" => $color_data,
                    "storage" => $storage_data,
                ];
            }
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Xpress Watchlist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold mb-1">Your watchlist</h1>
                <p class="text-muted mb-0">Keep track of products you love and come back to them easily.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="home.php" class="btn btn-outline-warning">Continue shopping</a>
                <a href="cart.php" class="btn btn-warning">View cart</a>
            </div>
        </div>

        <?php if (!$is_watchlist_available) : ?>
            <div class="alert alert-warning rounded-4 shadow-sm">
                The watchlist feature is not configured yet. You can still browse products and enable watchlist support later.
            </div>
        <?php elseif (empty($watchlist_items)) : ?>
            <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                <span class="d-inline-flex align-items-center justify-content-center bg-warning text-dark rounded-circle mb-4" style="width:72px;height:72px;">
                    <i class="bi bi-heart-fill fs-2"></i>
                </span>
                <h2 class="h4 fw-bold mb-2">Your watchlist is empty</h2>
                <p class="text-muted mb-4">Add products to your watchlist to save them for later.</p>
                <a href="home.php" class="btn btn-warning btn-lg">Browse products</a>
            </div>
        <?php else : ?>
            <div class="row g-4">
                <?php foreach ($watchlist_items as $item) : ?>
                    <?php $product = $item["product"]; ?>
                    <div class="col-12">
                        <div class="watchlist-card p-4 bg-white rounded-4 shadow-sm">
                            <div class="row g-3 align-items-center">
                                <div class="col-auto">
                                    <img src="<?php echo htmlspecialchars($item["image"]); ?>" alt="<?php echo htmlspecialchars($product["title"]); ?>" class="watchlist-image">
                                </div>
                                <div class="col-lg-6">
                                    <h2 class="h5 fw-bold mb-2"><?php echo htmlspecialchars($product["title"]); ?></h2>
                                    <p class="text-muted mb-2"><?php echo htmlspecialchars(substr($product["description"], 0, 100)); ?>...</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php if ($item["color"]) : ?>
                                            <span class="badge watchlist-chip">Color: <?php echo htmlspecialchars($item["color"]["color_name"]); ?></span>
                                        <?php endif; ?>
                                        <?php if ($item["storage"]) : ?>
                                            <span class="badge watchlist-chip">Storage: <?php echo htmlspecialchars($item["storage"]["storage_name"]); ?></span>
                                        <?php endif; ?>
                                        <span class="badge watchlist-chip">SKU: #<?php echo intval($product["product_id"]); ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-3 text-lg-end">
                                    <div class="h4 fw-bold text-dark mb-2">Rs. <?php echo number_format($product["price"], 2); ?></div>
                                    <div class="text-muted small mb-3">Available: <?php echo intval($product["qty"]); ?></div>
                                    <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                                        <a href="singleProductView.php?id=<?php echo intval($product["product_id"]); ?>" class="btn btn-outline-warning btn-sm">View</a>
                                        <button class="btn btn-warning btn-sm" onclick="addToCart(<?php echo intval($product["product_id"]); ?>)">Add to cart</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/script.js"></script>
</body>

</html>
