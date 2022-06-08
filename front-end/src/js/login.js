const login_form = document.getElementById("login_form");
const username = document.getElementById("username");
const password = document.getElementById("password");

// login submit
function login(e) {
    e.preventDefault();
    //validate
    const register = {
        username: username.value,
        password: password.value,
    };

    // console.log(Login);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api/login_register/login.php", true);

    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            const got = JSON.parse(xhr.responseText);

            if (got.error) {
                window.alert(got.error);
            } else {
                console.log("user", got);
                window.localStorage.setItem("user", JSON.stringify(got));

                window.location.replace("./edit2.html");
            }
        }
    };
    xhr.send(JSON.stringify(register));
}

function already_logged_in() {
    if (JSON.parse(window.localStorage.getItem("user")).user_id) {
        window.location.replace("./edit2.html");
    }
}

// if already logged in
window.addEventListener("DOMContentLoaded", already_logged_in);
// submit
login_form.addEventListener("submit", login);