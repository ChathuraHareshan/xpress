<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Xpress - Login | Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-light"> 

<?php
include "process/connection.php";
?>

    <div class="min-vh-100 d-flex align-items-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-9">
                    <div class="text-center mb-4">
                        <img src="img/logo.jpeg" class="brand-logo mx-auto d-block" alt="Xpress logo" onerror="this.style.display='none'">
                        <h1 class="h3 fw-bold mb-1">Welcome to Xpress</h1>
                        <p class="text-muted mb-0">Sign in or create an account to continue.</p>
                    </div>

                    <div class="card shadow-sm border-0 overflow-hidden orange-card">
                        <div class="card-body p-4 p-sm-5">
                            <div class="d-flex gap-2 justify-content-center mb-4 toggle-btn-group">
                                <button id="loginToggle" type="button" class="btn btn-warning" onclick="showLoginForm()">Login</button>
                                <button id="signupToggle" type="button" class="btn btn-outline-warning" onclick="showSignupForm()">Sign Up</button>
                            </div>

                            <div id="loginSection" class="form-section">
                                <h2 class="h5 mb-3 text-dark">Sign in</h2>

                                <?php
                                $email = "";
                                $password = "";

                                if (isset($_COOKIE["email"])) {
                                    $email = $_COOKIE["email"];
                                }

                                if (isset($_COOKIE["password"])) {
                                    $password = $_COOKIE["password"];
                                }
                                ?>

                                <div class="mb-3">
                                    <label for="loginemail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="loginemail" placeholder="Enter your email" value="<?php echo $email; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="loginpassword" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="loginpassword" placeholder="Enter your password" value="<?php echo $password; ?>">
                                </div>
                                <div class="mb-4 form-check">
                                    <input type="checkbox" class="form-check-input" id="rememberme">
                                    <label class="form-check-label" for="rememberme">Remember me</label>
                                </div>
                                <button type="button" class="btn btn-warning w-100" onclick="login()">Login</button>
                            </div>

                            <div id="signupSection" class="form-section d-none">
                                <h2 class="h5 mb-3 text-dark">Create account</h2>
                                <div class="row gx-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="fname" class="form-label">First name</label>
                                        <input type="text" class="form-control" id="fname" placeholder="Enter your first name">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="lname" class="form-label">Last name</label>
                                        <input type="text" class="form-control" id="lname" placeholder="Enter your last name">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="mobile" class="form-label">Mobile</label>
                                    <input type="text" class="form-control" id="mobile" placeholder="Enter your mobile number">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" placeholder="Enter your email">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" placeholder="Enter your password">
                                </div>
                                <div class="mb-3">
                                    <label for="line1" class="form-label">Address line 1</label>
                                    <input type="text" class="form-control" id="line1" placeholder="Enter your address line 1">
                                </div>
                                <div class="mb-3">
                                    <label for="line2" class="form-label">Address line 2</label>
                                    <input type="text" class="form-control" id="line2" placeholder="Enter your address line 2">
                                </div>
                                <div class="row gx-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control" id="city" placeholder="Enter your city">
                                    </div>
                                    <div class="col-md-6 mb-3">

                                    <?php
                                    
                                    $district_rs = Database::search("SELECT * FROM `district`");
                                    $district_num = $district_rs->num_rows;
                                    
                                    ?>

                                        <label for="district" class="form-label">District</label>
                                        <select id="district" class="form-select">
                                            <option value="">Select your district</option>
                                            <?php
                                            for($i=0; $i<$district_num; $i++){
                                                $district_data = $district_rs->fetch_assoc();
                                                ?>
                                            <option value="<?php echo $district_data["district_id"]; ?>"><?php echo $district_data["district_name"]; ?></option>
                                            <?php
                                            }
                                            ?>
                                            

                                        </select>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-warning w-100" onclick="signup()">Sign Up</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/script.js"></script>
</body>

</html>