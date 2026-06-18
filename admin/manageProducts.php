<?php
session_start();
include "../process/connection.php";

if (!isset($_SESSION["au"])) {
    echo "<script>alert('Please Login First.'); window.location = '../adminsignin.php';</script>";
    exit();
}

$product_rs = Database::search("SELECT p.*, c.color_name, s.storage_name FROM `product` p LEFT JOIN `color` c ON p.color_id = c.color_id LEFT JOIN `storage` s ON p.storage_id = s.storage_id ORDER BY p.product_id DESC");
$product_num = $product_rs->num_rows;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-auto sidebar" id="sidebarMenu">
                <div class="logo-area d-flex align-items-center justify-content-between">
                    <img src="../img/logo.jpeg" alt="Xpress logo" class="sidebar-logo mb-3 " style="width: 170px; height: auto;" onerror="this.style.display='none'">
                    <button class="btn d-md-none p-0 border-0" id="closeSidebarBtn"><i class="bi bi-x-lg text-orange" style="color:#f97316;"></i></button>
                </div>

                <div class="nav-section-title">HOME</div>
                <a href="../adminPanel.php" class="nav-link-custom " data-section="dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>

                <!-- PRODUCT MANAGEMENT -->
                <div class="nav-section-title mt-2">PRODUCT MANAGEMENT</div>
                <a href="addProduct.php" class="nav-link-custom " data-section="addProduct">
                    <i class="bi bi-plus-circle"></i> Add Product
                </a>
                <a href="manageProducts.php" class="nav-link-custom active" data-section="manageProducts">
                    <i class="bi bi-archive"></i> Manage Products
                </a>
                

                <!-- USER MANAGEMENT -->
                <div class="nav-section-title mt-2">USER MANAGEMENT</div>
                <a href="manageUsers.php" class="nav-link-custom" data-section="viewUsers">
                    <i class="bi bi-people"></i> View Users
                </a>
                <a href="#" class="nav-link-custom" data-section="reports">
                    <i class="bi bi-file-text"></i> Reports
                </a>

                <!-- SUB REPORTS -->
                <div class="ps-4 ms-2 mt-1 mb-2">
                    <a href="#" class="nav-link-custom small py-1 ps-3" data-section="salesReport" style="font-size:0.85rem;"><i class="bi bi-graph-up"></i> Sales Report</a>
                    <a href="#" class="nav-link-custom small py-1 ps-3" data-section="productsReport" style="font-size:0.85rem;"><i class="bi bi-tag"></i> Products Report</a>
                    <a href="#" class="nav-link-custom small py-1 ps-3" data-section="userReport" style="font-size:0.85rem;"><i class="bi bi-person-badge"></i> User Report</a>
                </div>
            </div>

            <div class="col main-content p-3 p-md-4">
                <div class="top-navbar d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <h4 class="page-title mb-0">Manage Products</h4>
                    </div>
                    <span class="badge-orange"><i class="bi bi-person-circle"></i> Admin</span>
                </div>

                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="fw-bold mb-1">All Products</h5>
                                <p class="text-muted mb-0">Manage your catalog and preview product details.</p>
                            </div>
                            <a href="addProduct.php" class="btn btn-warning">Add New</a>
                        </div>

                        <div class="row g-4">
                            <?php if ($product_num > 0) { ?>
                                <?php for ($i = 0; $i < $product_num; $i++) {
                                    $product_data = $product_rs->fetch_assoc();
                                    $img_rs = Database::search("SELECT path FROM `product_img` WHERE product_id = '" . $product_data["product_id"] . "' LIMIT 1");
                                    $img_data = $img_rs->num_rows > 0 ? $img_rs->fetch_assoc() : null;
                                    $img_path = ($img_data && isset($img_data["path"])) ? $img_data["path"] : "../img/no-image.png";
                                    $all_img_rs = Database::search("SELECT img_id, path FROM `product_img` WHERE product_id = '" . $product_data["product_id"] . "' ORDER BY img_id ASC");
                                ?>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <img src="../<?php echo $img_path; ?>" class="card-img-top" alt="<?php echo $product_data['title']; ?>" style="height: 220px; object-fit: cover;">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start gap-2">
                                                    <div>
                                                        <h6 class="fw-bold mb-1"><?php echo $product_data['title']; ?></h6>
                                                        <p class="text-muted small mb-0">Qty: <?php echo $product_data['qty']; ?></p>
                                                    </div>
                                                    <span class="badge bg-warning text-dark">Rs.<?php echo number_format($product_data['price'], 2); ?></span>
                                                </div>
                                                <div class="mt-3 d-flex gap-2">
                                                    <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#productModal<?php echo $product_data['product_id']; ?>">View details</button>
                                                    <a href="editProduct.php?id=<?php echo $product_data['product_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="productModal<?php echo $product_data['product_id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header border-0">
                                                    <h5 class="modal-title fw-bold"><?php echo $product_data['title']; ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row g-3">
                                                        <div class="col-12 col-md-5">
                                                            <div id="productCarousel<?php echo $product_data['product_id']; ?>" class="carousel slide" data-bs-ride="carousel">
                                                                <div class="carousel-inner rounded-3 overflow-hidden">
                                                                    <?php for ($img_i = 0; $img_i < $all_img_rs->num_rows; $img_i++) {
                                                                        $img_row = $all_img_rs->fetch_assoc();
                                                                    ?>
                                                                        <div class="carousel-item <?php echo ($img_i == 0) ? 'active' : ''; ?>">
                                                                            <img src="../<?php echo $img_row['path']; ?>" class="d-block w-100" style="height: 320px; object-fit: cover;">
                                                                        </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <?php if ($all_img_rs->num_rows > 1) { ?>
                                                                    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel<?php echo $product_data['product_id']; ?>" data-bs-slide="prev">
                                                                        <span class="carousel-control-prev-icon"></span>
                                                                    </button>
                                                                    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel<?php echo $product_data['product_id']; ?>" data-bs-slide="next">
                                                                        <span class="carousel-control-next-icon"></span>
                                                                    </button>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-7">
                                                            <p class="text-muted mb-3"><?php echo $product_data['description']; ?></p>
                                                            <div class="d-flex flex-wrap gap-2 mb-3">
                                                                <span class="badge bg-light text-dark">Color: <?php echo $product_data['color_name']; ?></span>
                                                                <span class="badge bg-light text-dark">Storage: <?php echo $product_data['storage_name']; ?></span>
                                                                <span class="badge bg-light text-dark">Stock: <?php echo $product_data['qty']; ?></span>
                                                            </div>
                                                            <h4 class="fw-bold text-warning">Rs.<?php echo number_format($product_data['price'], 2); ?></h4>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0">
                                                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                                    <a href="editProduct.php?id=<?php echo $product_data['product_id']; ?>" class="btn btn-warning">Edit Product</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } else { ?>
                                <div class="col-12">
                                    <div class="alert alert-warning mb-0">No products found.</div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>