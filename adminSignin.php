<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xpress Admin Sign In</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    
</head>

<body class="auth-page">

    <main class="auth-wrapper d-flex align-items-center justify-content-center min-vh-100">
        <div class="auth-card card shadow-lg border-0">
            <div class="card-body px-4 py-5">
                <div class="text-center mb-4">
                    <img src="img/logo.jpeg" alt="Xpress logo" class="auth-logo mb-3" onerror="this.style.display='none'">
                    <h1 class="h4 fw-bold">Admin Sign In</h1>
                    <p class="text-muted mb-0">Sign in with your admin email to receive a one-time verification code.</p>
                </div>

                <form id="adminSigninForm" class="mt-4">
                    <div class="mb-4">
                        <label for="email" class="form-label">Admin email</label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="admin@xpress.com" required>
                    </div>

                    <div class="d-grid gap-3 mb-3">
                        <button type="button" class="btn btn-warning btn-lg" onclick="adminVerification();">Send OTP</button>
                        <a href="home.php" class="btn btn-outline-warning btn-lg">Back to home</a>
                    </div>
                </form>

                <p class="text-center text-muted small mb-0">Protected admin access powered by Xpress.</p>
            </div>
        </div>
    </main>

    <div class="modal fade" tabindex="-1" id="verificationModel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-sm">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title">Admin Verification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Enter the verification code sent to your admin email.</p>
                    <label class="form-label" for="vcode">Verification code</label>
                    <input type="text" class="form-control form-control-lg" id="vcode" placeholder="123456">
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning" onclick="verify();">Verify</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/admin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>