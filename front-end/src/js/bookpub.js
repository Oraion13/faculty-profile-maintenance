const bookpub_form = document.getElementById("bookpub_form");
const title = document.getElementById("title");
const description = document.getElementById("description");
const published_at = document.getElementById("published_at");

bookpub_form.addEventListener("submit", login);

function login(e) {
    e.preventDefault();
    //validate
    const register = {
        title: title.value,
        description: description.value,
        published_at: published_at.value,

    };

    // console.log(Login);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api/profile/public/type_5/book_published.php", true);

    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            const got = JSON.parse(xhr.responseText);

            if (got.error) {
                window.alert(got.error);
            } else {
                console.log("user", got);
                window.localStorage.setItem("user", JSON.stringify(got));

                window.location.replace("#");
            }
        }
    };
    xhr.send(JSON.stringify(register));
}