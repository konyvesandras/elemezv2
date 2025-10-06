document.addEventListener("DOMContentLoaded", function () {
  const gomb = document.getElementById("modvalto");
  if (!gomb) return;

  gomb.addEventListener("click", function () {
    document.body.classList.toggle("dark-mode");
    document.body.classList.toggle("light-mode");
    localStorage.setItem("tema", document.body.classList.contains("dark-mode") ? "dark" : "light");
  });

  const mentettTema = localStorage.getItem("tema");
  if (mentettTema === "dark") {
    document.body.classList.add("dark-mode");
  } else {
    document.body.classList.add("light-mode");
  }
});
