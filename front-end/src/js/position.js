const position_form = document.getElementById("position_form");
const previous_position = document.getElementById("previous_position");
const previous_position_at = document.getElementById("previous_position_at");
const department = document.getElementById("department");
const previous_position_from = document.getElementById(
  "previous_position_from"
);
const previous_position_to = document.getElementById("previous_position_to");
const positions = document.getElementById("positions");

const alert = document.querySelector(".alert");
const add_position = document.getElementById("add_position");
const clear_all = document.getElementById("clear_all");
const previous = document.getElementById("previous");
const next = document.getElementById("next");

let edit_flag = false;
let edit_element;
let edit_tag;
let edit_at;
let edit_dept;
let edit_from;
let edit_to;
let edit_id = "";

// -------------------------------------------- add / edit pos  -------------------------------------------- //
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
  alert.textContent = text;
  alert.classList.add(`alert-${action}`);
  // remove alert
  setTimeout(function () {
    alert.textContent = "";
    alert.classList.remove(`alert-${action}`);
  }, 4000);
}

// set backt to defaults
function set_back_to_default() {
  previous_position.value = "default";
  previous_position_at.value = "";
  department.value = "default";
  previous_position_from.value = "";
  previous_position_to.value = "";

  edit_flag = false;
  edit_id = "";
  add_position.textContent = "Add";
}

// delete an item
function delete_item(e) {
  const element = e.currentTarget.parentElement.parentElement;
  const id = element.dataset.id;

  positions.removeChild(element);
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
  previous_position.value = element.dataset.po;
  department.value = element.dataset.de;
  previous_position_at.value = edit_element.childNodes[4].innerHTML;
  previous_position_from.value = edit_element.childNodes[6].innerHTML;
  previous_position_to.value = edit_element.childNodes[8].innerHTML;
  // faculty.value = edit_fac;
  edit_flag = true;
  edit_id = element.dataset.id;
  //
  add_position.textContent = "Edit";
}

// clear items
function clear_items(e) {
  e.preventDefault();

  return new Promise((resolve, reject) => {
    // confirm clear
    if (!window.confirm("Are you sure to clear all?")) {
      return;
    }

    window.localStorage.removeItem("pos");
    const items = document.querySelectorAll(".one-position");
    if (items.length > 0) {
      items.forEach(function (item) {
        positions.removeChild(item);
      });
    }
    display_alert("removed all faculty's previous position", "danger");
    resolve(set_back_to_default());
  });
}

