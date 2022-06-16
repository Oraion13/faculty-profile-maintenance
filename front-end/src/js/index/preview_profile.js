const table_faculty = document.getElementById("table_faculty");
const table_faculty_body = document.getElementById("table_faculty_body");

window.jsPDF = window.jspdf.jsPDF;
const doc = new window.jspdf.jsPDF();
let faculty_name = "";
let faculty_dept = "";
// ------------------------------------------------  Helpers  ------------------------------------------------ //
// Date to month-year
const month_year = (db_date) => {
    var months = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
    ];
    var now = new Date(db_date);
    return months[now.getMonth()] + "-" + now.getFullYear();
};

// Get from local storage
const get_faculty_id = () => window.localStorage.getItem("faculty_id");

// ------------------------------------------------ User  ------------------------------------------------ //
// Get user
function setup_user() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open("GET", `../../api/profile/public/type_0/users.php?ID=${id}`, true);

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    const element1 = document.createElement("tr");
                    const element2 = document.createElement("tr");

                    element1.innerHTML = `
                      <th>Name :</th><td>${got.honorific} ${got.full_name}</td>`;
                    element2.innerHTML = `
                      <th>E-mail ID :</th><td>${got.email}</td>
                      `;

                    faculty_name = `${got.honorific} ${got.full_name}`;
                    table_faculty_body.appendChild(element1);
                    resolve(table_faculty_body.appendChild(element2));
                }
            }
        };
        xhr.send();
    });
}

// ------------------------------------------------ Profile picture ----------------------------------------------- //
// get photo
const setup_photo = () => {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open("GET", `../../api/profile/public/type_5/photo.php?ID=${id}`, true);

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    reject(window.alert(got.error));
                } else {
                    const element = document.createElement("div");
                    const trr = document.createElement("tr");
                    const trd = document.createElement("td");
                    const trd1 = document.createElement("td");

                    element.innerHTML = `<img src="data:${got.photo_type};base64,${got.photo}"
                      alt="profile pic"
                                            width = "170px">`;

                    var imgData = `data:${got.photo_type};base64,${got.photo}`;
                    doc.addImage(imgData, "JPEG", 162, 16, 33, 42);
                    doc.output("datauri");
                    // Header

                    // pdf table

                    // resolve(table_faculty_body.appendChild(trr));
                    resolve();
                }
            }
        };
        xhr.send();
    });
};

// ------------------------------------------------ User Info  ------------------------------------------------ //
function setup_user_info() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_0/user_info.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    const element1 = document.createElement("tr");
                    const element2 = document.createElement("tr");
                    const element3 = document.createElement("tr");
                    const element4 = document.createElement("tr");
                    element1.innerHTML = `
                        <th>Cellphone : </th><td>${got.phone}</td>
                        `;
                    element2.innerHTML = `
                        <th>Address : </th><td>${got.address}</td>
                        `;

                    element3.innerHTML = `
              <th colspan="2">Present Position</th>`;
                    element4.innerHTML = `
              <td colspan="2">${got.position}, ${got.department}, ${
            got.position_present_where
          } from ${month_year(got.position_present_from)}</td>
              `;

                    faculty_dept = `${got.department}`;

                    table_faculty_body.appendChild(element1);
                    table_faculty_body.appendChild(element2);
                    table_faculty_body.appendChild(element3);
                    table_faculty_body.appendChild(element4);

                    resolve();
                }
            }
        };
        xhr.send();
    });
}

// ------------------------------------------------ Present Additional Responsiblitites  ------------------------------------------------ //=
function setup_present_add_respo() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_4/additional_responsibilities_present.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    let element = document.createElement("tr");

                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    element.innerHTML = `
                      <th colspan="2">Present Additional Responsibilities</th>
                      `;
                    table_faculty_body.appendChild(element);

                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");
                        e.innerHTML = `
            <td>${index + 1})</td>
            <td>${item.additional_responsibility_present}
                     from ${month_year(
                       item.additional_responsibility_present_from
                     )}
            </td>`;

                        table_faculty_body.appendChild(e);

                        if (index + 1 == array.length) {
                            // finally resolve main element
                            resolve();
                        }
                    });
                }
            }
        };
        xhr.send();
    });
}

