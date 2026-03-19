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
// ── Dados dos instrutores ─────────────────────────────────────
const INSTRUTORES = [
  {
    initials: "RB",
    avatarColor: "#d61532",
    kicker: "MUSCULAÇÃO · CREF 001234",
    name: "Ricardo Borges",
    role: "Especialista em Hipertrofia & Força",
    bio: "Com mais de 10 anos de experiência, Ricardo é referência em programação de treinos para hipertrofia e força máxima. Já acompanhou atletas amadores e profissionais, com metodologia própria baseada em periodização científica.",
    tags: ["Hipertrofia", "Força Máxima", "Periodização", "Reabilitação"],
    stat1: { value: "10+", label: "Anos de exp." },
    stat2: { value: "320+", label: "Alunos formados" },
    stat3: { value: "CREF", label: "001234-G/CE" },
  },
  {
    initials: "CA",
    avatarColor: "#1a1a1a",
    kicker: "SPINNING & FUNCIONAL · CREF 005678",
    name: "Carla Almeida",
    role: "Referência em Cardio de Alta Intensidade",
    bio: "Carla transformou suas aulas em experiências únicas de energia e superação. Certificada internacionalmente em Spinning e Treinamento Funcional, combina ciência e motivação para resultados rápidos e seguros.",
    tags: ["Spinning", "HIIT", "Funcional", "Emagrecimento"],
    stat1: { value: "8+", label: "Anos de exp." },
    stat2: { value: "250+", label: "Alunos ativos" },
    stat3: { value: "CREF", label: "005678-G/CE" },
  },
  {
    initials: "MF",
    avatarColor: "#d61532",
    kicker: "CROSS TRAINING · CREF 009012",
    name: "Marcos Freitas",
    role: "Performance & Calistenia",
    bio: "Marcos é apaixonado por superar limites. Seus circuitos de Cross Training desenvolvem força, resistência e agilidade de forma progressiva e segura, do iniciante ao atleta avançado.",
    tags: ["Cross Training", "Calistenia", "Performance", "Força"],
    stat1: { value: "7+", label: "Anos de exp." },
    stat2: { value: "180+", label: "Alunos formados" },
    stat3: { value: "CREF", label: "009012-G/CE" },
  },
  {
    initials: "JN",
    avatarColor: "#1a1a1a",
    kicker: "YOGA & PILATES · CREF 003456",
    name: "Juliana Neves",
    role: "Equilíbrio Mente-Corpo",
    bio: "Formada com certificação internacional em Hatha e Vinyasa Yoga, Juliana guia seus alunos em uma jornada de consciência corporal, flexibilidade e equilíbrio emocional que vai além da academia.",
    tags: ["Yoga", "Pilates", "Flexibilidade", "Meditação"],
    stat1: { value: "9+", label: "Anos de exp." },
    stat2: { value: "200+", label: "Alunos guiados" },
    stat3: { value: "CREF", label: "003456-G/CE" },
  },
  {
    initials: "FS",
    avatarColor: "#d61532",
    kicker: "ZUMBA & DANÇA · CREF 007890",
    name: "Felipe Santos",
    role: "Cardio que Parece Festa",
    bio: "Felipe transforma o cardio em diversão pura. Suas aulas de Zumba são sempre lotadas e cheias de ritmo. Com formação em dança e educação física, une técnica e entretenimento de forma contagiante.",
    tags: ["Zumba", "Dança", "Cardio", "Ritmo"],
    stat1: { value: "6+", label: "Anos de exp." },
    stat2: { value: "150+", label: "Alunos ativos" },
    stat3: { value: "CREF", label: "007890-G/CE" },
  },
  {
    initials: "AL",
    avatarColor: "#1a1a1a",
    kicker: "PERSONAL TRAINER · CREF 002345",
    name: "Ana Lima",
    role: "Emagrecimento & Condicionamento",
    bio: "Ana possui um olhar cuidadoso e individualizado para cada aluno. Especializada em emagrecimento saudável e melhora do condicionamento físico, acompanha de perto cada etapa da evolução dos seus clientes.",
    tags: ["Personal", "Emagrecimento", "Condicionamento", "Saúde"],
    stat1: { value: "8+", label: "Anos de exp." },
    stat2: { value: "270+", label: "Alunos formados" },
    stat3: { value: "CREF", label: "002345-G/CE" },
  },
  {
    initials: "BM",
    avatarColor: "#d61532",
    kicker: "MUSCULAÇÃO · CREF 011234",
    name: "Bruno Martins",
    role: "Força & Powerlifting",
    bio: "Bruno é especialista em levantamento de peso e treino de força máxima. Com passagem por competições de powerlifting, traz uma abordagem técnica e segura para quem busca resultados sólidos e duradouros.",
    tags: ["Powerlifting", "Força", "Musculação", "Técnica"],
    stat1: { value: "9+", label: "Anos de exp." },
    stat2: { value: "190+", label: "Alunos formados" },
    stat3: { value: "CREF", label: "011234-G/CE" },
  },
  {
    initials: "LC",
    avatarColor: "#1a1a1a",
    kicker: "NATAÇÃO & AQUA FITNESS · CREF 014567",
    name: "Larissa Costa",
    role: "Aqua Fitness & Condicionamento",
    bio: "Larissa une a leveza da água com treinos de alta eficiência. Especializada em aqua fitness e natação terapêutica, atende desde iniciantes até pessoas em recuperação de lesões, com foco em bem-estar completo.",
    tags: ["Aqua Fitness", "Natação", "Reabilitação", "Bem-estar"],
    stat1: { value: "7+", label: "Anos de exp." },
    stat2: { value: "160+", label: "Alunos ativos" },
    stat3: { value: "CREF", label: "014567-G/CE" },
  },
  {
    initials: "TO",
    avatarColor: "#d61532",
    kicker: "BOXE & ARTES MARCIAIS · CREF 018900",
    name: "Thiago Oliveira",
    role: "Boxe, MMA & Defesa Pessoal",
    bio: "Ex-atleta de MMA com passagem por campeonatos regionais, Thiago transforma a luta em ferramenta de condicionamento físico e mental. Suas aulas de boxe fitness são intensas, técnicas e motivadoras.",
    tags: ["Boxe", "MMA", "Defesa Pessoal", "Condicionamento"],
    stat1: { value: "11+", label: "Anos de exp." },
    stat2: { value: "230+", label: "Alunos formados" },
    stat3: { value: "CREF", label: "018900-G/CE" },
  },
  {
    initials: "PR",
    avatarColor: "#1a1a1a",
    kicker: "PILATES & MOBILIDADE · CREF 022345",
    name: "Patrícia Rocha",
    role: "Pilates Solo & Mobilidade Articular",
    bio: "Patrícia dedica sua carreira à qualidade do movimento. Com formação em Pilates Solo e aparelhos, ajuda alunos a corregirem postura, aumentarem a mobilidade e reduzirem dores crônicas com precisão e cuidado.",
    tags: ["Pilates", "Mobilidade", "Postura", "Dor Crônica"],
    stat1: { value: "8+", label: "Anos de exp." },
    stat2: { value: "210+", label: "Alunos guiados" },
    stat3: { value: "CREF", label: "022345-G/CE" },
  },
  {
    initials: "RV",
    avatarColor: "#d61532",
    kicker: "CORRIDA & ATLETISMO · CREF 026789",
    name: "Rafael Viana",
    role: "Corrida de Rua & Resistência",
    bio: "Maratonista e treinador de corrida, Rafael prepara alunos do primeiro quilômetro até provas de longa distância. Seu método combina planilhas personalizadas, técnica de passada e fortalecimento muscular para corredores.",
    tags: ["Corrida", "Maratona", "Resistência", "Atletismo"],
    stat1: { value: "6+", label: "Anos de exp." },
    stat2: { value: "140+", label: "Corredores treinados" },
    stat3: { value: "CREF", label: "026789-G/CE" },
  },
  {
    initials: "MS",
    avatarColor: "#1a1a1a",
    kicker: "ALONGAMENTO & FLEXIBILITY · CREF 030123",
    name: "Marina Sousa",
    role: "Flexibilidade & Ginástica Postural",
    bio: "Marina acredita que a flexibilidade é a base de qualquer treino de qualidade. Com formação em ginástica rítmica e educação física, desenvolve programas de alongamento e postura que transformam o desempenho dos alunos.",
    tags: ["Flexibilidade", "Ginástica", "Postura", "Alongamento"],
    stat1: { value: "5+", label: "Anos de exp." },
    stat2: { value: "120+", label: "Alunos ativos" },
    stat3: { value: "CREF", label: "030123-G/CE" },
  },
];
 
