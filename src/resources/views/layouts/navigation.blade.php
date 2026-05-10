<nav x-data="{ open: false }" class="nav-main">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="nav-fitpulse-link">
                        <img src="{{ asset('img/logo.png') }}"
                             class="nav-logo-img"
                             style="width:36px; height:36px;"
                             alt="FitPulse logo">
                        <span class="nav-fitpulse-fit">FIT</span><span class="nav-fitpulse-pulse">PULSE</span>
                    </a>
                    <span class="nav-separator" aria-hidden="true"></span>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                    {{-- Painel — todos exceto recepcionista --}}
                    @unless(Auth::user()->isReceptionist())
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             style="flex-shrink:0; margin-right:8px;">
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                        </svg>
                        {{ __('Painel') }}
                    </x-nav-link>
                    @endunless

                    {{-- ══ RECEPCIONISTA — apenas links de recepção ══ --}}
                    @if(Auth::user()->isReceptionist())

                        <x-nav-link :href="route('reception.index')" :active="request()->routeIs('reception.*')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 style="flex-shrink:0; margin-right:8px;">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <line x1="19" y1="8" x2="19" y2="14"/>
                                <line x1="22" y1="11" x2="16" y2="11"/>
                            </svg>
                            {{ __('Matrículas') }}
                        </x-nav-link>

                    @elseif(Auth::user()->isManager())
                    {{-- ══ GERENTE ══ --}}

                        <x-nav-link :href="route('shop.manager')" :active="request()->routeIs('shop.manager')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 style="flex-shrink:0; margin-right:8px;">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                <line x1="3" y1="6" x2="21" y2="6"/>
                                <path d="M16 10a4 4 0 0 1-8 0"/>
                            </svg>
                            {{ __('Lojinha') }}
                        </x-nav-link>

                        <x-nav-link :href="route('access.index')" :active="request()->routeIs('access.*')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 style="flex-shrink:0; margin-right:8px;">
                                <rect x="3" y="11" width="18" height="11" rx="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            {{ __('Acessos') }}
                        </x-nav-link>

                        <x-nav-link :href="route('maintenance.view')" :active="request()->routeIs('maintenance.*') || request()->routeIs('equipment.*')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 style="flex-shrink:0; margin-right:8px;">
                                <rect x="2" y="10" width="3" height="4" rx="1"/>
                                <rect x="19" y="10" width="3" height="4" rx="1"/>
                                <rect x="5" y="8" width="3" height="8" rx="1"/>
                                <rect x="16" y="8" width="3" height="8" rx="1"/>
                                <rect x="8" y="11" width="8" height="2" rx="1"/>
                            </svg>
                            {{ __('Manutenção') }}
                        </x-nav-link>

                    @elseif(Auth::user()->isInstructor())
                    {{-- ══ INSTRUTOR ══ --}}

                        <x-nav-link :href="route('evaluations.instructor')" :active="request()->routeIs('evaluations.instructor')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 style="flex-shrink:0; margin-right:8px;">
                                <path d="M3 3v18h18"/><path d="M7 16l4-4 4 4 4-6"/>
                            </svg>
                            {{ __('Evolução') }}
                        </x-nav-link>

                    @else
                    {{-- ══ ALUNO MATRICULADO ══ --}}

                        @if(Auth::user()->student?->isEnrolled())
                            <x-nav-link :href="route('workouts.index')" :active="request()->routeIs('workouts.*')">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     style="flex-shrink:0; margin-right:8px;">
                                    <rect x="2" y="9" width="4" height="6" rx="1"/>
                                    <rect x="18" y="9" width="4" height="6" rx="1"/>
                                    <rect x="7" y="11" width="10" height="2" rx="1"/>
                                </svg>
                                {{ __('Treinos') }}
                            </x-nav-link>

                            <x-nav-link :href="route('shop.index')" :active="request()->routeIs('shop.index')">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     style="flex-shrink:0; margin-right:8px;">
                                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                    <line x1="3" y1="6" x2="21" y2="6"/>
                                    <path d="M16 10a4 4 0 0 1-8 0"/>
                                </svg>
                                {{ __('Lojinha') }}
                            </x-nav-link>
                        @endif

                    @endif

                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6" style="gap:8px;">
                <button id="btnTheme" class="nav-btn-icon" aria-label="Trocar tema" type="button">
                    <i class="fa-solid fa-moon"></i>
                </button>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="nav-user-btn">
                            <span class="nav-user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                            <span class="nav-user-name">{{ Auth::user()->name }}</span>
                            <svg class="nav-user-chevron fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Perfil') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Sair') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Botão hamburguer mobile --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" style="background:none; border:none; cursor:pointer; padding:8px; color:#fff;">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    {{-- Context bar --}}
    <div class="nav-context-bar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="nav-context-inner">
                <span class="nav-ctx-crumb">FitPulse</span>
                <span class="nav-ctx-sep">/</span>
                <span class="nav-ctx-crumb nav-ctx-crumb--active">
                    @if(request()->routeIs('dashboard'))
                        Painel
                    @elseif(request()->routeIs('reception.*'))
                        Recepção
                    @elseif(request()->routeIs('workouts.*'))
                        Treinos
                    @elseif(request()->routeIs('profile.*'))
                        Perfil
                    @elseif(request()->routeIs('enrollment.*'))
                        Matrícula
                    @elseif(request()->routeIs('evaluations.*'))
                        Evolução Física
                    @elseif(request()->routeIs('maintenance.*') || request()->routeIs('equipment.*'))
                        Manutenção
                    @else
                        {{ ucfirst(request()->segment(1) ?? 'Página') }}
                    @endif
                </span>
            </div>
        </div>
    </div>

    {{-- Menu mobile --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden nav-mobile-menu">
        <div class="pt-2 pb-3 space-y-1">
            @unless(Auth::user()->isReceptionist())
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Painel') }}
            </x-responsive-nav-link>
            @endunless

            @if(Auth::user()->isReceptionist())
                <x-responsive-nav-link :href="route('reception.index')" :active="request()->routeIs('reception.*')">
                    {{ __('Matrículas') }}
                </x-responsive-nav-link>

            @elseif(Auth::user()->isManager())
                <x-responsive-nav-link :href="route('shop.manager')" :active="request()->routeIs('shop.manager')">
                    {{ __('Lojinha') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('access.index')" :active="request()->routeIs('access.*')">
                    {{ __('Controle de Acesso') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('evaluations.manager')" :active="request()->routeIs('evaluations.manager')">
                    {{ __('Evolução Física') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('maintenance.view')" :active="request()->routeIs('maintenance.*') || request()->routeIs('equipment.*')">
                    {{ __('Manutenção') }}
                </x-responsive-nav-link>

            @elseif(Auth::user()->isInstructor())
                <x-responsive-nav-link :href="route('evaluations.instructor')" :active="request()->routeIs('evaluations.instructor')">
                    {{ __('Evolução Física') }}
                </x-responsive-nav-link>

            @else
                @if(Auth::user()->student?->isEnrolled())
                    <x-responsive-nav-link :href="route('workouts.index')" :active="request()->routeIs('workouts.*')">
                        {{ __('Treinos') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('shop.index')" :active="request()->routeIs('shop.index')">
                        {{ __('Lojinha') }}
                    </x-responsive-nav-link>
                @endif
            @endif
        </div>
        <div class="pt-4 pb-1" style="border-top:1px solid rgba(255,255,255,0.08);">
            <div class="px-4">
                <div style="font-weight:700; font-size:14px; color:#fff;">{{ Auth::user()->name }}</div>
                <div style="font-size:12px; color:rgba(255,255,255,0.4); margin-top:2px;">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Perfil') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Sair') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>

</nav>