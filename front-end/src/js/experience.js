const experience_form = document.getElementById("experience_form");
const exp_abroad = document.getElementById("exp_abroad");
const exp_abroad_from = document.getElementById("exp_abroad_from");
const exp_abroad_to = document.getElementById("exp_abroad_to");
const purpose_of_visit = document.getElementById("purpose_of_visit");
const all_exp = document.getElementById("all_exp");
const add_exp = document.getElementById("add_exp");
const alert1 = document.querySelector(".alert1");
const clear_all = document.getElementById("clear_all");
const previous = document.getElementById("previous");
const next = document.getElementById("next");

let edit_flag = false;
let edit_element;
let edit_tag;
let edit_from;
let edit_to;
let edit_extra;
let edit_id = "";

// -------------------------------------------- add / edit experience_abroad_storage  -------------------------------------------- //
// Generate unique ID
function uuid() {
    return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function(c) {
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
    setTimeout(function() {
        alert1.textContent = "";
        alert1.classList.remove(`alert-${action}`);
    }, 4000);
}

// set backt to defaults
function set_back_to_default() {
    exp_abroad.value = "";
    exp_abroad_from.value = "";
    exp_abroad_to.value = "";
    purpose_of_visit.value = ""
    edit_flag = false;
    edit_id = "";
    add_exp.textContent = "Add";
}

// delete an item
function delete_item(e) {
    const element = e.currentTarget.parentElement.parentElement;
    const id = element.dataset.id;

    all_exp.removeChild(element);
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
    exp_abroad.value = edit_element.childNodes[0].innerHTML;
    exp_abroad_from.value = edit_element.childNodes[2].innerHTML;
    exp_abroad_to.value = edit_element.childNodes[4].innerHTML;
    purpose_of_visit.value = edit_element.childNodes[6].innerHTML
        // faculty.value = edit_fac;
    edit_flag = true;
    edit_id = element.dataset.id;
    //
    add_exp.textContent = "Edit";
}

// clear items
function clear_items(e) {
    e.preventDefault();

    return new Promise((resolve, reject) => {
        // confirm clear
        if (!window.confirm("Are you sure to clear all?")) {
            return;
        }

        window.localStorage.removeItem("experience_abroad_storage");
        const items = document.querySelectorAll(".one-experience_abroad_storage");
        if (items.length > 0) {
            items.forEach(function(item) {
                all_exp.removeChild(item);
            });
        }
        display_alert("removed all experience abroad", "danger");
        resolve(set_back_to_default());
    });
}

// add item to the list
function add_previous(e) {
    e.preventDefault();

    // *************************************** create *************************************** //
    if (
        exp_abroad.value &&
        exp_abroad_from.value &&
        exp_abroad_to.value &&
        purpose_of_visit.value &&
        !edit_flag
    ) {
        const id = uuid();
        const element = document.createElement("article");
        let attr = document.createAttribute("data-id");
        attr.value = id;

        element.setAttributeNode(attr);
        element.classList.add("one-experience_abroad_storage");

        element.innerHTML = `
      <p class="one-exp_abroad"><span><b>${exp_abroad.value}</b> from 
      <b>${exp_abroad_from.value}</b> to 
      <b>${exp_abroad_to.value}</b> purpose:
      <b>${purpose_of_visit.value}</b>
      </span>
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
        all_exp.appendChild(element);
        // display alert1
        display_alert("Added Successfully", "success");
        // set local storage
        add_to_local_storage(
            id,
            exp_abroad.value,
            exp_abroad_from.value,
            exp_abroad_to.value,
            purpose_of_visit.value
        );
        // // set back to default
        set_back_to_default();
        // ****************************************** Edit ****************************************** //
    } else if (
        exp_abroad.value &&
        exp_abroad_from.value &&
        exp_abroad_to.value &&
        edit_flag
    ) {
        edit_element.innerHTML = `<b>${exp_abroad.value}</b> from 
    <b>${exp_abroad_from.value}</b> to 
    <b>${exp_abroad_to.value}</b> purpose:
    <b>${purpose_of_visit.value}</b>`;
        display_alert("values changed", "success");

        // edit  local storage
        edit_local_storage(
            edit_id,
            exp_abroad.value,
            exp_abroad_from.value,
            exp_abroad_to.value,
            purpose_of_visit.value
        );
        set_back_to_default();
    } else {
        display_alert("please fill all the fields", "danger");
    }
}

// -------------------------------------------- Local Storage -------------------------------------------- //
// get eqd
const get_user = () => {
    return window.localStorage.getItem("user") ?
        JSON.parse(window.localStorage.getItem("user")) :
        [];
};

// get eqd
const get_local_storage = () => {
    return window.localStorage.getItem("experience_abroad_storage") ?
        JSON.parse(window.localStorage.getItem("experience_abroad_storage")) :
        [];
};

// add item to local storage
const add_to_local_storage = (id, deg, deg_f, deg_t, ext) => {
    const item = {
        exp_abroad_id: id,
        exp_abroad: deg,
        exp_abroad_from: deg_f,
        exp_abroad_to: deg_t,
        purpose_of_visit: ext
    };
    let items = get_local_storage();
    items.push(item);
    window.localStorage.setItem("experience_abroad_storage", JSON.stringify(items));
};

// remove from local storage
function remove_from_local_storage(id) {
    let items = get_local_storage();

    items = items.filter((item) => {
        if (item.exp_abroad_id != id) {
            return item;
        }
    });

    window.localStorage.setItem("experience_abroad_storage", JSON.stringify(items));
}

// edit an element in local storage
function edit_local_storage(id, deg, deg_f, deg_t, ext) {
    let items = get_local_storage();

    items = items.map(function(item) {
        if (item.exp_abroad_id == id) {
            item.exp_abroad = deg;
            item.exp_abroad_from = deg_f;
            item.exp_abroad_to = deg_t;
            item.purpose_of_visit = ext
        }
        return item;
    });
    window.localStorage.setItem("experience_abroad_storage", JSON.stringify(items));
    return;
}

// --------------------------------------- Setup Items after refresh --------------------------------------- //

// get from local storage
function setup_items() {
    all_exp.innerHTML = "";
    let items = get_local_storage();
    // console.log("local storage", items);
    if (items.length > 0) {
        items.forEach(function(item) {
            create_list_item(
                item.exp_abroad_id,
                item.exp_abroad,
                item.exp_abroad_from,
                item.exp_abroad_to,
                item.purpose_of_visit
            );
        });
    }

    set_back_to_default();
}

// append te child element to html
async function create_list_item(id, deg, deg_f, deg_t, ext) {
    const element = document.createElement("article");
    let attr = document.createAttribute("data-id");
    attr.value = id;

    element.setAttributeNode(attr);
    element.classList.add("one-experience_abroad_storage");

    element.innerHTML = `
      <p class="one-exp_abroad"><span><b>${deg}</b> from 
      <b>${deg_f}</b> to 
      <b>${deg_t}</b> purpose:
      <b>${ext}</b>
      </span>
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
    all_exp.appendChild(element);
}

// -------------------------------------------- Submit the form  -------------------------------------------- //
function submit_form(e) {
    e.preventDefault();

    let educational_qualification = [];

    const experience_abroad_storage = get_local_storage();

    // // check if empty
    // if (experience_abroad_storage.length <= 0) {
    //   display_alert("please add some present responsibilities", "danger");
    //   return;
    // }

    experience_abroad_storage.forEach((item) => {
        item.exp_abroad_id = isNaN(Number(item.exp_abroad_id)) ?
            0 :
            item.exp_abroad_id;
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
        `../../api/profile/public/type_6/exp_abroad.php?ID=${user.user_id}`,
        true
    );

    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            const got = JSON.parse(xhr.responseText);

            if (got.error) {
                display_alert(got.error, "danger");
            } else {
                window.alert("Experience abroad updated successfully");
                got.forEach((item, index, array) => {
                    item.exp_abroad_from = item.exp_abroad_from.substr(0, 7);
                    item.exp_abroad_to = item.exp_abroad_to.substr(0, 7);

                    if (index + 1 == array.length) {
                        // assign the data
                        window.localStorage.setItem(
                            "experience_abroad_storage",
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
    return new Promise(async(resolve, reject) => {
        const user = get_user();
        if (!user) {
            window.alert("user not logged in");
            return;
        }

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_6/exp_abroad.php?ID=${user.user_id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw the error
                    display_alert(got.error, "danger");
                } else {
                    // change date
                    got.forEach((item, index, array) => {
                        item.exp_abroad_from = item.exp_abroad_from.substr(0, 7);
                        item.exp_abroad_to = item.exp_abroad_to.substr(0, 7);

                        if (index + 1 == array.length) {
                            // assign the data
                            window.localStorage.setItem(
                                "experience_abroad_storage",
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

// add an experience_abroad_storage
add_exp.addEventListener("click", add_previous);
// when form submitted
experience_form.addEventListener("submit", submit_form);
// initialize
window.addEventListener("DOMContentLoaded", initialize);
// clear all
clear_all.addEventListener("click", clear_items);
// previous button
previous.addEventListener("click", () => {
    window.localStorage.removeItem("experience_abroad_storage");
    window.location.replace("./honors.html");
});
// next button
next.addEventListener("click", () => {
    window.localStorage.removeItem("experience_abroad_storage");
    window.location.replace("./lectures.html");
});