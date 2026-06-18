<?php
session_start();
include "process/connection.php";

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$colorFilter = isset($_GET['color']) ? (int)$_GET['color'] : 0;
$storageFilter = isset($_GET['storage']) ? (int)$_GET['storage'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';

$searchValue = Database::$connection ? Database::$connection->real_escape_string($search) : $search;

$query = "SELECT p.*, c.color_name, s.storage_name FROM `product` p LEFT JOIN `color` c ON p.color_id = c.color_id LEFT JOIN `storage` s ON p.storage_id = s.storage_id WHERE 1";

if ($search !== '') {
    $query .= " AND (p.title LIKE '%" . $searchValue . "%' OR p.description LIKE '%" . $searchValue . "%')";
}
if ($colorFilter > 0) {
    $query .= " AND p.color_id = '$colorFilter'";
}
if ($storageFilter > 0) {
    $query .= " AND p.storage_id = '$storageFilter'";
}

if ($sort === 'low') {
    $query .= " ORDER BY p.price ASC";
} elseif ($sort === 'high') {
    $query .= " ORDER BY p.price DESC";
} elseif ($sort === 'name') {
    $query .= " ORDER BY p.title ASC";
} else {
    $query .= " ORDER BY p.product_id DESC";
}

$product_rs = Database::search($query);
$product_num = $product_rs->num_rows;

$color_rs = Database::search("SELECT * FROM `color` ORDER BY `color_name` ASC");
$storage_rs = Database::search("SELECT * FROM `storage` ORDER BY `storage_name` ASC");
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shop - Xpress Store</title>
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
                if (isset($_SESSION["u"])) {
                    $name = $_SESSION["u"]["fname"] . " " . $_SESSION["u"]["lname"];
                ?>
                    <a href="home.php" class="text-warning text-decoration-none">Welcome, <?php echo $name; ?></a>
                <?php } else { ?>
                    <a href="index.php" class="text-warning text-decoration-none">Login</a>
                    <a href="index.php" class="text-warning text-decoration-none">Sign Up</a>
                <?php } ?>
            </div>
        </div>
    </div>

    <header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
        <div class="container align-items-center">
            <a class="navbar-brand d-flex align-items-center gap-2" href="home.php">
                <img src="img/logo.jpeg" alt="Xpress logo" class="brand-logo" onerror="this.style.display='none'">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="shop.php">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="home.php#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="home.php#contact">Contact</a></li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <a href="cart.php" class="btn btn-outline-warning btn-sm">Cart</a>
                    <a href="watchlist.php" class="btn btn-outline-warning btn-sm"><i class="bi bi-heart"></i> Watchlist</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="py-5 bg-light">
            <div class="container">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-lg-5">
                                <label class="form-label fw-semibold">Search products</label>
                                <form method="GET" action="shop.php">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                        <input type="text" name="search" class="form-control" placeholder="Search by name or keyword" value="<?php echo htmlspecialchars($search); ?>">
                                    </div>
                            </div>
                            <div class="col-6 col-lg-2">
                                <label class="form-label fw-semibold">Color</label>
                                <select name="color" class="form-select">
                                    <option value="0">All colors</option>
                                    <?php while ($color_data = $color_rs->fetch_assoc()) { ?>
                                        <option value="<?php echo $color_data['color_id']; ?>" <?php echo ($colorFilter == $color_data['color_id']) ? 'selected' : ''; ?>>
                                            <?php echo $color_data['color_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-6 col-lg-2">
                                <label class="form-label fw-semibold">Storage</label>
                                <select name="storage" class="form-select">
                                    <option value="0">All storage</option>
                                    <?php while ($storage_data = $storage_rs->fetch_assoc()) { ?>
                                        <option value="<?php echo $storage_data['storage_id']; ?>" <?php echo ($storageFilter == $storage_data['storage_id']) ? 'selected' : ''; ?>>
                                            <?php echo $storage_data['storage_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-6 col-lg-2">
                                <label class="form-label fw-semibold">Sort by</label>
                                <select name="sort" class="form-select">
                                    <option value="latest" <?php echo ($sort == 'latest') ? 'selected' : ''; ?>>Latest</option>
                                    <option value="low" <?php echo ($sort == 'low') ? 'selected' : ''; ?>>Price: Low to High</option>
                                    <option value="high" <?php echo ($sort == 'high') ? 'selected' : ''; ?>>Price: High to Low</option>
                                    <option value="name" <?php echo ($sort == 'name') ? 'selected' : ''; ?>>Name</option>
                                </select>
                            </div>
                            <div class="col-6 col-lg-1">
                                <button type="submit" class="btn btn-warning w-100">Filter</button>
                            </div>
                                </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5 bg-white">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 fw-bold mb-1">All products</h2>
                        <p class="text-muted mb-0">Showing <?php echo $product_num; ?> result(s)</p>
                    </div>
                </div>

                <div class="row g-4">
                    <?php if ($product_num > 0) { ?>
                        <?php for ($i = 0; $i < $product_num; $i++) {
                            $product_data = $product_rs->fetch_assoc();
                            $img_rs = Database::search("SELECT path FROM `product_img` WHERE product_id = '" . $product_data["product_id"] . "' LIMIT 1");
                            $img_data = $img_rs->num_rows > 0 ? $img_rs->fetch_assoc() : null;
                            $img_path = ($img_data && isset($img_data["path"])) ? $img_data["path"] : "img/no-image.png";
                        ?>
                            <div class="col-12 col-md-6 col-xl-4">
                                <div class="card h-100 border-0 shadow-sm product-card">
                                    <img src="<?php echo $img_path; ?>" class="card-img-top" alt="<?php echo $product_data['title']; ?>" style="height: 230px; object-fit: cover;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start gap-2">
                                            <div>
                                                <h5 class="fw-bold mb-1"><?php echo $product_data['title']; ?></h5>
                                                <p class="text-muted small mb-0"><?php echo $product_data['color_name']; ?> • <?php echo $product_data['storage_name']; ?></p>
                                            </div>
                                            <span class="badge bg-warning text-dark">Rs.<?php echo number_format($product_data['price'], 2); ?></span>
                                        </div>
                                        <p class="text-muted small mt-3 mb-3"><?php echo substr($product_data['description'], 0, 90); ?>...</p>
                                        <div class="d-grid gap-2">
                                            <a href="singleProductView.php?id=<?php echo $product_data['product_id']; ?>" class="btn btn-outline-warning">View details</a>
                                            <button class="btn btn-warning" onclick="addToCart(<?php echo $product_data['product_id']; ?>)">Add to cart</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="col-12">
                            <div class="alert alert-warning mb-0">No products found for your search.</div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
