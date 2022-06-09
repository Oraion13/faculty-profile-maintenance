const generate_pdf = document.getElementById("generate_pdf");

generate_pdf.addEventListener("click", () => {
  html2pdf(faculty_profile_container);
});
