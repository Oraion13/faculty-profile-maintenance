// get user from local storage
const get_user = () => {
    return window.localStorage.getItem("user") ?
        JSON.parse(window.localStorage.getItem("user")) :
        [];
};

// Users form
const honorific = document.getElementById("honorific");
const full_name = document.getElementById("full_name");
const username = document.getElementById("username");
const email = document.getElementById("email");

// User info
const faculty_position = document.getElementById("faculty_position");
const department = document.getElementById("department");
const zipcode = document.getElementById("zipcode");
// const inputDistrict = document.getElementById("inputDistrict");
const inputState = document.getElementById("inputState");
const address = document.getElementById("address");
const phone = document.getElementById("phone");
const position_present_from = document.getElementById("position_present_from");
const position_present_where = document.getElementById(
    "position_present_where"
);

// Photo
const photo = document.getElementById("photo");
const display_photo = document.getElementById("display_photo");

// buttons
const update_user = document.getElementById("update_user");
const upload_photo = document.getElementById("upload_photo");
const update_user_info = document.getElementById("update_user_info");

// --------------------------------------------- update user form --------------------------------------------- //
// Setup user basic info
const setup_user = () => {
    return new Promise((resolve, reject) => {
        const user = get_user();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_0/users.php?ID=${user.user_id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    reject(window.alert(got.error));
                } else {
                    honorific.value = got.honorific;
                    full_name.value = got.full_name;
                    username.value = got.username;
                    email.value = got.email;
                    resolve();
                }
            }
        };
        xhr.send();
    });
};

// Update user info
function submit_user(e) {
    e.preventDefault();

    return new Promise((resolve, reject) => {
        const user = get_user();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "PUT",
            `../../api/profile/public/type_0/users.php?ID=${user.user_id}`,
            true
        );

        const user_info = {
            honorific: honorific.value,
            full_name: full_name.value,
            username: username.value,
            email: email.value,
        };

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    reject(window.alert(got.error));
                } else {
                    window.alert("Info updated successfully");
                    resolve(setup_user());
                }
            }
        };
        xhr.send(JSON.stringify(user_info));
    });
}

// ----------------------------------------------- update user info  ----------------------------------------------- //

// get positions
function setup_position() {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();

        xhr.open("GET", `../../api/profile/public/type_2/positions.php`, true);

        xhr.onreadystatechange = function() {
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

    faculty_position.appendChild(element);
};

// get departments
function setup_departments() {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();

        xhr.open("GET", `../../api/profile/public/type_2/departments.php`, true);

        xhr.onreadystatechange = function() {
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

// Setup user basic info
const setup_user_info = () => {
    return new Promise((resolve, reject) => {
        const user = get_user();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_0/user_info.php?ID=${user.user_id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    reject(window.alert(got.error));
                } else {
                    (faculty_position.value = got.position_id),
                    (department.value = got.department_id);
                    phone.value = got.phone;
                    position_present_where.value = got.position_present_where;
                    position_present_from.value = got.position_present_from
                        .split("-")[0]
                        .concat("-", got.position_present_from.split("-")[1]);

                    const addresses = got.address.split(", ");

                    let last = "";
                    for (let index = 0; index < addresses.length - 2; index++) {
                        last = last + addresses[index] + ", ";
                    }

                    address.value = last;
                    inputState.value = addresses[addresses.length - 2];
                    // inputDistrict.value = addresses[addresses.length - 2];
                    zipcode.value = addresses[addresses.length - 1];
                    resolve();
                }
            }
        };
        xhr.send();
    });
};

// submit user info
function submit_user_info(e) {
    e.preventDefault();

    return new Promise((resolve, reject) => {
        const user = get_user();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "PUT",
            `../../api/profile/public/type_0/user_info.php?ID=${user.user_id}`,
            true
        );

        const user_info = {
            position_id: faculty_position.value,
            department_id: department.value,
            phone: phone.value,
            position_present_where: position_present_where.value,
            position_present_from: position_present_from.value,
            address: address.value
                .concat(", ", inputState.value)
                .concat(", ", zipcode.value),
        };

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    reject(window.alert(got.error));
                } else {
                    window.alert("Info updated successfully");
                    resolve(setup_user_info());
                }
            }
        };
        xhr.send(JSON.stringify(user_info));
    });
}

// -------------------------------------------------- Photo -------------------------------------------------- //

// submit photo
function submit_photo(e) {
    e.preventDefault();
    const user = get_user();

    const formData = new FormData();

    formData.append("photo", photo.files[0]);

    const xhr = new XMLHttpRequest();

    xhr.open(
        "POST",
        `../../api/profile/public/type_5/photo.php?ID=${user.user_id}`,
        true
    );

    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            const got = JSON.parse(xhr.responseText);

            if (got.error) {
                reject(window.alert(got.error));
            } else {
                setup_photo();
            }
        }
    };

    xhr.send(formData);
}

// get photo
const setup_photo = () => {
    return new Promise((resolve, reject) => {
        const user = get_user();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_5/photo.php?ID=${user.user_id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    reject(window.alert(got.error));
                } else {
                    display_photo.innerHTML = `<iframe src = "data:${got.photo_type};base64,${got.photo}"
                    height = "100%"
                    width = "100%"> </iframe>`;
                }
            }
        };
        xhr.send();
    });
};

// initialize info
async function initialize() {
    await setup_user();
    await setup_position();
    await setup_departments();
    await setup_user_info();
    await setup_photo();
}

// upload user pic
upload_photo.addEventListener("click", submit_photo);
// update user info
update_user_info.addEventListener("click", submit_user_info);
// upload user data
update_user.addEventListener("click", submit_user);
// initialize data from db
window.addEventListener("DOMContentLoaded", initialize);