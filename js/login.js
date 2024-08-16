// Clear Sessions
sessionStorage.clear();


// login func
function login(email, password) {

    // create form data
    let formData = new FormData();
    formData.append('email', email);
    formData.append('pass', password);
    // fetch php/Action
    fetch("services/Action.php?action=login", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(resp => {
            // SWL 2 Echo Status
            Swal.fire({
                icon: resp.status,
                title: resp.message,
                showConfirmButton: false,
                timer: 1500
            }).then(
                // if success
                () => {
                    // if status is success and role is owner to "admin.html" else customer to index.html
                    if (resp.status == "success" && resp['data'].role == "admin") {
                        window.location.href = "admin.php";
                    } else if (resp.status == "success" && resp['data'].role == "customer") {
                        window.location.href = "index.php";
                    }
                }
            )

        })
}

// id formLogin on submit
document.getElementById("formLogin")
    .addEventListener("submit", function (e) {
        // prevent default
        e.preventDefault();

        // get value
        let email = document.getElementById("email").value.trim();
        let password = document.getElementById("password").value.trim();

        // if email and password is empty
        if (email == "" || password == "") {
            // SWL 2 Echo Status
            Swal.fire({
                icon: "error",
                title: "Please fill all field",
                showConfirmButton: false,
                timer: 1500
            })
        } else {
            // call login func
            login(email, password)
        }
    })