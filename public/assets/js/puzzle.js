document.addEventListener("DOMContentLoaded", function () {
    const openLetterBtn = document.getElementById("open-letter-btn");
    const letterContent = document.getElementById("letterContent");

    if (openLetterBtn && letterContent) {
        openLetterBtn.addEventListener("click", function () {
            letterContent.classList.toggle("open");

            // Change le texte du bouton selon l'état
            if (letterContent.classList.contains("open")) {
                openLetterBtn.textContent = "Fermer la lettre";
            } else {
                openLetterBtn.textContent = "Ouvrir la lettre";
            }
        });
    }
});

// Vanilla, no framework — juste UX
const $ = s => document.querySelector(s);

const story = $("#story");
if (story) {
    const full = story.textContent;
    story.textContent = "";
    let i = 0, speed = 20;
    (function tick()
    {
        if (i <= full.length) {
            story.textContent = full.slice(0, i++);
            requestAnimationFrame(() => setTimeout(tick, speed));
        }
    })();
}

const butterfly = document.getElementById("ui-butterfly");
const intro = document.getElementById("intro-note");
if (butterfly) {
    butterfly.addEventListener("click", (e) => {
        e.preventDefault();
        if (intro) {
            intro.hidden = false;
        }
        window.location.href = "?m=papillon&a=epreuve";
    });
    butterfly.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
            e.preventDefault(); butterfly.click(); }
    });
}

// accessibilité
const h1 = document.querySelector("h1.heading");
if (h1) {
    h1.setAttribute("tabindex","-1"); h1.focus({preventScroll:false}); }

// reduce motion
if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
    document.querySelectorAll(".wing-l,.wing-r,.float").forEach(el => el.style.animation = "none");
}

