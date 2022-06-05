const specialrep_form = document.getElementById("specialrep_form");
const special_reprasentation = document.getElementById("special_reprasentation");
const special_reprasentation_from = document.getElementById("special_reprasentation_from");
const special_reprasentation_to = document.getElementById("special_reprasentation_to");


specialrep_form.addEventListener("submit", register);

function register(e) {
    e.preventDefault();
    //validate
    const register = {
        "special_reprasentation": special_reprasentation.value,
        "special_reprasentation_from": special_reprasentation_from.value,
        "special_reprasentation_to": special_reprasentation_to.value
    }

    // console.log(Login);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api/profile/public/type_5/special_representation.php", true);

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