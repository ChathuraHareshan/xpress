<?php
session_start();
include "process/connection.php";

if (!isset($_SESSION["u"])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["u"]["user_id"];

$order_rs = Database::search("SELECT * FROM `orders` WHERE `user_id` = '$user_id' ORDER BY `history_id` DESC");
$order_num = $order_rs->num_rows;

$step_labels = [
    "Pending",
    "Processing",
    "Shipped",
    "Completed"
];

$step_colors = [
    "pending" => "warning",
    "processing" => "info",
    "shipped" => "primary",
    "completed" => "success"
];
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Orders | Xpress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="orders-page">
    <div class="topbar bg-white border-bottom">
        <div class="container d-flex flex-wrap justify-content-between align-items-center py-2 small text-muted">
            <div>support@xpress.com | +94 123 456 789</div>
            <div>
                <a href="home.php" class="text-warning text-decoration-none">Welcome, <?php echo htmlspecialchars($_SESSION["u"]["fname"] . ' ' . $_SESSION["u"]["lname"]); ?></a>
            </div>
        </div>
    </div>

    <header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
        <div class="container align-items-center">
            <a class="navbar-brand d-flex align-items-center gap-2" href="home.php">
                <img src="img/logo.jpeg" alt="Xpress logo" class="brand-logo" onerror="this.style.display='none'">
            </a>
            <div class="d-flex align-items-center gap-2 ms-auto">
                <a href="cart.php" class="btn btn-outline-warning btn-sm">Cart</a>
                <a href="watchlist.php" class="btn btn-outline-warning btn-sm"><i class="bi bi-heart"></i> Watchlist</a>
            </div>
        </div>
    </header>

    <main class="py-5">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
                <div>
                    <p class="text-warning fw-semibold mb-1">Order history</p>
                    <h2 class="h3 fw-bold mb-0">My Orders</h2>
                </div>
                <a href="home.php" class="btn btn-outline-warning">Continue Shopping</a>
            </div>

            <?php if ($order_num > 0) { ?>
                <div class="d-grid gap-4">
                    <?php for ($i = 0; $i < $order_num; $i++) {
                        $order_data = $order_rs->fetch_assoc();
                        $status = strtolower($order_data['status']);
                        $status_class = isset($step_colors[$status]) ? $step_colors[$status] : 'secondary';
                        $status_label = ucfirst($status);

                        $item_rs = Database::search(
                            "SELECT oi.*, p.title, p.price FROM `order_items` oi 
                             LEFT JOIN `product` p ON oi.product_id = p.product_id 
                             WHERE oi.order_history_id = '" . $order_data['history_id'] . "'"
                        );
                        $item_num = $item_rs->num_rows;

                        $step_index = array_search($status, ["pending", "processing", "shipped", "completed"]);
                        $step_index = $step_index === false ? -1 : $step_index;
                    ?>
                        <div class="order-card card shadow-sm border-0">
                            <div class="card-body p-4">
                                <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
                                    <div>
                                        <p class="mb-1 text-muted small">Order #<?php echo $order_data['history_id']; ?></p>
                                        <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($order_data['order_id']); ?></h5>
                                        <p class="text-muted mb-0">Placed on <?php echo date('F j, Y', strtotime($order_data['order_date'])); ?></p>
                                    </div>
                                    <div class="d-flex flex-column align-items-md-end gap-2">
                                        <span class="badge bg-<?php echo $status_class; ?> rounded-pill px-3 py-2"><?php echo $status_label; ?></span>
                                        <a href="invoice.php?id=<?php echo $order_data['history_id']; ?>" class="btn btn-outline-warning btn-sm">View invoice</a>
                                    </div>
                                </div>

                                <div class="row g-4">
                                    <div class="col-lg-8">
                                        <div class="order-items-panel">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="fw-semibold mb-0">Items</h6>
                                                <span class="text-muted small"><?php echo $item_num; ?> item<?php echo $item_num == 1 ? '' : 's'; ?></span>
                                            </div>
                                            <div class="d-grid gap-2">
                                                <?php for ($j = 0; $j < $item_num; $j++) {
                                                    $item_data = $item_rs->fetch_assoc();
                                                ?>
                                                    <div class="order-item-row d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <p class="mb-0 fw-semibold"><?php echo htmlspecialchars($item_data['title']); ?></p>
                                                            <small class="text-muted">Qty: <?php echo $item_data['order_qty']; ?></small>
                                                        </div>
                                                        <span class="fw-semibold">Rs.<?php echo number_format($item_data['price'] * $item_data['order_qty'], 2); ?></span>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="tracking-panel">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="fw-semibold mb-0">Track order</h6>
                                                <span class="text-muted small">Total: Rs.<?php echo number_format($order_data['amount'], 2); ?></span>
                                            </div>
                                            <div class="tracking-steps">
                                                <?php for ($t = 0; $t < count($step_labels); $t++) {
                                                    $is_active = $t <= $step_index;
                                                    $step_class = $is_active ? 'active' : '';
                                                    if ($status == 'cancelled') {
                                                        $step_class = 'cancelled';
                                                    }
                                                ?>
                                                    <div class="tracking-step <?php echo $step_class; ?>">
                                                        <span class="tracking-dot"></span>
                                                        <span class="tracking-label"><?php echo $step_labels[$t]; ?></span>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="empty-orders-card card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <div class="empty-orders-icon mb-3"><i class="bi bi-bag-check"></i></div>
                        <h4 class="fw-bold mb-2">No orders yet</h4>
                        <p class="text-muted mb-3">You haven’t placed any orders yet. Start shopping to see your order history here.</p>
                        <a href="home.php" class="btn btn-warning">Shop now</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
