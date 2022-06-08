const duty_form = document.getElementById("duty_form");
const incharge_duty_file_name = document.getElementById(
  "incharge_duty_file_name"
);
const incharge_duty_file_at = document.getElementById("incharge_duty_file_at");
const incharge_duty_file = document.getElementById("incharge_duty_file");
const all_duties = document.getElementById("all_duties");
const add_duty = document.getElementById("add_duty");
const alert1 = document.querySelector(".alert1");
const previous = document.getElementById("previous");

// -------------------------------------------- add / edit projects_storage  -------------------------------------------- //

// get eqd
const get_user = () => {
  return window.localStorage.getItem("user")
    ? JSON.parse(window.localStorage.getItem("user"))
    : [];
};

// Generate unique ID
function uuid() {
  return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function (c) {
    var r = (Math.random() * 16) | 0,
      v = c == "x" ? r : (r & 0x3) | 0x8;
    return v.toString(16);
  });
}

// !!!Alert!!!
function display_alert(text, action) {
  alert1.textContent = text;
  alert1.classList.add(`alert1-${action}`);
  // remove alert1
  setTimeout(function () {
    alert1.textContent = "";
    alert1.classList.remove(`alert1-${action}`);
  }, 4000);
}

// set backt to defaults
function set_back_to_default() {
  incharge_duty_file_name.value = "";
  incharge_duty_file_at.value = "";
  incharge_duty_file.value = "";
  add_duty.textContent = "Add";
}

// delete an item
function delete_item(e) {
  const element = e.currentTarget.parentElement;
  const id = element.dataset.id;
  console.log("id", id);

  if (window.confirm("Are you sure to remove this file?")) {
    const xhr = new XMLHttpRequest();

    xhr.open(
      "DELETE",
      `../../api/profile/private/type_6/incharge_duty_files.php?ID=${id}`,
      true
    );

    xhr.onreadystatechange = function () {
      if (xhr.readyState == XMLHttpRequest.DONE) {
        const got = JSON.parse(xhr.responseText);

        if (got.error) {
          display_alert(got.error, "danger");
        } else {
          all_duties.removeChild(element);
          display_alert("item removed", "danger");

          db_data();
        }
      }
    };

    xhr.send();
  }
}

// add item to the list
function add_item(e) {
  e.preventDefault();

  const user = get_user();

  if (!user) {
    window.alert("user not logged in");
    return;
  }

  const formData = new FormData();

  formData.append("name", incharge_duty_file_name.value);
  formData.append("incharge_duty_file", incharge_duty_file.files[0]);
  formData.append("incharge_duty_file_at", incharge_duty_file_at.value);

  const xhr = new XMLHttpRequest();

  xhr.open(
    "POST",
    `../../api/profile/private/type_6/incharge_duty_files.php?ID=${user.user_id}`,
    true
  );

  xhr.onreadystatechange = function () {
    if (xhr.readyState == XMLHttpRequest.DONE) {
      const got = JSON.parse(xhr.responseText);

      if (got.error) {
        display_alert(got.error, "danger");
      } else {
        window.alert("Incharge duty file updated successfully");
        set_back_to_default();
        db_data();
      }
    }
  };

  xhr.send(formData);
}

// --------------------------------------------- Initially  --------------------------------------------- //
// DB data
const db_data = () => {
  return new Promise(async (resolve, reject) => {
    all_duties.innerHTML = "";

    const user = get_user();
    if (!user) {
      window.alert("user not logged in");
      return;
    }

    const xhr = new XMLHttpRequest();

    xhr.open(
      "GET",
      `../../api/profile/private/type_6/incharge_duty_files.php?ID=${user.user_id}`,
      true
    );

    xhr.onreadystatechange = function () {
      if (xhr.readyState == XMLHttpRequest.DONE) {
        const got = JSON.parse(xhr.responseText);

        if (got.error) {
          // if can't get the data, thorw the error
          reject(display_alert(got.error, "danger"));
        } else {
          // change date
          got.forEach((item, index, array) => {
            item.incharge_duty_file_at = item.incharge_duty_file_at.substr(
              0,
              7
            );

            const element = document.createElement("article");
            let attr = document.createAttribute("data-id");
            attr.value = item.incharge_duty_file_id;

            element.setAttributeNode(attr);
            element.classList.add("one-duty_file");

            element.innerHTML = `
            <p>File name: <b>${item.incharge_duty_file_name}</b> duty at <b>${item.incharge_duty_file_at}</b></p>
            <iframe src="data:${item.incharge_duty_file_type};base64,${item.incharge_duty_file}" height="100%" width="100%"></iframe>
            <a href="data:${item.incharge_duty_file_type};base64,${item.incharge_duty_file}" download="${item.incharge_duty_file_name}.pdf"><i class="fas fa-download"></i></a>
            <button type="button" class="delete-btn btn btn-danger">
                  <i class="fas fa-trash"></i>
            </button>
            `;

            // add event listener button;
            const deleteBtn = element.querySelector(".delete-btn");
            deleteBtn.addEventListener("click", delete_item);

            all_duties.appendChild(element);

            if (index + 1 == array.length) {
              resolve();
            }
          });
        }
      }
    };
    xhr.send();
  });
};
// Initial setup
async function initialize() {
  db_data();
}

// add an projects_storage
add_duty.addEventListener("click", add_item);
// initialize
window.addEventListener("DOMContentLoaded", initialize);
// previous button
previous.addEventListener("click", () => {
  window.location.replace("./onduty.html");
});