// ── Carrossel ─────────────────────────────────────────────────
document.addEventListener("DOMContentLoaded", () => {
  const track    = document.getElementById("carrTrack");
  const viewport = document.getElementById("carrViewport");
  const prevBtn  = document.getElementById("carrPrev");
  const nextBtn  = document.getElementById("carrNext");
  const dotsWrap = document.getElementById("carrDots");
  if (!track) return;
 
  const cards = track.querySelectorAll(".instr-card");
  const total  = cards.length;
  let current  = 0;
 
  // Quantos cards cabem na tela
  function visibleCount() {
    const vw = viewport.offsetWidth;
    if (vw < 480) return 1;
    if (vw < 768) return 2;
    if (vw < 1024) return 3;
    return 4;
  }
 
  // Cria dots
  for (let i = 0; i < total; i++) {
    const d = document.createElement("button");
    d.className = "carr-dot" + (i === 0 ? " active" : "");
    d.setAttribute("aria-label", `Instrutor ${i + 1}`);
    d.addEventListener("click", () => goTo(i));
    dotsWrap.appendChild(d);
  }
 
  function goTo(idx) {
    const vis = visibleCount();
    const max = Math.max(0, total - vis);
    current = Math.max(0, Math.min(idx, max));
 
    // Calcula width de um card + gap
    const cardWidth = cards[0].offsetWidth + 16;
    track.style.transform = `translateX(-${current * cardWidth}px)`;
 
    dotsWrap.querySelectorAll(".carr-dot").forEach((d, i) =>
      d.classList.toggle("active", i === current)
    );
 
    prevBtn.disabled = current === 0;
    nextBtn.disabled = current >= max;
  }
 
  prevBtn.addEventListener("click", () => goTo(current - 1));
  nextBtn.addEventListener("click", () => goTo(current + 1));
 
  // Swipe touch
  let tx = 0;
  track.addEventListener("touchstart", e => { tx = e.touches[0].clientX; }, { passive: true });
  track.addEventListener("touchend", e => {
    const diff = tx - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 40) diff > 0 ? goTo(current + 1) : goTo(current - 1);
  });
 
  window.addEventListener("resize", () => goTo(current));
  goTo(0);
});
 
