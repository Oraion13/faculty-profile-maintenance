const edq_form = document.getElementById("edq_form");
const degree = document.getElementById("degree");
const degree_from = document.getElementById("degree_from");
const degree_to = document.getElementById("degree_to");
const edqs = document.getElementById("edqs");
const add_edq = document.getElementById("add_edq");
const alert = document.querySelector(".alert");
const clear_all = document.getElementById("clear_all");
const previous = document.getElementById("previous");
const next = document.getElementById("next");

let edit_flag = false;
let edit_element;
let edit_tag;
let edit_from;
let edit_to;
let edit_id = "";

// -------------------------------------------- add / edit edq  -------------------------------------------- //
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
    alert.textContent = text;
    alert.classList.add(`alert-${action}`);
    // remove alert
    setTimeout(function() {
        alert.textContent = "";
        alert.classList.remove(`alert-${action}`);
    }, 4000);
}

// set backt to defaults
function set_back_to_default() {
    degree.value = "";
    degree_from.value = "";
    degree_to.value = "";
    edit_flag = false;
    edit_id = "";
    add_edq.textContent = "Add";
}

// delete an item
function delete_item(e) {
    const element = e.currentTarget.parentElement.parentElement;
    const id = element.dataset.id;

    edqs.removeChild(element);
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
    degree.value = edit_element.childNodes[0].innerHTML;
    degree_from.value = edit_element.childNodes[2].innerHTML;
    degree_to.value = edit_element.childNodes[4].innerHTML;
    // faculty.value = edit_fac;
    edit_flag = true;
    edit_id = element.dataset.id;
    //
    add_edq.textContent = "Edit";
}

// clear items
function clear_items(e) {
    e.preventDefault();

    return new Promise((resolve, reject) => {
        // confirm clear
        if (!window.confirm("Are you sure to clear all?")) {
            return;
        }

        window.localStorage.removeItem("edq");
        const items = document.querySelectorAll(".one-edq");
        if (items.length > 0) {
            items.forEach(function(item) {
                edqs.removeChild(item);
            });
        }
        display_alert("removed all educational qualifications", "danger");
        resolve(set_back_to_default());
    });
}

// add item to the list
function add_edqs(e) {
    e.preventDefault();

    // *************************************** create *************************************** //
    if (degree.value && degree_from.value && degree_to.value && !edit_flag) {
        const id = uuid();
        const element = document.createElement("article");
        let attr = document.createAttribute("data-id");
        attr.value = id;

        element.setAttributeNode(attr);
        element.classList.add("one-edq");

        element.innerHTML = `
      <p class="one-degree"><span><b>${degree.value}</b> from <b>${degree_from.value}</b> to <b>${degree_to.value}</b></span>
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
        edqs.appendChild(element);
        // display alert
        display_alert("Added Successfully", "success");
        // set local storage
        add_to_local_storage(id, degree.value, degree_from.value, degree_to.value);
        // // set back to default
        set_back_to_default();
        // ****************************************** Edit ****************************************** //
    } else if (
        degree.value &&
        degree_from.value &&
        degree_to.value &&
        edit_flag
    ) {
        edit_element.innerHTML = `<b>${degree.value}</b> from <b>${degree_from.value}</b> to <b>${degree_to.value}</b>`;
        display_alert("values changed", "success");

        // edit  local storage
        edit_local_storage(
            edit_id,
            degree.value,
            degree_from.value,
            degree_to.value
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
    return window.localStorage.getItem("edq") ?
        JSON.parse(window.localStorage.getItem("edq")) :
        [];
};

// add item to local storage
const add_to_local_storage = (id, deg, deg_f, deg_t) => {
    const item = {
        degree_id: id,
        degree: deg,
        degree_from: deg_f,
        degree_to: deg_t,
    };
    let items = get_local_storage();
    items.push(item);
    window.localStorage.setItem("edq", JSON.stringify(items));
};

// remove from local storage
function remove_from_local_storage(id) {
    let items = get_local_storage();

    items = items.filter((item) => {
        if (item.degree_id != id) {
            return item;
        }
    });

    window.localStorage.setItem("edq", JSON.stringify(items));
}

// edit an element in local storage
function edit_local_storage(id, deg, deg_f, deg_t) {
    let items = get_local_storage();

    items = items.map(function(item) {
        if (item.degree_id == id) {
            item.degree = deg;
            item.degree_from = deg_f;
            item.degree_to = deg_t;
        }
        return item;
    });
    window.localStorage.setItem("edq", JSON.stringify(items));
    return;
}

// --------------------------------------- Setup Items after refresh --------------------------------------- //

// get from local storage
function setup_items() {
    edqs.innerHTML = "";
    let items = get_local_storage();
    // console.log("local storage", items);
    if (items.length > 0) {
        items.forEach(function(item) {
            create_list_item(
                item.degree_id,
                item.degree,
                item.degree_from,
                item.degree_to
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
    element.classList.add("one-edq");

    element.innerHTML = `
      <p class="one-degree"><span><b>${deg}</b> from <b>${deg_f}</b> to <b>${deg_t}</b></span>
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
    edqs.appendChild(element);
}

// -------------------------------------------- Submit the form  -------------------------------------------- //
function submit_form(e) {
    e.preventDefault();

    let educational_qualification = [];

    const edq = get_local_storage();

    // // check if empty
    // if (edq.length <= 0) {
    //   display_alert("please add some educational qualifications", "danger");
    //   return;
    // }

    edq.forEach((item) => {
        item.degree_id = isNaN(Number(item.degree_id)) ? 0 : item.degree_id;
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
        `../../api/profile/public/type_5/degree.php?ID=${user.user_id}`,
        true
    );

    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            const got = JSON.parse(xhr.responseText);

            if (got.error) {
                display_alert(got.error, "danger");
            } else {
                window.alert("Educational Qualifications updated successfully");
                got.forEach((item, index, array) => {
                    item.degree_from = item.degree_from.substr(0, 7);
                    item.degree_to = item.degree_to.substr(0, 7);

                    if (index + 1 == array.length) {
                        // assign the data
                        window.localStorage.setItem("edq", JSON.stringify(got));
                        resolve(setup_items());
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
            `../../api/profile/public/type_5/degree.php?ID=${user.user_id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw the error
                    reject(display_alert(got.error, "danger"));
                } else {
                    // change date
                    got.forEach((item, index, array) => {
                        item.degree_from = item.degree_from.substr(0, 7);
                        item.degree_to = item.degree_to.substr(0, 7);

                        if (index + 1 == array.length) {
                            // assign the data
                            window.localStorage.setItem("edq", JSON.stringify(got));
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
    if (get_local_storage().length !== 0) {
        setup_items();
    } else {
        db_data();
    }
}

// add an edq
add_edq.addEventListener("click", add_edqs);
// when form submitted
edq_form.addEventListener("submit", submit_form);
// initialize
window.addEventListener("DOMContentLoaded", initialize);
// clear all
clear_all.addEventListener("click", clear_items);
// previous button
previous.addEventListener("click", () => {
    window.localStorage.removeItem("edq");
    window.location.replace("./edit2.html");
});
// next button
next.addEventListener("click", () => {
    window.localStorage.removeItem("edq");
    window.location.replace("./position.html");
});