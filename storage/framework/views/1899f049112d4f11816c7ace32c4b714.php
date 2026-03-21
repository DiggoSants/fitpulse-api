<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Academia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/style.css', 'resources/js/script.js']); ?>
</head>
<body>

<header class="topbar">
  <div class="container nav">
    <button class="nav-toggle" id="navToggle" aria-label="Abrir menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>

    <div class="nav-left">
      <a href="#sedes" class="nav-link">NOSSAS SEDES</a>
      <a href="#beneficios" class="nav-link">BENEFÍCIOS</a>
      <a href="#planos" class="nav-link">PLANOS</a>
      <div class="dropdown">
        <button class="dropdown-btn" type="button" aria-expanded="false">
          MAIS <span class="chev">▾</span>
        </button>
        <div class="dropdown-menu" role="menu">
          <a class="dropdown-item" href="#sobre">SOBRE NÓS</a>
        </div>
      </div>
    </div>

    <div class="logo">
      <div class="logo-title">FIT</div>
      <div class="logo-sub">PULSE</div>
    </div>

    <div class="nav-auth">
      <?php if(Route::has('login')): ?>
        <?php if(auth()->guard()->check()): ?>
          <a href="<?php echo e(url('/dashboard')); ?>" class="cta--solid">DASHBOARD</a>
        <?php else: ?>
          <a href="<?php echo e(route('login')); ?>" class="cta--outline">LOGIN</a>
          <?php if(Route::has('register')): ?>
            <a href="<?php echo e(route('register')); ?>" class="cta--solid">REGISTRAR</a>
          <?php endif; ?>
        <?php endif; ?>
      <?php endif; ?>
      <a class="cta--solid" href="#matricula">
        <i class="fa-solid fa-dumbbell cta-icon" aria-hidden="true"></i>
        <span class="matricula-text">MATRICULE-SE JÁ</span>
      </a>
    </div>
  </div>
  <nav class="mobile-menu" id="mobileMenu" aria-hidden="true">
    <a href="#sedes" class="mobile-link">NOSSAS SEDES</a>
    <a href="#beneficios" class="mobile-link">BENEFÍCIOS</a>
    <a href="#planos" class="mobile-link">PLANOS</a>
    <a href="#sobre" class="mobile-link">SOBRE NÓS</a>
    <a href="#matricula" class="mobile-link mobile-link--cta">MATRICULE-SE JÁ</a>
  </nav>
