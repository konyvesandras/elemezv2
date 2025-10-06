document.addEventListener("DOMContentLoaded", function () {
  const frissitesGomb = document.getElementById("frissites");
  if (frissitesGomb) {
    frissitesGomb.addEventListener("click", function () {
      location.reload(); // 🔁 újratölti az oldalt
    });
  }
});
