<?php
session_start();
include "../process/connection.php";

if (!isset($_SESSION["au"])) {
    echo "<script>alert('Please Login First.'); window.location = '../adminsignin.php';</script>";
    exit();
}

$user_rs = Database::search("SELECT u.*, s.status_name FROM `user` u LEFT JOIN `status` s ON u.status_id = s.id ORDER BY u.user_id DESC");
$user_num = $user_rs->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
                <a href="manageProducts.php" class="nav-link-custom " data-section="manageProducts">
                    <i class="bi bi-archive"></i> Manage Products
                </a>
                

                <!-- USER MANAGEMENT -->
                <div class="nav-section-title mt-2">USER MANAGEMENT</div>
                <a href="manageUsers.php" class="nav-link-custom active" data-section="viewUsers">
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
                    <h4 class="page-title mb-0">Manage Users</h4>
                </div>
                <span class="badge-orange"><i class="bi bi-person-circle"></i> Admin</span>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold mb-1">Registered Users</h5>
                            <p class="text-muted mb-0">Block or unblock users and review their details.</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($user_num > 0) { ?>
                                    <?php for ($i = 0; $i < $user_num; $i++) {
                                        $user_data = $user_rs->fetch_assoc();
                                        $address_rs = Database::search("SELECT * FROM `address` WHERE `user_email` = '" . $user_data['email'] . "' LIMIT 1");
                                        $address_data = $address_rs->num_rows > 0 ? $address_rs->fetch_assoc() : null;
                                        $is_active = ($user_data['status_id'] == 1);
                                    ?>
                                        <tr>
                                            <td><?php echo $user_data['user_id']; ?></td>
                                            <td><?php echo $user_data['fname'] . ' ' . $user_data['lname']; ?></td>
                                            <td><?php echo $user_data['email']; ?></td>
                                            <td><?php echo $user_data['mobile']; ?></td>
                                            <td>
                                                <span class="badge <?php echo $is_active ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?php echo $user_data['status_name']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo $user_data['joined_date']; ?></td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#userModal<?php echo $user_data['user_id']; ?>">View</button>
                                                    <form action="../process/toggleUserStatusProcess.php" method="post" class="d-inline">
                                                        <input type="hidden" name="user_id" value="<?php echo $user_data['user_id']; ?>">
                                                        <button type="submit" class="btn <?php echo $is_active ? 'btn-danger' : 'btn-success'; ?> btn-sm">
                                                            <?php echo $is_active ? 'Block' : 'Unblock'; ?>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="userModal<?php echo $user_data['user_id']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header border-0">
                                                        <h5 class="modal-title fw-bold">User Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="mb-2"><strong>Name:</strong> <?php echo $user_data['fname'] . ' ' . $user_data['lname']; ?></p>
                                                        <p class="mb-2"><strong>Email:</strong> <?php echo $user_data['email']; ?></p>
                                                        <p class="mb-2"><strong>Mobile:</strong> <?php echo $user_data['mobile']; ?></p>
                                                        <p class="mb-2"><strong>Joined Date:</strong> <?php echo $user_data['joined_date']; ?></p>
                                                        <p class="mb-2"><strong>Status:</strong> <?php echo $user_data['status_name']; ?></p>
                                                        <p class="mb-0"><strong>Address:</strong>
                                                            <?php echo ($address_data) ? $address_data['line1'] . ', ' . $address_data['line2'] . ', ' . $address_data['city'] : 'No address provided'; ?>
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer border-0">
                                                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No users found.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
