<!DOCTYPE html>
<html lang="pt-BR">
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
    @vite(['resources/css/style.css', 'resources/js/script.js'])
</head>
<body>

<header class="topbar">
  <div class="container nav">

    <!-- ESQUERDA: logo, texto, botão explorar -->
    <div class="nav-left">

      <img
        class="nav-logo-img"
        src="{{ asset('img/logo.png') }}"
        alt="FIT PULSE logo"
      >

      <div class="logo">
        <div class="logo-title">FIT</div>
        <div class="logo-sub">PULSE</div>
      </div>

      <div class="explorar-wrap">
  <button class="btn-explorar" id="openExplore" aria-label="Explorar a academia">
    <i class="fa-solid fa-compass"></i>
    <span>EXPLORAR</span>
  </button>
 
  <!-- Preview flutuante -->
  <div class="explorar-preview">
    <p class="explorar-preview__title">CONHEÇA A FIT PULSE</p>
    <div class="explorar-preview__tabs">
      <span class="ep-tab ep-tab--active"><i class="fa-solid fa-house"></i> Início</span>
      <span class="ep-tab"><i class="fa-solid fa-building"></i> Empresa</span>
      <span class="ep-tab"><i class="fa-solid fa-dumbbell"></i> Aulas</span>
      <span class="ep-tab"><i class="fa-solid fa-users"></i> Instrutores</span>
      <span class="ep-tab"><i class="fa-solid fa-tag"></i> Planos</span>
      <span class="ep-tab"><i class="fa-solid fa-clock"></i> Horários</span>
      <span class="ep-tab"><i class="fa-solid fa-location-dot"></i> Endereços</span>
      <span class="ep-tab"><i class="fa-solid fa-phone"></i> Contato</span>
    </div>
    <p class="explorar-preview__cta">Clique para explorar <i class="fa-solid fa-arrow-right"></i></p>
  </div>
</div>
 
    </div>

    <!-- DIREITA: auth -->
    <div class="nav-auth">
      @if (Route::has('login'))
        @auth
          <a href="{{ url('/dashboard') }}" class="cta--solid">DASHBOARD</a>
        @else
          <a href="{{ route('login') }}" class="cta--outline">LOGIN</a>
          @if (Route::has('register'))
            <a href="{{ route('register') }}" class="cta--solid">REGISTRAR</a>
          @endif
        @endauth
      @endif
      <a class="cta--solid" href="#matricula">
        <i class="fa-solid fa-dumbbell cta-icon" aria-hidden="true"></i>
        <span class="matricula-text">MATRICULE-SE JÁ</span>
      </a>
    </div>

  </div>
