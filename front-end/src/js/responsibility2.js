const submit_present = document.getElementById("submit_present");
const additional_responsibility_present = document.getElementById("additional_responsibility_present");
const additional_responsibility_present_from = document.getElementById("additional_responsibility_present_from");
const presents = document.getElementById("presents");
const add_present = document.getElementById("add_present");
const alert1 = document.querySelector(".alert1");
const clear_all_present = document.getElementById("clear_all_present");

let edit_flag1 = false;
let edit_element1;
let edit_tag1;
let edit_from1;
let edit_id1 = "";

// -------------------------------------------- add / edit present_responsibilities  -------------------------------------------- //
// Generate unique ID
function uuid1() {
  return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function (c) {
    var r = (Math.random() * 16) | 0,
      v = c == "x" ? r : (r & 0x3) | 0x8;
    return v.toString(16);
  });
}

// !!!Alert!!!
function display_alert1(text, action) {
  alert1.textContent = text;
  alert1.classList.add(`alert1-${action}`);
  // remove alert1
  setTimeout(function () {
    alert1.textContent = "";
    alert1.classList.remove(`alert1-${action}`);
  }, 4000);
}

// set backt to defaults
function set_back_to_default1() {
  additional_responsibility_present.value = "";
  additional_responsibility_present_from.value = "";
  edit_flag1 = false;
  edit_id1 = "";
  add_present.textContent = "Add";
}

// delete an item
function delete_item1(e) {
  const element = e.currentTarget.parentElement.parentElement;
  const id = element.dataset.id;

  presents.removeChild(element);
  display_alert1("item removed", "danger");

  set_back_to_default1();
  // remove from local storage
  remove_from_local_storage1(id);
}

// edit an item
async function edit_item1(e) {
  const element = e.currentTarget.parentElement.parentElement;
  // set edit item
  edit_tag1 = element;
  edit_element1 = e.currentTarget.previousElementSibling;
  // set form value
  additional_responsibility_present.value = edit_element1.childNodes[0].innerHTML;
  additional_responsibility_present_from.value = edit_element1.childNodes[2].innerHTML;
  // faculty.value = edit_fac;
  edit_flag1 = true;
  edit_id1 = element.dataset.id;
  //
  add_present.textContent = "Edit";
}

// clear items
function clear_items1(e) {
  e.preventDefault();

  return new Promise((resolve, reject) => {
    // confirm clear
    if (!window.confirm("Are you sure to clear all?")) {
      return;
    }

    window.localStorage.removeItem("present_responsibilities");
    const items = document.querySelectorAll(".one-present_responsibilities");
    if (items.length > 0) {
      items.forEach(function (item) {
        presents.removeChild(item);
      });
    }
    display_alert1("removed all previous additional responsibilities", "danger");
    resolve(set_back_to_default1());
  });
}

