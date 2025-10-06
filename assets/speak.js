document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".speak").forEach(function (btn) {
    btn.addEventListener("click", function () {
      const utterance = new SpeechSynthesisUtterance(btn.dataset.text);
      utterance.lang = btn.dataset.lang || "en-US";
      speechSynthesis.speak(utterance);
    });
  });
});
