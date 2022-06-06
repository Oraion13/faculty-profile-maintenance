const admin_form = document.getElementById("admin_form");
const username = document.getElementById("username");
const password = document.getElementById("password");

// login as admin
const login_admin = () => {
  const xhr = new XMLHttpRequest();

  xhr.open("POST", "../../api/login_register/login.php", true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState == XMLHttpRequest.DONE) {
      const got = JSON.parse(xhr.responseText);
      if (got.error) {
        window.alert(got.error);
      } else {
        if (!got.is_admin) {
          window.alert("Not an admin");
          return;
        } else {
          window.localStorage.setItem("admin", JSON.stringify(got));
          window.location.replace("./admin_report.html");
        }
      }
    }
  };

  xhr.send();
};

// logout all user if any
function logout_all(e) {
  e.preventDefault();

  const xhr = new XMLHttpRequest();

  xhr.open("GET", "../../api/login_register/logout.php", true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState == XMLHttpRequest.DONE) {
      login_admin();
    }
  };

  xhr.send();
}

admin_form.addEventListener("submit", logout_all);
