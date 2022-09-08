

const hamburger = document.querySelector(".hamburger");
const navLinks = document.querySelector(".links");
const links = document.querySelectorAll(".links a");

hamburger.addEventListener("click", () => {
    navLinks.classList.toggle("open");
  links.forEach(link => {
    link.classList.toggle("fade");
  });
});