</header>
<!-- MODAL EXPLORAR — TELA CHEIA COM SLIDES -->
<div class="explore-modal" id="exploreModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="exploreTitle">

  <div class="explore-header">
    <div class="explore-logo">FIT<span>PULSE</span></div>

    <nav class="explore-tabs" role="tablist">
      <button class="explore-tab active" data-slide="0" role="tab" aria-selected="true">INÍCIO</button>
      <button class="explore-tab" data-slide="1" role="tab" aria-selected="false">EMPRESA</button>
      <button class="explore-tab" data-slide="2" role="tab" aria-selected="false">AULAS</button>
      <button class="explore-tab" data-slide="3" role="tab" aria-selected="false">INSTRUTORES</button>
      <button class="explore-tab" data-slide="4" role="tab" aria-selected="false">PLANOS</button>
      <button class="explore-tab" data-slide="5" role="tab" aria-selected="false">HORÁRIOS</button>
      <button class="explore-tab" data-slide="6" role="tab" aria-selected="false">ENDEREÇOS</button>
      <button class="explore-tab" data-slide="7" role="tab" aria-selected="false">CONTATO</button>
    </nav>

    <button class="explore-close" id="closeExplore" aria-label="Fechar">
      <i class="fa-solid fa-xmark"></i>
    </button>
  </div>

  <div class="explore-progress">
    <div class="explore-progress-bar" id="exploreProgress"></div>
  </div>

  <div class="explore-track-wrap">
    <div class="explore-track" id="exploreTrack">

      <!-- SLIDE 0: INÍCIO -->
      <div class="explore-slide" data-index="0">
        <div class="explore-slide__bg" style="background: linear-gradient(135deg,#0a0a0a 60%,#1a0608 100%);">
          <div class="es-watermark" aria-hidden="true">FIT PULSE</div>
        </div>
        <div class="explore-slide__content es-intro">
          <p class="es-kicker">BEM-VINDO À</p>
          <h1 class="es-title" id="exploreTitle">FIT PULSE<br><span>ACADEMIA</span></h1>
          <p class="es-desc">Uma academia completa para quem leva o treino a sério. Navegue pelas seções ao lado e descubra tudo que temos para você.</p>
          <div class="es-pills">
            <span class="es-pill"><i class="fa-solid fa-dumbbell"></i> Musculação</span>
            <span class="es-pill"><i class="fa-solid fa-person-running"></i> Cardio</span>
            <span class="es-pill"><i class="fa-solid fa-users"></i> Aulas coletivas</span>
            <span class="es-pill"><i class="fa-solid fa-star"></i> 5 unidades</span>
          </div>
          <div class="es-stats-row">
            <div class="es-stat"><strong>+3.600</strong><span>alunos</span></div>
            <div class="es-stat"><strong>4,9/5</strong><span>avaliação</span></div>
            <div class="es-stat"><strong>12</strong><span>professores</span></div>
            <div class="es-stat"><strong>5</strong><span>unidades</span></div>
          </div>
          <button class="es-next-btn" data-goto="1">
            CONHEÇA A EMPRESA <i class="fa-solid fa-arrow-right"></i>
          </button>
        </div>
      </div>

      <!-- SLIDE 1: EMPRESA -->
      <div class="explore-slide" data-index="1">
        <div class="explore-slide__bg" style="background: #0d0d0d;">
          <div class="es-watermark" aria-hidden="true">EMPRESA</div>
        </div>
        <div class="explore-slide__content es-two-col">
          <div class="es-col-text">
            <p class="es-kicker">SOBRE NÓS</p>
            <h2 class="es-title">Uma história<br>de <span>superação</span></h2>
            <p class="es-desc">Fundada em 2014 em Fortaleza, a FIT PULSE nasceu da vontade de oferecer um espaço onde qualquer pessoa — iniciante ou atleta — pudesse evoluir com segurança, orientação e motivação real.</p>
            <p class="es-desc">Hoje somos referência no Ceará com 5 unidades, mais de 3.600 alunos ativos e uma equipe de professores certificados e apaixonados pelo que fazem.</p>
            <ul class="es-checklist">
              <li><i class="fa-solid fa-check"></i> Metodologia própria de treino</li>
              <li><i class="fa-solid fa-check"></i> Avaliação física na matrícula</li>
              <li><i class="fa-solid fa-check"></i> Acompanhamento contínuo</li>
              <li><i class="fa-solid fa-check"></i> Equipamentos premium renovados</li>
            </ul>
            <button class="es-next-btn" data-goto="2">VER NOSSAS AULAS <i class="fa-solid fa-arrow-right"></i></button>
          </div>
          <div class="es-col-visual">
            <div class="es-card-stat"><strong>2014</strong><span>Fundação</span></div>
            <div class="es-card-stat es-card-stat--red"><strong>5</strong><span>Unidades CE</span></div>
            <div class="es-card-stat"><strong>+3.6k</strong><span>Alunos ativos</span></div>
            <div class="es-card-stat es-card-stat--red"><strong>4,9★</strong><span>Avaliação</span></div>
          </div>
        </div>
      </div>

      <!-- SLIDE 2: AULAS -->
      <div class="explore-slide" data-index="2">
        <div class="explore-slide__bg" style="background: #0a0a0a;">
          <div class="es-watermark" aria-hidden="true">AULAS</div>
        </div>
        <div class="explore-slide__content">
          <p class="es-kicker">MODALIDADES</p>
          <h2 class="es-title">Nossas <span>Aulas</span></h2>
          <p class="es-desc" style="max-width:560px">Mais de 20 modalidades disponíveis para todos os níveis. Escolha a que mais combina com você.</p>
          <div class="es-aulas-grid">
            <div class="es-aula-card">
              <i class="fa-solid fa-dumbbell"></i>
              <strong>Musculação</strong>
              <span>Hipertrofia, força e definição com acompanhamento individual.</span>
            </div>
            <div class="es-aula-card">
              <i class="fa-solid fa-fire"></i>
              <strong>Spinning</strong>
              <span>Alta intensidade, queima calórica e muita energia em grupo.</span>
            </div>
            <div class="es-aula-card">
              <i class="fa-solid fa-person-running"></i>
              <strong>Funcional</strong>
              <span>Movimentos naturais para mobilidade, força e resistência.</span>
            </div>
            <div class="es-aula-card">
              <i class="fa-solid fa-spa"></i>
              <strong>Yoga</strong>
              <span>Equilíbrio, flexibilidade e consciência corporal.</span>
            </div>
            <div class="es-aula-card">
              <i class="fa-solid fa-heart-pulse"></i>
              <strong>Zumba</strong>
              <span>Dança, ritmo e cardio numa aula que parece festa.</span>
            </div>
            <div class="es-aula-card">
              <i class="fa-solid fa-trophy"></i>
              <strong>Cross Training</strong>
              <span>Circuitos variados de alta performance para desafiar seus limites.</span>
            </div>
          </div>
          <button class="es-next-btn" data-goto="3">CONHEÇA OS INSTRUTORES <i class="fa-solid fa-arrow-right"></i></button>
        </div>
      </div>

      <!-- SLIDE 3: INSTRUTORES -->
      <div class="explore-slide" data-index="3">
        <div class="explore-slide__bg" style="background: #0d0d0d;">
          <div class="es-watermark" aria-hidden="true">EQUIPE</div>
        </div>
        <div class="explore-slide__content">
          <p class="es-kicker">NOSSA EQUIPE</p>
          <h2 class="es-title">Os melhores<br><span>instrutores</span></h2>
          <p class="es-desc" style="max-width:520px">Todos certificados, atualizados e prontos para guiar sua evolução do primeiro ao último treino.</p>
          <div class="es-instrutores-grid">
            <div class="es-instrutor">
              <div class="es-instrutor__avatar">RB</div>
              <strong>Ricardo Borges</strong>
              <span>Musculação · CREF 001234</span>
              <div class="es-instrutor__tags"><em>Hipertrofia</em><em>Força</em></div>
            </div>
            <div class="es-instrutor">
              <div class="es-instrutor__avatar" style="background:var(--red)">CA</div>
              <strong>Carla Almeida</strong>
              <span>Spinning & Funcional · CREF 005678</span>
              <div class="es-instrutor__tags"><em>Cardio</em><em>HIIT</em></div>
            </div>
            <div class="es-instrutor">
              <div class="es-instrutor__avatar">MF</div>
              <strong>Marcos Freitas</strong>
              <span>Cross Training · CREF 009012</span>
              <div class="es-instrutor__tags"><em>Performance</em><em>Calistenia</em></div>
            </div>
            <div class="es-instrutor">
              <div class="es-instrutor__avatar" style="background:var(--red)">JN</div>
              <strong>Juliana Neves</strong>
              <span>Yoga & Pilates · CREF 003456</span>
              <div class="es-instrutor__tags"><em>Flexibilidade</em><em>Equilíbrio</em></div>
            </div>
          </div>
          <button class="es-next-btn" data-goto="4">VER PLANOS <i class="fa-solid fa-arrow-right"></i></button>
        </div>
      </div>

      <!-- SLIDE 4: PLANOS -->
      <div class="explore-slide" data-index="4">
        <div class="explore-slide__bg" style="background: #0a0a0a;">
          <div class="es-watermark" aria-hidden="true">PLANOS</div>
        </div>
        <div class="explore-slide__content">
          <p class="es-kicker">INVESTIMENTO</p>
          <h2 class="es-title">Escolha seu <span>plano</span></h2>
          <p class="es-desc" style="max-width:520px">Sem taxa de matrícula no primeiro mês. Cancele quando quiser.</p>
          <div class="es-planos-grid">
            <div class="es-plano">
              <div class="es-plano__name">BÁSICO</div>
              <div class="es-plano__price">R$<strong>79</strong><sub>,90/mês</sub></div>
              <ul>
                <li><i class="fa-solid fa-check"></i> Musculação</li>
                <li><i class="fa-solid fa-check"></i> Horário comercial</li>
                <li><i class="fa-solid fa-check"></i> 1 unidade</li>
                <li class="es-plano__no"><i class="fa-solid fa-xmark"></i> Aulas coletivas</li>
              </ul>
              <a href="#matricula" class="es-plano__btn">QUERO ESSE</a>
            </div>
            <div class="es-plano es-plano--featured">
              <div class="es-plano__badge">MAIS ESCOLHIDO</div>
              <div class="es-plano__name">PREMIUM</div>
              <div class="es-plano__price">R$<strong>119</strong><sub>,90/mês</sub></div>
              <ul>
                <li><i class="fa-solid fa-check"></i> Musculação + Aulas</li>
                <li><i class="fa-solid fa-check"></i> Qualquer horário</li>
                <li><i class="fa-solid fa-check"></i> Todas as unidades</li>
                <li><i class="fa-solid fa-check"></i> Acompanhamento</li>
              </ul>
              <a href="#matricula" class="es-plano__btn">ASSINAR AGORA</a>
            </div>
            <div class="es-plano">
              <div class="es-plano__name">BLACK</div>
              <div class="es-plano__price">R$<strong>149</strong><sub>,90/mês</sub></div>
              <ul>
                <li><i class="fa-solid fa-check"></i> Tudo do Premium</li>
                <li><i class="fa-solid fa-check"></i> Treino personalizado</li>
                <li><i class="fa-solid fa-check"></i> Personal trainer</li>
                <li><i class="fa-solid fa-check"></i> Nutrição básica</li>
              </ul>
              <a href="#matricula" class="es-plano__btn">QUERO ESSE</a>
            </div>
          </div>
          <button class="es-next-btn" data-goto="5">VER HORÁRIOS <i class="fa-solid fa-arrow-right"></i></button>
        </div>
      </div>

      <!-- SLIDE 5: HORÁRIOS -->
      <div class="explore-slide" data-index="5">
        <div class="explore-slide__bg" style="background: #0d0d0d;">
          <div class="es-watermark" aria-hidden="true">HORAS</div>
        </div>
        <div class="explore-slide__content es-two-col">
          <div class="es-col-text">
            <p class="es-kicker">FUNCIONAMENTO</p>
            <h2 class="es-title">Quando você<br><span>quiser</span> treinar</h2>
            <p class="es-desc">Horários pensados para caber na sua rotina, seja você madrugador ou notívago.</p>
            <div class="es-horario-table">
              <div class="es-horario-row es-horario-row--head">
                <span>DIA</span><span>ABERTURA</span><span>FECHAMENTO</span>
              </div>
              <div class="es-horario-row"><span>Segunda – Sexta</span><span>05:00</span><span>23:00</span></div>
              <div class="es-horario-row"><span>Sábado</span><span>06:00</span><span>20:00</span></div>
              <div class="es-horario-row"><span>Domingo</span><span>08:00</span><span>14:00</span></div>
              <div class="es-horario-row"><span>Feriados</span><span>08:00</span><span>14:00</span></div>
            </div>
            <p class="es-obs"><i class="fa-solid fa-circle-info"></i> Plano Básico: acesso apenas em horário comercial (08h–18h).</p>
          </div>
          <div class="es-col-visual es-aulas-schedule">
            <p class="es-kicker" style="margin-bottom:14px">GRADE DE AULAS</p>
            <div class="es-schedule-item"><span class="es-schedule-time">06:30</span><span class="es-schedule-name">Spinning</span><em>Seg · Qua · Sex</em></div>
            <div class="es-schedule-item"><span class="es-schedule-time">08:00</span><span class="es-schedule-name">Yoga</span><em>Ter · Qui</em></div>
            <div class="es-schedule-item es-schedule-item--red"><span class="es-schedule-time">12:00</span><span class="es-schedule-name">Funcional</span><em>Diário</em></div>
            <div class="es-schedule-item"><span class="es-schedule-time">18:30</span><span class="es-schedule-name">Zumba</span><em>Ter · Qui · Sáb</em></div>
            <div class="es-schedule-item es-schedule-item--red"><span class="es-schedule-time">19:30</span><span class="es-schedule-name">Cross Training</span><em>Seg · Qua · Sex</em></div>
          </div>
          <button class="es-next-btn" style="margin-top:24px" data-goto="6">VER ENDEREÇOS <i class="fa-solid fa-arrow-right"></i></button>
        </div>
      </div>

      <!-- SLIDE 6: ENDEREÇOS -->
      <div class="explore-slide" data-index="6">
        <div class="explore-slide__bg" style="background: #0a0a0a;">
          <div class="es-watermark" aria-hidden="true">SEDES</div>
        </div>
        <div class="explore-slide__content">
          <p class="es-kicker">NOSSAS SEDES</p>
          <h2 class="es-title">Uma unidade<br>perto de <span>você</span></h2>
          <div class="es-sedes-grid">
            <div class="es-sede">
              <div class="es-sede__num">01</div>
              <strong>Meireles</strong>
              <span>Av. Beira Mar, 1250 · Fortaleza – CE</span>
              <a href="https://maps.google.com" target="_blank" class="es-sede__link"><i class="fa-solid fa-location-dot"></i> Ver no mapa</a>
            </div>
            <div class="es-sede">
              <div class="es-sede__num">02</div>
              <strong>Aldeota</strong>
              <span>Rua Dom Luís, 880 · Fortaleza – CE</span>
              <a href="https://maps.google.com" target="_blank" class="es-sede__link"><i class="fa-solid fa-location-dot"></i> Ver no mapa</a>
            </div>
            <div class="es-sede">
              <div class="es-sede__num">03</div>
              <strong>Maraponga</strong>
              <span>Av. Godofredo Maciel, 540 · Fortaleza – CE</span>
              <a href="https://maps.google.com" target="_blank" class="es-sede__link"><i class="fa-solid fa-location-dot"></i> Ver no mapa</a>
            </div>
            <div class="es-sede">
              <div class="es-sede__num">04</div>
              <strong>Messejana</strong>
              <span>Av. Frei Cirilo, 1100 · Fortaleza – CE</span>
              <a href="https://maps.google.com" target="_blank" class="es-sede__link"><i class="fa-solid fa-location-dot"></i> Ver no mapa</a>
            </div>
            <div class="es-sede">
              <div class="es-sede__num">05</div>
              <strong>Caucaia</strong>
              <span>Rua Araújo, 320 · Caucaia – CE</span>
              <a href="https://maps.google.com" target="_blank" class="es-sede__link"><i class="fa-solid fa-location-dot"></i> Ver no mapa</a>
            </div>
          </div>
          <button class="es-next-btn" data-goto="7">FALE CONOSCO <i class="fa-solid fa-arrow-right"></i></button>
        </div>
      </div>

      <!-- SLIDE 7: CONTATO -->
      <div class="explore-slide" data-index="7">
        <div class="explore-slide__bg" style="background: #0d0d0d;">
          <div class="es-watermark" aria-hidden="true">CONTATO</div>
        </div>
        <div class="explore-slide__content es-two-col">
          <div class="es-col-text">
            <p class="es-kicker">FALE CONOSCO</p>
            <h2 class="es-title">Pronto para<br><span>começar?</span></h2>
            <p class="es-desc">Entre em contato ou já se matricule agora. Nosso time responde em até 2 horas.</p>
            <div class="es-contato-links">
              <a href="https://wa.me/5585999999999" target="_blank" class="es-contato-btn es-contato-btn--whats">
                <i class="fa-brands fa-whatsapp"></i> WhatsApp
              </a>
              <a href="tel:+5585999999999" class="es-contato-btn">
                <i class="fa-solid fa-phone"></i> (85) 99999-9999
              </a>
              <a href="mailto:contato@fitpulse.com.br" class="es-contato-btn">
                <i class="fa-solid fa-envelope"></i> contato@fitpulse.com.br
              </a>
            </div>
            <div class="es-social">
              <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
              <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
              <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
            </div>
          </div>
          <div class="es-col-visual">
            <div class="es-matricula-cta">
              <p class="es-kicker">OFERTA ESPECIAL</p>
              <h3>1º MÊS<br><span>SEM TAXA</span><br>DE MATRÍCULA</h3>
              <p>Válido para novas matrículas em qualquer unidade.</p>
              <a href="#matricula" class="es-plano__btn" style="margin-top:16px;display:inline-block">MATRICULE-SE AGORA</a>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <button class="explore-arrow explore-arrow--prev" id="explorePrev" aria-label="Slide anterior">
    <i class="fa-solid fa-chevron-left"></i>
  </button>
  <button class="explore-arrow explore-arrow--next" id="exploreNext" aria-label="Próximo slide">
    <i class="fa-solid fa-chevron-right"></i>
  </button>

  <div class="explore-dots" id="exploreDots"></div>

