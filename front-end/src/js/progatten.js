const progatten_form = document.getElementById("progatten_form");
const programme_attended = document.getElementById("programme_attended");
const programme_attended_from = document.getElementById("programme_attended_from");
const programme_attended_to = document.getElementById("programme_attended_to");


progatten_form.addEventListener("submit", register);

function register(e) {
    e.preventDefault();
    //validate
    const register = {
        "programme_attended": programme_attended.value,
        "programme_attended_from": programme_attended_from.value,
        "programme_attended_to": programme_attended_to.value

    }

    // console.log(Login);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api/profile/public/type_5/programme_attended.php", true);

    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            const got = JSON.parse(xhr.responseText);

            if (got.error) {
                window.alert(got.error);
            } else {
                console.log(got);
                window.location.replace("#");
            }

        }
    }
    xhr.send(JSON.stringify(register));
}