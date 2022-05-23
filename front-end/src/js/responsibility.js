const responsibility_form = document.getElementById("responsibility_form");
const additional_responsibility_present = document.getElementById("additional_responsibility_present");
const additional_responsibility_present_from = document.getElementById("additional_responsibility_present_from");

responsibility_form.addEventListener("submit", register);

function register(e) {
    e.preventDefault();
    //validate
    const register = {
        "additional_responsibility_present": additional_responsibility_present.value,
        "additional_responsibility_present_from": additional_responsibility_present_from.value,
    }

    // console.log(Login);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api/profile/public/type_4/additional_responsibilities_present.php", true);
    // console.log(Login);

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

// const responsibility_form = document.getElementById("responsibility_form");
const additional_responsibility_prev = document.getElementById("additional_responsibility_prev");
const additional_responsibility_prev_from = document.getElementById("additional_responsibility_prev_from");
const additional_responsibility_prev_to = document.getElementById("additional_responsibility_prev_to");


responsibility_form.addEventListener("submit2", register2);

function register2(e) {
    e.preventDefault();
    //validate
    const register2 = {
        "additional_responsibility_prev": additional_responsibility_prev.value,
        "additional_responsibility_prev_from": additional_responsibility_prev_from.value,
        "additional_responsibility_prev_to": additional_responsibility_prev_to.value
    }

    // console.log(Login);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api/profile/public/type_5/additional_responsibilities_prev.php", true);

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