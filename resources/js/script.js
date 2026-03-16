// ── Palavra dinâmica no hero ──────────────────────────────────
document.addEventListener("DOMContentLoaded", () => {
  const words = ["AMBIENTE", "ESTRUTURA", "PROFESSORES", "TREINOS", "RESULTADOS", "MOTIVAÇÃO"];
  const el = document.querySelector("#word");
  if (!el) return;

  let i = 0, timer = null, running = false;

  function renderWord(word) {
    el.innerHTML = "";
    [...word].forEach((ch) => {
      const span = document.createElement("span");
      span.className = "char";
      span.textContent = ch;
      el.appendChild(span);
    });
  }

  renderWord(words[i]);

  if (typeof gsap !== "undefined") {
    gsap.fromTo(
      el.querySelectorAll(".char"),
      { y: 10, opacity: 0 },
      { y: 0, opacity: 1, duration: 0.5, ease: "power2.out" }
    );
  }

  function flipToNext() {
    const currentChars = el.querySelectorAll(".char");
    gsap.to(currentChars, {
      y: -14, rotateX: 90, opacity: 0, duration: 0.28, ease: "power2.in",
      onComplete: () => {
        i = (i + 1) % words.length;
        renderWord(words[i]);
        const nextChars = el.querySelectorAll(".char");
        gsap.set(nextChars, { y: 14, rotateX: -90, opacity: 0 });
        gsap.to(nextChars, { y: 0, rotateX: 0, opacity: 1, duration: 0.34, ease: "power2.out" });
      },
    });
  }

  function start() {
    if (running) return;
    running = true;
    timer = setInterval(flipToNext, 1800);
  }

  function stop() {
    running = false;
    if (timer) clearInterval(timer);
    timer = null;
  }

  setTimeout(start, 1200);

  const hero = document.querySelector("#topo");
  if (hero && "IntersectionObserver" in window) {
    const io = new IntersectionObserver((entries) => {
      const visible = entries[0]?.isIntersecting;
      if (visible) { stop(); setTimeout(start, 700); } else { stop(); }
    }, { threshold: 0.35 });
    io.observe(hero);
  }
});

// ── Animação entrada hero-character ──────────────────────────
document.addEventListener("DOMContentLoaded", () => {
  const img = document.querySelector(".hero-character");
  if (!img || typeof gsap === "undefined") return;
  gsap.fromTo(
    img,
    { opacity: 0, y: 40, scale: 0.96 },
    { opacity: 1, y: 0, scale: 1, duration: 1.0, ease: "power3.out", delay: 0.2 }
  );
});

// ── Contadores animados ───────────────────────────────────────
document.addEventListener("DOMContentLoaded", () => {
  const counters = document.querySelectorAll(".stat-number");

  function formatValue(el, value) {
    const prefix = el.dataset.prefix || "";
    const suffix = el.dataset.suffix || "";
    if (suffix === "/5") {
      const v = (value / 10).toFixed(1).replace(".", ",");
      return `${prefix}${v}${suffix}`;
    }
    return `${prefix}${value.toLocaleString("pt-BR")}${suffix}`;
  }

  function animateCount(el, to, duration = 1200) {
    const startTime = performance.now();
    function tick(now) {
      const progress = Math.min((now - startTime) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = formatValue(el, Math.floor(to * eased));
      if (progress < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  }

  const io = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      const el = entry.target;
      if (el.dataset.done === "1") return;
      el.dataset.done = "1";
      animateCount(el, Number(el.dataset.to || "0"), 1200);
    });
  }, { threshold: 0.4 });

  counters.forEach((c) => io.observe(c));
});

// ── Animações da seção "Sobre nós" ───────────────────────────
document.addEventListener("DOMContentLoaded", () => {
  if (typeof gsap === "undefined" || typeof ScrollTrigger === "undefined") return;
  gsap.registerPlugin(ScrollTrigger);

  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const about = document.querySelector(".about");
  if (!about) return;

  const mainImg = about.querySelector(".about-img-main");
  const video   = about.querySelector(".about-video");
  const offer   = about.querySelector(".offer-card");
  const play    = about.querySelector(".play");
  const text    = about.querySelector(".about-text");

  ScrollTrigger.create({
    trigger: about, start: "top 75%", once: true,
    onEnter: () => {
      if (reduceMotion) return;
      const tl = gsap.timeline({ defaults: { ease: "power3.out" } });
      if (mainImg) tl.from(mainImg, { x: 20, y: 20, opacity: 0, duration: 0.6 }, 0);
      if (video)   tl.from(video,   { x: -20, y: 20, opacity: 0, duration: 0.6 }, 0.1);
      if (offer)   tl.from(offer,   { y: -16, opacity: 0, duration: 0.5 }, 0.15);
      if (text)    tl.from(text,    { x: 24, opacity: 0, duration: 0.6 }, 0.05);
      if (play)    gsap.to(play, { scale: 1.08, duration: 0.9, ease: "sine.inOut", yoyo: true, repeat: -1 });
      if (offer)   gsap.to(offer, { y: -4, duration: 1.6, ease: "sine.inOut", yoyo: true, repeat: -1 });
    }
  });
});