// ------------------------------------------------ Previous positions  ------------------------------------------------ //
// Get user
function setup_previous_positions() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_0/positions_prev.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");
                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    element.innerHTML = `
                  <th colspan="2">Previous Positions</th>
                  `;

                    table_faculty_body.appendChild(element);
                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");
                        e.innerHTML = `
            <td>${index + 1})</td>
                    <td>${item.position}, ${item.department}, ${
              item.position_prev_where
            }
                 during ${month_year(item.position_prev_from)} and ${month_year(
              item.position_prev_to
            )}</td>`;

                        table_faculty_body.appendChild(e);

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

// ------------------------------------------------ Previous Additional Responsiblitites  ------------------------------------------------ //=
function setup_previous_add_respo() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_5/additional_responsibilities_prev.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");

                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    // append list to main element
                    element.innerHTML = `
                    <th colspan="2">Previous Additional Responsibilities</th>
                    `;
                    table_faculty_body.appendChild(element);

                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");
                        e.innerHTML = `<td>${index + 1})</td>
                      <td style="padding-left: 1em">${
                        item.additional_responsibility_prev
                      }
                   during ${month_year(
                     item.additional_responsibility_prev_from
                   )} and ${month_year(
              item.additional_responsibility_prev_to
            )}</td>`;

                        table_faculty_body.appendChild(e);
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

// ------------------------------------------------ Other Employment  ------------------------------------------------ //=
function setup_other_employment() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_5/other_employment.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");

                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    // append list to main element
                    element.innerHTML = `
                      <th colspan="2">Other Employment</th>
                      `;
                    table_faculty_body.appendChild(element);

                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");
                        e.innerHTML = `<td>${index + 1})</td>
                        <td style="padding-left: 1em">${item.other_employment}
                     during ${month_year(
                       item.other_employment_from
                     )} and ${month_year(item.other_employment_to)}</td>`;

                        table_faculty_body.appendChild(e);

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

// ------------------------------------------------ Degree  ------------------------------------------------ //=
function setup_other_employment() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_5/degree.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");

                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    // append list to main element
                    element.innerHTML = `
                        <th colspan="2">Degree</th>
                        `;
                    table_faculty_body.appendChild(element);

                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");
                        e.innerHTML = `<td>${index + 1})</td>
                          <td style="padding-left: 1em">${item.degree}
                       (${month_year(item.degree_from)} and ${month_year(
              item.degree_to
            )})</td>`;
                        table_faculty_body.appendChild(e);

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

// ------------------------------------------------ Research Degree  ------------------------------------------------ //
function setup_research_degree() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_6/research_degree.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");

                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    // append list to main element
                    element.innerHTML = `
                          <th colspan="2">Research Degree</th>
                          `;
                    table_faculty_body.appendChild(element);

                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");

                        e.innerHTML = `<td>${index + 1})</td>
                            <td style="padding-left: 1em">${
                              item.research_degree
                            }
                         (${month_year(
                           item.research_degree_from
                         )} and ${month_year(
              item.research_degree_to
            )}) </br> title: ${item.title}</td>`;

                        table_faculty_body.appendChild(e);

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

// ------------------------------------------------ Area of Specialization  ------------------------------------------------ //
function setup_area_of_spec() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_3/area_of_specialization.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");
                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    // append list to main element
                    element.innerHTML = `
                          <th colspan="2">Area of Specialization</th>
                          `;
                    table_faculty_body.appendChild(element);

                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");
                        e.innerHTML = `<td>${index + 1})</td>
                            <td style="padding-left: 1em">${item.specialization}</td>`;

                        table_faculty_body.appendChild(e);

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

// ------------------------------------------------ Memberships  ------------------------------------------------ //
function setup_membership() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_3/memberships.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");

                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    // append list to main element
                    element.innerHTML = `
                          <th colspan="2">Membership in Professional Organization</th>
                          `;
                    table_faculty_body.appendChild(element);

                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");
                        e.innerHTML = `<td>${index + 1})</td>
                            <td style="padding-left: 1em">${item.membership}</td>`;
                        table_faculty_body.appendChild(e);
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