// ── Modal instrutor ───────────────────────────────────────────
document.addEventListener("DOMContentLoaded", () => {
  const modal    = document.getElementById("instrModal");
  const overlay  = document.getElementById("instrOverlay");
  const closeBtn = document.getElementById("instrClose");
  const closeBtn2= document.getElementById("instrClose2");
  if (!modal) return;
 
  // Refs dos elementos do modal
  const mAvatar  = document.getElementById("mAvatar");
  const mKicker  = document.getElementById("mKicker");
  const mName    = document.getElementById("mName");
  const mRole    = document.getElementById("mRole");
  const mBio     = document.getElementById("mBio");
  const mTags    = document.getElementById("mTags");
  const mStat1   = document.getElementById("mStat1");
  const mStat2   = document.getElementById("mStat2");
  const mStat3   = document.getElementById("mStat3");
 
  function statHTML(s) {
    return `<strong>${s.value}</strong><span>${s.label}</span>`;
  }
 
  function openModal(id) {
    const d = INSTRUTORES[id];
    if (!d) return;
 
    // Preenche
    mAvatar.textContent = d.initials;
    mAvatar.style.setProperty("--av-c", d.avatarColor);
    mKicker.textContent = d.kicker;
    mName.textContent   = d.name;
    mRole.textContent   = d.role;
    mBio.textContent    = d.bio;
    mTags.innerHTML     = d.tags.map(t => `<span>${t}</span>`).join("");
    mStat1.innerHTML    = statHTML(d.stat1);
    mStat2.innerHTML    = statHTML(d.stat2);
    mStat3.innerHTML    = statHTML(d.stat3);
 
    modal.classList.add("is-open");
    modal.setAttribute("aria-hidden", "false");
    document.body.style.overflow = "hidden";
    closeBtn.focus();
  }
 
  function closeModal() {
    modal.classList.remove("is-open");
    modal.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
  }
 
  // Abre ao clicar nos cards
  document.querySelectorAll(".instr-card").forEach(card => {
    card.addEventListener("click", () => {
      openModal(parseInt(card.dataset.id, 10));
    });
  });
 
  overlay.addEventListener("click", closeModal);
  closeBtn.addEventListener("click", closeModal);
  if (closeBtn2) closeBtn2.addEventListener("click", closeModal);
 
  document.addEventListener("keydown", e => {
    if (e.key === "Escape" && modal.classList.contains("is-open")) closeModal();
  });
});
 
// ── Animações de entrada (GSAP ScrollTrigger) ─────────────────
document.addEventListener("DOMContentLoaded", () => {
  if (typeof gsap === "undefined" || typeof ScrollTrigger === "undefined") return;
  gsap.registerPlugin(ScrollTrigger);
  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
 
  // Sedes
  gsap.from(".sede-card", {
    scrollTrigger: { trigger: ".sedes-grid", start: "top 82%", once: true },
    y: 40, opacity: 0, duration: 0.55, ease: "power3.out", stagger: 0.1
  });
 
  // Carrossel
  gsap.from(".instr-card", {
    scrollTrigger: { trigger: ".carr-wrap", start: "top 82%", once: true },
    y: 30, opacity: 0, duration: 0.5, ease: "power3.out", stagger: 0.08
  });
 
  // FAQ
  gsap.from(".faq-acc", {
    scrollTrigger: { trigger: ".faq-list", start: "top 82%", once: true },
    y: 20, opacity: 0, duration: 0.45, ease: "power2.out", stagger: 0.07
  });
});