// ── Modal de vídeo ────────────────────────────────────────────
document.addEventListener("DOMContentLoaded", () => {
  const openBtn = document.querySelector("#openVideo");
  const modal   = document.querySelector("#videoModal");
  const frame   = document.querySelector("#videoFrame");
  if (!openBtn || !modal || !frame) return;

  function openModal() {
    frame.src = openBtn.dataset.video;
    modal.classList.add("is-open");
    modal.setAttribute("aria-hidden", "false");
    document.body.style.overflow = "hidden";
  }
  function closeModal() {
    modal.classList.remove("is-open");
    modal.setAttribute("aria-hidden", "true");
    frame.src = "";
    document.body.style.overflow = "";
  }

  openBtn.addEventListener("click", openModal);
  modal.addEventListener("click", (e) => { if (e.target.matches("[data-close]")) closeModal(); });
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && modal.classList.contains("is-open")) closeModal();
  });
});

// ── Modal Explorar — navegação por slides ────────────────────
document.addEventListener("DOMContentLoaded", () => {
  const modal       = document.getElementById("exploreModal");
  const openBtn     = document.getElementById("openExplore");
  const closeBtn    = document.getElementById("closeExplore");
  const track       = document.getElementById("exploreTrack");
  const prevBtn     = document.getElementById("explorePrev");
  const nextBtn     = document.getElementById("exploreNext");
  const dotsWrap    = document.getElementById("exploreDots");
  const progressBar = document.getElementById("exploreProgress");
  const tabs        = document.querySelectorAll(".explore-tab");

  if (!modal || !track) return;

  const TOTAL = track.querySelectorAll(".explore-slide").length;
  let current = 0;

  // Dots
  for (let i = 0; i < TOTAL; i++) {
    const dot = document.createElement("button");
    dot.className = "explore-dot" + (i === 0 ? " active" : "");
    dot.setAttribute("aria-label", `Slide ${i + 1}`);
    dot.addEventListener("click", () => goTo(i));
    dotsWrap.appendChild(dot);
  }

  function goTo(index) {
    current = Math.max(0, Math.min(index, TOTAL - 1));
    track.style.transform = `translateX(-${current * 100}%)`;

    tabs.forEach((t, i) => {
      t.classList.toggle("active", i === current);
      t.setAttribute("aria-selected", i === current);
    });

    dotsWrap.querySelectorAll(".explore-dot").forEach((d, i) =>
      d.classList.toggle("active", i === current)
    );

    progressBar.style.width = ((current + 1) / TOTAL * 100) + "%";
    prevBtn.disabled = current === 0;
    nextBtn.disabled = current === TOTAL - 1;
  }

  tabs.forEach((tab, i) => tab.addEventListener("click", () => goTo(i)));
  prevBtn.addEventListener("click", () => goTo(current - 1));
  nextBtn.addEventListener("click", () => goTo(current + 1));

  track.querySelectorAll(".es-next-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const target = parseInt(btn.dataset.goto, 10);
      if (!isNaN(target)) goTo(target);
    });
  });

  // Swipe touch
  let touchStartX = 0;
  track.addEventListener("touchstart", (e) => { touchStartX = e.touches[0].clientX; }, { passive: true });
  track.addEventListener("touchend", (e) => {
    const diff = touchStartX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 50) diff > 0 ? goTo(current + 1) : goTo(current - 1);
  });

  // Teclado
  document.addEventListener("keydown", (e) => {
    if (!modal.classList.contains("is-open")) return;
    if (e.key === "ArrowRight") goTo(current + 1);
    if (e.key === "ArrowLeft")  goTo(current - 1);
    if (e.key === "Escape")     closeModal();
  });

  function openModal() {
    modal.classList.add("is-open");
    modal.setAttribute("aria-hidden", "false");
    document.body.style.overflow = "hidden";
    goTo(0);
  }
  function closeModal() {
    modal.classList.remove("is-open");
    modal.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
  }

  openBtn.addEventListener("click", openModal);
  closeBtn.addEventListener("click", closeModal);

  modal.querySelectorAll("a[href='#matricula']").forEach((a) => {
    a.addEventListener("click", closeModal);
  });

  goTo(0);
});