const progchaired_form = document.getElementById("progchaired_form");
const programme_chaired = document.getElementById("programme_chaired");
const programme_chaired_from = document.getElementById("programme_chaired_from");
const programme_chaired_to = document.getElementById("programme_chaired_to");


progchaired_form.addEventListener("submit", register);

function register(e) {
    e.preventDefault();
    //validate
    const register = {
        "programme_chaired": programme_chaired.value,
        "programme_chaired_from": programme_chaired_from.value,
        "programme_chaired_to": programme_chaired_to.value

    }

    // console.log(Login);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api/profile/public/type_5/programme_chaired.php", true);

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