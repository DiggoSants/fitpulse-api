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
    @if (file_exists(public_path('hot')))
      @vite(['resources/css/style.css', 'resources/js/script.js'])
    @else
      @php
        $viteManifestPath = public_path('build/manifest.json');
        $viteManifest = file_exists($viteManifestPath)
            ? json_decode(file_get_contents($viteManifestPath), true)
            : [];
      @endphp
      @isset($viteManifest['resources/css/style.css']['file'])
        <link rel="stylesheet" href="{{ asset('build/' . $viteManifest['resources/css/style.css']['file']) }}">
      @endisset
      @vite('resources/js/script.js')
    @endif
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
       <!-- Botão trocar cor -->
        <button class="btn-theme" id="btnTheme" aria-label="Trocar tema">
           <i class="fa-solid fa-moon"></i>
       </button>
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
        <div class="explore-slide__bg es-bg-dark">
          <div class="es-watermark" aria-hidden="true">FIT PULSE</div>
        </div>
        <div class="explore-slide__content es-intro">
          <p class="es-kicker">BEM-VINDO À</p>
          <h1 class="es-title" id="exploreTitle">FIT PULSE<br><span>ACADEMIA</span></h1>
          <p class="es-desc">Uma academia completa para quem leva o treino a sério. Navegue pelas seções ao lado e descubra tudo que temos para você.</p>
          <div class="es-pills">
            <span class="es-pill"><i class="fa-solid fa-dumbbell"></i> Musculação</span>
            <span class="es-pill"><i class="fa-solid fa-person-running"></i> Cardio</span>
            <span class="es-pill"><i class="fa-solid fa-users"></i> Plano conjunto</span>
            <span class="es-pill"><i class="fa-solid fa-star"></i> 6 unidades</span>
          </div>
          <div class="es-stats-row">
            <div class="es-stat"><strong>+3.600</strong><span>alunos</span></div>
            <div class="es-stat"><strong>4,9/5</strong><span>avaliação</span></div>
            <div class="es-stat"><strong>12</strong><span>professores</span></div>
            <div class="es-stat"><strong>6</strong><span>unidades</span></div>
          </div>
          <button class="es-next-btn" data-goto="1">
            CONHEÇA A EMPRESA <i class="fa-solid fa-arrow-right"></i>
          </button>
        </div>
      </div>
 
      <!-- SLIDE 1: EMPRESA -->
      <div class="explore-slide" data-index="1">
        <div class="explore-slide__bg es-bg-darker">
          <div class="es-watermark" aria-hidden="true">EMPRESA</div>
        </div>
        <div class="explore-slide__content es-two-col">
          <div class="es-col-text">
            <p class="es-kicker">SOBRE NÓS</p>
            <h2 class="es-title">Uma história<br>de <span>superação</span></h2>
            <p class="es-desc">Fundada em 2014 em Fortaleza, a FIT PULSE nasceu da vontade de oferecer um espaço onde qualquer pessoa — iniciante ou atleta — pudesse evoluir com segurança, orientação e motivação real.</p>
            <p class="es-desc">Hoje somos referência no Ceará com 6 unidades, mais de 3.600 alunos ativos e uma equipe de professores certificados e apaixonados pelo que fazem.</p>
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
            <div class="es-card-stat es-card-stat--red"><strong>6</strong><span>Unidades CE</span></div>
            <div class="es-card-stat"><strong>+3.6k</strong><span>Alunos ativos</span></div>
            <div class="es-card-stat es-card-stat--red"><strong>4,9★</strong><span>Avaliação</span></div>
          </div>
        </div>
      </div>
 
      <!-- SLIDE 2: AULAS -->
      <div class="explore-slide" data-index="2">
        <div class="explore-slide__bg es-bg-dark">
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
              <strong>Área Cardio</strong>
              <span>Esteiras, bicicletas e elípticos para melhorar o condicionamento e acelerar seus resultados.</span>
            </div>
            <div class="es-aula-card">
              <i class="fa-solid fa-heart-pulse"></i>
              <strong>Treino de Core</strong>
              <span>Exercícios focados em abdômen, postura e estabilidade para mais força e equilíbrio.</span>
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
        <div class="explore-slide__bg es-bg-darker">
          <div class="es-watermark" aria-hidden="true">EQUIPE</div>
        </div>
        <div class="explore-slide__content">
          <p class="es-kicker">NOSSA EQUIPE</p>
          <h2 class="es-title">Os melhores<br><span>instrutores</span></h2>
          <p class="es-desc" style="max-width:520px">Todos certificados, atualizados e prontos para guiar sua evolução do primeiro ao último treino.</p>
          <div class="es-instrutores-grid">
            <div class="es-instrutor">
               <img src="{{ asset('img/instrutor2.jpeg') }}" alt="Ricardo Borges" class="es-instrutor__avatar">
              <strong>Ricardo Borges</strong>
              <span>Musculação · CREF 001234</span>
              <div class="es-instrutor__tags"><em>Hipertrofia</em><em>Força</em></div>
            </div>
            <div class="es-instrutor">
              <img src="{{ asset('img/instrutora8.jpeg') }}" alt="Carla Almeida" class="es-instrutor__avatar">
              <strong>Carla Almeida</strong>
              <span>Spinning & Funcional · CREF 005678</span>
              <div class="es-instrutor__tags"><em>Cardio</em><em>HIIT</em></div>
            </div>
            <div class="es-instrutor">
              <img src="{{ asset('img/instrutor12.png') }}" alt="Marcos Freitas" class="es-instrutor__avatar">
              <strong>Marcos Freitas</strong>
              <span>Cross Training · CREF 009012</span>
              <div class="es-instrutor__tags"><em>Performance</em><em>Calistenia</em></div>
            </div>
            <div class="es-instrutor">
              <img src="{{ asset('img/instrutora7.jpg') }}" alt="Juliana Neves" class="es-instrutor__avatar">
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
        <div class="explore-slide__bg es-bg-dark">
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
              <a href="{{ route('register') }}" class="es-plano__btn">QUERO ESSE</a>
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
              <a href="{{ route('register') }}" class="es-plano__btn">ASSINAR AGORA</a>
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
              <a href="{{ route('register') }}" class="es-plano__btn">QUERO ESSE</a>
            </div>
          </div>
          <button class="es-next-btn" data-goto="5">VER HORÁRIOS <i class="fa-solid fa-arrow-right"></i></button>
        </div>
      </div>
 
      <!-- SLIDE 5: HORÁRIOS -->
      <div class="explore-slide" data-index="5">
        <div class="explore-slide__bg es-bg-darker">
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
            <div class="es-schedule-item"><span class="es-schedule-time">06:30</span><span class="es-schedule-name">Musculação liberada</span><em>Seg · Qua · Sex</em></div>
            <div class="es-schedule-item"><span class="es-schedule-time">08:00</span><span class="es-schedule-name">Spinning</span><em>Ter · Qui</em></div>
            <div class="es-schedule-item es-schedule-item--red"><span class="es-schedule-time">12:00</span><span class="es-schedule-name">Funcional</span><em>Diário</em></div>
            <div class="es-schedule-item"><span class="es-schedule-time">18:30</span><span class="es-schedule-name">Avaliação sob agendamento</span><em>Ter · Qui · Sáb</em></div>
            <div class="es-schedule-item es-schedule-item--red"><span class="es-schedule-time">19:30</span><span class="es-schedule-name">Cross Training</span><em>Seg · Qua · Sex</em></div>
          </div>
          <button class="es-next-btn" style="margin-top:24px" data-goto="6">VER ENDEREÇOS <i class="fa-solid fa-arrow-right"></i></button>
        </div>
      </div>
 
      <!-- SLIDE 6: ENDEREÇOS -->
      <div class="explore-slide" data-index="6">
        <div class="explore-slide__bg es-bg-dark">
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
              <a href="https://www.google.com/maps/search/Av.+Beira+Mar+1250+Fortaleza" target="_blank" class="es-sede__link"><i class="fa-solid fa-location-dot"></i> Ver no mapa</a>
            </div>
            <div class="es-sede">
              <div class="es-sede__num">02</div>
              <strong>Aldeota</strong>
              <span>Rua Dom Luís, 880 · Fortaleza – CE</span>
              <a href="https://www.google.com/maps/search/Rua+Dom+Luis+880+Fortaleza" target="_blank" class="es-sede__link"><i class="fa-solid fa-location-dot"></i> Ver no mapa</a>
            </div>
            <div class="es-sede">
              <div class="es-sede__num">03</div>
              <strong>Maraponga</strong>
              <span>Av. Godofredo Maciel, 540 · Fortaleza – CE</span>
              <a href="https://www.google.com/maps/search/Av+Godofredo+Maciel+540+Fortaleza" target="_blank" class="es-sede__link"><i class="fa-solid fa-location-dot"></i> Ver no mapa</a>
            </div>
            <div class="es-sede">
              <div class="es-sede__num">04</div>
              <strong>Messejana</strong>
              <span>Av. Frei Cirilo, 1100 · Fortaleza – CE</span>
              <a href="https://www.google.com/maps/search/Av+Frei+Cirilo+1100+Fortaleza" target="_blank" class="es-sede__link"><i class="fa-solid fa-location-dot"></i> Ver no mapa</a>
            </div>
            <div class="es-sede">
              <div class="es-sede__num">05</div>
              <strong>Caucaia</strong>
              <span>Rua Araújo, 320 · Caucaia – CE</span>
              <a href="https://www.google.com/maps/search/Av+Frei+Cirilo+1100+Fortaleza" target="_blank" class="es-sede__link"><i class="fa-solid fa-location-dot"></i> Ver no mapa</a>
            </div>
            <div class="es-sede">
              <div class="es-sede__num">6</div>
              <strong>Lagamar</strong>
              <span>Av.Mister Hull · Fortaleza – CE</span>
              <a href="https://www.google.com/maps/search/Av+Mister+Hull+890+Fortaleza" target="_blank" class="es-sede__link"><i class="fa-solid fa-location-dot"></i> Ver no mapa</a>
            </div>
          </div>
          <button class="es-next-btn" data-goto="7">FALE CONOSCO <i class="fa-solid fa-arrow-right"></i></button>
        </div>
          </div>
          
 
      <!-- SLIDE 7: CONTATO -->
      <div class="explore-slide" data-index="7">
        <div class="explore-slide__bg es-bg-darker">
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
              <a href="/cdn-cgi/l/email-protection#0f6c60617b6e7b604f69667b7f7a637c6a216c6062216d7d" class="es-contato-btn">
                <i class="fa-solid fa-envelope"></i> <span class="__cf_email__" data-cfemail="d9bab6b7adb8adb699bfb0ada9acb5aabcf7bab6b4f7bbab">fitpulse@gmail.com</span>
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
              <a href="{{ route('register') }}" class="es-plano__btn" style="margin-top:16px;display:inline-block">MATRICULE-SE AGORA</a>
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
        <div class="stat-number" data-to="6">0</div>
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
          <a class="offer-link" href="{{ route('register') }}">MATRICULE-SE →</a>
        </div>
        <img class="about-img-main" src="{{ asset('img/Fit couple standing.jpg') }}" alt="Pessoas treinando">
        <button class="about-video" type="button" id="openVideo"
                data-video="{{ asset('video/video.mp4') }}">
          <img src="{{ asset('img/gym.jpg') }}" alt="Assistir vídeo">
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


