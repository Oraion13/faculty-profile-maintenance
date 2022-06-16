const departments = document.getElementById("departments");

// Initally setup select tag - departments
window.addEventListener("DOMContentLoaded", () => {
  // get departments
  const xhr = new XMLHttpRequest();

  xhr.open("GET", `./api/profile/public/type_2/departments.php`, true);

  xhr.onreadystatechange = function () {
    if (xhr.readyState == XMLHttpRequest.DONE) {
      const got = JSON.parse(xhr.responseText);

      if (got.error) {
        window.alert(got.error);
      } else {
        got.forEach((element) => {
          append_department(element.department_id, element.department);
        });
      }
    }
  };
  xhr.send();
});

// append a child element in the document list
const append_department = (id, value) => {
  const element = document.createElement("option");
  let attr = document.createAttribute("value");
  attr.value = id;
  element.setAttributeNode(attr);
  element.innerHTML = `${value}`;

  departments.appendChild(element);
};