</div>

<main>

  <!-- HERO -->
  <section class="hero hero--porão" id="topo">
    <div class="container hero-layout">
      <div class="hero-watermark" aria-hidden="true">
        <span>FIT</span>
        <span>PULSE</span>
      </div>
      <div class="hero-content">
        <p class="hero-kicker">FIT PULSE ACADEMIA</p>
        <h1 class="hero-title">
          TREINE COM O<br>
          MELHOR EM<br>
          <span class="hero-dynamic" id="word">AMBIENTE</span>
        </h1>
      </div>
      <div class="hero-media">
        <img class="hero-character" src="{{ asset('img/foto_inicial.png') }}" alt="Pessoa levantando peso">
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

  <!-- MODAL VÍDEO -->
  <div class="modal" id="videoModal" aria-hidden="true">
    <div class="modal__overlay" data-close></div>
    <div class="modal__panel" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
      <button class="modal__close" type="button" aria-label="Fechar" data-close>✕</button>
      <h2 class="sr-only" id="modalTitle">Vídeo</h2>
      <div class="modal__content">
         <video id="videoFrame" controls playsinline style="width:100%;height:100%;display:block;background:#000;"></video>
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
        <img class="about-img-main" src="{{ asset('img\Fit couple standing.jpg') }}" alt="Pessoas treinando">
        <button class="about-video" type="button" id="openVideo"
                data-video="{{ asset('video/video.mp4') }}">
          <img src="img/gym.jpg" alt="Assistir vídeo">
          <span class="play" aria-hidden="true">▶</span>
        </button>
      </div>
      <div class="about-text">
        <p class="about-kicker">SOBRE NÓS</p>
        <h2 class="about-title">Leve sua saúde e corpo para <span>próximo nível</span></h2>
        <p class="about-sub">Treinos com metodologia, professores presentes e estrutura completa para você evoluir com segurança.</p>
        <ul class="about-list">
          <li><span class="check-dash">─</span> Avaliação e acompanhamento</li>
          <li><span class="check-dash">─</span>Treinos completos e bem orientados </li>
          <li><span class="check-dash">─</span>Ambiente motivador e seguro </li>

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
</html>