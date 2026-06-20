<?php
session_start();
include "process/connection.php";

if (!isset($_SESSION["u"])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["u"]["user_id"];
$user_rs = Database::search("SELECT * FROM `user` WHERE `user_id` = '$user_id'");
$user_data = $user_rs->fetch_assoc();

$address_rs = Database::search("SELECT * FROM `address` WHERE `user_email` = '" . $user_data['email'] . "'");
$address_data = $address_rs->num_rows > 0 ? $address_rs->fetch_assoc() : null;

$district_rs = Database::search("SELECT * FROM `district` ORDER BY `district_name`");
$district_num = $district_rs->num_rows;

$joined_date = date('F j, Y', strtotime($user_data['joined_date']));
$initials = strtoupper(substr($user_data['fname'], 0, 1) . substr($user_data['lname'], 0, 1));
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Profile | Xpress</title>
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
                <a href="myOrders.php" class="btn btn-outline-warning btn-sm">Orders</a>
            </div>
        </div>
    </header>

    <main class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card profile-sidebar shadow-sm border-0">
                        <div class="card-body p-4 text-center">
                            <div class="profile-avatar mx-auto mb-3"><?php echo $initials; ?></div>
                            <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($user_data['fname'] . ' ' . $user_data['lname']); ?></h4>
                            <p class="text-muted mb-3"><?php echo htmlspecialchars($user_data['email']); ?></p>
                            <span class="badge bg-warning text-dark rounded-pill">Member since <?php echo $joined_date; ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <?php if (isset($_GET['success'])) { ?>
                        <div class="alert alert-success rounded-3">Profile updated successfully.</div>
                    <?php } ?>
                    <?php if (isset($_GET['error'])) { ?>
                        <div class="alert alert-danger rounded-3"><?php echo htmlspecialchars($_GET['error']); ?></div>
                    <?php } ?>

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <p class="text-warning fw-semibold mb-1">Account</p>
                                    <h4 class="fw-bold mb-0">Personal details</h4>
                                </div>
                            </div>
                            <form method="POST" action="process/updateProfileProcess.php">
                                <input type="hidden" name="old_email" value="<?php echo htmlspecialchars($user_data['email']); ?>">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">First name</label>
                                        <input type="text" class="form-control" name="fname" value="<?php echo htmlspecialchars($user_data['fname']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Last name</label>
                                        <input type="text" class="form-control" name="lname" value="<?php echo htmlspecialchars($user_data['lname']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Mobile</label>
                                        <input type="text" class="form-control" name="mobile" value="<?php echo htmlspecialchars($user_data['mobile']); ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-warning">Save changes</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <p class="text-warning fw-semibold mb-1">Shipping</p>
                                    <h4 class="fw-bold mb-0">Address details</h4>
                                </div>
                            </div>
                            <form method="POST" action="process/updateProfileProcess.php">
                                <input type="hidden" name="old_email" value="<?php echo htmlspecialchars($user_data['email']); ?>">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Address line 1</label>
                                        <input type="text" class="form-control" name="line1" value="<?php echo htmlspecialchars($address_data['line1'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Address line 2</label>
                                        <input type="text" class="form-control" name="line2" value="<?php echo htmlspecialchars($address_data['line2'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">City</label>
                                        <input type="text" class="form-control" name="city" value="<?php echo htmlspecialchars($address_data['city'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">District</label>
                                        <select class="form-select" name="district">
                                            <option value="">Select district</option>
                                            <?php for ($i = 0; $i < $district_num; $i++) {
                                                $district_data = $district_rs->fetch_assoc();
                                                $selected = ($address_data && $address_data['district_id'] == $district_data['district_id']) ? 'selected' : '';
                                            ?>
                                                <option value="<?php echo $district_data['district_id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($district_data['district_name']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-outline-warning">Update address</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
