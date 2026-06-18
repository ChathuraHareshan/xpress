<?php
session_start();
include "../process/connection.php";

if (!isset($_SESSION["au"])) {
    echo "<script>alert('Please Login First.'); window.location = '../adminsignin.php';</script>";
    exit();
}

$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT o.*, u.fname, u.lname, u.email, u.mobile FROM `orders` o LEFT JOIN `user` u ON o.user_id = u.user_id";
if ($status_filter != '') {
    $query .= " WHERE o.status = '" . $status_filter . "'";
}
$query .= " ORDER BY o.history_id DESC";

$order_rs = Database::search($query);
$order_num = $order_rs->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<div class="container-fluid p-0">
    <div class="row g-0">
        <div class="col-auto sidebar" id="sidebarMenu">
            <div class="logo-area d-flex align-items-center justify-content-between">
                <img src="../img/logo.jpeg" alt="Xpress logo" class="sidebar-logo mb-3" style="width: 170px; height: auto;" onerror="this.style.display='none'">
                <button class="btn d-md-none p-0 border-0" id="closeSidebarBtn"><i class="bi bi-x-lg text-orange" style="color:#f97316;"></i></button>
            </div>
            <div class="nav-section-title">HOME</div>
            <a href="../adminPanel.php" class="nav-link-custom"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <div class="nav-section-title mt-2">PRODUCT MANAGEMENT</div>
            <a href="addProduct.php" class="nav-link-custom"><i class="bi bi-plus-circle"></i> Add Product</a>
            <a href="manageProducts.php" class="nav-link-custom"><i class="bi bi-archive"></i> Manage Products</a>
            <div class="nav-section-title mt-2">ORDER MANAGEMENT</div>
            <a href="manageOrders.php" class="nav-link-custom active"><i class="bi bi-cart-check"></i> Manage Orders</a>
            <div class="nav-section-title mt-2">USER MANAGEMENT</div>
            <a href="manageUsers.php" class="nav-link-custom"><i class="bi bi-people"></i> Manage Users</a>
        </div>

        <div class="col main-content p-3 p-md-4">
            <div class="top-navbar d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h4 class="page-title mb-0">Manage Orders</h4>
                </div>
                <span class="badge-orange"><i class="bi bi-person-circle"></i> Admin</span>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
                        <div>
                            <h5 class="fw-bold mb-1">Order List</h5>
                            <p class="text-muted mb-0">Review customer orders and update their current status.</p>
                        </div>
                        <form method="GET" class="d-flex gap-2">
                            <select name="status" class="form-select">
                                <option value="" <?php echo ($status_filter == '') ? 'selected' : ''; ?>>All statuses</option>
                                <option value="pending" <?php echo ($status_filter == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo ($status_filter == 'processing') ? 'selected' : ''; ?>>Processing</option>
                                <option value="shipped" <?php echo ($status_filter == 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                                <option value="completed" <?php echo ($status_filter == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo ($status_filter == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <button type="submit" class="btn btn-warning">Filter</button>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($order_num > 0) { ?>
                                    <?php for ($i = 0; $i < $order_num; $i++) {
                                        $order_data = $order_rs->fetch_assoc();
                                        $status_class = 'bg-secondary';
                                        if ($order_data['status'] == 'completed') $status_class = 'bg-success';
                                        elseif ($order_data['status'] == 'pending') $status_class = 'bg-warning text-dark';
                                        elseif ($order_data['status'] == 'processing') $status_class = 'bg-info text-dark';
                                        elseif ($order_data['status'] == 'shipped') $status_class = 'bg-primary';
                                        elseif ($order_data['status'] == 'cancelled') $status_class = 'bg-danger';
                                    ?>
                                        <tr>
                                            <td>#<?php echo $order_data['history_id']; ?></td>
                                            <td><?php echo $order_data['fname'] . ' ' . $order_data['lname']; ?></td>
                                            <td><?php echo $order_data['email']; ?></td>
                                            <td><?php echo $order_data['order_date']; ?></td>
                                            <td>Rs.<?php echo number_format($order_data['amount'], 2); ?></td>
                                            <td><?php echo $order_data['payment_method']; ?></td>
                                            <td>
                                                <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($order_data['status']); ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#orderModal<?php echo $order_data['history_id']; ?>">View</button>
                                                    <select class="form-select form-select-sm order-status-select" data-order-id="<?php echo $order_data['history_id']; ?>">
                                                        <option value="pending" <?php echo ($order_data['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="processing" <?php echo ($order_data['status'] == 'processing') ? 'selected' : ''; ?>>Processing</option>
                                                        <option value="shipped" <?php echo ($order_data['status'] == 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                                                        <option value="completed" <?php echo ($order_data['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                                        <option value="cancelled" <?php echo ($order_data['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No orders found.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <?php
                        if ($order_num > 0) {
                            $order_rs->data_seek(0);
                            for ($i = 0; $i < $order_num; $i++) {
                                $order_data = $order_rs->fetch_assoc();
                        ?>
                            <div class="modal fade" id="orderModal<?php echo $order_data['history_id']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header border-0">
                                            <h5 class="modal-title fw-bold">Order Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            $item_rs = Database::search("SELECT oi.*, p.title, p.price FROM `order_items` oi LEFT JOIN `product` p ON oi.product_id = p.product_id WHERE oi.order_history_id = '" . $order_data['history_id'] . "'");
                                            $item_num = $item_rs->num_rows;
                                            ?>
                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md-6">
                                                    <p class="mb-1"><strong>Customer:</strong> <?php echo $order_data['fname'] . ' ' . $order_data['lname']; ?></p>
                                                    <p class="mb-1"><strong>Email:</strong> <?php echo $order_data['email']; ?></p>
                                                    <p class="mb-1"><strong>Mobile:</strong> <?php echo $order_data['mobile']; ?></p>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <p class="mb-1"><strong>Order Date:</strong> <?php echo $order_data['order_date']; ?></p>
                                                    <p class="mb-1"><strong>Payment Method:</strong> <?php echo $order_data['payment_method']; ?></p>
                                                    <p class="mb-1"><strong>Order ID:</strong> <?php echo $order_data['order_id']; ?></p>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>Qty</th>
                                                            <th>Price</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if ($item_num > 0) { ?>
                                                            <?php for ($j = 0; $j < $item_num; $j++) {
                                                                $item_data = $item_rs->fetch_assoc();
                                                            ?>
                                                                <tr>
                                                                    <td><?php echo $item_data['title']; ?></td>
                                                                    <td><?php echo $item_data['order_qty']; ?></td>
                                                                    <td>Rs.<?php echo number_format($item_data['price'], 2); ?></td>
                                                                </tr>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <tr>
                                                                <td colspan="3" class="text-center text-muted">No items found.</td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../js/admin.js"></script>
</body>
</html>
