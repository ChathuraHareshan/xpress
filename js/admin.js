var av;
function adminVerification() {

    var email = document.getElementById("email");

    var form = new FormData();
    form.append("e", email.value);

    var request = new XMLHttpRequest();

    request.onreadystatechange = function () {
        if (request.status == 200 & request.readyState == 4) {
            var response = request.responseText;
            if (response == "Success") {
                alert("Please take a look at your email to find the VERIFICATION CODE.");
                var adminVerificationModal = document.getElementById("verificationModel");
                av = new bootstrap.Modal(adminVerificationModal);
                av.show();
            } else {
                alert(response);
            }

        }
    }

    request.open("POST", "process/adminVerificationProcess.php", true);
    request.send(form);
}

function verify() {

    var code = document.getElementById("vcode");

    var form = new FormData();
    form.append("c", code.value);

    var request = new XMLHttpRequest();

    request.onreadystatechange = function () {
        if (request.status == 200 & request.readyState == 4) {
            var response = request.responseText;
            if (response == "success") {
                av.hide();
                window.location = "adminPanel.php";
            } else {
                alert(response);
            }

        }
    }

    request.open("POST", "process/verificationProcess.php", true);
    request.send(form);

}


function saveProduct() {
    var title = document.getElementById("title").value.trim();
    var qty = document.getElementById("qty").value.trim();
    var description = document.getElementById("description").value.trim();
    var color = document.getElementById("productColor").value;
    var storage = document.getElementById("productStorage").value;
    var price = document.getElementById("price").value.trim();
    
    if (title === "" || qty === "" || description === "" || color === "" || storage === "" || price === "") {
        Swal.fire({
            title: "Missing fields",
            text: "Please fill in all required fields.",
            icon: "warning"
        });
        return;
    }
    
    var imageInputs = document.querySelectorAll('input[type="file"]');
    var hasImages = false;
    
    for (var i = 0; i < imageInputs.length; i++) {
        if (imageInputs[i].files.length > 0) {
            hasImages = true;
            break;
        }
    }
    
    if (!hasImages) {
        Swal.fire({
            title: "No images",
            text: "Please select at least one product image.",
            icon: "warning"
        });
        return;
    }
    
    var form = new FormData();
    form.append("title", title);
    form.append("qty", qty);
    form.append("description", description);
    form.append("color", color);
    form.append("storage", storage);
    form.append("price", price);
    
    for (var i = 0; i < imageInputs.length; i++) {
        if (imageInputs[i].files.length > 0) {
            form.append("images[]", imageInputs[i].files[0]);
        }
    }
    
    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState === 4) {
            if (request.status === 200) {
                var response = request.responseText.trim();
                if (response === "success") {
                    Swal.fire({
                        title: "Success!",
                        text: "Product added successfully!",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function() {
                        window.location.reload(); 
                    });
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: response,
                        icon: "error"
                    });
                }
            } else {
                Swal.fire({
                    title: "Error!",
                    text: "Network error occurred.",
                    icon: "error"
                });
            }
        }
    };
    request.open("POST", "../process/addProductProcess.php", true);
    request.send(form);
}