</header>
<main>

  <!-- HERO -->
  <section class="hero hero--porão" id="topo">
    <div class="container hero-layout">

      <!-- WATERMARK DENTRO do hero-layout (posição absoluta funciona corretamente aqui) -->
      <div class="hero-watermark" aria-hidden="true">
        <span>FIT</span>
        <span>PULSE</span>
      </div>

      <!-- Esquerda: texto -->
      <div class="hero-content">
        <p class="hero-kicker">FIT PULSE ACADEMIA</p>
        <h1 class="hero-title">
          TREINE COM O<br>
          MELHOR EM<br>
          <span class="hero-dynamic" id="word">AMBIENTE</span>
        </h1>
      </div>

      <!-- Direita: imagem -->
      <div class="hero-media">
        <img class="hero-character" src="<?php echo e(asset('img/foto_inicial.png')); ?>" alt="Pessoa levantando peso">
      </div>

    </div>
  </section>

  <!-- CONTADOR -->
  <section class="stats" id="stats">
    <div class="container stats-head">
      <h2 class="stats-title">Resultados que dão confiança</h2>
      <p class="stats-sub">Números reais do dia a dia da FIT PULSE.</p>
    </div>
    <div class="container stats-grid">
      <div class="stat">
        <div class="stat-number" data-to="3600" data-prefix="+">0</div>
        <div class="stat-label">alunos ativos</div>
      </div>
      <div class="stat">
        <div class="stat-number" data-to="49" data-suffix="/5">0</div>
        <div class="stat-label">avaliação média</div>
      </div>
      <div class="stat">
        <div class="stat-number" data-to="12">0</div>
        <div class="stat-label">professores</div>
      </div>
      <div class="stat">
        <div class="stat-number" data-to="5">0</div>
        <div class="stat-label">unidades</div>
      </div>
    </div>
  </section>

  <!-- MODAL -->
  <div class="modal" id="videoModal" aria-hidden="true">
    <div class="modal__overlay" data-close></div>
    <div class="modal__panel" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
      <button class="modal__close" type="button" aria-label="Fechar" data-close>✕</button>
      <h2 class="sr-only" id="modalTitle">Vídeo</h2>
      <div class="modal__content">
        <iframe id="videoFrame" src="" title="Vídeo da academia" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
      </div>
    </div>
  </div>

  <!-- SOBRE -->
  <section class="about" id="sobre">
    <div class="container about-grid">
      <div class="about-media">
        <div class="offer-card">
          <h4>Nós oferecemos</h4>
          <p>Treinos completos, acompanhamento e um ambiente feito pra evoluir.</p>
          <a class="offer-link" href="#matricula">MATRICULE-SE →</a>
        </div>
        <img class="about-img-main" src="<?php echo e(asset('img/Fit_couple_standing.jpg')); ?>" alt="Pessoas treinando">
        <button class="about-video" type="button" id="openVideo"
                data-video="https://www.youtube.com/embed/SEU_ID_AQUI?autoplay=1">
          <img src="img/about-video.jpg" alt="Assistir vídeo">
          <span class="play" aria-hidden="true">▶</span>
        </button>
      </div>
      <div class="about-text">
        <p class="about-kicker">SOBRE NÓS</p>
        <h2 class="about-title">Leve sua saúde e corpo para <span>próximo nível</span></h2>
        <p class="about-sub">Treinos com metodologia, professores presentes e estrutura completa para você evoluir com segurança.</p>
        <ul class="about-list">
          <li>✅ Avaliação e acompanhamento</li>
          <li>✅ Treinos completos e bem orientados</li>
          <li>✅ Ambiente motivador e seguro</li>
        </ul>
        <div class="about-actions">
          <a class="btn btn--ghost" href="#sobre">MAIS SOBRE NÓS</a>
          <div class="about-phone">
            <small>Precisa de ajuda? Fale conosco</small>
            <strong>(85) 99999-9999</strong>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- SEDES -->
  <section class="section" id="sedes">
    <div class="container">
      <h2 class="section-title">Nossas sedes</h2>
      <p class="section-sub">Escolha a unidade mais perto de você.</p>
      <div class="grid-3">
        <div class="card">Unidade 1<br><small>Endereço</small></div>
        <div class="card">Unidade 2<br><small>Endereço</small></div>
        <div class="card">Unidade 3<br><small>Endereço</small></div>
      </div>
    </div>
  </section>

  <!-- INSTRUTORES -->
  <section class="section">
    <div class="container">
      <h2 class="section-title">CONHEÇA NOSSOS INSTRUTORES</h2>
      <p class="section-sub">texto...</p>
      <div class="grid-3"></div>
    </div>
  </section>

  <!-- BENEFÍCIOS -->
  <section class="section section--alt" id="beneficios">
    <div class="container">
      <h2 class="section-title">Benefícios</h2>
      <p class="section-sub">O que você ganha treinando aqui.</p>
      <div class="grid-3">
        <div class="card">Treinos completos</div>
        <div class="card">Professores presentes</div>
        <div class="card">Ambiente motivador</div>
      </div>
    </div>
  </section>

  <!-- PLANOS -->
  <section class="section" id="planos">
    <div class="container">
      <h2 class="section-title">Planos</h2>
      <p class="section-sub">Escolha o plano ideal pra você.</p>
      <div class="grid-3">
        <div class="pricing">
          <h3>Básico</h3>
          <p class="price">R$ 79,90</p>
          <ul><li>Musculação</li><li>Acesso em horário comercial</li></ul>
          <a class="btn btn--outline" href="#matricula">Quero esse</a>
        </div>
        <div class="pricing pricing--featured">
          <h3>Premium</h3>
          <p class="price">R$ 119,90</p>
          <ul><li>Musculação + Aulas</li><li>Qualquer horário</li><li>Acompanhamento</li></ul>
          <a class="btn btn--ghost" href="#matricula">Mais escolhido</a>
        </div>
        <div class="pricing">
          <h3>Black</h3>
          <p class="price">R$ 149,90</p>
          <ul><li>Tudo do Premium</li><li>Treino personalizado</li></ul>
          <a class="btn btn--outline" href="#matricula">Quero esse</a>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section class="section section--alt" id="faq">
    <div class="container">
      <h2 class="section-title">Perguntas frequentes</h2>
      <div class="faq">
        <details class="faq-item">
          <summary>Tem taxa de matrícula?</summary>
          <p>Coloque aqui sua resposta.</p>
        </details>
        <details class="faq-item">
          <summary>Posso treinar em qualquer unidade?</summary>
          <p>Coloque aqui sua resposta.</p>
        </details>
      </div>
    </div>
  </section>

</main>

<footer class="footer">
  <div class="container footer-inner">
    <p>© FIT PULSE</p>
    <a class="nav-link" href="#topo">Voltar ao topo</a>
  </div>
</footer>

</body>
</html><?php /**PATH C:\xampp\htdocs\fitpulse-api\resources\views/welcome.blade.php ENDPATH**/ ?>