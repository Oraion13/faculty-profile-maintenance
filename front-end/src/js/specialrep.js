const specialrep_form = document.getElementById("specialrep_form");
const special_representation = document.getElementById("special_representation");
const special_representation_from = document.getElementById(
  "special_representation_from"
);
const special_representation_to = document.getElementById("special_representation_to");
const special_respresentations = document.getElementById("special_respresentations");
const add_spcl = document.getElementById("add_spcl");
const alert1 = document.querySelector(".alert1");
const clear_all = document.getElementById("clear_all");
const previous = document.getElementById("previous");
const next = document.getElementById("next");

let edit_flag = false;
let edit_element;
let edit_tag;
let edit_from;
let edit_to;
let edit_id = "";

// -------------------------------------------- add / edit special_representation_storage  -------------------------------------------- //
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
  special_representation.value = "";
  special_representation_from.value = "";
  special_representation_to.value = "";
  edit_flag = false;
  edit_id = "";
  add_spcl.textContent = "Add";
}

// delete an item
function delete_item(e) {
  const element = e.currentTarget.parentElement.parentElement;
  const id = element.dataset.id;

  special_respresentations.removeChild(element);
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
  special_representation.value = edit_element.childNodes[0].innerHTML;
  special_representation_from.value = edit_element.childNodes[2].innerHTML;
  special_representation_to.value = edit_element.childNodes[4].innerHTML;
  // faculty.value = edit_fac;
  edit_flag = true;
  edit_id = element.dataset.id;
  //
  add_spcl.textContent = "Edit";
}

// clear items
function clear_items(e) {
  e.preventDefault();

  return new Promise((resolve, reject) => {
    // confirm clear
    if (!window.confirm("Are you sure to clear all?")) {
      return;
    }

    window.localStorage.removeItem("special_representation_storage");
    const items = document.querySelectorAll(".one-special_representation_storage");
    if (items.length > 0) {
      items.forEach(function (item) {
        special_respresentations.removeChild(item);
      });
    }
    display_alert("removed all special representations", "danger");
    resolve(set_back_to_default());
  });
}

// add item to the list
function add_item(e) {
  e.preventDefault();

  // *************************************** create *************************************** //
  if (
    special_representation.value &&
    special_representation_from.value &&
    special_representation_to.value &&
    !edit_flag
  ) {
    const id = uuid();
    const element = document.createElement("article");
    let attr = document.createAttribute("data-id");
    attr.value = id;

    element.setAttributeNode(attr);
    element.classList.add("one-special_representation_storage");

    element.innerHTML = `
      <p class="one-special_representation"><span><b>${special_representation.value}</b> from <b>${special_representation_from.value}</b> to <b>${special_representation_to.value}</b></span>
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
    special_respresentations.appendChild(element);
    // display alert1
    display_alert("Added Successfully", "success");
    // set local storage
    add_to_local_storage(
      id,
      special_representation.value,
      special_representation_from.value,
      special_representation_to.value
    );
    // // set back to default
    set_back_to_default();
    // ****************************************** Edit ****************************************** //
  } else if (
    special_representation.value &&
    special_representation_from.value &&
    special_representation_to.value &&
    edit_flag
  ) {
    edit_element.innerHTML = `<b>${special_representation.value}</b> from <b>${special_representation_from.value}</b> to <b>${special_representation_to.value}</b>`;
    display_alert("values changed", "success");

    // edit  local storage
    edit_local_storage(
      edit_id,
      special_representation.value,
      special_representation_from.value,
      special_representation_to.value
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
  return window.localStorage.getItem("special_representation_storage")
    ? JSON.parse(window.localStorage.getItem("special_representation_storage"))
    : [];
};

// add item to local storage
const add_to_local_storage = (id, deg, deg_f, deg_t) => {
  const item = {
    special_representation_id: id,
    special_representation: deg,
    special_representation_from: deg_f,
    special_representation_to: deg_t,
  };
  let items = get_local_storage();
  items.push(item);
  window.localStorage.setItem(
    "special_representation_storage",
    JSON.stringify(items)
  );
};

// remove from local storage
function remove_from_local_storage(id) {
  let items = get_local_storage();

  items = items.filter((item) => {
    if (item.special_representation_id != id) {
      return item;
    }
  });

  window.localStorage.setItem(
    "special_representation_storage",
    JSON.stringify(items)
  );
}

// edit an element in local storage
function edit_local_storage(id, deg, deg_f, deg_t) {
  let items = get_local_storage();

  items = items.map(function (item) {
    if (item.special_representation_id == id) {
      item.special_representation = deg;
      item.special_representation_from = deg_f;
      item.special_representation_to = deg_t;
    }
    return item;
  });
  window.localStorage.setItem(
    "special_representation_storage",
    JSON.stringify(items)
  );
  return;
}

// --------------------------------------- Setup Items after refresh --------------------------------------- //

// get from local storage
function setup_items() {
  special_respresentations.innerHTML = "";
  let items = get_local_storage();
  // console.log("local storage", items);
  if (items.length > 0) {
    items.forEach(function (item) {
      create_list_item(
        item.special_representation_id,
        item.special_representation,
        item.special_representation_from,
        item.special_representation_to
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
  element.classList.add("one-special_representation_storage");

  element.innerHTML = `
      <p class="one-special_representation"><span><b>${deg}</b> from <b>${deg_f}</b> to <b>${deg_t}</b></span>
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
  special_respresentations.appendChild(element);
}

// -------------------------------------------- Submit the form  -------------------------------------------- //
function submit_form(e) {
  e.preventDefault();

  let educational_qualification = [];

  const special_representation_storage = get_local_storage();

  // // check if empty
  // if (special_representation_storage.length <= 0) {
  //   display_alert("please add some present responsibilities", "danger");
  //   return;
  // }

  special_representation_storage.forEach((item) => {
    item.special_representation_id = isNaN(Number(item.special_representation_id))
      ? 0
      : item.special_representation_id;
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
    `../../api/profile/public/type_5/special_representations.php?ID=${user.user_id}`,
    true
  );

  xhr.onreadystatechange = function () {
    if (xhr.readyState == XMLHttpRequest.DONE) {
      const got = JSON.parse(xhr.responseText);

      if (got.error) {
        display_alert(got.error, "danger");
      } else {
        window.alert("Special representations updated successfully");
        got.forEach((item, index, array) => {
          item.special_representation_from = item.special_representation_from.substr(
            0,
            7
          );
          item.special_representation_to = item.special_representation_to.substr(0, 7);

          if (index + 1 == array.length) {
            // assign the data
            window.localStorage.setItem(
              "special_representation_storage",
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
      `../../api/profile/public/type_5/special_representations.php?ID=${user.user_id}`,
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
            item.special_representation_from = item.special_representation_from.substr(
              0,
              7
            );
            item.special_representation_to = item.special_representation_to.substr(
              0,
              7
            );

            if (index + 1 == array.length) {
              // assign the data
              window.localStorage.setItem(
                "special_representation_storage",
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

// add an special_representation_storage
add_spcl.addEventListener("click", add_item);
// when form submitted
specialrep_form.addEventListener("submit", submit_form);
// initialize
window.addEventListener("DOMContentLoaded", initialize);
// clear all
clear_all.addEventListener("click", clear_items);
// previous button
previous.addEventListener("click", () => {
  window.localStorage.removeItem("special_representation_storage");
  window.location.replace("./progatten.html");
});
// next button
next.addEventListener("click", () => {
  window.localStorage.removeItem("special_representation_storage");
  window.location.replace("./honors.html");
});
