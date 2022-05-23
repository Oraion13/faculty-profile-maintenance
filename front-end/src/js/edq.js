const edq_form = document.getElementById("edq_form");
const degree = document.getElementById("degree");
const degree_from = document.getElementById("degree_from");
const degree_to = document.getElementById("degree_to");


edq_form.addEventListener("submit", register);

function register(e) {
    e.preventDefault();
    //validate
    const register = {
        "degree": degree.value,
        "degree_from": degree_from.value,
        "degree_to": degree_to.value
    }

    // console.log(Login);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api/profile/public/type_5/degree.php", true);

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