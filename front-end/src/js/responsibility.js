const submit_prev = document.getElementById("submit_prev");
const additional_responsibility_prev = document.getElementById("additional_responsibility_prev");
const additional_responsibility_prev_from = document.getElementById("additional_responsibility_prev_from");
const additional_responsibility_prev_to = document.getElementById("additional_responsibility_prev_to");
const previouses = document.getElementById("previouses");
const add_prev = document.getElementById("add_prev");
const alert2 = document.querySelector(".alert2");
const clear_all_prev = document.getElementById("clear_all_prev");
const previous = document.getElementById("previous");
const next = document.getElementById("next");

let edit_flag = false;
let edit_element;
let edit_tag;
let edit_from;
let edit_to;
let edit_id = "";

// -------------------------------------------- add / edit previous_responsibilities  -------------------------------------------- //
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
  alert2.textContent = text;
  alert2.classList.add(`alert2-${action}`);
  // remove alert2
  setTimeout(function () {
    alert2.textContent = "";
    alert2.classList.remove(`alert2-${action}`);
  }, 4000);
}

// set backt to defaults
function set_back_to_default() {
  additional_responsibility_prev.value = "";
  additional_responsibility_prev_from.value = "";
  additional_responsibility_prev_to.value = "";
  edit_flag = false;
  edit_id = "";
  add_prev.textContent = "Add";
}

// delete an item
function delete_item(e) {
  const element = e.currentTarget.parentElement.parentElement;
  const id = element.dataset.id;

  previouses.removeChild(element);
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
  additional_responsibility_prev.value = edit_element.childNodes[0].innerHTML;
  additional_responsibility_prev_from.value = edit_element.childNodes[2].innerHTML;
  additional_responsibility_prev_to.value = edit_element.childNodes[4].innerHTML;
  // faculty.value = edit_fac;
  edit_flag = true;
  edit_id = element.dataset.id;
  //
  add_prev.textContent = "Edit";
}

// clear items
function clear_items(e) {
  e.preventDefault();

  return new Promise((resolve, reject) => {
    // confirm clear
    if (!window.confirm("Are you sure to clear all?")) {
      return;
    }

    window.localStorage.removeItem("previous_responsibilities");
    const items = document.querySelectorAll(".one-previous_responsibilities");
    if (items.length > 0) {
      items.forEach(function (item) {
        previouses.removeChild(item);
      });
    }
    display_alert("removed all previous additional responsibilities", "danger");
    resolve(set_back_to_default());
  });
}