<!-- sedes -->
<section class="section sedes-section" id="sedes">
  <div class="container">
 
    <div class="sec-header">
      <span class="sec-kicker">ONDE ESTAMOS</span>
      <h2 class="section-title">Nossas <span class="txt-red">Sedes</span></h2>
      <p class="section-sub">6 unidades espalhadas pela Grande Fortaleza. Encontre a mais perto de você.</p>
    </div>
 
    <div class="sedes-grid">
 
      <!-- SEDE 1 -->
      <article class="sede-card">
        <div class="sede-card__img-wrap">
          <img
            src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=600&q=80"
            alt="Unidade Meireles"
            loading="lazy"
          >
          <span class="sede-card__badge sede-card__badge--main">SEDE PRINCIPAL</span>
          <span class="sede-card__num">01</span>
        </div>
        <div class="sede-card__body">
          <h3 class="sede-card__name">Meireles</h3>
          <p class="sede-card__addr">
            <i class="fa-solid fa-location-dot"></i>
            Av. Beira Mar, 1250 · Fortaleza – CE
          </p>
          <ul class="sede-card__info">
            <li><i class="fa-solid fa-clock"></i> Seg–Sex 05h–23h &nbsp;·&nbsp; Sáb 06h–20h &nbsp;·&nbsp; Dom 08h–14h</li>
            <li><i class="fa-solid fa-phone"></i> (85) 99999-0001</li>
            <li><i class="fa-solid fa-car"></i> Estacionamento gratuito</li>
          </ul>
          <a
            href="https://www.google.com/maps/search/Av.+Beira+Mar+1250+Fortaleza"
            target="_blank" rel="noopener"
            class="sede-card__map-btn"
          >
            <i class="fa-solid fa-map-location-dot"></i> Ver no mapa
          </a>
        </div>
      </article>
 
      <!-- SEDE 2 -->
      <article class="sede-card">
        <div class="sede-card__img-wrap">
          <img
            src="https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=600&q=80"
            alt="Unidade Aldeota"
            loading="lazy"
          >
          <span class="sede-card__num">02</span>
        </div>
        <div class="sede-card__body">
          <h3 class="sede-card__name">Aldeota</h3>
          <p class="sede-card__addr">
            <i class="fa-solid fa-location-dot"></i>
            Rua Dom Luís, 880 · Fortaleza – CE
          </p>
          <ul class="sede-card__info">
            <li><i class="fa-solid fa-clock"></i> Seg–Sex 05h–23h &nbsp;·&nbsp; Sáb 06h–20h &nbsp;·&nbsp; Dom 08h–14h</li>
            <li><i class="fa-solid fa-phone"></i> (85) 99999-0002</li>
            <li><i class="fa-solid fa-dumbbell"></i> Spinning exclusivo</li>
          </ul>
          <a
            href="https://www.google.com/maps/search/Rua+Dom+Luis+880+Fortaleza"
            target="_blank" rel="noopener"
            class="sede-card__map-btn"
          >
            <i class="fa-solid fa-map-location-dot"></i> Ver no mapa
          </a>
        </div>
      </article>
 
      <!-- SEDE 3 -->
      <article class="sede-card">
        <div class="sede-card__img-wrap">
          <img
            src="https://images.unsplash.com/photo-1593079831268-3381b0db4a77?w=600&q=80"
            alt="Unidade Maraponga"
            loading="lazy"
          >
          <span class="sede-card__num">03</span>
        </div>
        <div class="sede-card__body">
          <h3 class="sede-card__name">Maraponga</h3>
          <p class="sede-card__addr">
            <i class="fa-solid fa-location-dot"></i>
            Av. Godofredo Maciel, 540 · Fortaleza – CE
          </p>
          <ul class="sede-card__info">
            <li><i class="fa-solid fa-clock"></i> Seg–Sex 05h–23h &nbsp;·&nbsp; Sáb 06h–20h &nbsp;·&nbsp; Dom 08h–14h</li>
            <li><i class="fa-solid fa-phone"></i> (85) 99999-0003</li>
            <li><i class="fa-solid fa-spa"></i> Acompanhamento personalizado </li>
          </ul>
          <a
            href="https://www.google.com/maps/search/Av+Godofredo+Maciel+540+Fortaleza"
            target="_blank" rel="noopener"
            class="sede-card__map-btn"
          >
            <i class="fa-solid fa-map-location-dot"></i> Ver no mapa
          </a>
        </div>
      </article>
 
      <!-- SEDE 4 -->
      <article class="sede-card">
        <div class="sede-card__img-wrap">
          <img
            src="https://images.unsplash.com/photo-1540497077202-7c8a3999166f?w=600&q=80"
            alt="Unidade Messejana"
            loading="lazy"
          >
          <span class="sede-card__num">04</span>
        </div>
        <div class="sede-card__body">
          <h3 class="sede-card__name">Messejana</h3>
          <p class="sede-card__addr">
            <i class="fa-solid fa-location-dot"></i>
            Av. Frei Cirilo, 1100 · Fortaleza – CE
          </p>
          <ul class="sede-card__info">
            <li><i class="fa-solid fa-clock"></i> Seg–Sex 05h–23h &nbsp;·&nbsp; Sáb 06h–20h &nbsp;·&nbsp; Dom 08h–14h</li>
            <li><i class="fa-solid fa-phone"></i> (85) 99999-0004</li>
            <li><i class="fa-solid fa-trophy"></i> Área de cross training</li>
          </ul>
          <a
            href="https://www.google.com/maps/search/Av+Frei+Cirilo+1100+Fortaleza"
            target="_blank" rel="noopener"
            class="sede-card__map-btn"
          >
            <i class="fa-solid fa-map-location-dot"></i> Ver no mapa
          </a>
        </div>
      </article>
 
      <!-- SEDE 5 -->
      <article class="sede-card">
        <div class="sede-card__img-wrap">
          <img src="{{ asset('img/sede5.jpg') }}" alt="Unidade Caucaia" loading="lazy">
          <span class="sede-card__num">05</span>
        </div>
        <div class="sede-card__body">
          <h3 class="sede-card__name">Caucaia</h3>
          <p class="sede-card__addr">
            <i class="fa-solid fa-location-dot"></i>
            Rua Araújo, 320 · Caucaia – CE
          </p>
          <ul class="sede-card__info">
            <li><i class="fa-solid fa-clock"></i> Seg–Sex 06h–22h &nbsp;·&nbsp; Sáb 07h–18h &nbsp;·&nbsp; Dom 08h–14h</li>
            <li><i class="fa-solid fa-phone"></i> (85) 99999-0005</li>
            <li><i class="fa-solid fa-star"></i> Inaugurada em 2024</li>
          </ul>
          <a
            href="https://www.google.com/maps/search/Rua+Araujo+320+Caucaia+CE"
            target="_blank" rel="noopener"
            class="sede-card__map-btn"
          >
            <i class="fa-solid fa-map-location-dot"></i> Ver no mapa
          </a>
        </div>
      </article>

      <!-- SEDE 6 -->
        <article class="sede-card">
          <div class="sede-card__img-wrap">
           <img src="{{ asset('img/sede6.jpg') }}" alt="Unidade Lagamar" loading="lazy">
             <span class="sede-card__badge sede-card__badge--new">NOVA</span>
             <span class="sede-card__num">06</span>
         </div>
         <div class="sede-card__body">
            <h3 class="sede-card__name">Lagamar</h3>
            <p class="sede-card__addr">
             <i class="fa-solid fa-location-dot"></i>
                Av. Mister Hull, 890 · Fortaleza – CE
            </p>
          <ul class="sede-card__info">
             <li><i class="fa-solid fa-clock"></i> Seg–Sex 05h–23h · Sáb 06h–20h · Dom 08h–14h</li>
             <li><i class="fa-solid fa-phone"></i> (85) 99999-0006</li>
             <li><i class="fa-solid fa-star"></i> Equipamentos premium</li>
         </ul>
         <a href="https://www.google.com/maps/search/Av+Mister+Hull+890+Fortaleza" target="_blank" rel="noopener" class="sede-card__map-btn">
          <i class="fa-solid fa-map-location-dot"></i> Ver no mapa
         </a>
      </div>
    </article>
 
    </div><!-- /sedes-grid -->
  </div>
