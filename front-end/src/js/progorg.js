const progorg_form = document.getElementById("progorg_form");
const programme_organized = document.getElementById("programme_organized");
const programme_organized_from = document.getElementById("programme_organized_from");
const programme_organized_to = document.getElementById("programme_organized_to");


progorg_form.addEventListener("submit", register);

function register(e) {
    e.preventDefault();
    //validate
    const register = {
        "programme_organized": programme_organized.value,
        "programme_organized_from": programme_organized_from.value,
        "programme_organized_to": programme_organized_to.value

    }

    // console.log(Login);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api/profile/public/type_5/programme_organized.php", true);

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