// add item to the list
function add_previous(e) {
  e.preventDefault();

  // *************************************** create *************************************** //
  if (additional_responsibility_prev.value && additional_responsibility_prev_from.value && additional_responsibility_prev_to.value && !edit_flag) {
    const id = uuid();
    const element = document.createElement("article");
    let attr = document.createAttribute("data-id");
    attr.value = id;

    element.setAttributeNode(attr);
    element.classList.add("one-previous_responsibilities");

    element.innerHTML = `
      <p class="one-additional_responsibility_prev"><span><b>${additional_responsibility_prev.value}</b> from <b>${additional_responsibility_prev_from.value}</b> to <b>${additional_responsibility_prev_to.value}</b></span>
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
    previouses.appendChild(element);
    // display alert2
    display_alert("Added Successfully", "success");
    // set local storage
    add_to_local_storage(id, additional_responsibility_prev.value, additional_responsibility_prev_from.value, additional_responsibility_prev_to.value);
    // // set back to default
    set_back_to_default();
    // ****************************************** Edit ****************************************** //
  } else if (
    additional_responsibility_prev.value &&
    additional_responsibility_prev_from.value &&
    additional_responsibility_prev_to.value &&
    edit_flag
  ) {
    edit_element.innerHTML = `<b>${additional_responsibility_prev.value}</b> from <b>${additional_responsibility_prev_from.value}</b> to <b>${additional_responsibility_prev_to.value}</b>`;
    display_alert("values changed", "success");

    // edit  local storage
    edit_local_storage(
      edit_id,
      additional_responsibility_prev.value,
      additional_responsibility_prev_from.value,
      additional_responsibility_prev_to.value
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
  return window.localStorage.getItem("previous_responsibilities")
    ? JSON.parse(window.localStorage.getItem("previous_responsibilities"))
    : [];
};

// add item to local storage
const add_to_local_storage = (id, deg, deg_f, deg_t) => {
  const item = {
    additional_responsibility_prev_id: id,
    additional_responsibility_prev: deg,
    additional_responsibility_prev_from: deg_f,
    additional_responsibility_prev_to: deg_t,
  };
  let items = get_local_storage();
  items.push(item);
  window.localStorage.setItem("previous_responsibilities", JSON.stringify(items));
};

// remove from local storage
function remove_from_local_storage(id) {
  let items = get_local_storage();

  items = items.filter((item) => {
    if (item.additional_responsibility_prev_id != id) {
      return item;
    }
  });

  window.localStorage.setItem("previous_responsibilities", JSON.stringify(items));
}

// edit an element in local storage
function edit_local_storage(id, deg, deg_f, deg_t) {
  let items = get_local_storage();

  items = items.map(function (item) {
    if (item.additional_responsibility_prev_id == id) {
      item.additional_responsibility_prev = deg;
      item.additional_responsibility_prev_from = deg_f;
      item.additional_responsibility_prev_to = deg_t;
    }
    return item;
  });
  window.localStorage.setItem("previous_responsibilities", JSON.stringify(items));
  return;
}

// --------------------------------------- Setup Items after refresh --------------------------------------- //

// get from local storage
function setup_items() {
  previouses.innerHTML = "";
  let items = get_local_storage();
  // console.log("local storage", items);
  if (items.length > 0) {
    items.forEach(function (item) {
      create_list_item(
        item.additional_responsibility_prev_id,
        item.additional_responsibility_prev,
        item.additional_responsibility_prev_from,
        item.additional_responsibility_prev_to
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
  element.classList.add("one-previous_responsibilities");

  element.innerHTML = `
      <p class="one-additional_responsibility_prev"><span><b>${deg}</b> from <b>${deg_f}</b> to <b>${deg_t}</b></span>
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
  previouses.appendChild(element);
}

// -------------------------------------------- Submit the form  -------------------------------------------- //
function submit_form(e) {
  e.preventDefault();

  let educational_qualification = [];

  const previous_responsibilities = get_local_storage();

  // // check if empty
  // if (previous_responsibilities.length <= 0) {
  //   display_alert("please add some present responsibilities", "danger");
  //   return;
  // }

  previous_responsibilities.forEach((item) => {
    item.additional_responsibility_prev_id = isNaN(Number(item.additional_responsibility_prev_id)) ? 0 : item.additional_responsibility_prev_id;
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
    `../../api/profile/public/type_5/additional_responsibilities_prev.php?ID=${user.user_id}`,
    true
  );

  xhr.onreadystatechange = function () {
    if (xhr.readyState == XMLHttpRequest.DONE) {
      const got = JSON.parse(xhr.responseText);

      if (got.error) {
        display_alert(got.error, "danger");
      } else {
        window.alert("Previous additional responsibilities updated successfully");
        got.forEach((item, index, array) => {
          item.additional_responsibility_prev_from = item.additional_responsibility_prev_from.substr(0, 7);
          item.additional_responsibility_prev_to = item.additional_responsibility_prev_to.substr(0, 7);

          if (index + 1 == array.length) {
            // assign the data
            window.localStorage.setItem("previous_responsibilities", JSON.stringify(got));
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
      `../../api/profile/public/type_5/additional_responsibilities_prev.php?ID=${user.user_id}`,
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
            item.additional_responsibility_prev_from = item.additional_responsibility_prev_from.substr(0, 7);
            item.additional_responsibility_prev_to = item.additional_responsibility_prev_to.substr(0, 7);

            if (index + 1 == array.length) {
              // assign the data
              window.localStorage.setItem("previous_responsibilities", JSON.stringify(got));
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

// add an previous_responsibilities
add_prev.addEventListener("click", add_previous);
// when form submitted
submit_prev.addEventListener("click", submit_form);
// initialize
window.addEventListener("DOMContentLoaded", initialize);
// clear all
clear_all_prev.addEventListener("click", clear_items);
// previous button
previous.addEventListener("click", () => {
  window.localStorage.removeItem("previous_responsibilities");
  window.localStorage.removeItem("present_responsibilities");
  window.location.replace("./position.html");
});
// next button
next.addEventListener("click", () => {
  window.localStorage.removeItem("previous_responsibilities");
  window.localStorage.removeItem("present_responsibilities");
  window.location.replace("./paperpub.html");
});
