// const generate_pdf = document.getElementById("generate_pdf");

// generate_pdf.addEventListener("click", () => {
//   html2pdf(faculty_profile_container);
// // });

// generate_pdf.addEventListener("click", () => {
//             window.html2canvas = html2canvas;
//             window.jsPDF = window.jspdf.jsPDF;

//             let doc = new jsPDF();

//             var elementHandler = {
//                 "#ignorePDF": function(element, renderer) {
//                     return true;
//                 },
//             };
//             var source = document.getElementById("faculty_profile_container");
//             doc.fromHTML(source, 15, 15, {
//                 width: 180,
//                 elementHandlers: elementHandler,
//             });

//             doc.output("datauri");


//             });
// const generate_pdf = document.getElementById("generate_pdf");

// generate_pdf.addEventListener("click", () => {
//     // html2pdf(faculty_profile_container);
//     window.location.replace("./preview_profile.html");
// });