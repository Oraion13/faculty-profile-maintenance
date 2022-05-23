const position_form = document.getElementById("position_form");
const honorific = document.getElementById("honorific");
const full_name = document.getElementById("full_name");
const username = document.getElementById("username");
const email = document.getElementById("email");
const password = document.getElementById("password");

position_form.addEventListener("submit", register);

function register(e) {
    e.preventDefault();
    //validate
    const register = {
        "honorific": honorific.value,
        "full_name": full_name.value,
        "username": username.value,
        "email": email.value,
        "password": password.value
    }

    // console.log(Login);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api/login_register/register.php", true);

    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            const got = JSON.parse(xhr.responseText);

            if (got.error) {
                window.alert(got.error);
            } else {
                console.log(got);
                window.location.replace("./login.html");
            }

        }
    }
    xhr.send(JSON.stringify(register));
}