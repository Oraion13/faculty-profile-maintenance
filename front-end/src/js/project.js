const project_form = document.getElementById("project_form");
const project = document.getElementById("project");
const project_from = document.getElementById("project_from");
const project_to = document.getElementById("project_to");
const project_cost = document.getElementById("project_cost");



project_form.addEventListener("submit", register);

function register(e) {
    e.preventDefault();
    //validate
    const register = {
        "project": project.value,
        "project_from": project_from.value,
        "project_to": project_to.value,
        "project_cost": project_cost.value


    }

    // console.log(Login);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api/profile/public/type_6/sponsored_project_completed.php", true);

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