<?php
session_start();
include "../process/connection.php";

if (!isset($_SESSION["au"])) {
    echo "<script>alert('Please Login First.'); window.location = '../adminsignin.php';</script>";
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid product selected.'); window.location = 'manageProducts.php';</script>";
    exit();
}

$product_id = (int) $_GET['id'];
$product_rs = Database::search("SELECT * FROM `product` WHERE `product_id` = '$product_id'");
if ($product_rs->num_rows == 0) {
    echo "<script>alert('Product not found.'); window.location = 'manageProducts.php';</script>";
    exit();
}

$product_data = $product_rs->fetch_assoc();
$color_rs = Database::search("SELECT * FROM `color`");
$storage_rs = Database::search("SELECT * FROM `storage`");
$img_rs = Database::search("SELECT img_id, path FROM `product_img` WHERE product_id = '$product_id' ORDER BY img_id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
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
                    <a href="#" class="nav-link-custom " data-section="addProduct">
                        <i class="bi bi-plus-circle"></i> Add Product
                    </a>
                    <a href="#" class="nav-link-custom active" data-section="manageProducts">
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
                    <h4 class="page-title mb-0">Edit Product</h4>
                </div>
                <span class="badge-orange"><i class="bi bi-person-circle"></i> Admin</span>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold mb-1">Update Product Details</h5>
                            <p class="text-muted mb-0">Edit the product information and upload new images if needed.</p>
                        </div>
                        <a href="manageProducts.php" class="btn btn-outline-secondary">Back</a>
                    </div>

                    <form action="../process/updateProductProcess.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" value="<?php echo $product_data['product_id']; ?>">
                        <div class="row g-3">
                            <div class="col-12 col-lg-6">
                                <label class="form-label fw-semibold">Product title</label>
                                <input type="text" name="title" class="form-control form-control-lg" value="<?php echo htmlspecialchars($product_data['title']); ?>" required>
                            </div>
                            <div class="col-12 col-lg-6">
                                <label class="form-label fw-semibold">Quantity</label>
                                <input type="number" name="qty" min="0" class="form-control form-control-lg" value="<?php echo $product_data['qty']; ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea name="description" rows="4" class="form-control form-control-lg" required><?php echo htmlspecialchars($product_data['description']); ?></textarea>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Color</label>
                                <select name="color" class="form-select form-select-lg" required>
                                    <?php while ($color_data = $color_rs->fetch_assoc()) { ?>
                                        <option value="<?php echo $color_data['color_id']; ?>" <?php echo ($product_data['color_id'] == $color_data['color_id']) ? 'selected' : ''; ?>>
                                            <?php echo $color_data['color_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Storage</label>
                                <select name="storage" class="form-select form-select-lg" required>
                                    <?php while ($storage_data = $storage_rs->fetch_assoc()) { ?>
                                        <option value="<?php echo $storage_data['storage_id']; ?>" <?php echo ($product_data['storage_id'] == $storage_data['storage_id']) ? 'selected' : ''; ?>>
                                            <?php echo $storage_data['storage_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Price</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white text-warning">Rs.</span>
                                    <input type="number" name="price" min="0" step="0.01" class="form-control" value="<?php echo $product_data['price']; ?>" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Current product images</label>
                                <div class="row g-3">
                                    <?php if ($img_rs->num_rows > 0) { ?>
                                        <?php while ($img_data = $img_rs->fetch_assoc()) { ?>
                                            <div class="col-12 col-md-4">
                                                <div class="border rounded-3 p-2 h-100">
                                                    <img src="../<?php echo $img_data['path']; ?>" class="img-fluid rounded-2 mb-2" style="height: 150px; width: 100%; object-fit: cover;">
                                                    <label class="d-flex align-items-center gap-2 small mb-0">
                                                        <input type="checkbox" name="delete_images[]" value="<?php echo $img_data['img_id']; ?>">
                                                        Delete this image
                                                    </label>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <div class="col-12">
                                            <div class="alert alert-warning mb-0">No images available for this product.</div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Add more images</label>
                                <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-warning btn-lg">Update Product</button>
                            <a href="manageProducts.php" class="btn btn-outline-secondary btn-lg">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
