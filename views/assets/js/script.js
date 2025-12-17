document.addEventListener("DOMContentLoaded", function() {
  const box = document.querySelector(".box");
  if (box) {
    // animate in on page load
    box.classList.add("animate-in");
  }

  // attach click animation for links
  const links = document.querySelectorAll(".link-transition");
  links.forEach(link => {
    link.addEventListener("click", function(e) {
      e.preventDefault();
      box.classList.add("animate-out");
      setTimeout(() => {
        window.location.href = link.href;
      }, 300); // match animation duration
    });
  });
});
