document.addEventListener("DOMContentLoaded", function () {
    showLoginForm();
});

function updateToggleButtons(activeSection) {
    var loginToggle = document.getElementById("loginToggle");
    var signupToggle = document.getElementById("signupToggle");

    loginToggle.classList.toggle("btn-primary", activeSection === "login");
    loginToggle.classList.toggle("btn-outline-primary", activeSection !== "login");
    signupToggle.classList.toggle("btn-success", activeSection === "signup");
    signupToggle.classList.toggle("btn-outline-primary", activeSection !== "signup");

    loginToggle.classList.toggle("active", activeSection === "login");
    signupToggle.classList.toggle("active", activeSection === "signup");
}

function showLoginForm() {
    document.getElementById("loginSection").classList.remove("d-none");
    document.getElementById("signupSection").classList.add("d-none");
    updateToggleButtons("login");
}

function showSignupForm() {
    document.getElementById("signupSection").classList.remove("d-none");
    document.getElementById("loginSection").classList.add("d-none");
    updateToggleButtons("signup");
}

function signup() {
    var fname = document.getElementById("fname").value.trim();
    var lname = document.getElementById("lname").value.trim();
    var email = document.getElementById("email").value.trim();
    var password = document.getElementById("password").value.trim();
    var mobile = document.getElementById("mobile").value.trim();
    var line1 = document.getElementById("line1").value.trim();
    var line2 = document.getElementById("line2").value.trim();
    var city = document.getElementById("city").value.trim();
    var district = document.getElementById("district").value;

    if (fname === "" || lname === "" || email === "" || password === "" || mobile === "" || line1 === "" || line2 === "" || city === "" || district === "") {
        Swal.fire({
            title: "Incomplete form",
            text: "Please fill in all the fields.",
            icon: "warning"
        });
        return;
    }

    var form = new FormData();
    form.append("f", fname);
    form.append("l", lname);
    form.append("e", email);
    form.append("p", password);
    form.append("m", mobile);
    form.append("l1", line1);
    form.append("l2", line2);
    form.append("c", city);
    form.append("d", district);

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState === 4 && request.status === 200) {
            var response = request.responseText;
            if (response === "success") {
                Swal.fire({
                    title: "Sign up success!",
                    icon: "success",
                    timer: 1800,
                    showConfirmButton: false
                }).then(function () {
                    window.location.href = "home.php";
                });
            } else {
                Swal.fire({
                    title: "Error!",
                    text: response,
                    icon: "error"
                });
            }
        }
    };
    request.open("POST", "process/signupProcess.php", true);
    request.send(form);
}

function login() {
    var email = document.getElementById("loginemail").value.trim();
    var password = document.getElementById("loginpassword").value.trim();
    var rememberme = document.getElementById("rememberme").checked;


    if (email === "" || password === "") {
        Swal.fire({
            title: "Missing credentials",
            text: "Please enter both email and password.",
            icon: "warning"
        });
        return;
    }

    var form = new FormData();
    form.append("e", email);
    form.append("p", password);
    form.append("r", rememberme);

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState === 4 && request.status === 200) {
            var response = request.responseText;
            if (response === "success") {
                window.location = "home.php";
            } else {
                Swal.fire({
                    title: "Sorry!",
                    text: response,
                    icon: "error"
                });
            }
        }
    };
    request.open("POST", "process/signinProcess.php", true);
    request.send(form);
}

