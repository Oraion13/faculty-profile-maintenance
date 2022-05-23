const experience_form = document.getElementById("experience_form");
const exp_abroad = document.getElementById("exp_abroad");
const exp_abroad_from = document.getElementById("exp_abroad_from");
const exp_abroad_to = document.getElementById("exp_abroad_to");
const purpose_of_visit = document.getElementById("purpose_of_visit");



experience_form.addEventListener("submit", register);

function register(e) {
    e.preventDefault();
    //validate
    const register = {
        "exp_abroad": exp_abroad.value,
        "exp_abroad_from": exp_abroad_from.value,
        "exp_abroad_to": exp_abroad_to.value,
        "purpose_of_visit": purpose_of_visit.value


    }

    // console.log(Login);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api/profile/public/type_6/exp_abroad.php", true);

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