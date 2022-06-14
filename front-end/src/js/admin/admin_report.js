const all_section = document.getElementById("all_section");
const from_date = document.getElementById("from_date");
const to_date = document.getElementById("to_date");
const search_report = document.getElementById("search_report");
const table_body = document.getElementById("table_body");
const department = document.getElementById("department");

const urls = {
  "type_4/additional_responsibilities_present.php": ["./responsibility.html", "Additional Responsibilities Present"],
  "type_4/honors.php": ["./honors.html", "Honors"],
  "type_4/invigilation_duties.php": ["./invigilationduties.html", "Invigilation Duties"],
  "type_4/onduty_orders.php": ["./onduty.html", "Onduty Orders"],
  "type_5/additional_responsibilities_prev.php": ["./responsibility.html", "Additional Responsibilities Previous"],
  "type_5/books_published.php": ["./bookpub.html", "Books Published"],
  "type_5/degree.php": ["./edq.html", "Degree"],
  "type_5/other_employment.php": ["./other_employment.html", "Other Employment"],
  "type_5/papers_presented.php": ["./paperpres.html", "Papers Presented"],
  "type_5/papers_published.php": ["./paperpub.html", "Papers Published"],
  "type_5/patents.php": ["./patents.html", "Patents"],
  "type_5/programme_attended.php": ["./progatten.html", "Programme Attended"],
  "type_5/programme_chaired.php": ["./progchair.html", "Programme Chaired"],
  "type_5/programme_organized.php": ["./progorg.html", "Programme Organized"],
  "type_5/special_respresentations.php": ["./specialrep.html", "Special Respresentations"],
  "type_6/exp_abroad.php": ["./experience.html", "Experience Abroad"],
  "type_6/extension_outreach.php": ["./extension.html", "Extension Outreach"],
  "type_6/research_degree.php": ["./research.html", "Research Degree"],
  "type_6/sponsored_projects_completed.php": ["./project.html", "Sponsored Projects Completed"],
  "type_6/incharge_duty_files.php": ["./incharge_duty.html", "Incharge Duty Files"],
};

let overall = [];

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

// view in page
function view_faculty(e) {
  e.preventDefault();

  const element = e.currentTarget.parentElement.parentElement;
  const id = element.dataset.id;

  const xhr = new XMLHttpRequest();

  xhr.open("GET", `../../api/profile/public/type_0/users.php?ID=${id}`, true);

  xhr.onreadystatechange = function () {
    if (xhr.readyState == XMLHttpRequest.DONE) {
      const got = JSON.parse(xhr.responseText);

      if (got.error) {
        // if can't get the data, thorw an error
        window.alert(got.error);
      } else {
        window.localStorage.setItem("user", JSON.stringify(got));
        window.location.replace(`${urls[all_section.value][0]}`);
      }
    }
  };
  xhr.send();
}

// setup the table
const setup_report_table = (got) => {
  table_body.innerHTML = ``;
  console.log(got);
  // header setup
  const elem = document.createElement("tr");
  elem.innerHTML = `
  <th colspan="2">Report for: </th>
  <th colspan="2">${urls[all_section.value][1]}</th>
  `;
  table_body.appendChild(elem);
  got.forEach((item) => {
    const element = document.createElement("tr");

    let attr = document.createAttribute("data-id");
    attr.value = item.user_id;

    element.setAttributeNode(attr);

    element.innerHTML = `
    <td>${item.honorific} ${item.full_name}</td>
    <td>${item.position}</td>
    <td>${item.department}</td>
    <td><button type="button" class="view-btn btn btn-info">
    Details</i>
  </button></td>
    `;

    const view_btn = element.querySelector(".view-btn");
    view_btn.addEventListener("click", view_faculty);

    table_body.appendChild(element);
  });
};

// get from api
function search_report_func(e) {
  e.preventDefault();

  if (all_section.value != "default" && !from_date.value) {
    window.alert("Provide both category and From value!");
    return;
  }

  const xhr = new XMLHttpRequest();

  xhr.open(
    "GET",
    `../../api/profile/public/${all_section.value}?from=${from_date.value}&to=${
      !to_date.value ? 0 : to_date.value
    }`,
    true
  );

  xhr.onreadystatechange = function () {
    if (xhr.readyState == XMLHttpRequest.DONE) {
      const got = JSON.parse(xhr.responseText);

      if (got.error) {
        // if can't get the data, thorw an error
        window.alert(got.error);
      } else {
        console.log(got);
        overall = got;
        setup_report_table(got);
      }
    }
  };
  xhr.send();
}

window.addEventListener("DOMContentLoaded", () => {
  setup_departments();
})

search_report.addEventListener("click", search_report_func);

document.getElementById("generate_report").addEventListener("click", () => {
  if (!window.confirm("Are you sure to generate report?")) {
    return;
  }

  window.jsPDF = window.jspdf.jsPDF;
  const doc = new window.jspdf.jsPDF();
  doc.autoTable({
    html: "#report_table",
    theme: "grid",
    bodyStyles: { lineColor: [0, 0, 0] },
  });

  doc.save("faculty_report.pdf");
});

department.addEventListener("change", () => {
  setup_report_table(
    overall.filter((item) => item.department_id == department.value)
  );
});
