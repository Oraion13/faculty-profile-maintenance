const duty_form = document.getElementById("duty_form");
const invigilation_duty = document.getElementById("invigilation_duty");
const invigilation_duty_at = document.getElementById("invigilation_duty_at");
const all_duties = document.getElementById("all_duties");
const add_duty = document.getElementById("add_duty");
const alert1 = document.querySelector(".alert1");
const clear_all = document.getElementById("clear_all");
const previous = document.getElementById("previous");
const next = document.getElementById("next");

let edit_flag = false;
let edit_element;
let edit_tag;
let edit_from;
let edit_id = "";

// -------------------------------------------- add / edit invigilation_duty_storage  -------------------------------------------- //
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
  alert1.classList.add(`alert-${action}`);
  // remove alert1
  setTimeout(function () {
    alert1.textContent = "";
    alert1.classList.remove(`alert-${action}`);
  }, 4000);
}

// set backt to defaults
function set_back_to_default() {
  invigilation_duty.value = "";
  invigilation_duty_at.value = "";
  edit_flag = false;
  edit_id = "";
  add_duty.textContent = "Add";
}

// delete an item
function delete_item(e) {
  const element = e.currentTarget.parentElement.parentElement;
  const id = element.dataset.id;

  all_duties.removeChild(element);
  display_alert("item removed", "danger");

  set_back_to_default();
  // remove from local storage
  remove_from_local_storage(id);
}

// edit an item
async function edit_item(e) {
  const element = e.currentTarget.parentElement.parentElement;
  // set edit item
  edit_tag = element;
  edit_element = e.currentTarget.previousElementSibling;
  // set form value
  invigilation_duty.value = edit_element.childNodes[0].innerHTML;
  invigilation_duty_at.value = edit_element.childNodes[2].innerHTML;
  // faculty.value = edit_fac;
  edit_flag = true;
  edit_id = element.dataset.id;
  //
  add_duty.textContent = "Edit";
}

// clear items
function clear_items(e) {
  e.preventDefault();

  return new Promise((resolve, reject) => {
    // confirm clear
    if (!window.confirm("Are you sure to clear all?")) {
      return;
    }

    window.localStorage.removeItem("invigilation_duty_storage");
    const items = document.querySelectorAll(".one-invigilation_duty_storage");
    if (items.length > 0) {
      items.forEach(function (item) {
        all_duties.removeChild(item);
      });
    }
    display_alert("removed all Invigilation duties", "danger");
    resolve(set_back_to_default());
  });
}

// add item to the list
function add_item(e) {
  e.preventDefault();

  // *************************************** create *************************************** //
  if (invigilation_duty.value && invigilation_duty_at.value && !edit_flag) {
    const id = uuid();
    const element = document.createElement("article");
    let attr = document.createAttribute("data-id");
    attr.value = id;

    element.setAttributeNode(attr);
    element.classList.add("one-invigilation_duty_storage");

    element.innerHTML = `
      <p class="one-invigilation_duty"><span><b>${invigilation_duty.value}</b> at <b>${invigilation_duty_at.value}</b></span>
      &ensp;
                <button type="button" class="edit-btn btn btn-warning">
                  <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="delete-btn btn btn-danger">
                  <i class="fas fa-trash"></i>
                </button></p>
            `;
    // add event listeners to both buttons;
    const deleteBtn = element.querySelector(".delete-btn");
    deleteBtn.addEventListener("click", delete_item);
    const editBtn = element.querySelector(".edit-btn");
    editBtn.addEventListener("click", edit_item);

    // append child
    all_duties.appendChild(element);
    // display alert1
    display_alert("Added Successfully", "success");
    // set local storage
    add_to_local_storage(
      id,
      invigilation_duty.value,
      invigilation_duty_at.value
    );
    // // set back to default
    set_back_to_default();
    // ****************************************** Edit ****************************************** //
  } else if (
    invigilation_duty.value &&
    invigilation_duty_at.value &&
    edit_flag
  ) {
    edit_element.innerHTML = `<b>${invigilation_duty.value}</b> at <b>${invigilation_duty_at.value}</b>`;
    display_alert("values changed", "success");

    // edit  local storage
    edit_local_storage(
      edit_id,
      invigilation_duty.value,
      invigilation_duty_at.value
    );
    set_back_to_default();
  } else {
    display_alert("please fill all the fields", "danger");
  }
}

// -------------------------------------------- Local Storage -------------------------------------------- //
// get eqd
const get_user = () => {
  return window.localStorage.getItem("user")
    ? JSON.parse(window.localStorage.getItem("user"))
    : [];
};

// get eqd
const get_local_storage = () => {
  return window.localStorage.getItem("invigilation_duty_storage")
    ? JSON.parse(window.localStorage.getItem("invigilation_duty_storage"))
    : [];
};

// add item to local storage
const add_to_local_storage = (id, deg, deg_f) => {
  const item = {
    invigilation_duty_id: id,
    invigilation_duty: deg,
    invigilation_duty_at: deg_f,
  };
  let items = get_local_storage();
  items.push(item);
  window.localStorage.setItem(
    "invigilation_duty_storage",
    JSON.stringify(items)
  );
};

