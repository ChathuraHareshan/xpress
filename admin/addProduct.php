<?php
session_start();

include "../process/connection.php";

if (isset($_SESSION["au"])) {
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
        <title>Admin Panel | Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
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
                    <a href="#" class="nav-link-custom active" data-section="addProduct">
                        <i class="bi bi-plus-circle"></i> Add Product
                    </a>
                    <a href="manageProducts.php" class="nav-link-custom" data-section="manageProducts">
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

                <!-- MAIN CONTENT AREA -->
                <div class="col main-content p-3 p-md-4" id="mainContentArea">
                    <div class="top-navbar d-flex flex-wrap justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <button class="btn d-md-none" id="menuToggleBtn"><i class="bi bi-list fs-3" style="color:#f97316;"></i></button>
                            <h4 class="page-title mb-0" id="dynamicPageTitle">Add Product</h4>
                        </div>
                        <div class="d-flex gap-2 align-items-center mt-2 mt-sm-0">
                            <span class="badge-orange"><i class="bi bi-bell me-1"></i> Notifications</span>
                            <span class="badge-orange"><i class="bi bi-person-circle"></i> Admin</span>
                        </div>
                    </div>

                    <div id="dynamicContent">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
                                    <div>
                                        <h2 class="h5 fw-bold mb-1">Add New Product</h2>
                                        <p class="text-muted mb-0">Create a new product with title, description, variant options, pricing, and images.</p>
                                    </div>
                                </div>

                                <form action="" method="post" enctype="multipart/form-data">
                                    <div class="row g-3">
                                        <div class="col-12 col-lg-6">
                                            <label for="title" class="form-label fw-semibold">Product title</label>
                                            <input type="text" id="title" class="form-control form-control-lg" placeholder="Enter product title" required>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="qty" class="form-label fw-semibold">Quantity</label>
                                            <input type="number" id="qty" min="0" class="form-control form-control-lg" placeholder="Available quantity" required>
                                        </div>
                                        <div class="col-12">
                                            <label for="description" class="form-label fw-semibold">Description</label>
                                            <textarea id="description" name="description" rows="4" class="form-control form-control-lg" placeholder="Write the product description"></textarea>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label for="productColor" class="form-label fw-semibold">Color</label>
                                            <select id="productColor" name="color" class="form-select form-select-lg" required>
                                                <option value="" disabled selected>Select color</option>
                                                <?php

                                                $color_rs = Database::search("SELECT * FROM `color`");
                                                $color_num = $color_rs->num_rows;
                                                for ($i = 0; $i < $color_num; $i++) {
                                                    $color_data = $color_rs->fetch_assoc();
                                                ?>
                                                    <option value="<?php echo $color_data["color_id"]; ?>"><?php echo $color_data["color_name"]; ?></option>
                                                <?php
                                                }

                                                ?>

                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label for="productStorage" class="form-label fw-semibold">Storage</label>
                                            <select id="productStorage" name="storage" class="form-select form-select-lg" required>
                                                <option value="" disabled selected>Select storage</option>
                                                <?php
                                                $storage_rs = Database::search("SELECT * FROM `storage`");
                                                $storage_num = $storage_rs->num_rows;
                                                for ($i = 0; $i < $storage_num; $i++) {
                                                    $storage_data = $storage_rs->fetch_assoc();
                                                ?>
                                                    <option value="<?php echo $storage_data["storage_id"]; ?>"><?php echo $storage_data["storage_name"]; ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label for="productPrice" class="form-label fw-semibold">Price</label>
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text bg-white text-orange border-0">$</span>
                                                <input type="number" id="price" name="price" min="0" step="0.01" class="form-control" placeholder="0.00" required>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Product images</label>
                                            <div class="row g-3">
                                                <div class="col-12 col-md-4">
                                                    <div class="border rounded-3 p-3 h-100">
                                                        <p class="small text-muted mb-2">Image 1</p>
                                                        <input type="file" name="images[]" class="form-control" accept="image/*" required>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="border rounded-3 p-3 h-100">
                                                        <p class="small text-muted mb-2">Image 2</p>
                                                        <input type="file" name="images[]" class="form-control" accept="image/*">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="border rounded-3 p-3 h-100">
                                                        <p class="small text-muted mb-2">Image 3</p>
                                                        <input type="file" name="images[]" class="form-control" accept="image/*">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="mt-4 d-flex flex-column flex-sm-row gap-2">
                                        <button type="button" class="btn btn-warning btn-lg" onclick="saveProduct()">Save product</button>
                                        <button type="reset" class="btn btn-outline-secondary btn-lg">Reset form</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="footer-demo text-center mt-5 pt-3 pb-2">
                        <i class="bi bi-database-slash me-1"></i> Demo Admin Panel — Orange & White Theme | No DB connection errors
                    </div>
                </div>
            </div>
        </div>

        <div id="mobileOverlay" class="d-md-none" style="position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.3); z-index:1040; display: none;"></div>

        <script src="../js/admin.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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

<?php

?>