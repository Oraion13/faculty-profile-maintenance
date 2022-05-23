const paperpres_form = document.getElementById("paperpres_form");
const paper_presented = document.getElementById("paper_presented");
const paper_presented_at = document.getElementById("paper_presented_at");



paperpres_form.addEventListener("submit", register);

function register(e) {
    e.preventDefault();
    //validate
    const register = {
        "paper_presented": paper_presented.value,
        "paper_presented_at": paper_presented_at.value,

    }

    // console.log(Login);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api/profile/public/type_5/papers_presented.php", true);

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