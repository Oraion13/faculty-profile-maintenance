const faculty_profile_container = document.getElementById(
  "faculty_profile_container"
);

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

    xhr.onreadystatechange = function () {
      if (xhr.readyState == XMLHttpRequest.DONE) {
        const got = JSON.parse(xhr.responseText);

        if (got.error) {
          // if can't get the data, thorw an error
          reject(window.alert(got.error));
        } else {
          const element = document.createElement("section");

          element.innerHTML = `
                    <p><b>Name : </b>${got.honorific} ${got.full_name}</p>
                    <p><b>E-mail ID : </b>${got.email}</p>
                    `;

          resolve(faculty_profile_container.appendChild(element));
        }
      }
    };
    xhr.send();
  });
}

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

    xhr.onreadystatechange = function () {
      if (xhr.readyState == XMLHttpRequest.DONE) {
        const got = JSON.parse(xhr.responseText);

        if (got.error) {
          // if can't get the data, thorw an error
          reject(window.alert(got.error));
        } else {
          const element = document.createElement("section");
          const element1 = document.createElement("div");

          element.innerHTML = `
                      <p><b>Cellphone : </b>${got.phone}</p>
                      <p><b>Address : </b>${got.address}</p>
                      `;

          element1.innerHTML = `
            <h2>Present Position</h2>
            <p style="padding-left: 1em">${got.position}, ${got.department}, ${
            got.position_present_where
          }
             from ${month_year(got.position_present_from)}</p>
            `;

          faculty_profile_container.appendChild(element);
          resolve(faculty_profile_container.appendChild(element1));
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

    xhr.onreadystatechange = function () {
      if (xhr.readyState == XMLHttpRequest.DONE) {
        const got = JSON.parse(xhr.responseText);

        if (got.error) {
          // if can't get the data, thorw an error
          reject(window.alert(got.error));
        } else {
          // create a main element and list element
          const element = document.createElement("div");
          const list_tag = document.createElement("ul");

          // check for empty array
          if (got.length == 0) {
            resolve();
            return;
          }

          // create list
          got.forEach((item, index, array) => {
            list_tag.innerHTML += `
                      <li style="padding-left: 1em">${
                        item.additional_responsibility_present
                      }
                   from ${month_year(
                     item.additional_responsibility_present_from
                   )}</li>`;

            if (index + 1 == array.length) {
              // append list to main element
              element.innerHTML = `
                    <h2>Present Additional Responsibilities</h2>
                    `;
              element.appendChild(list_tag);
            }
          });

          // finally resolve main element
          resolve(faculty_profile_container.appendChild(element));
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

    xhr.onreadystatechange = function () {
      if (xhr.readyState == XMLHttpRequest.DONE) {
        const got = JSON.parse(xhr.responseText);

        if (got.error) {
          // if can't get the data, thorw an error
          reject(window.alert(got.error));
        } else {
          // create a main element and list element
          const element = document.createElement("div");
          const list_tag = document.createElement("ul");

          // check for empty array
          if (got.length == 0) {
            resolve();
            return;
          }

          // create list
          got.forEach((item, index, array) => {
            list_tag.innerHTML += `
                  <li style="padding-left: 1em">${item.position}, ${
              item.department
            }, ${item.position_prev_where}
               during ${month_year(item.position_prev_from)} and ${month_year(
              item.position_prev_to
            )}</li>`;

            if (index + 1 == array.length) {
              // append list to main element
              element.innerHTML = `
                <h2>Previous Positions</h2>
                `;
              element.appendChild(list_tag);
            }
          });

          // finally resolve main element
          resolve(faculty_profile_container.appendChild(element));
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

    xhr.onreadystatechange = function () {
      if (xhr.readyState == XMLHttpRequest.DONE) {
        const got = JSON.parse(xhr.responseText);

        if (got.error) {
          // if can't get the data, thorw an error
          reject(window.alert(got.error));
        } else {
          // create a main element and list element
          const element = document.createElement("div");
          const list_tag = document.createElement("ul");

          // check for empty array
          if (got.length == 0) {
            resolve();
            return;
          }

          // create list
          got.forEach((item, index, array) => {
            list_tag.innerHTML += `
                    <li style="padding-left: 1em">${
                      item.additional_responsibility_prev
                    }
                 during ${month_year(
                   item.additional_responsibility_prev_from
                 )} and ${month_year(
              item.additional_responsibility_prev_to
            )}</li>`;

            if (index + 1 == array.length) {
              // append list to main element
              element.innerHTML = `
                  <h2>Previous Additional Responsibilities</h2>
                  `;
              element.appendChild(list_tag);
            }
          });

          // finally resolve main element
          resolve(faculty_profile_container.appendChild(element));
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

    xhr.onreadystatechange = function () {
      if (xhr.readyState == XMLHttpRequest.DONE) {
        const got = JSON.parse(xhr.responseText);

        if (got.error) {
          // if can't get the data, thorw an error
          reject(window.alert(got.error));
        } else {
          // create a main element and list element
          const element = document.createElement("div");
          const list_tag = document.createElement("ul");

          // check for empty array
          if (got.length == 0) {
            resolve();
            return;
          }

          // create list
          got.forEach((item, index, array) => {
            list_tag.innerHTML += `
                      <li style="padding-left: 1em">${item.other_employment}
                   during ${month_year(
                     item.other_employment_from
                   )} and ${month_year(item.other_employment_to)}</li>`;

            if (index + 1 == array.length) {
              // append list to main element
              element.innerHTML = `
                    <h2>Other Employment</h2>
                    `;
              element.appendChild(list_tag);
            }
          });

          // finally resolve main element
          resolve(faculty_profile_container.appendChild(element));
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

    xhr.onreadystatechange = function () {
      if (xhr.readyState == XMLHttpRequest.DONE) {
        const got = JSON.parse(xhr.responseText);

        if (got.error) {
          // if can't get the data, thorw an error
          reject(window.alert(got.error));
        } else {
          // create a main element and list element
          const element = document.createElement("div");
          const list_tag = document.createElement("ul");

          // check for empty array
          if (got.length == 0) {
            resolve();
            return;
          }

          // create list
          got.forEach((item, index, array) => {
            list_tag.innerHTML += `
                        <li style="padding-left: 1em">${item.degree}
                     (${month_year(item.degree_from)} and ${month_year(
              item.degree_to
            )})</li>`;

            if (index + 1 == array.length) {
              // append list to main element
              element.innerHTML = `
                      <h2>Degree</h2>
                      `;
              element.appendChild(list_tag);
            }
          });

          // finally resolve main element
          resolve(faculty_profile_container.appendChild(element));
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

    xhr.onreadystatechange = function () {
      if (xhr.readyState == XMLHttpRequest.DONE) {
        const got = JSON.parse(xhr.responseText);

        if (got.error) {
          // if can't get the data, thorw an error
          reject(window.alert(got.error));
        } else {
          // create a main element and list element
          const element = document.createElement("div");
          const list_tag = document.createElement("ul");

          // check for empty array
          if (got.length == 0) {
            resolve();
            return;
          }

          // create list
          got.forEach((item, index, array) => {
            list_tag.innerHTML += `
                          <li style="padding-left: 1em">${item.research_degree}
                       (${month_year(
                         item.research_degree_from
                       )} and ${month_year(
              item.research_degree_to
            )}) </br> title: ${item.title}</li>`;

            if (index + 1 == array.length) {
              // append list to main element
              element.innerHTML = `
                        <h2>Research Degree</h2>
                        `;
              element.appendChild(list_tag);
            }
          });

          // finally resolve main element
          resolve(faculty_profile_container.appendChild(element));
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

    xhr.onreadystatechange = function () {
      if (xhr.readyState == XMLHttpRequest.DONE) {
        const got = JSON.parse(xhr.responseText);

        if (got.error) {
          // if can't get the data, thorw an error
          reject(window.alert(got.error));
        } else {
          // create a main element and list element
          const element = document.createElement("div");
          const list_tag = document.createElement("ul");

          // check for empty array
          if (got.length == 0) {
            resolve();
            return;
          }

          // create list
          got.forEach((item, index, array) => {
            list_tag.innerHTML += `
                          <li style="padding-left: 1em">${item.specialization}</li>`;

            if (index + 1 == array.length) {
              // append list to main element
              element.innerHTML = `
                        <h2>Area of Specialization</h2>
                        `;
              element.appendChild(list_tag);
            }
          });

          // finally resolve main element
          resolve(faculty_profile_container.appendChild(element));
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

    xhr.onreadystatechange = function () {
      if (xhr.readyState == XMLHttpRequest.DONE) {
        const got = JSON.parse(xhr.responseText);

        if (got.error) {
          // if can't get the data, thorw an error
          reject(window.alert(got.error));
        } else {
          // create a main element and list element
          const element = document.createElement("div");
          const list_tag = document.createElement("ul");

          // check for empty array
          if (got.length == 0) {
            resolve();
            return;
          }

          // create list
          got.forEach((item, index, array) => {
            list_tag.innerHTML += `
                          <li style="padding-left: 1em">${item.membership}</li>`;

            if (index + 1 == array.length) {
              // append list to main element
              element.innerHTML = `
                        <h2>Membership in Professional Organization</h2>
                        `;
              element.appendChild(list_tag);
            }
          });

          // finally resolve main element
          resolve(faculty_profile_container.appendChild(element));
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

    xhr.onreadystatechange = function () {
      if (xhr.readyState == XMLHttpRequest.DONE) {
        const got = JSON.parse(xhr.responseText);

        if (got.error) {
          // if can't get the data, thorw an error
          reject(window.alert(got.error));
        } else {
          // create a main element and list element
          const element = document.createElement("div");

          // check for empty array
          if (got.length == 0) {
            resolve();
            return;
          }

          // create list

          // append list to main element
          element.innerHTML = `
                          <h2>Membership in Professional Organization</h2>
                          Number of Ph.D Scholars Guided : ${got[0].phd_guided} <br>
                            Number of Ph.D Scholars Guiding : ${got[0].phd_guiding} <br>
                            Number of M.E./ M.Tech. Projects Guided : ${got[0].me_guided} <br>
                            Number of M.E./ M.Tech. Projects Guiding : ${got[0].me_guiding} <br>
                            Number of M.S (By Research) Students Guided : ${got[0].ms_guided} <br>
                            Number of M.S (By Research) Students Guiding : ${got[0].ms_guiding}
                          `;

          // finally resolve main element
          resolve(faculty_profile_container.appendChild(element));
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

    xhr.onreadystatechange = function () {
      if (xhr.readyState == XMLHttpRequest.DONE) {
        const got = JSON.parse(xhr.responseText);

        if (got.error) {
          // if can't get the data, thorw an error
          reject(window.alert(got.error));
        } else {
          // create a main element and list element
          const element = document.createElement("div");
          const list_tag = document.createElement("ul");

          // check for empty array
          if (got.length == 0) {
            resolve();
            return;
          }

          let intern = 0;
          let non_intern = 0;
          // create list
          got.forEach((item, index, array) => {
            if (item.is_international == "yes") {
              intern += 1;
            } else {
              non_intern += 1;
            }

            list_tag.innerHTML += `
                            <li style="padding-left: 1em">${item.paper_published}, Is International?: ${item.is_international} at 
                            ${item.paper_published_at}</li>`;

            if (index + 1 == array.length) {
              // append list to main element
              element.innerHTML = `
                          <h2>Papers Published</h2>
                          <p style="padding-left: 1em">Research Papers Published in International Journals : ${intern}</p>
                          <p style="padding-left: 1em">Research Papers Published in National Journals : ${non_intern}</p>
                          `;
              element.appendChild(list_tag);
            }
          });

          // finally resolve main element
          resolve(faculty_profile_container.appendChild(element));
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
  
      xhr.onreadystatechange = function () {
        if (xhr.readyState == XMLHttpRequest.DONE) {
          const got = JSON.parse(xhr.responseText);
  
          if (got.error) {
            // if can't get the data, thorw an error
            reject(window.alert(got.error));
          } else {
            // create a main element and list element
            const element = document.createElement("div");
            const list_tag = document.createElement("ul");
  
            // check for empty array
            if (got.length == 0) {
              resolve();
              return;
            }
  
            let intern = 0;
            let non_intern = 0;
            // create list
            got.forEach((item, index, array) => {
              if (item.is_international == "yes") {
                intern += 1;
              } else {
                non_intern += 1;
              }
  
              list_tag.innerHTML += `
                              <li style="padding-left: 1em">${item.paper_presented}, Is International?: ${item.is_international} at 
                              ${item.paper_presented_at}</li>`;
  
              if (index + 1 == array.length) {
                // append list to main element
                element.innerHTML = `
                            <h2>Papers Presented in Programmes</h2>
                            <p style="padding-left: 1em">Research Papers Presented in International Programmes : ${intern}</p>
                            <p style="padding-left: 1em">Research Papers Presented in National Programmes : ${non_intern}</p>
                            `;
                element.appendChild(list_tag);
              }
            });
  
            // finally resolve main element
            resolve(faculty_profile_container.appendChild(element));
          }
        }
      };
      xhr.send();
    });
  }
  

// Initially
window.addEventListener("DOMContentLoaded", async () => {
  await setup_user()
    .then(() => setup_user_info())
    .then(() => setup_present_add_respo())
    .then(() => setup_previous_positions())
    .then(() => setup_previous_add_respo())
    .then(() => setup_other_employment())
    .then(() => setup_research_degree())
    .then(() => setup_area_of_spec())
    .then(() => setup_membership())
    .then(() => setup_papers_published())
    .then(() => setup_papers_presented());
});