</section>
 
<!--INSTRUTORES-->
<section class="section section--dark instrutores-section" id="instrutores">
  <div class="container">
 
    <div class="sec-header">
      <span class="sec-kicker">NOSSA EQUIPE</span>
      <h2 class="section-title">Conheça nossos <span class="txt-red">Instrutores</span></h2>
      <p class="section-sub">Clique em qualquer instrutor para conhecer melhor a trajetória e especialidades de cada um.</p>
    </div>
 
    <!-- Carrossel wrapper -->
    <div class="carr-wrap">
      <button class="carr-btn carr-btn--prev" id="carrPrev" aria-label="Anterior">
        <i class="fa-solid fa-chevron-left"></i>
      </button>
 
      <div class="carr-viewport" id="carrViewport">
        <div class="carr-track" id="carrTrack">
 
          <!-- CARD 1 -->
          <button class="instr-card" data-id="0" aria-label="Ver perfil de Ricardo Borges">
             <div class="instr-card__img">
               <img src="{{ asset('img/instrutor2.jpeg') }}" alt="Ricardo Borges" class="instr-card__photo">
               <div class="instr-card__overlay">
                <span class="instr-card__cta">Ver perfil <i class="fa-solid fa-arrow-right"></i></span>
               </div>
             </div>
            <div class="instr-card__foot">
              <strong>Ricardo Borges</strong>
              <span>Musculação</span>
            </div>
          </button>
 
          <!-- CARD 2 -->
          <button class="instr-card" data-id="1" aria-label="Ver perfil de Carla Almeida">
            <div class="instr-card__img">
              <img src="{{ asset('img/instrutora8.jpeg') }}" alt="Carla Almeida" class="instr-card__photo">
            <div class="instr-card__overlay">
              <span class="instr-card__cta">Ver perfil <i class="fa-solid fa-arrow-right"></i></span>
            </div>
            </div>
            <div class="instr-card__foot">
              <strong>Carla Almeida</strong>
              <span>Spinning & Funcional</span>
            </div>
          </button>
 
          <!-- CARD 3 -->
          <button class="instr-card" data-id="2" aria-label="Ver perfil de Marcos Freitas">
            <div class="instr-card__img">
              <img src="{{ asset('img/instrutor12.png') }}" alt="Marcos Freitas" class="instr-card__photo">
            <div class="instr-card__overlay">
              <span class="instr-card__cta">Ver perfil <i class="fa-solid fa-arrow-right"></i></span>
            </div>
            </div>
            <div class="instr-card__foot">
              <strong>Marcos Freitas</strong>
              <span>Cross Training</span>
            </div>
          </button>
 
          <!-- CARD 4 -->
          <button class="instr-card" data-id="3" aria-label="Ver perfil de Juliana Neves">
            <div class="instr-card__img">
              <img src="{{ asset('img/instrutora7.jpg') }}" alt="Juliana Neves" class="instr-card__photo">
            <div class="instr-card__overlay">
              <span class="instr-card__cta">Ver perfil <i class="fa-solid fa-arrow-right"></i></span>
            </div>
            </div>
            <div class="instr-card__foot">
              <strong>Juliana Neves</strong>
              <span>Yoga & Pilates</span>
            </div>
          </button>
 
          <!-- CARD 5 -->
          <button class="instr-card" data-id="4" aria-label="Ver perfil de Felipe Santos">
            <div class="instr-card__img">
              <img src="{{ asset('img/instrutor3.jpeg') }}" alt="Felipe Santos" class="instr-card__photo">
              <div class="instr-card__overlay">
              <span class="instr-card__cta">Ver perfil <i class="fa-solid fa-arrow-right"></i></span>
            </div>
          </div>
            <div class="instr-card__foot">
              <strong>Felipe Santos</strong>
              <span>Zumba & Dança</span>
            </div>
          </button>
 
          <!-- CARD 6 -->
          <button class="instr-card" data-id="5" aria-label="Ver perfil de Ana Lima">
            <div class="instr-card__img">
              <img src="{{ asset('img/instrutora4.jpeg') }}" alt="Ana Lima" class="instr-card__photo">
            <div class="instr-card__overlay">
              <span class="instr-card__cta">Ver perfil <i class="fa-solid fa-arrow-right"></i></span>
            </div>
          </div>
            <div class="instr-card__foot">
              <strong>Ana Lima</strong>
              <span>Personal Trainer</span>
            </div>
          </button>

            <!-- CARD 7 -->
         <button class="instr-card" data-id="6" aria-label="Ver perfil de Bruno Martins">
          <div class="instr-card__img">
           <img src="{{ asset('img/Instrutor5.jpeg') }}" alt="Bruno Martins" class="instr-card__photo">
          <div class="instr-card__overlay">
            <span class="instr-card__cta">Ver perfil <i class="fa-solid fa-arrow-right"></i></span>
          </div>
        </div>     
          <div class="instr-card__foot">
             <strong>Bruno Martins</strong>
             <span>Força & Powerlifting</span>
            </div>
          </button>
           
          <!-- CARD 8 -->
         <button class="instr-card" data-id="7" aria-label="Ver perfil de Larissa Costa">
           <div class="instr-card__img">
            <img src="{{ asset('img/instrutora6.jpeg') }}" alt="Larissa Costa" class="instr-card__photo">
          <div class="instr-card__overlay">
             <span class="instr-card__cta">Ver perfil <i class="fa-solid fa-arrow-right"></i></span>
           </div>
          </div>
           <div class="instr-card__foot">
              <strong>Larissa Costa</strong>
              <span>Aqua Fitness</span>
              </div>
           </button>

        <!-- CARD 9 -->
         <button class="instr-card" data-id="8" aria-label="Ver perfil de Thiago Oliveira">
          <div class="instr-card__img">
            <img src="{{ asset('img/instrutor9.png') }}" alt="Thiago Oliveira" class="instr-card__photo">
          <div class="instr-card__overlay">
            <span class="instr-card__cta">Ver perfil <i class="fa-solid fa-arrow-right"></i></span>
          </div>
        </div>
          <div class="instr-card__foot">
             <strong>Thiago Oliveira</strong>
             <span>Boxe & MMA</span>
          </div>
         </button>

         <!-- CARD 10 -->
       <button class="instr-card" data-id="9" aria-label="Ver perfil de Patrícia Rocha">
         <div class="instr-card__img">
            <img src="{{ asset('img/Instrutora1.jpeg') }}" alt="Patrícia Rocha" class="instr-card__photo">
          <div class="instr-card__overlay">
              <span class="instr-card__cta">Ver perfil <i class="fa-solid fa-arrow-right"></i></span>
         </div>
       </div>
          <div class="instr-card__foot">
            <strong>Patrícia Rocha</strong>
            <span>Pilates & Mobilidade</span>
           </div>
         </button>

        <!-- CARD 11 -->
       <button class="instr-card" data-id="10" aria-label="Ver perfil de Rafael Viana">
        <div class="instr-card__img">
          <img src="{{ asset('img/instrutor10.png') }}" alt="Rafael Viana" class="instr-card__photo">
          <div class="instr-card__overlay">
           <span class="instr-card__cta">Ver perfil <i class="fa-solid fa-arrow-right"></i></span>
       </div>
      </div>
       <div class="instr-card__foot">
        <strong>Rafael Viana</strong>
        <span>Corrida & Atletismo</span>
       </div>
      </button>

      <!-- CARD 12 -->
      <button class="instr-card" data-id="11" aria-label="Ver perfil de Marina Sousa">
       <div class="instr-card__img">
          <img src="{{ asset('img/instrutora13.jpg') }}" alt="Marina Sousa" class="instr-card__photo">
           <div class="instr-card__overlay">
         <span class="instr-card__cta">Ver perfil <i class="fa-solid fa-arrow-right"></i></span>
      </div>
    </div>
      <div class="instr-card__foot">
        <strong>Marina Sousa</strong>
        <span>Flexibilidade & Postura</span>
       </div>
      </button>
 
        </div>
      </div>
 
      <button class="carr-btn carr-btn--next" id="carrNext" aria-label="Próximo">
        <i class="fa-solid fa-chevron-right"></i>
      </button>
    </div>
 
    
    <div class="carr-dots" id="carrDots"></div>
 
  </div>