// add item to the list
function add_positions(e) {
  e.preventDefault();

  // *************************************** create *************************************** //
  if (
    !isNaN(previous_position.value) &&
    previous_position_at.value &&
    !isNaN(department.value) &&
    previous_position_from.value &&
    previous_position_to.value &&
    !edit_flag
  ) {
    const id = uuid();
    const element = document.createElement("article");
    let attr = document.createAttribute("data-id");
    attr.value = id;
    let po = document.createAttribute("data-po");
    po.value = previous_position.value;
    let de = document.createAttribute("data-de");
    de.value = department.value;

    element.setAttributeNode(attr);
    element.setAttributeNode(po);
    element.setAttributeNode(de);
    element.classList.add("one-position");

    element.innerHTML = `
      <p><span><b>${
        previous_position.options[previous_position.selectedIndex].text
      }</b> in 
      <b>${department.options[department.selectedIndex].text}</b> at <b>${
      previous_position_at.value
    }</b> from 
      <b>${previous_position_from.value}</b> to 
      <b>${previous_position_to.value}</b></span>
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
    positions.appendChild(element);
    // display alert
    display_alert("Added Successfully", "success");
    // set local storage
    add_to_local_storage(
      id,
      previous_position.value,
      previous_position_at.value,
      department.value,
      previous_position_from.value,
      previous_position_to.value
    );
    // // set back to default
    set_back_to_default();
    // ****************************************** Edit ****************************************** //
  } else if (
    !isNaN(previous_position.value) &&
    previous_position_at.value &&
    !isNaN(department.value) &&
    previous_position_from.value &&
    previous_position_to.value &&
    edit_flag
  ) {
    edit_element.innerHTML = `<b>${
      previous_position.options[previous_position.selectedIndex].text
    }</b> in 
    <b>${department.options[department.selectedIndex].text}</b> at <b>${
      previous_position_at.value
    }</b> from 
    <b>${previous_position_from.value}</b> to 
    <b>${previous_position_to.value}</b>`;
    display_alert("values changed", "success");

    // edit  local storage
    edit_local_storage(
      edit_id,
      previous_position.value,
      previous_position_at.value,
      department.value,
      previous_position_from.value,
      previous_position_to.value
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
  return window.localStorage.getItem("pos")
    ? JSON.parse(window.localStorage.getItem("pos"))
    : [];
};

// add item to local storage
const add_to_local_storage = (
  id,
  posit,
  posit_at,
  dept,
  posit_from,
  posit_to
) => {
  const item = {
    position_prev_id: id,
    position_id: posit,
    department_id: dept,
    position_prev_where: posit_at,
    position_prev_from: posit_from,
    position_prev_to: posit_to,
  };
  let items = get_local_storage();
  items.push(item);
  window.localStorage.setItem("pos", JSON.stringify(items));
};

// remove from local storage
function remove_from_local_storage(id) {
  let items = get_local_storage();

  items = items.filter((item) => {
    if (item.position_prev_id != id) {
      return item;
    }
  });

  window.localStorage.setItem("pos", JSON.stringify(items));
}

// edit an element in local storage
function edit_local_storage(id, posit, posit_at, dept, posit_from, posit_to) {
  let items = get_local_storage();

  items = items.map(function (item) {
    if (item.position_prev_id == id) {
      item.position_id = posit;
      item.position_prev_where = posit_at;
      item.department_id = dept;
      item.position_prev_from = posit_from;
      item.position_prev_to = posit_to;
    }
    return item;
  });
  window.localStorage.setItem("pos", JSON.stringify(items));
  return;
}

// --------------------------------------- Setup Items after refresh --------------------------------------- //

// get from local storage
function setup_items() {
  positions.innerHTML = "";
  let items = get_local_storage();
  // console.log("local storage", items);
  if (items.length > 0) {
    items.forEach(function (item) {
      create_list_item(
        item.position_prev_id,
        item.position_id,
        item.department_id,
        item.position_prev_where,
        item.position_prev_from,
        item.position_prev_to
      );
    });
  }

  set_back_to_default();
}

// append te child element to html
async function create_list_item(
  id,
  posit,
  dept,
  posit_at,
  posit_from,
  posit_to
) {
  const element = document.createElement("article");
  let attr = document.createAttribute("data-id");
  attr.value = id;
  let po = document.createAttribute("data-po");
  po.value = posit;
  let de = document.createAttribute("data-de");
  de.value = dept;

  element.setAttributeNode(attr);
  element.setAttributeNode(po);
  element.setAttributeNode(de);
  element.classList.add("one-position");

  previous_position.value = posit;
  department.value = dept;

  element.innerHTML = `
    <p><span><b>${
      previous_position.options[previous_position.selectedIndex].text
    }</b> in 
    <b>${
      department.options[department.selectedIndex].text
    }</b> at <b>${posit_at}</b> from 
    <b>${posit_from}</b> to 
    <b>${posit_to}</b></span>
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
  positions.appendChild(element);
}

// -------------------------------------------- Submit the form  -------------------------------------------- //
function submit_form(e) {
  e.preventDefault();

  let previous_positions = [];

  const pos = get_local_storage();

  // // check if empty
  // if (pos.length <= 0) {
  //   display_alert("please add some educational qualifications", "danger");
  //   return;
  // }

  pos.forEach((item) => {
    item.position_prev_id = isNaN(Number(item.position_prev_id))
      ? 0
      : item.position_prev_id;
    previous_positions.push(item);
  });

  const user = get_user();

  if (!user) {
    window.alert("user not logged in");
    return;
  }
  const xhr = new XMLHttpRequest();

  xhr.open(
    "POST",
    `../../api/profile/public/type_0/positions_prev.php?ID=${user.user_id}`,
    true
  );

  xhr.onreadystatechange = function () {
    if (xhr.readyState == XMLHttpRequest.DONE) {
      const got = JSON.parse(xhr.responseText);

      if (got.error) {
        display_alert(got.error, "danger");
      } else {
        window.alert("Previous positions updated successfully");
        got.forEach((item, index, array) => {
          item.position_prev_from = item.position_prev_from.substr(0, 7);
          item.position_prev_to = item.position_prev_to.substr(0, 7);

          if (index + 1 == array.length) {
            console.log(got);
            // assign the data
            window.localStorage.setItem("pos", JSON.stringify(got));
            resolve(setup_items());
          }
        });
      }
    }
  };

  xhr.send(JSON.stringify(previous_positions));
}

// --------------------------------------------- Initially  --------------------------------------------- //

// get positions
function setup_position() {
  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();

    xhr.open("GET", `../../api/profile/public/type_2/positions.php`, true);

    xhr.onreadystatechange = function () {
      if (xhr.readyState == XMLHttpRequest.DONE) {
        const got = JSON.parse(xhr.responseText);

        if (got.error) {
          reject(window.alert(got.error));
        } else {
          got.forEach((element, index, array) => {
            append_position(element.position_id, element.position);

            if (index + 1 == array.length) {
              resolve();
            }
          });
        }
      }
    };
    xhr.send();
  });
}

