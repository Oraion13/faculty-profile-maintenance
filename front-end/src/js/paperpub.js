const paperpub_form = document.getElementById("paperpub_form");
const paper_published = document.getElementById("paper_published");
const paper_published_at = document.getElementById("paper_published_at");
const is_international = document.getElementById("is_international");


paperpub_form.addEventListener("submit", register);

function register(e) {
    e.preventDefault();
    //validate
    const register = {
        "paper_published": paper_published.value,
        "paper_published_at": paper_published_at.value,
        "is_international": is_international.value

    }

    // console.log(Login);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api/profile/public/type_5/papers_published.php", true);

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