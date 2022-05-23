const forgetpassword_form = document.getElementById("forgetpassword_form");
const email = document.getElementById("email");
const password = document.getElementById("password");

forgetpassword_form.addEventListener("submit", login);

function login(e) {
    e.preventDefault();
    //validate
    const register = {
        "email": email.value
    }

    // console.log(Login);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api/login_register/forget_password.php", true);

    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            const got = JSON.parse(xhr.responseText);

            if (got.error) {
                window.alert(got.error);
            } else {
                console.log(got);
                window.location.replace("./edit2.html");
            }

        }
    }
    xhr.send(JSON.stringify(register));
}