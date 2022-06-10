const all_section = document.getElementById("all_section");
const from_date = document.getElementById("from_date");
const to_date = document.getElementById("to_date");
const search_report = document.getElementById("search_report");

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
      }
    }
  };
  xhr.send();
}

search_report.addEventListener("click", search_report_func);
