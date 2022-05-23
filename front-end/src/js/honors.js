const honors_form = document.getElementById("honors_form");
const honor = document.getElementById("honor");
const honor_at = document.getElementById("honor_at");



honors_form.addEventListener("submit", register);

function register(e) {
    e.preventDefault();
    //validate
    const register = {
        "honor": honor.value,
        "honor_at": honor_at.value,


    }

    // console.log(Login);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../../api/profile/public/type_4/honors.php", true);

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