// remove from local storage
function remove_from_local_storage(id) {
  let items = get_local_storage();

  items = items.filter((item) => {
    if (item.invigilation_duty_id != id) {
      return item;
    }
  });

  window.localStorage.setItem(
    "invigilation_duty_storage",
    JSON.stringify(items)
  );
}

// edit an element in local storage
function edit_local_storage(id, deg, deg_f) {
  let items = get_local_storage();

  items = items.map(function (item) {
    if (item.invigilation_duty_id == id) {
      item.invigilation_duty = deg;
      item.invigilation_duty_at = deg_f;
    }
    return item;
  });
  window.localStorage.setItem(
    "invigilation_duty_storage",
    JSON.stringify(items)
  );
  return;
}

// --------------------------------------- Setup Items after refresh --------------------------------------- //

// get from local storage
function setup_items() {
  all_duties.innerHTML = "";
  let items = get_local_storage();
  // console.log("local storage", items);
  if (items.length > 0) {
    items.forEach(function (item) {
      create_list_item(
        item.invigilation_duty_id,
        item.invigilation_duty,
        item.invigilation_duty_at
      );
    });
  }

  set_back_to_default();
}

// append te child element to html
async function create_list_item(id, deg, deg_f, deg_t) {
  const element = document.createElement("article");
  let attr = document.createAttribute("data-id");
  attr.value = id;

  element.setAttributeNode(attr);
  element.classList.add("one-invigilation_duty_storage");

  element.innerHTML = `
      <p class="one-invigilation_duty"><span><b>${deg}</b> at <b>${deg_f}</b></span>
      &ensp;
                <button type="button" class="edit-btn btn btn-warning">
                  <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="delete-btn btn btn-danger">
                  <i class="fas fa-trash"></i>
                </button></p>
            `;
  // add event listeners to both buttons;
  const deleteBtn = element.querySelector(".delete-btn");
  deleteBtn.addEventListener("click", delete_item);
  const editBtn = element.querySelector(".edit-btn");
  editBtn.addEventListener("click", edit_item);

  // append child
  all_duties.appendChild(element);
}

// -------------------------------------------- Submit the form  -------------------------------------------- //
function submit_form(e) {
  e.preventDefault();

  let educational_qualification = [];

  const invigilation_duty_storage = get_local_storage();

  // // check if empty
  // if (invigilation_duty_storage.length <= 0) {
  //   display_alert("please add some present responsibilities", "danger");
  //   return;
  // }

  invigilation_duty_storage.forEach((item) => {
    item.invigilation_duty_id = isNaN(Number(item.invigilation_duty_id))
      ? 0
      : item.invigilation_duty_id;
    educational_qualification.push(item);
  });
  const user = get_user();

  if (!user) {
    window.alert("user not logged in");
    return;
  }
  const xhr = new XMLHttpRequest();

  xhr.open(
    "POST",
    `../../api/profile/private/type_4/invigilation_duties.php?ID=${user.user_id}`,
    true
  );

  xhr.onreadystatechange = function () {
    if (xhr.readyState == XMLHttpRequest.DONE) {
      const got = JSON.parse(xhr.responseText);

      if (got.error) {
        display_alert(got.error, "danger");
      } else {
        window.alert("Invigilation duties updated successfully");
        got.forEach((item, index, array) => {
          item.invigilation_duty_at = item.invigilation_duty_at.substr(0, 7);

          if (index + 1 == array.length) {
            // assign the data
            window.localStorage.setItem(
              "invigilation_duty_storage",
              JSON.stringify(got)
            );
            setup_items();
          }
        });
      }
    }
  };

  xhr.send(JSON.stringify(educational_qualification));
}

// --------------------------------------------- Initially  --------------------------------------------- //
// DB data
const db_data = () => {
  return new Promise(async (resolve, reject) => {
    const user = get_user();
    if (!user) {
      window.alert("user not logged in");
      return;
    }

    const xhr = new XMLHttpRequest();

    xhr.open(
      "GET",
      `../../api/profile/private/type_4/invigilation_duties.php?ID=${user.user_id}`,
      true
    );

    xhr.onreadystatechange = function () {
      if (xhr.readyState == XMLHttpRequest.DONE) {
        const got = JSON.parse(xhr.responseText);

        if (got.error) {
          // if can't get the data, thorw the error
          display_alert(got.error, "danger");
        } else {
          // change date
          got.forEach((item, index, array) => {
            item.invigilation_duty_at = item.invigilation_duty_at.substr(0, 7);

            if (index + 1 == array.length) {
              // assign the data
              window.localStorage.setItem(
                "invigilation_duty_storage",
                JSON.stringify(got)
              );
              setup_items();
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
  if (get_local_storage().length !== 0) {
    setup_items();
  } else {
    db_data();
  }
}

// add an invigilation_duty_storage
add_duty.addEventListener("click", add_item);
// when form submitted
duty_form.addEventListener("submit", submit_form);
// initialize
window.addEventListener("DOMContentLoaded", initialize);
// clear all
clear_all.addEventListener("click", clear_items);
// previous button
previous.addEventListener("click", () => {
  window.localStorage.removeItem("invigilation_duty_storage");
  window.location.replace("./extension.html");
});
// next button
next.addEventListener("click", () => {
  window.localStorage.removeItem("invigilation_duty_storage");
  window.location.replace("./onduty.html");
});