</section>
 
<!-- MODAL: perfil do instrutor -->
<div class="instr-modal" id="instrModal" aria-hidden="true" role="dialog" aria-modal="true">
  <div class="instr-modal__overlay" id="instrOverlay"></div>
  <div class="instr-modal__panel">
    <button class="instr-modal__close" id="instrClose" aria-label="Fechar">
      <i class="fa-solid fa-xmark"></i>
    </button>
 
    <div class="instr-modal__hero">
      <div class="instr-modal__avatar" id="mAvatar"></div>
      <div class="instr-modal__headline">
        <p class="sec-kicker" id="mKicker"></p>
        <h2 class="instr-modal__name" id="mName"></h2>
        <p class="instr-modal__role" id="mRole"></p>
      </div>
    </div>
 
    <div class="instr-modal__body">
      <div class="instr-modal__col">
        <h4 class="instr-modal__label">Sobre</h4>
        <p id="mBio"></p>
 
        <h4 class="instr-modal__label" style="margin-top:20px">Especialidades</h4>
        <div class="instr-modal__tags" id="mTags"></div>
      </div>
      <div class="instr-modal__col instr-modal__col--stats">
        <div class="instr-modal__stat" id="mStat1"></div>
        <div class="instr-modal__stat" id="mStat2"></div>
        <div class="instr-modal__stat" id="mStat3"></div>
      </div>
    </div>
 
    <div class="instr-modal__footer">
      <a href="{{ route('register') }}" class="instr-modal__cta-btn" id="instrClose2">
        <i class="fa-solid fa-dumbbell"></i> Treinar com este instrutor
      </a>
    </div>
  </div>