// add item to the list
function add_previous1(e) {
  e.preventDefault();

  // *************************************** create *************************************** //
  if (additional_responsibility_present.value && additional_responsibility_present_from.value && !edit_flag1) {
    const id = uuid1();
    const element = document.createElement("article");
    let attr = document.createAttribute("data-id");
    attr.value = id;

    element.setAttributeNode(attr);
    element.classList.add("one-present_responsibilities");

    element.innerHTML = `
      <p class="one-additional_responsibility_present"><span><b>${additional_responsibility_present.value}</b> from <b>${additional_responsibility_present_from.value}</b></span>
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
    deleteBtn.addEventListener("click", delete_item1);
    const editBtn = element.querySelector(".edit-btn");
    editBtn.addEventListener("click", edit_item1);

    // append child
    presents.appendChild(element);
    // display alert1
    display_alert1("Added Successfully", "success");
    // set local storage
    add_to_local_storage1(id, additional_responsibility_present.value, additional_responsibility_present_from.value);
    // // set back to default
    set_back_to_default1();
    // ****************************************** Edit ****************************************** //
  } else if (
    additional_responsibility_present.value &&
    additional_responsibility_present_from.value &&
    edit_flag1
  ) {
    edit_element1.innerHTML = `<b>${additional_responsibility_present.value}</b> from <b>${additional_responsibility_present_from.value}</b>`;
    display_alert1("values changed", "success");

    // edit  local storage
    edit_local_storage1(
      edit_id1,
      additional_responsibility_present.value,
      additional_responsibility_present_from.value
    );
    set_back_to_default1();
  } else {
    display_alert1("please fill all the fields", "danger");
  }
}

// -------------------------------------------- Local Storage -------------------------------------------- //
// get eqd
const get_user1 = () => {
  return window.localStorage.getItem("user")
    ? JSON.parse(window.localStorage.getItem("user"))
    : [];
};

// get eqd
const get_local_storage1 = () => {
  return window.localStorage.getItem("present_responsibilities")
    ? JSON.parse(window.localStorage.getItem("present_responsibilities"))
    : [];
};

// add item to local storage
const add_to_local_storage1 = (id, deg, deg_f) => {
  const item = {
    additional_responsibility_present_id: id,
    additional_responsibility_present: deg,
    additional_responsibility_present_from: deg_f
  };
  let items = get_local_storage1();
  items.push(item);
  window.localStorage.setItem("present_responsibilities", JSON.stringify(items));
};

// remove from local storage
function remove_from_local_storage1(id) {
  let items = get_local_storage1();

  items = items.filter((item) => {
    if (item.additional_responsibility_present_id != id) {
      return item;
    }
  });

  window.localStorage.setItem("present_responsibilities", JSON.stringify(items));
}

// edit an element in local storage
function edit_local_storage1(id, deg, deg_f) {
  let items = get_local_storage1();

  items = items.map(function (item) {
    if (item.additional_responsibility_present_id == id) {
      item.additional_responsibility_present = deg;
      item.additional_responsibility_present_from = deg_f;
    }
    return item;
  });
  window.localStorage.setItem("present_responsibilities", JSON.stringify(items));
  return;
}

// --------------------------------------- Setup Items after refresh --------------------------------------- //

// get from local storage
function setup_items1() {
  presents.innerHTML = "";
  let items = get_local_storage1();
  // console.log("local storage", items);
  if (items.length > 0) {
    items.forEach(function (item) {
      create_list_item1(
        item.additional_responsibility_present_id,
        item.additional_responsibility_present,
        item.additional_responsibility_present_from
      );
    });
  }

  set_back_to_default1();
}

// append te child element to html
async function create_list_item1(id, deg, deg_f) {
  const element = document.createElement("article");
  let attr = document.createAttribute("data-id");
  attr.value = id;

  element.setAttributeNode(attr);
  element.classList.add("one-present_responsibilities");

  element.innerHTML = `
      <p class="one-additional_responsibility_present"><span><b>${deg}</b> from <b>${deg_f}</b></span>
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
  deleteBtn.addEventListener("click", delete_item1);
  const editBtn = element.querySelector(".edit-btn");
  editBtn.addEventListener("click", edit_item1);

  // append child
  presents.appendChild(element);
}

// -------------------------------------------- Submit the form  -------------------------------------------- //
function submit_form1(e) {
  e.preventDefault();

  let educational_qualification = [];

  const present_responsibilities = get_local_storage1();

  // // check if empty
  // if (present_responsibilities.length <= 0) {
  //   display_alert1("please add some present responsibilities", "danger");
  //   return;
  // }

  present_responsibilities.forEach((item) => {
    item.additional_responsibility_present_id = isNaN(Number(item.additional_responsibility_present_id)) ? 0 : item.additional_responsibility_present_id;
    educational_qualification.push(item);
  });
  const user = get_user1();

  if (!user) {
    window.alert("user not logged in");
    return;
  }
  const xhr = new XMLHttpRequest();

  xhr.open(
    "POST",
    `../../api/profile/public/type_4/additional_responsibilities_present.php?ID=${user.user_id}`,
    true
  );

  xhr.onreadystatechange = function () {
    if (xhr.readyState == XMLHttpRequest.DONE) {
      const got = JSON.parse(xhr.responseText);

      if (got.error) {
        display_alert1(got.error, "danger");
      } else {
        window.alert("Present additional responsibilities updated successfully");
        got.forEach((item, index, array) => {
          item.additional_responsibility_present_from = item.additional_responsibility_present_from.substr(0, 7);

          if (index + 1 == array.length) {
            // assign the data
            window.localStorage.setItem("present_responsibilities", JSON.stringify(got));
            setup_items1();
          }
        });
      }
    }
  };

  xhr.send(JSON.stringify(educational_qualification));
}

// --------------------------------------------- Initially  --------------------------------------------- //
// DB data
const db_data1 = () => {
  return new Promise(async (resolve, reject) => {
    const user = get_user1();
    if (!user) {
      window.alert("user not logged in");
      return;
    }

    const xhr = new XMLHttpRequest();

    xhr.open(
      "GET",
      `../../api/profile/public/type_4/additional_responsibilities_present.php?ID=${user.user_id}`,
      true
    );

    xhr.onreadystatechange = function () {
      if (xhr.readyState == XMLHttpRequest.DONE) {
        const got = JSON.parse(xhr.responseText);

        if (got.error) {
          // if can't get the data, thorw the error
          reject(display_alert1(got.error, "danger"));
        } else {
          // change date
          got.forEach((item, index, array) => {
            item.additional_responsibility_present_from = item.additional_responsibility_present_from.substr(0, 7);

            if (index + 1 == array.length) {
              // assign the data
              window.localStorage.setItem("present_responsibilities", JSON.stringify(got));
              resolve(setup_items1());
            }
          });
        }
      }
    };
    xhr.send();
  });
};
// Initial setup
async function initialize1() {
  if (get_local_storage1().length !== 0) {
    setup_items1();
  } else {
    db_data1();
  }
}

// add an present_responsibilities
add_present.addEventListener("click", add_previous1);
// when form submitted
submit_present.addEventListener("click", submit_form1);
// initialize1
window.addEventListener("DOMContentLoaded", initialize1);
// clear all
clear_all_present.addEventListener("click", clear_items1);