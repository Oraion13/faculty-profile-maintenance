const extension_form = document.getElementById("extension_form");
const extension_outreach = document.getElementById("extension_outreach");
const extension_outreach_from = document.getElementById("extension_outreach_from");
const extension_outreach_to = document.getElementById("extension_outreach_to");
const number_of_participants = document.getElementById("number_of_participants");


extension_form.addEventListener("submit", register);

function register(e) {
    e.preventDefault();
    //validate
    const register = {
        "extension_outreach": extension_outreach.value,
        "extension_outreach_from": extension_outreach_from.value,
        "extension_outreach_to": extension_outreach_to.value,
        "number_of_participants": number_of_participants.value

    }

    // console.log(Login);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api/profile/public/type_6/extension_outreach.php", true);

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