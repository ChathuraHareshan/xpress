<?php
session_start();
include "process/connection.php";

if (!isset($_SESSION["u"])) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION["u"]["user_id"];
$order_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($order_id <= 0) {
    header("Location: cart.php");
    exit();
}

$order_rs = Database::search("SELECT * FROM `orders` WHERE `history_id` = '$order_id' AND `user_id` = '$user_id'");
if ($order_rs->num_rows == 0) {
    header("Location: cart.php");
    exit();
}
$order_data = $order_rs->fetch_assoc();

$items_rs = Database::search("SELECT * FROM `order_items` WHERE `order_history_id` = '$order_id'");
$items_num = $items_rs->num_rows;

$order_date = date("F j, Y", strtotime($order_data['order_date']));
$subtotal = 0;
$items_list = array();

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Xpress Invoice - Order #<?php echo $order_data['order_id']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: #fff7f0;
        }
        .invoice-container {
            max-width: 1100px;
            margin: 0 auto;
        }
        .invoice-card {
            border-radius: 1.75rem;
            border: 1px solid rgba(255, 159, 67, 0.18);
            background: rgba(255, 255, 255, 0.98);
        }
        .invoice-header {
            border-bottom: 1px solid rgba(255, 159, 67, 0.12);
        }
        .invoice-number {
            background: rgba(255, 138, 0, 0.12);
            color: #bf6200;
            border-radius: 999px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
        }
        .invoice-table thead {
            background: #fff1e4;
        }
        .invoice-table th,
        .invoice-table td {
            vertical-align: middle;
        }
        .invoice-table .item-description {
            color: #6e4d0e;
            font-size: 0.95rem;
        }
        .invoice-summary {
            border-radius: 1.25rem;
            background: rgba(255, 248, 242, 0.9);
            border: 1px solid rgba(255, 159, 67, 0.18);
        }
        .btn-invoice {
            min-width: 160px;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .status-paid {
            background: #d4edda;
            color: #155724;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #fff;
                padding: 0;
                margin: 0;
            }
            .invoice-card {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="container invoice-container py-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4 no-print">
            <div>
                <h1 class="h3 fw-bold mb-1">Order Confirmation</h1>
                <p class="text-muted mb-0">Thank you for shopping with Xpress Store.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-warning btn-invoice" onclick="window.print()">
                    <i class="bi bi-printer-fill me-2"></i>Print
                </button>
                <a href="home.php" class="btn btn-outline-warning btn-invoice">
                    <i class="bi bi-shop me-2"></i>Continue Shopping
                </a>
            </div>
        </div>

        <div id="invoiceContent" class="invoice-card p-4 p-md-5">
            <!-- Header -->
            <div class="d-flex flex-column flex-lg-row justify-content-between invoice-header pb-4 mb-4 gap-3">
                <div>
                    <a href="home.php" class="text-decoration-none d-inline-flex align-items-center gap-2 mb-3">
                        <span class="fw-bold text-dark fs-4">Xpress Store</span>
                    </a>
                    <p class="text-muted mb-1">47 Orange Street, Colombo 07</p>
                    <p class="text-muted mb-0">support@xpress.com · +94 123 456 789</p>
                </div>
                <div class="text-md-end">
                    <h2 class="h5 fw-bold mb-2">Invoice</h2>
                    <div class="invoice-number mb-2">
                        <i class="bi bi-receipt me-2"></i><?php echo $order_data['order_id']; ?>
                    </div>
                    <p class="mb-1"><span class="text-muted">Order date:</span> <?php echo $order_date; ?></p>
                    <p class="mb-0"><span class="text-muted">Payment method:</span> <?php echo $order_data['payment_method']; ?></p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12 col-md-6 mb-3 mb-md-0">
                    <h3 class="h6 fw-semibold mb-2">Bill to</h3>
                    <?php
                    $user_rs = Database::search("SELECT * FROM `user` WHERE `user_id` = '$user_id'");
                    $user_data = $user_rs->fetch_assoc();
                    $address_rs = Database::search("SELECT * FROM `address` WHERE `user_email` = '" . $user_data['email'] . "'");
                    ?>
                    <p class="mb-1 fw-semibold"><?php echo htmlspecialchars($user_data['fname'] . ' ' . $user_data['lname']); ?></p>
                    <p class="text-muted mb-1"><?php echo htmlspecialchars($user_data['email']); ?></p>
                    <p class="text-muted mb-0"><?php echo htmlspecialchars($user_data['mobile']); ?></p>
                    <?php if ($address_rs->num_rows > 0) { 
                        $address_data = $address_rs->fetch_assoc();
                    ?>
                        <p class="text-muted mt-2 mb-0"><?php echo htmlspecialchars($address_data['line1'] . ', ' . $address_data['line2'] . ', ' . $address_data['city']); ?></p>
                    <?php } ?>
                </div>
                <div class="col-12 col-md-6 text-md-end">
                    <h3 class="h6 fw-semibold mb-2">Payment info</h3>
                    <p class="mb-1"><span class="text-muted">Method:</span> <?php echo $order_data['payment_method']; ?></p>
                    <p class="mb-0">
                        <span class="text-muted">Status:</span> 
                        <span class="status-badge status-paid">Paid</span>
                    </p>
                </div>
            </div>

            <!-- Order Items Table -->
            <div class="table-responsive mb-4">
                <table class="table invoice-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Item</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Unit price</th>
                            <th scope="col" class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($item_data = $items_rs->fetch_assoc()) {
                            $product_rs = Database::search("SELECT * FROM `product` WHERE `product_id` = '" . $item_data["product_id"] . "'");
                            $product_data = $product_rs->fetch_assoc();
                            
                            $color_rs = Database::search("SELECT * FROM `color` WHERE `color_id` = '" . $product_data["color_id"] . "'");
                            $color_data = $color_rs->fetch_assoc();
                            
                            $storage_rs = Database::search("SELECT * FROM `storage` WHERE `storage_id` = '" . $product_data["storage_id"] . "'");
                            $storage_data = $storage_rs->fetch_assoc();
                            
                            $item_total = $product_data["price"] * $item_data["order_qty"];
                            $subtotal += $item_total;
                        ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($product_data['title']); ?></div>
                                    <div class="item-description">
                                        Color: <?php echo $color_data['color_name']; ?> | 
                                        Storage: <?php echo $storage_data['storage_name']; ?>
                                    </div>
                                </td>
                                <td><?php echo $item_data['order_qty']; ?></td>
                                <td>Rs. <?php echo number_format($product_data['price'], 2); ?></td>
                                <td class="text-end">Rs. <?php echo number_format($item_total, 2); ?></td>
                            </tr>
                        <?php
                            $items_list[] = $product_data['title'];
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Order Summary -->
            <div class="row justify-content-end">
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="invoice-summary p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Subtotal</span>
                            <strong>Rs. <?php echo number_format($subtotal, 2); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Delivery fee</span>
                            <strong>Rs. <?php 
                                $delivery_fee = $order_data['amount'] - $subtotal;
                                echo number_format($delivery_fee, 2); 
                            ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Discount</span>
                            <strong class="text-success">Rs. 0.00</strong>
                        </div>
                        <div class="border-top pt-3 mt-3 d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Total paid</span>
                            <span class="fs-4 fw-bold text-warning">Rs. <?php echo number_format($order_data['amount'], 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer Message -->
            <div class="text-center mt-4 pt-3 border-top">
                <p class="text-muted small mb-0">
                    <i class="bi bi-check-circle-fill text-success me-1"></i> 
                    Thank you for your purchase! Your order will be delivered within 3-5 business days.
                </p>
                <p class="text-muted small mt-2 mb-0">
                    For any inquiries, please contact our support team at support@xpress.com
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>

const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('print') && urlParams.get('print') === 'true') {
            setTimeout(() => {
                window.print();
            }, 500);
        }
        
        function downloadInvoice() {
            const printContent = document.getElementById('invoiceContent').cloneNode(true);
            const originalTitle = document.title;
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write('<!DOCTYPE html>');
            printWindow.document.write('<html>');
            printWindow.document.write('<head>');
            printWindow.document.write('<title>Invoice - <?php echo $order_data['order_id']; ?></title>');
            printWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">');
            printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">');
            printWindow.document.write('<style>');
            printWindow.document.write('body { padding: 30px; }');
            printWindow.document.write('.no-print { display: none !important; }');
            printWindow.document.write('.invoice-card { border: none; }');
            printWindow.document.write('</style>');
            printWindow.document.write('</head>');
            printWindow.document.write('<body>');
            printWindow.document.write(printContent.outerHTML);
            printWindow.document.write('</body>');
            printWindow.document.write('</html>');
            printWindow.document.close();
            
            printWindow.onload = function() {
                printWindow.print();
                printWindow.close();
            };
        }
    </script>
</body>
</html>