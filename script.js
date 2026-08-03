// ============ 0. Theme toggle ============
const themeToggle = document.getElementById('themeToggle');
const htmlElement = document.documentElement;
const savedTheme = localStorage.getItem('theme') || 'dark';

function initTheme() {
  htmlElement.setAttribute('data-theme', savedTheme);
  updateThemeIcon();
}

function updateThemeIcon() {
  const currentTheme = htmlElement.getAttribute('data-theme');
  themeToggle.textContent = currentTheme === 'dark' ? '☀️' : '🌙';
}

function toggleTheme() {
  const currentTheme = htmlElement.getAttribute('data-theme');
  const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
  htmlElement.setAttribute('data-theme', newTheme);
  localStorage.setItem('theme', newTheme);
  updateThemeIcon();
}

initTheme();
themeToggle.addEventListener('click', toggleTheme);

// ============ 0.5. Scroll progress bar ============
const progressBar = document.createElement('div');
progressBar.className = 'progress-bar';
document.body.insertBefore(progressBar, document.body.firstChild);

window.addEventListener('scroll', () => {
  const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
  const scrolled = (window.scrollY / scrollHeight) * 100;
  progressBar.style.width = scrolled + '%';
});

// ============ 1. Terminal typing effect ============
// Simulasi teks yang diketik otomatis di hero section
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
      setTimeout(typeLoop, 1400); // jeda sebelum menghapus
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

// ============ 2. Scroll reveal with stagger ============
// Section muncul fade-in + slide-up saat masuk viewport (satu kali)
const revealTargets = document.querySelectorAll('.section, .project-card, .skill-card');

revealTargets.forEach(el => {
  el.style.opacity = '0';
  el.style.transform = 'translateY(24px)';
  el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
});

const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '1';
      entry.target.style.transform = 'translateY(0)';
      revealObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.15 });

revealTargets.forEach((el, index) => {
  // Stagger animation - each element delays based on its index
  el.style.transitionDelay = (index % 4) * 0.1 + 's';
  revealObserver.observe(el);
});

// ============ 3. Active nav link on scroll ============
const sections = document.querySelectorAll('section[id]');
const navLinks = document.querySelectorAll('.nav-links a');

function updateActiveNav() {
  let current = '';
  sections.forEach(section => {
    const sectionTop = section.offsetTop - 100;
    if (window.scrollY >= sectionTop) {
      current = section.getAttribute('id');
    }
  });

  navLinks.forEach(link => {
    link.style.color = link.getAttribute('href') === `#${current}`
      ? 'var(--accent)'
      : '';
  });
}

updateActiveNav();
window.addEventListener('scroll', updateActiveNav);

// ============ 4. Header hide/show + hero parallax ============
const heroSchema = document.querySelector('.hero-schema');
let lastScroll = window.scrollY || 0;
let ticking = false;

window.addEventListener('scroll', () => {
  const currentScroll = window.scrollY || 0;

  if (!ticking) {
    window.requestAnimationFrame(() => {
      // header hide on scroll down, show on scroll up (with small offset)
      if (currentScroll > lastScroll && currentScroll > 80) {
        document.body.classList.add('scroll-down');
        document.body.classList.remove('scroll-up');
      } else {
        document.body.classList.add('scroll-up');
        document.body.classList.remove('scroll-down');
      }

      // gentle parallax for hero-schema (moves slower than page scroll)
      if (heroSchema) {
        const offset = Math.max(0, currentScroll) * -0.04; // negative to move up
        heroSchema.style.transform = `translateY(${offset}px)`;
      }

      lastScroll = currentScroll;
      ticking = false;
    });
    ticking = true;
  }
});

// ============ 5. Projects carousel (geser samping + auto-scroll dengan loop) ============
const track = document.getElementById('projectsTrack');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
let autoScrollInterval;
let isAutoScrolling = false;
let isScrolling = false;

function scrollByCard(direction) {
  if (!track || isScrolling) return;
  const card = track.querySelector('.project-card');
  if (!card) return;
  const gap = 16;
  const distance = card.offsetWidth + gap;
  track.scrollBy({ left: direction * distance, behavior: 'smooth' });
  
  // Tandai sedang scroll
  isScrolling = true;
  setTimeout(() => { isScrolling = false; }, 600);
  
  resetAutoScroll();
}

function autoScroll() {
  if (!track || isScrolling) return;
  const card = track.querySelector('.project-card');
  if (!card) return;
  
  const gap = 16;
  const distance = card.offsetWidth + gap;
  const maxScroll = track.scrollWidth - track.clientWidth;
  
  // Jika udah di akhir, kembali ke awal
  if (track.scrollLeft >= maxScroll - 10) {
    isScrolling = true;
    track.scrollTo({ left: 0, behavior: 'smooth' });
    // Tunggu smooth animation selesai sebelum bisa scroll lagi
    setTimeout(() => { isScrolling = false; }, 600);
  } else {
    track.scrollBy({ left: distance, behavior: 'smooth' });
    isScrolling = true;
    setTimeout(() => { isScrolling = false; }, 600);
  }
}

function resetAutoScroll() {
  clearInterval(autoScrollInterval);
  autoScrollInterval = setInterval(autoScroll, 3000);
}

if (prevBtn) prevBtn.addEventListener('click', () => scrollByCard(-1));
if (nextBtn) nextBtn.addEventListener('click', () => scrollByCard(1));

// Pastikan track ada sebelum start auto-scroll
if (track) {
  // Start auto-scroll setelah delay kecil
  setTimeout(() => {
    isAutoScrolling = true;
    resetAutoScroll();
  }, 800);
  
  // Pause saat hover
  track.addEventListener('mouseenter', () => {
    clearInterval(autoScrollInterval);
    isAutoScrolling = false;
  });
  
  // Resume saat mouse leave
  track.addEventListener('mouseleave', () => {
    if (!isAutoScrolling) {
      resetAutoScroll();
      isAutoScrolling = true;
    }
  });
}
