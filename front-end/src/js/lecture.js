const lecture_form = document.getElementById("lecture_form");
const invited_lecture = document.getElementById("invited_lecture");
const invited_lecture_at = document.getElementById("invited_lecture_at");



lecture_form.addEventListener("submit", register);

function register(e) {
    e.preventDefault();
    //validate
    const register = {
        "invited_lecture": invited_lecture.value,
        "invited_lecture_at": invited_lecture_at.value

    }

    // console.log(Login);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api/profile/public/type_4/invited_lectures.php", true);

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