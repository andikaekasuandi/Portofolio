// ============ 1. Terminal typing effect ============
const typedEl = document.getElementById('typed');

const phrases = [
    'whoami',
    'echo "mahasiswa software engineering"',
    'cat skills.txt'
];

let phraseIndex = 0;
let charIndex = 0;
let deleting = false;

function typeLoop() {

    const current = phrases[phraseIndex];

    if (!deleting) {

        typedEl.textContent = current.slice(0, charIndex + 1);

        charIndex++;

        if (charIndex === current.length) {

            deleting = true;

            setTimeout(typeLoop, 1400);

            return;

        }

    } else {

        typedEl.textContent = current.slice(0, charIndex - 1);

        charIndex--;

        if (charIndex === 0) {

            deleting = false;

            phraseIndex = (phraseIndex + 1) % phrases.length;

        }

    }

    setTimeout(typeLoop, deleting ? 35 : 65);

}

typeLoop();



// ============ 2. Scroll Reveal ============
const revealTargets = document.querySelectorAll('.section,.project-card,.skill-card');

revealTargets.forEach(el => {

    el.style.opacity = "0";
    el.style.transform = "translateY(24px)";
    el.style.transition = ".6s";

});

const revealObserver = new IntersectionObserver(entries => {

    entries.forEach(entry => {

        if(entry.isIntersecting){

            entry.target.style.opacity="1";
            entry.target.style.transform="translateY(0)";
            revealObserver.unobserve(entry.target);

        }

    });

},{threshold:.15});

revealTargets.forEach(el=>revealObserver.observe(el));



// ============ 3. Active Navbar ============
const sections=document.querySelectorAll("section[id]");
const navLinks=document.querySelectorAll(".nav-links a");

function updateActiveNav(){

    let current="";

    sections.forEach(section=>{

        const top=section.offsetTop-100;

        if(window.scrollY>=top){

            current=section.getAttribute("id");

        }

    });

    navLinks.forEach(link=>{

        link.style.color=
        link.getAttribute("href")==="#" + current
        ?"var(--accent)"
        :"";

    });

}

updateActiveNav();

window.addEventListener("scroll",updateActiveNav);



// ============ 4. Header Hide ============
const heroSchema=document.querySelector(".hero-schema");

let lastScroll=window.scrollY;

let ticking=false;

window.addEventListener("scroll",()=>{

    const currentScroll=window.scrollY;

    if(!ticking){

        requestAnimationFrame(()=>{

            if(currentScroll>lastScroll && currentScroll>80){

                document.body.classList.add("scroll-down");
                document.body.classList.remove("scroll-up");

            }else{

                document.body.classList.add("scroll-up");
                document.body.classList.remove("scroll-down");

            }

            if(heroSchema){

                heroSchema.style.transform=
                `translateY(${currentScroll*-0.04}px)`;

            }

            lastScroll=currentScroll;

            ticking=false;

        });

        ticking=true;

    }

});



// ============ 5. Drag Carousel ============
const slider=document.getElementById("projectsTrack");

if(slider){

let isDown=false;

let startX;

let scrollLeft;

slider.addEventListener("mousedown",(e)=>{

    isDown=true;

    slider.classList.add("active");

    startX=e.pageX-slider.offsetLeft;

    scrollLeft=slider.scrollLeft;

});

slider.addEventListener("mouseleave",()=>{

    isDown=false;

});

slider.addEventListener("mouseup",()=>{

    isDown=false;

});

slider.addEventListener("mousemove",(e)=>{

    if(!isDown) return;

    e.preventDefault();

    const x=e.pageX-slider.offsetLeft;

    const walk=(x-startX)*2;

    slider.scrollLeft=scrollLeft-walk;

});

}