// ------------------------------------------------ Research Guidance  ------------------------------------------------ //
function setup_membership() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_8/research_guidance.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");
                    const element1 = document.createElement("tr");
                    const element2 = document.createElement("tr");
                    const element3 = document.createElement("tr");
                    const element4 = document.createElement("tr");
                    const element5 = document.createElement("tr");
                    const element6 = document.createElement("tr");

                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    element.innerHTML = `<th colspan="2">Membership in Professional Organization</th>`;
                    element1.innerHTML = `<th>Number of Ph.D Scholars Guided :</th><td>${got[0].phd_guided}</td>`;
                    element2.innerHTML = `<th>Number of Ph.D Scholars Guiding :</th><td>${got[0].phd_guiding}</td>`;
                    element3.innerHTML = `<th>Number of M.E./ M.Tech. Projects Guided :</th><td>${got[0].me_guided}</td>`;
                    element4.innerHTML = `<th>Number of M.E./ M.Tech. Projects Guiding :</th><td>${got[0].me_guiding}</td>`;
                    element5.innerHTML = `<th>Number of M.S (By Research) Students Guided :</th><td>${got[0].ms_guided}</td>`;
                    element6.innerHTML = `<th>Number of M.S (By Research) Students Guiding :</th><td>${got[0].ms_guiding}</td>`;

                    table_faculty_body.appendChild(element);
                    table_faculty_body.appendChild(element1);
                    table_faculty_body.appendChild(element2);
                    table_faculty_body.appendChild(element3);
                    table_faculty_body.appendChild(element4);
                    table_faculty_body.appendChild(element5);
                    table_faculty_body.appendChild(element6);
                    // finally resolve main element
                    resolve();
                }
            }
        };
        xhr.send();
    });
}

// ------------------------------------------------ Papers Published  ------------------------------------------------ //
function setup_papers_published() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_5/papers_published.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");

                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    element.innerHTML = `<th colspan="2">Papers Published</th>`;

                    table_faculty_body.appendChild(element);
                    let intern = 0;
                    let non_intern = 0;
                    // create list
                    got.forEach((item, index, array) => {
                        if (item.is_international == "yes") {
                            intern += 1;
                        } else {
                            non_intern += 1;
                        }

                        if (index + 1 == array.length) {
                            // append list to main element
                            const e = document.createElement("tr");
                            const e1 = document.createElement("tr");

                            e.innerHTML = `
                            <th colspan="2">Research Papers Published in International Journals : ${intern}</th>
                            `;
                            e1.innerHTML = `
                            <th colspan="2">Research Papers Published in National Journals : ${non_intern}</th>
                            `;
                            table_faculty_body.appendChild(e);
                            table_faculty_body.appendChild(e1);
                        }
                    });

                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");
                        e.innerHTML = `<td>${index + 1})</td>
                              <td>${item.paper_published}, Is International?: ${
              item.is_international
            } at 
                              ${month_year(item.paper_published_at)}</td>`;
                        table_faculty_body.appendChild(e);
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

// ------------------------------------------------ Papers Presented in Programmes  ------------------------------------------------ //
function setup_papers_presented() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_5/papers_presented.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");

                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    element.innerHTML = `<th colspan="2">Papers Presented in Programmes</th>`;

                    table_faculty_body.appendChild(element);

                    let intern = 0;
                    let non_intern = 0;
                    // create list
                    got.forEach((item, index, array) => {
                        if (item.is_international == "yes") {
                            intern += 1;
                        } else {
                            non_intern += 1;
                        }

                        if (index + 1 == array.length) {
                            // append list to main element
                            const e = document.createElement("tr");
                            const e1 = document.createElement("tr");

                            e.innerHTML = `
                              <th colspan="2">Research Papers Presented in International Programmes : ${intern}</th>
                              `;
                            e1.innerHTML = `
                              <th colspan="2">Research Papers Presented in National Programmes : ${non_intern}</th>
                              `;
                            table_faculty_body.appendChild(e);
                            table_faculty_body.appendChild(e1);
                        }
                    });

                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");
                        e.innerHTML = `<td>${index + 1})</td>
                                <td>${
                                  item.paper_presented
                                }, Is International?: ${
              item.is_international
            } at ${month_year(item.paper_presented_at)}</td>`;
                        table_faculty_body.appendChild(e);
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

// ------------------------------------------------ Books Published  ------------------------------------------------ //
function setup_books_published() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_5/books_published.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");

                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    // append list to main element
                    element.innerHTML = `
                            <th colspan="2">Books Published</th>
                            `;
                    table_faculty_body.appendChild(element);

                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");
                        e.innerHTML = `<td>${index + 1})</td>
                              <td>${item.title} - ${
              item.description
            } (${month_year(item.published_at)})</td>`;
                        table_faculty_body.appendChild(e);
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

// ------------------------------------------------ Sponsored Projects Completed  ------------------------------------------------ //
function setup_sponsored_projs() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_6/sponsored_projects_completed.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");

                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    // append list to main element
                    element.innerHTML = `
                            <th colspan="2">Sponsored Projects Completed</th>
                            `;
                    table_faculty_body.appendChild(element);

                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");
                        e.innerHTML = `<td>${index + 1})</td>
                              <td>${item.project} (${month_year(
              item.project_from
            )} - ${month_year(item.project_to)}). Project Cost: ${
              item.project_cost
            }</td>`;
                        table_faculty_body.appendChild(e);
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

