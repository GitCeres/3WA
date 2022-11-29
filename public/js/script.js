const hamburgerToggler = document.querySelector(".hamburger");
const navLinksContainerSlide = document.querySelector(".navlinks-container-slide");
const shadowBody = document.getElementById("shadow");

// Affiche ou cache la side bar et le fond sombre au format mobile
function toggleNav() {
    hamburgerToggler.classList.toggle("open");
    navLinksContainerSlide.classList.toggle("open");
    shadowBody.classList.toggle("activate")
}

hamburgerToggler.addEventListener("click", toggleNav);

// Vérifie la taille de la fenêtre et en fonction ajoute ou non la transition
new ResizeObserver(entries => {
    if (entries[0].contentRect.width <= 768) {
        navLinksContainerSlide.style.transition = "transform 0.3s ease-out"
    } else {
        navLinksContainerSlide.style.transition = "none"
    }
}).observe(document.body)