</div>
 
 
<!-- fac-->
<section class="section section--alt faq-section" id="faq">
  <div class="container">
 
    <div class="sec-header">
      <span class="sec-kicker">TIRE SUAS DÚVIDAS</span>
      <h2 class="section-title">Perguntas <span class="txt-red">Frequentes</span></h2>
      <p class="section-sub">Respondemos as dúvidas mais comuns. Não achou o que procura? Fale com a gente.</p>
    </div>
 
    <div class="faq-list">
 
      <details class="faq-acc">
        <summary><span>Tem taxa de matrícula?</span><i class="fa-solid fa-plus faq-acc__icon"></i></summary>
        <div class="faq-acc__body">
          <p>No primeiro mês a taxa de matrícula é <strong>totalmente isenta</strong> para qualquer plano. A partir do segundo mês, não há cobranças extras além da mensalidade escolhida.</p>
        </div>
      </details>
 
      <details class="faq-acc">
        <summary><span>Posso treinar em qualquer unidade?</span><i class="fa-solid fa-plus faq-acc__icon"></i></summary>
        <div class="faq-acc__body">
          <p>Sim! Os planos <strong>Premium</strong> e <strong>Black</strong> dão acesso livre a todas as 5 unidades. O plano Básico é vinculado à unidade escolhida na matrícula.</p>
        </div>
      </details>
 
      <details class="faq-acc">
        <summary><span>Posso cancelar quando quiser?</span><i class="fa-solid fa-plus faq-acc__icon"></i></summary>
        <div class="faq-acc__body">
          <p>Sim. Não existe fidelidade mínima. O cancelamento é feito na recepção ou pelo WhatsApp, sem multa e sem burocracia.</p>
        </div>
      </details>
 
      <details class="faq-acc">
        <summary><span>Como funciona a avaliação física?</span><i class="fa-solid fa-plus faq-acc__icon"></i></summary>
        <div class="faq-acc__body">
          <p>Toda matrícula inclui uma <strong>avaliação física completa</strong> com um de nossos professores. Com base nela, montamos um plano de treino personalizado para seus objetivos.</p>
        </div>
      </details>
 
      <details class="faq-acc">
        <summary><span>Quais os horários de funcionamento?</span><i class="fa-solid fa-plus faq-acc__icon"></i></summary>
        <div class="faq-acc__body">
          <p><strong>Segunda a sexta:</strong> 05h às 23h &nbsp;|&nbsp; <strong>Sábado:</strong> 06h às 20h &nbsp;|&nbsp; <strong>Domingo e feriados:</strong> 08h às 14h. O plano Básico tem acesso apenas em horário comercial (08h–18h).</p>
        </div>
      </details>
 
      <details class="faq-acc">
        <summary><span>Tem serviço de nutrição?</span><i class="fa-solid fa-plus faq-acc__icon"></i></summary>
        <div class="faq-acc__body">
          <p>O plano <strong>Black</strong> inclui orientação nutricional básica. Para acompanhamento completo com nutricionista, oferecemos o serviço como add-on em qualquer plano. Consulte na recepção.</p>
        </div>
      </details>
 
      <details class="faq-acc">
        <summary><span>Aceita plano empresarial ou convênio?</span><i class="fa-solid fa-plus faq-acc__icon"></i></summary>
        <div class="faq-acc__body">
          <p>Sim! Oferecemos planos corporativos com condições especiais para grupos. Entre em contato por <strong>comercial@fitpulse.com.br</strong> ou pelo WhatsApp para saber mais.</p>
        </div>
      </details>
 
    </div><!-- /faq-list -->
 
    <div class="faq-cta">
      <p>Ainda tem dúvidas?</p>
      <a href="https://wa.me/5585999999999" target="_blank" rel="noopener" class="faq-whats-btn">
        <i class="fa-brands fa-whatsapp"></i> Falar no WhatsApp
      </a>
    </div>
 
  </div>
</section>
 
 <!-- RODAPÉ -->
<footer class="footer">
  <div class="container footer-inner">
    <div class="footer-brand">
      <div class="footer-logo">FIT<span>PULSE</span></div>
      <p class="footer-copy">© 2025 FIT PULSE Academia. Todos os direitos reservados.</p>
    </div>
    <div class="footer-links">
      <a href="#sobre">Sobre</a>
      <a href="#sedes">Unidades</a>
      <a href="#instrutores">Instrutores</a>
      <a href="#faq">FAQ</a>
    </div>
    <a href="#topo" class="footer-top-btn" aria-label="Voltar ao topo">
      <i class="fa-solid fa-chevron-up"></i>
    </a>
  </div>
</footer>
<a href="https://wa.me/5585999999999"
   target="_blank"
   rel="noopener"
   class="whatsapp-float"
   aria-label="Falar no WhatsApp">
  <i class="fa-brands fa-whatsapp"></i>
</a>
</body>
</html>