// ------------------------------------------------ Programme Chaired  ------------------------------------------------ //
function setup_programme_chaired() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_5/programme_chaired.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");
                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    // append list to main element
                    element.innerHTML = `
                              <th colspan="2">Programme Chaired</th>
                              `;
                    table_faculty_body.appendChild(element);

                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");
                        e.innerHTML = `<td>${index + 1})</td>
                                <td>${item.programme_chaired} from ${month_year(
              item.programme_chaired_from
            )} - ${month_year(item.programme_chaired_to)}</td>`;
                        table_faculty_body.appendChild(e);
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

// ------------------------------------------------ Programme Organized  ------------------------------------------------ //
function setup_programme_organized() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_5/programme_organized.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");

                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    // append list to main element
                    element.innerHTML = `
                              <th colspan="2">Programme Organized</th>
                              `;
                    table_faculty_body.appendChild(element);

                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");
                        e.innerHTML = `<td>${index + 1})</td>
                                <td>${
                                  item.programme_organized
                                } from ${month_year(
              item.programme_organized_from
            )} - ${month_year(item.programme_organized_to)}</td>`;
                        table_faculty_body.appendChild(e);
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

// ------------------------------------------------ Programme Attended  ------------------------------------------------ //
function setup_programme_attended() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_5/programme_attended.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");

                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    // append list to main element
                    element.innerHTML = `
                              <th colspan="2">Programme Attended</th>
                              `;
                    table_faculty_body.appendChild(element);

                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");
                        e.innerHTML = `<td>${index + 1})</td>
                                <td>${
                                  item.programme_attended
                                } from ${month_year(
              item.programme_attended_from
            )} - ${month_year(item.programme_attended_to)}</td>`;
                        table_faculty_body.appendChild(e);
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

// ------------------------------------------------ Special Reprasentations  ------------------------------------------------ //
function setup_special_representations() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_5/special_representations.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");

                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    // append list to main element
                    element.innerHTML = `
                              <th colspan="2">Special Reprasentations</th>
                              `;
                    table_faculty_body.appendChild(element);

                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");
                        e.innerHTML = `<td>${index + 1})</td>
                                <td>${
                                  item.special_representation
                                } from ${month_year(
              item.special_representation_from
            )} - ${month_year(item.special_representation_to)}</td>`;
                        table_faculty_body.appendChild(e);
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

// ------------------------------------------------ Honours  ------------------------------------------------ //
function setup_honours() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_4/honors.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");

                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    // append list to main element
                    element.innerHTML = `
                              <th colspan="2">Honours</th>
                              `;
                    table_faculty_body.appendChild(element);

                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");
                        e.innerHTML = `<td>${index + 1})</td>
                                <td>${item.honor} at ${month_year(
              item.honored_at
            )}</td>`;
                        table_faculty_body.appendChild(e);
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

// ------------------------------------------------ Patents  ------------------------------------------------ //
function setup_patents() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_5/patents.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");

                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    // append list to main element
                    element.innerHTML = `
                              <th colspan="2">Patents Field</th>
                              `;
                    table_faculty_body.appendChild(element);

                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");
                        e.innerHTML = `<td>${index + 1})</td>
                                <td>${item.patent}. File Number: ${
              item.file_number
            } patent at ${month_year(item.patent_at)}</td>`;
                        table_faculty_body.appendChild(e);
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

// ------------------------------------------------ Experience Abroad  ------------------------------------------------ //
function setup_exp_abroad() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_6/exp_abroad.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");
                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    // append list to main element
                    element.innerHTML = `
                              <th colspan="2">Experience Abroad</th>
                              `;
                    table_faculty_body.appendChild(element);

                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");
                        e.innerHTML = `<td>${index + 1})</td>
                                <td>${item.exp_abroad} from ${
              item.exp_abroad_from
            } to ${month_year(item.exp_abroad_to)}
                                Purpose of visit: ${
                                  item.purpose_of_visit
                                }</td>`;
                        table_faculty_body.appendChild(e);
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