// append a child element in the document list
const append_position = (id, value) => {
  const element = document.createElement("option");
  let attr = document.createAttribute("value");
  attr.value = id;
  element.setAttributeNode(attr);
  element.innerHTML = `${value}`;

  previous_position.appendChild(element);
};

// get departments
function setup_departments() {
  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();

    xhr.open("GET", `../../api/profile/public/type_2/departments.php`, true);

    xhr.onreadystatechange = function () {
      if (xhr.readyState == XMLHttpRequest.DONE) {
        const got = JSON.parse(xhr.responseText);

        if (got.error) {
          reject(window.alert(got.error));
        } else {
          got.forEach((element, index, array) => {
            append_department(element.department_id, element.department);

            if (index + 1 == array.length) {
              resolve();
            }
          });
        }
      }
    };
    xhr.send();
  });
}

// append a child element in the document list
const append_department = (id, value) => {
  const element = document.createElement("option");
  let attr = document.createAttribute("value");
  attr.value = id;
  element.setAttributeNode(attr);
  element.innerHTML = `${value}`;

  department.appendChild(element);
};

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
      `../../api/profile/public/type_0/positions_prev.php?ID=${user.user_id}`,
      true
    );

    xhr.onreadystatechange = function () {
      if (xhr.readyState == XMLHttpRequest.DONE) {
        const got = JSON.parse(xhr.responseText);

        if (got.error) {
          // if can't get the data, thorw the error
          reject(display_alert(got.error, "danger"));
        } else {
          got.forEach((item, index, array) => {
            item.position_prev_from = item.position_prev_from.substr(0, 7);
            item.position_prev_to = item.position_prev_to.substr(0, 7);

            if (index + 1 == array.length) {
              console.log(got);
              // assign the data
              window.localStorage.setItem("pos", JSON.stringify(got));
              resolve(setup_items());
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
  await setup_position();
  await setup_departments();
  if (get_local_storage().length !== 0) {
    setup_items();
  } else {
    db_data();
  }
}

// add an pos
add_position.addEventListener("click", add_positions);
// when form submitted
position_form.addEventListener("submit", submit_form);
// initialize
window.addEventListener("DOMContentLoaded", initialize);
// clear all
clear_all.addEventListener("click", clear_items);
// previous button
previous.addEventListener("click", () => {
  window.localStorage.removeItem("pos");
  window.location.replace("./edq.html");
});
// next button
next.addEventListener("click", () => {
  window.localStorage.removeItem("pos");
  window.location.replace("./responsibility.html");
});
