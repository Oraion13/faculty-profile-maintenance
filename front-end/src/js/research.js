const research_form = document.getElementById("research_form");
const phd_guided = document.getElementById("phd_guided");
const phd_guiding = document.getElementById("phd_guiding");
const me_guided = document.getElementById("me_guided");
const me_guiding = document.getElementById("me_guiding");
const ms_guided = document.getElementById("ms_guided");
const ms_guiding = document.getElementById("ms_guiding");
const alert1 = document.querySelector(".alert1");
const previous = document.getElementById("previous");
const next = document.getElementById("next");

// get eqd
const get_user = () => {
  return window.localStorage.getItem("user")
    ? JSON.parse(window.localStorage.getItem("user"))
    : [];
};

// submit research form
function setup_research() {
  const user = get_user();
  const xhr = new XMLHttpRequest();

  xhr.open(
    "GET",
    `../../api/profile/public/type_8/research_guidance.php?ID=${user.user_id}`,
    true
  );

  xhr.onreadystatechange = function () {
    if (xhr.readyState == XMLHttpRequest.DONE) {
      const got = JSON.parse(xhr.responseText);

      if (got.error) {
        display_alert(got.error, "danger");
      } else {
        console.log("got", got);
        if (got.length != 0) {
          let attr = document.createAttribute("data-id");
          attr.value = got[0].research_guidance_id;

          research_form.setAttributeNode(attr);
          phd_guided.value = got[0].phd_guided;
          phd_guiding.value = got[0].phd_guiding;
          me_guided.value = got[0].me_guided;
          me_guiding.value = got[0].me_guiding;
          ms_guided.value = got[0].ms_guided;
          ms_guiding.value = got[0].ms_guiding;
        } else {
          let attr = document.createAttribute("data-id");
          attr.value = 0;

          research_form.setAttributeNode(attr);
        }
      }
    }
  };

  xhr.send();
}

// submit research form
function submit_form(e) {
  const user = get_user();
  e.preventDefault();

  const xhr = new XMLHttpRequest();

  console.log(research_form.dataset.id);

  const research = [
    {
      research_guidance_id: Number(research_form.dataset.id),
      phd_guided: phd_guided.value,
      phd_guiding: phd_guiding.value,
      me_guided: me_guided.value,
      me_guiding: me_guiding.value,
      ms_guided: ms_guided.value,
      ms_guiding: ms_guiding.value,
    },
  ];

  console.log(research);

  xhr.open(
    "POST",
    `../../api/profile/public/type_8/research_guidance.php?ID=${user.user_id}`,
    true
  );

  xhr.onreadystatechange = function () {
    if (xhr.readyState == XMLHttpRequest.DONE) {
      const got = JSON.parse(xhr.responseText);

      if (got.error) {
        display_alert(got.error, "danger");
      } else {
        window.alert("Membership updated successfully");
        // assign the data
        setup_research();
      }
    }
  };

  xhr.send(JSON.stringify(research));
}

// submit form
research_form.addEventListener("submit", submit_form);
// initally
window.addEventListener("DOMContentLoaded", () => {
  setup_research();
});
// previous button
previous.addEventListener("click", () => {
  window.location.replace("./edq.html");
});
// next button
next.addEventListener("click", () => {
  window.location.replace("./position.html");
});
