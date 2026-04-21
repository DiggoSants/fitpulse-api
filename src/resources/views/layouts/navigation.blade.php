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
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"> <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-right:8px;">
                     <rect x="3" y="3" width="7" height="7" rx="1"/>
                     <rect x="14" y="3" width="7" height="7" rx="1"/>
                     <rect x="14" y="14" width="7" height="7" rx="1"/>
                     <rect x="3" y="14" width="7" height="7" rx="1"/>
                    </svg>

                   {{ __('Painel') }}
                   </x-nav-link>

                   @if(Auth::user()->isManager())
                   <x-nav-link :href="route('access.index')" :active="request()->routeIs('access.*')">
                    <svg width="14" height="14" viewBox="0 0 24 24" 
                    fill="none" stroke="currentColor" stroke-width="2" 
                    stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-right:8px;">
                     <rect x="3" y="11" width="18" height="11" rx="2"/>
                     <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>{{ __('Acessos') }}
                </x-nav-link>
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
                @elseif(request()->routeIs('profile.*'))
                    Perfil
                @elseif(request()->routeIs('enrollment.*'))
                    Matrícula
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
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Painel') }}</x-responsive-nav-link>
                @if(Auth::user()->isManager())
                <x-responsive-nav-link
                 :href="route('access.index')" :active="request()->routeIs('access.*')">
                 {{ __('Controle de Acesso') }}
                </x-responsive-nav-link>
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