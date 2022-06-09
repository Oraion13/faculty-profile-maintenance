const faculty_infos = document.getElementById("faculty_infos");
const table_body = document.getElementById("table_body");

// view faculty in pdf
function view_faculty(e){
    e.preventDefault();

    const element = e.currentTarget.parentElement.parentElement;
    const id = element.dataset.id;

    window.localStorage.setItem("faculty_id", id)
    window.location.replace("./profile_preview.html");
}

// append faculty to the table
const append_table_body = (user_id, honorific, full_name, position) => {
  const element = document.createElement("tr");

  let attr = document.createAttribute("data-id");
  attr.value = user_id;

  element.setAttributeNode(attr);

  element.innerHTML = `
    <td>${honorific} ${full_name}</td>
    <td>${position}</td>
    <td><button type="button" class="view-btn btn btn-info">
    View</i>
  </button></td>
    `;

  const view_btn = element.querySelector(".view-btn");
  view_btn.addEventListener("click", view_faculty);

  table_body.appendChild(element);
};

// get faculties by department
departments.addEventListener("change", () => {
  if (isNaN(Number(departments.value))) {
    return;
  }
  // get faculties by department
  const xhr = new XMLHttpRequest();

  xhr.open(
    "GET",
    `../../api/profile/public/type_0/user_info.php?dept=${departments.value}`,
    true
  );

  xhr.onreadystatechange = function () {
    if (xhr.readyState == XMLHttpRequest.DONE) {
      const got = JSON.parse(xhr.responseText);

      if (got.error) {
        window.alert(got.error);
      } else {
        // console.log(got);
        table_body.innerHTML = "";
        got.forEach((element) => {
          append_table_body(
            element.user_id,
            element.honorific,
            element.full_name,
            element.position
          );
        });
      }
    }
  };
  xhr.send();
});