// ------------------------------------------------ Invited Lectures  ------------------------------------------------ //
function setup_invited_lectures() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_4/invited_lectures.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");

                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    // append list to main element
                    element.innerHTML = `
                              <th colspan="2">Invited Lectures</th>
                              `;
                    table_faculty_body.appendChild(element);

                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");
                        e.innerHTML = `<td>${index + 1})</td>
                                <td>${item.invited_lecture} at ${month_year(
              item.invited_lecture_at
            )}</td>`;
                        table_faculty_body.appendChild(e);
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

// ------------------------------------------------ Extension & Outreach Programme  ------------------------------------------------ //
function setup_extension_outreach() {
    return new Promise((resolve, reject) => {
        // get faculty id from local storage
        const id = get_faculty_id();

        const xhr = new XMLHttpRequest();

        xhr.open(
            "GET",
            `../../api/profile/public/type_6/extension_outreach.php?ID=${id}`,
            true
        );

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                const got = JSON.parse(xhr.responseText);

                if (got.error) {
                    // if can't get the data, thorw an error
                    reject(window.alert(got.error));
                } else {
                    // create a main element and list element
                    const element = document.createElement("tr");

                    // check for empty array
                    if (got.length == 0) {
                        resolve();
                        return;
                    }

                    // append list to main element
                    element.innerHTML = `
                              <th colspan="2">Extension & Outreach Programme</th>
                              `;
                    table_faculty_body.appendChild(element);

                    // create list
                    got.forEach((item, index, array) => {
                        const e = document.createElement("tr");
                        e.innerHTML = `<td>${index + 1})</td>
                                <td>${
                                  item.extension_outreach
                                } during ${month_year(
              item.extension_outreach_from
            )} and ${month_year(item.extension_outreach_to)}
                                . No. of participants: ${
                                  item.number_of_participants
                                }</td>`;
                        table_faculty_body.appendChild(e);
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

function generate_jsPdf() {
    return new Promise((resolve, reject) => {
        doc.autoTable({
            html: "#table_faculty",
            theme: "grid",
            bodyStyles: { lineColor: [255, 255, 255] },
            startY: 60,
            didDrawPage: function(data) {

                    // Header
                    doc.setFontSize(10);
                    doc.setTextColor(40);
                    doc.text(195, 7, `${faculty_name}`, { align: 'right' }).setFont(undefined, 'bold');
                    // doc.line(1, 12, 4, 4);
                    doc.text(195, 11, `${faculty_dept}`, { align: 'right' }).setFont(undefined, 'bold');

                    // Footer
                    var str = "Page " + doc.internal.getNumberOfPages();

                    doc.setFontSize(10);

                    // jsPDF 1.4+ uses getWidth, <1.4 uses .width
                    var pageSize = doc.internal.pageSize;
                    var pageHeight = pageSize.height ?
                        pageSize.height :
                        pageSize.getHeight();
                    doc.text(str, data.settings.margin.left, pageHeight - 2);
                    doc.setDrawColor("#000000");
                    doc.rect(10, 13, doc.internal.pageSize.width - 20, doc.internal.pageSize.height - 19, 'S');
                    doc.rect(11, 14, doc.internal.pageSize.width - 22, doc.internal.pageSize.height - 21, 'S');
                }
                // headStyles: {
                //   valign: "middle",
                //   halign: "center",
                //   fillColor: [255, 255, 255],
                //   textColor: [0, 0, 0],
                // },
                
        });
        // border
        // doc.rect(20, 20, doc.internal.pageSize.width - 40, doc.internal.pageSize.height - 40, 'S');

        resolve(doc.save("faculty_aurct.pdf"));
    });
}

// Initially
window.addEventListener("DOMContentLoaded", async() => {
    await setup_photo()
        .then(() => setup_user())
        .then(() => setup_user_info())

    .then(() => setup_present_add_respo())
        .then(() => setup_previous_positions())
        .then(() => setup_previous_add_respo())
        .then(() => setup_other_employment())
        .then(() => setup_research_degree())
        .then(() => setup_area_of_spec())
        .then(() => setup_membership())
        .then(() => setup_papers_published())
        .then(() => setup_papers_presented())
        .then(() => setup_books_published())
        .then(() => setup_sponsored_projs())
        .then(() => setup_programme_chaired())
        .then(() => setup_programme_organized())
        .then(() => setup_programme_attended())
        .then(() => setup_special_representations())
        .then(() => setup_honours())
        .then(() => setup_patents())
        .then(() => setup_exp_abroad())
        .then(() => setup_invited_lectures())
        .then(() => setup_extension_outreach())
        .then(() => generate_jsPdf())
        .then(() => window.location.replace("./profile_preview.html"));
});