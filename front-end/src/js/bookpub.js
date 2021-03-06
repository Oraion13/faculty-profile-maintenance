const bookpub_form = document.getElementById("bookpub_form");
const title = document.getElementById("title");
const published_at = document.getElementById("published_at");
const description = document.getElementById("description");
const books_published = document.getElementById("books_published");
const add_book_pub = document.getElementById("add_book_pub");
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

// -------------------------------------------- add / edit books_published_storage  -------------------------------------------- //
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
    alert1.classList.add(`alert1-${action}`);
    // remove alert1
    setTimeout(function() {
        alert1.textContent = "";
        alert1.classList.remove(`alert1-${action}`);
    }, 4000);
}

// set backt to defaults
function set_back_to_default() {
    title.value = "";
    published_at.value = "";
    description.value = "";
    edit_flag = false;
    edit_id = "";
    add_book_pub.textContent = "Add";
}

// delete an item
function delete_item(e) {
    const element = e.currentTarget.parentElement.parentElement;
    const id = element.dataset.id;

    books_published.removeChild(element);
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
    title.value = edit_element.childNodes[0].innerHTML;
    published_at.value = edit_element.childNodes[2].innerHTML;
    description.value = edit_element.childNodes[4].innerHTML;
    // faculty.value = edit_fac;
    edit_flag = true;
    edit_id = element.dataset.id;
    //
    add_book_pub.textContent = "Edit";
}

// clear items
function clear_items(e) {
    e.preventDefault();

    return new Promise((resolve, reject) => {
        // confirm clear
        if (!window.confirm("Are you sure to clear all?")) {
            return;
        }

        window.localStorage.removeItem("books_published_storage");
        const items = document.querySelectorAll(".one-books_published_storage");
        if (items.length > 0) {
            items.forEach(function(item) {
                books_published.removeChild(item);
            });
        }
        display_alert("removed all books published", "danger");
        resolve(set_back_to_default());
    });
}

// add item to the list
function add_item(e) {
    e.preventDefault();

    // *************************************** create *************************************** //
    if (
        title.value &&
        published_at.value &&
        description.value &&
        !edit_flag
    ) {
        const id = uuid();
        const element = document.createElement("article");
        let attr = document.createAttribute("data-id");
        attr.value = id;

        element.setAttributeNode(attr);
        element.classList.add("one-books_published_storage");

        element.innerHTML = `
      <p class="one-title"><span><b>${title.value}</b> at <b>${published_at.value}</b> Description: <b>${description.value}</b></span>
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
        books_published.appendChild(element);
        // display alert1
        display_alert("Added Successfully", "success");
        // set local storage
        add_to_local_storage(
            id,
            title.value,
            published_at.value,
            description.value
        );
        // // set back to default
        set_back_to_default();
        // ****************************************** Edit ****************************************** //
    } else if (
        title.value &&
        published_at.value &&
        description.value &&
        edit_flag
    ) {
        edit_element.innerHTML = `<b>${title.value}</b> at <b>${published_at.value}</b> Description: <b>${description.value}</b>`;
        display_alert("values changed", "success");

        // edit  local storage
        edit_local_storage(
            edit_id,
            title.value,
            published_at.value,
            description.value
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
    return window.localStorage.getItem("books_published_storage") ?
        JSON.parse(window.localStorage.getItem("books_published_storage")) :
        [];
};

// add item to local storage
const add_to_local_storage = (id, deg, deg_f, deg_t) => {
    const item = {
        book_published_id: id,
        title: deg,
        published_at: deg_f,
        description: deg_t,
    };
    let items = get_local_storage();
    items.push(item);
    window.localStorage.setItem("books_published_storage", JSON.stringify(items));
};

// remove from local storage
function remove_from_local_storage(id) {
    let items = get_local_storage();

    items = items.filter((item) => {
        if (item.book_published_id != id) {
            return item;
        }
    });

    window.localStorage.setItem("books_published_storage", JSON.stringify(items));
}

// edit an element in local storage
function edit_local_storage(id, deg, deg_f, deg_t) {
    let items = get_local_storage();

    items = items.map(function(item) {
        if (item.book_published_id == id) {
            item.title = deg;
            item.published_at = deg_f;
            item.description = deg_t;
        }
        return item;
    });
    window.localStorage.setItem("books_published_storage", JSON.stringify(items));
    return;
}

// --------------------------------------- Setup Items after refresh --------------------------------------- //

// get from local storage
function setup_items() {
    books_published.innerHTML = "";
    let items = get_local_storage();
    // console.log("local storage", items);
    if (items.length > 0) {
        items.forEach(function(item) {
            create_list_item(
                item.book_published_id,
                item.title,
                item.published_at,
                item.description
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
    element.classList.add("one-books_published_storage");

    element.innerHTML = `
      <p class="one-title"><span><b>${deg}</b> at <b>${deg_f}</b> Description: <b>${deg_t}</b></span>
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
    books_published.appendChild(element);
}

// -------------------------------------------- Submit the form  -------------------------------------------- //
function submit_form(e) {
    e.preventDefault();

    let educational_qualification = [];

    const books_published_storage = get_local_storage();

    // // check if empty
    // if (books_published_storage.length <= 0) {
    //   display_alert("please add some present responsibilities", "danger");
    //   return;
    // }

    books_published_storage.forEach((item) => {
        item.book_published_id = isNaN(Number(item.book_published_id)) ?
            0 :
            item.book_published_id;
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
        `../../api/profile/public/type_5/books_published.php?ID=${user.user_id}`,
        true
    );

    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            const got = JSON.parse(xhr.responseText);

            if (got.error) {
                display_alert(got.error, "danger");
            } else {
                window.alert("Books published updated successfully");
                got.forEach((item, index, array) => {
                    item.published_at = item.published_at.substr(0, 7);

                    if (index + 1 == array.length) {
                        // assign the data
                        window.localStorage.setItem(
                            "books_published_storage",
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
            `../../api/profile/public/type_5/books_published.php?ID=${user.user_id}`,
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
                        item.published_at = item.published_at.substr(0, 7);

                        if (index + 1 == array.length) {
                            // assign the data
                            window.localStorage.setItem(
                                "books_published_storage",
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

// add an books_published_storage
add_book_pub.addEventListener("click", add_item);
// when form submitted
bookpub_form.addEventListener("submit", submit_form);
// initialize
window.addEventListener("DOMContentLoaded", initialize);
// clear all
clear_all.addEventListener("click", clear_items);
// previous button
previous.addEventListener("click", () => {
    window.localStorage.removeItem("books_published_storage");
    window.location.replace("./patets.html");
});
// next button
next.addEventListener("click", () => {
    window.localStorage.removeItem("books_published_storage");
    window.location.replace("./other_employment.html");
});