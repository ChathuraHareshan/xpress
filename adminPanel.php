<?php
session_start();

include "process/connection.php"; 


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
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        <div class="col-auto sidebar" id="sidebarMenu">
            <div class="logo-area d-flex align-items-center justify-content-between">
                <h3><i class="bi bi-grid-1x2-fill me-2" style="color: #f97316;"></i> AdminHub</h3>
                <button class="btn d-md-none p-0 border-0" id="closeSidebarBtn"><i class="bi bi-x-lg text-orange" style="color:#f97316;"></i></button>
            </div>

            <div class="nav-section-title">HOME</div>
            <a href="#" class="nav-link-custom active" data-section="dashboard">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <!-- PRODUCT MANAGEMENT -->
            <div class="nav-section-title mt-2">PRODUCT MANAGEMENT</div>
            <a href="admin/addProduct.php" class="nav-link-custom" data-section="addProduct">
                <i class="bi bi-plus-circle"></i> Add Product
            </a>
            <a href="admin/manageProducts.php" class="nav-link-custom" data-section="manageProducts">
                <i class="bi bi-archive"></i> Manage Products
            </a>
            

            <!-- USER MANAGEMENT -->
            <div class="nav-section-title mt-2">USER MANAGEMENT</div>
            <a href="admin/manageUsers.php" class="nav-link-custom" data-section="viewUsers">
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
                    <h4 class="page-title mb-0" id="dynamicPageTitle">Dashboard</h4>
                </div>
                <div class="d-flex gap-2 align-items-center mt-2 mt-sm-0">
                    <span class="badge-orange"><i class="bi bi-bell me-1"></i> Notifications</span>
                    <span class="badge-orange"><i class="bi bi-person-circle"></i> Admin</span>
                </div>
            </div>

            <div id="dynamicContent">
                <!-- Dashboard content will load here -->
            </div>
            <div class="footer-demo text-center mt-5 pt-3 pb-2">
                <i class="bi bi-database-slash me-1"></i> Demo Admin Panel — Orange & White Theme | No DB connection errors
            </div>
        </div>
    </div>
</div>

<div id="mobileOverlay" class="d-md-none" style="position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.3); z-index:1040; display: none;"></div>

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